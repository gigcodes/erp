<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Email;
use App\EmailRemark;
use App\CronJob;
use App\CronJobReport;
use Webklex\IMAP\Client;
use App\Mails\Manual\PurchaseEmail;
use Mail;
use Auth;
use DB;
use App\Mails\Manual\ReplyToEmail;
use App\Mails\Manual\ForwardEmail;
use Illuminate\Support\Facades\Validator;
use App\Wetransfer;
use seo2websites\ErpExcelImporter\ErpExcelImporter;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Set default type as incoming
        $type = "incoming";
		$seen = '1';
		
        $term = $request->term ?? '';
        $sender = $request->sender ?? '';
        $receiver = $request->receiver ?? '';
        $status = $request->status ?? '';
        $category = $request->category ?? '';
        $date = $request->date ?? '';
        $type = $request->type ?? $type;
        $seen = $request->seen ?? $seen;
        $query = (new Email())->newQuery();
        $trash_query = false;

        // If type is bin, check for status only
        if($type == "bin"){
            $trash_query = true;
            $query = $query->where('status',"bin");
        }else{
            $query = $query->where('type',$type);
        }
        if($date) {
            $query = $query->whereDate('created_at',$date);
        }
        if($term) {
            $query = $query->where(function ($query) use ($term) {
                $query->where('from','like','%'.$term.'%')
                ->orWhere('to','like','%'.$term.'%')
                ->orWhere('subject','like','%'.$term.'%')
                ->orWhere('message','like','%'.$term.'%');
            });
        }
		
		if(!$term)
		{
			if($sender)
			{
				$query = $query->where(function ($query) use ($sender) {
					$query->orWhere('from','like','%'.$sender.'%');
				});
			}
			if($receiver)
			{
				$query = $query->where(function ($query) use ($receiver) {
					$query->orWhere('to','like','%'.$receiver.'%');
				});
			}
			if($status)
			{
				$query = $query->where(function ($query) use ($status) {
					$query->orWhere('status',$status);
				});
			}
			if($category)
			{
				$query = $query->where(function ($query) use ($category) {
					$query->orWhere('email_category_id',$category);
				});
			}
			
		}

        if(isset($seen)){
            if($seen != 'both'){
                $query = $query->where('seen',$seen);
            }
        }

        // If it isn't trash query remove email with status trashed
        if(!$trash_query){
            $query = $query->where(function($query){ return $query->where('status','<>',"bin")->orWhereNull('status');});
        }
		
		$query = $query->orderByDesc('created_at');
		
		
		
		//Get All Category
		$email_status = DB::table('email_status')->get();
		
		//Get All Status
        $email_categories = DB::table('email_category')->get();
        
        //Get Cron Email Histroy
		$reports = CronJobReport::where('cron_job_reports.signature','fetch:all_emails')
        ->join('cron_jobs', 'cron_job_reports.signature', 'cron_jobs.signature')
        ->select(['cron_job_reports.*','cron_jobs.last_error'])->paginate(15);

        $emails = $query->paginate(30)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('emails.search', compact('emails','date','term','type','email_categories','email_status'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string)$emails->links(),
                'count' => $emails->total(),
                'emails' => $emails
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
        return view('emails.index',['emails'=>$emails,'type'=>'email' ,'search_suggestions'=>$search_suggestions,'email_categories'=>$email_categories,'email_status'=>$email_status, 'reports' => $reports])->with('i', ($request->input('page', 1) - 1) * 5);

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
     * @param  \Illuminate\Http\Request  $request
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
        $status = "bin";
        $message = "Email has been trashed";

        // If status is already trashed, move to inbox
        if($email->status == 'bin'){
            $status = "";
            $message = "Email has been sent to inbox";
        }

        $email->status= $status;
        $email->update();

        return response()->json(['message' => $message]);
    }

    public function resendMail($id, Request $request) {
        $email = Email::find($id);
        $attachment = [];
        $imap = new Client([
            'host' => env('IMAP_HOST_PURCHASE'),
            'port' => env('IMAP_PORT_PURCHASE'),
            'encryption' => env('IMAP_ENCRYPTION_PURCHASE'),
            'validate_cert' => env('IMAP_VALIDATE_CERT_PURCHASE'),
            'username' => env('IMAP_USERNAME_PURCHASE'),
            'password' => env('IMAP_PASSWORD_PURCHASE'),
            'protocol' => env('IMAP_PROTOCOL_PURCHASE')
        ]);

        $imap->connect();

        $array = is_array(json_decode($email->additional_data, true)) ? json_decode($email->additional_data, true) : [];

        if (array_key_exists('attachment', $array)) {
            $temp = json_decode($email->additional_data, true)[ 'attachment' ];
        }
        if (!is_array($temp)) {
            $attachment[] = $temp;
        } else {
            $attachment = $temp;
        }
        $customConfig = [
            'from' =>  $email->from,
        ];
        Mail::to($email->to)->send(new PurchaseEmail($email->subject, $email->message, $attachment));
        if($type == 'approve') {
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
    public function replyMail($id) {
        $email = Email::find($id);
        return view('emails.reply-modal',compact('email'));
    }

    /**
     * Provide view for email forward modal
     *
     * @param [type] $id
     * @return void
     */
    public function forwardMail($id) {
        $email = Email::find($id);
        return view('emails.forward-modal',compact('email'));
        }

    /**
     * Handle the email reply
     *
     * @param Request $request
     * @return json
     */
    public function submitReply(Request $request)
    {
       $validator = Validator::make($request->all(), [
           'message' => 'required'
       ]);

       if ($validator->fails()) {
           return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
       }

       $email = Email::find($request->reply_email_id);
       Mail::send(new ReplyToEmail($email, $request->message));

       return response()->json(['success' => true, 'message' => 'Email has been successfully sent.']);
   }

   /**
    * Handle the email forward
    *
    * @param Request $request
    * @return json
    */
   public function submitForward(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'email' => 'required'
       ]);

       if ($validator->fails()) {
           return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
       }

       $email = Email::find($request->forward_email_id);
       
       Mail::to($request->email)->send(new ForwardEmail($email, $email->message));

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

        if (!empty($remark)) {
            $remark_entry = EmailRemark::create([
                'email_id' => $email_id,
                'remarks' => $remark,
                'user_name' => Auth::user()->name
            ]);
        }

        return response()->json(['remark' => $remark], 200);
    }

    public function markAsRead($id){
        $email = Email::find($id);
        $email->seen = 1;
        $email->update();
        return response()->json(['success' => true, 'message' => 'Email has been read.']);
    }

    public function getAllEmails(){
            $available_models = ["supplier" =>\App\Supplier::class,"vendor"=>\App\Vendor::class,
                             "customer"=>\App\Customer::class,"users"=>\App\User::class];
            $email_list = [];
            foreach ($available_models as $key => $value) {
                $email_list = array_merge($email_list, $value::whereNotNull('email')->pluck('email')->unique()->all());
            }
        // dd($email_list);
        return array_values(array_unique($email_list));
    }
	
	
	public function category(Request $request){
		$values = array('category_name' => $request->input('category_name'));
		DB::table('email_category')->insert($values);
		
		session()->flash('success', 'Category added successfully');
		return redirect('email');

	}
	
	public function status(Request $request){
		$email_id = $request->input('status');
		$values = array('email_status' => $request->input('email_status'));
		DB::table('email_status')->insert($values);
		
		session()->flash('success', 'Status added successfully');
		return redirect('email');
		
	}
	
	
	public function updateEmail(Request $request){
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

    public function excelImporter(Request $request)
    {
        $id = $request->id;

        $email = Email::find($id);

        $body = $email->message;

        //check for wetransfer link

        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $body, $match);

        if(isset($match[0])){
            $matches = $match[0];
            foreach ($matches as $matchLink) {
                if((strpos($matchLink, 'wetransfer.com') !== false || strpos($matchLink, 'we.') !== false) && strpos($matchLink, 'google.com') !== true ){
                    //check if wetransfer already exist
                    $checkIfExist = Wetransfer::where('url',$matchLink)->where('supplier',$request->supplier)->first();
                    if(!$checkIfExist){
                        $wetransfer = new Wetransfer();
                        $wetransfer->type = 'excel';
                        $wetransfer->url = $matchLink;
                        $wetransfer->supplier = $request->supplier;
                        $wetransfer->save();
                    }
                }
            }
        }

        //getting from attachments

        $attachments = $email->additional_data;
        if($attachments){
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
                        ErpExcelImporter::excelFileProcess($attach, $excel,'');
                    }
                } elseif ($extension == 'zip') {
                    if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {
                        $excel = $request->supplier;
                        $attachments_array = [];
                        $attachments       = ErpExcelImporter::excelZipProcess('', $attach, $excel, '', $attachments_array);
                        
                    }
                }
            }


        }

        
        return response()->json(['message' => 'Successfully Imported'], 200);

    }
}
