<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Email;
use App\Mail\PurchaseEmail;
use App\Supplier;
use App\Vendor;
use App\VendorProduct;
use App\VendorCategory;
use App\Setting;
use App\ReplyCategory;
use App\Helpers;
use App\User;
use Carbon\Carbon;
use Mail;
use Illuminate\Http\Request;
use Plank\Mediable\Media;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Illuminate\Pagination\LengthAwarePaginator;
use Webklex\IMAP\Client;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
     // $this->middleware('permission:vendor-all');
    }

    public function updateReminder(Request $request) {
        $vendor = Vendor::find($request->get('vendor_id'));
        $vendor->frequency = $request->get('frequency');
        $vendor->reminder_message = $request->get('message');
        $vendor->save();

        return response()->json([
            'success'
        ]);
    }

    public function index(Request $request)
    {

     $term = $request->term ?? '';
      $sortByClause = '';
      $orderby = 'DESC';

      if ($request->orderby == '') {
         $orderby = 'ASC';
      }

      if ($request->sortby == 'category') {
        $sortByClause = "category_name $orderby,";
      }
        $whereArchived = ' `deleted_at` IS NULL ';

      if ($request->get('with_archived') == 'on') {
          $whereArchived = '  `deleted_at` IS NOT NULL  ';
      }

      //getting request 
      if($request->term || $request->name || $request->id || $request->category || $request->phone || $request->address || $request->email || $request->term){


        //Query Initiate
        $query  = Vendor::query();

          if(request('term') != null){
              $query->where('name', 'LIKE', "%{$request->term}%")
                    ->orWhere('address', 'LIKE', "%{$request->term}%")
                    ->orWhere('phone', 'LIKE', "%{$request->term}%")
                    ->orWhere('email', 'LIKE', "%{$request->term}%")
                    ->orWhereHas('category', function ($qu) use ($request) {
                      $qu->where('title', 'LIKE', "%{$request->term}%");
                      });

            }

            //if Id is not null 
          if (request('id') != null) {
                $query->where('id', request('id',0));
            }

            //If name is not null 
          if (request('name') != null) {
                $query->where('name','LIKE', '%' . request('name') . '%');
            } 

           
            //if addess is not null
          if (request('address') != null) {
                $query->where('address', 'LIKE', '%' . request('address') . '%');
            } 
           
           //if email is not null 
          if (request('email') != null) {
                $query->where('email', 'LIKE', '%' . request('email') . '%');
            } 
         
            
            //if phone is not null
         if (request('phone') != null) {
                $query->where('phone', 'LIKE', '%' . request('phone') . '%');
            }
            
            //if category is not nyll
         if (request('category') != null) {
                $query->whereHas('category', function ($qu) use ($request) {
                    $qu->where('title', 'LIKE', '%' . request('category') . '%');
                    });
            }
                        
          if($request->with_archived != null && $request->with_archived != ''){
                $pagination = Setting::get('pagination'); 
                if (request()->get('select_all') == 'true') {
                    $pagination = $vendors->count();
                }
                 $vendors = $query->orderby('name','asc')->whereNotNull('deleted_at')->paginate($pagination);  
           }else{
                $pagination = Setting::get('pagination'); 
                if (request()->get('select_all') == 'true') {
                    $pagination = $vendors->count();
                }
                $vendors = $query->orderby('name','asc')->paginate($pagination);
          }

      }else{

       
        $vendors = DB::select('
                  SELECT *,
                  (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
                  (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) as message_status,
                  (SELECT mm3.created_at FROM chat_messages mm3 WHERE mm3.id = message_id) as message_created_at

                  FROM (SELECT vendors.id, vendors.frequency, vendors.is_blocked ,vendors.reminder_message, vendors.category_id, vendors.name, vendors.phone, vendors.email, vendors.address, vendors.social_handle, vendors.website, vendors.login, vendors.password, vendors.gst, vendors.account_name, vendors.account_iban, vendors.account_swift,
                  category_name,
                  chat_messages.message_id FROM vendors

                  LEFT JOIN (SELECT MAX(id) as message_id, vendor_id FROM chat_messages GROUP BY vendor_id ORDER BY created_at DESC) AS chat_messages
                  ON vendors.id = chat_messages.vendor_id

                  LEFT JOIN (SELECT id, title AS category_name FROM vendor_categories) AS vendor_categories
                  ON vendors.category_id = vendor_categories.id WHERE '. $whereArchived . '
                  )

                  AS vendors

                  WHERE (name LIKE "%' . $term . '%" OR
                  phone LIKE "%' . $term . '%" OR
                  email LIKE "%' . $term . '%" OR
                  address LIKE "%' . $term . '%" OR
                  social_handle LIKE "%' . $term . '%" OR
                  category_id IN (SELECT id FROM vendor_categories WHERE title LIKE "%' . $term . '%") OR
                   id IN (SELECT model_id FROM agents WHERE model_type LIKE "%Vendor%" AND (name LIKE "%' . $term . '%" OR phone LIKE "%' . $term . '%" OR email LIKE "%' . $term . '%")))
                  ORDER BY ' . $sortByClause . ' message_created_at DESC;
              ');

              // dd($vendors);

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      $perPage = Setting::get('pagination'); 
      if (request()->get('select_all') == 'true') {
          $perPage = count($vendors);
          $currentPage = 1;
      }
      $currentItems = array_slice($vendors, $perPage * ($currentPage - 1), $perPage);

      $vendors = new LengthAwarePaginator($currentItems, count($vendors), $perPage, $currentPage, [
        'path'  => LengthAwarePaginator::resolveCurrentPath()
      ]);
      
      }
     

      $vendor_categories = VendorCategory::all();


      $users = User::all();

      

       if ($request->ajax()) {
            return response()->json([
                'tbody' => view('vendors.partials.data', compact('vendors'))->render(),
                'links' => (string)$vendors->render()
            ], 200);
        }

      return view('vendors.index', [
        'vendors' => $vendors,
        'vendor_categories' => $vendor_categories,
        'term'    => $term,
        'orderby'    => $orderby,
          'users' => $users
      ]);
    }

    public function email(Request $request) {
        $vendorArr  = Vendor::join('emails', 'emails.model_id', 'vendors.id')
                          ->where('emails.model_type', Vendor::class)
                          ->where('vendors.id', $request->get('id',0))
                          ->get();
        $data = [];
        foreach ($vendorArr as $vendor) {
          $additional_data =  json_decode($vendor->additional_data);
           $data[] = [
              'from'            => $vendor->from,
              'to'              => $vendor->to,
              'subject'         => $vendor->subject,
              'message'         => strip_tags($vendor->message),
              'cc'              => $vendor->cc,
              'bcc'             => $vendor->bcc,
              'created_at'      => $vendor->created_at,
              'attachment'      => !empty($additional_data->attachment) ? $additional_data->attachment : '',
              'inout'           => $vendor->email != $vendor->from ? 'out' : 'in',
           ];
        }

        return response()->json($data);
    }

    public function assignUserToCategory(Request $request) {
        $user = $request->get('user_id');
        $category = $request->get('category_id');

        $category = VendorCategory::find($category);
        $category->user_id = $user;
        $category->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function product()
    {
      $products = VendorProduct::with('vendor')->latest()->paginate(Setting::get('pagination'));
      $vendors = Vendor::select(['id', 'name'])->get();

      return view('vendors.product', [
        'products'  => $products,
        'vendors'  => $vendors
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'category_id'   => 'sometimes|nullable|numeric',
        'name'          => 'required|string|max:255',
        'address'       => 'sometimes|nullable|string',
        'phone'         => 'sometimes|nullable|numeric',
        'email'         => 'sometimes|nullable|email',
        'social_handle' => 'sometimes|nullable',
        'website'       => 'sometimes|nullable',
        'login'         => 'sometimes|nullable',
        'password'      => 'sometimes|nullable',
        'gst'           => 'sometimes|nullable|max:255',
        'account_name'  => 'sometimes|nullable|max:255',
        'account_iban'  => 'sometimes|nullable|max:255',
        'account_swift' => 'sometimes|nullable|max:255'
      ]);

      $data = $request->except('_token');

      Vendor::create($data);

      return redirect()->route('vendor.index')->withSuccess('You have successfully saved a vendor!');
    }

    public function productStore(Request $request)
    {
      $this->validate($request, [
        'vendor_id'       => 'required|numeric',
        'images.*'        => 'sometimes|nullable|image',
        'date_of_order'   => 'required|date',
        'name'            => 'required|string|max:255',
        'qty'             => 'sometimes|nullable|numeric',
        'price'           => 'sometimes|nullable|numeric',
        'payment_terms'   => 'sometimes|nullable|string',
        'recurring_type'  => 'required|string',
        'delivery_date'   => 'sometimes|nullable|date',
        'received_by'     => 'sometimes|nullable|string',
        'approved_by'     => 'sometimes|nullable|string',
        'payment_details' => 'sometimes|nullable|string'
      ]);

      $data = $request->except('_token');

      $product = VendorProduct::create($data);

      if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)
                                  ->toDirectory('vendorproduct/'.floor($product->id / config('constants.image_per_folder')))
                                  ->upload();
          $product->attachMedia($media,config('constants.media_tags'));
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
        'vendor'  => $vendor,
        'vendor_categories'  => $vendor_categories,
        'vendor_show'  => $vendor_show,
        'reply_categories'  => $reply_categories,
        'users_array'  => $users_array,
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $this->validate($request, [
        'category_id'     => 'sometimes|nullable|numeric',
        'name'            => 'required|string|max:255',
        'address'         => 'sometimes|nullable|string',
        'phone'           => 'sometimes|nullable|numeric',
        'default_phone'   => 'sometimes|nullable|numeric',
        'whatsapp_number' => 'sometimes|nullable|numeric',
        'email'           => 'sometimes|nullable|email',
        'social_handle'   => 'sometimes|nullable',
        'website'         => 'sometimes|nullable',
        'login'           => 'sometimes|nullable',
        'password'        => 'sometimes|nullable',
        'gst'             => 'sometimes|nullable|max:255',
        'account_name'    => 'sometimes|nullable|max:255',
        'account_iban'    => 'sometimes|nullable|max:255',
        'account_swift'   => 'sometimes|nullable|max:255'
      ]);

      $data = $request->except('_token');

      Vendor::find($id)->update($data);

      return redirect()->route('vendor.index')->withSuccess('You have successfully updated a vendor!');
    }

    public function productUpdate(Request $request, $id)
    {
      $this->validate($request, [
        'vendor_id'       => 'sometimes|nullable|numeric',
        'images.*'        => 'sometimes|nullable|image',
        'date_of_order'   => 'required|date',
        'name'            => 'required|string|max:255',
        'qty'             => 'sometimes|nullable|numeric',
        'price'           => 'sometimes|nullable|numeric',
        'payment_terms'   => 'sometimes|nullable|string',
        'recurring_type'  => 'required|string',
        'delivery_date'   => 'sometimes|nullable|date',
        'received_by'     => 'sometimes|nullable|string',
        'approved_by'     => 'sometimes|nullable|string',
        'payment_details' => 'sometimes|nullable|string'
      ]);

      $data = $request->except('_token');

      $product = VendorProduct::find($id);
      $product->update($data);

      if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)
                                ->toDirectory('vendorproduct/'.floor($product->id / config('constants.image_per_folder')))
                                ->upload();
          $product->attachMedia($media,config('constants.media_tags'));
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

//      foreach ($vendor->products as $product) {
//        $product->detachMediaTags(config('constants.media_tags'));
//      }

//      $vendor->products()->delete();
//      $vendor->chat_messages()->delete();
//      $vendor->agents()->delete();
      $vendor->delete();

      return redirect()->route('vendor.index')->withSuccess('You have successfully deleted a vendor');
    }

    public function productDestroy($id)
    {
      $product = VendorProduct::find($id);

      $product->detachMediaTags(config('constants.media_tags'));
      $product->delete();

      return redirect()->back()->withSuccess('You have successfully deleted a vendor product!');
    }

    public function sendEmailBulk(Request $request){
        $this->validate($request, [
            'subject' => 'required|min:3|max:255',
            'message' => 'required',
            'cc.*' => 'nullable|email',
            'bcc.*' => 'nullable|email'
        ]);

        if ($request->from_mail) {
          $mail = \App\EmailAddress::where('id', $request->from_mail)->first();
          if ($mail)  {
              $config = array(
                  'driver'    => $mail->driver,
                  'host'      => $mail->host,
                  'port'      => $mail->port,
                  'from'      => [
                      'address' => $mail->from_address,
                      'name' => $mail->from_name,
                  ],
                  'encryption'  => $mail->encryption,
                  'username'    => $mail->username,
                  'password'    => $mail->password,
                  'sendmail'    => '/usr/sbin/sendmail -bs',
                  'markdown'    => [
                      'theme' => 'default',
                      'paths' => [
                          resource_path('views/vendor/mail'),
                      ],
                  ],
              );
              \Config::set('mail', $config);
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
                return redirect()->route('vendor.index')->withErrors(['Please select vendors']);
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

                $file->storeAs("documents", $filename, 'files');

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

            $mail->send(new PurchaseEmail($request->subject, $request->message, $file_paths));

            $params = [
                'model_id'        => $vendor->id,
                'model_type'      => Vendor::class,
                'from'            => 'buying@amourint.com',
                'seen'            => 1,
                'to'              => $vendor->email,
                'subject'         => $request->subject,
                'message'         => $request->message,
                'template'		=> 'customer-simple',
                'additional_data'	=> json_encode(['attachment' => $file_paths]),
                'cc'              => $cc ?: null,
                'bcc'             => $bcc ?: null,
            ];

            Email::create($params);
        }

        return redirect()->route('vendor.index')->withSuccess('You have successfully sent emails in bulk!');
    }

    public function sendEmail(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required|min:3|max:255',
            'message' => 'required',
            'email.*' => 'required|email',
            'cc.*' => 'nullable|email',
            'bcc.*' => 'nullable|email'
        ]);

        $vendor = Vendor::find($request->vendor_id);

        if ($request->from_mail) {
          $mail = \App\EmailAddress::where('id', $request->from_mail)->first();
          if ($mail)  {
              $config = array(
                  'driver'    => $mail->driver,
                  'host'      => $mail->host,
                  'port'      => $mail->port,
                  'from'      => [
                      'address' => $mail->from_address,
                      'name' => $mail->from_name,
                  ],
                  'encryption'  => $mail->encryption,
                  'username'    => $mail->username,
                  'password'    => $mail->password,
                  'sendmail'    => '/usr/sbin/sendmail -bs',
                  'markdown'    => [
                      'theme' => 'default',
                      'paths' => [
                          resource_path('views/vendor/mail'),
                      ],
                  ],
              );
              \Config::set('mail', $config);
          }
        }
        
        if ($vendor->email != '') {
            $file_paths = [];

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $filename = $file->getClientOriginalName();

                    $file->storeAs("documents", $filename, 'files');

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

            if (is_array($emails) && !empty($emails)) {
                $to = array_shift($emails);
                $cc = array_merge($emails, $cc);

                $mail = Mail::to($to);

                if ($cc) {
                    $mail->cc($cc);
                }
                if ($bcc) {
                    $mail->bcc($bcc);
                }

                $mail->send(new PurchaseEmail($request->subject, $request->message, $file_paths));
            } else {
                return redirect()->back()->withErrors('Please select an email');
            }

            $params = [
                'model_id' => $vendor->id,
                'model_type' => Vendor::class,
                'from' => 'buying@amourint.com',
                'to' => $request->email[0],
                'seen' => 1,
                'subject' => $request->subject,
                'message' => $request->message,
                'template' => 'customer-simple',
                'additional_data' => json_encode(['attachment' => $file_paths]),
                'cc' => $cc ?: null,
                'bcc' => $bcc ?: null
            ];

            Email::create($params);

            return redirect()->route('vendor.show', $vendor->id)->withSuccess('You have successfully sent an email!');

        }
    }

    public function emailInbox(Request $request){
        $imap = new Client([
            'host'          => env('IMAP_HOST_PURCHASE'),
            'port'          => env('IMAP_PORT_PURCHASE'),
            'encryption'    => env('IMAP_ENCRYPTION_PURCHASE'),
            'validate_cert' => env('IMAP_VALIDATE_CERT_PURCHASE'),
            'username'      => env('IMAP_USERNAME_PURCHASE'),
            'password'      => env('IMAP_PASSWORD_PURCHASE'),
            'protocol'      => env('IMAP_PROTOCOL_PURCHASE')
        ]);

        $imap->connect();

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

        $latest_email = Email::where('type', $type)->where('model_id', $vendor->id)->where('model_type', 'App\Vendor')->latest()->first();

        $latest_email_date = $latest_email
            ? Carbon::parse($latest_email->created_at)
            : Carbon::parse('1990-01-01');

        $vendorAgentsCount = $vendor->agents()->count();

        if ($vendorAgentsCount == 0) {
            $emails = $inbox->messages()->where($direction, $vendor->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
            $emails = $emails->leaveUnread()->get();
            $this->createEmailsForEmailInbox($vendor, $type, $latest_email_date, $emails);
        }
        else if($vendorAgentsCount == 1) {
            $emails = $inbox->messages()->where($direction, $vendor->agents[0]->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
            $emails = $emails->leaveUnread()->get();
            $this->createEmailsForEmailInbox($vendor, $type, $latest_email_date, $emails);
        }
        else {
            foreach ($vendor->agents as $key => $agent) {
                if ($key == 0) {
                    $emails = $inbox->messages()->where($direction, $agent->email)->where([
                        ['SINCE', $latest_email_date->format('d M y H:i')]
                    ]);
                    $emails = $emails->leaveUnread()->get();
                    $this->createEmailsForEmailInbox($vendor, $type, $latest_email_date, $emails);
                } else {
                    $additional = $inbox->messages()->where($direction, $agent->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
                    $additional = $additional->leaveUnread()->get();
                    $this->createEmailsForEmailInbox($vendor, $type, $latest_email_date, $additional);
                    // $emails = $emails->merge($additional);
                }
            }
        }

        $db_emails = $vendor->emails()->with('model')->where('type', $type)->get();

        $emails_array = []; $count = 0;
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

        $emails_array = array_values(array_sort($emails_array, function ($value) {
            return $value['date'];
        }));

        $emails_array = array_reverse($emails_array);

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($emails_array, $perPage * ($currentPage - 1), $perPage);
        $emails = new LengthAwarePaginator($currentItems, count($emails_array), $perPage, $currentPage);

        $view = view('vendors.partials.email', ['emails' => $emails, 'type' => $request->type])->render();

        return response()->json(['emails' => $view]);
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
                    $path = "email-attachments/" . $attachment->name;
                    $attachments_array[] = $path;
                });

                $params = [
                    'model_id'        => $vendor->id,
                    'model_type'      => Vendor::class,
                    'type'            => $type,
                    'seen'            => $email->getFlags()['seen'],
                    'from'            => $email->getFrom()[0]->mail,
                    'to'              => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                    'subject'         => $email->getSubject(),
                    'message'         => $content,
                    'template'		  => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $attachments_array]),
                    'created_at'      => $email->getDate()
                ];

                Email::create($params);
            }
        }
    }
    public function block(Request $request){
        $vendor = Vendor::find($request->vendor_id);

        if ($vendor->is_blocked == 0) {
            $vendor->is_blocked = 1;
        } else {
            $vendor->is_blocked = 0;
        }

        $vendor->save();

        return response()->json(['is_blocked' => $vendor->is_blocked]);
    }
}
