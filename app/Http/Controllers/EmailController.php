<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Mail;
use App\Email;
use App\Reply;
use App\EmailLog;
use Carbon\Carbon;
use App\LogRequest;
use App\ModelColor;
use App\Wetransfer;
use App\EmailRemark;
use App\EmailAddress;
use App\CronJobReport;
use App\EmailCategory;
use App\ReplyCategory;
use App\EmailRunHistories;
use App\SendgridEventColor;
use Illuminate\Http\Request;
use App\DigitalMarketingPlatform;
use App\Mails\Manual\ForwardEmail;
use App\Mails\Manual\ReplyToEmail;
use Webklex\PHPIMAP\ClientManager;
use App\Mails\Manual\PurchaseEmail;
use App\Models\EmailCategoryHistory;
use App\Models\EmailStatusChangeHistory;
use EmailReplyParser\Parser\EmailParser;
use Illuminate\Support\Facades\Validator;
use seo2websites\ErpExcelImporter\ErpExcelImporter;
use App\Models\EmailBox;
use App\Models\DataTableColumn;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //Purpose : Add Email Parameter - DEVTASK-18283
    public function index(Request $request, $email = null)
    {
        $user = Auth::user();
        $admin = $user->isAdmin();
        $usernames = [];
        if (!$admin) {
            $emaildetails = \App\EmailAssign::select('id', 'email_address_id')
                ->with('emailAddress:username')
                ->where(['user_id' => $user->id])
                ->getModels();
            if ($emaildetails) {
                $usernames = array_map(fn ($item) => $item->emailAddress->username, $emaildetails);
            }
        }

        // Set default type as incoming
        $type = 'incoming';
        $seen = '0';
        $from = ''; //Purpose : Add var -  DEVTASK-18283

        $term = $request->term ?? '';
        $sender = $request->sender ?? '';
        $receiver = $request->receiver ?? '';
        $status = $request->status ?? '';
        $category = $request->category ?? '';
        $mailbox = $request->mail_box ?? '';
        $email_model_type = $request->email_model_type ?? '';

        $date = $request->date ?? '';
        $type = $request->type ?? $type;
        $seen = $request->seen ?? $seen;
        $query = (new Email())->newQuery();
        $trash_query = false;
        $query = $query->leftJoin('chat_messages', 'chat_messages.email_id', 'emails.id')
            ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
            ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
            ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id');
        if (count($usernames) > 0) {
            $query = $query->where(function ($query) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $query->orWhere('from', 'like', '%' . $_uname . '%');
                }
            });

            $query = $query->orWhere(function ($query) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $query->orWhere('to', 'like', '%' . $_uname . '%');
                }
            });
        }

        //START - Purpose : Add Email - DEVTASK-18283
        if ($email != '' && $receiver == '') {
            $receiver = $email;
            $from = 'order_data';
            $seen = 'both';
            $type = 'outgoing';
        }
        //END - DEVTASK-18283

        // If type is bin, check for status only
        if ($type == 'bin') {
            $trash_query = true;
            $query = $query->where('emails.status', 'bin');
        } elseif ($type == 'draft') {
            $query = $query->where('is_draft', 1)->where('emails.status', '<>', 'pre-send');
        } elseif ($type == 'pre-send') {
            $query = $query->where('emails.status', 'pre-send');
        } else {
            $query = $query->where(function ($query) use ($type) {
                $query->where('emails.type', $type)->orWhere('emails.type', 'open')->orWhere('emails.type', 'delivered')->orWhere('emails.type', 'processed');
            });
        }
        if ($email_model_type) {
            $model_type = explode(',', $email_model_type);
            $query = $query->where(function ($query) use ($model_type) {
                $query->whereIn('model_type', $model_type);
            });
        }
        if ($date) {
            $query = $query->whereDate('created_at', $date);
        }
        if ($term) {
            $query = $query->where(function ($query) use ($term) {
                $query->orWhere('from', 'like', '%' . $term . '%')
                    ->orWhere('to', 'like', '%' . $term . '%')
                    ->orWhere('emails.subject', 'like', '%' . $term . '%')
                    ->orWhere(DB::raw('FROM_BASE64(emails.message)'), 'like', '%' . $term . '%')
                    ->orWhere('chat_messages.message', 'like', '%' . $term . '%');
            });
        }

        if (! $term) {
            if ($sender) {
                $sender = explode(',', $request->sender);
                $query = $query->where(function ($query) use ($sender) {
                    $query->whereIn('emails.from', $sender);
                });
            }
            if ($receiver) {
                $receiver = explode(',', $request->receiver);
                $query = $query->where(function ($query) use ($receiver) {
                    $query->whereIn('emails.to', $receiver);
                });
            }
            if ($status) {
                $status = explode(',', $request->status);
                $query = $query->where(function ($query) use ($status) {
                    $query->whereIn('emails.status', $status);
                });
            }
            if ($category) {
                $category = explode(',', $request->category);
                $query = $query->where(function ($query) use ($category) {
                    $query->whereIn('email_category_id', $category);
                });
            }
        }

        if (! empty($mailbox)) {
            $mailbox = explode(',', $request->mail_box);
            $query = $query->where(function ($query) use ($mailbox) {
                $query->orWhere('to', $mailbox);
            });
        }
        
        if (isset($seen) && $seen != "0") {
            if ($seen != 'both') {
                $query = $query->where('seen', $seen);
            } else if ($seen == 'both' && $type == 'outgoing') {
                $query = $query->where('emails.status', 'outgoing');
            }
        }

        // If it isn't trash query remove email with status trashed
        if (! $trash_query) {
            $query = $query->where(function ($query) use ($type) {
                $isDraft = ($type == 'draft') ? 1 : 0;

                return $query->where('emails.status', '<>', 'bin')->orWhereNull('emails.status')->where('is_draft', $isDraft);
            });
        }
        $query = $query->select('emails.*', 'chat_messages.customer_id', 'chat_messages.supplier_id', 'chat_messages.vendor_id', 'c.is_auto_simulator as customer_auto_simulator',
            'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator');

        if ($admin == 1) {
            $query = $query->orderByDesc('emails.id');

            $emails = $query->paginate(30)->appends(request()->except(['page']));
        } else {
            if (count($usernames) > 0) {
                $query = $query->where(function ($query) use ($usernames) {
                    foreach ($usernames as $_uname) {
                        $query->orWhere('from', 'like', '%' . $_uname . '%');
                    }
                });

                $query = $query->where(function ($query) use ($usernames) {
                    foreach ($usernames as $_uname) {
                        $query->orWhere('to', 'like', '%' . $_uname . '%');
                    }
                });
                

                $query = $query->orderByDesc('emails.id');
                $emails = $query->paginate(30)->appends(request()->except(['page']));
            } else {
                $emails = (new Email())->newQuery();
                $emails = $emails->whereNull('id');
                $emails = $emails->orderByDesc('emails.id');
                $emails = $emails->paginate(30)->appends(request()->except(['page']));
            }
        }
        

        //Get Cron Email Histroy
        $reports = CronJobReport::where('cron_job_reports.signature', 'fetch:all_emails')
            ->join('cron_jobs', 'cron_job_reports.signature', 'cron_jobs.signature')
            ->whereDate('cron_job_reports.created_at', '>=', Carbon::now()->subDays(10))
            ->select(['cron_job_reports.*', 'cron_jobs.last_error'])->paginate(15);

        //Get All Status
        $email_status = DB::table('email_status');

        /*if (! empty($request->type) && $request->type == 'outgoing') {
            $email_status = $email_status->where('type', 'sent');
        } else {
            $email_status = $email_status->where('type', '!=', 'sent');
        }*/

        $email_status = $email_status->get(['id', 'email_status']);

        //Get List of model types
        $emailModelTypes = Email::emailModelTypeList();

        //Get All Category
        $email_categories = DB::table('email_category');

        /*if (! empty($request->type) && $request->type == 'outgoing') {
            $email_categories = $email_categories->where('type', 'sent');
        } else {
            $email_categories = $email_categories->where('type', '!=', 'sent');
        }*/

        $email_categories = $email_categories->get(['id', 'category_name']);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('emails.search', compact('emails', 'date', 'term', 'type', 'email_categories', 'email_status', 'emailModelTypes'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $emails->links(),
                'count' => $emails->total(),
                'emails' => $emails,
            ], 200);
        }

        // suggested search for email forwarding
        $search_suggestions = $this->getAllEmails();

        // dd(array_values($search_suggestions));

        // if($request->AJAX()) {
        //     return view('emails.search',compact('emails'));
        // }

        // dont load any data, data will be loaded by tabs based on ajax
        // return view('emails.index',compact('emails','date','term','type'))->with('i', ($request->input('page', 1) - 1) * 5);
        $digita_platfirms = DigitalMarketingPlatform::all();

        $totalEmail = Email::count();
        $modelColors = ModelColor::whereIn('model_name', ['customer', 'vendor', 'supplier', 'user'])->limit(10)->get();

        $datatableModel = DataTableColumn::select('column_name')
                                            ->where('user_id', auth()->user()->id)
                                            ->where('section_name', 'emails')->first();
        $dynamicColumnsToShowb = [];
        if(!empty($datatableModel->column_name)){
            $hideColumns = $datatableModel->column_name ?? "";
            $dynamicColumnsToShowb = json_decode($hideColumns, true);
        }
        
        return view('emails.index',
            [
                'emails' => $emails,
                'type' => 'email',
                'search_suggestions' => $search_suggestions,
                'email_status' => $email_status,
                'email_categories' => $email_categories,
                'emailModelTypes' => $emailModelTypes,
                'reports' => $reports,
                'digita_platfirms' => $digita_platfirms,
                'receiver' => $receiver,
                'from' => $from,
                'totalEmail' => $totalEmail,
                'modelColors' => $modelColors,
                'dynamicColumnsToShowb' => $dynamicColumnsToShowb
            ])->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function emailsColumnVisbilityUpdate(Request $request){
        $userCheck = DataTableColumn::where('user_id',auth()->user()->id)->where('section_name','emails')->first();

        if($userCheck)
        {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'emails';
            $column->column_name = json_encode($request->column_data); 
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'emails';
            $column->column_name = json_encode($request->column_data); 
            $column->user_id =  auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity saved successfully!');
    }

    public function platformUpdate(Request $request)
    {
        if ($request->id) {
            if (Email::where('id', $request->id)->update(['digital_platfirm' => $request->platform])) {
                return redirect()->back()->with('success', 'Updated successfully.');
            }

            return redirect()->back()->with('error', 'Records not found!');
        }

        return redirect()->back()->with('error', 'Error Occured! Please try again later.');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $email = Email::find($id);
        $status = 'bin';
        $message = 'Email has been trashed';

        // If status is already trashed, move to inbox
        if ($email->status == 'bin') {
            $status = '';
            $message = 'Email has been sent to inbox';
        }

        $email->status = $status;
        $email->update();

        return response()->json(['message' => $message]);
    }

    public function resendMail($id, Request $request)
    {
        $email = Email::find($id);
        $attachment = [];
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

        $array = is_array(json_decode($email->additional_data, true)) ? json_decode($email->additional_data, true) : [];

        if (array_key_exists('attachment', $array)) {
            $temp = json_decode($email->additional_data, true)['attachment'];
        }
        if (isset($temp)) {
            if (! is_array($temp)) {
                $attachment[] = $temp;
            } else {
                $attachment = $temp;
            }
        }
        $customConfig = [
            'from' => $email->from,
        ];

        $emailsLog = \App\Email::create([
            'model_id' => $email->id,
            'model_type' => \App\Email::class,
            'type' => $email->type,
            'from' => $email->from,
            'to' => $email->to,
            'subject' => $email->subject,
            'message' => $email->message,
            'template' => 'resend-email',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
            'is_draft' => 1,
        ]);
        \App\EmailLog::create([
            'email_id' => $email->id,
            'email_log' => 'Email resend initiated',
            'message' => $email->to,
        ]);
        Mail::to($email->to)->send(new PurchaseEmail($email->subject, $email->message, $attachment));
        if ($type == 'approve') {
            $email->update(['approve_mail' => 0]);
        }

        return response()->json(['message' => 'Mail resent successfully']);
    }

    /**
     * Provide view for email reply modal
     *
     * @param [type] $id
     * @return view
     */
    public function replyMail($id)
    {
        $email = Email::find($id);
        $replyCategories = DB::table('reply_categories')->orderBy('name', 'asc')->get();
        $storeWebsites = \App\StoreWebsite::get();

        $parentCategory = ReplyCategory::where('parent_id', 0)->get();
        $allSubCategory = ReplyCategory::where('parent_id', '!=', 0)->get();
        $category = $subCategory = [];
        foreach ($allSubCategory as $key => $value) {
            $categoryList = ReplyCategory::where('id', $value->parent_id)->first();
            if ($categoryList->parent_id == 0) {
                $category[$value->id] = $value->name;
            } else {
                $subCategory[$value->id] = $value->name;
            }
        }

        $categories = $category;

        return view('emails.reply-modal', compact('email', 'replyCategories', 'storeWebsites', 'parentCategory', 'subCategory', 'categories'));
    }

    /**
     * Provide view for email reply all modal
     *
     * @param [type] $id
     * @return view
     */
    public function replyAllMail($id)
    {
        $email = Email::find($id);

        return view('emails.reply-all-modal', compact('email'));
    }

    /**
     * Provide view for email forward modal
     *
     * @param [type] $id
     * @return void
     */
    public function forwardMail($id)
    {
        $email = Email::find($id);

        return view('emails.forward-modal', compact('email'));
    }

    /**
     * Handle the email reply
     *
     * @return json
     */
    public function submitReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
        }

        $email = Email::find($request->reply_email_id);
        $replyPrefix = 'Re: ';
        $subject = substr($request->subject, 0, 4) === $replyPrefix
            ? $request->subject
            : $replyPrefix . $request->subject;
        $dateCreated = $email->created_at->format('D, d M Y');
        $timeCreated = $email->created_at->format('H:i');
        $originalEmailInfo = "On {$dateCreated} at {$timeCreated}, <{$email->from}> wrote:";
        //$message_to_store = $originalEmailInfo . '<br/>' . $request->message . '<br/>' . $email->message;

        $message_to_store = $originalEmailInfo . '<br/>' . $request->message;
        if($request->pass_history==1){
            $message_to_store = $originalEmailInfo . '<br/>' . $request->message . '<br/>' . $email->message;
        }

        $emailsLog = \App\Email::create([
            'model_id' => $email->id,
            'model_type' => \App\Email::class,
            'from' => $email->from,
            'to' => $request->receiver_email,
            'subject' => $subject,
            'message' => $message_to_store,
            'template' => 'reply-email',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
            'is_draft' => 1,
        ]);

        \App\EmailLog::create([
            'email_id' => $email->id,
            'email_log' => 'Email reply initiated',
            'to' => $request->receiver_email,
        ]);
        //$replyemails = (new ReplyToEmail($email, $request->message))->build();
        \App\Jobs\SendEmail::dispatch($emailsLog)->onQueue('send_email');
        //Mail::send(new ReplyToEmail($email, $request->message));

        return response()->json(['success' => true, 'message' => 'Email has been successfully sent.']);
    }

    /**
     * Handle the email reply
     *
     * @return json
     */
    public function submitReplyAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
        }

        $email = Email::find($request->reply_email_id);
        $replyPrefix = 'Re: ';
        $subject = substr($request->subject, 0, 4) === $replyPrefix
            ? $request->subject
            : $replyPrefix . $request->subject;
        $dateCreated = $email->created_at->format('D, d M Y');
        $timeCreated = $email->created_at->format('H:i');
        $originalEmailInfo = "On {$dateCreated} at {$timeCreated}, <{$email->to}> wrote:";
        $message_to_store = $originalEmailInfo . '<br/>' . $request->message . '<br/>' . $email->message;

        $emailAddress = $email->to;
        $emailPattern = '/<([^>]+)>/';
        $matches = [];
        if (preg_match($emailPattern, $emailAddress, $matches)) {
            $extractedEmail = $matches[1];
            $emailFrom = $extractedEmail;
        } else {
            $emailFrom = $email->from;
        }

        $emailsLog = \App\Email::create([
            'model_id' => $email->id,
            'model_type' => \App\Email::class,
            'from' => $email->to,
            'to' => $email->from,
            'subject' => $subject,
            'message' => $message_to_store,
            'template' => 'reply-email',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
            'is_draft' => 1,
        ]);

        \App\EmailLog::create([
            'email_id' => $email->id,
            'email_log' => 'Email reply initiated',
            'to' => $email->from,
        ]);
        //$replyemails = (new ReplyToEmail($email, $request->message))->build();
        \App\Jobs\SendEmail::dispatch($emailsLog)->onQueue('send_email');
        //Mail::send(new ReplyToEmail($email, $request->message));

        return response()->json(['success' => true, 'message' => 'Email has been successfully sent.']);
    }

    /**
     * Handle the email forward
     *
     * @return json
     */
    public function submitForward(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
        }

        $email = Email::find($request->forward_email_id);

        $emailClass = (new ForwardEmail($email, $email->message))->build();

        $email = \App\Email::create([
            'model_id' => $email->id,
            'model_type' => \App\Email::class,
            'from' => @$emailClass->from[0]['address'],
            'to' => $request->email,
            'subject' => $emailClass->subject,
            'message' => $emailClass->render(),
            'template' => 'forward-email',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
            'is_draft' => 1,
        ]);

        \App\EmailLog::create([
            'email_id' => $email->id,
            'email_log' => 'Email forward initiated',
            'message' => $email->to,
        ]);

        \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');

        //Mail::to($request->email)->send(new ForwardEmail($email, $email->message));

        return response()->json(['success' => true, 'message' => 'Email has been successfully sent.']);
    }

    public function getRemark(Request $request)
    {
        $email_id = $request->input('email_id');

        $remark = EmailRemark::where('email_id', $email_id)->get();

        return response()->json($remark, 200);
    }

    public function addRemark(Request $request)
    {
        $remark = $request->input('remark');
        $email_id = $request->input('id');
        $created_at = date('Y-m-d H:i:s');
        $update_at = date('Y-m-d H:i:s');

        if (! empty($remark)) {
            $remark_entry = EmailRemark::create([
                'email_id' => $email_id,
                'remarks' => $remark,
                'user_name' => Auth::user()->name,
            ]);
        }

        return response()->json(['remark' => $remark], 200);
    }

    public function markAsRead($id)
    {
        $email = Email::find($id);
        $email->seen = 1;
        $email->update();

        return response()->json(['success' => true, 'message' => 'Email has been read.']);
    }

    public function getAllEmails()
    {
        $available_models = ['supplier' => \App\Supplier::class, 'vendor' => \App\Vendor::class,
            'customer' => \App\Customer::class, 'users' => \App\User::class, ];
        $email_list = [];
        foreach ($available_models as $key => $value) {
            $email_list = array_merge($email_list, $value::whereNotNull('email')->pluck('email')->unique()->all());
        }
        // dd($email_list);
        return array_values(array_unique($email_list));
    }

    public function category(Request $request)
    {
        $values = ['category_name' => $request->input('category_name'), 'priority' => $request->input('priority'), 'type' => $request->type];
        DB::table('email_category')->insert($values);

        session()->flash('success', 'Category added successfully');

        return redirect('email');
    }

    public function status(Request $request)
    {
        $email_id = $request->input('status');
        $values = ['email_status' => $request->input('email_status'), 'type' => $request->type];
        DB::table('email_status')->insert($values);

        session()->flash('success', 'Status added successfully');

        return redirect('email');
    }

    public function updateEmail(Request $request)
    {
        $email_id = $request->input('email_id');
        $category = $request->input('category');
        $status = $request->input('status');

        $email = Email::find($email_id);
        $email->status = $status;
        $email->email_category_id = $category;

        $email->update();

        session()->flash('success', 'Data updated successfully');

        return redirect('email');
    }

    public function getFileStatus(Request $request)
    {
        $id = $request->id;
        $email = Email::find($id);

        if (isset($email->email_excel_importer)) {
            $status = 'No any update';

            if ($email->email_excel_importer === 3) {
                $status = 'File move on wetransfer';
            } elseif ($email->email_excel_importer === 2) {
                $status = 'Executed but we transfer file not exist';
            } elseif ($email->email_excel_importer === 1) {
                $status = 'Transfer exist';
            }

            return response()->json([
                'status' => true,
                'mail_status' => $status,
                'message' => 'Data found',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data not found',
        ], 200);
    }

    public function excelImporter(Request $request)
    {
        $id = $request->id;

        $email = Email::find($id);

        $body = $email->message;

        //check for wetransfer link

        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $body, $match);

        if (isset($match[0])) {
            $matches = $match[0];
            foreach ($matches as $matchLink) {
                if (strpos($matchLink, 'wetransfer.com') !== false || strpos($matchLink, 'we.tl') !== false) {
                    if (strpos($matchLink, 'google.com') === false) {
                        //check if wetransfer already exist
                        $checkIfExist = Wetransfer::where('url', $matchLink)->where('supplier', $request->supplier)->first();
                        if (! $checkIfExist) {
                            $wetransfer = new Wetransfer();
                            $wetransfer->type = 'excel';
                            $wetransfer->url = $matchLink;
                            $wetransfer->is_processed = 1;
                            $wetransfer->supplier = $request->supplier;
                            $wetransfer->save();

                            Email::where('id', $id)->update(['email_excel_importer' => 3]);

                            try {
                                self::downloadFromURL($matchLink, $request->supplier);
                            } catch (Exception $e) {
                                return response()->json(['message' => 'Something went wrong!'], 422);
                            }
                            //downloading wetransfer and generating data
                        }
                    }
                }
            }
        }

        //getting from attachments

        $attachments = $email->additional_data;
        if ($attachments) {
            $attachJson = json_decode($attachments);
            $attachs = $attachJson->attachment;

            //getting all attachments
            //check if extension is .xls or xlsx
            foreach ($attachs as $attach) {
                $attach = str_replace('email-attachments/', '', $attach);
                $extension = last(explode('.', $attach));
                if ($extension == 'xlsx' || $extension == 'xls') {
                    if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {
                        $excel = $request->supplier;
                        ErpExcelImporter::excelFileProcess($attach, $excel, '');
                    }
                } elseif ($extension == 'zip') {
                    if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {
                        $excel = $request->supplier;
                        $attachments_array = [];
                        $attachments = ErpExcelImporter::excelZipProcess('', $attach, $excel, '', $attachments_array);
                    }
                }
            }
        }

        return response()->json(['message' => 'Successfully Imported'], 200);
    }

    public static function downloadFromURL($url, $supplier)
    {
        $WETRANSFER_API_URL = 'https://wetransfer.com/api/v4/transfers/';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        if (strpos($url, 'https://we.tl/') !== false) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:21.0) Gecko/20100101 Feirefox/21.0'); // Necessary. The server checks for a valid User-Agent.
            curl_exec($ch);

            $response = curl_exec($ch);
            preg_match_all('/^Location:(.*)$/mi', $response, $matches);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, \App\Console\Commands\EmailController::class, 'downloadFromURL');
            curl_close($ch);

            if (isset($matches[1])) {
                if (isset($matches[1][0])) {
                    $url = trim($matches[1][0]);
                }
            }
        }

        //replace https://wetransfer.com/downloads/ from url

        $url = str_replace('https://wetransfer.com/downloads/', '', $url);

        //making array from url

        $dataArray = explode('/', $url);

        if (count($dataArray) == 2) {
            $securityhash = $dataArray[1];
            $transferId = $dataArray[0];
        } elseif (count($dataArray) == 3) {
            $securityhash = $dataArray[2];
            $recieptId = $dataArray[1];
            $transferId = $dataArray[0];
        } else {
            exit('Something is wrong with url');
        }

        //making post request to get the url
        $data = [];
        $data['intent'] = 'entire_transfer';
        $data['security_hash'] = $securityhash;

        $curlURL = $WETRANSFER_API_URL . $transferId . '/download';

        $cookie = 'cookie.txt';
        $url = 'https://wetransfer.com/';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/' . $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/' . $cookie);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            exit(curl_error($ch));
        }

        $re = '/name="csrf-token" content="([^"]+)"/m';

        preg_match_all($re, $response, $matches, PREG_SET_ORDER, 0);

        if (count($matches) != 0) {
            if (isset($matches[0])) {
                if (isset($matches[0][1])) {
                    $token = $matches[0][1];
                }
            }
        }

        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-CSRF-Token:' . $token;

        curl_setopt($ch, CURLOPT_URL, $curlURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $real = curl_exec($ch);

        $urlResponse = json_decode($real); //response decode
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $parameters = [];
        LogRequest::log($startTime, $url, 'GET', json_encode($parameters), $urlResponse, $httpcode, \App\Http\Controllers\EmailController::class, 'downloadFromURL');

        //dd($urlResponse);

        if (isset($urlResponse->direct_link)) {
            //echo $real;
            $downloadURL = $urlResponse->direct_link;

            $d = explode('?', $downloadURL);

            $fileArray = explode('/', $d[0]);

            $filename = end($fileArray);

            $file = file_get_contents($downloadURL);

            file_put_contents(storage_path('app/files/email-attachments/' . $filename), $file);
            $path = 'email-attachments/' . $filename;

            if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {
                if (strpos($filename, '.zip') !== false) {
                    $attachments = ErpExcelImporter::excelZipProcess($path, $filename, $supplier, '', '');
                }

                if (strpos($filename, '.xls') !== false || strpos($filename, '.xlsx') !== false) {
                    if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {
                        $excel = $supplier;
                        ErpExcelImporter::excelFileProcess($filename, $excel, '');
                    }
                }
            }
        }
    }

    public function bluckAction(Request $request)
    {
        $ids = $request->ids;
        $status = $request->status;
        $action_type = $request->action_type;

        if ($action_type == 'delete') {
            session()->flash('success', 'Email has been moved to trash successfully');
            Email::whereIn('id', $ids)->update(['status' => 'bin']);
        } else {
            session()->flash('success', 'Status has been updated successfully');
            Email::whereIn('id', $ids)->update(['status' => $status]);
        }

        return response()->json(['type' => 'success'], 200);
    }

    public function changeStatus(Request $request)
    {
        Email::where('id', $request->email_id)->update(['status' => $request->status_id]);

        $emailStatusHistory = EmailStatusChangeHistory::where('email_id', $request->email_id)->orderBy('id', 'desc')->first();

        $old_status_id = '';
        $old_user_id = '';

        if (! empty($emailStatusHistory)) {
            $old_status_id = $emailStatusHistory->status_id;
            $old_user_id = $emailStatusHistory->user_id;
        }

        EmailStatusChangeHistory::create([
            'status_id' => $request->status_id,
            'user_id' => \Auth::id(),
            'old_status_id' => $old_status_id,
            'old_user_id' => $old_user_id,
            'email_id' => $request->email_id,
        ]);

        session()->flash('success', 'Status has been updated successfully');

        return response()->json(['type' => 'success'], 200);
    }

    public function syncroniseEmail()
    {
        $report = CronJobReport::create([
            'signature' => 'fetch:all_emails',
            'start_time' => \Carbon\Carbon::now(),
        ]);
        $failedEmailAddresses = [];
        $emailAddresses = EmailAddress::orderBy('id', 'asc')->get();

        foreach ($emailAddresses as $emailAddress) {
            try {
                $cm = new ClientManager();
                $imap = $cm->make([
                    'host' => $emailAddress->host,
                    'port' => 993,
                    'encryption' => 'ssl',
                    'validate_cert' => false,
                    'username' => $emailAddress->username,
                    'password' => $emailAddress->password,
                    'protocol' => 'imap',
                ]);

                $imap->connect();

                $types = [
                    'inbox' => [
                        'inbox_name' => 'INBOX',
                        'direction' => 'from',
                        'type' => 'incoming',
                    ],
                    'sent' => [
                        'inbox_name' => 'INBOX.Sent',
                        'direction' => 'to',
                        'type' => 'outgoing',
                    ],
                ];

                $available_models = [
                    'supplier' => \App\Supplier::class, 'vendor' => \App\Vendor::class,
                    'customer' => \App\Customer::class, 'users' => \App\User::class,
                ];
                $email_list = [];
                foreach ($available_models as $key => $value) {
                    $email_list[$value] = $value::whereNotNull('email')->pluck('id', 'email')->unique()->all();
                }

                foreach ($types as $type) {
                    //dump("Getting emails for: " . $type['type']);
                    $inbox = $imap->getFolder($type['inbox_name']);
                    if ($type['type'] == 'incoming') {
                        $latest_email = Email::where('to', $emailAddress->from_address)->where('type', $type['type'])->latest()->first();
                    } else {
                        $latest_email = Email::where('from', $emailAddress->from_address)->where('type', $type['type'])->latest()->first();
                    }

                    $latest_email_date = $latest_email ? Carbon::parse($latest_email->created_at) : false;
                    if ($latest_email_date) {
                        $emails = ($inbox) ? $inbox->messages()->where('SINCE', $latest_email_date->subDays(1)->format('d-M-Y')) : '';
                    } else {
                        $emails = ($inbox) ? $inbox->messages() : '';
                    }
                    if ($emails) {
                        $emails = $emails->all()->get();
                        foreach ($emails as $email) {
                            $reference_id = $email->references;
                            //                        dump($reference_id);
                            $origin_id = $email->message_id;

                            // Skip if message is already stored
                            if (Email::where('origin_id', $origin_id)->count() > 0) {
                                continue;
                            }

                            // check if email has already been received

                            if ($email->hasHTMLBody()) {
                                $content = $email->getHTMLBody();
                            } else {
                                $content = $email->getTextBody();
                            }

                            $email_subject = $email->getSubject();
                            \Log::channel('customer')->info('Subject  => ' . $email_subject);

                            //if (!$latest_email_date || $email->getDate()->timestamp > $latest_email_date->timestamp) {
                            $attachments_array = [];
                            $attachments = $email->getAttachments();
                            $fromThis = $email->getFrom()[0]->mail;
                            $attachments->each(function ($attachment) use (&$attachments_array, $fromThis, $email_subject) {
                                $attachment->name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $attachment->name);
                                file_put_contents(storage_path('app/files/email-attachments/' . $attachment->name), $attachment->content);
                                $path = 'email-attachments/' . $attachment->name;

                                $attachments_array[] = $path;

                                /*start 3215 attachment fetch from DHL mail */
                                \Log::channel('customer')->info('Match Start  => ' . $email_subject);

                                $findFromEmail = explode('@', $fromThis);
                                if (strpos(strtolower($email_subject), 'your copy invoice') !== false && isset($findFromEmail[1]) && (strtolower($findFromEmail[1]) == 'dhl.com')) {
                                    \Log::channel('customer')->info('Match Found  => ' . $email_subject);
                                    $this->getEmailAttachedFileData($attachment->name);
                                }
                                /*end 3215 attachment fetch from DHL mail */
                            });

                            $from = $email->getFrom()[0]->mail;
                            $to = array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail;

                            // Model is sender if its incoming else its receiver if outgoing
                            if ($type['type'] == 'incoming') {
                                $model_email = $from;
                            } else {
                                $model_email = $to;
                            }

                            // Get model id and model type

                            extract($this->getModel($model_email, $email_list));

                            $subject = explode('#', $email_subject);
                            if (isset($subject[1]) && ! empty($subject[1])) {
                                $findTicket = \App\Tickets::where('ticket_id', $subject[1])->first();
                                if ($findTicket) {
                                    $model_id = $findTicket->id;
                                    $model_type = \App\Tickets::class;
                                }
                            }

                            $params = [
                                'model_id' => $model_id,
                                'model_type' => $model_type,
                                'origin_id' => $origin_id,
                                'reference_id' => $reference_id,
                                'type' => $type['type'],
                                'seen' => count($email->getFlags()) > 0 ? $email->getFlags()['seen'] : 0,
                                'from' => $email->getFrom()[0]->mail,
                                'to' => array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                'subject' => $email->getSubject(),
                                'message' => $content,
                                'template' => 'customer-simple',
                                'additional_data' => json_encode(['attachment' => $attachments_array]),
                                'created_at' => $email->getDate(),
                            ];

                            $emailData = Email::create($params);

                            if ($type['type'] == 'incoming') {
                                $message = trim($content);
                                $reply = (new EmailParser())->parse($message);
                                $fragment = current($reply->getFragments());
                                if ($reply) {
                                    $customer = \App\Customer::where('email', $from)->first();
                                    if (! empty($customer)) {
                                        // store the main message
                                        $params = [
                                            'number' => $customer->phone,
                                            'message' => $fragment->getContent(),
                                            'media_url' => null,
                                            'approved' => 0,
                                            'status' => 0,
                                            'contact_id' => null,
                                            'erp_user' => null,
                                            'supplier_id' => null,
                                            'task_id' => null,
                                            'dubizzle_id' => null,
                                            'vendor_id' => null,
                                            'customer_id' => $customer->id,
                                            'is_email' => 1,
                                            'from_email' => $email->getFrom()[0]->mail,
                                            'to_email' => array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                            'email_id' => $emailData->id,
                                        ];
                                        $messageModel = \App\ChatMessage::create($params);
                                        \App\Helpers\MessageHelper::whatsAppSend($customer, $fragment->getContent(), null, null, $isEmail = true);
                                        \App\Helpers\MessageHelper::sendwatson($customer, $fragment->getContent(), null, $messageModel, $params, $isEmail = true);

                                        // Code for if auto approve flag is YES then send Bot replay to customer email address account, If No then save email in draft tab.
                                        $replies = \App\ChatbotQuestion::join('chatbot_question_examples', 'chatbot_questions.id', 'chatbot_question_examples.chatbot_question_id')
                                        ->join('chatbot_questions_reply', 'chatbot_questions.id', 'chatbot_questions_reply.chatbot_question_id')
                                        ->where('chatbot_questions_reply.store_website_id', ($customer->store_website_id) ? $customer->store_website_id : 1)
                                        ->select('chatbot_questions.value', 'chatbot_questions.keyword_or_question', 'chatbot_questions.erp_or_watson', 'chatbot_questions.auto_approve', 'chatbot_question_examples.question', 'chatbot_questions_reply.suggested_reply')
                                        ->where('chatbot_questions.erp_or_watson', 'erp')
                                        ->get();

                                        $messages = $fragment->getContent();

                                        foreach ($replies as $reply) {
                                            if ($messages != '' && $customer) {
                                                $keyword = $reply->question;
                                                if (($keyword == $messages || strpos(strtolower(trim($messages)), strtolower(trim($keyword))) !== false) && $reply->suggested_reply) {
                                                    $lastInsertedEmail = Email::where('id', $emailData->id)->first();
                                                    if ($reply->auto_approve == 0) {
                                                        $lastInsertedEmail->is_draft = 1;
                                                        $lastInsertedEmail->save();
                                                    } else {
                                                        $emaildetails = [];

                                                        $emaildetails['id'] = $lastInsertedEmail->id;
                                                        $emaildetails['to'] = $customer->email;
                                                        $emaildetails['subject'] = $lastInsertedEmail->subject;
                                                        $emaildetails['message'] = $reply->suggested_reply;
                                                        $from_address = '';
                                                        $from_address = array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail;
                                                        if (empty($from_address)) {
                                                            $from_address = config('env.MAIL_FROM_ADDRESS');
                                                        }
                                                        $emaildetails['from'] = $from_address;

                                                        \App\Jobs\SendEmail::dispatch($lastInsertedEmail, $emaildetails)->onQueue('send_email');

                                                        $createEmail = \App\Email::create([
                                                            'model_id' => $model_id,
                                                            'model_type' => $model_type,
                                                            'from' => $emaildetails['from'],
                                                            'to' => $emaildetails['to'],
                                                            'subject' => $emaildetails['subject'],
                                                            'message' => $reply->suggested_reply,
                                                            'template' => 'customer-simple',
                                                            'additional_data' => $model_id,
                                                            'status' => 'send',
                                                            'store_website_id' => null,
                                                            'is_draft' => 0,
                                                            'type' => 'outgoing',
                                                        ]);

                                                        $chatMessage = [
                                                            'number' => $customer->phone,
                                                            'message' => $reply->suggested_reply,
                                                            'media_url' => null,
                                                            'approved' => 0,
                                                            'status' => 0,
                                                            'contact_id' => null,
                                                            'erp_user' => null,
                                                            'supplier_id' => null,
                                                            'task_id' => null,
                                                            'dubizzle_id' => null,
                                                            'vendor_id' => null,
                                                            'customer_id' => $customer->id,
                                                            'is_email' => 1,
                                                            'from_email' => $emaildetails['from'],
                                                            'to_email' => $emaildetails['to'],
                                                            'email_id' => $createEmail->id,
                                                        ];
                                                        \App\ChatMessage::create($chatMessage);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            //}
                        }
                    }
                }

                $historyParam = [
                    'email_address_id' => $emailAddress->id,
                    'is_success' => 1,
                ];

                EmailRunHistories::create($historyParam);
                $report->update(['end_time' => Carbon::now()]);
            } catch (\Exception $e) {
                $exceptionMessage = $e->getMessage();

                if ($e->getPrevious() !== null) {
                    $previousMessage = $e->getPrevious()->getMessage();
                    $exceptionMessage = $previousMessage . ' | ' . $exceptionMessage;
                }

                \Log::channel('customer')->info($exceptionMessage);
                $historyParam = [
                    'email_address_id' => $emailAddress->id,
                    'is_success' => 0,
                    'message' => $exceptionMessage,
                ];
                EmailRunHistories::create($historyParam);
                \App\CronJob::insertLastError('fetch:all_emails', $exceptionMessage);
                $failedEmailAddresses[] = $emailAddress->username;
            }
        }
        if (! empty($failedEmailAddresses)) {
            session()->flash('danger', 'Some address failed to synchronize.For more details: please check Email Run History for following Email Addresses: ' . implode(', ', $failedEmailAddresses));

            return redirect('/email');
        } else {
            session()->flash('success', 'Emails added successfully');

            return redirect('/email');
        }
    }

    public function getModel($email, $email_list)
    {
        $model_id = null;
        $model_type = null;

        // Traverse all models
        foreach ($email_list as $key => $value) {
            // If email exists in the DB
            if (isset($value[$email])) {
                $model_id = $value[$email];
                $model_type = $key;
                break;
            }
        }

        return compact('model_id', 'model_type');
    }

    public function getEmailAttachedFileData($fileName = '')
    {
        $file = fopen(storage_path('app/files/email-attachments/' . $fileName), 'r');

        $skiprowupto = 1; //skip first line
        $rowincrement = 1;
        $attachedFileDataArray = [];
        while (($data = fgetcsv($file, 4000, ',')) !== false) {
            if ($rowincrement > $skiprowupto) {
                //echo '<pre>'.print_r($data = fgetcsv($file, 4000, ","),true).'</pre>';
                if (isset($data[0]) && ! empty($data[0])) {
                    try {
                        $due_date = date('Y-m-d', strtotime($data[9]));
                        $attachedFileDataArray = [
                            'line_type' => $data[0],
                            'billing_source' => $data[1],
                            'original_invoice_number' => $data[2],
                            'invoice_number' => $data[3],
                            'invoice_identifier' => $data[5],
                            'invoice_type' => $data[6],
                            'invoice_currency' => $data[69],
                            'invoice_amount' => $data[70],
                            'invoice_type' => $data[6],
                            'invoice_date' => $data[7],
                            'payment_terms' => $data[8],
                            'due_date' => $due_date,
                            'billing_account' => $data[11],
                            'billing_account_name' => $data[12],
                            'billing_account_name_additional' => $data[13],
                            'billing_address_1' => $data[14],
                            'billing_postcode' => $data[17],
                            'billing_city' => $data[18],
                            'billing_state_province' => $data[19],
                            'billing_country_code' => $data[20],
                            'billing_contact' => $data[21],
                            'shipment_number' => $data[23],
                            'shipment_date' => $data[24],
                            'product' => $data[30],
                            'product_name' => $data[31],
                            'pieces' => $data[32],
                            'origin' => $data[33],
                            'orig_name' => $data[34],
                            'orig_country_code' => $data[35],
                            'orig_country_name' => $data[36],
                            'senders_name' => $data[37],
                            'senders_city' => $data[42],
                            'created_at' => \Carbon\Carbon::now(),
                            'updated_at' => \Carbon\Carbon::now(),
                        ];
                        if (! empty($attachedFileDataArray)) {
                            $attachresponse = \App\Waybillinvoice::create($attachedFileDataArray);

                            // check that way bill exist not then create
                            $wayBill = \App\Waybill::where('awb', $attachresponse->shipment_number)->first();
                            if (! $wayBill) {
                                $wayBill = new \App\Waybill;
                                $wayBill->awb = $attachresponse->shipment_number;

                                $wayBill->from_customer_name = $data[45];
                                $wayBill->from_city = $data[42];
                                $wayBill->from_country_code = $data[44];
                                $wayBill->from_customer_address_1 = $data[38];
                                $wayBill->from_customer_address_2 = $data[39];
                                $wayBill->from_customer_pincode = $data[41];
                                $wayBill->from_company_name = $data[39];

                                $wayBill->to_customer_name = $data[50];
                                $wayBill->to_city = $data[55];
                                $wayBill->to_country_code = $data[57];
                                $wayBill->to_customer_phone = '';
                                $wayBill->to_customer_address_1 = $data[51];
                                $wayBill->to_customer_address_2 = $data[52];
                                $wayBill->to_customer_pincode = $data[54];
                                $wayBill->to_company_name = '';

                                $wayBill->actual_weight = $data[68];
                                $wayBill->volume_weight = $data[66];

                                $wayBill->cost_of_shipment = $data[70];
                                $wayBill->package_slip = $attachresponse->shipment_number;
                                $wayBill->pickup_date = date('Y-m-d', strtotime($data[24]));
                                $wayBill->save();
                            }

                            $cash_flow = new CashFlow();
                            $cash_flow->fill([
                                'date' => $attachresponse->due_date ? $attachresponse->due_date : null,
                                'type' => 'pending',
                                'description' => 'Waybill invoice details',
                                'cash_flow_able_id' => $attachresponse->id,
                                'cash_flow_able_type' => \App\Waybillinvoice::class,
                            ])->save();
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error from the dhl invoice : ' . $e->getMessage());
                    }
                }
            }
            $rowincrement++;
        }
        fclose($file);
    }

    public function getEmailEvents($emailId)
    {
        $exist = Email::where('id', $emailId)->first(); //$originId = "9e238becd3bc31addeff3942fc54e340@swift.generated";
        $events = [];
        $eventData = '';
        if ($exist != null) {
            $events = \App\SendgridEvent::where('email_id', $emailId)->select('timestamp', 'event')->orderBy('id', 'desc')->get();
        }
        foreach ($events as $event) {
            $eventData .= '<tr><td>' . $event['timestamp'] . '</td><td>' . $event['event'] . '</td></tr>';
        }
        if ($eventData == '') {
            $eventData = '<tr><td>No data found.</td></tr>';
        }

        return $eventData;
    }

    public function getAllEmailEvents(Request $request)
    {
        $events = \App\SendgridEvent::select('*');

        if (! empty($request->email)) {
            $events = $events->where('email', 'like', '%' . $request->email . '%');
        }

        if (! empty($request->event)) {
            $events = $events->where('event', 'like', '%' . $request->event . '%');
        }

        $events = $events->orderBy('id', 'desc')->groupBy('sg_message_id')->paginate(30)->appends(request()->except(['page']));

        $event = $request->event ?? '';

        return view('emails.events', compact('events', 'event'));
    }

    public function getAllEmailEventsJourney(Request $request)
    {
        $events = \App\SendgridEvent::select('*');

        if (! empty($request->email)) {
            $events = $events->where('email', 'like', '%' . $request->email . '%');
        }

        if (! empty($sender_email = $request->sender_email)) {
            $events = $events->whereHas('sender', function ($query) use ($sender_email) {
                // Define the condition for filtering the related emails
                $query->where('from', $sender_email);
            });
        }

        if (! empty($request->event)) {
            $events = $events->where('event', 'like', '%' . $request->event . '%');
        }
        $events = $events->orderBy('id', 'desc')->paginate(30)->appends(request()->except(['page']));

        $eventColors = SendgridEventColor::all();

        return view('emails.event_journey', compact('events', 'eventColors'));
    }

    /**
     * Get Email Logs
     */
    public function getEmailLogs($emailid)
    {
        $emailLogs = EmailLog::where('email_id', $emailid)->orderBy('id', 'desc')->get();

        $emailLogData = '';

        foreach ($emailLogs as $emailLog) {
            $colorCode = '';

            if ($emailLog['is_error'] == 1 && $emailLog['service_type'] === 'SMTP') {
                $colorCode = env('EMAIL_LOG_SMTP_ERROR_COLOR_CODE', '#f8eddd');
            }

            if ($emailLog['is_error'] == 1 && $emailLog['service_type'] === 'IMAP') {
                $colorCode = env('EMAIL_LOG_IMAP_ERROR_COLOR_CODE', '#eddddd');
            }

            $emailLogData .= '<tr style="background:' . $colorCode . '"><td>' . $emailLog['created_at'] . '</td><td>' . $emailLog['email_log'] . '</td><td>' . $emailLog['message'] . '</td></tr>';
        }
        if ($emailLogData == '') {
            $emailLogData = '<tr><td>No data found.</td></tr>';
        }

        return $emailLogData;
    }

    /**
     * Update Email Category using Ajax
     */
    public function changeEmailCategory(Request $request)
    {
        Email::where('id', $request->email_id)->update(['email_category_id' => $request->category_id]);

        $emailCategoryHistory = EmailCategoryHistory::where('email_id', $request->email_id)->orderBy('id', 'desc')->first();

        $old_category_id = '';
        $old_user_id = '';

        if (! empty($emailCategoryHistory)) {
            $old_category_id = $emailCategoryHistory->category_id;
            $old_user_id = $emailCategoryHistory->user_id;
        }

        EmailCategoryHistory::create([
            'category_id' => $request->category_id,
            'user_id' => \Auth::id(),
            'old_category_id' => $old_category_id,
            'old_user_id' => $old_user_id,
            'email_id' => $request->email_id,
        ]);

        session()->flash('success', 'Status has been updated successfully');

        return response()->json(['type' => 'success'], 200);
    }

    public function changeEmailStatus(Request $request)
    {
        Email::where('id', $request->status)->update(['status' => $request->status_id]);

        session()->flash('success', 'Status has been updated successfully');

        return response()->json(['type' => 'success'], 200);
    }

    /**
     * To view email in iframe
     */
    public function viewEmailFrame(Request $request)
    {
        $id = $request->id;
        $emailData = Email::find($id);
        if($emailData->seen==1){
            $emailData->seen = 0;        
        } else {
            $emailData->seen = 1;        
        }
        $emailData->save();

        return view('emails.frame-view', compact('emailData'));
    }

    public function getEmailFilterOptions(Request $request)
    {
        $user = Auth::user();
        $admin = $user->isAdmin();
        $usernames = [];
        if (! $admin) {
            $emaildetails = \App\EmailAssign::select('id', 'email_address_id')->with('emailAddress')->where(['user_id' => $user->id])->get();
            if ($emaildetails) {
                foreach ($emaildetails as $_email) {
                    $usernames[] = $_email->emailAddress->username;
                }
            }
        }

        $senderDropdown = Email::select('from');

        if (count($usernames) > 0) {
            $senderDropdown = $senderDropdown->where(function ($senderDropdown) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $senderDropdown->orWhere('from', 'like', '%' . $_uname . '%');
                }
            });

            $senderDropdown = $senderDropdown->orWhere(function ($senderDropdown) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $senderDropdown->orWhere('to', 'like', '%' . $_uname . '%');
                }
            });
        }
        $senderDropdown = $senderDropdown->distinct()->get()->toArray();

        $receiverDropdown = Email::select('to');

        if (count($usernames) > 0) {
            $receiverDropdown = $receiverDropdown->where(function ($receiverDropdown) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $receiverDropdown->orWhere('from', 'like', '%' . $_uname . '%');
                }
            });

            $receiverDropdown = $receiverDropdown->orWhere(function ($receiverDropdown) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $receiverDropdown->orWhere('to', 'like', '%' . $_uname . '%');
                }
            });
        }

        $receiverDropdown = $receiverDropdown->distinct()->get()->toArray();

        $mailboxDropdown = \App\EmailAddress::pluck('from_address', 'from_name', 'username');

        $mailboxDropdown = $mailboxDropdown->toArray();

        $response = [
            'senderDropdown' => $senderDropdown,
            'receiverDropdown' => $receiverDropdown,
            'mailboxDropdown' => $mailboxDropdown,
        ];

        return $response;
    }

    public function ajaxsearch(Request $request)
    {
        $searchEmail = $request->get('search');
        if (! empty($searchEmail)) {
            $userEmails = Email::where('type', 'incoming')->where('from', 'like', '%' . $searchEmail . '%')->orderBy('created_at', 'desc')->get();
        } else {
            $userEmails = Email::where('type', 'incoming')->orderBy('created_at', 'desc')->limit(5)->get();
        }

        $html = '';
        foreach ($userEmails as $key => $userEmail) {
            $html .= '<tr>
                <td>' . Carbon::parse($userEmail->created_at)->format('d-m-Y H:i:s') . '</td>
                <td>' . substr($userEmail->from, 0, 20) . ' ' . (strlen($userEmail->from) > 20 ? '...' : '') . '</td>
                <td>' . substr($userEmail->to, 0, 15) . ' ' . (strlen($userEmail->to) > 10 ? '...' : '') . '</td>
                <td>' . substr($userEmail->subject, 0, 15) . ' ' . (strlen($userEmail->subject) > 10 ? '...' : '') . '</td>
                <td>' . substr($userEmail->message, 0, 25) . ' ' . (strlen($userEmail->message) > 20 ? '...' : '') . '</td>
                <td> <a href="javascript:;" data-id="' . $userEmail->id . '" data-content="' . $userEmail->message . '" class="menu_editor_copy btn btn-xs p-2" >
                                    <i class="fa fa-copy"></i>
                    </a></td>
            </tr>';
        }

        return $html;
    }

    public function getCategoryMappings(Request $request)
    {
        $term = $request->term ?? '';
        $sender = $request->sender ?? '';
        $receiver = $request->receiver ?? '';
        $status = $request->status ?? '';
        $category = $request->category ?? '';
        $mailbox = $request->mail_box ?? '';
        $email_model_type = $request->email_model_type ?? '';
        $email_box_id = $request->email_box_id ?? '';


        //where('type', 'incoming')
        $userEmails = Email::where('email_category_id', '>', 0)
            ->orderBy('created_at', 'desc')
            ->groupBy('from');

        if ($term) {
            $userEmails = $userEmails->where(function ($userEmails) use ($term) {
                $userEmails->where('from', 'like', '%' . $term . '%')
                    ->orWhere('to', 'like', '%' . $term . '%')
                    ->orWhere('subject', 'like', '%' . $term . '%')
                    ->orWhere('message', 'like', '%' . $term . '%');
            });
        }

        if ($sender) {
            $sender = explode(',', $request->sender);
            $userEmails = $userEmails->where(function ($userEmails) use ($sender) {
                $userEmails->whereIn('from', $sender);
            });
        }

        if ($receiver) {
            $receiver = explode(',', $request->receiver);
            $userEmails = $userEmails->where(function ($userEmails) use ($receiver) {
                $userEmails->whereIn('to', $receiver);
            });
        }
        
        if ($category) {
            $category = explode(',', $request->category);
            $userEmails = $userEmails->where(function ($userEmails) use ($category) {
                $userEmails->whereIn('email_category_id', $category);
            });
        }

        if ($email_box_id) {
            $emailBoxIds = explode(',', $email_box_id);

            $userEmails = $userEmails->where(function ($userEmails) use ($emailBoxIds) {
                $userEmails->whereIn('email_box_id', $emailBoxIds);
            });
        }

        $userEmails = $userEmails->paginate(10)->appends(request()->except(['page']));

        //Get All Category
        $email_categories = DB::table('email_category')->get();

        $emailModelTypes = Email::emailModelTypeList();

        $emailBoxes = EmailBox::select('id', 'box_name')->get();
        return view('emails.category.mappings', compact('userEmails', 'email_categories', 'emailModelTypes', 'emailBoxes'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    // DEVTASK - 23369
    public function assignModel(Request $request)
    {
        $model_type = '';
        $model = '';
        if ($request->model_name == 'customer') {
            $model_type = "\App\Customer";
            $model = new \App\Customer;
            $model_name = 'Customer';
        } elseif ($request->model_name == 'vendor') {
            $model_type = "\App\Vendor";
            $model = new \App\Vendor;
            $model_name = 'Vendor';
        } elseif ($request->model_name == 'supplier') {
            $model_type = "\App\Supplier";
            $model = new \App\Supplier;
            $model_name = 'Supplier';
        } else {
            $model_type = "\App\User";
            $model = new \App\User;
            $model_name = 'User';
        }

        $email = Email::where('id', $request->email_id)->first();
        $email->is_unknow_module = 0;
        $email->model_type = $model_name;
        $email->save();

        \Log::info('Assign Model to email : ' . $model_name);

        $userExist = $model::where('email', $email->from)->first();

        if (empty($userExist)) {
            if ($request->model_name == 'supplier') {
                $model::create([
                    'email' => $email->from,
                ]);
            } else {
                $model::create([
                    'name' => explode('@', $email->from)[0],
                    'email' => $email->from,
                ]);
            }

            return response()->json(['type' => 'success'], 200);
        }
    }

    public function updateModelColor(Request $request)
    {
        foreach ($request->color_name as $key => $value) {
            $model = ModelColor::where('id', $key)->first();
            $model->color_code = $value;
            $model->save();
        }

        return redirect('/email');
    }

    public function getModelNames(Request $request)
    {
        $modelColors = ModelColor::where('model_name', 'like', '%' . $request->model_name . '%')->get();
        $returnHTML = view('emails.modelTable')->with('modelColors', $modelColors)->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function getEmailCategoryChangeLogs(Request $request)
    {
        $emailId = $request->email_id;
        $emailCagoryLogs = EmailCategoryHistory::with(['category', 'oldCategory', 'updatedByUser', 'user'])->where('email_id', $emailId)->get();

        $returnHTML = view('emails.categoryChangeLogs')->with('data', $emailCagoryLogs)->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function getEmailStatusChangeLogs(Request $request)
    {
        $emailId = $request->email_id;
        $emailCagoryLogs = EmailStatusChangeHistory::with(['status', 'oldstatus', 'updatedByUser', 'user'])->where('email_id', $emailId)->get();

        $returnHTML = view('emails.statusChangeLogs')->with('data', $emailCagoryLogs)->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function getReplyListByCategory(Request $request)
    {
        $replies = Reply::where('category_id', $request->category_id)->get();
        $returnHTML = view('emails.replyList')->with('data', $replies)->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function getReplyListFromQuickReply(Request $request)
    {
        $storeWebsite = $request->get('storeWebsiteId');
        $parent_category = $request->get('parentCategoryId');
        $category_ids = $request->get('categoryId');
        $sub_category_ids = $request->get('subCategoryId');

        $categoryChildNode = [];

        if ($parent_category) {
            $parentNode = ReplyCategory::select(\DB::raw('group_concat(id) as ids'))->where('id', $parent_category)->where('parent_id', '=', 0)->first();
            if ($parentNode) {
                $subCatChild = ReplyCategory::whereIn('parent_id', explode(',', $parentNode->ids))->get()->pluck('id')->toArray();
                $categoryChildNode = ReplyCategory::whereIn('parent_id', $subCatChild)->get()->pluck('id')->toArray();
            }
        }

        $replies = \App\ReplyCategory::join('replies', 'reply_categories.id', 'replies.category_id')
        ->leftJoin('store_websites as sw', 'sw.id', 'replies.store_website_id')
        ->where('model', 'Store Website')
        ->select(['replies.*', 'sw.website', 'reply_categories.intent_id', 'reply_categories.name as category_name', 'reply_categories.parent_id', 'reply_categories.id as reply_cat_id']);

        if ($storeWebsite > 0) {
            $replies = $replies->where('replies.store_website_id', $storeWebsite);
        }

        if (! empty($parent_category)) {
            if ($categoryChildNode) {
                $replies = $replies->where(function ($q) use ($categoryChildNode) {
                    $q->orWhereIn('reply_categories.id', $categoryChildNode);
                });
            } else {
                $replies = $replies->where(function ($q) use ($parent_category) {
                    $q->orWhere('reply_categories.id', $parent_category)->where('reply_categories.parent_id', '=', 0);
                });
            }
        }

        if (! empty($category_ids)) {
            $replies = $replies->where(function ($q) use ($category_ids) {
                $q->orWhere('reply_categories.parent_id', $category_ids)->where('reply_categories.parent_id', '!=', 0);
            });
        }

        if (! empty($sub_category_ids)) {
            $replies = $replies->where(function ($q) use ($sub_category_ids) {
                $q->orWhere('reply_categories.id', $sub_category_ids)->where('reply_categories.parent_id', '!=', 0);
            });
        }

        $replies = $replies->get();

        $returnHTML = view('emails.replyList')->with('data', $replies)->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function eventColor(Request $request)
    {
        $eventColors = $request->all();
        $data = $request->except('_token');
        foreach ($eventColors['color_name'] as $key => $value) {
            $sendgridEventColor = SendgridEventColor::find($key);
            $sendgridEventColor->color = $value;
            $sendgridEventColor->save();
        }

        return redirect()->back()->with('success', 'The event color updated successfully.');
    }

    public function updateEmailRead(Request $request)
    {
        $email = Email::findOrFail($request->get('id'));
        $email->seen = 1;
        $email->update();

        return response()->json(['code' => 200, 'data' => $email, 'message' => 'Email Update successfully!!!']);
    }

    public function quickEmailList(Request $request)
    {
        $emails = new Email();
        $email_categories = EmailCategory::get();

        $senderEmailIds = Email::select('from')->groupBy('from')->get();
        $receiverEmailIds = Email::select('to')->groupBy('to')->get();
        $modelsTypes = Email::select('model_type')->groupBy('model_type')->get();
        $mailTypes = Email::select('type')->groupBy('type')->get();
        $emailStatuses = Email::select('status')->groupBy('status')->get();

        //Get All Status
        $email_status = DB::table('email_status');

        if (! empty($request->type) && $request->type == 'outgoing') {
            $email_status = $email_status->where('type', 'sent');
        } else {
            $email_status = $email_status->where('type', '!=', 'sent');
        }

        $email_status = $email_status->get();

        if ($request->sender_ids) {
            $emails = $emails->WhereIn('from', $request->sender_ids);
        }
        if ($request->receiver_ids) {
            $emails = $emails->WhereIn('website_id', $request->receiver_ids);
        }
        if ($request->model_types) {
            $emails = $emails->WhereIn('to', $request->model_types);
        }
        if ($request->mail_types) {
            $emails = $emails->WhereIn('type', $request->mail_types);
        }
        if ($request->cat_ids) {
            $emails = $emails->WhereIn('email_category_id', $request->cat_ids);
        }
        if ($request->status) {
            $emails = $emails->WhereIn('status', $request->status);
        }
        if ($request->date) {
            $emails = $emails->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        $emails = $emails->latest()->paginate(\App\Setting::get('pagination', 25));

        return view('emails.quick-email-list', compact('emails', 'email_categories', 'senderEmailIds', 'receiverEmailIds', 'modelsTypes', 'mailTypes', 'emailStatuses', 'email_status'));
    }

    public function getEmailreplies(Request $request)
    {   
        $id = $request->id;
        $emailReplies = Reply::where('category_id', $id)->orderBy('id', 'ASC')->get();
        
        return json_encode($emailReplies);
    }
}
