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
        $start = $request->range_start ? $request->range_start : Carbon::now()->startOfWeek();
        $end = $request->range_end ? $request->range_end : Carbon::now()->endOfWeek();
        $selectedUser = $request->user_id ? $request->user_id : null;
        $tasks = PaymentReceipt::where('status','Pending');
        if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM')) {
            if ($request->user_id != null && $request->user_id != "") {
                $tasks = $tasks->where('user_id', $request->user_id)->where('billing_start_date', '>=' , $start)->where('billing_end_date', '<=' , $end);
            } else {
                $tasks = $tasks->where('billing_start_date', '>=' , $start)->where('billing_end_date', '<=' , $end);
            }
        } else {
            $tasks = $tasks->where('user_id', Auth::id())->where('billing_start_date', '>=' , $start)->where('billing_end_date', '<=' , $end);
        }

        $tasks = $tasks->paginate(10)->appends(request()->except('page'));
        foreach($tasks as $task) {
            $task->user;
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
            'user_id' => 'required',
            'billing_start_date' => 'required',
            'billing_end_date' => 'required',
            'rate_estimated' => 'required'
        ]);

        $input = $request->except('_token');
        $input['status'] = 'Pending';
        PaymentReceipt::create($input);
        return redirect()->back()->with('success','Successfully created');
    }
}
