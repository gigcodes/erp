<?php

namespace App\Http\Controllers;

use App\Product;
use App\Setting;
use App\Stage;
use App\Brand;
use App\Category;
use App\Helpers;
use App\ReadOnly\LocationList;
use Dompdf\Css\Style;
use Dompdf\Css\Stylesheet;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InventoryImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductInventoryController extends Controller
{
	public function __construct() {

//		$this->middleware('permission:inventory-list',['only' => ['index']]);
//		$this->middleware('permission:inventory-edit',['only' => ['edit','stock']]);
	}


	public function index(Stage $stage){

		$products = Product::latest()
											->where('stock', '>=', 1)
//		                   ->where('stage','>=',$stage->get('Approver') )
		                   ->whereNull('dnf')
											 ->select(['id', 'sku', 'size', 'price_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at']);

                        $products_count = $products->count();
		                   $products = $products->paginate(Setting::get('pagination'));

		$roletype = 'Inventory';

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected(1)
		                                        ->renderAsDropdown();

		return view('partials.grid',compact('products', 'products_count', 'roletype', 'category_selection'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function list(Request $request, Stage $stage)
	{
		$category_tree = [];

		foreach (Category::all() as $category) {
			if ($category->parent_id != 0) {
				$parent = $category->parent;
				if ($parent->parent_id != 0) {
					$category_tree[$parent->parent_id][$parent->id][$category->id];
				} else {
					$category_tree[$parent->id][$category->id] = 0;
				}
			}
		}

		// dd($category_tree);

		$brands_array = Brand::getAll();
		$products_brands = Product::latest()
		                   ->where('stage','>=',$stage->get('Approver') )
											 ->whereNull('dnf')
											 ->where('stock', '>=', 1)->get()
											 ->groupBy([function ($query) use ($brands_array) {
												 if (isset($brands_array[$query->brand])) {
													 return $brands_array[$query->brand];
												 }

												 return 'Unknown Brand';
											 }, 'supplier', 'category']);

		// dd($products_brands);

		$inventory_data = [];

		foreach ($products_brands as $brand_name => $suppliers) {
			foreach ($suppliers as $supplier_name => $categories) {
				$tree = [];
				$inventory_data[$brand_name][$supplier_name] = $category_tree;

				foreach ($categories as $category_id => $products) {
					$category = Category::find($category_id);
					if ($category !== NULL && $category->parent_id != 0) {
						$parent = $category->parent;
						if (isset($parent->parent_id) && $parent->parent_id != 0) {
							$inventory_data[$brand_name][$supplier_name][$parent->parent_id][$parent->id] += count($products);
						} else {
							$inventory_data[$brand_name][$supplier_name][$parent->id][$category->id] += count($products);
						}
					}
				}
			}
		}

		// dd($inventory_data);

		$categories_array = [];
		$categories = Category::all();

		foreach ($categories as $category) {
			$categories_array[$category->id] = $category->title;
		}

 		return view('products.list',compact('inventory_data', 'categories_array'))
 			->with('i', (request()->input('page', 1) - 1) * 10);
	}

	public function edit(Product $productlister){

		return redirect( route('products.show',$productlister->id) );
	}

	public function stock(Product $product,Request $request,Stage $stage){


		$this->validate($request,[
			'stock' => 'required|numeric|min:0'
		]);

		$result = $this->magentoSoapUpdateStock($product,$request->input( 'stock' ));
		$product->stock = $request->input( 'stock' );
		$product->stage = $stage->get( 'Inventory' );
		$product->save();

		if( $result ) {

			//		NotificaitonContoller::store('has Final Approved',['Admin'],$product->id);
			ActivityConroller::create( $product->id, 'inventory', 'create' );

			return back()->with( 'success', 'Product inventory has been updated' );
		}

		return back()->with('error','Error Occured while uploading stock');
	}

	public function instock(Request $request)
	{
		$data     = [];
		$term     = $request->input( 'term' );
		$data['term']     = $term;

		if ($request->brand[0] != null) {
			$productQuery = ( new Product() )->newQuery()->latest()->whereIn('brand', $request->brand);

			$data['brand'] = $request->brand[0];
		}

		if ($request->color[0] != null) {
			if ($request->brand[0] != null) {
				$productQuery = $productQuery->whereIn('color', $request->color);
			} else {
				$productQuery = ( new Product() )->newQuery()
				                                 ->latest()->whereIn('color', $request->color);
			}

			$data['color'] = $request->color[0];
		}

		if (isset($request->category) && $request->category[0] != 1) {
			$is_parent = Category::isParent($request->category[0]);
			$category_children = [];

			if ($is_parent) {
				$childs = Category::find($request->category[0])->childs()->get();

				foreach ($childs as $child) {
					$is_parent = Category::isParent($child->id);

					if ($is_parent) {
						$children = Category::find($child->id)->childs()->get();

						foreach ($children as $chili) {
							array_push($category_children, $chili->id);
						}
					} else {
						array_push($category_children, $child->id);
					}
				}
			} else {
				array_push($category_children, $request->category[0]);
			}

			if ($request->brand[0] != null || $request->color[0] != null) {
				$productQuery = $productQuery->whereIn('category', $category_children);
			} else {
				$productQuery = (new Product())->newQuery()
				                                 ->latest()->whereIn('category', $category_children);
			}

			$data['category'] = $request->category[0];
		}

		if (isset($request->price) && $request->price != null) {
			$exploded = explode(',', $request->price);
			$min = $exploded[0];
			$max = $exploded[1];

			if ($min != '0' || $max != '10000000') {
				if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1) {
					$productQuery = $productQuery->whereBetween('price_special', [$min, $max]);
				} else {
					$productQuery = ( new Product() )->newQuery()
					                                 ->latest()->whereBetween('price_special', [$min, $max]);
				}
			}

			$data['price'][0] = $min;
			$data['price'][1] = $max;
		}

		if ($request->location[0] != null) {
			if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1 || $request->price != "0,10000000") {
				$productQuery = $productQuery->whereIn('location', $request->location);

			} else {
				$productQuery = ( new Product() )->newQuery()->latest()
				                                 ->whereIn('location', $request->location);
			}
			$data['location'] = $request->location[0];
		}

		if ($request->no_locations) {
			if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1 || $request->price != "0,10000000" || $request->location[0] != null) {
				$productQuery = $productQuery->whereNull('location');

			} else {
				$productQuery = ( new Product() )->newQuery()->latest()
				                                 ->whereNull('location');
			}
			$data['no_locations'] = true;
		}

		if (trim($term) != '') {
			$productQuery = (( new Product() )->newQuery())
			                                 ->latest()->where(function ($query) use ($term){
															 	    		return $query->orWhere( 'sku', 'LIKE', "%$term%" )
			                                 							->orWhere( 'id', 'LIKE', "%$term%" );
																									});


			if ( $term == - 1 ) {
				$productQuery = $productQuery->where(function ($query){
				 															return $query->orWhere( 'isApproved', - 1 );
									 });
			}

			if ( Brand::where('name', 'LIKE' ,"%$term%")->first() ) {
				$brand_id = Brand::where('name', 'LIKE' ,"%$term%")->first()->id;
				$productQuery = $productQuery->where(function ($query) use ($brand_id){
																			return $query->orWhere( 'brand', 'LIKE', "%$brand_id%" );});
			}

			if ( $category = Category::where('title', 'LIKE' ,"%$term%")->first() ) {
				$category_id = $category = Category::where('title', 'LIKE' ,"%$term%")->first()->id;
				$productQuery = $productQuery->where(function ($query) use ($term){
								return $query->orWhere( 'category', CategoryController::getCategoryIdByName( $term ));} );
			}

		} else {
			if ($request->brand[0] == null && $request->color[0] == null && (!isset($request->category) || $request->category[0] == 1) && (!isset($request->price) || $request->price == "0,10000000") && $request->location[0] == null && !isset($request->no_locations)) {
				$productQuery = ( new Product() )->newQuery()
				                                 ->latest();

			}
		}

		// $search_suggestions = [];
		//
		// $sku_suggestions = ( new Product() )->newQuery()->where('supplier', 'In-stock')
		// 																	 ->latest()->whereNotNull('sku')->select('sku')->get()->toArray();
		//
		// $brand_suggestions = Brand::getAll();
		//
		// foreach ($sku_suggestions as $key => $suggestion) {
		// 	array_push($search_suggestions, $suggestion['sku']);
		// }
		//
		// foreach ($brand_suggestions as $key => $suggestion) {
		// 	array_push($search_suggestions, $suggestion);
		// }
		//
		// $data['search_suggestions'] = $search_suggestions;

		$selected_categories = $request->category ? $request->category : 1;

		$data['category_selection'] = Category::attr(['name' => 'category[]','class' => 'form-control'])
		                                        ->selected($selected_categories)
		                                        ->renderAsDropdown();


//		$data['products'] = $productQuery->paginate( Setting::get( 'pagination' ) );
		
		if ($request->get('shoe_size', false)) {
            $productQuery = $productQuery->where('products.size', 'like', "%".$request->get('shoe_size')."%");
        }

        if ($request->get('in_pdf') === 'on') {
            $data[ 'products' ] = $productQuery->whereRaw( "(products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id = 11) OR (location IS NOT NULL AND location != ''))" )->get();
        } else {
            $data[ 'products' ] = $productQuery->whereRaw( "(products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id = 11) OR (location IS NOT NULL AND location != ''))" )->paginate( Setting::get( 'pagination' ) );
        }

		$data['date'] = $request->date ? $request->date : '';
		$data['type'] = $request->type ? $request->type : '';
		$data['customer_id'] = $request->customer_id ? $request->customer_id : '';
		$data['locations'] = (new \App\ProductLocation())->pluck('name');

		$data['new_category_selection'] = Category::attr(['name' => 'category','class' => 'form-control', 'id' => 'product-category'])
		                                        ->renderAsDropdown();

		$data['category_tree'] = [];
		$data['categories_array'] = [];

		foreach (Category::all() as $category) {
			if ($category->parent_id != 0) {
				$parent = $category->parent;
				if ($parent->parent_id != 0) {
					$data['category_tree'][$parent->parent_id][$parent->id][$category->id];
				} else {
					$data['category_tree'][$parent->id][$category->id] = $category->id;
				}
			}

			$data['categories_array'][$category->id] = $category->parent_id;
		}

		if ($request->ajax()) {
			$html = view('instock.product-items', $data)->render();
			return response()->json(['html' => $html]);
		}

        if ($request->get('in_pdf') === 'on') {
		    set_time_limit(0);
            $html = view( 'instock.instock_pdf', $data );

            $pdf = new Dompdf();
            $pdf->loadHtml($html);
            $pdf->render();
            $pdf->stream('instock.pdf');
            return;
        }

		return view( 'instock.index', $data );
	}

	public function inDelivered(Request $request)
	{
		$data     = [];
		$term     = $request->input( 'term' );
		$data['term']     = $term;

		$productQuery = ( new Product() )->newQuery()->latest();
		if ($request->brand[0] != null) {
			$productQuery = $productQuery->whereIn('brand', $request->brand);
			$data['brand'] = $request->brand[0];
		}

		if ($request->color[0] != null) {
			$productQuery = $productQuery->whereIn('color', $request->color);
			$data['color'] = $request->color[0];
		}

		if (isset($request->category) && $request->category[0] != 1) {
			$is_parent = Category::isParent($request->category[0]);
			$category_children = [];

			if ($is_parent) {
				$childs = Category::find($request->category[0])->childs()->get();

				foreach ($childs as $child) {
					$is_parent = Category::isParent($child->id);

					if ($is_parent) {
						$children = Category::find($child->id)->childs()->get();

						foreach ($children as $chili) {
							array_push($category_children, $chili->id);
						}
					} else {
						array_push($category_children, $child->id);
					}
				}
			} else {
				array_push($category_children, $request->category[0]);
			}

			$productQuery = $productQuery->whereIn('category', $category_children);

			$data['category'] = $request->category[0];
		}

		if (isset($request->price) && $request->price != null) {
			$exploded = explode(',', $request->price);
			$min = $exploded[0];
			$max = $exploded[1];

			if ($min != '0' || $max != '10000000') {
				$productQuery = $productQuery->whereBetween('price_special', [$min, $max]);
			}

			$data['price'][0] = $min;
			$data['price'][1] = $max;
		}

		
		if (trim($term) != '') {
			$productQuery = $productQuery->where(function ($query) use ($term){
 	    		$query->orWhere( 'sku', 'LIKE', "%$term%" )
					  ->orWhere( 'id', 'LIKE', "%$term%" );
			});


			if ( $term == - 1 ) {
				$productQuery = $productQuery->where(function ($query){
				 															return $query->orWhere( 'isApproved', - 1 );
									 });
			}

			if ( Brand::where('name', 'LIKE' ,"%$term%")->first() ) {
				$brand_id = Brand::where('name', 'LIKE' ,"%$term%")->first()->id;
				$productQuery = $productQuery->where(function ($query) use ($brand_id){
																			return $query->orWhere( 'brand', 'LIKE', "%$brand_id%" );});
			}

			if ( $category = Category::where('title', 'LIKE' ,"%$term%")->first() ) {
				$category_id = $category = Category::where('title', 'LIKE' ,"%$term%")->first()->id;
				$productQuery = $productQuery->where(function ($query) use ($term){
								return $query->orWhere( 'category', CategoryController::getCategoryIdByName( $term ));} );
			}

		}

		$selected_categories = $request->category ? $request->category : 1;

		$data['category_selection'] = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple2'])
		                                        ->selected($selected_categories)
		                                        ->renderAsDropdown();


//		$data['products'] = $productQuery->paginate( Setting::get( 'pagination' ) );
		
		if ($request->get('shoe_size', false)) {
            $productQuery = $productQuery->where('products.size', 'like', "%".$request->get('shoe_size')."%");
        }

        $data[ 'products' ] = $productQuery->where('products.purchase_status', '=', 'Delivered')->paginate( Setting::get( 'pagination' ) );

		return view( 'indelivered.index', $data );
	}

	public function magentoSoapUpdateStock($product,$stockQty){

		$options = array(
			'trace' => true,
			'connection_timeout' => 120,
			'wsdl_cache' => WSDL_CACHE_NONE,
		);
		$proxy = new \SoapClient(config('magentoapi.url'), $options);
		$sessionId = $proxy->login(config('
		api.user'), config('magentoapi.password'));

		$sku = $product->sku . $product->color;
		$result = false;

//		$result = $proxy->catalogProductUpdate($sessionId, $sku , array('visibility' => 4));

		if(!empty($product->size)){

			$sizes_array = explode( ',', $product->size );

			foreach ($sizes_array as $size) {
				$error_message = '';

				try {
					$result = $proxy->catalogInventoryStockItemUpdate( $sessionId, $sku . '-' . $size, array(
						'qty'         => $stockQty,
						'is_in_stock' => $stockQty ? 1 : 0
					) );
				} catch (\Exception $e) {
					$error_message = $e->getMessage();
				}

				if ($error_message == 'Product not exists.') {
                      $product->isUploaded = 0;
                      $product->isFinal = 0;
					$product->save();
				}
			}

			$error_message = '';
			try {
				$result = $proxy->catalogInventoryStockItemUpdate( $sessionId, $sku, array(
	//				'qty'         => 0,
					'is_in_stock' => $stockQty ? 1 : 0
				) );
			} catch (\Exception $e) {
				$error_message = $e->getMessage();
			}

			if ($error_message == 'Product not exists.') {
				$product->isUploaded = 0;
				$product->isFinal = 0;
				$product->save();
			}
		}
		else {
			$error_message = '';

			try {
				$result = $proxy->catalogInventoryStockItemUpdate( $sessionId, $sku, array(
					'qty'         => $stockQty,
					'is_in_stock' => $stockQty ? 1 : 0
				) );
			} catch (\Exception $e) {
				$error_message = $e->getMessage();
			}

			if ($error_message == 'Product not exists.') {
				$product->isUploaded = 0;
				$product->isFinal = 0;
				$product->save();
			}
		}

		return $result;
	}

	public function import(Request $request)
	{
		$this->validate($request, [
			'file'	=> 'required'
		]);

		$array = (new InventoryImport)->toArray($request->file('file'));

		$new_array = [];
		$brands_array = Helpers::getUserArray(Brand::all());

		foreach ($array[0] as $key => $item) {
			$new_array[$item['modellovariante']][] = $item;
		}

		foreach ($new_array as $sku => $items) {
			$formatted_sku = str_replace(' ', '', $sku);

			if ($product = Product::where('sku', $formatted_sku)->first()) {
				if (in_array($items[0]['brand'], $brands_array)) {
					if (count($items) > 1) {
						$sizes = '';
						$product->stock = 1;
						$product->import_date = Carbon::now();
						$product->status = 3; // Import Update status

						foreach ($items as $key => $item) {
							$size = str_replace('½', '.5', $item['taglia']);

							if ($key == 0) {
								$sizes .= $size;
							} else {
								$sizes .= "," . $size;
							}
						}

						if (!preg_match('/UNI/', $sizes)) {
							$product->size = $sizes;
						}

						$product->save();
					} else {
						$product->stock = 1;
						$product->import_date = Carbon::now();
						$product->status = 3; // Import Update status

						foreach ($items as $key => $item) {
							$size = str_replace('½', '.5', $item['taglia']);
						}

						if (!preg_match('/UNI/', $size)) {
							$product->size = $size;
						}

						$product->save();
					}
				}
			} else {
				if (in_array($items[0]['brand'], $brands_array)) {
					if (count($items) > 1) {
						$sizes = '';
						$product = new Product;
						$product->sku = $formatted_sku;
						$product->brand = array_search($items[0]['brand'], $brands_array);
						$product->stage = 3;
						$product->stock = 1;
						$product->import_date = Carbon::now();
						$product->status = 2; // Import Create status

						foreach ($items as $key => $item) {
							$size = str_replace('½', '.5', $item['taglia']);

							if ($key == 0) {
								$sizes .= $size;
							} else {
								$sizes .= "," . $size;
							}
						}

						if (!preg_match('/UNI/', $sizes)) {
							$product->size = $sizes;
						}

						$product->save();
					} else {
						$product = new Product;
						$product->sku = $formatted_sku;
						$product->brand = array_search($items[0]['brand'], $brands_array);
						$product->stage = 3;
						$product->stock = 1;
						$product->import_date = Carbon::now();
						$product->status = 2; // Import Create status

						foreach ($items as $key => $item) {
							$size = str_replace('½', '.5', $item['taglia']);
						}

						if (!preg_match('/UNI/', $size)) {
							$product->size = $sizes;
						}

						$product->save();
					}
				}
			}
		}

		return back()->with('success', 'You have successfully imported Inventory');
	}

	public function instructionCreate()
	{

		$productId = request()->get("product_id",0);
		$users = \App\User::all()->pluck("name","id");
		$product = \App\Product::where("id",$productId)->first();
		$locations = \App\ProductLocation::all()->pluck("name","name");
		$couriers = \App\Courier::all()->pluck("name","name");
		$order = [];
		if($product) {
		   $order = \App\OrderProduct::where("sku",$product->sku)
		   ->join("orders as o","o.id","order_products.order_id")
		   ->select(["o.id",\DB::raw("concat(o.id,' => ',o.client_name) as client_name")])->pluck("client_name",'id');
		}

		$reply_categories = \App\ReplyCategory::all();

		return view("instock.instruction_create",compact(['productId','users','customers','order','locations','couriers', 'reply_categories']));

	}

	public function instruction()
	{
		$params =  request()->all();

		// validate incoming request
        
        $validator = Validator::make($params, [
           'product_id' => 'required',
           'location_name' => 'required',
           'instruction_type' => 'required',
           'instruction_message' => 'required',
           'courier_name' => 'required',
           'courier_details' => 'required',
           'date_time' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json(["code" => 0, "errors" => $validator->messages()]);
        }

        // start to store first location as per the request
		$product = \App\Product::where("id",$params["product_id"])->first();
		$instruction = new \App\Instruction();

		if($params['instruction_type'] == "dispatch") {
			$orderId = request()->get("order_id",0);
			if($orderId > 0) {
				$order = \App\Order::where("id",$params["order_id"])->first();
				if($order) {
				  	
				  	$instruction->customer_id = $order->customer_id;
				  	$order->order_status = "Delivered";
				  	$order->save();

				  	if($order->customer) {
				  		$customer = $order->customer;
				  		//$product->location =  null;
					    //$product->save();
				  	}
				}
			}

			$assign_to = request()->get("assign_to",0);

			if($assign_to > 0) {
				$user = \App\User::where('id',$assign_to)->first();
			}
			// if customer object found then send message
			if(!empty($user)) {
				$messageData = implode("\n",[
			  		"We have dispatched your parcel",
			  		$params["courier_name"],
			  		$params["courier_details"]	
			  	]);

			    $params['approved'] = 1;
			    $params['message']  = $messageData;
			    $params['status']   = 2;
			    $params['user_id'] = $user->id;

			    app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone,$user->whatsapp_number,$messageData);
			    $chat_message = \App\ChatMessage::create($params);
			    if ($product->hasMedia(config('constants.media_tags'))) {
	                foreach ($product->getMedia(config('constants.media_tags')) as $image) {
	                	app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone,$user->whatsapp_number,null, $image->getUrl());
	                    $chat_message->attachMedia($image, config('constants.media_tags'));
	                }
	            }
			}

		}elseif ($params['instruction_type'] == "location") {
			if($product) {
				$product->location = $params["location_name"];
				$product->save();

				$user = \App\User::where("id",$params["assign_to"])->first();
				if($user) {
					// send location message 
					$messageData = implode("\n",[
				  		$params['instruction_message'],
				  		$params["courier_name"],
				  		$params["courier_details"]	
				  	]);

				    $params['approved'] = 1;
				    $params['message']  = $messageData;
				    $params['status']   = 2;
				    $params['user_id'] = $user->id;

				    app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone,$user->whatsapp_number,$messageData);
				    $chat_message = \App\ChatMessage::create($params);
				    if ($product->hasMedia(config('constants.media_tags'))) {
		                foreach ($product->getMedia(config('constants.media_tags')) as $image) {
		                	app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone,$user->whatsapp_number,null, $image->getUrl());
		                    $chat_message->attachMedia($image, config('constants.media_tags'));
		                }
		            }
				}
			}
		}

		$instruction->category_id = 7;
		$instruction->instruction = $params["instruction_message"];
		$instruction->assigned_from = \Auth::user()->id;
		$instruction->assigned_to = $params["assign_to"];
		$instruction->product_id = $params["product_id"];
		$instruction->order_id = isset($params["order_id"]) ? $params["order_id"] : 0;
		$instruction->save();


		$productHistory = new \App\ProductLocationHistory();
		$productHistory->fill($params);	
		$productHistory->created_by = \Auth::user()->id;
		$productHistory->save();


		return response()->json(["code" => 1, "message" => "Done"]);	


	}

	public function locationHistory()
	{
		$productId = request()->get("product_id",0);
		$history = \App\ProductLocationHistory::where("product_id",$productId)
		->orderBy("date_time","desc")
		->get();
		return view("instock.history_list",compact(['history']));
	}

	public function dispatchCreate()
	{

		$productId = request()->get("product_id",0);
		//$users = \App\User::all()->pluck("name","id");
		//$product = \App\Product::where("id",$productId)->first();

		return view("instock.dispatch_create",compact(['productId','users','order']));

	}

	public function dispatchStore(Request $request)
	{
		$validator = Validator::make($request->all(), [
           'product_id' => 'required',
           'modeof_shipment' => 'required',
           'awb' => 'required',
           'eta' => 'required',
           //'date_time' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json(["code" => 0, "errors" => $validator->messages()]);
        }

        $productDispatch = new \App\ProductDispatch;
        $productDispatch->fill($request->all());
        $productDispatch->save();

        $uploaded_images = [];	

        if ($request->hasFile('file')) {
            try{
                foreach ($request->file('file') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('dispatch-images')->upload();
                    array_push($uploaded_images, $media);
                    $productDispatch->attachMedia($media,config('constants.media_tags'));
                }
            }catch (\Exception $exception){
               // return response($exception->getMessage(), $exception->getCode());
            }
        }
        
        if ($request->get('product_id') > 0 ) {
        	$product = \App\Product::where("id",$request->get('product_id'))->first();
	  		$product->purchase_status =  'Delivered';
	  		$product->location =  null;
		    $product->save();
        	$instruction = \App\Instruction::where('product_id', $request->get('product_id'))->where('customer_id', '>', '0')->orderBy('id', 'desc')->first();
			if ($instruction) {

				$customer = \App\Customer::where('id',$instruction->customer_id)->first();

				// if customer object found then send message
				if(!empty($customer)) {
					$params = [];
					$messageData = implode("\n",[
				  		"We have dispatched your parcel",
				  		$request->awb,
				  		$request->modeof_shipment	
				  	]);

				    $params['approved'] = 1;
				    $params['message']  = $messageData;
				    $params['status']   = 2;
				    $params['customer_id'] = $customer->id;

				    app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($customer->phone,$customer->whatsapp_number,$messageData);
				    $chat_message = \App\ChatMessage::create($params);

				    if ($productDispatch->hasMedia(config('constants.media_tags'))) {
		                foreach ($productDispatch->getMedia(config('constants.media_tags')) as $image) {
		                	app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($customer->phone,$customer->whatsapp_number,null, $image->getUrl());
		                    $chat_message->attachMedia($image, config('constants.media_tags'));
		                }
		            }
				    
				}
			}
		}

		

        return response()->json(["code" => 1, "message" => "Done"]);

	}
}
