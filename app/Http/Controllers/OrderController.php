<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Helpers;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\ReadOnly\OrderStatus as OrderStatus;
use App\User;
use App\Leads;
use App\Message;
use App\Task;
use App\Reply;
use App\CallRecording;
use App\OrderStatus as OrderStatuses;
use App\OrderReport;
use App\Purchase;
use App\Customer;
use App\ReplyCategory;
use App\Refund;
use App\Email;
use App\ChatMessage;
use App\CommunicationHistory;
use Auth;
use Cache;
use Validator;
use Storage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\CallBusyMessage;
use App\CallHistory;
use App\Setting;
use App\StatusChange;
use App\Category;
use App\Mail\RefundProcessed;
use App\Mail\AdvanceReceipt;
use App\Mail\AdvanceReceiptPDF;
use App\Mail\OrderInvoicePDF;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;
use Dompdf\Dompdf;

use App\Services\BlueDart\BlueDart;
use App\DeliveryApproval;
use App\Waybill;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use \SoapClient;

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
				$orderby = 'asc';
		else
				$orderby = 'desc';

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
					 $sortby = 'communication';
		}

		$orders = (new Order())->newQuery()->with('customer');

		if ($sortby != 'communication' && $sortby != 'action' && $sortby != 'due') {
			$orders = $orders->orderBy( $sortby, $orderby );
		}

		if(empty($term))
			$orders = $orders->latest();
		else{

			$orders = $orders->latest()->whereHas('customer', function($query) use ($term) {
				return $query->where('name', 'LIKE', "%$term%");
			})
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
					 $sortby = 'order_status';
					break;
			case 'communication':
					 $sortby = 'communication';
					break;
			default :
					 $sortby = 'id';
		}

		if(empty($term))
			$products = OrderProduct::with(['Product' => function($query) {
				$query->with('Purchases');
			}, 'Order'])->get()->toArray();
		else{

			$products = OrderProduct::whereHas('Product', function ($query) use ($term){
	        $query->where('supplier', 'like', '%'.$term.'%');
	    })
	    ->with(['Product', 'Order'])->orWhere('product_price', 'LIKE', "%$term%")
					->orWhereHas('Order', function ($query) use ($term){
			        $query->where('date_of_delivery', 'LIKE', "%$term%")
										->orWhere('estimated_delivery_date', 'LIKE', "%$term%")
										->orWhere('order_status', 'LIKE', "%$term%");
			    })->get()->toArray();
		}

		$brand = $request->input('brand');
		$supplier = $request->input('supplier');

		if ($sortby == 'supplier') {
			if ($orderby == 'asc') {
				$products = array_values(array_sort($products, function ($value) {
						return $value['product']['supplier'];
				}));

				$products = array_reverse($products);
			} else {
				$products = array_values(array_sort($products, function ($value) {
						return $value['product']['supplier'];
				}));
			}
		}

		if ($sortby == 'client_name') {
			if ($orderby == 'asc') {
				$products = array_values(array_sort($products, function ($value) {
					if ($value['order']) {
						return $value['order']['client_name'];
					}

					return '';
				}));

				$products = array_reverse($products);
			} else {
				$products = array_values(array_sort($products, function ($value) {
					if ($value['order']) {
						return $value['order']['client_name'];
					}

					return '';
				}));
			}
		}

		if ($sortby == 'price') {
			if ($orderby == 'asc') {
				$products = array_values(array_sort($products, function ($value) {
						return $value['product_price'];
				}));

				$products = array_reverse($products);
			} else {
				$products = array_values(array_sort($products, function ($value) {
						return $value['product_price'];
				}));
			}
		}

		if ($sortby == 'created_at') {
			if ($orderby == 'asc') {
				$products = array_values(array_sort($products, function ($value) {
					if ($value['order']) {
						return $value['order']['created_at'];
					}

					return '1999-01-01 00:00:00';
				}));

				$products = array_reverse($products);
			} else {
				$products = array_values(array_sort($products, function ($value) {
					if ($value['order']) {
						return $value['order']['created_at'];
					}

					return '1999-01-01 00:00:00';
				}));
			}
		}

		if ($sortby == 'date_of_delivery') {
			if ($orderby == 'asc') {
				$products = array_values(array_sort($products, function ($value) {
					if ($value['order']) {
						return $value['order']['date_of_delivery'];
					}

					return '1999-01-01 00:00:00';
				}));

				$products = array_reverse($products);
			} else {
				$products = array_values(array_sort($products, function ($value) {
					if ($value['order']) {
						return $value['order']['date_of_delivery'];
					}

					return '1999-01-01 00:00:00';
				}));
			}
		}

		if ($sortby == 'estimated_delivery_date') {
			if ($orderby == 'asc') {
				$products = array_values(array_sort($products, function ($value) {
					if ($value['order']) {
						return $value['order']['estimated_delivery_date'];
					}

					return '1999-01-01 00:00:00';
				}));

				$products = array_reverse($products);
			} else {
				$products = array_values(array_sort($products, function ($value) {
					if ($value['order']) {
						return $value['order']['estimated_delivery_date'];
					}

					return '1999-01-01 00:00:00';
				}));
			}
		}

		if ($sortby == 'order_status') {
			if ($orderby == 'asc') {
				$products = array_values(array_sort($products, function ($value) {
					if ($value['order']) {
						return $value['order']['order_status'];
					}

					return '';
				}));

				$products = array_reverse($products);
			} else {
				$products = array_values(array_sort($products, function ($value) {
					if ($value['order']) {
						return $value['order']['order_status'];
					}

					return '';
				}));
			}
		}

		if ($sortby == 'communication') {
			if ($orderby == 'asc') {
				$products = array_values(array_sort($products, function ($value) {
						return $value['communication']['created_at'];
				}));

				$products = array_reverse($products);
			} else {
				$products = array_values(array_sort($products, function ($value) {
						return $value['communication']['created_at'];
				}));
			}
		}

		$currentPage = LengthAwarePaginator::resolveCurrentPage();
		$perPage = 10;
		$currentItems = array_slice($products, $perPage * ($currentPage - 1), $perPage);

		$products = new LengthAwarePaginator($currentItems, count($products), $perPage, $currentPage, [
			'path'	=> LengthAwarePaginator::resolveCurrentPath()
		]);

		return view('orders.products', compact('products','term', 'orderby', 'brand', 'supplier'));
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

		$customer_suggestions = [];
		$customers = ( new Customer() )->newQuery()
																			->latest()->select('name')->get()->toArray();

		foreach ($customers as $customer) {
			array_push($customer_suggestions, $customer['name']);
		}

		$data['customers'] = Customer::all();

		$data['customer_suggestions'] = $customer_suggestions;


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
			'customer_id'    => 'required',
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

		// if ($customer = Customer::where('name', $data['client_name'])->first()) {
		// 	$data['customer_id'] = $customer->id;
		// } else {
		// 	$customer = new Customer;
		// 	$customer->name = $data['client_name'];
		//
		// 	$validator = Validator::make($data, [
		// 		'contact_detail' => 'unique:customers,phone'
		// 	]);
		//
		// 	if ($validator->fails()) {
		// 		return back()->with('phone_error', 'The phone already exists')->withInput();
		// 	}
		//
		// 	$customer->phone = $data['contact_detail'];
		// 	$customer->city = $data['city'];
		// 	$customer->save();
		//
		// 	$data['customer_id'] = $customer->id;
		// }

		$customer = Customer::find($request->customer_id);

		$data['client_name'] = $customer->name;
		$data['contact_detail'] = $customer->phone;

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

		if ($order->order_status == 'Proceed without Advance' && $order->order_type == 'online') {
			$product_names = '';
			foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
				$product_names .= $order_product->product ? $order_product->product->name . ", " : '';
			}

			$auto_message = "We have received your COD order for $product_names and we will deliver the same by " . ($order->estimated_delivery_date ? Carbon::parse($order->estimated_delivery_date)->format('d \of\ F') : Carbon::parse($order->order_date)->addDays(15)->format('d \of\ F')) . '.';
			$followup_message = "Ma'am please also note that since your order was placed on c o d - an initial advance needs to be paid to process the order - pls let us know how you would like to make this payment.";
			$requestData = new Request();
			$requestData2 = new Request();
			$requestData->setMethod('POST');
			$requestData2->setMethod('POST');
			$requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message]);
			$requestData2->request->add(['customer_id' => $order->customer->id, 'message' => $followup_message]);

			app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');
			app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData2, 'customer');

			// $order->update([
			// 	'auto_messaged' => 1,
			// 	'auto_messaged_date' => Carbon::now()
			// ]);

			CommunicationHistory::create([
				'model_id'		=> $order->id,
				'model_type'	=> Order::class,
				'type'				=> 'initial-advance',
				'method'			=> 'whatsapp'
			]);
		} elseif ($order->order_status == 'Prepaid') {
			$auto_message = "Greetings from Solo Luxury. We have received your order. This is our whatsapp number to assist you with order related queries. You can contact us between 9.00 am - 5.30 pm on 0008000401700. Thank you.";
			$requestData = new Request();
			$requestData->setMethod('POST');
			$requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message]);

			app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');

			// $order->update([
			// 	'auto_messaged' => 1,
			// 	'auto_messaged_date' => Carbon::now()
			// ]);

			CommunicationHistory::create([
				'model_id'		=> $order->id,
				'model_type'	=> Order::class,
				'type'				=> 'online-confirmation',
				'method'			=> 'whatsapp'
			]);
		} elseif ($order->order_status == 'Refund to be processed') {
			$refund = Refund::where('order_id', $order->id)->first();

			if (!$refund) {
				Refund::create([
					'customer_id'			=> $order->customer->id,
					'order_id'				=> $order->id,
					'type'						=> 'Cash',
					'date_of_request'	=> Carbon::now(),
					'date_of_issue' 	=> Carbon::now()->addDays(10)
				]);
			}

			if ($order->payment_mode == 'paytm') {
				if ($order->customer) {
					$all_amount = 0;

					if ($order->order_product) {
						foreach ($order->order_product as $order_product) {
							$all_amount += $order_product->product_price;
						}
					}

					$order->customer->credit += $all_amount;
					$order->customer->save();
				}
			} else if ($order->payment_mode != 'paytm' || $order->advance_detail > 0) {
				if ($order->customer) {
					$order->customer->credit += $order->advance_detail;
					$order->customer->save();
				}
			}
		}

		// if ($order->auto_emailed == 0) {
		if (!$order->is_sent_offline_confirmation()) {
			if ($order->order_type == 'offline') {

			}
		}

		// NotificationQueueController::createNewNotification([
		// 	'type' => 'button',
		// 	'message' => $data['client_name'],
		// 	// 'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
		// 	'timestamps' => ['+0 minutes'],
		// 	'model_type' => Order::class,
		// 	'model_id' =>  $order->id,
		// 	'user_id' => \Auth::id(),
		// 	'sent_to' => $request->input( 'sales_person' ),
		// 	'role' => '',
		// ]);
		//
		// NotificationQueueController::createNewNotification([
		// 	'message' => $data['client_name'],
		// 	'timestamps' => ['+0 minutes'],
		// 	'model_type' => Order::class,
		// 	'model_id' =>  $order->id,
		// 	'user_id' => \Auth::id(),
		// 	'sent_to' => '',
		// 	'role' => 'Admin',
		// ]);

		if ($request->ajax()) {
			return response()->json(['order' => $order]);
		}

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
		// $data['approval_replies'] = Reply::where('model', 'Approval Order')->get();
		// $data['internal_replies'] = Reply::where('model', 'Internal Order')->get();
        $data['order_recordings'] = CallRecording::where('order_id', '=', $data['order_id'])->get()->toArray();
		$data['order_status_report'] = OrderStatuses::all();
		if ($order->customer)
			$data['order_reports'] = OrderReport::where('order_id', $order->customer->id)->get();

		$data['users_array'] = Helpers::getUserArray(User::all());
		$data['has_customer'] = $order->customer ? $order->customer->id : false;
		$data['customer'] = $order->customer;
		$data['reply_categories'] = ReplyCategory::all();
		$data['delivery_approval'] = $order->delivery_approval;
		$data['waybill'] = $order->waybill;

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

		if ($request->type != 'customer') {
			$this->validate( $request, [
				// 'client_name'    => 'required',
				'advance_detail' => 'numeric|nullable',
				'balance_amount' => 'numeric|nullable',
				'contact_detail'	=> 'sometimes|nullable|numeric|regex:/^[91]{2}/|digits:12'
			] );
		}


		// if( $order->sales_person != $request->input('sales_person') ){
		//
		// 	NotificationQueueController::createNewNotification([
		// 		'type' => 'button',
		// 		'message' => $order->client_name,
		// 		// 'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
		// 		'timestamps' => ['+0 minutes'],
		// 		'model_type' => Order::class,
		// 		'model_id' =>  $order->id,
		// 		'user_id' => \Auth::id(),
		// 		'sent_to' => $request->input( 'sales_person' ),
		// 		'role' => '',
		// 	]);
		// }

		if(!empty($request->input('order_products'))) {
			foreach ($request->input('order_products') as $key => $order_product_data) {
				$order_product = OrderProduct::findOrFail( $key );

				if ($order_product_data['purchase_status'] != $order_product->purchase_status) {
					StatusChange::create([
						'model_id'    => $order_product->id,
						'model_type'  => OrderProduct::class,
						'user_id'     => Auth::id(),
						'from_status' => $order_product->purchase_status,
						'to_status'   => $order_product_data['purchase_status']
					]);
				}

				$order_product->update($order_product_data);
			}
		}

		if ($request->status != $order->order_status) {
			StatusChange::create([
				'model_id'    => $order->id,
				'model_type'  => Order::class,
				'user_id'     => Auth::id(),
				'from_status' => $order->order_status,
				'to_status'   => $request->status
			]);
		}

		$data = $request->except(['_token', '_method', 'status', 'purchase_status']);
		$data['order_status'] = $request->status;
		$order->update($data);

		$this->calculateBalanceAmount($order);
		$order = Order::find($order->id);

		if (!$order->is_sent_initial_advance() && $order->order_status == 'Proceed without Advance' && $order->order_type == 'online') {
			$product_names = '';
			foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
				$product_names .= $order_product->product ? $order_product->product->name . ", " : '';
			}

			$auto_message = "We have received your COD order for $product_names and we will deliver the same by " . ($order->estimated_delivery_date ? Carbon::parse($order->estimated_delivery_date)->format('d \of\ F') : Carbon::parse($order->order_date)->addDays(15)->format('d \of\ F')) . '.';
			$followup_message = "Ma'am please also note that since your order was placed on c o d - an initial advance needs to be paid to process the order - pls let us know how you would like to make this payment.";
			$requestData = new Request();
			$requestData2 = new Request();
			$requestData->setMethod('POST');
			$requestData2->setMethod('POST');
			$requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message]);
			$requestData2->request->add(['customer_id' => $order->customer->id, 'message' => $followup_message]);

			app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');
			app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData2, 'customer');

			CommunicationHistory::create([
				'model_id'		=> $order->id,
				'model_type'	=> Order::class,
				'type'				=> 'initial-advance',
				'method'			=> 'whatsapp'
			]);
		} elseif (!$order->is_sent_online_confirmation() && $order->order_status == 'Prepaid') {
			$auto_message = "Greetings from Solo Luxury. We have received your order. This is our whatsapp number to assist you with order related queries. You can contact us between 9.00 am - 5.30 pm on 0008000401700. Thank you.";
			$requestData = new Request();
			$requestData->setMethod('POST');
			$requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message]);

			app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');

			CommunicationHistory::create([
				'model_id'		=> $order->id,
				'model_type'	=> Order::class,
				'type'				=> 'online-confirmation',
				'method'			=> 'whatsapp'
			]);
		}

		// if ($order->auto_emailed == 0) {
		if (!$order->is_sent_offline_confirmation()) {
			if ($order->order_type == 'offline') {

			}
		}

		if ($order->order_status == 'Refund to be processed') {
			$refund = Refund::where('order_id', $order->id)->first();

			if (!$refund) {
				Refund::create([
					'customer_id'			=> $order->customer->id,
					'order_id'				=> $order->id,
					'type'						=> 'Cash',
					'date_of_request'	=> Carbon::now(),
					'date_of_issue' 	=> Carbon::now()->addDays(10)
				]);
			}

			if ($order->payment_mode == 'paytm') {
				if ($order->customer) {
					$all_amount = 0;

					if ($order->order_product) {
						foreach ($order->order_product as $order_product) {
							$all_amount += $order_product->product_price;
						}
					}

					$order->customer->credit += $all_amount;
					$order->customer->save();
				}
			} else if ($order->payment_mode != 'paytm' || $order->advance_detail > 0) {
				if ($order->customer) {
					$order->customer->credit += $order->advance_detail;
					$order->customer->save();
				}
			}
		}

		if ($order->order_status == 'Delivered') {
			if ($order->order_product) {
				foreach ($order->order_product as $order_product) {
					if ($order_product->product) {
						if ($order_product->product->supplier == 'In-stock') {
							$order_product->product->supplier = '';
							$order_product->product->save();
						}
					}
				}
			}

			if (!$order->is_sent_order_delivered()) {
				$message = "Your Order has been delivered!";
				$requestData = new Request();
				$requestData->setMethod('POST');
				$requestData->request->add(['customer_id' => $order->customer_id, 'message' => $message]);

				app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');

				CommunicationHistory::create([
					'model_id'		=> $order->id,
					'model_type'	=> Order::class,
					'type'				=> 'order-delivered',
					'method'			=> 'whatsapp'
				]);
			}

			$order->delete();

			if ($request->type != 'customer') {
				return redirect()->route('order.index')->with('success', 'Order was updated and archived successfully!');
			} else {
				return back()->with('success', 'Order was updated and archived successfully!');
			}
		}

		return back()->with( 'message', 'Order updated successfully' );
	}

	public function printAdvanceReceipt($id)
	{
		$order = Order::find($id);

		return (new AdvanceReceiptPDF($order))->render();
		$view = (new AdvanceReceiptPDF($order))->render();

		$pdf = new Dompdf;
		$pdf->loadHtml($view);
		$pdf->render();
		$pdf->stream();
	}

	public function emailAdvanceReceipt($id)
	{
		$order = Order::find($id);

		if (true) {
		// if ($order->auto_emailed == 0) {
			if ($order->order_status == 'Advance received') {
				Mail::to($order->customer->email)->send(new AdvanceReceipt($order));

				// $order->update([
				// 	'auto_emailed' => 1,
				// 	'auto_emailed_date' => Carbon::now()
				// ]);

				$params = [
	        'model_id'    		=> $order->customer->id,
	        'model_type'  		=> Customer::class,
	        'from'        		=> 'customercare@sololuxury.co.in',
	        'to'          		=> $order->customer->email,
	        'subject'     		=> "Advance Receipt",
	        'message'     		=> '',
					'template'				=> 'advance-receipt',
					'additional_data'	=> $order->id
	      ];

	      Email::create($params);

				CommunicationHistory::create([
					'model_id'		=> $order->id,
					'model_type'	=> Order::class,
					'type'				=> 'advance-receipt',
					'method'			=> 'email'
				]);
			}
		}

		return redirect()->back()->withSuccess('Advance Receipt was successfully emailed!');
	}

	public function sendConfirmation($id)
	{
		$order = Order::find($id);

		// if ($order->auto_emailed == 0) {
		if (!$order->is_sent_offline_confirmation()) {
			if ($order->order_type == 'offline') {
				Mail::to($order->customer->email)->send(new OrderConfirmation($order));

				// $order->update([
				// 	'auto_emailed' => 1,
				// 	'auto_emailed_date' => Carbon::now()
				// ]);

				$params = [
	        'model_id'    		=> $order->customer->id,
	        'model_type'  		=> Customer::class,
	        'from'        		=> 'customercare@sololuxury.co.in',
	        'to'          		=> $order->customer->email,
	        'subject'     		=> "New Order # " . $order->order_id,
	        'message'     		=> '',
					'template'				=> 'order-confirmation',
					'additional_data'	=> $order->id
	      ];

	      Email::create($params);

				CommunicationHistory::create([
					'model_id'		=> $order->id,
					'model_type'	=> Order::class,
					'type'				=> 'offline-confirmation',
					'method'			=> 'email'
				]);
			}
		}

		return redirect()->back()->withSuccess('You have successfully sent confirmation email!');
	}

	public function generateInvoice($id)
	{
		$order = Order::find($id);
		$consignor = [
			'name'		=> Setting::get('consignor_name'),
			'address'	=> Setting::get('consignor_address'),
			'city'		=> Setting::get('consignor_city'),
			'country'	=> Setting::get('consignor_country'),
			'phone'		=> Setting::get('consignor_phone')
		];

		$view = view('emails.orders.invoice-pdf', [
			'order'			=> $order,
			'consignor'	=> $consignor
		])->render();

		$pdf = new Dompdf;
		$pdf->loadHtml($view);
		$pdf->render();
		$pdf->stream();
	}

	public function uploadForApproval(Request $request, $id) {
		$this->validate($request, [
			'images'	=> 'required'
		]);

		if ($delivery_approval = Order::find($id)->delivery_approval) {

		} else {
			$delivery_approval = new DeliveryApproval;
			$delivery_approval->order_id = $id;
			$delivery_approval->save();
		}

		if ($request->hasfile('images')) {
			foreach ($request->file('images') as $image) {
				$media = MediaUploader::fromSource($image)->upload();
				$delivery_approval->attachMedia($media,config('constants.media_tags'));
			}
		}

		return redirect()->back()->with('success', 'You have successfully uploaded delivery images for approval!');
	}

	public function deliveryApprove(Request $request, $id)
	{
		$delivery_approval = DeliveryApproval::find($id);

		// if ($delivery_approval->approved == 1) {
		// 	$delivery_approval->approved = 2;
		// } else {
			$delivery_approval->approved = 1;
		// }
		$delivery_approval->save();

		return redirect()->back()->with('success', 'You have successfully approved delivery!');
	}

	public function downloadPackageSlip($id)
	{
		$waybill = Waybill::find($id);

		return Storage::disk('files')->download('waybills/' . $waybill->package_slip);
	}

	public function refundAnswer(Request $request, $id)
	{
		$order = Order::find($id);

		$order->refund_answer = $request->answer;
		$order->refund_answer_date = Carbon::now();

		$order->save();

		return response('success');
	}

	public function sendSuggestion(Request $request, $id)
	{
		$params = [
			'number'  => NULL,
			'status'  => 1, // message status for auto messaging
			'user_id' => 6,
		];

		$order = Order::with(['Order_Product' => function ($query) {
			$query->with('Product');
			$query;
		}])->where('id', $id)->first();

		if (count($order->order_product) > 0) {
			$order_products_count = count($order->order_product);
			$limit = 20 < $order_products_count ? 1 : (int) round(20 / $order_products_count);

			foreach ($order->order_product as $order_product) {
				$brand = (int) $order_product->product->brand;
				$category = (int) $order_product->product->category;

				if ($category != 0 && $category != 1 && $category != 2 && $category != 3) {
					$is_parent = Category::isParent($category);
					$category_children = [];

					if ($is_parent) {
						$children = Category::find($category)->childs()->get();

						foreach ($children as $child) {
							array_push($category_children, $child->id);
						}
					} else {
						$children = Category::find($category)->parent->childs;

						foreach ($children as $child) {
							array_push($category_children, $child->id);
						}

						if (($key = array_search($category, $category_children)) !== false) {
							unset($category_children[$key]);
						}
					}
				}

				if ($brand && $category != 1) {
					$products = Product::where('brand', $brand)->whereIn('category', $category_children)->latest()->take($limit)->get();
				} elseif ($brand) {
					$products = Product::where('brand', $brand)->latest()->take($limit)->get();
				} elseif ($category != 1) {
					$products = Product::where('category', $category)->latest()->take($limit)->get();
				}

				if (count($products) > 0) {
					$params['customer_id'] = $order->customer_id;

					$chat_message = ChatMessage::create($params);

					foreach ($products as $product) {
						$chat_message->attachMedia($product->getMedia(config('constants.media_tags'))->first(), config('constants.media_tags'));
					}

					// CommunicationHistory::create([
					// 	'model_id'		=> $order->id,
					// 	'model_type'	=> Order::class,
					// 	'type'				=> 'order-suggestion',
					// 	'method'			=> 'whatsapp'
					// ]);
				}
			}
		}

		$order->refund_answer = 'yes';
		$order->refund_answer_date = Carbon::now();
		$order->save();



		return redirect()->back()->withSuccess('You have successfully sent suggestions!');







		// }
	}

	public function sendDelivery(Request $request)
	{
		$params = [
			'number'      => NULL,
			'user_id'     => 6,
			'approved'    => 0,
			'status'      => 1,
		];

		$customer = Customer::find($request->customer_id);
		$message = 'We offer these shipping times: ';
		$express_shipping = '';
		$normal_shipping = '';
		$in_stock = 0;
		$normal_products = 0;

		foreach ($request->selected_product as $key => $product_id) {
			$product = Product::find($product_id);

			if ($product->supplier == 'In-stock') {
				$express_shipping .= $in_stock == 0 ? $product->name : ", $product->name";
				$in_stock++;
			} else {
				$normal_shipping .= $normal_products == 0 ? $product->name : ", $product->name";
				$normal_products++;
			}
		}

		if ($in_stock >= 1) {
			$express_shipping .= " - within 3 days in India with additional cost; ";
		}

		if ($normal_products >= 1) {
			$normal_shipping .= " - minimum 10 days - no additional cost; ";
		}

		$message .= $express_shipping . $normal_shipping;

		$params['customer_id'] = $customer->id;
		$params['message'] = $message;

		$chat_message = ChatMessage::create($params);

		// try {
		// app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $message, false, $chat_message->id);
		// } catch {
		//   // ok
		// }
		//
		// $chat_message->update([
		//   'approved'  => 1
		// ]);

		// CommunicationHistory::create([
		// 	'model_id'		=> $request->order_id,
		// 	'model_type'	=> Order::class,
		// 	'type'				=> 'order-delivery-info',
		// 	'method'			=> 'whatsapp'
		// ]);

		$histories = CommunicationHistory::where('model_id', $customer->id)->where('model_type', Customer::class)->where('type', 'initiate-followup')->where('is_stopped', 0)->get();

		foreach ($histories as $history) {
			$history->is_stopped = 1;
			$history->save();
		}

		CommunicationHistory::create([
			'model_id'		=> $customer->id,
			'model_type'	=> Customer::class,
			'type'				=> 'initiate-followup',
			'method'			=> 'whatsapp'
		]);

		return response('success');
	}

	public function updateStatus(Request $request, $id)
	{
		$order = Order::find($id);

		StatusChange::create([
			'model_id'    => $order->id,
			'model_type'  => Order::class,
			'user_id'     => Auth::id(),
			'from_status' => $order->order_status,
			'to_status'   => $request->status
		]);

		$order->order_status = $request->status;
		$order->save();

		// if ($order->auto_messaged == 0) {
		if (!$order->is_sent_initial_advance() && $order->order_status == 'Proceed without Advance' && $order->order_type == 'online') {
			$product_names = '';
			foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
				$product_names .= $order_product->product ? $order_product->product->name . ", " : '';
			}

			$auto_message = "We have received your COD order for $product_names and we will deliver the same by " . ($order->estimated_delivery_date ? Carbon::parse($order->estimated_delivery_date)->format('d \of\ F') : Carbon::parse($order->order_date)->addDays(15)->format('d \of\ F')) . '.';
			$followup_message = "Ma'am please also note that since your order was placed on c o d - an initial advance needs to be paid to process the order - pls let us know how you would like to make this payment.";
			$requestData = new Request();
			$requestData2 = new Request();
			$requestData->setMethod('POST');
			$requestData2->setMethod('POST');
			$requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message]);
			$requestData2->request->add(['customer_id' => $order->customer->id, 'message' => $followup_message]);

			app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');
			app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData2, 'customer');

			CommunicationHistory::create([
				'model_id'		=> $order->id,
				'model_type'	=> Order::class,
				'type'				=> 'initial-advance',
				'method'			=> 'whatsapp'
			]);
		} elseif (!$order->is_sent_online_confirmation() && $order->order_status == 'Prepaid') {
			$auto_message = "Greetings from Solo Luxury. We have received your order. This is our whatsapp number to assist you with order related queries. You can contact us between 9.00 am - 5.30 pm on 0008000401700. Thank you.";
			$requestData = new Request();
			$requestData->setMethod('POST');
			$requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message]);

			app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');

			CommunicationHistory::create([
				'model_id'		=> $order->id,
				'model_type'	=> Order::class,
				'type'				=> 'online-confirmation',
				'method'			=> 'whatsapp'
			]);
		}

		if ($order->order_status == 'Refund to be processed') {
			$refund = Refund::where('order_id', $order->id)->first();

			if (!$refund) {
				Refund::create([
					'customer_id'			=> $order->customer->id,
					'order_id'				=> $order->id,
					'type'						=> 'Cash',
					'date_of_request'	=> Carbon::now(),
					'date_of_issue' 	=> Carbon::now()->addDays(10)
				]);
			}

			if ($order->payment_mode == 'paytm') {
				if ($order->customer) {
					$all_amount = 0;

					if ($order->order_product) {
						foreach ($order->order_product as $order_product) {
							$all_amount += $order_product->product_price;
						}
					}

					$order->customer->credit += $all_amount;
					$order->customer->save();
				}
			} else if ($order->payment_mode != 'paytm' || $order->advance_detail > 0) {
				if ($order->customer) {
					$order->customer->credit += $order->advance_detail;
					$order->customer->save();
				}
			}
		}
	}

	public function sendRefund(Request $request, $id)
	{
		$order = Order::find($id);

		if (!$order->is_sent_refund_initiated()) {
			$product_names = '';
			foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
				$product_names .= $order_product->product ? $order_product->product->name . ", " : '';
			}

			$auto_message = "Greetings from Solo Luxury Ref: order number #$order->order_id for $product_names we have initiated the refund process you should receive the refund within 7-10 days by the method that you made the payment.";
			$requestData = new Request();
			$requestData->setMethod('POST');
			$requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message]);

			app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');

			CommunicationHistory::create([
				'model_id'		=> $order->id,
				'model_type'	=> Order::class,
				'type'				=> 'refund-initiated',
				'method'			=> 'whatsapp'
			]);

			Mail::to($order->customer->email)->send(new RefundProcessed($order->order_id, $product_names));

			$params = [
				'model_id'    		=> $order->customer->id,
				'model_type'  		=> Customer::class,
				'from'        		=> 'customercare@sololuxury.co.in',
				'to'          		=> $order->customer->email,
				'subject'     		=> "Refund Processed",
				'message'     		=> '',
				'template'				=> 'refund-processed',
				'additional_data'	=> json_encode(['order_id' => $order->order_id, 'product_names' => $product_names])
			];

			Email::create($params);

			CommunicationHistory::create([
				'model_id'		=> $order->id,
				'model_type'	=> Order::class,
				'type'				=> 'refund-initiated',
				'method'			=> 'email'
			]);
		}

		return response('success');
	}

	public function generateAWB(Request $request) {
		$options   = array(
			'trace' 							=> 1,
			'style'								=> SOAP_DOCUMENT,
			'use'									=> SOAP_LITERAL,
			'soap_version' 				=> SOAP_1_2
		);

		$soap     = new SoapClient('https://netconnect.bluedart.com/Ver1.8/ShippingAPI/Waybill/WayBillGeneration.svc?wsdl', $options);

		$soap->__setLocation("https://netconnect.bluedart.com/Ver1.8/ShippingAPI/Waybill/WayBillGeneration.svc");

		$soap->sendRequest = true;
		$soap->printRequest = false;
		$soap->formatXML = true;

		$actionHeader = new \SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IWayBillGeneration/GenerateWayBill',true);

		$soap->__setSoapHeaders($actionHeader);

		$order = Order::find($request->order_id);

		$order->customer->name = $request->customer_name;
		$order->customer->address = $request->customer_address1;
		$order->customer->city = $request->customer_address2;
		$order->customer->pincode = $request->customer_pincode;

		$order->customer->save();

		$pickup_datetime = explode(' ', $request->pickup_time);
		$pickup_date = $pickup_datetime[0];
		$pickup_time = str_replace(':', '', $pickup_datetime[1]);

		$total_price = 0;

		foreach ($order->order_product as $product) {
			$total_price += $product->product_price;
		}

		$piece_count = $order->order_product()->count();

		$actual_weight = $request->box_width * $request->box_length * $request->box_height / 5000;

		$params = array(
		'Request' =>
			array (
				'Consignee' =>
					array (
						'ConsigneeAddress1' => $order->customer->address,
						'ConsigneeAddress2' => $order->customer->city,
						'ConsigneeMobile'=> $order->customer->phone,
						'ConsigneeName'=> $order->customer->name,
						'ConsigneePincode'=> $order->customer->pincode,
					)	,
				'Services' =>
					array (
						'ActualWeight' => $actual_weight,

						'CreditReferenceNo' => $order->id,
						'PickupDate' => $pickup_date,
						'PickupTime' => $pickup_time,
						'PieceCount' => $piece_count,
						// 'DeclaredValue'	=> $total_price,
						'DeclaredValue'	=> 500,
						'ProductCode' => 'D',
						'ProductType' => 'Dutiables',

						'Dimensions' =>
							array (
								'Dimension' =>
									array (
										'Breadth' => $request->box_width,
										'Count' => $piece_count,
										'Height' => $request->box_height,
										'Length' => $request->box_length
									),
							),
					),
					'Shipper' =>
						array(
							'CustomerAddress1' => '807, Hubtown Viva, Western Express Highway, Shankarwadi, Andheri East',
							'CustomerAddress2' => 'Mumbai',
							'CustomerCode' => '382200',
							'CustomerMobile' => '022-62363488',
							'CustomerName' => 'Solo Luxury',
							'CustomerPincode' => '400060',
							'IsToPayCustomer' => '',
							'OriginArea' => 'BOM'
						)
			),
			'Profile' =>
				 array(
				 	'Api_type' => 'S',
					'LicenceKey'=>env('BLUEDART_LICENSE_KEY'),
					'LoginID'=>env('BLUEDART_LOGIN_ID'),
					'Version'=>'1.3')
					);

		$result = $soap->__soapCall('GenerateWayBill', [$params])->GenerateWayBillResult;

		if ($result->IsError) {
			if (is_array($result->Status->WayBillGenerationStatus)) {
				$error = '';
				foreach ($result->Status->WayBillGenerationStatus as $error_object) {
					$error .= $error_object->StatusInformation . '. ';
				}
			} else {
				$error = $result->Status->WayBillGenerationStatus->StatusInformation;
			}
			// dd($error);
			return redirect()->back()->with('error', "$error");
		} else {
			Storage::disk('files')->put('waybills/' . $order->id . '_package_slip.pdf', $result->AWBPrintContent);

			$waybill = new Waybill;
			$waybill->order_id = $order->id;
			$waybill->awb = $result->AWBNo;
			$waybill->box_width = $request->box_width;
			$waybill->box_height = $request->box_height;
			$waybill->box_length = $request->box_length;
			$waybill->actual_weight = $actual_weight;
			$waybill->package_slip = $order->id . '_package_slip.pdf';
			$waybill->pickup_date = $request->pickup_time;
			$waybill->save();
		}

		return redirect()->back()->with('success', 'You have successfully generated AWB!');
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

	public function missedCalls() {

        $callBusyMessages = CallBusyMessage::select( 'call_busy_messages.id','twilio_call_sid' ,'message', 'recording_url' ,  'call_busy_messages.created_at')
        // ->join("leads", "leads.id", "call_busy_messages.lead_id")
        ->orderBy('id', 'DESC')->paginate(20)->toArray();


		foreach ($callBusyMessages['data'] as $key => $value){

			if (is_numeric($value['twilio_call_sid'])) {
				# code...
				$formatted_phone = str_replace('+91', '', $value['twilio_call_sid']);
			$customer_array = Customer::where('phone', 'LIKE', "%$formatted_phone%")->get()->toArray();
				 if(!empty($customer_array)){
					 $callBusyMessages['data'][$key]['customerid'] = $customer_array[0]['id'];
				 	$callBusyMessages['data'][$key]['customer_name'] = $customer_array[0]['name'];
				 	if(!empty( $customer_array[0]['lead'])){
				 	$callBusyMessages['data'][$key]['lead_id'] = $customer_array[0]['lead']['id'];
				 }
			}

		}
    }
       return view( 'orders.missed_call', compact( 'callBusyMessages') );

}

public function callsHistory() {
	$calls = CallHistory::latest()->paginate(Setting::get('pagination'));

	return view('orders.call_history', [
		'calls'	=> $calls
	]);
}

public function createProductOnMagento(Request $request, $id){
	$order = Order::find($id);
	$total_special_price = 0;

	foreach ($order->order_product as $order_product) {
		$total_special_price += $order_product->product_price;

		if ($order_product->product->category != 1) {
			$category = Category::find($order_product->product->category);
			$url_structure = [];
			$category_id = $category->magento_id;

			if ($category->parent) {
				$parent = $category->parent;
				$url_structure[0] = $parent->title;
				$category_id = $parent->magento_id;

				if ($parent->parent) {
					$second_parent = $parent->parent;
					$url_structure[0] = $second_parent->title;
					$url_structure[1] = $parent->title;
				}
			}
		}

		// $categories = CategoryController::getCategoryTreeMagentoIds($product->category);
	}

	dd($url_structure, $category_id);

	// dd($order->order_product);

	$options = array(
		'trace' => true,
		'connection_timeout' => 120,
		'wsdl_cache' => WSDL_CACHE_NONE,
	);

	$proxy = new \SoapClient(config('magentoapi.url'), $options);
	$sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

	// $sku = $product->sku . $product->color;
	// $categories = CategoryController::getCategoryTreeMagentoIds($product->category);
	//
	// $brand= $product->brands()->get();
	// array_push($categories,$brand[0]->magento_id);
	//
	// $associated_skus = [];
	// $sizes_array = explode( ',', $product->size );
	//
	// foreach ( $sizes_array as $size ) {
	//
	// 	$productData = array(
	// 		'categories'            => $categories,
	// 		'name'                  => $product->name,
	// 		'description'           => '<p></p>',
	// 		'short_description'     => $product->short_description,
	// 		'website_ids'           => array(1),
	// 		// Id or code of website
	// 		'status'                => 2,
	// 		// 1 = Enabled, 2 = Disabled
	// 		'visibility'            => 1,
	// 		// 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
	// 		'tax_class_id'          => 2,
	// 		// Default VAT
	// 		'weight'                => 0,
	// 		'stock_data' => array(
	// 			'use_config_manage_stock' => 1,
	// 			'manage_stock' => 1,
	// 		),
	// 		'price'                 => $product->price_inr,
	// 		// Same price than configurable product, no price change
	// 		'special_price'         => $product->price_special,
	// 		'additional_attributes' => array(
	// 			'single_data' => array(
	// 				array( 'key' => 'composition', 'value' => $product->composition, ),
	// 				array( 'key' => 'color', 'value' => $product->color, ),
	// 				array( 'key' => 'sizes', 'value' => $size, ),
	// 				array( 'key' => 'country_of_manufacture', 'value' => $product->made_in, ),
	// 				array( 'key' => 'brands', 'value' => BrandController::getBrandName( $product->brand ), ),
	// 			),
	// 		),
	// 	);
	// 	// Creation of product simple
	// 	$result            = $proxy->catalogProductCreate( $sessionId, 'simple', 14, $sku . '-' . $size, $productData );
	// 	$associated_skus[] = $sku . '-' . $size;
	// }

	/**
	 * Configurable product
	 */
	$productData = array(
		'categories'              => $category_id,
		'name'                    => 'Test Product from ERP',
		'description'             => '<p></p>',
		'short_description'       => 'Short Test Description from ERP',
		'website_ids'             => array(1),
		// Id or code of website
		'status'                  => 1,
		// 1 = Enabled, 2 = Disabled
		'visibility'              => 1,
		// 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
		'tax_class_id'            => 2,
		// Default VAT
		'weight'                  => 0,
		'stock_data' => array(
			'use_config_manage_stock' => 1,
			'manage_stock' => 1,
		),
		'price'                   => $total_special_price,
		// Same price than configurable product, no price change
		'special_price'           => '',
		'associated_skus'         => "",
		// Simple products to associate
		'configurable_attributes' => array( 155 ),
		// 'additional_attributes'   => array(
		// 	'single_data' => array(
		// 		array( 'key' => 'composition', 'value' => $product->composition, ),
		// 		array( 'key' => 'color', 'value' => $product->color, ),
		// 		array( 'key' => 'country_of_manufacture', 'value' => $product->made_in, ),
		// 		array( 'key' => 'brands', 'value' => BrandController::getBrandName( $product->brand ), ),
		// 	),
		// ),
	);
	// Creation of configurable product
	$result = $proxy->catalogProductCreate($sessionId, 'configurable', 14, "CUSTOMPRO$order->id", $productData);


	// $images = $product->getMedia(config('constants.media_tags'));
	//
	// $i = 0;
	// $productId = $result;
	//
	// foreach ($images as $image){
	//
	// 	$image->getUrl();
	//
	// 	$file = array(
	// 		'name' => $image->getBasenameAttribute(),
	// 		'content' => base64_encode(file_get_contents($image->getAbsolutePath())),
	// 		'mime' => mime_content_type($image->getAbsolutePath())
	// 	);
	//
	// 	$types = $i ? array('') : array('size_guide','image','small_image','thumbnail','hover_image');
	//
	// 	$result = $proxy->catalogProductAttributeMediaCreate(
	// 		$sessionId,
	// 		$productId,
	// 		array('file' => $file, 'label' => $image->getBasenameAttribute() , 'position' => ++$i , 'types' => $types, 'exclude' => 0)
	// 	);
	// }
	$product_url = "https://www.sololuxury.co.in/$url_structure[0]/$url_structure[1]/show-all/test-product-from-erp-$result.html";
	dd($product_url, $result);
	return $result;
}

}
