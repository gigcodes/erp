<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Helpers;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\ReadOnly\OrderStatus as OrderStatus;
use App\User;
use App\Message;
use App\Task;
use App\Reply;
use App\CallRecording;
use App\OrderStatus as OrderStatuses;
use App\OrderReport;
use App\Purchase;
use Auth;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderController extends Controller {


	public function __construct() {

		$this->middleware( 'permission:order-view', [ 'only' => ['index','show'] ] );
		$this->middleware( 'permission:order-create', [ 'only' => [ 'create', 'store' ] ] );
		$this->middleware( 'permission:order-edit', [ 'only' => [ 'edit', 'update' ] ] );
		$this->middleware( 'permission:order-delete', [ 'only' => ['destroy','deleteOrderProduct'] ] );
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {

		$term = $request->input('term');

		if($request->input('orderby') == '')
				$orderby = 'desc';
		else
				$orderby = 'asc';

		switch ($request->input('sortby')) {
			case 'type':
					 $sortby = 'order_type';
					break;
			case 'date':
					 $sortby = 'order_date';
					break;
			case 'order_handler':
					 $sortby = 'sales_person';
					break;
			case 'client_name':
					 $sortby = 'client_name';
					break;
			case 'status':
					 $sortby = 'order_status';
					break;
			case 'action':
					 $sortby = 'action';
					break;
			case 'due':
					 $sortby = 'due';
					break;
			case 'communication':
					 $sortby = 'communication';
					break;
			default :
					 $sortby = 'id';
		}

		$orders = ((new Order())->newQuery());

		if ($sortby != 'communication' && $sortby != 'action' && $sortby != 'due') {
			$orders = $orders->orderBy( $sortby, $orderby );
		}

		if(empty($term))
			$orders = $orders->latest();
		else{

			$orders = $orders->latest()
			               ->orWhere('order_id','like','%'.$term.'%')
			               ->orWhere('order_type',$term)
			               ->orWhere('sales_person',Helpers::getUserIdByName($term))
			               ->orWhere('received_by',Helpers::getUserIdByName($term))
			               ->orWhere('client_name','like','%'.$term.'%')
			               ->orWhere('city','like','%'.$term.'%')
			               ->orWhere('order_status',(new OrderStatus())->getIDCaseInsensitive($term));
		}



		$users  = Helpers::getUserArray( User::all() );

		$orders_array = $orders->whereNull( 'deleted_at' )->get()->toArray();

		if ($sortby == 'communication') {
			if ($orderby == 'asc') {
				$orders_array = array_values(array_sort($orders_array, function ($value) {
						return $value['communication']['created_at'];
				}));

				$orders_array = array_reverse($orders_array);
			} else {
				$orders_array = array_values(array_sort($orders_array, function ($value) {
						return $value['communication']['created_at'];
				}));
			}
		}

		if ($sortby == 'action') {
			if ($orderby == 'asc') {
				$orders_array = array_values(array_sort($orders_array, function ($value) {
						return $value['action']['status'];
				}));

				$orders_array = array_reverse($orders_array);
			} else {
				$orders_array = array_values(array_sort($orders_array, function ($value) {
						return $value['action']['status'];
				}));
			}
		}

		if ($sortby == 'due') {
			if ($orderby == 'asc') {
				$orders_array = array_values(array_sort($orders_array, function ($value) {
						return $value['action']['completion_date'];
				}));

				$orders_array = array_reverse($orders_array);
			} else {
				$orders_array = array_values(array_sort($orders_array, function ($value) {
						return $value['action']['completion_date'];
				}));
			}
		}

		$currentPage = LengthAwarePaginator::resolveCurrentPage();
		$perPage = 10;
		$currentItems = array_slice($orders_array, $perPage * ($currentPage - 1), $perPage);

		$orders_array = new LengthAwarePaginator($currentItems, count($orders_array), $perPage, $currentPage, [
			'path'	=> LengthAwarePaginator::resolveCurrentPath()
		]);

		return view( 'orders.index', compact('orders_array', 'users','term', 'orderby' ) );
	}

	public function products(Request $request)
	{
		$term = $request->input('term');

		if($request->input('orderby') == '')
				$orderby = 'desc';
		else
				$orderby = 'asc';

		switch ($request->input('sortby')) {
			case 'supplier':
					 $sortby = 'supplier';
					break;
			case 'customer':
					 $sortby = 'client_name';
					break;
			case 'customer_price':
					 $sortby = 'price';
					break;
			case 'date':
					 $sortby = 'created_at';
					break;
			case 'delivery_date':
					 $sortby = 'date_of_delivery';
					break;
			case 'updated_date':
					 $sortby = 'estimated_delivery_date';
					break;
			case 'status':
					 $sortby = 'status';
					break;
			case 'communication':
					 $sortby = 'communication';
					break;
			default :
					 $sortby = 'id';
		}

		$products = ((new OrderProduct())->newQuery());

		// if ($sortby != 'communication') {
		// 	$purchases = $purchases->orderBy( $sortby, $orderby );
		// }

		// if(empty($term))
		// 	$purchases = $purchases->latest();
		// else{
		//
		// 	$purchases = $purchases->latest()
		// 	               ->orWhere('id','like','%'.$term.'%')
		// 	               ->orWhere('purchase_handler',Helpers::getUserIdByName($term))
		// 	               ->orWhere('supplier','like','%'.$term.'%')
		//                  ->orWhere('status','like','%'.$term.'%');
		// }



		// $users  = Helpers::getUserArray( User::all() );

		// $purchases_array = $products->get()->toArray();
		$products = $products->get();
		$product_array = [];
		$count = 0;

		foreach ($products as $index => $product) {
			// $purchases_array[$index]['products'] = [];
			// foreach ($purchase->products as $key => $product) {
				$order = [];
				$purchase = [];
				// $customer_price = '';
				$image = '';
				$supplier = '';
				$percentage = '';
				$factor = '';
				$price = '';
				array_push($product_array, $product->toArray());

				if ($product_image = Product::where('sku', $product_array[$count]['sku'])->first()) {
					$image = $product_image->getMedia(config('constants.media_tags'))->first() ? $product_image->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
					$supplier = $product_image->supplier;
					$percentage = $product_image->percentage;
					$factor = $product_image->factor;
					$price = $product_image->price;
					$product_array[$count]['purchase'] = [];

					if ($product_image->purchases->first()) {
						// dd($product_image->purchases);
						$purchase = $product_image->purchases->first()->toArray();
					}
				}
				// $product_array[$count]['image'] = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '';

				if (Order::find($product->order_id)) {
					$order = Order::find($product->order_id)->toArray();
					// $customer_price = OrderProduct::where('sku', $product->sku)->first()->product_price;
				}

				$product_array[$count]['image'] = $image;
				$product_array[$count]['supplier'] = $supplier;
				$product_array[$count]['percentage'] = $percentage;
				$product_array[$count]['factor'] = $factor;
				$product_array[$count]['price'] = $price;
				$product_array[$count]['order'] = $order;
				$product_array[$count]['purchase'] = $purchase;
				// $product_array[$count]['customer_price'] = $customer_price;

				$count++;
				// dd($product_array);
			// }
		}

		// dd($product_array);

		if ($sortby == 'supplier') {
			if ($orderby == 'asc') {
				$product_array = array_values(array_sort($product_array, function ($value) {
						return $value['supplier'];
				}));

				$product_array = array_reverse($product_array);
			} else {
				$product_array = array_values(array_sort($product_array, function ($value) {
						return $value['supplier'];
				}));
			}
		}

		if ($sortby == 'client_name') {
			if ($orderby == 'asc') {
				$product_array = array_values(array_sort($product_array, function ($value) {
					if ($value['order']) {
						return $value['order']['client_name'];
					}

					return '';
				}));

				$product_array = array_reverse($product_array);
			} else {
				$product_array = array_values(array_sort($product_array, function ($value) {
					if ($value['order']) {
						return $value['order']['client_name'];
					}

					return '';
				}));
			}
		}

		if ($sortby == 'price') {
			if ($orderby == 'asc') {
				$product_array = array_values(array_sort($product_array, function ($value) {
						return $value['price'];
				}));

				$product_array = array_reverse($product_array);
			} else {
				$product_array = array_values(array_sort($product_array, function ($value) {
						return $value['price'];
				}));
			}
		}

		if ($sortby == 'created_at') {
			if ($orderby == 'asc') {
				$product_array = array_values(array_sort($product_array, function ($value) {
					if ($value['purchase']) {
						return $value['purchase']['created_at'];
					}

					return '1999-01-01 00:00:00';
				}));

				$product_array = array_reverse($product_array);
			} else {
				$product_array = array_values(array_sort($product_array, function ($value) {
					if ($value['purchase']) {
						return $value['purchase']['created_at'];
					}

					return '1999-01-01 00:00:00';
				}));
			}
		}

		if ($sortby == 'date_of_delivery') {
			if ($orderby == 'asc') {
				$product_array = array_values(array_sort($product_array, function ($value) {
					if ($value['order']) {
						return $value['order']['date_of_delivery'];
					}

					return '1999-01-01 00:00:00';
				}));

				$product_array = array_reverse($product_array);
			} else {
				$product_array = array_values(array_sort($product_array, function ($value) {
					if ($value['order']) {
						return $value['order']['date_of_delivery'];
					}

					return '1999-01-01 00:00:00';
				}));
			}
		}

		if ($sortby == 'estimated_delivery_date') {
			if ($orderby == 'asc') {
				$product_array = array_values(array_sort($product_array, function ($value) {
					if ($value['order']) {
						return $value['order']['estimated_delivery_date'];
					}

					return '1999-01-01 00:00:00';
				}));

				$product_array = array_reverse($product_array);
			} else {
				$product_array = array_values(array_sort($product_array, function ($value) {
					if ($value['order']) {
						return $value['order']['estimated_delivery_date'];
					}

					return '1999-01-01 00:00:00';
				}));
			}
		}

		if ($sortby == 'status') {
			if ($orderby == 'asc') {
				$product_array = array_values(array_sort($product_array, function ($value) {
					if ($value['order']) {
						return $value['order']['order_status'];
					}

					return '';
				}));

				$product_array = array_reverse($product_array);
			} else {
				$product_array = array_values(array_sort($product_array, function ($value) {
					if ($value['order']) {
						return $value['order']['order_status'];
					}

					return '';
				}));
			}
		}

		if ($sortby == 'communication') {
			if ($orderby == 'asc') {
				$product_array = array_values(array_sort($product_array, function ($value) {
						return $value['communication']['created_at'];
				}));

				$product_array = array_reverse($product_array);
			} else {
				$product_array = array_values(array_sort($product_array, function ($value) {
						return $value['communication']['created_at'];
				}));
			}
		}

		$currentPage = LengthAwarePaginator::resolveCurrentPage();
		$perPage = 10;
		$currentItems = array_slice($product_array, $perPage * ($currentPage - 1), $perPage);

		$product_array = new LengthAwarePaginator($currentItems, count($product_array), $perPage, $currentPage, [
			'path'	=> LengthAwarePaginator::resolveCurrentPath()
		]);

		return view('orders.products', compact('product_array','term', 'orderby'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$order = new Order();
		$data  = [];
		foreach ( $order->getFillable() as $item ) {
			$data[ $item ] = '';
		}

		$expiresAt = Carbon::now()->addMinutes(10);

		if (Cache::has('last-order')) {
			if (!Cache::has('user-order-' . Auth::id())) {
				$last_order = Cache::get('last-order') + 1;
				Cache::put('user-order-' . Auth::id(), $last_order, $expiresAt);
				Cache::put('last-order', $last_order, $expiresAt);
			}
		} else {
			$last_order = Order::withTrashed()->latest()->first()->id + 1;
			Cache::put('user-order-' . Auth::id(), $last_order, $expiresAt);
			Cache::put('last-order', $last_order, $expiresAt);
		}

		$data['id'] = Cache::get('user-order-' . Auth::id());
		$data['sales_persons'] = Helpers::getUsersArrayByRole( 'Sales' );
		$data['modify']        = 0;
		$data['order_products'] = $this->getOrderProductsWithProductData($data['id']);


		return view( 'orders.form', $data );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request ) {

		$this->validate( $request, [
			'client_name'    => 'required',
			'advance_detail' => 'numeric|nullable',
			'balance_amount' => 'numeric|nullable',
		] );

		$data = $request->all();
		$data['user_id'] = Auth::id();

		if ( $request->input( 'order_type' ) == 'offline' ) {
			$data['order_id'] = $this->generateNextOrderId();
		}

		if ( empty( $request->input( 'order_date' ) ) ) {

			$data['order_date'] = date( 'Y-m-d' );
		}

		$order = Order::create( $data );

		$expiresAt = Carbon::now()->addMinutes(10);
		$last_order = $order->id + 1;
		Cache::put('user-order-' . Auth::id(), $last_order, $expiresAt);

		if ($request->convert_order == 'convert_order') {
			if (!empty($request->selected_product)) {
				foreach ($request->selected_product as $product) {
					self::attachProduct( $order->id, $product );
				}
			}
		}

		NotificationQueueController::createNewNotification([
			'type' => 'button',
			'message' => $data['client_name'],
			// 'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
			'timestamps' => ['+0 minutes'],
			'model_type' => Order::class,
			'model_id' =>  $order->id,
			'user_id' => \Auth::id(),
			'sent_to' => $request->input( 'sales_person' ),
			'role' => '',
		]);

		// NotificationQueueController::createNewNotification([
		// 	'message' => $data['client_name'],
		// 	'timestamps' => ['+45 minutes'],
		// 	'model_type' => Order::class,
		// 	'model_id' =>  $order->id,
		// 	'user_id' => \Auth::id(),
		// 	'sent_to' => \Auth::id(),
		// 	'role' => '',
		// ]);

		NotificationQueueController::createNewNotification([
			'message' => $data['client_name'],
			'timestamps' => ['+0 minutes'],
			'model_type' => Order::class,
			'model_id' =>  $order->id,
			'user_id' => \Auth::id(),
			'sent_to' => '',
			'role' => 'Admin',
		]);

		return redirect()->route( 'order.index' )
		                 ->with( 'message', 'Order created successfully' );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Order $order
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( Order $order ) {

		$data                   = $order->toArray();
		$data['sales_persons']  = Helpers::getUsersArrayByRole( 'Sales' );
		$data['order_products'] = $this->getOrderProductsWithProductData($order->id);
		$data['comments']        = Comment::with('user')->where( 'subject_id', $order->id )
		                                 ->where( 'subject_type','=' ,Order::class )->get();
		$data['users']          = User::all()->toArray();
		$messages = Message::all()->where('moduleid','=', $data['id'])->where('moduletype','=', 'order')->sortByDesc("created_at")->take(10)->toArray();
        $data['messages'] = $messages;
        $data['total_price'] = $this->getTotalOrderPrice($order);

		$order_statuses = (new OrderStatus)->all();
		$data['order_statuses'] = $order_statuses;
		$data['tasks'] = Task::where('model_type', 'order')->where('model_id', $order->id)->get()->toArray();
		$data['approval_replies'] = Reply::where('model', 'Approval Order')->get();
		$data['internal_replies'] = Reply::where('model', 'Internal Order')->get();
        $data['order_recordings'] = CallRecording::where('order_id', '=', $data['order_id'])->get()->toArray();
		$data['order_status_report'] = OrderStatuses::all();
		$data['order_reports'] = OrderReport::where('order_id', $order->id)->get();
		$data['users_array'] = Helpers::getUserArray(User::all());

		// dd($data);
		//return $data;
		return view( 'orders.show', $data );
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Order $order
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit( Order $order ) {

		$data                   = $order->toArray();
		$data['modify']         = 1;
		$data['sales_persons']  = Helpers::getUsersArrayByRole( 'Sales' );
		$data['order_products'] = $this->getOrderProductsWithProductData($order->id);

		return view( 'orders.form', $data );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\Order $order
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, Order $order ) {

		$this->validate( $request, [
			'client_name'    => 'required',
			'advance_detail' => 'numeric|nullable',
			'balance_amount' => 'numeric|nullable',
		] );


		if( $order->sales_person != $request->input('sales_person') ){

			NotificationQueueController::createNewNotification([
				'type' => 'button',
				'message' => $order->client_name,
				'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
				'model_type' => Order::class,
				'model_id' =>  $order->id,
				'user_id' => \Auth::id(),
				'sent_to' => $request->input( 'sales_person' ),
				'role' => '',
			]);

			NotificationQueueController::createNewNotification([
				'message' => $order->client_name,
				'timestamps' => ['+45 minutes'],
				'model_type' => Order::class,
				'model_id' =>  $order->id,
				'user_id' => \Auth::id(),
				'sent_to' => \Auth::id(),
				'role' => '',
			]);
		}

		if(!empty($request->input('order_products'))) {

			foreach ( $request->input( 'order_products' ) as $key => $order_product_data ) {

				$order_product = OrderProduct::findOrFail( $key );
				$order_product->update( $order_product_data );
			}
		}

		$data = $request->all();
		$order->update( $data );

		$this->calculateBalanceAmount($order);


		return back()->with( 'message', 'Order updated successfully' );
	}

	public function updateStatus(Request $request, $id)
	{
		$order = Order::find($id);
		$order->order_status = $request->status;
		$order->save();
	}

	public function calculateBalanceAmount(Order $order){

		$order_instance = Order::where('id',$order->id)->with('order_product')->get()->first();

		$balance_amt = 0;

		foreach ($order_instance->order_product as $order_product)
		{
			$balance_amt += $order_product->product_price * $order_product->qty;
		}

		if( !empty($order_instance->advance_detail) ){
			$balance_amt -= $order_instance->advance_detail;
		}

		$order->update([
			'balance_amount' => $balance_amt
		]);
	}

	public function getTotalOrderPrice($order_instance){

		$balance_amt = 0;

		foreach ($order_instance->order_product as $order_product)
		{
			$balance_amt += $order_product->product_price * $order_product->qty;
		}


		return $balance_amt;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Order $order
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( Order $order ) {

		$order->delete();
		return redirect('order')->with('success','Order has been archived');
	}

	public function permanentDelete(Order $order ){

		$order_products = OrderProduct::where('order_id','=',$order->id);

		$order_products->delete();
		$comments = Comment::where('subject_id',$order->id)->where('subject_type',Order::class);
		$comments->delete();

		$order->forceDelete();

		return redirect('order')->with('success','Order has been  deleted');
	}

	public function deleteOrderProduct(OrderProduct $order_product){
		$order_product->delete();

		return redirect()->back()->with('message','Product Detached');
	}


	public static function attachProduct( $model_id, $product_id ) {

		$product = Product::where( 'id', '=', $product_id )->get()->first();

		$order_product = OrderProduct::where( 'order_id', $model_id )->where( 'sku', $product->sku )->first();

		if ( empty( $order_product ) ) {

			OrderProduct::create( [
				'order_id'      => $model_id,
				'sku'           => $product->sku,
				'product_price' => $product->price_special,
				'color' => $product->color,
			] );

			$action = 'Attached';
		} else {

			$order_product->delete();
			$action = 'Attach';
		}

		return $action;
	}

	public function generateNextOrderId() {

		$previous = Order::withTrashed()->latest()->where( 'order_type', '=', 'Offline' )->first( [ 'order_id' ] );

		if ( ! empty( $previous ) ) {

			$temp = explode( '-', $previous );

			return 'OFF-' . ( intval( $temp[1] ) + 1 );
		}

		return 'OFF-1000001';
	}

	public function getOrderProductsWithProductData($order_id){


		$orderProducts = OrderProduct::where('order_id', '=', $order_id)->get()->toArray();

		foreach ($orderProducts as $key => $value){

			if(!empty($orderProducts[$key]['color'])) {

				$temp = Product::where( 'sku', '=', $orderProducts[ $key ]['sku'] )
				                                           ->where( 'color', $orderProducts[ $key ]['color'] )
				                                           ->get()->first();

			}else{

				$temp = Product::where( 'sku', '=', $orderProducts[ $key ]['sku'] )
				                                           ->get()->first();
			}

			if(!empty($temp)){

				$orderProducts[ $key ]['product'] = $temp;
				$orderProducts[ $key ]['product']['image'] = $temp->getMedia(config('constants.media_tags'))->first() ? $temp->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
			}
		}

		return $orderProducts;

//		return OrderProduct::with( 'product' )->where( 'order_id', '=', $order_id )->get()->toArray();
	}
}
