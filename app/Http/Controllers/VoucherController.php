<?php

namespace App\Http\Controllers;

use App\AutoReply;
use App\ChatMessage;
use App\Events\VoucherApproved;
use Illuminate\Http\Request;
use App\Voucher;
use App\VoucherCategory;
use App\Setting;
use App\Helpers;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use App\DeveloperTask;
use App\Task;
use App\PaymentReceipt;
use App\PaymentMethod;
use App\Payment;

class VoucherController extends Controller
{
    public function __construct()
    {
        //$this->middleware('permission:voucher');
    }

    public function index(Request $request)
    {
        // dd($request->all());
        // $start = $request->range_start ? $request->range_start : Carbon::now()->startOfWeek();
        // $end = $request->range_end ? $request->range_end : Carbon::now()->endOfWeek();
        $start = $request->range_start ? $request->range_start : date("Y-m-d", strtotime('monday this week'));
        $end = $request->range_end ? $request->range_end : date("Y-m-d", strtotime('saturday this week'));
        $selectedUser = $request->user_id ? $request->user_id : null;
        $tasks = PaymentReceipt::where('status','Pending');
        if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM')) {
            if ($request->user_id != null && $request->user_id != "") {
                $tasks = $tasks->where('user_id', $request->user_id)->where('date', '>=' , $start)->where('date', '<=' , $end);
            } else {
                $tasks = $tasks->where('date', '>=' , $start)->where('date', '<=' , $end);
            }
        } else {
            $tasks = $tasks->where('user_id', Auth::id())->where('date', '>=' , $start)->where('date', '<=' , $end);
        }

        $tasks = $tasks->orderBy('id','desc')->paginate(10)->appends(request()->except('page'));
        foreach($tasks as $task) {
            $task->user;

            $totalPaid = Payment::where('payment_receipt_id',$task->id)->sum('amount');
            if($totalPaid) {
                $task->paid_amount = number_format($totalPaid,2);
                $task->balance = $task->rate_estimated - $totalPaid; 
                $task->balance = number_format($task->balance,2);
            }
            else {
                $task->paid_amount = 0; 
                $task->balance = $task->rate_estimated;
                $task->balance = number_format($task->balance,2); 
            }
            // $task->assignedUser;
            if($task->task_id) {
                $task->taskdetails = Task::find($task->task_id);
                $task->details = $task->taskdetails->task_details;
                if(!$task->worked_minutes) {
                    $task->estimate_minutes = $task->taskdetails->approximate;
                }
            }
            else if($task->developer_task_id) {
                $task->taskdetails = DeveloperTask::find($task->developer_task_id);
                $task->details = $task->taskdetails->task;
                if(!$task->worked_minutes) {
                    $task->estimate_minutes = $task->taskdetails->estimate_minutes;
                }
            }
            else {
                $task->details = $task->remarks;
                $task->estimate_minutes = $task->worked_minutes;
            }  
        }


