<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\Setting;
use App\Purchase;
use App\Helpers;
use App\User;
use App\Comment;
use App\Reply;
use App\Message;
use App\ReplyCategory;
use App\Task;
use App\ReadOnly\OrderStatus as OrderStatus;
use App\ReadOnly\SupplierList;
use App\ReadOnly\PurchaseStatus;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseController extends Controller
{
    public function __construct() {

      $this->middleware( 'permission:purchase');
    }

    public function index(Request $request)
    {
      $term = $request->input('term');

  		if($request->input('orderby') == '')
  				$orderby = 'desc';
  		else
  				$orderby = 'asc';

  		switch ($request->input('sortby')) {
  			case 'date':
  					 $sortby = 'created_at';
  					break;
  			case 'purchase_handler':
  					 $sortby = 'purchase_handler';
  					break;
        case 'supplier':
  					 $sortby = 'supplier';
  					break;
  			case 'status':
  					 $sortby = 'status';
  					break;
  			case 'communication':
  					 $sortby = 'communication';
  					break;
  			default :
  					 $sortby = 'created_at';
  		}

  		$purchases = (new Purchase())->newQuery()->with(['Products' => function ($query) {
        $query->with(['orderproducts' => function ($quer) {
          $quer->with(['Order' => function ($q) {
            $q->with('customer');
          }]);
        }]);
      }]);

  		if ($sortby != 'communication') {
  			$purchases = $purchases->orderBy( $sortby, $orderby );
  		}

  		if(empty($term))
  			$purchases = $purchases->latest();
  		else{

  			$purchases = $purchases->latest()
  			               ->orWhere('id','like','%'.$term.'%')
  			               ->orWhere('purchase_handler',Helpers::getUserIdByName($term))
  			               ->orWhere('supplier','like','%'.$term.'%')
                       ->orWhere('status','like','%'.$term.'%');
  		}



  		$users  = Helpers::getUserArray( User::all() );

  		$purchases_array = $purchases->whereNull( 'deleted_at' )->get()->toArray();
      // dd($purchases_array);

  		if ($sortby == 'communication') {
  			if ($orderby == 'asc') {
  				$purchases_array = array_values(array_sort($purchases_array, function ($value) {
  						return $value['communication']['created_at'];
  				}));

  				$purchases_array = array_reverse($purchases_array);
  			} else {
  				$purchases_array = array_values(array_sort($purchases_array, function ($value) {
  						return $value['communication']['created_at'];
  				}));
  			}
  		}

  		$currentPage = LengthAwarePaginator::resolveCurrentPage();
  		$perPage = 10;
  		$currentItems = array_slice($purchases_array, $perPage * ($currentPage - 1), $perPage);

  		$purchases_array = new LengthAwarePaginator($currentItems, count($purchases_array), $perPage, $currentPage, [
  			'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		]);

  		return view( 'purchase.index', compact('purchases_array','term', 'orderby', 'users' ) );
    }

    public function purchaseGrid(Request $request, $page = null)
    {
      $purchases = Purchase::all();
      $not_include_products = [];

      foreach ($purchases as $purchase) {
        foreach ($purchase->products as $product) {
          $not_include_products[] = $product->sku;
        }
      }

      if ($request->status[0] != null) {
        $status = $request->status;

  			$orders = OrderProduct::select('sku')->with('Order')->whereHas('Order', function($q) use ($status) {
          $q->whereIn('order_status', $status);
        })->get();
  		}

      if ($request->supplier[0] != null) {
        $supplier = $request->supplier[0];

        if ($request->status[0] != null) {
          $orders = OrderProduct::select('sku')->with(['Order', 'Product'])->whereHas('Order', function($q) use ($status) {
            $q->whereIn('order_status', $status);
          })->whereHas('Product', function($q) use ($supplier) {
            $q->where('supplier', $supplier);
          })->get();
        } else {
          $orders = OrderProduct::select('sku')->with(['Order', 'Product']);

          if ($page == 'canceled') {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereIn('order_status', ['Cancel']);
            });
          } elseif ($page == 'refunded') {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereIn('order_status', ['Refund to be processed']);
            });
          } else {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereNotIn('order_status', ['Cancel', 'Refund to be processed']);
            });
          }

          $orders = $orders->whereHas('Product', function($q) use ($supplier) {
            $q->where('supplier', $supplier);
          })->get();
        }
      }

      if ($request->brand[0] != null) {
        $brand = $request->brand[0];

        if ($request->status[0] != null) {
          $orders = OrderProduct::select('sku')->with(['Order', 'Product'])->whereHas('Order', function($q) use ($status) {
            $q->whereIn('order_status', $status);
          })->whereHas('Product', function($q) use ($brand) {
            $q->where('brand', $brand);
          })->get();
        } else {
          $orders = OrderProduct::select('sku')->with(['Order', 'Product']);

          if ($page == 'canceled') {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereIn('order_status', ['Cancel']);
            });
          } elseif ($page == 'refunded') {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereIn('order_status', ['Refund to be processed']);
            });
          } else {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereNotIn('order_status', ['Cancel', 'Refund to be processed']);
            });
          }

          $orders = $orders->whereHas('Product', function($q) use ($brand) {
            $q->where('brand', $brand);
          })->get();
        }
      }

      if ($request->status[0] == null && $request->supplier[0] == null && $request->brand[0] == null) {
        if ($page == 'canceled') {
          $orders = OrderProduct::with('Order')->whereHas('Order', function($q) {
            $q->whereIn('order_status', ['Cancel']);
          });
        } elseif ($page == 'refunded') {
          $orders = OrderProduct::with('Order')->whereHas('Order', function($q) {
            $q->whereIn('order_status', ['Refund to be processed']);
          });
        } else {
          $orders = OrderProduct::with('Order')->whereHas('Order', function($q) {
            $q->whereNotIn('order_status', ['Cancel', 'Refund to be processed']);
          });
        }

        $orders = $orders->select('sku')->get()->toArray();
      }

      $new_orders = [];
      foreach ($orders as $order) {
        array_push($new_orders, $order['sku']);
      }

      $products = Product::with(['Orderproducts' => function($query) {
        $query->with('Order');
      }])->whereIn('sku', $new_orders)->whereNotIn('sku', $not_include_products);
      $term = $request->input('term');
      $status = isset($status) ? $status : '';
      $supplier = isset($supplier) ? $supplier : '';
      $brand = isset($brand) ? $brand : '';
      $order_status = (new OrderStatus)->all();
      $supplier_list = (new SupplierList)->all();

     if(!empty($term)){
	    	$products = $products->where(function ($query) use ($term){
	    		return $query
					    ->orWhere('name','like','%'.$term.'%')
					    ->orWhere('short_description','like','%'.$term.'%')
              ->orWhere('sku','like','%'.$term.'%')
					    ->orWhere('supplier','like','%'.$term.'%')
			    ;
		    });
	    }

      $new_products = [];
      $products = $products->select(['id', 'sku', 'supplier'])->get()->sortBy('supplier');

      foreach($products as $key => $product) {
        $new_products[$key]['id'] = $product->id;
        $new_products[$key]['sku'] = $product->sku;
        $new_products[$key]['supplier'] = $product->supplier;
        $new_products[$key]['image'] = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
        $new_products[$key]['customer_id'] = $product->orderproducts->first()->order ? ($product->orderproducts->first()->order->customer ? $product->orderproducts->first()->order->customer->id : 'No Customer') : 'No Order';
        $new_products[$key]['customer_name'] = $product->orderproducts->first()->order ? ($product->orderproducts->first()->order->customer ? $product->orderproducts->first()->order->customer->name : 'No Customer') : 'No Order';
        $new_products[$key]['order_price'] = $product->orderproducts->first()->product_price;
        $new_products[$key]['order_date'] = $product->orderproducts->first()->order ? $product->orderproducts->first()->order->order_date : 'No Order';
      }

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      $perPage = Setting::get('pagination');
      $currentItems = array_slice($new_products, $perPage * ($currentPage - 1), $perPage);

      $new_products = new LengthAwarePaginator($currentItems, count($new_products), $perPage, $currentPage, [
        'path'  => LengthAwarePaginator::resolveCurrentPath()
      ]);

      return view('purchase.purchase-grid')->with([
        'products'      => $new_products,
        'order_status'  => $order_status,
        'supplier_list' => $supplier_list,
        'term'          => $term,
        'status'        => $status,
        'supplier'      => $supplier,
        'brand'         => $brand,
        'page'          => $page
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
        'purchase_handler'  => 'required',
        // 'supplier'          => 'required',
        'products'          => 'required'
      ]);

      $purchase = new Purchase;

      $purchase->purchase_handler = $request->purchase_handler;
      $purchase->supplier = $request->supplier;

      $purchase->save();

      $purchase->products()->attach($request->products);

      return redirect()->route('purchase.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $purchase = Purchase::find($id);
  		$data['comments']        = Comment::with('user')->where( 'subject_id', $purchase->id )
  		                                 ->where( 'subject_type','=' ,Order::class )->get();
  		$data['users']          = User::all()->toArray();
  		$messages = Message::all()->where('moduleid', $purchase->id)->where('moduletype','=', 'purchase')->sortByDesc("created_at")->take(10)->toArray();
      $data['messages'] = $messages;
  		$data['tasks'] = Task::where('model_type', 'purchase')->where('model_id', $purchase->id)->get()->toArray();
  		$data['approval_replies'] = Reply::where('model', 'Approval Purchase')->get();
  		$data['internal_replies'] = Reply::where('model', 'Internal Purchase')->get();
      $data['purchase_status'] = (new PurchaseStatus)->all();
      $data['reply_categories'] = ReplyCategory::all();

  		return view('purchase.show', $data)->withOrder($purchase);
    }

    public function productShow($id)
    {
      $product = Product::find($id);

  		$data['users']          = User::all()->toArray();
  		$messages = Message::all()->where('moduleid', $product->id)->where('moduletype','=', 'product')->sortByDesc("created_at")->take(10)->toArray();
      $data['messages'] = $messages;
  		$data['approval_replies'] = Reply::where('model', 'Approval Purchase')->get();
  		$data['internal_replies'] = Reply::where('model', 'Internal Purchase')->get();
      $data['order_details'] = OrderProduct::where('sku', $product->sku)->get(['order_id', 'size']);

  		return view('purchase.product-show', $data)->withProduct($product);
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
      $order = Purchase::find($id);
      $order->status = $request->status;
      $order->save();

      foreach ($order->products as $product) {
        foreach ($product->orderproducts as $order_product) {
          $order_product->purchase_status = $request->status;
          $order_product->save();
        }
      }

      return response($order->status);
    }

    public function updatePercentage(Request $request, $id)
    {
      $product = Product::find($id);
      $product->percentage = $request->percentage;
      $product->factor = $request->factor;
      $product->save();
    }

    public function saveBill(Request $request, $id)
    {
      $purchase = Purchase::find($id);
      $purchase->supplier = $request->supplier;
      $purchase->bill_number = $request->bill_number;
      $purchase->supplier_phone = $request->supplier_phone;
      $purchase->whatsapp_number = $request->whatsapp_number;
      $purchase->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $purchase = Purchase::find($id);

      $purchase->delete();

      return redirect()->route('purchase.index')->with('success','Purchase has been archived');
    }

    public function permanentDelete($id)
    {
      $purchase = Purchase::find($id);

      $purchase->products()->detach();
      $purchase->forceDelete();

      return redirect()->route('purchase.index')->with('success','Purchase has been deleted');
    }

    public function getOrderProductsWithProductData($order_id){


  		$orderProducts = OrderProduct::where('order_id', '=', $order_id)->get()->toArray();
      $temp = [];
  		foreach ($orderProducts as $key => $value){

  			if(!empty($orderProducts[$key]['color'])) {

  				$temp = Product::where( 'sku', '=', $orderProducts[ $key ]['sku'] )
  				                                           ->where( 'color', $orderProducts[ $key ]['color'] )->whereNotNull('supplier_link')
  				                                           ->get()->first();

  			}else{

  				$temp = Product::where( 'sku', '=', $orderProducts[ $key ]['sku'] )->whereNotNull('supplier_link')
  				                                           ->get()->first();
  			}

  			if(!empty($temp)){

  				$orderProducts[ $key ]['product'] = $temp;
  				$orderProducts[ $key ]['product']['image'] = $temp->getMedia(config('constants.media_tags'))->first() ? $temp->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
  			}
  		}

  		return $temp;

  //		return OrderProduct::with( 'product' )->where( 'order_id', '=', $order_id )->get()->toArray();
  	}
}
