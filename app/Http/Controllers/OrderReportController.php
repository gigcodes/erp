<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrderStatus;
use App\OrderReport;
use App\Order;
use Auth;

class OrderReportController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'status_id' => 'required',
        'completion_date' => 'required'
      ]);

      $report = new OrderReport;

      $report->status_id = $request->status_id;
      $report->user_id = Auth::id();

      if ($request->order_id)
        $report->order_id = $request->order_id;
      else
        $report->customer_id = $request->customer_id;

      $report->completion_date = $request->completion_date;

      $report->save();

      // $order = Order::find($report->order_id);
      //
      // if ($order->sales_person) {
      //   NotificationQueueController::createNewNotification([
    	// 		'type' => 'button',
    	// 		'message' => $order->client_name . ' - ' . $report->status,
    	// 		'timestamps' => ['+0 minutes'],
    	// 		'model_type' => Order::class,
    	// 		'model_id' =>  $report->order_id,
    	// 		'user_id' => \Auth::id(),
    	// 		'sent_to' => $order->sales_person,
    	// 		'role' => '',
    	// 	]);
      // }

  		// NotificationQueueController::createNewNotification([
  		// 	'message' => $order->client_name . ' - ' . $report->status,
  		// 	'timestamps' => ['+0 minutes'],
  		// 	'model_type' => Order::class,
  		// 	'model_id' =>  $report->order_id,
  		// 	'user_id' => \Auth::id(),
  		// 	'sent_to' => '',
  		// 	'role' => 'Admin',
  		// ]);

      return redirect()->back()->with('message', 'Order action was created successfully');
    }

    public function statusStore(Request $request) {
  		$this->validate($request, [
  			'status'	=> 'required'
  		]);

  		$status = new OrderStatus;

  		$status->status = $request->status;

  		$status->save();

  		return redirect()->back()->with('message', 'Order status was created successfully');
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
}
