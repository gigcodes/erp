<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\CustomerImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Customer;
use App\Setting;
use App\Leads;
use App\Order;
use App\Status;
use App\Brand;
use App\User;
use App\ChatMessage;
use App\MessageQueue;
use App\Message;
use App\Helpers;
use App\Reply;
use App\Instruction;
use App\ReplyCategory;
use App\CallRecording;
use App\InstructionCategory;
use App\OrderStatus as OrderStatuses;
use App\ReadOnly\PurchaseStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $customers = $this->getCustomersIndex($request);
      $term = $request->input('term');

      $orderby = 'desc';
      if($request->orderby == '') {
         $orderby = 'asc';
      }

      $customers_all = Customer::all();
      $users_array = Helpers::getUserArray(User::all());

      $last_set_id = MessageQueue::max('group_id');
      // $queues_total_count = MessageQueue::where('status', '!=', 1)->where('sending_time', 'LIKE', "%" . Carbon::now()->format('Y-m-d') . "%")->count();
      // $queues_sent_count = MessageQueue::where('sent', 1)->where('status', '!=', 1)->where('sending_time', 'LIKE', "%" . Carbon::now()->format('Y-m-d') . "%")->count();
      $queues_total_count = MessageQueue::where('status', '!=', 1)->where('group_id', $last_set_id)->count();
      $queues_sent_count = MessageQueue::where('sent', 1)->where('status', '!=', 1)->where('group_id', $last_set_id)->count();

      return view('customers.index', [
        'customers' => $customers,
        'customers_all' => $customers_all,
        'users_array' => $users_array,
        'term' => $term,
        'orderby' => $orderby,
        'queues_total_count' => $queues_total_count,
        'queues_sent_count' => $queues_sent_count,
      ]);
    }

    public function getCustomersIndex(Request $request) {
        $term = $request->input('term');
        $customers = DB::table('customers');

        $orderWhereClause = '';

        if(!empty($term)) {
            $customers = $customers->latest()
                ->orWhere('customers.name', 'LIKE', "%$term%")
                ->orWhere('customers.phone', 'LIKE', "%$term%")
                ->orWhere('customers.instahandler', 'LIKE', "%$term%");

            $orderWhereClause = "WHERE orders.order_id LIKE '%$term%'";
        }



        $customers = $customers->join(DB::raw('(SELECT MAX(id) as order_id, orders.customer_id as ocid, MAX(orders.created_at) as order_created, orders.order_status as order_status FROM `orders` '. $orderWhereClause .' GROUP BY customer_id) as orders'), 'customers.id', '=', 'orders.ocid', 'LEFT');
        $customers = $customers->join(DB::raw('(SELECT MAX(id) as lead_id, leads.customer_id as lcid, leads.rating as rating, MAX(leads.created_at) as lead_created, leads.status as lead_status FROM `leads` GROUP BY customer_id) as leads'), 'customers.id', '=', 'leads.lcid', 'LEFT');
//        $customers = $customers->leftJoin(DB::raw('(SELECT * FROM (SELECT instructions.id, instructions.instruction, instructions.pending, instructions.verified, instructions.assigned_to, instructions.completed_at, MAX(instructions.created_at) as created_at, instructions.customer_id FROM `instructions` GROUP BY customer_id) as latest_instructions) as final_instructions INNER JOIN instructions ON instructions.customer_id = final_instructions.customer_id AND instructions.created_at = final_instructions.created_at'), function($join) {
//          $join->on('customers.id', '=', 'final_instructions.customer_id');
//          // $join->on('instructions.created_at', '=', 'instructions.instruction_created');
//        });

        $orderby = 'DESC';

        if($request->input('orderby')) {
            $orderby = 'ASC';
        }

        $sortby = 'communication';

        $sortBys = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'instagram' => 'instahandler',
            'lead_created' => 'lead_created',
            'order_created' => 'order_created',
            'rating' => 'rating',
            'communication' => 'communication',
        ];

        if (isset($sortBys[$request->input('sortby')])) {
            $sortby = $sortBys[$request->input('sortby')];
        }

        if ($sortby !== 'communication') {
            $customers = $customers->orderBy($sortby, $orderby);
        }


        $customers = $customers->join(DB::raw('(SELECT MAX(id) as chat_message_id, chat_messages.customer_id as cmcid, MAX(chat_messages.created_at) as chat_message_created_at FROM chat_messages WHERE chat_messages.status != 7 AND chat_messages.status != 8 GROUP BY chat_messages.customer_id ORDER BY chat_messages.created_at ' . $orderby . ') as chat_messages'), 'chat_messages.cmcid', '=', 'customers.id', 'LEFT');
        $customers = $customers->join(DB::raw('(SELECT MAX(id) as message_id, messages.customer_id as mcid, MAX(messages.created_at) as message_created_at FROM messages GROUP BY messages.customer_id ORDER BY messages.created_at ' . $orderby . ') as messages'), 'messages.mcid', '=', 'customers.id', 'LEFT');

        if ($sortby === 'communication') {
            $customers = $customers->orderBy('last_communicated_at', $orderby);
        }

