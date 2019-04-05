<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CashFlow;
use App\Setting;
use App\User;
use App\File;
use App\Order;
use App\Purchase;
use App\Voucher;
use App\ReadOnly\CashFlowCategories;
use Auth;
use Storage;

class CashFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $cash_flows = CashFlow::with(['user', 'files'])->latest()->paginate(Setting::get('pagination'));
      $users = User::select(['id', 'name', 'email'])->get();
      $categories = (new CashFlowCategories)->all();
      $orders = Order::with('order_product')->select(['id', 'order_date', 'balance_amount'])->orderBy('order_date', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'order-page');
      $purchases = Purchase::with('products')->select(['id', 'created_at'])->orderBy('created_at', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'purchase-page');
      $vouchers = Voucher::orderBy('date', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'voucher-page');

      return view('cashflows.index', [
        'cash_flows'  => $cash_flows,
        'users'  => $users,
        'categories'  => $categories,
        'orders'  => $orders,
        'purchases'  => $purchases,
        'vouchers'  => $vouchers
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
        // 'user_id'               => 'required|integer',
        'cash_flow_category_id' => 'sometimes|nullable|integer',
        'description'           => 'sometimes|nullable|string',
        'date'                  => 'required',
        'amount'                => 'required|integer',
        'type'                  => 'required|string'
      ]);

      $data = $request->except(['_token', 'file']);
      $data['user_id'] = Auth::id();

      $cash_flow = CashFlow::create($data);

      if ($request->hasFile('file')) {
        foreach ($request->file('file') as $file) {
          $filename = $file->getClientOriginalName();

          $file->storeAs("files", $filename, 'uploads');

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

      return Storage::disk('uploads')->download('files/' . $file->filename);
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
      $cash_flow = CashFlow::find($id);

      if ($cash_flow->files) {
        foreach ($cash_flow->files as $file) {
          Storage::disk('uploads')->delete("files/$file->filename");
          $file->delete();
        }
      }

      $cash_flow->delete();

      return redirect()->route('cashflow.index')->withSuccess('You have successfully deleted a record!');
    }
}
