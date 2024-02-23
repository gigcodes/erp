<?php

namespace App\Http\Controllers;

use Auth;
use App\Task;
use App\Team;
use App\User;
use App\Payment;
use App\Voucher;
use App\Currency;
use App\ChatMessage;
use App\DeveloperTask;
use App\PaymentMethod;
use App\PaymentReceipt;
use App\VoucherCategory;
use Illuminate\Http\Request;
use App\Events\VoucherApproved;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->range_start ? $request->range_start : date('Y-m-d', strtotime('monday this week'));
        $end = $request->range_end ? $request->range_end : date('Y-m-d', strtotime('saturday this week'));
        $selectedUser = $request->user_id ? $request->user_id : null;
        $status = $request->status ? $request->status : 'Pending';
        $tasks = PaymentReceipt::with('chat_messages', 'user')->where('status', $status);
        $teammembers = Team::where(['teams.user_id' => Auth::user()->id])->join('team_user', 'team_user.team_id', '=', 'teams.id')->select(['team_user.user_id'])->get()->toArray();
        $teammembers[] = Auth::user()->id;

        $limit = request('limit');
        if (! empty($limit)) {
            if ($limit == 'all') {
                $limit = $tasks->count();
            }
        }

        $tasks = $tasks->orderBy('id', 'desc')->paginate($limit)->appends(request()->except('page'));

        foreach ($tasks as $task) {
            $task->user;

            $totalPaid = Payment::where('payment_receipt_id', $task->id)->sum('amount');
            if ($totalPaid) {
                $task->paid_amount = number_format($totalPaid, 2);
                $task->balance = $task->rate_estimated - $totalPaid;
                $task->balance = number_format($task->balance, 2);
            } else {
                $task->paid_amount = 0;
                $task->balance = $task->rate_estimated;
                $task->balance = number_format($task->balance, 2);
            }
            if ($task->task_id) {
                $task->taskdetails = Task::find($task->task_id);
                $task->estimate_minutes = 0;
                if ($task->taskdetails) {
                    $task->details = $task->taskdetails->task_details;
                    if ($task->worked_minutes == null) {
                        $task->estimate_minutes = $task->taskdetails->approximate;
                    } else {
                        $task->estimate_minutes = $task->worked_minutes;
                    }
                }
            } elseif ($task->developer_task_id) {
                $task->taskdetails = DeveloperTask::find($task->developer_task_id);
                $task->estimate_minutes = 0;
                if ($task->taskdetails) {
                    $task->details = $task->taskdetails->task;
                    if ($task->worked_minutes == null) {
                        $task->estimate_minutes = $task->taskdetails->estimate_minutes;
                    } else {
                        $task->estimate_minutes = $task->worked_minutes;
                    }
                }
            } else {
                $task->details = $task->remarks;
                $task->estimate_minutes = $task->worked_minutes;
            }
        }
        $users = User::all();

        return view('vouchers.index', [
            'tasks' => $tasks,
            'users' => $users,
            'user' => $request->user,
            'selectedUser' => $selectedUser,
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
            'number' => null,
            'user_id' => Auth::id(),
            'voucher_id' => $voucher->id,
            'message' => $voucher->description . ' ' . $voucher->amount,
        ];
        $message = ChatMessage::create($params);

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
            'title' => 'required_without:subcategory',
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = PaymentReceipt::where('id', $id)->first();

        return response()->json($task);

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->type == 'partial') {
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

        if ($request->type == 'partial') {
            return redirect()->back()->with('success', 'You have successfully updated cash voucher');
        }

        return redirect()->route('voucher.index')->with('success', 'You have successfully updated cash voucher');
    }

    public function approve(Request $request, $id)
    {
        $voucher = Voucher::find($id);

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Voucher::find($id)->delete();

        return redirect()->route('voucher.index')->with('success', 'You have successfully deleted a cash voucher');
    }

    public function userSearch()
    {
        $term = request()->get('q', null);
        $search = User::where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('email', 'LIKE', '%' . $term . '%')->get();

        return response()->json($search);
    }

    public function createPaymentRequest(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'currency' => 'required',
        ]);

        $input = $request->except('_token');
        $input['status'] = 'Pending';
        $input['rate_estimated'] = $input['amount'];
        PaymentReceipt::create($input);

        return redirect()->back()->with('success', 'Successfully created');
    }

    public function paymentRequest()
    {
        $users = User::all();

        return view('vouchers.payment-request', compact('users'));
    }

    public function viewPaymentModal($id)
    {
        $task = PaymentReceipt::find($id);
        if ($task->user_id) {
            $task->userName = User::find($task->user_id)->name;
        }
        $paymentMethods = PaymentMethod::all();
        $currencies = Currency::get();

        return view('vouchers.payment-modal', compact('task', 'paymentMethods', 'currencies'));
    }

    public function submitPayment($id, Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'payment_method_id' => 'required',
        ]);
        $preceipt = PaymentReceipt::find($id);

        if (! $preceipt) {
            return redirect()->back()->with('warning', 'Payment receipt not found');
        }
        $totalPaid = Payment::where('payment_receipt_id', $preceipt->id)->sum('amount');
        $newTotal = $totalPaid + $request->amount;

        if ($newTotal > $preceipt->rate_estimated) {
            return redirect()->back()->with('warning', 'Amount can not be greater than receipt amount');
        }

        $input = $request->except('_token');

        if (! is_numeric($input['payment_method_id'])) {
            $paymentMethod = PaymentMethod::where('name', $input['payment_method_id'])->first();
            if (! $paymentMethod) {
                $paymentMethod = PaymentMethod::create([
                    'name' => $input['payment_method_id'],
                ]);
                $input['payment_method_id'] = $paymentMethod->id;
            } else {
                $input['payment_method_id'] = $paymentMethod->id;
            }
        }

        $payment_method = PaymentMethod::find($input['payment_method_id']);
        $input['payment_receipt_id'] = $preceipt->id;
        $message['message'] = 'Admin has given the payment of Payment Receipt #' . $preceipt->id . ' and amount ' . $request->amount . ' ' . $request->currency . ' through ' . $payment_method->name . " \n Note: " . $request->note;
        $message['user_id'] = $request->user_id;
        $message['status'] = 1;

        Payment::create($input);
        $request1 = new \Illuminate\Http\Request();
        $request1->replace($message);

        $sendMessage = app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($request1, 'user');
        if ($newTotal >= $preceipt->rate_estimated) {
            $preceipt->update(['status' => 'Done']);
            $cashdata['order_status'] = 'Done';
            $cashdata['status'] = 1;
        }

        return redirect()->back()->with('success', 'Successfully submitted');
    }

    public function uploadDocuments(Request $request)
    {
        $path = storage_path('tmp/uploads');

        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function saveDocuments(Request $request)
    {
        $documents = $request->input('document', []);
        if (! empty($documents)) {
            $receipt = PaymentReceipt::find($request->id);

            foreach ($request->input('document', []) as $file) {
                $path = storage_path('tmp/uploads/' . $file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('voucher/' . floor($request->id / config('constants.image_per_folder')))
                    ->upload();
                $receipt->attachMedia($media, config('constants.media_tags'));
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Done!']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'No documents for upload']);
        }
    }

    public function listDocuments(Request $request, $id)
    {
        $receipt = PaymentReceipt::find($request->id);

        $userList = [];

        $records = [];
        if ($receipt) {
            if ($receipt->hasMedia(config('constants.media_tags'))) {
                foreach ($receipt->getMedia(config('constants.media_tags')) as $media) {
                    $records[] = [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'payment_receipt_id' => $request->id,
                    ];
                }
            }
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function deleteDocument(Request $request)
    {
        if ($request->id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            if ($media) {
                $media->delete();

                return response()->json(['code' => 200, 'message' => 'Document delete succesfully']);
            }
        }

        return response()->json(['code' => 500, 'message' => 'No document found']);
    }

    public function viewManualPaymentModal()
    {
        $users = User::all();
        $paymentMethods = PaymentMethod::all();
        $currencies = Currency::get();

        return view('vouchers.manual-payment-modal', compact('users', 'paymentMethods', 'currencies'));
    }

    public function manualPaymentSubmit(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'user_id' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'payment_method_id' => 'required',
        ]);
        $input = $request->except('_token');

        $input['status'] = 'Pending';
        $input['rate_estimated'] = $input['amount'];
        $input['remarks'] = $input['note'];
        $paymentReceipt = PaymentReceipt::create($input);

        $input['payment_receipt_id'] = $paymentReceipt->id;

        if (! is_numeric($input['payment_method_id'])) {
            $paymentMethod = PaymentMethod::where('name', $input['payment_method_id'])->first();
            if (! $paymentMethod) {
                $paymentMethod = PaymentMethod::create([
                    'name' => $input['payment_method_id'],
                ]);
                $input['payment_method_id'] = $paymentMethod->id;
            } else {
                $input['payment_method_id'] = $paymentMethod->id;
            }
        }

        Payment::create($input);

        return redirect()->back()->with('success', 'Successfully submitted');
    }

    public function paidSelected(Request $request)
    {
        $ids = ! empty($request->ids) ? $request->ids : [0];
        $paymentReceipt = \App\PaymentReceipt::whereIn('id', $ids)->get();

        $paymentMethods = PaymentMethod::all();
        $currencies = Currency::get();

        return view('vouchers.partials.modal-payment-receipt-paid', compact('paymentReceipt', 'currencies', 'paymentMethods'));
    }

    public function paidSelectedPaymentList(Request $request)
    {
        $payments = \App\Payment::where('payment_receipt_id', $request->payment_receipt_id)->get();

        return view('vouchers.partials.payment-receipt-list', compact('payments'));
    }

    public function payMultiple(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'amount.*' => 'required',
            'currency' => 'required',
            'payment_method_id' => 'required',
        ]);

        $input = $request->except('_token');

        if (! is_numeric($input['payment_method_id'])) {
            $paymentMethod = PaymentMethod::where('name', $input['payment_method_id'])->first();
            if (! $paymentMethod) {
                $paymentMethod = PaymentMethod::create([
                    'name' => $input['payment_method_id'],
                ]);
                $input['payment_method_id'] = $paymentMethod->id;
            } else {
                $input['payment_method_id'] = $paymentMethod->id;
            }
        }

        $payment_method = PaymentMethod::find($input['payment_method_id']);

        if (! empty($request->amount)) {
            foreach ($request->amount as $k => $amount) {
                $preceipt = PaymentReceipt::find($k);
                if ($preceipt) {
                    $totalPaid = Payment::where('payment_receipt_id', $preceipt->id)->sum('amount');
                    $newTotal = $totalPaid + $amount;

                    $input['payment_receipt_id'] = $preceipt->id;
                    $input['amount'] = $amount;
                    $message['message'] = 'Admin has given the payment of Payment Receipt #' . $preceipt->id . ' and amount ' . $amount . ' ' . $request->currency . ' through ' . $payment_method->name . " \n Note: " . $request->note;
                    $message['user_id'] = $preceipt->user_id;
                    $message['status'] = 1;

                    Payment::create($input);
                    $request1 = new \Illuminate\Http\Request();
                    $request1->replace($message);

                    $sendMessage = app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($request1, 'user');
                    $cashData = [
                        'user_id' => $preceipt->user_id,
                        'description' => 'Vendor paid',
                        'date' => $request->input('date'),
                        'amount' => $newTotal,
                        'type' => 'paid',
                        'cash_flow_able_type' => \App\PaymentReceipt::class,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_by' => \Auth::user()->id,
                    ];
                    if ($newTotal >= $preceipt->rate_estimated) {
                        $preceipt->update(['status' => 'Done']);
                        $cashdata['order_status'] = 'Done';
                        $cashdata['status'] = 1;
                    }
                    //create entry in table cash_flows
                    \DB::table('cash_flows')->insert($cashData);
                }
            }
        }

        return response()->json(['code' => 200, 'message' => 'Payment paid successfully']);
    }

    public function paymentHistory(request $request)
    {
        $task_id = $request->input('task_id');
        $html = '';
        $paymentData = \App\CashFlow::where('cash_flow_able_id', $task_id)
            ->where('cash_flow_able_type', \App\PaymentReceipt::class)
            ->where('type', 'paid')
            ->orderBy('date', 'DESC')
            ->get();
        $i = 1;
        if (count($paymentData) > 0) {
            foreach ($paymentData as $history) {
                $html .= '<tr>';
                $html .= '<td>' . $history->id . '</td>';
                $html .= '<td>' . $history->amount . '</td>';
                $html .= '<td>' . $history->date . '</td>';
                $html .= '<td>' . $history->description . '</td>';
                $html .= '</tr>';

                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }
}
