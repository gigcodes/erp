<?php

namespace App\Http\Controllers;

use App\DeliveryApproval;
use App\StatusChange;
use App\PrivateView;
use App\Helpers;
use App\User;
use Auth;
use Illuminate\Http\Request;

class DeliveryApprovalController extends Controller
{

    public function __construct() {
      $this->middleware('permission:delivery-approval');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $delivery_approvals = DeliveryApproval::all();
      $users_array = Helpers::getUserArray(User::all());

      return view('deliveryapprovals.index', [
        'delivery_approvals'  => $delivery_approvals,
        'users_array'  => $users_array,
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

    public function updateStatus(Request $request, $id)
    {
      $delivery_approval = DeliveryApproval::find($id);

      StatusChange::create([
        'model_id'    => $delivery_approval->id,
        'model_type'  => DeliveryApproval::class,
        'user_id'     => Auth::id(),
        'from_status' => $delivery_approval->status,
        'to_status'   => $request->status
      ]);

      $delivery_approval->status = $request->status;
      $delivery_approval->save();

      if ($request->status == 'delivered') {
        $delivery_approval->private_view->products[0]->supplier = '';
        $delivery_approval->private_view->products[0]->save();
      } elseif ($request->status == 'returned') {
        $delivery_approval->private_view->products[0]->supplier = 'In-stock';
        $delivery_approval->private_view->products[0]->save();
      }

      if ($delivery_approval->private_view) {
        $delivery_approval->private_view->status = $request->status;
        $delivery_approval->private_view->save();

        StatusChange::create([
          'model_id'    => $delivery_approval->private_view->id,
          'model_type'  => PrivateView::class,
          'user_id'     => Auth::id(),
          'from_status' => $delivery_approval->private_view->status,
          'to_status'   => $request->status
        ]);
      }

      return response('success');
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
}
