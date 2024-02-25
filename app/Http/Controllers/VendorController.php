<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use Mail;
use App\Role;
use App\User;
use App\Email;
use App\Vendor;
use App\Helpers;
use App\Setting;
use App\Customer;
use App\Supplier;
use Carbon\Carbon;
use App\ChatMessage;
use App\VendorStatus;
use App\ReplyCategory;
use App\VendorProduct;
use App\VendorCategory;
use App\Mail\PurchaseEmail;
use App\VendorStatusDetail;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Helpers\GithubTrait;
use Illuminate\Http\Request;
use App\Helpers\HubstaffTrait;
use GuzzleHttp\RequestOptions;
use App\Models\DataTableColumn;
use App\Models\VendorFlowChart;
use App\Models\VendorFrequency;
use App\Models\VendorQuestions;
use App\Models\VendorFrameworks;
use App\Models\VendorPriceHistory;
use App\VendorStatusDetailHistory;
use Illuminate\Support\Facades\DB;
use Webklex\PHPIMAP\ClientManager;
use App\Models\VendorRatingQANotes;
use App\Meetings\ZoomMeetingDetails;
use App\Models\VendorFLowChartNotes;
use App\Models\VendorQuestionAnswer;
use App\Models\VendorQuestionStatus;
use App\Models\VendorRatingQAStatus;
use App\Models\VendorRemarksHistory;
use App\VendorStatusHistory as VSHM;
use App\Models\VendorFlowChartMaster;
use App\Models\VendorFlowChartStatus;
use App\Models\VendorRatingQuestions;
use App\Models\VendorFlowChartRemarks;
use App\Models\VendorFlowChartSorting;
use App\Models\VendorRatingQuestionAnswer;
use GuzzleHttp\Client as GuzzleHttpClient;
use App\Models\VendorQuestionStatusHistory;
use App\Models\VendorRatingQAStatusHistory;
use App\Models\VendorFlowChartStatusHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class VendorController extends Controller
{
    use GithubTrait;
    use HubstaffTrait;

    const DEFAULT_FOR = 2; //For Vendor

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->init(config('env.HUBSTAFF_SEED_PERSONAL_TOKEN'));
    }

    public function updateReminder(Request $request)
    {
        $vendor = Vendor::find($request->get('vendor_id'));
        $vendor->frequency = $request->get('frequency');
        $vendor->reminder_message = $request->get('message');
        $vendor->reminder_from = $request->get('reminder_from', '0000-00-00 00:00');
        $vendor->reminder_last_reply = $request->get('reminder_last_reply', 0);
        $vendor->save();

        $message = 'Reminder : ' . $request->get('message');
        app(\App\Http\Controllers\WhatsAppController::class)->sendWithThirdApi($vendor->phone, '', $message);

        return response()->json([
            'success',
        ]);
    }

    public function index(Request $request)
    {
        $term = $request->term ?? '';
        $sortByClause = 'id';
        $orderby = 'ASC';

        if ($request->orderby == '') {
            $orderby = 'DESC';
        }

        if ($request->sortby == 'category') {
            $sortByClause = 'category_name';
        }

        if ($request->sortby == 'communication') {
            $sortByClause = 'message_created_at';
        }

        if ($request->sortby == 'id') {
            $sortByClause = 'id';
        }

        $whereArchived = ' `deleted_at` IS NULL ';

        if ($request->get('with_archived') == 'on') {
            $whereArchived = '  `deleted_at` IS NOT NULL  ';
        }

        $isAdmin = Auth::user()->isAdmin();
        if ($isAdmin) {
            $permittedCategories = [];
        } else {
            $permittedCategories = Auth::user()->vendorCategoryPermission->pluck('id')->all() + [0];
        }

        $updatedByWhere = '';
        if ($isAdmin) {
            $permittedCategories = '';
        } else {
            if (empty($permittedCategories)) {
                $permittedCategories = [0];
            }
            $permittedCategories_all = implode(',', $permittedCategories);
            if ($permittedCategories_all == 0) {
                $permittedCategories = '';
            } else {
                $permittedCategories = 'and vendors.category_id in (' . implode(',', $permittedCategories) . ')';
            }
            $updatedByWhere = ' and vendors.email="' . Auth::user()->email . '"';
        }

        $whereCondition = [];
        if (request('term') != null) {
            $whereCondition[] = 'name LIKE "%' . $request->term . '%"';
        }

        //if email is not null
        if (request('email') != null) {
            $whereCondition[] = 'email LIKE "%' . $request->email . '%"';
        }

        if (request('whatsapp_number') != null) {
            $whereCondition[] = 'whatsapp_number LIKE "%' . $request->whatsapp_number . '%"';
        }

        //if phone is not null
        if (request('phone') != null) {
            $whereCondition[] = 'phone LIKE "%' . $request->phone . '%"';
        }

        $status = request('status');
        if ($status != null && ! request('with_archived')) {
            $whereCondition[] = 'status = "' . $status . '"';
        }

        if (request('updated_by') != null && ! request('with_archived')) {
            $whereCondition[] = 'updated_by = "' . $request->updated_by . '"';
        }

        //if category is not nyll
        if (request('category') != null) {
            $whereCondition[] = 'category_id IN (' . implode(',', $request->category) . ')';
        }

        if (request('type') != null) {
            $whereCondition[] = 'type = "' . $request->type . '"';
        }

        if (request('framework') != null) {
            $whereCondition[] = 'framework = "' . $request->framework . '"';
        }

        if (request('communication_history') != null && ! request('with_archived')) {
            $communication_history = request('communication_history');
            $whereCondition[] = 'vendors.id in (select vendor_id from chat_messages where vendor_id is not null and message LIKE "%' . $communication_history . '%"';
        }

        if ($request->flt_vendor_status != null) {
            $whereCondition[] = 'vendor_status LIKE "%' . $request->flt_vendor_status . '%"';
        }

        $vendorsQuery = 'SELECT *,
              (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
              (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) as message_status,
              (SELECT mm3.created_at FROM chat_messages mm3 WHERE mm3.id = message_id) as message_created_at

              FROM (SELECT vendors.id, vendors.frequency, vendors.is_blocked ,vendors.reminder_message, vendors.category_id, vendors.name, vendors.phone, vendors.email, vendors.address, vendors.social_handle, vendors.website, vendors.login, vendors.password, vendors.gst, vendors.account_name, vendors.account_iban, vendors.vendor_status, vendors.account_swift,
                vendors.created_at,vendors.updated_at,
                vendors.updated_by,
                vendors.reminder_from,
                vendors.reminder_last_reply,
                vendors.status,
                vendors.whatsapp_number,
                vendors.remark,
                vendors.type,
                vendors.framework,
                vendors.fc_status,
                vendors.flowcharts,
                vendors.question_status,
                vendors.rating_question_status,
                vendors.flowchart_date,
                vendors.feeback_status,
                vendors.price,
                vendors.currency,
                vendors.price_remarks,
                vendors.frequency_of_payment,
                category_name,
              chat_messages.message_id,
              vf.name as framework_name
              FROM vendors
              LEFT JOIN vendor_frameworks AS vf ON vendors.framework = vf.id
              LEFT JOIN (SELECT MAX(id) as message_id, vendor_id FROM chat_messages GROUP BY vendor_id ORDER BY created_at DESC) AS chat_messages
              ON vendors.id = chat_messages.vendor_id

              LEFT JOIN (SELECT id, title AS category_name FROM vendor_categories) AS vendor_categories
              ON vendors.category_id = vendor_categories.id WHERE ' . $whereArchived . $updatedByWhere . '
              )

              AS vendors ';

        if (! empty($whereCondition)) {
            $vendorsQuery .= 'WHERE ( ' . implode(' AND ', $whereCondition) . ') ' . $permittedCategories . ' ORDER BY ' . $sortByClause . ' ' . $orderby;
        } else {
            $vendorsQuery .= 'WHERE 1 ' . $permittedCategories . ' ORDER BY ' . $sortByClause . ' ' . $orderby;
        }

        $vendors = DB::select($vendorsQuery);

        $totalVendor = count($vendors);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        if (request()->get('select_all') == 'true') {
            $perPage = count($vendors);
            $currentPage = 1;
        }

        if (! is_numeric($perPage)) {
            $perPage = 2;
        }

        $currentItems = array_slice($vendors, $perPage * ($currentPage - 1), $perPage);

        $vendors = new LengthAwarePaginator($currentItems, count($vendors), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $vendor_categories = VendorCategory::all();

        $users = User::all();

        $replies = \App\Reply::where('model', 'Vendor')->whereNull('deleted_at')->pluck('reply', 'id')->toArray();

        $statusList = \DB::table('vendor_status')->select('name')->pluck('name', 'name')->toArray();

        $updatedProducts = \App\Vendor::join('users as u', 'u.id', 'vendors.updated_by')
            ->groupBy('vendors.updated_by')
            ->select([\DB::raw('count(u.id) as total_records'), 'u.name'])
            ->get();

        $whatsapp = DB::select('SELECT number FROM whatsapp_configs WHERE status = 1 '); // and provider="Chat-API"

        $status = VendorStatus::all();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'vendors-listing')->first();

        $dynamicColumnsToShowVendors = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowVendors = json_decode($hideColumns, true);
        }

        $flowchart_master = VendorFlowChartMaster::all();
        $vendor_flow_charts = VendorFlowChart::with('master')->orderBy('sorting', 'ASC')->get();

        $vendor_questions = VendorQuestions::orderBy('sorting', 'ASC')->get();

        $rating_questions = VendorRatingQuestions::orderBy('sorting', 'ASC')->get();

        $status_q = VendorQuestionStatus::all();

        return view('vendors.index', [
            'vendors' => $vendors,
            'vendor_categories' => $vendor_categories,
            'term' => $term,
            'orderby' => $orderby,
            'users' => $users,
            'status' => $status,
            'replies' => $replies,
            'updatedProducts' => $updatedProducts,
            'totalVendor' => $totalVendor,
            'statusList' => $statusList,
            'dynamicColumnsToShowVendors' => $dynamicColumnsToShowVendors,
            'whatsapp' => $whatsapp,
            'flowchart_master' => $flowchart_master,
            'vendor_flow_charts' => $vendor_flow_charts,
            'vendor_questions' => $vendor_questions,
            'rating_questions' => $rating_questions,
            'status_q' => $status_q,
        ]);
    }

    /**
     * This will use to change vendor whatsapp number
     */
    public function changeWhatsapp(Request $request)
    {
        $vendor = Vendor::find($request->vendor_id)->first();
        $data = ['whatsapp_number' => $request->whatsapp_number];
        $vendor->update($data);

        return response()->json(['success' => 'successfully updated', 'data' => $data]);
    }

    public function vendorSearch()
    {
        $term = request()->get('q', null);
        $search = Vendor::where('name', 'LIKE', '%' . $term . '%')
            ->get();

        return response()->json($search);
    }

    public function vendorSearchEmail()
    {
        $term = request()->get('q', null);
        $search = Vendor::where('email', 'LIKE', '%' . $term . '%')
            ->get();

        return response()->json($search);
    }

    public function vendorSearchPhone()
    {
        $term = request()->get('q', null);
        $search = Vendor::where('phone', 'LIKE', '%' . $term . '%')
            ->get();

        return response()->json($search);
    }

    public function email(Request $request)
    {
        $vendorArr = Vendor::join('emails', 'emails.model_id', 'vendors.id')
            ->where('emails.model_type', Vendor::class)
            ->where('vendors.id', $request->get('id', 0))
            ->get();
        $data = [];
        foreach ($vendorArr as $vendor) {
            $additional_data = json_decode($vendor->additional_data);
            $data[] = [
                'from' => $vendor->from,
                'to' => $vendor->to,
                'subject' => $vendor->subject,
                'message' => strip_tags($vendor->message),
                'cc' => $vendor->cc,
                'bcc' => $vendor->bcc,
                'created_at' => $vendor->created_at,
                'attachment' => ! empty($additional_data->attachment) ? $additional_data->attachment : '',
                'inout' => $vendor->email != $vendor->from ? 'out' : 'in',
            ];
        }

        return response()->json($data);
    }

    public function assignUserToCategory(Request $request)
    {
        $user = $request->get('user_id');
        $category = $request->get('category_id');

        $category = VendorCategory::find($category);
        $category->user_id = $user;
        $category->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function product()
    {
        $products = VendorProduct::with('vendor')->latest()->paginate(Setting::get('pagination'));
        $vendors = Vendor::select(['id', 'name'])->get();

        return view('vendors.product', [
            'products' => $products,
            'vendors' => $vendors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'category_id' => 'sometimes|nullable|numeric',
            'name' => 'required|string|max:255',
            'address' => 'sometimes|nullable|string',
            'email' => 'sometimes|nullable|email',
            'gmail' => 'sometimes|nullable|email',
            'social_handle' => 'sometimes|nullable',
            'website' => 'sometimes|nullable',
            'login' => 'sometimes|nullable',
            'password' => 'sometimes|nullable',
            'gst' => 'sometimes|nullable|max:255',
            'account_name' => 'sometimes|nullable|max:255',
            'account_iban' => 'sometimes|nullable|max:255',
            'account_swift' => 'sometimes|nullable|max:255',
            'frequency_of_payment' => 'sometimes|nullable|max:255',
            'bank_name' => 'sometimes|nullable|max:255',
            'bank_address' => 'sometimes|nullable|max:255',
            'city' => 'sometimes|nullable|max:255',
            'country' => 'sometimes|nullable|max:255',
            'ifsc_code' => 'sometimes|nullable|max:255',
            'remark' => 'sometimes|nullable|max:255',
        ];
        $vendorCount = ! empty($request['vendor_name']) ? count($request['vendor_name']) : 0;
        $vendorRules = $vendorData = [];
        $inputs = $request->all();
        if ($vendorCount !== '') {
            $vendorRules = [
                'vendor_name' => 'sometimes|array',
                'vendor_name.*' => 'sometimes|string|max:255',
                'vendor_email' => 'sometimes|array',
                'vendor_email.*' => 'sometimes|nullable|email',
                'vendor_gmail' => 'sometimes|array',
                'vendor_gmail.*' => 'sometimes|nullable|email',
            ];
            for ($i = 0; $i < $vendorCount; $i++) {
                $vendorData[$i]['category_id'] = $request['category_id'];
                $vendorData[$i]['name'] = $request['vendor_name'][$i];
                $vendorData[$i]['email'] = $request['vendor_email'][$i];
                $vendorData[$i]['gmail'] = $request['vendor_gmail'][$i];
            }
        }
        $rules = array_merge($rules, $vendorRules);
        $this->validate($request, $rules);

        $source = $request->get('source', '');
        $data = $request->except(['_token', 'create_user']);

        if (empty($data['whatsapp_number'])) {
            //get default whatsapp number for vendor from whatsapp config
            $task_info = DB::table('whatsapp_configs')
                ->select('*')
                ->whereRaw('find_in_set(' . self::DEFAULT_FOR . ',default_for)')
                ->first();
            if (isset($task_info->number) && $task_info->number != null) {
                $data['whatsapp_number'] = $task_info->number;
            }
        }

        if (empty($data['default_phone'])) {
            $data['default_phone'] = $data['phone'];
        }

        if (! empty($source)) {
            $data['status'] = 0;
        }
        if (! empty($request['framework'])) {
            $data['framework'] = implode(',', $request['framework']);
        }
        if (! empty($request['frequency_of_payment'])) {
            $data['frequency_of_payment'] = implode(',', $request['frequency_of_payment']);
        }

        $VPHA = 0;
        $VPH = new VendorPriceHistory;
        if (! empty($request['price'])) {
            $data['price'] = $request['price'];
            $VPH->price = $request['price'];
            $VPHA = 1;
        }
        if (! empty($request['currency'])) {
            $data['currency'] = $request['currency'];
            $VPH->currency = $request['currency'];
            $VPHA = 1;
        }
        if (! empty($request['price_remarks'])) {
            $data['price_remarks'] = $request['price_remarks'];
            $VPH->hisotry = $request['price_remarks'];
            $VPHA = 1;
        }

        $mainVendorData[0] = $data;
        $existArray = [];
        $sourceStatus = $validateStatus = false;
        $inputsData = array_merge($mainVendorData, $vendorData);
        foreach ($inputsData as $key => $data) {
            $vendorInsert = Vendor::create($data);

            if ($VPHA == 1) {
                $VPH->vendor_id = $vendorInsert->id;
                $VPH->user_id = Auth::user()->id;
                $VPH->save();
            }

            if ($request->create_user == 'on') {
                if ($data['email'] != null) {
                    $userEmail = User::where('email', $data['email'])->first();
                } else {
                    $userEmail = null;
                }
                if ($key == 0) {
                    $userPhone = User::where('phone', $data['phone'])->first();
                }
                if ($userEmail == null) {
                    $user = new User;
                    $user->name = str_replace(' ', '_', $data['name']);
                    if ($data['email'] == null) {
                        $email = str_replace(' ', '_', $data['name']) . '@solo.com';
                    } else {
                        $email = $data['email'];
                    }
                    $password = Str::random(10);
                    $user->email = $email;
                    $user->gmail = $data['gmail'];
                    $user->password = Hash::make($password);
                    $user->phone = ! empty($data['phone']) ? $data['phone'] : null;

                    // check the default whatsapp no and store it
                    $whpno = \DB::table('whatsapp_configs')
                        ->select('*')
                        ->whereRaw('find_in_set(4,default_for)')
                        ->first();
                    if ($whpno) {
                        $user->whatsapp_number = $whpno->number;
                    }

                    $user->save();
                    $role = Role::where('name', 'Developer')->first();
                    $user->roles()->sync($role->id);
                    $message = 'We have created an account for you on our ERP. You can login using the following details: url: https://erp.theluxuryunlimited.com/ username: ' . $email . ' password:  ' . $password . '';
                    if ($key == 0) {
                        app(\App\Http\Controllers\WhatsAppController::class)->sendWithThirdApi($data['phone'], $user->whatsapp_number, $message);
                    }
                } else {
                    if (! empty($source)) {
                        $sourceStatus = true;
                    }
                    $validateStatus = true;
                    $existArray[] = $data['name'];
                }
            }
        }
        if ($sourceStatus) {
            return redirect()->back()->withErrors('Vendor Created , couldnt create User, Email or Phone Already Exist');
        }
        $existArrayString = '';
        if ($validateStatus) {
            if (! empty($existArray)) {
                $existArrayString = '(' . implode(',', $existArray) . ')';
            }

            return redirect()->route('vendors.index')->withErrors('Vendor Created , couldnt create User ' . $existArrayString . ', Email or Phone Already Exist');
        }

        $isInvitedOnGithub = false;
        if ($request->create_user_github == 'on' && isset($request->email) && isset($request->organization_id)) {
            //has requested for github invitation
            $isInvitedOnGithub = $this->sendGithubInvitaion($request->email, $request->organization_id);
        }

        $isInvitedOnHubstaff = false;
        if ($request->create_user_hubstaff == 'on' && isset($request->email)) {
            //has requested hubstaff invitation
            $isInvitedOnHubstaff = $this->sendHubstaffInvitation($request->email);
        }

        if (! empty($source)) {
            return redirect()->back()->withSuccess('You have successfully saved a vendor!');
        }

        return redirect()->route('vendors.index')->withSuccess('You have successfully saved a vendor!');
    }

    public function productStore(Request $request)
    {
        $this->validate($request, [
            'vendor_id' => 'required|numeric',
            'images.*' => 'sometimes|nullable|image',
            'date_of_order' => 'required|date',
            'name' => 'required|string|max:255',
            'qty' => 'sometimes|nullable|numeric',
            'price' => 'sometimes|nullable|numeric',
            'payment_terms' => 'sometimes|nullable|string',
            'recurring_type' => 'required|string',
            'delivery_date' => 'sometimes|nullable|date',
            'received_by' => 'sometimes|nullable|string',
            'approved_by' => 'sometimes|nullable|string',
            'payment_details' => 'sometimes|nullable|string',
        ]);

        $data = $request->except('_token');

        $product = VendorProduct::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)
                    ->toDirectory('vendorproduct/' . floor($product->id / config('constants.image_per_folder')))
                    ->upload();
                $product->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->back()->withSuccess('You have successfully saved a vendor product!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vendor = Vendor::find($id);
        $vendor_categories = VendorCategory::all();
        $vendor_show = true;
        $emails = [];
        $reply_categories = ReplyCategory::all();
        $users_array = Helpers::getUserArray(User::all());

        return view('vendors.show', [
            'vendor' => $vendor,
            'vendor_categories' => $vendor_categories,
            'vendor_show' => $vendor_show,
            'reply_categories' => $reply_categories,
            'users_array' => $users_array,
            'emails' => $emails,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'category_id' => 'sometimes|nullable|numeric',
            'name' => 'required|string|max:255',
            'address' => 'sometimes|nullable|string',
            'phone' => 'sometimes|nullable|numeric',
            'default_phone' => 'sometimes|nullable|numeric',
            'whatsapp_number' => 'sometimes|nullable|numeric',
            'email' => 'sometimes|nullable|email',
            'social_handle' => 'sometimes|nullable',
            'website' => 'sometimes|nullable',
            'login' => 'sometimes|nullable',
            'password' => 'sometimes|nullable',
            'gst' => 'sometimes|nullable|max:255',
            'account_name' => 'sometimes|nullable|max:255',
            'account_iban' => 'sometimes|nullable|max:255',
            'account_swift' => 'sometimes|nullable|max:255',
            'frequency_of_payment' => 'sometimes|nullable|max:255',
            'bank_name' => 'sometimes|nullable|max:255',
            'bank_address' => 'sometimes|nullable|max:255',
            'city' => 'sometimes|nullable|max:255',
            'country' => 'sometimes|nullable|max:255',
            'ifsc_code' => 'sometimes|nullable|max:255',
            'remark' => 'sometimes|nullable|max:255',
        ]);

        $vendorData = Vendor::find($id);

        $data = $request->except('_token');

        if (! empty($request['framework'])) {
            $data['framework'] = implode(',', $request['framework']);
        }

        if (! empty($request['frequency_of_payment'])) {
            $data['frequency_of_payment'] = implode(',', $request['frequency_of_payment']);
        }

        $VPHA = 0;
        $VPH = new VendorPriceHistory;
        if (! empty($request['price'])) {
            $data['price'] = $request['price'];

            if ($vendorData['price'] != $request['price']) {
                $VPH->price = $request['price'];
                $VPHA = 1;
            }
        }
        if (! empty($request['currency'])) {
            $data['currency'] = $request['currency'];

            if ($vendorData['currency'] != $request['currency']) {
                $VPH->currency = $request['currency'];
                $VPHA = 1;
            }
        }
        if (! empty($request['price_remarks'])) {
            $data['price_remarks'] = $request['price_remarks'];

            if ($vendorData['price_remarks'] != $request['price_remarks']) {
                $VPH->hisotry = $request['price_remarks'];
                $VPHA = 1;
            }
        }

        Vendor::find($id)->update($data);

        if ($VPHA == 1) {
            $VPH->vendor_id = $id;
            $VPH->user_id = Auth::user()->id;
            $VPH->save();
        }

        return redirect()->route('vendors.index')->withSuccess('You have successfully updated a vendor!');
    }

    public function productUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'vendor_id' => 'sometimes|nullable|numeric',
            'images.*' => 'sometimes|nullable|image',
            'date_of_order' => 'required|date',
            'name' => 'required|string|max:255',
            'qty' => 'sometimes|nullable|numeric',
            'price' => 'sometimes|nullable|numeric',
            'payment_terms' => 'sometimes|nullable|string',
            'recurring_type' => 'required|string',
            'delivery_date' => 'sometimes|nullable|date',
            'received_by' => 'sometimes|nullable|string',
            'approved_by' => 'sometimes|nullable|string',
            'payment_details' => 'sometimes|nullable|string',
        ]);

        $data = $request->except('_token');

        $product = VendorProduct::find($id);
        $product->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)
                    ->toDirectory('vendorproduct/' . floor($product->id / config('constants.image_per_folder')))
                    ->upload();
                $product->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->back()->withSuccess('You have successfully updated a vendor product!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vendor = Vendor::find($id);
        $vendor->delete();

        return redirect()->route('vendors.index')->withSuccess('You have successfully deleted a vendor');
    }

    public function productDestroy($id)
    {
        $product = VendorProduct::find($id);

        $product->detachMediaTags(config('constants.media_tags'));
        $product->delete();

        return redirect()->back()->withSuccess('You have successfully deleted a vendor product!');
    }

    public function sendEmailBulk(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required|min:3|max:255',
            'message' => 'required',
            'cc.*' => 'nullable|email',
            'bcc.*' => 'nullable|email',
        ]);

        $fromEmail = 'buying@amourint.com';
        $fromName = 'buying';

        if ($request->from_mail) {
            $mail = \App\EmailAddress::where('id', $request->from_mail)->first();
            if ($mail) {
                $fromEmail = $mail->from_address;
                $fromName = $mail->from_name;
                $config = config('mail');
                unset($config['sendmail']);
                $configExtra = [
                    'driver' => $mail->driver,
                    'host' => $mail->host,
                    'port' => $mail->port,
                    'from' => [
                        'address' => $mail->from_address,
                        'name' => $mail->from_name,
                    ],
                    'encryption' => $mail->encryption,
                    'username' => $mail->username,
                    'password' => $mail->password,
                ];
                \Config::set('mail', array_merge($config, $configExtra));
                (new \Illuminate\Mail\MailServiceProvider(app()))->register();
            }
        }

        if ($request->vendor_ids) {
            $vendor_ids = explode(',', $request->vendor_ids);
            $vendors = Vendor::whereIn('id', $vendor_ids)->get();
        }

        if ($request->vendors) {
            $vendors = Vendor::where('id', $request->vendors)->get();
        } else {
            if ($request->not_received != 'on' && $request->received != 'on') {
                return redirect()->route('vendors.index')->withErrors(['Please select vendors']);
            }
        }

        if ($request->not_received == 'on') {
            $vendors = Vendor::doesnthave('emails')->where(function ($query) {
                $query->whereNotNull('email');
            })->get();
        }

        if ($request->received == 'on') {
            $vendors = Vendor::whereDoesntHave('emails', function ($query) {
                $query->where('type', 'incoming');
            })->where(function ($query) {
                $query->orWhereNotNull('email');
            })->where('has_error', 0)->get();
        }

        $file_paths = [];

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $filename = $file->getClientOriginalName();

                $file->storeAs('documents', $filename, 'files');

                $file_paths[] = "documents/$filename";
            }
        }

        $cc = $bcc = [];
        if ($request->has('cc')) {
            $cc = array_values(array_filter($request->cc));
        }
        if ($request->has('bcc')) {
            $bcc = array_values(array_filter($request->bcc));
        }

        foreach ($vendors as $vendor) {
            $mail = Mail::to($vendor->email);

            if ($cc) {
                $mail->cc($cc);
            }
            if ($bcc) {
                $mail->bcc($bcc);
            }

            $mail->send(new PurchaseEmail($request->subject, $request->message, $file_paths, ['from' => $fromEmail]));

            $params = [
                'model_id' => $vendor->id,
                'model_type' => Vendor::class,
                'from' => $fromEmail,
                'seen' => 1,
                'to' => $vendor->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'template' => 'customer-simple',
                'additional_data' => json_encode(['attachment' => $file_paths]),
                'cc' => $cc ?: null,
                'bcc' => $bcc ?: null,
            ];

            Email::create($params);
        }

        return redirect()->route('vendors.index')->withSuccess('You have successfully sent emails in bulk!');
    }

    public function sendEmail(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required|min:3|max:255',
            'message' => 'required',
            'email.*' => 'required|email',
            'cc.*' => 'nullable|email',
            'bcc.*' => 'nullable|email',
        ]);

        $vendor = Vendor::find($request->vendor_id);

        $fromEmail = 'buying@amourint.com';
        $fromName = 'buying';

        if ($request->from_mail) {
            $mail = \App\EmailAddress::where('id', $request->from_mail)->first();
            if ($mail) {
                $fromEmail = $mail->from_address;
                $fromName = $mail->from_name;
                $config = config('mail');
                unset($config['sendmail']);
                $configExtra = [
                    'driver' => $mail->driver,
                    'host' => $mail->host,
                    'port' => $mail->port,
                    'from' => [
                        'address' => $mail->from_address,
                        'name' => $mail->from_name,
                    ],
                    'encryption' => $mail->encryption,
                    'username' => $mail->username,
                    'password' => $mail->password,
                ];
                \Config::set('mail', array_merge($config, $configExtra));
                (new \Illuminate\Mail\MailServiceProvider(app()))->register();
            }
        }

        if ($vendor->email != '') {
            $file_paths = [];

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $filename = $file->getClientOriginalName();

                    $file->storeAs('documents', $filename, 'files');

                    $file_paths[] = "documents/$filename";
                }
            }

            $cc = $bcc = [];
            $emails = $request->email;

            if ($request->has('cc')) {
                $cc = array_values(array_filter($request->cc));
            }
            if ($request->has('bcc')) {
                $bcc = array_values(array_filter($request->bcc));
            }

            if (is_array($emails) && ! empty($emails)) {
                $to = array_shift($emails);
                $cc = array_merge($emails, $cc);

                $mail = Mail::to($to);

                if ($cc) {
                    $mail->cc($cc);
                }
                if ($bcc) {
                    $mail->bcc($bcc);
                }

                $mail->send(new PurchaseEmail($request->subject, $request->message, $file_paths, ['from' => $fromEmail]));
            } else {
                return redirect()->back()->withErrors('Please select an email');
            }

            $params = [
                'model_id' => $vendor->id,
                'model_type' => Vendor::class,
                'from' => $fromEmail,
                'to' => $request->email[0],
                'seen' => 1,
                'subject' => $request->subject,
                'message' => $request->message,
                'template' => 'customer-simple',
                'additional_data' => json_encode(['attachment' => $file_paths]),
                'cc' => $cc ?: null,
                'bcc' => $bcc ?: null,
            ];

            Email::create($params);

            return redirect()->route('vendors.show', $vendor->id)->withSuccess('You have successfully sent an email!');
        }
    }

    public function emailInbox(Request $request)
    {
        try {
            $cm = new ClientManager();
            $imap = $cm->make([
                'host' => env('IMAP_HOST_PURCHASE'),
                'port' => env('IMAP_PORT_PURCHASE'),
                'encryption' => env('IMAP_ENCRYPTION_PURCHASE'),
                'validate_cert' => env('IMAP_VALIDATE_CERT_PURCHASE'),
                'username' => env('IMAP_USERNAME_PURCHASE'),
                'password' => env('IMAP_PASSWORD_PURCHASE'),
                'protocol' => env('IMAP_PROTOCOL_PURCHASE'),
            ]);

            $imap->connect();
            if ($request->vendor_id) {
                $vendor = Vendor::find($request->vendor_id);

                if ($request->type == 'inbox') {
                    $inbox_name = 'INBOX';
                    $direction = 'from';
                    $type = 'incoming';
                } else {
                    $inbox_name = 'INBOX.Sent';
                    $direction = 'to';
                    $type = 'outgoing';
                }

                $inbox = $imap->getFolder($inbox_name);

                $latest_email = Email::where('type', $type)->where('model_id', $vendor->id)->where('model_type', \App\Vendor::class)->latest()->first();

                $latest_email_date = $latest_email
                    ? Carbon::parse($latest_email->created_at)
                    : Carbon::parse('1990-01-01');

                $vendorAgentsCount = $vendor->agents()->count();

                if ($vendorAgentsCount == 0) {
                    $emails = $inbox->messages()->where($direction, $vendor->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
                    $emails = $emails->leaveUnread()->get();
                    $this->createEmailsForEmailInbox($vendor, $type, $latest_email_date, $emails);
                } elseif ($vendorAgentsCount == 1) {
                    $emails = $inbox->messages()->where($direction, $vendor->agents[0]->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
                    $emails = $emails->leaveUnread()->get();
                    $this->createEmailsForEmailInbox($vendor, $type, $latest_email_date, $emails);
                } else {
                    foreach ($vendor->agents as $key => $agent) {
                        if ($key == 0) {
                            $emails = $inbox->messages()->where($direction, $agent->email)->where([
                                ['SINCE', $latest_email_date->format('d M y H:i')],
                            ]);
                            $emails = $emails->leaveUnread()->get();
                            $this->createEmailsForEmailInbox($vendor, $type, $latest_email_date, $emails);
                        } else {
                            $additional = $inbox->messages()->where($direction, $agent->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
                            $additional = $additional->leaveUnread()->get();
                            $this->createEmailsForEmailInbox($vendor, $type, $latest_email_date, $additional);
                        }
                    }
                }

                $db_emails = $vendor->emails()->with('model')->where('type', $type)->get();

                $emails_array = [];
                $count = 0;
                foreach ($db_emails as $key2 => $email) {
                    $dateCreated = $email->created_at->format('D, d M Y');
                    $timeCreated = $email->created_at->format('H:i');
                    $userName = null;
                    if ($email->model instanceof Supplier) {
                        $userName = $email->model->supplier;
                    } elseif ($email->model instanceof Customer) {
                        $userName = $email->model->name;
                    }

                    $emails_array[$count + $key2]['id'] = $email->id;
                    $emails_array[$count + $key2]['subject'] = $email->subject;
                    $emails_array[$count + $key2]['seen'] = $email->seen;
                    $emails_array[$count + $key2]['type'] = $email->type;
                    $emails_array[$count + $key2]['date'] = $email->created_at;
                    $emails_array[$count + $key2]['from'] = $email->from;
                    $emails_array[$count + $key2]['to'] = $email->to;
                    $emails_array[$count + $key2]['message'] = $email->message;
                    $emails_array[$count + $key2]['cc'] = $email->cc;
                    $emails_array[$count + $key2]['bcc'] = $email->bcc;
                    $emails_array[$count + $key2]['replyInfo'] = "On {$dateCreated} at {$timeCreated}, $userName <{$email->from}> wrote:";
                    $emails_array[$count + $key2]['dateCreated'] = $dateCreated;
                    $emails_array[$count + $key2]['timeCreated'] = $timeCreated;
                }

                $emails_array = array_values(Arr::sort($emails_array, function ($value) {
                    return $value['date'];
                }));

                $emails_array = array_reverse($emails_array);

                $perPage = 10;
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $currentItems = array_slice($emails_array, $perPage * ($currentPage - 1), $perPage);
                $emails = new LengthAwarePaginator($currentItems, count($emails_array), $perPage, $currentPage);

                $view = view('vendors.partials.email', ['emails' => $emails, 'type' => $request->type])->render();

                return response()->json(['emails' => $view]);
            } else {
                return response()->json(['message' => 'Something went wrong! No request data vaialable.'], 422);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!'], 422);
        }
    }

    private function createEmailsForEmailInbox($vendor, $type, $latest_email_date, $emails)
    {
        foreach ($emails as $email) {
            $content = $email->hasHTMLBody() ? $email->getHTMLBody() : $email->getTextBody();

            if ($email->getDate()->format('Y-m-d H:i:s') > $latest_email_date->format('Y-m-d H:i:s')) {
                $attachments_array = [];
                $attachments = $email->getAttachments();

                $attachments->each(function ($attachment) use (&$attachments_array) {
                    file_put_contents(storage_path('app/files/email-attachments/' . $attachment->name), $attachment->content);
                    $path = 'email-attachments/' . $attachment->name;
                    $attachments_array[] = $path;
                });

                $params = [
                    'model_id' => $vendor->id,
                    'model_type' => Vendor::class,
                    'type' => $type,
                    'seen' => $email->getFlags()['seen'],
                    'from' => $email->getFrom()[0]->mail,
                    'to' => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                    'subject' => $email->getSubject(),
                    'message' => $content,
                    'template' => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $attachments_array]),
                    'created_at' => $email->getDate(),
                ];

                Email::create($params);
            }
        }
    }

    public function block(Request $request)
    {
        $vendor = Vendor::find($request->vendor_id);

        if ($vendor->is_blocked == 0) {
            $vendor->is_blocked = 1;
        } else {
            $vendor->is_blocked = 0;
        }

        $vendor->save();

        return response()->json(['is_blocked' => $vendor->is_blocked]);
    }

    public function addReply(Request $request)
    {
        $reply = $request->get('reply');
        $autoReply = [];
        // add reply from here
        if (! empty($reply)) {
            $autoReply = \App\Reply::updateOrCreate(
                ['reply' => $reply, 'model' => 'Vendor', 'category_id' => 1],
                ['reply' => $reply]
            );
        }

        return response()->json(['code' => 200, 'data' => $autoReply]);
    }

    public function deleteReply(Request $request)
    {
        $id = $request->get('id');

        if ($id > 0) {
            $autoReply = \App\Reply::where('id', $id)->first();
            if ($autoReply) {
                $autoReply->delete();
            }
        }

        return response()->json([
            'code' => 200, 'data' => \App\Reply::where('model', 'Vendor')
                ->whereNull('deleted_at')
                ->pluck('reply', 'id')
                ->toArray(),
        ]);
    }

    public function createUser(Request $request)
    {
        $vendor = Vendor::find($request->id);
        //Check If User Exist
        $userEmail = User::where('email', $vendor->email)->first();
        $userPhone = User::where('phone', $vendor->phone)->first();
        if ($userEmail == null && $userPhone == null) {
            $user = new User;
            $user->name = str_replace(' ', '_', $vendor->name);
            if ($vendor->email == null) {
                $email = str_replace(' ', '_', $vendor->name) . '@solo.com';
            } else {
                $email = $vendor->email;
            }
            $password = Str::random(10);
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->phone = $vendor->phone;
            $user->save();
            $role = Role::where('name', 'Developer')->first();
            $user->roles()->sync($role->id);
            $message = 'We have created an account for you on our ERP. You can login using the following details: url: https://erp.theluxuryunlimited.com/ username: ' . $email . ' password:  ' . $password . '';
            app(\App\Http\Controllers\WhatsAppController::class)->sendWithThirdApi($vendor->phone, '', $message);

            return response()->json(['code' => 200, 'data' => 'User Created']);
        } else {
            return response()->json(['code' => 200, 'data' => "Couldn't Create User Email or Phone Already Exist"]);
        }
    }

    public function inviteGithub(Request $request)
    {
        $email = $request->get('email');
        $organizationId = $request->get('organizationId');

        if (! empty($email) && strlen($organizationId) > 0) {
            if ($this->sendGithubInvitaion($email, $organizationId)) {
                return response()->json(
                    ['message' => 'Invitation sent to ' . $email]
                );
            }

            return response()->json(
                ['message' => 'Unable to send invitation to ' . $email],
                500
            );
        }

        return response()->json(
            ['message' => 'Email not mentioned'],
            400
        );
    }

    public function inviteHubstaff(Request $request)
    {
        $email = $request->get('email');
        if ($email) {
            $response = $this->sendHubstaffInvitation($email);
            if ($response['code'] == 200) {
                return response()->json(
                    ['message' => 'Invitation sent to ' . $email]
                );
            }

            return response()->json(
                ['message' => $response['message']],
                500
            );
        }

        return response()->json(
            ['message' => 'Email not mentioned'],
            400
        );
    }

    private function sendGithubInvitaion(string $email, $organizationId)
    {
        return $this->inviteUser($email, $organizationId);
    }

    public function changeHubstaffUserRole(Request $request)
    {
        $id = $request->vendor_id;
        $role = $request->role;
        if ($id && $role && $role != '') {
            $vendor = Vendor::find($id);
            $user = User::where('phone', $vendor->phone)->first();
            if ($user) {
                $member = \App\Hubstaff\HubstaffMember::where('user_id', $user->id)->first();
                if ($member) {
                    $hubstaff_member_id = $member->hubstaff_user_id;
                    // $hubstaff_member_id = 901839;
                    $response = $this->changeHubstaffUserRoleApi($hubstaff_member_id);
                    if ($response['code'] == 200) {
                        return response()->json(['message' => 'Role successfully changed in the hubstaff'], 200);
                    } else {
                        return response()->json(['message' => $response['message']], 500);
                    }
                }
            }
        }

        return response()->json(['message' => 'User or hubstaff member not found'], 500);
    }

    private function changeHubstaffUserRoleApi($hubstaff_member_id)
    {
        try {
            $tokens = $this->getTokens();
            $url = 'https://api.hubstaff.com/v2/organizations/' . config('env.HUBSTAFF_ORG_ID') . '/update_members';
            $client = new GuzzleHttpClient();
            $body = [
                'members' => [
                    [
                        'user_id' => $hubstaff_member_id,
                        'role' => 'user',
                    ],
                ],
            ];

            $response = $client->put(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $tokens->access_token,
                        'Content-Type' => 'application/json',
                    ],
                    RequestOptions::BODY => json_encode($body),
                ]
            );
            $message = [
                'code' => 200,
                'message' => 'Successful',
            ];

            return $message;
        } catch (\Exception $e) {
            $exception = (string) $e->getResponse()->getBody();
            $exception = json_decode($exception);
            if ($e->getCode() != 200) {
                $message = [
                    'code' => 500,
                    'message' => $exception->error,
                ];

                return $message;
            } else {
                $message = [
                    'code' => 200,
                    'message' => 'Successful',
                ];

                return $message;
            }
        }
    }

    private function sendHubstaffInvitation(string $email)
    {
        try {
            $tokens = $this->getTokens();
            $url = 'https://api.hubstaff.com/v2/organizations/' . config('env.HUBSTAFF_ORG_ID') . '/invites';
            $client = new GuzzleHttpClient();
            $response = $client->post(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $tokens->access_token,
                        'Content-Type' => 'application/json',
                    ],
                    RequestOptions::JSON => [
                        'email' => $email,
                    ],
                ]
            );
            $message = [
                'code' => 200,
                'message' => 'Successful',
            ];

            return $message;
        } catch (\Exception $e) {
            $exception = (string) $e->getResponse()->getBody();
            $exception = json_decode($exception);
            if ($e->getCode() != 200) {
                $message = [
                    'code' => 500,
                    'message' => $exception->error,
                ];

                return $message;
            } else {
                $message = [
                    'code' => 200,
                    'message' => 'Successful',
                ];

                return $message;
            }
        }
    }

    public function changeStatus(Request $request)
    {
        $vendorId = $request->get('vendor_id');
        $statusId = $request->get('status');

        if (! empty($vendorId)) {
            $vendor = \App\Vendor::find($vendorId);
            if (! empty($vendor)) {
                $vendor->status = ($statusId == 'false') ? 0 : 1;
                $vendor->save();
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Status updated successfully']);
    }

    public function sendMessage(Request $request)
    {
        set_time_limit(0);
        $vendors = Vendor::whereIn('id', $request->vendors)->get();
        //Create broadcast
        $broadcast = \App\BroadcastMessage::create(['name' => $request->name]);
        if (count($vendors)) {
            foreach ($vendors as $key => $item) {
                $params = [
                    'vendor_id' => $item->id,
                    'number' => null,
                    'message' => $request->message,
                    'user_id' => Auth::id(),
                    'status' => 2,
                    'approved' => 1,
                    'is_queue' => 0,
                ];
                $message = [
                    'type_id' => $item->id,
                    'type' => App\Vendor::class,
                    'broadcast_message_id' => $broadcast->id,
                ];
                $broadcastnumber = \App\BroadcastMessageNumber::create($message);
                $chat_message = ChatMessage::create($params);
                $myRequest = new Request();
                $myRequest->setMethod('POST');
                $myRequest->request->add(['messageId' => $chat_message->id]);
                app(\App\Http\Controllers\WhatsAppController::class)->approveMessage('vendor', $myRequest);
            }
        }
        // return $params;

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Message sent successfully']);
    }

    public function editVendor(Request $request)
    {
        if (! $request->vendor_id || $request->vendor_id == '' || ! $request->column || $request->column == '' || ! $request->value || $request->value == '') {
            return response()->json(['message' => 'Incomplete data'], 500);
        }
        $vendor = Vendor::find($request->vendor_id);
        $column = $request->column;
        $vendor->$column = $request->value;
        $vendor->save();

        return response()->json(['message' => 'Successful'], 200);
    }

    public function addStatus(Request $request)
    {
        if ($request->vendor_id == '' || $request->status == '' || $request->agency == '' || $request->hourly_rate == '' || $request->available_hour == '' || $request->experience_level == '' || $request->communication_skill == '') {
            return response()->json(['message' => 'Incomplete data'], 500);
        }
        $vendorStatus = VendorStatusDetail::where('vendor_id', $request->vendor_id)->first();
        if (! $vendorStatus) {
            $vendorStatus = new VendorStatusDetail();
        }
        $vendorStatus->vendor_id = $request->vendor_id;
        $vendorStatus->user_id = Auth::user()->id;
        $vendorStatus->status = $request->status;
        $vendorStatus->hourly_rate = $request->hourly_rate;
        $vendorStatus->available_hour = $request->available_hour;
        $vendorStatus->experience_level = $request->experience_level;
        $vendorStatus->communication_skill = $request->communication_skill;
        $vendorStatus->agency = $request->agency;
        $vendorStatus->remark = $request->remark;
        $vendorStatus->save();

        $vendorStatusHistory = new VendorStatusDetailHistory();
        $vendorStatusHistory->vendor_id = $request->vendor_id;
        $vendorStatusHistory->user_id = Auth::user()->id;
        $vendorStatusHistory->status = $request->status;
        $vendorStatusHistory->hourly_rate = $request->hourly_rate;
        $vendorStatusHistory->available_hour = $request->available_hour;
        $vendorStatusHistory->experience_level = $request->experience_level;
        $vendorStatusHistory->communication_skill = $request->communication_skill;
        $vendorStatusHistory->agency = $request->agency;
        $vendorStatusHistory->remark = $request->remark;
        $vendorStatusHistory->save();

        return response()->json(['message' => 'Successful'], 200);
    }

    public function statusStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);
        $data = $request->except('_token');
        VendorStatus::create($data);

        return redirect()->back()->with('success', 'You have successfully created a status!');
    }

    public function updateStatus(Request $request)
    {
        $vendor = Vendor::find($request->id);
        $vendor->vendor_status = $request->status;
        $vendor->save();

        $vshm = new VSHM;
        $vshm->vendor_id = $request->id;
        $vshm->user_id = $request->user_id;
        $vshm->status = $request->status;
        $vshm->save();
    }

    public function vendorStatusHistory(Request $request)
    {
        $data = VSHM::with(['user' => function ($query) {
        }])->where('vendor_id', $request->id)->get();

        return response()->json(['code' => 200, 'data' => $data, 'message' => 'Message sent successfully']);
    }

    public function vendorDetailStatusHistory(Request $request)
    {
        $data = VendorStatusDetailHistory::where('vendor_id', $request->id)->with('user')->get();

        return response()->json(['code' => 200, 'data' => $data, 'message' => 'Message sent successfully']);
    }

    public function zoomMeetingList(Request $request)
    {
        $meetings = ZoomMeetingDetails::get();

        return view('vendors.list-zoom-meetings', [
            'meetings' => $meetings,
        ]);
    }

    public function updateMeetingDescription(Request $request)
    {
        $meetingdata = ZoomMeetingDetails::find($request->id);
        $meetingdata->description = $request->description;
        $meetingdata->save();

        return response()->json(['code' => 200, 'message' => 'Successful'], 200);
    }

    public function refreshMeetingList(Request $request)
    {
        \Artisan::call('save:zoom-meetings');

        return redirect()->back();
    }

    public function syncMeetingsRecordings(Request $request)
    {
        \Artisan::call('zoom:meetings-sync');

        return redirect()->back();
    }

    public function statuscolor(Request $request)
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['colorname'] as $key => $value) {
            $status_vendor = VendorStatus::find($key);
            $status_vendor->name = $value;
            $status_vendor->color = $status_color['color_name'][$key];
            $status_vendor->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function deleteVStatus(Request $request)
    {
        try {
            VendorStatus::where('id', $request->id)->delete();

            return response()->json(['code' => '200', 'data' => [], 'message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function storeshortcut(Request $request)
    {
        $rules = [
            'category_id' => 'sometimes|nullable|numeric',
            'name' => 'required|string|max:255',
            'email' => 'sometimes|nullable|email',
            'gmail' => 'sometimes|nullable|email',
            'website' => 'sometimes|nullable',
        ];
        $vendorCount = ! empty($request['vendor_name']) ? count($request['vendor_name']) : 0;
        $vendorRules = $vendorData = [];
        $inputs = $request->all();
        if ($vendorCount !== '') {
            $vendorRules = [
                'vendor_name' => 'sometimes|array',
                'vendor_name.*' => 'sometimes|string|max:255',
                'vendor_email' => 'sometimes|array',
                'vendor_email.*' => 'sometimes|nullable|email',
                'vendor_gmail' => 'sometimes|array',
                'vendor_gmail.*' => 'sometimes|nullable|email',
            ];
            for ($i = 0; $i < $vendorCount; $i++) {
                $vendorData[$i]['category_id'] = $request['category_id'];
                $vendorData[$i]['name'] = $request['vendor_name'][$i];
                $vendorData[$i]['email'] = $request['vendor_email'][$i];
                $vendorData[$i]['gmail'] = $request['vendor_gmail'][$i];
            }
        }
        $rules = array_merge($rules, $vendorRules);
        $this->validate($request, $rules);

        $source = $request->get('source', '');
        $data = $request->except(['_token', 'create_user']);

        if (empty($data['whatsapp_number'])) {
            //get default whatsapp number for vendor from whatsapp config
            $task_info = DB::table('whatsapp_configs')
                ->select('*')
                ->whereRaw('find_in_set(' . self::DEFAULT_FOR . ',default_for)')
                ->first();
            if (isset($task_info->number) && $task_info->number != null) {
                $data['whatsapp_number'] = $task_info->number;
            }
        }

        if (empty($data['default_phone'])) {
            $data['default_phone'] = $data['phone'];
        }

        if (! empty($source)) {
            $data['status'] = 0;
        }
        $mainVendorData[0] = $data;
        $existArray = [];
        $sourceStatus = $validateStatus = false;
        $inputsData = array_merge($mainVendorData, $vendorData);
        foreach ($inputsData as $key => $data) {
            if (! empty($data['framework'])) {
                $data['framework'] = implode(',', $data['framework']);
            }
            Vendor::create($data);

            if ($request->create_user == 'on') {
                if ($data['email'] != null) {
                    $userEmail = User::where('email', $data['email'])->first();
                } else {
                    $userEmail = null;
                }
                if ($key == 0) {
                    $userPhone = User::where('phone', $data['phone'])->first();
                }
                if ($userEmail == null) {
                    $user = new User;
                    $user->name = str_replace(' ', '_', $data['name']);
                    if ($data['email'] == null) {
                        $email = str_replace(' ', '_', $data['name']) . '@solo.com';
                    } else {
                        $email = $data['email'];
                    }
                    $password = Str::random(10);
                    $user->email = $email;
                    $user->gmail = $data['gmail'];
                    $user->password = Hash::make($password);
                    $user->phone = ! empty($data['phone']) ? $data['phone'] : null;

                    // check the default whatsapp no and store it
                    $whpno = \DB::table('whatsapp_configs')
                        ->select('*')
                        ->whereRaw('find_in_set(4,default_for)')
                        ->first();
                    if ($whpno) {
                        $user->whatsapp_number = $whpno->number;
                    }

                    $user->save();
                    $role = Role::where('name', 'Developer')->first();
                    $user->roles()->sync($role->id);
                    $message = 'We have created an account for you on our ERP. You can login using the following details: url: https://erp.theluxuryunlimited.com/ username: ' . $email . ' password:  ' . $password . '';
                    if ($key == 0) {
                        app(\App\Http\Controllers\WhatsAppController::class)->sendWithThirdApi($data['phone'], $user->whatsapp_number, $message);
                    }
                } else {
                    if (! empty($source)) {
                        $sourceStatus = true;
                    }
                    $validateStatus = true;
                    $existArray[] = $data['name'];
                }
            }
        }
        if ($sourceStatus) {
            return redirect()->back()->withErrors('Vendor Created , couldnt create User, Email or Phone Already Exist');
        }
        $existArrayString = '';
        if ($validateStatus) {
            if (! empty($existArray)) {
                $existArrayString = '(' . implode(',', $existArray) . ')';
            }

            return redirect()->route('vendors.index')->withErrors('Vendor Created , couldnt create User ' . $existArrayString . ', Email or Phone Already Exist');
        }

        $isInvitedOnGithub = false;
        if ($request->create_user_github == 'on' && isset($request->email) && isset($request->organization_id)) {
            //has requested for github invitation
            $isInvitedOnGithub = $this->sendGithubInvitaion($request->email, $request->organization_id);
        }

        $isInvitedOnHubstaff = false;
        if ($request->create_user_hubstaff == 'on' && isset($request->email)) {
            //has requested hubstaff invitation
            $isInvitedOnHubstaff = $this->sendHubstaffInvitation($request->email);
        }

        if (! empty($source)) {
            return redirect()->back()->withSuccess('You have successfully saved a vendor!');
        }

        return back()->with('success', 'You have successfully saved a vendor!');
    }

    public function columnVisbilityUpdate(Request $request)
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'vendors-listing')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'vendors-listing';
            $column->column_name = json_encode($request->column_vendors);
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'vendors-listing';
            $column->column_name = json_encode($request->column_vendors);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function framworkAdd(Request $request)
    {
        try {
            $framework = VendorFrequency::create(
                [
                    'user_id' => \Auth::user()->id,
                    'name' => $request->framework_name,
                ]
            );
            $framework = VendorFrameworks::where('id', $framework->id)->first();

            return response()->json(['code' => 200, 'data' => $framework, 'message' => 'Added successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function vendorRemarkHistory(Request $request)
    {
        $data = VendorRemarksHistory::with(['user' => function ($query) {
        }])->where('vendor_id', $request->id)->orderBy('id', 'DESC')->get();

        return response()->json(['code' => 200, 'data' => $data, 'message' => 'Message sent successfully']);
    }

    public function vendorRemarkPostHistory(Request $request)
    {
        try {
            $remarks = VendorRemarksHistory::create(
                [
                    'user_id' => \Auth::user()->id,
                    'remarks' => $request->remarks,
                    'vendor_id' => $request->vendor_id,
                ]
            );
            $remarks = VendorRemarksHistory::where('id', $remarks->id)->first();

            return response()->json(['code' => 200, 'data' => $remarks, 'message' => 'Added successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function flowChart(Request $request, $master_id)
    {
        $VendorFlowchart = Vendor::with('category');

        if (request('category') != null) {
            $VendorFlowchart = $VendorFlowchart->where('category_id', $request->category);
        }

        if ((! empty(request('selectedId')) && (request('selectedId') != null))) {
            $VendorFlowchart = $VendorFlowchart->where('id', $request->selectedId);
        }

        $VendorFlowchart = $VendorFlowchart->whereNotNull('flowchart_date')->orderBy('flowchart_date', 'DESC')->paginate(25);

        $totalVendor = Vendor::whereNotNull('flowchart_date')->count();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'vendors-flow-chart-listing')->first();

        $dynamicColumnsToShowVendorsfc = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowVendorsfc = json_decode($hideColumns, true);
        }

        $flowchart_master = VendorFlowChartMaster::where('id', $master_id)->get();

        $vendor_flow_charts = VendorFlowChart::where('master_id', $master_id)->orderBy('sorting', 'ASC')->get();

        $vendor_categories = VendorCategory::all();

        $status = VendorFlowChartStatus::all();

        return view('vendors.flow-chart', compact('VendorFlowchart', 'dynamicColumnsToShowVendorsfc', 'totalVendor', 'vendor_flow_charts', 'vendor_categories', 'status', 'master_id', 'flowchart_master'))
            ->with('i', ($request->input('page', 1) - 1) * 25);
    }

    public function flowchartStore(Request $request)
    {
        $this->validate($request, [
            'master_id' => 'required',
            'name' => 'required|string',
            'sorting' => 'required|numeric',
        ]);
        $data = $request->except('_token');
        $data['created_by'] = Auth::user()->id;
        VendorFlowChart::create($data);

        return redirect()->back()->with('success', 'You have successfully created a flow chart!');
    }

    public function getFlowchartMaster()
    {
        $flowchart_master = VendorFlowChartMaster::all();

        return $flowchart_master;
    }

    public function masterFlowchartStore(Request $request)
    {
        try {
            $this->validate($request, [
                'title' => 'required|string',
            ]);

            $data = $request->except('_token');
            $flowchart = VendorFlowChartMaster::create($data);

            return response()->json(['code' => '200', 'data' => $flowchart, 'all' => $this->getFlowchartMaster(), 'message' => 'You have successfully created a master flow chart!']);
        } catch (\Throwable $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function masterFlowchartDestroy($id)
    {
        try {
            $flowchart = VendorFlowChartMaster::find($id);
            if ($flowchart->flow_charts()->get()->isEmpty()) {
                $flowchart->delete();

                return response()->json(['code' => '200', 'data' => [], 'all' => $this->getFlowchartMaster(), 'message' => 'You have successfully deleted a master flow chart!']);
            } else {
                return response()->json(['code' => '400', 'message' => 'Vendor flow charts not empty!']);
            }
        } catch (\Throwable $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function masterFlowchartUpdate(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'title' => 'required|string',
            ]);

            $data = $request->except('_token');
            $flowchart = VendorFlowChartMaster::find($id);
            $flowchart->update($data);

            return response()->json(['code' => '200', 'data' => [], 'all' => $this->getFlowchartMaster(), 'message' => 'You have successfully updated a master flow chart!']);
        } catch (\Throwable $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function questionStore(Request $request)
    {
        $this->validate($request, [
            'question' => 'required',
        ]);
        $data = $request->except('_token');
        $data['created_by'] = Auth::user()->id;
        VendorQuestions::create($data);

        return redirect()->back()->with('success', 'You have successfully created a question!');
    }

    public function rquestionStore(Request $request)
    {
        $this->validate($request, [
            'question' => 'required',
        ]);
        $data = $request->except('_token');
        $data['created_by'] = Auth::user()->id;
        VendorRatingQuestions::create($data);

        return redirect()->back()->with('success', 'You have successfully created a question!');
    }

    public function vendorFlowchart(Request $request)
    {
        $vendor = Vendor::find($request->id);

        if (isset($request->flowcharts) && ! empty($request->flowcharts)) {
            $vendor->flowchart_date = Carbon::now();
            $vendor->fc_status = 1;
            $vendor->flowcharts = $request->flowcharts;
        } else {
            $vendor->flowchart_date = null;
            $vendor->fc_status = 0;
            $vendor->flowcharts = null;
        }

        $vendor->update();

        return redirect()->back()->with('success', 'You have successfully created a flow chart!');
    }

    public function saveVendorFlowChartRemarks(Request $request)
    {
        $post = $request->all();

        $this->validate($request, [
            'vendor_id' => 'required',
            'flow_chart_id' => 'required',
            'remarks' => 'required',
        ]);

        $input = $request->except(['_token']);
        $input['added_by'] = Auth::user()->id;
        VendorFlowChartRemarks::create($input);

        return response()->json(['code' => 200, 'data' => $input]);
    }

    public function getFlowChartRemarksHistories(Request $request)
    {
        $datas = VendorFlowChartRemarks::with(['user'])
            ->where('vendor_id', $request->vendor_id)
            ->where('flow_chart_id', $request->flow_chart_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function vendorFlowChartVolumnVisbilityUpdate(Request $request)
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'vendors-flow-chart-listing')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'vendors-flow-chart-listing';
            $column->column_name = json_encode($request->column_vendorsfc);
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'vendors-flow-chart-listing';
            $column->column_name = json_encode($request->column_vendorsfc);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function vendorRqaVolumnVisbilityUpdate(Request $request)
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'vendors-rqa-listing')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'vendors-rqa-listing';
            $column->column_name = json_encode($request->column_vendorsfc);
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'vendors-rqa-listing';
            $column->column_name = json_encode($request->column_vendorsfc);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function vendorQaVolumnVisbilityUpdate(Request $request)
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'vendors-qa-listing')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'vendors-qa-listing';
            $column->column_name = json_encode($request->column_vendorsfc);
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'vendors-qa-listing';
            $column->column_name = json_encode($request->column_vendorsfc);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function getVendorAutocomplete(Request $request)
    {
        $input = $_GET['term'];

        $products = [];
        if (! empty($input)) {
            $products = Vendor::where('name', 'like', '%' . $input . '%')->whereNull('deleted_at')->pluck('name', 'id');
        }

        return response()->json($products);
    }

    public function sortingVendorFlowchart(Request $request)
    {
        $flow_chart = $request->all();
        $data = $request->except('_token');
        foreach ($flow_chart['sorting'] as $key => $value) {
            $vendor_fc = VendorFlowChart::find($key);
            $vendor_fc->sorting = $value;
            $vendor_fc->save();
        }

        return redirect()->back()->with('success', 'The flow-chart sorting updated successfully.');
    }

    public function vendorFeedbackStatus(Request $request)
    {
        $vendor = Vendor::find($request->id);

        if (empty($vendor->feeback_status)) {
            $data['feeback_status'] = 1;
        } else {
            $data['feeback_status'] = 0;
        }

        Vendor::find($request->id)->update($data);

        return redirect()->back()->with('success', 'Vendor feedback status has been updated!');
    }

    public function getVendorQuestions(Request $request)
    {
        $datas = VendorQuestions::with(['user'])
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'Question get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getVendorRatingQuestions(Request $request)
    {
        $datas = VendorQuestions::with(['user'])
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'Question get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getQuestionAnswerHistories(Request $request)
    {
        $datas = VendorQuestionAnswer::where('vendor_id', $request->vendor_id)
            ->where('question_id', $request->question_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getRatingQuestionAnswerHistories(Request $request)
    {
        $datas = VendorRatingQuestionAnswer::where('vendor_id', $request->vendor_id)
            ->where('question_id', $request->question_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function saveVendorQuestionAnswer(Request $request)
    {
        $post = $request->all();

        $this->validate($request, [
            'vendor_id' => 'required',
            'question_id' => 'required',
            'answer' => 'required',
        ]);

        $input = $request->except(['_token']);
        $input['added_by'] = Auth::user()->id;
        VendorQuestionAnswer::create($input);

        return response()->json(['code' => 200, 'data' => $input]);
    }

    public function saveVendorRatingQuestionAnswer(Request $request)
    {
        $post = $request->all();

        $this->validate($request, [
            'vendor_id' => 'required',
            'question_id' => 'required',
            'answer' => 'required',
        ]);

        $input = $request->except(['_token']);
        $input['added_by'] = Auth::user()->id;
        VendorRatingQuestionAnswer::create($input);

        return response()->json(['code' => 200, 'data' => $input]);
    }

    public function vendorQuestionAnswerStatus(Request $request)
    {
        $vendor = Vendor::find($request->id);

        if (empty($vendor->question_status)) {
            $data['question_status'] = 1;
        } else {
            $data['question_status'] = null;
        }

        Vendor::find($request->id)->update($data);

        return redirect()->back()->with('success', 'You have successfully created a question answer!');
    }

    public function vendorRatingQuestionAnswerStatus(Request $request)
    {
        $vendor = Vendor::find($request->id);

        if (empty($vendor->rating_question_status)) {
            $data['rating_question_status'] = 1;
        } else {
            $data['rating_question_status'] = null;
        }

        Vendor::find($request->id)->update($data);

        return redirect()->back()->with('success', 'You have successfully created a question answer!');
    }

    public function questionAnswer(Request $request)
    {
        $VendorQuestionAnswer = Vendor::with('category');

        if (request('category') != null) {
            $VendorQuestionAnswer = $VendorQuestionAnswer->where('category_id', $request->category);
        }

        if ((! empty(request('selectedId')) && (request('selectedId') != null))) {
            $VendorQuestionAnswer = $VendorQuestionAnswer->where('id', $request->selectedId);
        }

        $VendorQuestionAnswer = $VendorQuestionAnswer->where('question_status', 1)->orderBy('flowchart_date', 'DESC')->paginate(25);

        $totalVendor = Vendor::where('question_status', 1)->count();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'vendors-qa-listing')->first();

        $dynamicColumnsToShowVendorsqa = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowVendorsqa = json_decode($hideColumns, true);
        }

        $vendor_questions = VendorQuestions::orderBy('sorting', 'ASC')->get();

        $vendor_categories = VendorCategory::all();

        $status = VendorRatingQAStatus::all();

        $status_q = VendorQuestionStatus::all();

        return view('vendors.question-answer', compact('VendorQuestionAnswer', 'dynamicColumnsToShowVendorsqa', 'totalVendor', 'vendor_questions', 'vendor_categories', 'status', 'status_q'))
            ->with('i', ($request->input('page', 1) - 1) * 25);
    }

    public function ratingquestionAnswer(Request $request)
    {
        $VendorQuestionAnswer = Vendor::with('category');

        if (request('category') != null) {
            $VendorQuestionAnswer = $VendorQuestionAnswer->where('category_id', $request->category);
        }

        if ((! empty(request('selectedId')) && (request('selectedId') != null))) {
            $VendorQuestionAnswer = $VendorQuestionAnswer->where('id', $request->selectedId);
        }

        $VendorQuestionAnswer = $VendorQuestionAnswer->where('rating_question_status', 1)->orderBy('flowchart_date', 'DESC')->paginate(25);

        $totalVendor = Vendor::where('rating_question_status', 1)->count();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'vendors-rqa-listing')->first();

        $dynamicColumnsToShowVendorsrqa = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowVendorsrqa = json_decode($hideColumns, true);
        }

        $rating_questions = VendorRatingQuestions::orderBy('sorting', 'ASC')->get();

        $vendor_categories = VendorCategory::all();

        $status = VendorRatingQAStatus::all();

        $status_q = VendorQuestionStatus::all();

        return view('vendors.rating-question-answer', compact('VendorQuestionAnswer', 'dynamicColumnsToShowVendorsrqa', 'totalVendor', 'rating_questions', 'vendor_categories', 'status', 'status_q'))
            ->with('i', ($request->input('page', 1) - 1) * 25);
    }

    public function rqaStatusCreate(Request $request)
    {
        try {
            $status = new VendorRatingQAStatus();
            $status->status_name = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function rqastatuscolor(Request $request)
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['colorname'] as $key => $value) {
            $vr_status = VendorRatingQAStatus::find($key);
            $vr_status->status_name = $value;
            $vr_status->status_color = $status_color['color_name'][$key];
            $vr_status->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function deleteFlowchartstatus(Request $request)
    {
        try {
            VendorFlowChartStatus::where('id', $request->id)->delete();

            return response()->json(['code' => '200', 'data' => [], 'message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function deleteQAStatus(Request $request)
    {
        try {
            VendorQuestionStatus::where('id', $request->id)->delete();

            return response()->json(['code' => '200', 'data' => [], 'message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function deleteRQAStatus(Request $request)
    {
        try {
            VendorRatingQAStatus::where('id', $request->id)->delete();

            return response()->json(['code' => '200', 'data' => [], 'message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function flowchartStatusCreate(Request $request)
    {
        try {
            $status = new VendorFlowChartStatus();
            $status->status_name = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function flowchartstatuscolor(Request $request)
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['colorname'] as $key => $value) {
            $vf_status = VendorFlowChartStatus::find($key);
            $vf_status->status_name = $value;
            $vf_status->status_color = $status_color['color_name'][$key];
            $vf_status->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function rqaupdateStatus(Request $request)
    {
        $vendor_id = $request->input('vendor_id');
        $question_id = $request->input('question_id');
        $selectedStatus = $request->input('selectedStatus');

        $vendor_status = VendorRatingQAStatusHistory::where('vendor_id', $vendor_id)->where('question_id', $question_id)->orderBy('id', 'DESC')->first();
        $history = new VendorRatingQAStatusHistory();
        $history->vendor_id = $vendor_id;
        $history->question_id = $question_id;

        if (! empty($vendor_status)) {
            $history->old_value = $vendor_status->new_value;
        } else {
            $history->old_value = '';
        }
        $history->new_value = $selectedStatus;
        $history->user_id = Auth::user()->id;
        $history->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function rqaStatusHistories(Request $request)
    {
        $datas = VendorRatingQAStatusHistory::with(['user', 'newValue', 'oldValue'])
            ->where('vendor_id', $request->vendor_id)
            ->where('question_id', $request->question_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function flowchartupdateStatus(Request $request)
    {
        $vendor_id = $request->input('vendor_id');
        $flow_chart_id = $request->input('flow_chart_id');
        $selectedStatus = $request->input('selectedStatus');

        $vendor_status = VendorFlowChartStatusHistory::where('vendor_id', $vendor_id)->where('flow_chart_id', $flow_chart_id)->orderBy('id', 'DESC')->first();
        $history = new VendorFlowChartStatusHistory();
        $history->vendor_id = $vendor_id;
        $history->flow_chart_id = $flow_chart_id;

        if (! empty($vendor_status)) {
            $history->old_value = $vendor_status->new_value;
        } else {
            $history->old_value = '';
        }
        $history->new_value = $selectedStatus;
        $history->user_id = Auth::user()->id;
        $history->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function flowchartStatusHistories(Request $request)
    {
        $datas = VendorFlowChartStatusHistory::with(['user', 'newValue', 'oldValue'])
            ->where('vendor_id', $request->vendor_id)
            ->where('flow_chart_id', $request->flow_chart_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getVendorRatingQuestionsAnswerNotes(Request $request)
    {
        $datas = VendorRatingQANotes::where('vendor_id', $request->vendor_id)
            ->where('question_id', $request->question_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'Question get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function notesStore(Request $request)
    {
        $this->validate($request, [
            'notes' => 'required',
        ]);
        $data = $request->except('_token');
        $data['user_id'] = Auth::user()->id;
        VendorRatingQANotes::create($data);

        return redirect()->back()->with('success', 'You have successfully created a notes!');
    }

    public function flowchartnotesStore(Request $request)
    {
        $this->validate($request, [
            'notes' => 'required',
        ]);
        $data = $request->except('_token');
        $data['user_id'] = Auth::user()->id;
        VendorFLowChartNotes::create($data);

        return redirect()->back()->with('success', 'You have successfully created a notes!');
    }

    public function qaStatusCreate(Request $request)
    {
        try {
            $status = new VendorQuestionStatus();
            $status->status_name = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function qastatuscolor(Request $request)
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['colorname'] as $key => $value) {
            $vq_status = VendorQuestionStatus::find($key);
            $vq_status->status_name = $value;
            $vq_status->status_color = $status_color['color_name'][$key];
            $vq_status->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function qaupdateStatus(Request $request)
    {
        $vendor_id = $request->input('vendor_id');
        $question_id = $request->input('question_id');
        $selectedStatus = $request->input('selectedStatus');

        $vendor_status = VendorQuestionStatusHistory::where('vendor_id', $vendor_id)->where('question_id', $question_id)->orderBy('id', 'DESC')->first();
        $history = new VendorQuestionStatusHistory();
        $history->vendor_id = $vendor_id;
        $history->question_id = $question_id;

        if (! empty($vendor_status)) {
            $history->old_value = $vendor_status->new_value;
        } else {
            $history->old_value = '';
        }
        $history->new_value = $selectedStatus;
        $history->user_id = Auth::user()->id;
        $history->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function qaStatusHistories(Request $request)
    {
        $datas = VendorQuestionStatusHistory::with(['user', 'newValue', 'oldValue'])
            ->where('vendor_id', $request->vendor_id)
            ->where('question_id', $request->question_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function vendorAllSection(Request $request)
    {
        $VendorFlowchart = [];
        $VendorQuestionAnswer = [];
        $VendorQuestionRAnswer = [];

        if ((! empty(request('vendors')) && (request('vendors') != null))) {
            $VendorFlowchart = Vendor::with('category');

            if ((! empty(request('vendors')) && (request('vendors') != null))) {
                $VendorFlowchart = $VendorFlowchart->whereIn('id', $request->vendors);
            }

            $VendorFlowchart = $VendorFlowchart->whereNotNull('flowchart_date')->orderBy('flowchart_date', 'DESC')->get();

            $VendorQuestionAnswer = Vendor::with('category');

            if ((! empty(request('vendors')) && (request('vendors') != null))) {
                $VendorQuestionAnswer = $VendorQuestionAnswer->whereIn('id', $request->vendors);
            }

            $VendorQuestionAnswer = $VendorQuestionAnswer->where('question_status', 1)->orderBy('flowchart_date', 'DESC')->get();

            $VendorQuestionRAnswer = Vendor::with('category');

            if ((! empty(request('vendors')) && (request('vendors') != null))) {
                $VendorQuestionRAnswer = $VendorQuestionRAnswer->whereIn('id', $request->vendors);
            }

            $VendorQuestionRAnswer = $VendorQuestionRAnswer->where('rating_question_status', 1)->orderBy('flowchart_date', 'DESC')->get();
        }

        $vendor_flow_charts = VendorFlowChart::orderBy('sorting', 'ASC')->get();

        $vendor_categories = VendorCategory::all();

        $status = VendorFlowChartStatus::all();

        $vendor_questions = VendorQuestions::orderBy('sorting', 'ASC')->get();

        $status_q = VendorQuestionStatus::all();

        $vendor_r_questions = VendorRatingQuestions::orderBy('sorting', 'ASC')->get();

        $status_r = VendorRatingQAStatus::all();

        return view('vendors.all-section', compact('VendorFlowchart', 'VendorQuestionAnswer', 'VendorQuestionRAnswer', 'vendor_flow_charts', 'vendor_categories', 'vendor_r_questions', 'status', 'vendor_questions', 'status_q', 'status_r'));
    }

    public function deleteFlowchartCategory(Request $request)
    {
        try {
            VendorFlowChart::where('id', $request->id)->delete();

            return response()->json(['code' => '200', 'data' => [], 'message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function deleteQACategory(Request $request)
    {
        try {
            VendorQuestions::where('id', $request->id)->delete();

            return response()->json(['code' => '200', 'data' => [], 'message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function deleteRQACategory(Request $request)
    {
        try {
            VendorRatingQuestions::where('id', $request->id)->delete();

            return response()->json(['code' => '200', 'data' => [], 'message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function flowchartSortOrder(Request $request)
    {
        $flowchart_vendor = $request->all();
        $data = $request->except('_token');
        foreach ($flowchart_vendor['sorting'] as $key => $value) {
            $f_vendor = VendorFlowChart::find($key);
            $f_vendor->sorting = $value;
            $f_vendor->save();
        }

        return redirect()->back()->with('success', 'The sort order updated successfully.');
    }

    public function qaSortOrder(Request $request)
    {
        $question_vendor = $request->all();
        $data = $request->except('_token');
        foreach ($question_vendor['sorting'] as $key => $value) {
            $qa_vendor = VendorQuestions::find($key);
            $qa_vendor->sorting = $value;
            $qa_vendor->save();
        }

        return redirect()->back()->with('success', 'The sort order updated successfully.');
    }

    public function rqaSortOrder(Request $request)
    {
        $rquestion_vendor = $request->all();
        $data = $request->except('_token');
        foreach ($rquestion_vendor['sorting'] as $key => $value) {
            $rqa_vendor = VendorRatingQuestions::find($key);
            $rqa_vendor->sorting = $value;
            $rqa_vendor->save();
        }

        return redirect()->back()->with('success', 'The sort order updated successfully.');
    }

    public function searchVendorFlowcharts(Request $request)
    {
        $vendor_id = $request->vendor_id;

        $VendorFlowchart = [];

        if ((! empty($vendor_id) && ($vendor_id != null))) {
            $VendorFlowchart = Vendor::with('category');

            $VendorFlowchart = $VendorFlowchart->where('id', $vendor_id);

            $VendorFlowchart = $VendorFlowchart->whereNotNull('flowchart_date')->orderBy('flowchart_date', 'DESC')->get();
        }

        $vendor_flow_charts = VendorFlowChart::orderBy('sorting', 'ASC')->get();

        $status = VendorFlowChartStatus::all();

        return view('vendors.partials.search-data-fc', compact('VendorFlowchart', 'vendor_flow_charts', 'status', 'vendor_id'));
    }

    public function searchVendorQa(Request $request)
    {
        $vendor_id = $request->vendor_id;

        $VendorQuestionAnswer = [];

        if ((! empty($vendor_id) && ($vendor_id != null))) {
            $VendorQuestionAnswer = Vendor::with('category');

            $VendorQuestionAnswer = $VendorQuestionAnswer->where('id', $vendor_id);

            $VendorQuestionAnswer = $VendorQuestionAnswer->where('question_status', 1)->orderBy('flowchart_date', 'DESC')->get();
        }

        $vendor_questions = VendorQuestions::orderBy('sorting', 'ASC')->get();

        $status_q = VendorQuestionStatus::all();

        return view('vendors.partials.search-data-qa', compact('VendorQuestionAnswer', 'vendor_questions', 'status_q', 'vendor_id'));
    }

    public function searchVendorRQa(Request $request)
    {
        $vendor_id = $request->vendor_id;

        $VendorQuestionRAnswer = [];

        if ((! empty($vendor_id) && ($vendor_id != null))) {
            $VendorQuestionRAnswer = Vendor::with('category');

            $VendorQuestionRAnswer = $VendorQuestionRAnswer->where('id', $vendor_id);

            $VendorQuestionRAnswer = $VendorQuestionRAnswer->where('rating_question_status', 1)->orderBy('flowchart_date', 'DESC')->get();
        }

        $vendor_r_questions = VendorRatingQuestions::orderBy('sorting', 'ASC')->get();

        $status_r = VendorRatingQAStatus::all();

        return view('vendors.partials.search-data-rqa', compact('VendorQuestionRAnswer', 'vendor_r_questions', 'status_r', 'vendor_id'));
    }

    public function getVendorFlowchartNotes(Request $request)
    {
        $datas = VendorFLowChartNotes::where('vendor_id', $request->vendor_id)
            ->where('flow_chart_id', $request->flow_chart_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'Question get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function searchforVendorFlowcharts(Request $request)
    {
        $vendor_id = $request->vendor_id;

        $VendorFlowchart = [];

        if ((! empty($vendor_id) && ($vendor_id != null))) {
            $VendorFlowchart = Vendor::with('category');

            $VendorFlowchart = $VendorFlowchart->where('id', $vendor_id);

            $VendorFlowchart = $VendorFlowchart->whereNotNull('flowchart_date')->orderBy('flowchart_date', 'DESC')->get();
        }

        $vendor_flow_charts = VendorFlowChart::orderBy('sorting', 'ASC')->get();

        if (! empty($vendor_flow_charts)) {
            foreach ($vendor_flow_charts as $key => $value) {
                $vendorflowcharts = VendorFlowChartSorting::where('vendor_id', $vendor_id)->where('flow_chart_id', $value->id)->first();

                if (empty($vendorflowcharts)) {
                    $vendorflowchartsSorting = VendorFlowChartSorting::where('vendor_id', $vendor_id)->orderBy('sorting_f', 'DESC')->first();

                    $sorting_f = ($key + 1);
                    if (! empty($vendorflowchartsSorting)) {
                        $sorting_f = ($vendorflowchartsSorting->sorting_f + 1);
                    }

                    $vendorfs = new VendorFlowChartSorting();
                    $vendorfs->vendor_id = $vendor_id;
                    $vendorfs->flow_chart_id = $value->id;
                    $vendorfs->sorting_f = $sorting_f;
                    $vendorfs->save();
                }
            }
        }

        $vendor_flow_charts = VendorFlowChartSorting::with('flowchart')->where('vendor_id', $vendor_id)->orderBy('sorting_f', 'ASC')->get();

        $status = VendorFlowChartStatus::all();

        return view('vendors.partials.search-data-fc-vendor', compact('VendorFlowchart', 'vendor_flow_charts', 'status', 'vendor_id'));
    }

    public function flowchartupdatesorting(Request $request)
    {
        $update_sorting = $request->all();
        $data = $request->except('_token');
        foreach ($update_sorting['updatesorting'] as $key => $value) {
            $upsorting = VendorFlowChartSorting::find($key);
            $upsorting->sorting_f = $value;
            $upsorting->save();
        }

        return redirect()->back()->with('success', 'The sorting updated successfully.');
    }

    public function frequencyAdd(Request $request)
    {
        try {
            $frequency = VendorFrequency::create(
                [
                    'user_id' => \Auth::user()->id,
                    'name' => $request->frequency_name,
                ]
            );
            $frequency = VendorFrequency::where('id', $frequency->id)->first();

            return response()->json(['code' => 200, 'data' => $frequency, 'message' => 'Added successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function vendorPriceHistory(Request $request)
    {
        $data = VendorPriceHistory::with(['user' => function ($query) {
        }])->where('vendor_id', $request->id)->get();

        return response()->json(['code' => 200, 'data' => $data, 'message' => 'Message sent successfully']);
    }

    public function searchforVendorEmails(Request $request)
    {
        $vendor_id = $request->vendor_id;

        $vendor = Vendor::find($vendor_id);

        if (! empty($vendor)) {
            // Set default type as incoming
            $type = 'incoming';
            $seen = '0';
            $from = ''; //Purpose : Add var -  DEVTASK-18283

            $sender = $vendor->email;
            $date = $request->date ?? '';

            $query = (new Email())->newQuery();

            $query = $query->leftJoin('chat_messages', 'chat_messages.email_id', 'emails.id')
                ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
                ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
                ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id');

            $query = $query->where(function ($query) use ($type) {
                $query->where('emails.type', $type)->orWhere('emails.type', 'open')->orWhere('emails.type', 'delivered')->orWhere('emails.type', 'processed');
            });

            $query = $query->where(function ($query) use ($sender) {
                $query->where('emails.from', $sender)->orWhere('emails.to', $sender);
            });

            $query = $query->select('emails.*', 'chat_messages.customer_id', 'chat_messages.supplier_id', 'chat_messages.vendor_id', 'c.is_auto_simulator as customer_auto_simulator',
                'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator');

            $query = $query->orderByDesc('emails.id');

            $emails = $query->orderBy('emails.id', 'DESC')->take(5)->get();

            $emailModelTypes = Email::emailModelTypeList();

            return view('vendors.partials.vendor-email', compact('emails', 'emailModelTypes'));
        }
    }

    public function getVendorFlowchartUpdateNotes(Request $request)
    {
        $vendorN = VendorFLowChartNotes::find($request->note_id);
        $data['notes'] = $request->notes;
        $vendorN->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Notes updated successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function deleteFlowchartnotes(Request $request)
    {
        try {
            VendorFLowChartNotes::where('id', $request->id)->delete();

            return response()->json(['code' => '200', 'data' => [], 'message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function getVendorrqaUpdateNotes(Request $request)
    {
        $vendorN = VendorRatingQANotes::find($request->note_id);
        $data['notes'] = $request->notes;
        $vendorN->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Notes updated successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function deleteRqnotes(Request $request)
    {
        try {
            VendorRatingQANotes::where('id', $request->id)->delete();

            return response()->json(['code' => '200', 'data' => [], 'message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function searchforSupplierEmails(Request $request)
    {
        $supplier_id = $request->supplier_id;

        $supplier = Supplier::find($supplier_id);

        if (! empty($supplier)) {
            // Set default type as incoming
            $type = 'incoming';
            $seen = '0';
            $from = ''; //Purpose : Add var -  DEVTASK-18283

            $sender = $supplier->email;
            $date = $request->date ?? '';

            $query = (new Email())->newQuery();

            $query = $query->leftJoin('chat_messages', 'chat_messages.email_id', 'emails.id')
                ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
                ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
                ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id');

            $query = $query->where(function ($query) use ($type) {
                $query->where('emails.type', $type)->orWhere('emails.type', 'open')->orWhere('emails.type', 'delivered')->orWhere('emails.type', 'processed');
            });

            $query = $query->where(function ($query) use ($sender) {
                $query->where('emails.from', $sender)->orWhere('emails.to', $sender);
            });

            $query = $query->select('emails.*', 'chat_messages.customer_id', 'chat_messages.supplier_id', 'chat_messages.vendor_id', 'c.is_auto_simulator as customer_auto_simulator',
                'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator');

            $query = $query->orderByDesc('emails.id');

            $emails = $query->orderBy('emails.id', 'DESC')->take(5)->get();

            $emailModelTypes = Email::emailModelTypeList();

            return view('suppliers.partials.suppliers-email', compact('emails', 'emailModelTypes'));
        }
    }
}
