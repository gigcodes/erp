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
use App\Task;
use App\ReadOnly\OrderStatus as OrderStatus;
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
  					 $sortby = 'id';
  		}

  		$purchases = ((new Purchase())->newQuery());

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

    public function purchaseGrid(Request $request)
    {
      if ($request->status[0] != null) {
        $status = $request->status[0];

  			$orders = OrderProduct::select('sku')->with('Order')->whereHas('Order', function($q) use ($status) {
          $q->where('order_status', $status);
        })->get()->toArray();
  		} else {
        $orders = OrderProduct::select('sku')->get()->toArray();
      }

      $products = Product::whereIn('sku', $orders);
      $term = $request->input('term');
      $status = isset($status) ? $status : '';
      $order_status = (new OrderStatus)->all();

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
      $products = $products->get()->sortBy('supplier');

      foreach($products as $key => $product) {
        $new_products[$key]['id'] = $product->id;
        $new_products[$key]['supplier'] = $product->supplier;
        $new_products[$key]['image'] = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
      }

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      $perPage = Setting::get('pagination');
      $currentItems = array_slice($new_products, $perPage * ($currentPage - 1), $perPage);

      $leads_array = new LengthAwarePaginator($currentItems, count($new_products), $perPage, $currentPage, [
        'path'  => LengthAwarePaginator::resolveCurrentPath()
      ]);

      return view('purchase.purchase-grid')->with([
        'products'      => $leads_array,
        'order_status'  => $order_status,
        'term'          => $term,
        'status'        => $status
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
        'supplier'          => 'required',
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
      $purchase->bill_number = $request->bill_number;
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
