<?php

namespace App\Http\Controllers;

use App\Category;
use App\Notification;
use App\Leads;
use App\Status;
use App\Setting;
use App\User;
use App\Brand;
use App\Product;
use App\Message;
use App\ChatMessage;
use App\Task;
use App\Image;
use App\AutoReply;
use App\Reply;
use App\Customer;
use App\StatusChange;
use App\CallRecording;
use App\CommunicationHistory;
use App\ReplyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers;
use Validator;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use GuzzleHttp\Client as GuzzleClient;

use App\CallBusyMessage;
use App\Http\Controllers\WhatsAppController;


class LeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('orderby') == '') {
            $orderby = 'asc';
        } else {
            $orderby = 'desc';
        }

        switch ($request->input('sortby')) {
            case 'client_name':
                $sortby = 'client_name';
                break;
            case 'city':
                $sortby = 'city';
                break;
            case 'assigned_user':
                $sortby = 'assigned_user';
                break;
            case 'rating':
                $sortby = 'rating';
                break;
            case 'communication':
                $sortby = 'communication';
                break;
            case 'status':
                $sortby = 'status';
                break;
            case 'created_at':
                $sortby = 'created_at';
                break;
            default :
                $sortby = 'communication';
        }

        $term = $request->input('term');
        $brand = $request->input('brand');
        $rating = $request->input('rating');

        $type = false;
        $leads = ((new Leads())->newQuery()->with('customer'));

        if ($request->type == 'multiple') {
            $type = true;
        }

        if ($request->brand[ 0 ] != null) {
            $implode = implode(',', $request->brand);
            $leads->where('multi_brand', 'LIKE', "%$implode%");

            $brand = $request->brand;
        }

        if ($request->rating[ 0 ] != null) {
            $leads->whereIn('rating', $request->rating);

            $rating = $request->rating;
        }

        if (helpers::getadminorsupervisor()) {
            if ($sortby != 'communication') {
                $leads = $leads->orderBy($sortby, $orderby);
            }
        } else {
            if (helpers::getmessagingrole()) {
                $leads = $leads->oldest();
            } else {
                $leads = $leads->oldest()->where('assigned_user', '=', Auth::id());
            }
        }

        if (!empty($term)) {
            $leads = $leads->whereHas('customer', function ($query) use ($term) {
                return $query->where('name', 'LIKE', "%$term%");
            })->where(function ($query) use ($term) {
                return $query
                    ->orWhere('client_name', 'like', '%' . $term . '%')
                    ->orWhere('id', 'like', '%' . $term . '%')
                    ->orWhere('contactno', $term)
                    ->orWhere('city', 'like', '%' . $term . '%')
                    ->orWhere('instahandler', $term)
                    ->orWhere('assigned_user', Helpers::getUserIdByName($term))
                    ->orWhere('assigned_user', Helpers::getUserIdByName($term))
                    ->orWhere('userid', Helpers::getUserIdByName($term))
                    ->orWhere('status', (new Status())->getIDCaseInsensitive($term));
            });
        }

        $leads_array = $leads->whereNull('deleted_at')->get()->toArray();

        if ($sortby == 'communication') {
            if ($orderby == 'asc') {
                $leads_array = array_values(array_sort($leads_array, function ($value) {
                    return $value[ 'communication' ][ 'created_at' ];
                }));

                $leads_array = array_reverse($leads_array);
            } else {
                $leads_array = array_values(array_sort($leads_array, function ($value) {
                    return $value[ 'communication' ][ 'created_at' ];
                }));
            }

        }
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($leads_array, $perPage * ($currentPage - 1), $perPage);

        $leads_array = new LengthAwarePaginator($currentItems, count($leads_array), $perPage, $currentPage);
        $leads = $leads->whereNull('deleted_at')->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            $html = view('leads.lead-item', ['leads_array' => $leads_array, 'leads' => $leads, 'orderby' => $orderby, 'term' => $term, 'brand' => http_build_query(['brand' => $brand]), 'rating' => http_build_query(['rating' => $rating]), 'type' => $type])->render();

            return response()->json(['html' => $html]);
        }

        return view('leads.index', compact('leads', 'leads_array', 'term', 'orderby', 'brand', 'rating', 'type'))
            ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $status = New status;
        $data[ 'status' ] = $status->all();
        $users = User::oldest()->get()->toArray();
        $data[ 'users' ] = $users;
        $brands = Brand::oldest()->get()->toArray();
        $data[ 'brands' ] = $brands;
        $data[ 'products_array' ] = [];

        $data[ 'category_select' ] = Category::attr(['name' => 'multi_category', 'class' => 'form-control', 'id' => 'multi_category'])
            ->selected()
            ->renderAsDropdown();

        $customer_suggestions = [];
        $customers = (new Customer())->newQuery()
            ->latest()->select('name')->get()->toArray();

        foreach ($customers as $customer) {
            array_push($customer_suggestions, $customer[ 'name' ]);
        }

        $data[ 'customers' ] = Customer::all();

        $data[ 'customer_suggestions' ] = $customer_suggestions;

        return view('leads.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $leads = $this->validate(request(), [
            'customer_id' => 'required',
//          'contactno' => 'required',
//          'city' => 'required',
            'instahandler' => '',
            'rating' => 'required',
            'status' => 'required',
            'solophone' => '',
            'comments' => '',
            'userid' => '',
            'address' => '',
            'multi_brand' => '',
            'email' => '',
            'source' => '',
            'assigned_user' => '',
            'selected_product',
            'size',
            'leadsourcetxt',
            'created_at' => 'required|date_format:"Y-m-d H:i"',
            'whatsapp_number'
        ]);

        $data = $request->except('_token');
        // dd($data);
        //
        // if ($customer = Customer::where('name', $data['client_name'])->first()) {
        //   $data['customer_id'] = $customer->id;
        // } else {
        //   $customer = new Customer;
        //   $customer->name = $data['client_name'];
        //
        //   $validator = Validator::make($data, [
        //     'contactno' => 'unique:customers,phone'
        //   ]);
        //
        //   if ($validator->fails()) {
        //     return back()->with('phone_error', 'The phone already exists')->withInput();
        //   }
        //   $customer->phone = $data['contactno'];
        //
        //   if ($data['source'] == 'instagram') {
        //     $customer->instahandler = $data['leadsourcetxt'];
        //   }
        //
        //   $customer->rating = $data['rating'];
        //   $customer->address = $data['address'];
        //   $customer->city = $data['city'];
        //
        //   $customer->save();
        //
        //   $data['customer_id'] = $customer->id;
        // }
        $customer = Customer::find($request->customer_id);

        $data[ 'client_name' ] = $customer->name;
        $data[ 'contactno' ] = $customer->phone;

        $data[ 'userid' ] = Auth::id();
        $data[ 'selected_product' ] = json_encode($request->input('selected_product'));

        if ($request->type == 'product-lead') {
            $brand_array = [];
            $category_array = [];

            foreach ($request->selected_product as $product_id) {
                $product = Product::find($product_id);

                array_push($brand_array, $product->brand);
                array_push($category_array, $product->category);
            }

            $data[ 'multi_brand' ] = $brand_array ? json_encode($brand_array) : null;
            $data[ 'multi_category' ] = $category_array ? json_encode($category_array) : null;
        } else {
            $data[ 'multi_brand' ] = $request->input('multi_brand') ? json_encode($request->input('multi_brand')) : null;
            $data[ 'multi_category' ] = $request->input('multi_category');
        }

        // $data['multi_category'] = json_encode( $request->input( 'multi_category' ) );


        $lead = Leads::create($data);
        if ($request->hasfile('image')) {
            foreach ($request->file('image') as $image) {
                $media = MediaUploader::fromSource($image)->upload();
                $lead->attachMedia($media, config('constants.media_tags'));
            }
        }


        // if(!empty($request->input('assigned_user'))){
        //
        //   NotificationQueueController::createNewNotification([
        //     'type' => 'button',
        //     'message' => $data['client_name'],
        //     // 'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
        //     'timestamps' => ['+0 minutes'],
        //     'model_type' => Leads::class,
        //     'model_id' =>  $lead->id,
        //     'user_id' => Auth::id(),
        //     'sent_to' => $request->input('assigned_user'),
        //     'role' => '',
        //   ]);
        // }
        // else{
        //
        //   NotificationQueueController::createNewNotification([
        //     'type' => 'button',
        //     'message' => $data['client_name'],
        //     // 'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
        //     'timestamps' => ['+0 minutes'],
        //     'model_type' => Leads::class,
        //     'model_id' =>  $lead->id,
        //     'user_id' => Auth::id(),
        //     'sent_to' => '',
        //     'role' => 'crm',
        //   ]);
        // }

        // NotificationQueueController::createNewNotification([
        //   'message' => $data['client_name'],
        //   'timestamps' => ['+0 minutes'],
        //   'model_type' => Leads::class,
        //   'model_id' =>  $lead->id,
        //   'user_id' => Auth::id(),
        //   'sent_to' => '',
        //   'role' => 'Admin',
        // ]);

        if ($request->ajax()) {
            return response()->json(['lead' => $lead]);
        }

        return redirect()->route('leads.create')
            ->with('success', 'Lead created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leads = Leads::find($id);
        $status = New status;
        $data = $status->all();
        $sales_persons = Helpers::getUsersArrayByRole('Sales');
        $leads[ 'statusid' ] = $data;
        $users = User::all()->toArray();
        $leads[ 'users' ] = $users;
        $brands = Brand::all()->toArray();
        $leads[ 'brands' ] = $brands;
        $leads[ 'selected_products_array' ] = json_decode($leads[ 'selected_product' ]);
        $leads[ 'products_array' ] = [];
        $leads[ 'recordings' ] = CallRecording::where('lead_id', $leads->id)->get()->toArray();
        $leads[ 'customers' ] = Customer::all();
        $tasks = Task::where('model_type', 'leads')->where('model_id', $id)->get()->toArray();
        // $approval_replies = Reply::where('model', 'Approval Lead')->get();
        // $internal_replies = Reply::where('model', 'Internal Lead')->get();
        $reply_categories = ReplyCategory::all();

        $leads[ 'multi_brand' ] = is_array(json_decode($leads[ 'multi_brand' ], true)) ? json_decode($leads[ 'multi_brand' ], true) : [];
        // $selected_categories = is_array(json_decode( $leads['multi_category'],true)) ? json_decode( $leads['multi_category'] ,true) : [] ;
        $data[ 'category_select' ] = Category::attr(['name' => 'multi_category', 'class' => 'form-control', 'id' => 'multi_category'])
            ->selected($leads->multi_category)
            ->renderAsDropdown();
        $leads[ 'remark' ] = $leads->remark;

        $messages = Message::all()->where('moduleid', '=', $leads[ 'id' ])->where('moduletype', '=', 'leads')->sortByDesc("created_at")->take(10)->toArray();
        $leads[ 'messages' ] = $messages;

        if (!empty($leads[ 'selected_products_array' ])) {
            foreach ($leads[ 'selected_products_array' ] as $product_id) {
                $skuOrName = $this->getProductNameSkuById($product_id);

                $data[ 'products_array' ][ $product_id ] = $skuOrName;
            }
        }

        $users_array = Helpers::getUserArray(User::all());

        $selected_categories = $leads[ 'multi_category' ];
        return view('leads.show', compact('leads', 'id', 'data', 'tasks', 'sales_persons', 'selected_categories', 'users_array', 'reply_categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $leads = Leads::find($id);

        if ($request->type != 'customer') {
            $this->validate(request(), [
                'customer_id' => 'required',
                'client_name' => '',
                'contactno' => 'sometimes|nullable|numeric|regex:/^[91]{2}/|digits:12',
                //          'city' => 'required',
                'instahandler' => '',
                'rating' => 'required',
                'status' => 'required',
                'solophone' => '',
                'comments' => '',
                'userid' => '',
                'created_at' => 'required|date_format:"Y-m-d H:i"',

            ]);
        }


        // if (  $request->input( 'assigned_user' ) != $leads->assigned_user && !empty($request->input( 'assigned_user' ))  ) {
        //
        //   NotificationQueueController::createNewNotification([
        //     'type' => 'button',
        //     'message' => $leads->client_name,
        //     'timestamps' => ['+0 minutes'],
        //     // 'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
        //     'model_type' => Leads::class,
        //     'model_id' =>  $id,
        //     'user_id' => Auth::id(),
        //     'sent_to' => $request->input('assigned_user'),
        //     'role' => '',
        //   ]);
        //
        //   // NotificationQueueController::createNewNotification([
        //   //   'message' => $leads->client_name,
        //   //   'timestamps' => ['+45 minutes'],
        //   //   'model_type' => Leads::class,
        //   //   'model_id' =>  $id,
        //   //   'user_id' => Auth::id(),
        //   //   'sent_to' => Auth::id(),
        //   //   'role' => '',
        //   // ]);
        // }

        if ($request->type != 'customer') {
            $leads->customer_id = $request->customer_id;
            $leads->client_name = $request->get('client_name');
            $leads->contactno = $request->get('contactno');
            $leads->city = $request->get('city');
            $leads->source = $request->get('source');
            $leads->rating = $request->get('rating');
            $leads->solophone = $request->get('solophone');
            $leads->userid = $request->get('userid');
            $leads->email = $request->get('email');
            $leads->address = $request->get('address');
            $leads->leadsourcetxt = $request->get('leadsourcetxt');
            $leads->created_at = $request->created_at;
            $leads->whatsapp_number = $request->whatsapp_number;
        }


        if ($request->status != $leads->status) {
            $lead_status = (New status)->all();
            StatusChange::create([
                'model_id' => $id,
                'model_type' => Leads::class,
                'user_id' => Auth::id(),
                'from_status' => array_search($leads->status, $lead_status),
                'to_status' => array_search($request->status, $lead_status)
            ]);
        }

        $leads->status = $request->get('status');
        $leads->comments = $request->get('comments');
        $leads->assigned_user = $request->get('assigned_user');

        $leads->multi_brand = $request->input('multi_brand') ? json_encode($request->get('multi_brand')) : null;
        // $leads->multi_category = json_encode($request->get('multi_category'));
        $leads->multi_category = $request->get('multi_category');

        $leads->selected_product = json_encode($request->input('selected_product'));

        $leads->save();

        $messages = Message::where('moduletype', 'leads')->where('moduleid', $leads->id)->get();

        foreach ($messages as $message) {
            $message->customer_id = $leads->customer_id;
            $message->save();
        }

        $chats = ChatMessage::where('lead_id', $leads->id)->get();

        foreach ($chats as $chat) {
            $chat->customer_id = $leads->customer_id;
            $chat->save();
        }

        $count = 0;
        foreach ($request->oldImage as $old) {
            if ($old > 0) {
                self::removeImage($old);
            } elseif ($old == -1) {
                foreach ($request->file('image') as $image) {
                    $media = MediaUploader::fromSource($image)->upload();
                    $leads->attachMedia($media, config('constants.media_tags'));
                }
            } elseif ($old == 0) {
                $count++;
            }
        }

        if ($count > 0) {
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $image) {
                    $media = MediaUploader::fromSource($image)->upload();
                    $leads->attachMedia($media, config('constants.media_tags'));
                }
            }

        }

        return redirect()->back()->with('success', 'Lead has been updated');
    }

    public function sendPrices(Request $request, GuzzleClient $client)
    {
        $params = [
            'number' => null,
            'user_id' => Auth::id() ?? 6,
            'approved' => 0,
            'status' => 1,
        ];

        $customer = Customer::find($request->customer_id);
        $lead = Customer::find($request->lead_id);
        $product_names = '';

        $params[ 'customer_id' ] = $customer->id;

        foreach ($request->selected_product as $product_id) {

            $product = Product::find($product_id);
            $brand_name = $product->brands->name ?? '';
            $special_price = $product->price_special_offer ?? $product->price_special;

            if ($request->has('dimension')) {

                $product_names .= "$brand_name $product->name" . ' (' . "Length: $product->lmeasurement cm, Height: $product->hmeasurement cm & Depth: $product->dmeasurement cm) \n";
                $params[ 'message' ] = 'The products with their respective dimensions are: : ' . $product_names . '.';
                $chat_message = ChatMessage::create($params);

            } else {
                if ($request->has('detailed')) {

                    $params[ 'message' ] = 'The product images for : : ' . $brand_name . ' ' . $product->name . ' are.';
                    $chat_message = ChatMessage::create($params);
                    $chat_message->attachMedia($product->getMedia(config('constants.media_tags')), config('constants.media_tags'));

                } else {

                    $auto_message = "$brand_name $product->name" . ' - ' . "$special_price";
                    //$auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'lead-product-prices')->first();
                    //$auto_message = preg_replace("/{product_names}/i", $product_names, $auto_reply->reply);
                    $params['message'] = "";//$auto_message;
                    $chat_message = ChatMessage::create($params);

                    $mediaImage = $product->getMedia(config('constants.media_tags'))->first();

                    $chat_message->attachMedia($mediaImage,  config('constants.media_tags'));

                    // create text image to null first so no issue ahead
                    $textImage = null;
                    if($mediaImage) {
                      // define seperator
                      if(!defined("DSP")) {
                        define("DSP",DIRECTORY_SEPARATOR);
                      }
                      // add text message and create image
                      $textImage = self::createProductTextImage(
                        public_path($mediaImage->disk.DSP.$mediaImage->filename.".".$mediaImage->extension),
                        "instant_message_".$chat_message->id,
                        $auto_message
                      );

                      $chat_message->media_url = $textImage;
                      $chat_message->save();

                    }
                    // send message now
                    // uncomment this one to send message immidiatly
                    $autoApprove = $request->get("auto_approve", false);

                    if($autoApprove) {
                        // send request if auto approve    
                        $approveRequest = new Request();
                        $approveRequest->setMethod('GET');
                        $approveRequest->request->add(['messageId' => $chat_message->id]);
                        
                        app(WhatsAppController::class)->approveMessage("customer",$approveRequest);
                    
                    }
                    
                    app(WhatsAppController::class)->sendRealTime($chat_message, 'customer_' . $customer->id, $client, $textImage);
                }
            }

        }

        if ($request->has('dimension') || $request->has('detailed')) {
            app(WhatsAppController::class)->sendRealTime($chat_message, 'customer_' . $customer->id, $client);
        }


        $histories = CommunicationHistory::where('model_id', $customer->id)->where('model_type', Customer::class)->where('type', 'initiate-followup')->where('is_stopped', 0)->get();

        foreach ($histories as $history) {
            $history->is_stopped = 1;
            $history->save();
        }

        CommunicationHistory::create([
            'model_id' => $customer->id,
            'model_type' => Customer::class,
            'type' => 'initiate-followup',
            'method' => 'whatsapp'
        ]);

        return response('success');
    }

    public function removeImage($old_image)
    {


        if ($old_image != 0) {

            $results = Media::where('id', $old_image)->get();

            $results->each(function ($media) {
                Image::trashImage($media->basename);
                $media->delete();
            });
        }

    }

    public function updateStatus(Request $request, $id)
    {
        $lead = Leads::find($id);
        $lead_status = (New status)->all();
        StatusChange::create([
            'model_id' => $id,
            'model_type' => Leads::class,
            'user_id' => Auth::id(),
            'from_status' => array_search($lead->status, $lead_status),
            'to_status' => array_search($request->status, $lead_status)
        ]);

        $lead->status = $request->status;
        $lead->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leads = Leads::findOrFail($id);
        $leads->delete();
        return redirect('leads')->with('success', 'Lead has been archived');
    }

    public function permanentDelete(Leads $leads)
    {

        $leads->forceDelete();
        return redirect('leads')->with('success', 'Lead has been  deleted');
    }

    public function getProductNameSkuById($product_id)
    {

        $product = new Product();

        $product_instance = $product->find($product_id);

        return $product_instance->name ? $product_instance->name : $product_instance->sku;
    }

    public function imageGrid()
    {
        $leads_array = Leads::whereNull('deleted_at')->where('status', '!=', 1)->get()->toArray();
        $leads = Leads::whereNull('deleted_at')->where('status', '!=', 1)->get();
        $new_leads = [];

        foreach ($leads_array as $key => $lead) {
            if ($leads[ $key ]->getMedia(config('constants.media_tags'))->first() !== null) {
                $new_leads[ $key ][ 'id' ] = $lead[ 'id' ];
                $new_leads[ $key ][ 'image' ] = $leads[ $key ]->getMedia(config('constants.media_tags'));
                $new_leads[ $key ][ 'status' ] = $lead[ 'status' ];
                $new_leads[ $key ][ 'rating' ] = $lead[ 'rating' ];
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');

        if (count($new_leads) > $perPage) {
            $currentItems = array_slice($new_leads, $perPage * ($currentPage - 1), $perPage);
        } else {
            $currentItems = $new_leads;
        }

        $new_leads = new LengthAwarePaginator($currentItems, count($new_leads), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        return view('leads.image-grid')->withLeads($new_leads);
    }


    public function saveLeaveMessage(Request $request)
    {
        $callBusyMessage = new CallBusyMessage();
        $callBusyMessage->lead_id = $request->input('lead_id');
        $callBusyMessage->message = $request->input('message');
        $callBusyMessage->save();
    }

    /**
     * Create images with text from product
     *
     */

    public static function createProductTextImage($path, $name = "", $text = "", $color = "228B22", $fontSize = "40")
    {
       $text = wordwrap(strtoupper($text), 24, "\n");
       $img = \IImage::make($path);
       $img->resize(600, null, function ($constraint) {
          $constraint->aspectRatio();
       });
       // use callback to define details
        $img->text($text, 5, 50, function($font) use ($fontSize,$color) {
            $font->file(public_path('/fonts/HelveticaNeue.ttf'));
            $font->size($fontSize);
            $font->color("#" . $color);
            $font->align('top');
        });

        $name = !empty($name) ? $name . "_watermarked" : time() . "_watermarked";

        $path = 'uploads/' . $name . '.jpg';

        $img->save(public_path($path));

        return url('/') . "/" . $path;
    }
}