//        $customers = $customers->selectRaw('customers.id, customers.name, orders.order_id, leads.lead_id, orders.order_created as order_created, orders.order_status as order_status, leads.lead_status as lead_status, leads.lead_created as lead_created, leads.rating as rating, instructions.id as instruction_id, instructions.pending as instruction_pending, instructions.verified as instruction_verified, instructions.instruction, instructions.created_at, instructions.completed_at as instruction_completed, instructions.assigned_to as instruction_assigned_to, CASE WHEN messages.message_created_at > chat_messages.chat_message_created_at THEN messages.message_created_at ELSE chat_messages.chat_message_created_at END AS last_communicated_at,
        $customers = $customers->selectRaw('customers.id, customers.name, orders.order_id, leads.lead_id, orders.order_created as order_created, orders.order_status as order_status, leads.lead_status as lead_status, leads.lead_created as lead_created, leads.rating as rating, CASE WHEN messages.message_created_at > chat_messages.chat_message_created_at THEN messages.message_created_at ELSE chat_messages.chat_message_created_at END AS last_communicated_at,
        CASE WHEN messages.message_created_at > chat_messages.chat_message_created_at THEN (SELECT mmm.body FROM messages mmm WHERE mmm.id = message_id) ELSE (SELECT mm2.message FROM chat_messages mm2 WHERE mm2.id = chat_message_id) END AS message,
        CASE WHEN messages.message_created_at > chat_messages.chat_message_created_at THEN (SELECT mm3.status FROM messages mm3 WHERE mm3.id = message_id) ELSE (SELECT mm4.status FROM chat_messages mm4 WHERE mm4.id = chat_message_id) END AS message_status,
        CASE WHEN messages.message_created_at > chat_messages.chat_message_created_at THEN (SELECT mm5.id FROM messages mm5 WHERE mm5.id = message_id) ELSE (SELECT mm6.id FROM chat_messages mm6 WHERE mm6.id = chat_message_id) END AS message_id,
        CASE WHEN messages.message_created_at > chat_messages.chat_message_created_at THEN (SELECT mm7.moduletype FROM messages mm7 WHERE mm7.id = message_id) ELSE (SELECT mm8.sent FROM chat_messages mm8 WHERE mm8.id = chat_message_id) END AS message_type')->paginate(24);

        return $customers;
    }

    public function load(Request $request)
    {
        $first_customer = Customer::find($request->first_customer);
        $second_customer = Customer::find($request->second_customer);

        return response()->json([
            'first_customer'  => $first_customer,
            'second_customer'  => $second_customer
        ]);
    }

    public function merge(Request $request)
    {
        $this->validate($request, [
            'name'          => 'required|min:3|max:255',
            'email'         => 'required_without_all:phone,instahandler|nullable|email',
            'phone'         => 'required_without_all:email,instahandler|nullable|numeric|regex:/^[91]{2}/|digits:12|unique:customers,phone,' . $request->first_customer_id,
            'instahandler'  => 'required_without_all:email,phone|nullable|min:3|max:255',
            'rating'        => 'required|numeric',
            'address'       => 'sometimes|nullable|min:3|max:255',
            'city'          => 'sometimes|nullable|min:3|max:255',
            'country'       => 'sometimes|nullable|min:3|max:255',
            'pincode'       => 'sometimes|nullable|max:6'
        ]);

        $first_customer = Customer::find($request->first_customer_id);

        $first_customer->name = $request->name;
        $first_customer->email = $request->email;
        $first_customer->phone = $request->phone;
        $first_customer->whatsapp_number = $request->whatsapp_number;
        $first_customer->instahandler = $request->instahandler;
        $first_customer->rating = $request->rating;
        $first_customer->address = $request->address;
        $first_customer->city = $request->city;
        $first_customer->country = $request->country;
        $first_customer->pincode = $request->pincode;

        $first_customer->save();

        $chat_messages = ChatMessage::where('customer_id', $request->second_customer_id)->get();

        foreach ($chat_messages as $chat) {
          $chat->customer_id = $first_customer->id;
          $chat->save();
        }

        $messages = Message::where('customer_id', $request->second_customer_id)->get();

        foreach ($messages as $message) {
            $message->customer_id = $first_customer->id;
            $message->save();
        }

        $leads = Leads::where('customer_id', $request->second_customer_id)->get();

        foreach ($leads as $lead) {
            $lead->customer_id = $first_customer->id;
            $lead->save();
        }

        $orders = Order::where('customer_id', $request->second_customer_id)->get();

        foreach ($orders as $order) {
            $order->customer_id = $first_customer->id;
            $order->save();
        }

        $instructions = Instruction::where('customer_id', $request->second_customer_id)->get();

        foreach ($instructions as $instruction) {
            $instruction->customer_id = $first_customer->id;
            $instruction->save();
        }

        $second_customer = Customer::find($request->second_customer_id);
        $second_customer->delete();

        return redirect()->route('customer.index');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file'  => 'required|mimes:xls,xlsx'
        ]);

        (new CustomerImport)->queue($request->file('file'));

        return redirect()->back()->with('success', 'Customers are being imported in the background');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
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
            'name'          => 'required|min:3|max:255',
            'email'         => 'required_without_all:phone,instahandler|nullable|email',
            'phone'         => 'required_without_all:email,instahandler|nullable|numeric|regex:/^[91]{2}/|digits:12|unique:customers',
            'instahandler'  => 'required_without_all:email,phone|nullable|min:3|max:255',
            'rating'        => 'required|numeric',
            'address'       => 'sometimes|nullable|min:3|max:255',
            'city'          => 'sometimes|nullable|min:3|max:255',
            'country'       => 'sometimes|nullable|min:2|max:255',
            'pincode'       => 'sometimes|nullable|max:6',
        ]);

        $customer = new Customer;

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->whatsapp_number = $request->whatsapp_number;
        $customer->instahandler = $request->instahandler;
        $customer->rating = $request->rating;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->country = $request->country;
        $customer->pincode = $request->pincode;

        $customer->save();

        return redirect()->route('customer.index')->with('success', 'You have successfully added new customer!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::find($id);
        $customers = Customer::all();
        $call_history = [];

        if($customer->phone != ''){
            $phone_number = str_replace('+', '', $customer->phone);
            if (strlen($phone_number) > 10) {
                $customer_number = str_replace('91', '', $phone_number);
            }else{
                $customer_number = $phone_number;
            }

            $lead_ids = [];
            $order_ids = [];
            foreach ($customer->leads as $lead) {
                $lead_ids[] = $lead->id;
            }
            foreach ($customer->orders as $order) {
                $order_ids[] = $order->id;
            }

            // $call_history = CallRecording::where('customer_number','LIKE', "%$customer_number%")->orderBy('created_at', 'DESC')->get()->toArray();
            $call_history = CallRecording::whereIn('lead_id', $lead_ids)->orWhereIn('order_id', $order_ids)->orWhere('customer_id', $customer->id)->orderBy('created_at', 'DESC')->get()->toArray();
        }


        // $leads = Leads::find($id);
        $status = (New status)->all();
        $users = User::all()->toArray();
        $users_array = Helpers::getUserArray(User::all());
        $brands = Brand::all()->toArray();
        $reply_categories = ReplyCategory::all();
        $instruction_categories = InstructionCategory::all();
        $instruction_replies = Reply::where('model', 'Instruction')->get();
        $order_status_report = OrderStatuses::all();
        $purchase_status = (new PurchaseStatus)->all();

        return view('customers.show', [
            'customers'  => $customers,
            'customer'  => $customer,
            'status'    => $status,
            'brands'    => $brands,
            'users'     => $users,
            'users_array'     => $users_array,
            // 'approval_replies'     => $approval_replies,
            // 'internal_replies'     => $internal_replies,
            'reply_categories'  => $reply_categories,
            'call_history' =>  $call_history,
            'instruction_categories' =>  $instruction_categories,
            'instruction_replies' =>  $instruction_replies,
            'order_status_report' =>  $order_status_report,
            'purchase_status' =>  $purchase_status
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
        $customer = Customer::find($id);

        return view('customers.edit')->withCustomer($customer);
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
        $customer = Customer::find($id);

        $this->validate($request, [
            'name'          => 'required|min:3|max:255',
            'email'         => 'required_without_all:phone,instahandler|nullable|email',
            'phone'         => 'required_without_all:email,instahandler|nullable|regex:/^[91]{2}/|digits:12|unique:customers,phone,' . $id,
            'instahandler'  => 'required_without_all:email,phone|nullable|min:3|max:255',
            'rating'        => 'required|numeric',
            'address'       => 'sometimes|nullable|min:3|max:255',
            'city'          => 'sometimes|nullable|min:3|max:255',
            'country'       => 'sometimes|nullable|min:2|max:255',
            'pincode'       => 'sometimes|nullable|max:6',
        ]);

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->whatsapp_number = $request->whatsapp_number;
        $customer->instahandler = $request->instahandler;
        $customer->rating = $request->rating;
        $customer->do_not_disturb = $request->do_not_disturb == 'on' ? 1 : 0;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->country = $request->country;
        $customer->pincode = $request->pincode;

        $customer->save();

        if ($request->do_not_disturb == 'on') {
          $message_queues = MessageQueue::where('customer_id', $customer->id)->get();

          foreach ($message_queues as $message_queue) {
            $message_queue->status = 1; // message stopped
            $message_queue->save();
          }
        }

        return redirect()->route('customer.show', $id)->with('success', 'You have successfully updated the customer!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (count($customer->leads) > 0 || count($customer->orders) > 0) {
            return redirect()->route('customer.index')->with('warning', 'You have related leads or orders to this customer');
        }

        $customer->delete();

        return redirect()->route('customer.index')->with('success', 'You have successfully deleted a customer');
    }
}
