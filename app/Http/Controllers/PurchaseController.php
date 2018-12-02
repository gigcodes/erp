<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\Setting;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseController extends Controller
{
    public function __construct() {

      $this->middleware( 'permission:purchase');
    }

    public function index()
    {
        //
    }

    public function purchaseGrid(Request $request)
    {
      $orders = OrderProduct::select('sku')->get()->toArray();
      // dd($orders);
      // dd($orders);
      $products_array = [];
      // $products_all = Product::all()->groupBy('supplier_link');

      //gebnegozionline
      //cuccuini
      //thedoublef
      //toryburch
      //divoboutique

      // foreach($products_all as $index => $product) {
      //   // dd($product, $index);
      //   if (strpos($index, 'gebnegozionline') !== false) {
      //     foreach ($product as $item) {
      //       $item->supplier = 'G&B, Negozi Online';
      //       $item->update();
      //     }
      //   }
      //
      //   if (strpos($index, 'cuccuini') !== false) {
      //     foreach ($product as $item) {
      //       $item->supplier = 'Cuccuini';
      //       $item->update();
      //     }
      //   }
      //
      //   if (strpos($index, 'thedoublef') !== false) {
      //     foreach ($product as $item) {
      //       $item->supplier = 'The DoubleF';
      //       $item->update();
      //     }
      //   }
      //
      //   if (strpos($index, 'toryburch') !== false) {
      //     foreach ($product as $item) {
      //       $item->supplier = 'Tory Burch';
      //       $item->update();
      //     }
      //   }
      //
      //   if (strpos($index, 'divoboutique') !== false) {
      //     foreach ($product as $item) {
      //       $item->supplier = 'Divo Boutique';
      //       $item->update();
      //     }
      //   }
      // }
      // dd('success');


      // foreach ($orders as $key => $order) {
      //   $products_array[$key] = $this->getOrderProductsWithProductData($order->id);
      //   // $orderProducts = OrderProduct::where('order_id', '=', $order->id)->get()->toArray();
      // }

      $term = $request->input('term');


      $products = Product::whereIn('sku', $orders)->whereNotNull('supplier');


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

      $products = $products->get()->sortBy('supplier');
      // dd($products_all);
      // $products_array = array_filter($products_array);
      // dd($products[0]->get);
      $new_products = [];
      foreach($products as $key => $product) {
        $new_products[$key]['id'] = $product->id;
        $new_products[$key]['supplier'] = $product->supplier;
        $new_products[$key]['image'] = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
      }

      // dd($new_products);
      // $new_products = $new_products->groupBy('supplier');

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      $perPage = Setting::get('pagination');
      $currentItems = array_slice($new_products, $perPage * ($currentPage - 1), $perPage);

      $leads_array = new LengthAwarePaginator($currentItems, count($new_products), $perPage, $currentPage, [
        'path'  => LengthAwarePaginator::resolveCurrentPath()
      ]);
      // dd($leads_array);
      // $leads = $leads->whereNull( 'deleted_at' )->paginate( Setting::get( 'pagination' ) );



      // dd($leads_array);

      return view('purchase.purchase-grid')->withProducts($leads_array);
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