        // $vouchers = $vouchers->orderBy('date', 'DESC')->get();
        // dd($vouchers);
        //
        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // $perPage = Setting::get('pagination');
        // $currentItems = array_slice($vouchers, $perPage * ($currentPage - 1), $perPage);
        //
        // $vouchers = new LengthAwarePaginator($currentItems, count($vouchers), $perPage, $currentPage, [
        // 	'path'	=> LengthAwarePaginator::resolveCurrentPath()
        // ]);
        //
        // dd($vouchers);
        // paginate(Setting::get('pagination'));
        // $users_array = Helpers::getUserArray(User::all());
        $users = User::all();
        return view('vouchers.index', [
            'tasks' => $tasks,
            'users' => $users,
            'user' => $request->user,
            'selectedUser' => $selectedUser
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $voucher_categories = VoucherCategory::where('parent_id', 0)->get();
        $voucher_categories_dropdown = VoucherCategory::attr(['name' => 'category_id', 'class' => 'form-control', 'placeholder' => 'Select a Category'])
            ->renderAsDropdown();

        return view('vouchers.create', [
            'voucher_categories' => $voucher_categories,
            'voucher_categories_dropdown' => $voucher_categories_dropdown,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'description' => 'required|min:3',
            'travel_type' => 'sometimes|nullable|string',
            'amount' => 'sometimes|nullable|numeric',
            'paid' => 'sometimes|nullable|numeric',
            'date' => 'required|date',
        ]);

        $data = $request->except('_token');
        $data['user_id'] = Auth::id();

        $voucher = Voucher::create($data);
        //create chat message
        $params = [
            'number' => NULL,
            'user_id' => Auth::id(),
            'voucher_id' => $voucher->id,
            'message' => $voucher->description . ' ' .$voucher->amount
        ];
        $message = ChatMessage::create( $params );

        //TODO send message to admin yogesh for approval

        //TODO listen for whatsapp messages, identify the keywords and update the approval status accordingly
        if ($request->ajax()) {
            return response()->json(['id' => $voucher->id]);
        }

        return redirect()->route('voucher.index')->with('success', 'You have successfully created cash voucher');
    }

    public function storeCategory(Request $request)
    {
        $this->validate($request, [
            'title' => 'required_without:subcategory'
        ]);

        if ($request->title != '') {
            VoucherCategory::create(['title' => $request->title]);
        }

        if ($request->parent_id != '' && $request->subcategory != '') {
            VoucherCategory::create(['title' => $request->subcategory, 'parent_id' => $request->parent_id]);
        }


        return redirect()->back()->with('success', 'Category created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $voucher = Voucher::find($id);
        $voucher_categories = VoucherCategory::where('parent_id', 0)->get();
        $voucher_categories_dropdown = VoucherCategory::attr(['name' => 'category_id', 'class' => 'form-control', 'placeholder' => 'Select a Category'])
            ->selected($voucher->category_id)
            ->renderAsDropdown();

        return view('vouchers.edit', [
            'voucher' => $voucher,
            'voucher_categories' => $voucher_categories,
            'voucher_categories_dropdown' => $voucher_categories_dropdown,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->type == "partial") {
            $this->validate($request, [
                'travel_type' => 'sometimes|nullable|string',
                'amount' => 'sometimes|nullable|numeric',
            ]);
        } else {
            $this->validate($request, [
                'description' => 'required|min:3',
                'travel_type' => 'sometimes|nullable|string',
                'amount' => 'sometimes|nullable|numeric',
                'paid' => 'sometimes|nullable|numeric',
                'date' => 'required|date',
            ]);
        }

        $data = $request->except('_token');

        Voucher::find($id)->update($data);

        if ($request->type == "partial") {
            return redirect()->back()->with('success', 'You have successfully updated cash voucher');
        }

        return redirect()->route('voucher.index')->with('success', 'You have successfully updated cash voucher');
    }

    public function approve(Request $request, $id)
    {
        $voucher = Voucher::find($id);

        //
        /*if ($voucher->approved == 1) {
          $voucher->approved = 2;
        } else {
          $voucher->approved = 1;
        }*/

        $voucher->approved = 2;

        $voucher->save();
        event(new VoucherApproved($voucher));
        //TODO send message to user via whatsapp notifying that the voucher request has been approved.
        return redirect()->route('voucher.index')->withSuccess('Voucher Approved.');
    }

    public function reject(Request $request, $id)
    {
        $voucher = Voucher::find($id);

        $voucher->reject_reason = $request->get('reject_reason');
        $voucher->reject_count += 1;

        $voucher->save();
        //TODO send message to user via whatsapp notifying that the voucher request has been rejected.
        return redirect()->route('voucher.index')->withSuccess('You have successfully updated the voucher!');
    }

    public function resubmit(Request $request, $id)
    {
        $voucher = Voucher::find($id);
        $voucher->approved = 1;
        $voucher->resubmit_count += 1;

        $voucher->save();

        return redirect()->route('voucher.index')->withSuccess('You have successfully resubmitted the voucher!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Voucher::find($id)->delete();

        return redirect()->route('voucher.index')->with('success', 'You have successfully deleted a cash voucher');
    }


    public function userSearch()
    {
      $term = request()->get("q", null);
      $search = User::where('name', 'LIKE', "%" . $term . "%")
        ->orWhere('email', 'LIKE', "%" . $term . "%")->get();
      return response()->json($search);
    }


    public function createPaymentRequest(Request $request) {
        $this->validate($request, [
            'date' => 'required',
            'amount' => 'required',
            'currency' => 'required'
        ]);

        $input = $request->except('_token');
        $input['status'] = 'Pending';
        $input['rate_estimated'] = $input['amount'];
        PaymentReceipt::create($input);
        return redirect()->back()->with('success','Successfully created');
    }

    public function paymentRequest() {
        $users = User::all();
        return view("vouchers.payment-request",compact('users'));
    }

    public function viewPaymentModal($id) {
        $task = PaymentReceipt::find($id);
        if($task->user_id) {
            $task->userName = User::find($task->user_id)->name;
        }
        $paymentMethods = PaymentMethod::all();
        return view("vouchers.payment-modal",compact('task','paymentMethods'));
    }

    public function submitPayment($id, Request $request) {
        $this->validate($request, [
            'date' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'payment_method_id' => 'required'
        ]);
        $preceipt = PaymentReceipt::find($id);
        if(!$preceipt) {
            return redirect()->back()->with('warning','Payment receipt not found');
        }
        $totalPaid = Payment::where('payment_receipt_id',$preceipt->id)->sum('amount');
        $newTotal = $totalPaid + $request->amount;
        if($newTotal > $preceipt->rate_estimated) {
            return redirect()->back()->with('warning','Amount can not be greater than receipt amount');
        }
        $input = $request->except('_token');
        $input['payment_receipt_id'] = $preceipt->id;
       
        Payment::create($input);
        
      
        if($newTotal >= $preceipt->rate_estimated) {
            $preceipt->update(['status' => 'Done']);
        }
        return redirect()->back()->with('success','Successfully submitted');
    }


    public function viewManualPaymentModal() {
        $users = User::all(); 
        $paymentMethods = PaymentMethod::all();
        return view("vouchers.manual-payment-modal",compact('users','paymentMethods'));
    }


    public function manualPaymentSubmit(Request $request) {
        $this->validate($request, [
            'date' => 'required',
            'user_id' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'payment_method_id' => 'required'
        ]);
        $input = $request->except('_token');       
        Payment::create($input);
        return redirect()->back()->with('success','Successfully submitted');
    }
}
