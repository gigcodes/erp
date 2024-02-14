<?php

namespace App\Http\Controllers\Api\v1;

use Exception;
use Throwable;
use App\Tickets;
use Carbon\Carbon;
use App\ChatMessage;
use Illuminate\Http\Request;
use App\Models\TicketsImages;
use App\Mails\Manual\TicketCreate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @SWG\Post(
     *   path="/ticket/create",
     *   tags={"Ticket"},
     *   summary="create ticket",
     *   operationId="create-ticket",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:80',
            'last_name' => 'required|max:80',
            'email' => 'required|email',
            'type_of_inquiry' => 'required',
            'subject' => 'required|max:80',
            'message' => 'required',
            'source_of_ticket' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $this->generate_erp_response('ticket.failed.validation', 0, $default = 'Please check the errors in validation!', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }
        if (isset($request->notify_on) && $request->notify_on != 'email' && $request->notify_on != 'phone') {
            $message = $this->generate_erp_response('ticket.failed.email_or_phone', 0, $default = 'notify_on field must be either email or phone!', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }

        $data = $request->all();
        $data['ticket_id'] = 'T' . date('YmdHis');
        $data['status_id'] = 1;
        $data['resolution_date'] = Carbon::now()->addDays(2)->format('Y-m-d H:i:s');
        if (isset($request->lang_code) && $request->lang_code != '') {
            $lang = explode('_', str_replace('-', '_', $request->lang_code));
            $data['lang_code'] = $lang[1];
        }

        $success = Tickets::create($data);
        $ticket = Tickets::find($success->id);
        $emailClass = (new TicketCreate($ticket))->build();

        try {
            if ($request->file('images') && is_array($request->file('images'))) {
                $directoryPath = public_path('images/tickets');
                if (! File::isDirectory($directoryPath)) {
                    File::makeDirectory($directoryPath, 0777, true, true);
                }
                foreach ($request->file('images') as $image) {
                    $img = new TicketsImages();
                    $img->setTicketId((int) $success->id);
                    $img->setFile($image);
                    $img->save();
                }
            }
        } catch (Exception|Throwable $e) {
        }

        $email = \App\Email::create([
            'model_id' => $ticket->id,
            'model_type' => Tickets::class,
            'from' => $emailClass->fromMailer,
            'to' => @$ticket->email,
            'subject' => $emailClass->subject,
            'message' => '', //$emailClass->render()
            'template' => 'ticket-create',
            'additional_data' => $ticket->id,
            'status' => 'pre-send',
            'is_draft' => 1,
        ]);
        \App\EmailLog::create([
            'email_id' => $email->id,
            'email_log' => 'Email initiated',
            'message' => $email->to,
        ]);
        \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');

        if (! is_null($success)) {
            $this->checkMessageAndSendReply($ticket->id);
            $message = $this->generate_erp_response('ticket.success', 0, $default = 'Ticket #' . $data['ticket_id'] . ' created successfully', request('lang_code'));

            return response()->json(['status' => 'success', 'data' => ['id' => $data['ticket_id']], 'message' => $message], 200);
        }
        $message = $this->generate_erp_response('ticket.failed', 0, $default = 'Unable to create ticket', request('lang_code'));

        return response()->json(['status' => 'error', 'message' => $message], 500);
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
        //
    }

    /**
     * @SWG\Post(
     *   path="/ticket/send",
     *   tags={"Ticket"},
     *   summary="Send ticket to customers",
     *   operationId="send-ticket-to-customer",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function sendTicketsToCustomers(request $request)
    {
        $Validator = Validator::make($request->all(), [
            'website' => 'required',
        ]);
        if ($Validator->fails()) {
            $message = $this->generate_erp_response('ticket.send.failed.validation', 0, $default = 'Please check validation errors !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $Validator->errors()], 400);
        }
        if (empty($request->email) && empty($request->ticket_id)) {
            $message = $this->generate_erp_response('ticket.send.failed.ticket_or_email', 0, $default = 'Please input either email or ticket_id !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message], 400);
        }
        $tickets = Tickets::select('tickets.*', 'ts.name as status')->where('source_of_ticket', $request->website);
        if ($request->email != null) {
            $tickets->where('email', $request->email);
        }
        if ($request->ticket_id != null) {
            $tickets->where('ticket_id', $request->ticket_id);
        }
        if (isset($request->action) && $request->action == 'send_messsage') {
            $ticket = Tickets::where('source_of_ticket', $request->website)->where('ticket_id', $request->ticket_id)->first();
            $params['message'] = $request->get('message');
            $params['message_en'] = $request->get('message');
            $params['ticket_id'] = $ticket->id;
            $params['user_id'] = 6;
            $params['approved'] = 1;
            $params['status'] = 2;
            $chat_message = ChatMessage::create($params);
        }
        $per_page = '';
        if (! empty($request->per_page)) {
            $per_page = $request->per_page;
        }
        $tickets = $tickets->join('ticket_statuses as ts', 'ts.id', 'tickets.status_id')->paginate($per_page);
        if (empty($tickets)) {
            $message = $this->generate_erp_response('ticket.send.failed', 0, $default = 'Tickets not found for customer !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message], 404);
        }
        foreach ($tickets as $ticket) {
            $replies = [];
            $messages = \App\ChatMessage::where('ticket_id', $ticket->id)->select('id', 'message', 'created_at', 'user_id')->latest()->get();
            foreach ($messages as $message) {
                $message->send_by = 'Admin';
                if ($message->user_id != '') {
                    $userData = \App\User::where('id', $message->user_id)->first();
                    if ($userData) {
                        $message->send_by = ($userData->screen_name != '') ? $userData->screen_name : $userData->name;
                    }
                }
                if ($message->user_id == 6) {
                    $message->send_by = 'Customer';
                }
                $replies[] = $message;
            }
            $ticket->messages = $replies;
        }

        return response()->json(['status' => 'success', 'tickets' => $tickets], 200);
    }

    /*Get message reply for ticket from database of Watson */
    public function checkMessageAndSendReply($ticker_id)
    {
        $get_ticket_data = Tickets::where(['id' => $ticker_id])->first();
        if (! empty($get_ticket_data)) {
            $customer = \App\Customer::where('email', $get_ticket_data->email)->first();

            $params = [
                'number' => $customer->phone,
                'message' => $get_ticket_data->message,
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
                'ticket_id' => $ticker_id,
            ];
            $messageModel = \App\ChatMessage::create($params);

            \App\Helpers\MessageHelper::sendwatson($customer, $get_ticket_data->message, null, $messageModel, $params);
        }

        return true;
    }
}
