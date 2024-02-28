<?php

namespace App\Http\Controllers;

use Auth;
use Storage;
use App\File;
use App\User;
use App\Helpers;
use App\Setting;
use App\CashFlow;
use App\Purchase;
use Carbon\Carbon;
use App\StoreWebsite;
use App\MonetaryAccount;
use Illuminate\Http\Request;
use App\Loggers\HubstuffCommandLog;
use App\ReadOnly\CashFlowCategories;
use Illuminate\Support\Facades\Validator;
use App\Loggers\HubstuffCommandLogMessage;

class CashFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date_fornightly = Carbon::now()->format('d');

        $cash_flow = CashFlow::with(['user', 'files', 'website']);
        $website_name = StoreWebsite::get();

        if ($request->site_name != '') {
            $cash_flow->join('store_website_orders', 'cash_flows.cash_flow_able_id', 'store_website_orders.order_id')->join('store_websites', 'store_websites.id', 'store_website_orders.website_id')->whereIn('store_websites.website', $request->site_name);
        }

        if ($request->type != '') {
            $cash_flow->where('type', $request->type);
        }
        if ($request->module_type != '') {
            if ($request->module_type == 'order') {
                $cash_flow->where('cash_flow_able_type', \App\Order::class);
                if ($request->b_name != '') {
                    $cash_flow->join('orders', 'cash_flows.cash_flow_able_id', 'orders.id');
                    $cash_flow->join('customers', 'orders.customer_id', 'customers.id');
                    $cash_flow->where('name', 'like', "%$request->b_name%");
                }
            }
            if ($request->module_type == 'payment_receipt') {
                $cash_flow->where('cash_flow_able_type', \App\PaymentReceipt::class);
                if ($request->b_name != '') {
                    $cash_flow->join('payment_receipts', 'cash_flows.cash_flow_able_id', 'assets_manager.id');
                    $cash_flow->where('remarks', 'like', "%$request->b_name%");
                }
            }
            if ($request->module_type == 'assent_manager') {
                $cash_flow->where('cash_flow_able_type', \App\AssetsManager::class);
                if ($request->b_name != '') {
                    $cash_flow->join('assets_manager', 'cash_flows.cash_flow_able_id', 'assets_manager.id');
                    $cash_flow->where('name', 'like', "%$request->b_name%");
                }
            }
            if ($request->module_type == 'vendor_frequency') {
                $cash_flow->where('cash_flow_able_type', \App\HubstaffActivityByPaymentFrequency::class);
                if ($request->b_name != '') {
                }
            }
        }
        if ($request->hidden_daterange != '') {
            $date = explode('-', $request->hidden_daterange);
            $datefrom = date('Y-m-d', strtotime($date[0]));
            $dateto = date('Y-m-d', strtotime($date[1]));
            $cash_flow->whereRaw("date(date) between date('$datefrom') and date('$dateto')");
        }

        $cash_flows = $cash_flow->orderBy('date', 'desc')->orderBy('cash_flows.id', 'desc')->paginate(Setting::get('pagination'));

        $users = User::select(['id', 'name', 'email'])->get();
        $categories = (new CashFlowCategories)->all();
        $purchases = Purchase::with('products')->select(['id', 'created_at'])->orderBy('created_at', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'purchase-page');
        if ($request->ajax()) {
            return view('cashflows.index_page', [
                'cash_flows' => $cash_flows,
                'users' => $users,
                'website_name' => $website_name,
                'categories' => $categories,
                'purchases' => $purchases,
            ]);
        } else {
            return view('cashflows.index', [
                'cash_flows' => $cash_flows,
                'users' => $users,
                'website_name' => $website_name,
                'categories' => $categories,
                'purchases' => $purchases,
            ]);
        }
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
        $this->validate($request, [
            'cash_flow_category_id' => 'sometimes|nullable|integer',
            'description' => 'sometimes|nullable|string',
            'date' => 'required',
            'amount' => 'required|integer',
            'type' => 'required|string',
        ]);

        $data = $request->except(['_token', 'file']);
        $data['user_id'] = Auth::id();

        $cash_flow = CashFlow::create($data);

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $filename = $file->getClientOriginalName();

                $file->storeAs('files', $filename, 'uploads');

                $new_file = new File;
                $new_file->filename = $filename;
                $new_file->model_id = $cash_flow->id;
                $new_file->model_type = CashFlow::class;
                $new_file->save();
            }
        }

        return redirect()->route('cashflow.index')->withSuccess('You have successfully added a record!');
    }

    public function download($id)
    {
        $file = File::find($id);

        return Storage::disk('s3')->download('files/' . $file->filename);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
        $cash_flow = CashFlow::find($id);

        if ($cash_flow->files) {
            foreach ($cash_flow->files as $file) {
                Storage::disk('s3')->delete("files/$file->filename");
                $file->delete();
            }
        }

        $cash_flow->delete();

        return redirect()->route('cashflow.index')->withSuccess('You have successfully deleted a record!');
    }

    public function masterCashFlow(CashFlow $cashFlow, MonetaryAccount $account, Request $request)
    {
        $cash_flows = $cashFlow;
        $capitals = $account;
        $data['start_date'] = date('Y-m-d');
        $data['end_date'] = date('Y-m-d');
        $range_start = $request->get('range_start');
        $range_end = $request->get('range_end');

        $dates = [date('Y-m-d')];
        if ($range_start != '' && $range_end != '') {
            $cash_flows = $cash_flows->whereBetween('date', [$range_start . ' 00:00', $range_end . ' 23:59']);
            $added_capitals_in_between = MonetaryAccount::whereBetween('date', [$range_start . ' 00:00', $range_end . ' 23:59'])->get();
            $data['start_date'] = $range_start;
            $data['end_date'] = $range_end;
        }

        if (! $range_start || ! $range_end) {
            $cash_flows = $cash_flows->where('date', date('Y-m-d'));
            $added_capitals_in_between = MonetaryAccount::where('date', date('Y-m-d'))->get();
        }
        $capitals = $capitals->where('date', '<', $data['start_date'] . ' 00:00')->get();
        $currencies = Helpers::currencies();
        $opening_balance = [
            'total' => 0,
        ];
        foreach ($currencies as $currency_id => $currency) {
            $opening_balance[$currency] = 0;
        }
        foreach ($capitals as $capital) {
            $opening_balance['total'] += $capital->amount;
            foreach ($currencies as $currency_id => $currency) {
                if (array_key_exists($currency, $opening_balance)) {
                    $opening_balance[$currency] += $capital->currency == $currency_id ? $capital->amount : 0;
                } else {
                    $opening_balance[$currency] = $capital->currency == $currency_id ? $capital->amount : 0;
                }
            }
        }
        $data['currencies'] = $currencies;
        $data['opening_balance'] = $opening_balance;
        $data['added_capitals_in_between'] = $added_capitals_in_between;
        $data['transactions'] = collect($cash_flows->orderBy('date')->orderBy('type', 'desc')->orderBy('cash_flow_able_type')->get()->toArray());

        return view('cashflows.master', $data);
    }

    public function doPayment(Request $request)
    {
        $id = $request->get('cash_flow_id', 0);

        $validator = Validator::make($request->all(), [
            'cash_flow_id' => 'required',
            'description' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 401, 'data' => $validator->errors(), 'message' => 'Please fix validation errors']);
        }

        if ($id > 0) {
            $cashflow = \App\CashFlow::find($id);
            if ($cashflow) {
                $cashflow->erp_amount = $request->amount;
                $cashflow->type = $request->type;
                $cashflow->monetary_account_id = $request->monetary_account_id;
                $cashflow->updated_by = auth()->user()->id;
                $cashflow->status = 1;
                if ($cashflow->erp_amount > 0) {
                    $cashflow->erp_eur_amount = \App\Currency::convert($cashflow->erp_amount, 'EUR', $cashflow->currency);
                }

                $cashflow->save();
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Receipt Created successfully']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Cashflow requested id is not found']);
    }

    public function getPaymentDetails(Request $request)
    {
        $cashflow_id = $request->id;
        $cash_flow = CashFlow::find($cashflow_id);

        $payment_receipts = [];
        if ($cash_flow) {
            $hubstaffActivityByPaymentFrequency = \App\HubstaffActivityByPaymentFrequency::where('id', $cash_flow->cash_flow_able_id)->first();

            if ($hubstaffActivityByPaymentFrequency) {
                $payment_receipts = json_decode($hubstaffActivityByPaymentFrequency->payment_receipt_ids);
                if (! empty($payment_receipts)) {
                    $tasks = \App\PaymentReceipt::with('chat_messages', 'user')->whereIn('id', $payment_receipts)->get();

                    return view('cashflows.payment_receipts', compact('tasks'));
                }
            }
        }
    }

    public function getBnameList(Request $request)
    {
        $model_type = $request->model_type;
        if ($model_type == 'order') {
            $model_type = \App\Customer::class;
            $rs = $model_type::get();
            $data = '';
            foreach ($rs as $r) {
                $arr['name'] = $r->name;

                $data = $arr;
            }

            return response()->json($data);
        }
        if ($model_type == 'assent_manager') {
            $model_type = \App\AssetsManager::class;
            $rs = $model_type::get();
            $data = '';
            foreach ($rs as $r) {
                $arr['name'] = $r->name;

                $data = $arr;
            }

            return response()->json($data);
        }

        if ($model_type == 'payment_receipt') {
            $model_type = \App\PaymentReceipt::class;
            $rs = $model_type::get();
            $data = '';
            foreach ($rs as $r) {
                $arr['name'] = $r->remarks;

                $data = $arr;
            }

            return response()->json($data);
        }
    }

    public function hubstuffCommandLog(Request $request)
    {
        if ($request->created_at) {
            $query = HubstuffCommandLog::orderby('created_at', 'desc');

            if (request('created_at') != null) {
                $query->whereDate('created_at', request('created_at'));
            }

            $paginate = (Setting::get('pagination') * 10);
            $logs = $query->paginate($paginate)->appends(request()->except(['page']));
        } else {
            $paginate = (Setting::get('pagination') * 10);
            $logs = HubstuffCommandLog::orderby('created_at', 'desc')->paginate($paginate);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('cashflows.hubstuff_command_log_data', compact('logs'))->render(),
                'links' => (string) $logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

        return view('cashflows.hubstuff_command_log', compact('logs'));
    }

    public function hubstuffCommandLogDetail(Request $request)
    {
        $messageLogs = [];
        $users = \App\User::pluck('name', 'id');
        if (isset($request->id) and $request->id != 0) {
            $messageLogs = HubstuffCommandLogMessage::where('hubstuff_command_log_id', $request->id)->get();
        }

        return view('cashflows.hubstuff_command_log_detail_data', compact('messageLogs', 'users'));
    }
}
