<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Email;
use Webklex\IMAP\Client;
use App\Mails\Manual\PurchaseEmail;
use Mail;
use App\Mails\Manual\ReplyToEmail;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $term = $request->term ?? '';
        $date = $request->date ?? '';
        $type = $request->type ?? '';
        $seen = $request->seen ?? '';
        $query = (new Email())->newQuery();

        if($type) {
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


            // $query = $query->where('from','like','%'.$term.'%')
            // ->orWhere('to','like','%'.$term.'%')
            // ->orWhere('subject','like','%'.$term.'%')
            // ->orWhere('message','like','%'.$term.'%');
        }
        if($seen){
            if($seen != 'both'){
                $query = $query->where('seen',$seen);
            }
        }

        $emails = $query->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('emails.search', compact('emails','date','term','type'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string)$emails->render(),
                'count' => $emails->total(),
            ], 200);
        }


        // if($request->AJAX()) {
        //     return view('emails.search',compact('emails'));
        // }

        // dont load any data, data will be loaded by tabs based on ajax
        // return view('emails.index',compact('emails','date','term','type'))->with('i', ($request->input('page', 1) - 1) * 5);
        return view('emails.index',['emails'=>[]])->with('i', ($request->input('page', 1) - 1) * 5);

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
        //
    }

    public function resendMail($id) {
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
        return response()->json(['message' => 'Mail resent successfully']);
   }

   public function replyMail($id) {
    $email = Email::find($id);
    return view('emails.reply-modal',compact('email'));
    }

    public function forwardMail($id) {
        $email = Email::find($id);
        return view('emails.forward-modal',compact('email'));
        }

    public function remarkMail($id) {
        $email = Email::find($id);
        return view('emails.remark-modal',compact('email'));
        }

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

   public function submitForward(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'email' => 'required'
       ]);

       if ($validator->fails()) {
           return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
       }

       $email = Email::find($request->forward_email_id);
       Mail::send(new ForwardEmail($request->email, $email->message));

       return response()->json(['success' => true, 'message' => 'Email has been successfully sent.']);
   }

   public function submitRemark(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'message' => 'required'
       ]);

       if ($validator->fails()) {
           return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
       }

       $email = Email::find($request->remark_email_id);
       $email->remarks = $request->message;
       $email->update();

       return response()->json(['success' => true, 'message' => 'Remark has been successfully updated.']);
   }


}
