<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\Setting;
use App\Purchase;
use App\Customer;
use App\Helpers;
use App\ChatMessage;
use App\User;
use App\Comment;
use App\Reply;
use App\Message;
use App\ReplyCategory;
use App\CommunicationHistory;
use App\Task;
use App\Brand;
use App\Email;
use App\PurchaseDiscount;
use App\StatusChange;
use App\Mail\CustomerEmail;
use App\Mail\PurchaseEmail;
use App\Supplier;
use App\Agent;
use App\File;
use App\Mail\PurchaseExport;
use Illuminate\Support\Facades\Mail;
use App\Exports\PurchasesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\ReadOnly\OrderStatus as OrderStatus;
use App\ReadOnly\SupplierList;
use App\ReadOnly\PurchaseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Carbon\Carbon;
use Storage;
use Auth;
use Webklex\IMAP\Client;

class PurchaseController extends Controller
{
    public function __construct() {
      $this->middleware( 'permission:purchase');
    }

    public function index(Request $request)
    {
      $term = $request->input('term');

  		if($request->input('orderby') == '')
  				$orderby = 'DESC';
  		else
  				$orderby = 'ASC';

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
      }, 'purchase_supplier']);

      // $purchases_new = DB::table('purchases');



  		if(!empty($term)) {
        $purchases = $purchases
        ->orWhere('id','like','%'.$term.'%')
        ->orWhere('purchase_handler',Helpers::getUserIdByName($term))
        ->orWhere('supplier','like','%'.$term.'%')
        ->orWhere('status','like','%'.$term.'%')
        ->orWhereHas('Products', function ($query) use ($term) {
          $query->where('sku', 'LIKE', "%$term%");
        });
      }


      if ($sortby != 'communication') {
  			$purchases = $purchases->orderBy($sortby, $orderby);
  		}
      // dd($purchases->get());

      // $order_products = DB::table('order_products')->join(DB::raw('(SELECT sku as product_sku FROM `products`)'), 'order_products.sku', '=', 'products.product_sku', 'LEFT');
      // dd($order_products->get());
      // // dd(DB::raw('(SELECT product_id, purchase_id as FROM `purchase_products` GROUP BY purchase_id) as products'))->get();
      // $purchases_new = $purchases_new->join(DB::raw('(SELECT products.id, products.name FROM purchase_products INNER JOIN products ON purchases.id=products.id'), 'purchase_products.purchase_id', '=', 'purchases.id', 'LEFT');
      // dd($purchases_new->get());
      // $purchases_new = $purchases_new->join(DB::raw('(SELECT MAX(id) as chat_message_id, chat_messages.purchase_id as purid, MAX(chat_messages.created_at) as chat_message_created_at FROM chat_messages WHERE chat_messages.status != 7 AND chat_messages.status != 8 GROUP BY chat_messages.purchase_id ORDER BY chat_messages.created_at ' . $orderby . ') as chat_messages'), 'chat_messages.purid', '=', 'purchases.id', 'LEFT');
      // $purchases_new = $purchases_new->join(DB::raw('(SELECT MAX(id) as message_id, messages.moduleid as mcid, MAX(messages.created_at) as message_created_at FROM messages WHERE messages.moduletype = "purchase" GROUP BY messages.moduleid ORDER BY messages.created_at ' . $orderby . ') as messages'), 'messages.mcid', '=', 'purchases.id', 'LEFT');
      //
      // $purchases_new = $purchases_new->selectRaw('purchases.id, purchases.purchase_handler, CASE WHEN messages.message_created_at > chat_messages.chat_message_created_at THEN messages.message_created_at ELSE chat_messages.chat_message_created_at END AS last_communicated_at,
      // CASE WHEN messages.message_created_at > chat_messages.chat_message_created_at THEN (SELECT mmm.body FROM messages mmm WHERE mmm.id = message_id) ELSE (SELECT mm2.message FROM chat_messages mm2 WHERE mm2.id = chat_message_id) END AS message,
      // CASE WHEN messages.message_created_at > chat_messages.chat_message_created_at THEN (SELECT mm3.status FROM messages mm3 WHERE mm3.id = message_id) ELSE (SELECT mm4.status FROM chat_messages mm4 WHERE mm4.id = chat_message_id) END AS message_status')->paginate(24);
      //
      // dd($purchases_new);


  		$users  = Helpers::getUserArray( User::all());

  		$purchases_array = $purchases->select(['id', 'purchase_handler', 'supplier', 'supplier_id', 'status', 'created_at'])->get()->toArray();
      // dd($purchases_array);
  		// if ($sortby == 'communication') {
  		// 	if ($orderby == 'asc') {
  		// 		$purchases_array = array_values(array_sort($purchases_array, function ($value) {
  		// 				return $value['communication']['created_at'];
  		// 		}));
      //
  		// 		$purchases_array = array_reverse($purchases_array);
  		// 	} else {
  		// 		$purchases_array = array_values(array_sort($purchases_array, function ($value) {
  		// 				return $value['communication']['created_at'];
  		// 		}));
  		// 	}
  		// }

  		$currentPage = LengthAwarePaginator::resolveCurrentPage();
  		$perPage = 10;
  		$currentItems = array_slice($purchases_array, $perPage * ($currentPage - 1), $perPage);

  		$purchases_array = new LengthAwarePaginator($currentItems, count($purchases_array), $perPage, $currentPage, [
  			'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		]);

      $suppliers = Supplier::select(['id', 'supplier'])->get();
      $agents = Agent::where('model_type', 'App\Supplier')->get();
      $agents_array = [];

      foreach ($agents as $agent) {
        $agents_array[$agent->model_id][$agent->id] = $agent->name . " - " . $agent->email;
      }

      if ($request->ajax()) {
  			$html = view('purchase.purchase-item', ['purchases_array' => $purchases_array, 'orderby' => $orderby, 'users'  => $users])->render();

  			return response()->json(['html' => $html]);
  		}

  		return view( 'purchase.index', compact('purchases_array','term', 'orderby', 'users', 'suppliers', 'agents_array' ) );
    }

    public function purchaseGrid(Request $request, $page = null)
    {
      $purchases = Purchase::select('id');
      $not_include_products = [];

      foreach ($purchases as $purchase) {
        foreach ($purchase->products as $product) {
          $not_include_products[] = $product->sku;
        }
      }

      if ($request->status[0] != null && $request->supplier[0] == null && $request->brand[0] == null) {
        $status = $request->status;
        $status_list = implode("','", $request->status);

  			$orders = OrderProduct::select('sku')->with('Order')
        ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status IN ('$status_list'))")
        ->get();
  		}



      if ($request->supplier[0] != null) {
        $supplier = $request->supplier[0];
        $supplier_list = implode(',', $request->supplier);

        if ($request->status[0] != null) {
          $status_list = implode("','", $request->status);

          $orders = OrderProduct::select(['sku', 'order_id'])->with(['Order', 'Product'])
          ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status IN ('$status_list'))")
          // ->whereRaw("order_products.sku IN (SELECT products.sku FROM (SELECT products.id FROM products WHERE IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($supplier_list))) WHERE products.sku = order_products.sku)")
          ->whereHas('Product', function ($qs) use ($supplier_list) {
            $qs->whereRaw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($supplier_list))");
          })->get();
        } else {
          $orders = OrderProduct::select('sku')->with(['Order', 'Product']);

          if ($page == 'canceled-refunded') {
            $orders = $orders
            ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status IN ('Cancel', 'Refund to be processed'))");
            // ->whereHas('Order', function($q) {
            //   $q->whereIn('order_status', ['Cancel', 'Refund to be processed']);
            // });
          } elseif ($page == 'ordered') {

          } elseif ($page == 'delivered') {
            $orders = $orders
            ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status IN ('Delivered'))");
            // ->whereHas('Order', function($q) {
            //   $q->whereIn('order_status', ['Delivered']);
            // });
          } else {
            $orders = $orders
            ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status NOT IN ('Cancel', 'Refund to be processed', 'Delivered'))");
            // ->whereHas('Order', function($q) {
            //   $q->whereNotIn('order_status', ['Cancel', 'Refund to be processed', 'Delivered']);
            // });

          }

          $orders = $orders
          ->whereRaw("order_products.sku IN (SELECT products.sku FROM products WHERE id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($supplier_list)))")
          // ->whereHas('Product', function($q) use ($supplier_list) {
          //   $q->whereRaw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($supplier_list))");
          // })
          ->get();
          // dd($orders);
        }
      }



      if ($request->brand[0] != null) {
        $brand = $request->brand[0];

        if ($request->status[0] != null || $request->supplier[0] != null) {
          $orders = OrderProduct::select('sku')->with(['Order', 'Product'])
          ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status IN ('$status_list'))")
          // ->whereHas('Order', function($q) use ($status) {
          //   $q->whereIn('order_status', $status);
          // })
          ->whereHas('Product', function($q) use ($brand) {
            $q->where('brand', $brand);
          })->get();
        } else {
          $orders = OrderProduct::select('sku')->with(['Order', 'Product']);

          if ($page == 'canceled-refunded') {
            $orders = $orders
            ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status IN ('Cancel', 'Refund to be processed'))");
            // ->whereHas('Order', function($q) {
            //   $q->whereIn('order_status', ['Cancel', 'Refund to be processed']);
            // });
          } elseif ($page == 'ordered') {

          } elseif ($page == 'delivered') {
            $orders = $orders
            ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status IN ('Delivered'))");
            // ->whereHas('Order', function($q) {
            //   $q->whereIn('order_status', ['Delivered']);
            // });
          } else {
            $orders = $orders
            ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status NOT IN ('Cancel', 'Refund to be processed', 'Delivered'))");
            // ->whereHas('Order', function($q) {
            //   $q->whereNotIn('order_status', ['Cancel', 'Refund to be processed', 'Delivered']);
            // });
          }

          $orders = $orders->whereHas('Product', function($q) use ($brand) {
            $q->where('brand', $brand);
          })->get();
        }
      }



      if ($request->status[0] == null && $request->supplier[0] == null && $request->brand[0] == null) {
        if ($page == 'canceled-refunded') {
          $orders = OrderProduct::with('Order')
          ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status IN ('Cancel', 'Refund to be processed'))");
          // ->whereHas('Order', function($q) {
          //   $q->whereIn('order_status', ['Cancel', 'Refund to be processed']);
          // });
        } elseif ($page == 'ordered') {
          $orders = OrderProduct::with('Order');
        } elseif ($page == 'delivered') {
          $orders = OrderProduct::with('Order')
          ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status IN ('Delivered'))");
          // ->whereHas('Order', function($q) {
          //   $q->whereIn('order_status', ['Delivered']);
          // });
        } else {
          $orders = OrderProduct::with('Order')
          ->whereRaw("order_products.order_id IN (SELECT orders.id FROM orders WHERE orders.order_status NOT IN ('Cancel', 'Refund to be processed', 'Delivered'))");
          // ->whereHas('Order', function($q) {
          //   $q->whereNotIn('order_status', ['Cancel', 'Refund to be processed', 'Delivered']);
          // });
        }

        $orders = $orders->select('sku')->get()->toArray();
      }



      $new_orders = [];
      foreach ($orders as $order) {
        array_push($new_orders, $order['sku']);
      }

      $products = Product::with(['Orderproducts' => function($query) {
        $query->with('Order');
      }, 'Purchases', 'Suppliers'])->whereIn('sku', $new_orders);



      if ($page == 'ordered') {
        $products = $products->whereHas('Purchases', function ($query) {
          $query->where('status', 'Ordered');
        });
      } else {
        $products = $products->whereNotIn('sku', $not_include_products);
      }



      $term = $request->input('term');
      $status = isset($status) ? $status : '';
      $supplier = isset($supplier) ? $supplier : '';
      $brand = isset($brand) ? $brand : '';
      $order_status = (new OrderStatus)->all();
      $supplier_list = (new SupplierList)->all();
      $suppliers = Supplier::select(['id', 'supplier'])->whereHas('products')->get();

      $suppliers_array = [];

      foreach ($suppliers as $supp) {
        $suppliers_array[$supp->id] = $supp->supplier;
      }

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
      $count = 0;
      
      foreach($products as $key => $product) {
        $supplier_list = '';
        $single_supplier = '';
        foreach ($product->suppliers as $key2 => $supplier) {
          if ($key2 == 0) {
            $supplier_list .= "$supplier->supplier";
          } else {
            $supplier_list .= ", $supplier->supplier";
          }

          $single_supplier = $supplier->id;
        }

        $customer_names = '';

        foreach ($product->orderproducts as $key => $order_product) {
          if ($order_product->order && $order_product->order->customer) {
            if ($count == 0) {
              $customer_names .= $order_product->order->customer->name;
            } else {
              $customer_names .= ", " . $order_product->order->customer->name;
            }
          }
        }

        $new_products[$count]['id'] = $product->id;
        $new_products[$count]['sku'] = $product->sku;
        $new_products[$count]['supplier'] = $product->supplier;
        $new_products[$count]['supplier_list'] = $supplier_list;
        $new_products[$count]['single_supplier'] = $single_supplier;
        $new_products[$count]['image'] = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
        $new_products[$count]['customer_id'] = $product->orderproducts->first()->order ? ($product->orderproducts->first()->order->customer ? $product->orderproducts->first()->order->customer->id : 'No Customer') : 'No Order';
        $new_products[$count]['customer_names'] = $customer_names;
        $new_products[$count]['order_price'] = $product->orderproducts->first()->product_price;
        $new_products[$count]['order_date'] = $product->orderproducts->first()->order ? $product->orderproducts->first()->order->order_date : 'No Order';

        $count++;
      }

      $new_products = array_values(array_sort($new_products, function ($value) {
        return $value['order_date'];
      }));

      $new_products = array_reverse($new_products);

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
        'suppliers_array' => $suppliers_array,
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

    public function export(Request $request)
    {
      $selected_purchases = json_decode($request->selected_purchases);
      $path = "purchase_exports/" . Carbon::now()->format('Y-m-d') . "_purchases_export.xlsx";

      Excel::store(new PurchasesExport($selected_purchases), $path, 'files');

      $first_agent_email = '';
      $cc_agents_emails = [];
      foreach ($request->agent_id as $key => $agent_id) {
        $agent = Agent::find($agent_id);

        if ($key == 0) {
          $first_agent_email = $agent->email;
        } else {
          $cc_agents_emails[] = $agent->email;
        }
      }

      Mail::to($agent->email)->cc($cc_agents_emails)->bcc('yogeshmordani@icloud.com')->send(new PurchaseExport($path, $request->subject, $request->message));

      $params = [
        'model_id'        => $request->supplier_id,
        'model_type'      => Supplier::class,
        'from'            => 'buying@amourint.com',
        'to'              => $first_agent_email,
        'subject'         => $request->subject,
        'message'         => $request->message,
        'template'				=> 'purchase-simple',
        'additional_data'	=> json_encode(['attachment' => $path])
      ];

      Email::create($params);

      foreach ($selected_purchases as $purchase_id) {
        $purchase = Purchase::find($purchase_id);
        $purchase->status = 'Request Sent to Supplier';
        $purchase->save();
      }

      return Storage::disk('files')->download($path);

      // return redirect()->route('purchase.index')->with('success', 'You have successfully exported purchases');
    }

    public function downloadFile(Request $request, $id)
    {
      $file = File::find($id);

      return Storage::disk('files')->download('files/' . $file->filename);
    }

    public function downloadAttachments(Request $request)
    {
      return Storage::disk('files')->download($request->path);
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
      $purchase->supplier_id = $request->supplier_id;

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
      $data['emails'] = [];
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
      $data['suppliers'] = Supplier::all();
      $data['purchase_discounts'] = PurchaseDiscount::where('purchase_id', $id)->where('type', 'product')->latest()->take(3)->get()->groupBy([function($query) {
        return Carbon::parse($query->created_at)->format('Y-m-d H:i:s');
      }, 'product_id']);

      $data['purchase_discounts_rest'] = PurchaseDiscount::where('purchase_id', $id)->where('type', 'product')->latest()->skip(3)->take(30)->get()->groupBy([function($query) {
        return Carbon::parse($query->created_at)->format('Y-m-d H:i:s');
      }, 'product_id']);

      $data['agents_array'] = [];
      $agents = Agent::all();

      foreach ($agents as $agent) {
        $data['agents_array'][$agent->model_id][$agent->id] = $agent->name . " - " . $agent->email;
      }

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

    public function productReplace(Request $request)
    {
      $old_product = Product::find($request->moduleid);
      $new_product = Product::find(json_decode($request->images)[0]);

      foreach ($old_product->purchases as $purchase) {
        $purchase->products()->detach($old_product);
        $purchase->products()->attach($new_product);
      }

      foreach ($old_product->orderproducts as $order_product) {
        $new_order = new OrderProduct;
        $new_order->order_id = $order_product->order_id;
        $new_order->sku = $new_product->sku;
        $new_order->product_price = $new_product->price_special;
        $new_order->size = $order_product->size;
        $new_order->color = $order_product->color;
        $new_order->save();

        $order_product->delete();
      }

      PurchaseDiscount::where('product_id', $old_product->id)->delete();

      return redirect()->route('purchase.index')->with('success', 'You have successfully replaced product!');
    }

    public function productRemove(Request $request, $id)
    {
      $product = Product::find($id);
      $purchase = Purchase::find($request->purchase_id);

      $purchase->products()->detach($product);

      PurchaseDiscount::where('product_id', $id)->delete();

      return redirect()->route('purchase.show', $request->purchase_id)->with('success', 'You have successfully removed product!');
    }

    public function productCreateReplace(Request $request)
    {
      $this->validate($request, [
  			'sku' => 'required|unique:products'
  		]);

  		$product = new Product;

  		$product->name = $request->name;
  		$product->sku = $request->sku;
  		$product->size = $request->size;
  		$product->brand = $request->brand;
  		$product->color = $request->color;
  		$product->supplier = $request->supplier;
  		$product->price = $request->price;

  		$brand = Brand::find($request->brand);

  		if ($request->price) {
  			if(isset($request->brand) && !empty($brand->euro_to_inr))
  				$product->price_inr = $brand->euro_to_inr * $product->price;
  			else
  				$product->price_inr = Setting::get('euro_to_inr') * $product->price;

  			$product->price_inr = round($product->price_inr, -3);
  			$product->price_special = $product->price_inr - ($product->price_inr * $brand->deduction_percentage) / 100;

  			$product->price_special = round($product->price_special, -3);
  		}

  		$product->save();

  		$product->detachMediaTags(config('constants.media_tags'));
  		$media = MediaUploader::fromSource($request->file('image'))->upload();
  		$product->attachMedia($media,config('constants.media_tags'));

      $old_product = Product::find($request->product_id);

      foreach ($old_product->purchases as $purchase) {
        $purchase->products()->detach($old_product);
        $purchase->products()->attach($product);
      }

      foreach ($old_product->orderproducts as $order_product) {
        $new_order = new OrderProduct;
        $new_order->order_id = $order_product->order_id;
        $new_order->sku = $product->sku;
        $new_order->product_price = $product->price_special;
        $new_order->size = $order_product->size;
        $new_order->color = $order_product->color;
        $new_order->save();

        $order_product->delete();
      }

      PurchaseDiscount::where('product_id', $old_product->id)->delete();

      return redirect()->back()->with('success', 'You have successfully created and replaced product!');
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
      $purchase = Purchase::find($id);

      StatusChange::create([
        'model_id'    => $purchase->id,
        'model_type'  => Purchase::class,
        'user_id'     => Auth::id(),
        'from_status' => $purchase->status,
        'to_status'   => $request->status
      ]);

      $purchase->status = $request->status;
      $purchase->save();

      if ($request->status == 'In Transit from Italy to Dubai') {
        if ($purchase->products) {
          foreach ($purchase->products as $product) {
            $supplier = Supplier::where('supplier', 'In-stock')->first();

            $product->supplier = 'In-stock';
            $product->location = 'Italy';
            $product->save();

            $product->suppliers()->syncWithoutDetaching($supplier);

            if ($product->orderproducts) {
              $params = [
                 'number'       => NULL,
                 'user_id'      => Auth::id(),
                 'approved'     => 0,
                 'status'       => 1,
                 'message'      => 'Your Order is shipped from Italy'
               ];

              foreach ($product->orderproducts as $order_product) {
                if ($order_product->order && !$purchase->is_sent_in_italy()) {
                  $params['customer_id'] = $order_product->order->customer->id;

                  ChatMessage::create($params);

                  CommunicationHistory::create([
            				'model_id'		=> $purchase->id,
            				'model_type'	=> Purchase::class,
            				'type'				=> 'purchase-in-italy',
            				'method'			=> 'whatsapp'
            			]);
                }
              }
            }
          }
        }
      }

      if ($request->status == 'Shipment Received in Dubai' || $request->status == 'Shipment in Transit from Dubai to India') {
        if ($purchase->products) {
          foreach ($purchase->products as $product) {
            $supplier = Supplier::where('supplier', 'In-stock')->first();

            $product->supplier = 'In-stock';
            $product->location = 'Dubai';
            $product->save();

            $product->suppliers()->syncWithoutDetaching($supplier);

            if ($product->orderproducts) {
              $params = [
                 'number'       => NULL,
                 'user_id'      => Auth::id(),
                 'approved'     => 0,
                 'status'       => 1,
                 'message'      => 'Your Order is received in Dubai and is being shipped to Dubai'
               ];

              foreach ($product->orderproducts as $order_product) {
                if ($order_product->order && !$purchase->is_sent_in_dubai()) {
                  $params['customer_id'] = $order_product->order->customer->id;

                  ChatMessage::create($params);

                  CommunicationHistory::create([
            				'model_id'		=> $purchase->id,
            				'model_type'	=> Purchase::class,
            				'type'				=> 'purchase-in-dubai',
            				'method'			=> 'whatsapp'
            			]);
                }
              }
            }
          }
        }
      }

      if ($request->status == 'Shipment Received in India') {
        if ($purchase->products) {
          foreach ($purchase->products as $product) {
            $supplier = Supplier::where('supplier', 'In-stock')->first();

            $product->location = 'Mumbai';
            $product->save();

            $product->suppliers()->syncWithoutDetaching($supplier);

            if ($product->orderproducts) {
              $params = [
                 'number'       => NULL,
                 'user_id'      => Auth::id(),
                 'approved'     => 0,
                 'status'       => 1,
                 'message'      => 'Your Order is received in India'
               ];

              foreach ($product->orderproducts as $order_product) {
                if ($order_product->order && !$purchase->is_sent_in_mumbai()) {
                  $params['customer_id'] = $order_product->order->customer->id;

                  ChatMessage::create($params);

                  CommunicationHistory::create([
            				'model_id'		=> $purchase->id,
            				'model_type'	=> Purchase::class,
            				'type'				=> 'purchase-in-mumbai',
            				'method'			=> 'whatsapp'
            			]);
                }
              }
            }
          }
        }
      }

      foreach ($purchase->products as $product) {
        foreach ($product->orderproducts as $order_product) {
          if ($request->status != $order_product->purchase_status) {
            StatusChange::create([
              'model_id'    => $order_product->id,
              'model_type'  => OrderProduct::class,
              'user_id'     => Auth::id(),
              'from_status' => $order_product->purchase_status,
              'to_status'   => $request->status
            ]);
          }

          $order_product->purchase_status = $request->status;
          $order_product->save();
        }

        $product->purchase_status = $purchase->status;
        $product->save();
      }

      return response($purchase->status);
    }

    public function updateProductStatus(Request $request, $id)
    {
      $product = Product::find($request->product_id);
      $product->purchase_status = $request->status;
      $product->save();

      $params = [
        'number'       => NULL,
        'user_id'      => Auth::id(),
        'approved'     => 0,
        'status'       => 1,
        'message'      => 'Your Product is not available with the Supplier. Please choose alternative'
      ];

      foreach ($product->purchases as $purchase) {
        if ($purchase->id == $id) {
          foreach ($purchase->products as $related_product) {
            if ($related_product->id == $product->id) {
              foreach ($product->orderproducts as $order_product) {
                if ($order_product->order) {
                  $params['customer_id'] = $order_product->order->customer->id;

                  ChatMessage::create($params);
                }
              }
            }
          }
        }
      }

      return response('success');
    }

    public function updatePercentage(Request $request, $id)
    {
      foreach ($request->percentages as $percentage) {
        $product = Product::find($percentage[0]);
        $product->percentage = $percentage[1];
        $product->save();

        PurchaseDiscount::create([
          'purchase_id' => $request->purchase_id,
          'product_id'  => $percentage[0],
          'percentage'  => $percentage[1],
          'amount'      => $request->amount,
          'type'        => $request->type
        ]);
      }

      $purchase = Purchase::find($request->purchase_id);
      $purchase->status = 'Price under Negotiation';
      $purchase->save();

      return response('success');
    }

    public function saveBill(Request $request, $id)
    {
      $purchase = Purchase::find($id);
      $purchase->supplier_id = $request->supplier;
      $purchase->agent_id = $request->agent_id;
      $purchase->transaction_id = $request->transaction_id;
      $purchase->transaction_date = $request->transaction_date;
      $purchase->transaction_amount = $request->transaction_amount;
      $purchase->bill_number = $request->bill_number;
      $purchase->shipper = $request->shipper;
      $purchase->shipment_cost = $request->shipment_cost;
      $purchase->shipment_status = $request->shipment_status;
      $purchase->supplier_phone = $request->supplier_phone;
      $purchase->whatsapp_number = $request->whatsapp_number;

      if ($request->bill_number != '') {
        $purchase->status = 'AWB Details Received';
      }

      $purchase->save();

      if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
          $original_name = $file->getClientOriginalName();
          $filename = pathinfo($original_name, PATHINFO_FILENAME);
          $extension = $file->getClientOriginalExtension();

          $full_name = $filename . '.' . $extension;

          $file->storeAs("files", $full_name, 'files');

          $new_file = new File;
          $new_file->filename = $full_name;
          $new_file->model_id = $id;
          $new_file->model_type = Purchase::class;
          $new_file->save();
        }
      }

      return response()->json(['data' => $request->all()]);
    }

    public function confirmProforma(Request $request, $id)
    {
      $purchase = Purchase::find($id);
      $matched = 0;

      foreach ($request->proformas as $data) {
        $product = Product::find($data[0]);
        $discounted_price = round(($product->price - ($product->price * $product->percentage / 100)) / 1.22);
        $proforma = $data[1];

        if (($proforma - $discounted_price) < 10) {
          $matched++;
        }
      }

      if ($matched == count($request->proformas)) {
        $purchase->proforma_confirmed = 1;
        $purchase->proforma_id = $request->proforma_id;
        $purchase->proforma_date = $request->proforma_date;

        $purchase->status = 'Price Confirmed - Payment in Process';
        $purchase->save();
      }

      return response()->json([
        'proforma_confirmed' => $purchase->proforma_confirmed
      ]);
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

    public function emailInbox(Request $request)
    {
      $imap = new Client([
          'host'          => env('IMAP_HOST_PURCHASE'),
          'port'          => env('IMAP_PORT_PURCHASE'),
          'encryption'    => env('IMAP_ENCRYPTION_PURCHASE'),
          'validate_cert' => env('IMAP_VALIDATE_CERT_PURCHASE'),
          'username'      => env('IMAP_USERNAME_PURCHASE'),
          'password'      => env('IMAP_PASSWORD_PURCHASE'),
          'protocol'      => env('IMAP_PROTOCOL_PURCHASE')
      ]);

      $imap->connect();

      $supplier = Supplier::find($request->supplier_id);

      if ($request->type == 'inbox') {
        $inbox_name = 'INBOX';
        $direction = 'from';
        $type = 'incoming';
      } else {
        $inbox_name = 'INBOX.Sent';
        $direction = 'to';
        $type = 'outgoing';
      }

      $inbox = $imap->getFolder($inbox_name);
      $latest_email = Email::where('type', $type)->where('model_id', $supplier->id)->where(function($query) {
        $query->where('model_type', 'App\Supplier')->orWhere('model_type', 'App\Purchase');
      })->latest()->first();

      if ($latest_email) {
        $latest_email_date = Carbon::parse($latest_email->created_at);
      } else {
        $latest_email_date = Carbon::parse('1990-01-01');
      }

      // dd(Carbon::parse($latest_email_date)->format('d M y 11:i:50'));

      if ($supplier->agents) {
        if ($supplier->agents()->count() > 1) {
          foreach ($supplier->agents as $key => $agent) {
            if ($key == 0) {
              $emails = $inbox->messages()->where($direction, $agent->email)->where([
                  ['SINCE', $latest_email_date->format('d M y H:i')]
              ]);
              // $emails = $emails->setFetchFlags(false)
              //                 ->setFetchBody(false)
              //                 ->setFetchAttachment(false)->leaveUnread()->get();

              $emails = $emails->leaveUnread()->get();


              foreach ($emails as $email) {
                if ($email->hasHTMLBody()) {
                  $content = $email->getHTMLBody();
                } else {
                  $content = $email->getTextBody();
                }

                if ($email->getDate()->format('Y-m-d H:i:s') > $latest_email_date->format('Y-m-d H:i:s')) {
                  $params = [
                    'model_id'        => $supplier->id,
                    'model_type'      => Supplier::class,
                    'type'            => $type,
                    'seen'            => $email->getFlags()['seen'],
                    'from'            => $email->getFrom()[0]->mail,
                    'to'              => $email->getTo()[0]->mail,
                    'subject'         => $email->getSubject(),
                    'message'         => $content,
                    'template'				=> 'customer-simple',
          					'additional_data'	=> "",
                    'created_at'      => $email->getDate()
                  ];

                  Email::create($params);
                }
              }
            } else {
              $additional = $inbox->messages()->where($direction, $agent->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
              // $additional = $additional->setFetchFlags(false)
              //                 ->setFetchBody(false)
              //                 ->setFetchAttachment(false)->leaveUnread()->get();

              $additional = $additional->leaveUnread()->get();

              foreach ($additional as $email) {
                if ($email->hasHTMLBody()) {
                  $content = $email->getHTMLBody();
                } else {
                  $content = $email->getTextBody();
                }

                if ($email->getDate()->format('Y-m-d H:i:s') > $latest_email_date->format('Y-m-d H:i:s')) {
                  $params = [
                    'model_id'        => $supplier->id,
                    'model_type'      => Supplier::class,
                    'type'            => $type,
                    'seen'            => $email->getFlags()['seen'],
                    'from'            => $email->getFrom()[0]->mail,
                    'to'              => $email->getTo()[0]->mail,
                    'subject'         => $email->getSubject(),
                    'message'         => $content,
                    'template'				=> 'customer-simple',
          					'additional_data'	=> "",
                    'created_at'      => $email->getDate()
                  ];

                  Email::create($params);
                }
              }

              $emails = $emails->merge($additional);
            }
          }
        } else if ($supplier->agents()->count() == 1) {
          $emails = $inbox->messages()->where($direction, $supplier->agents[0]->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
          // $emails = $emails->setFetchFlags(false)
          //                 ->setFetchBody(false)
          //                 ->setFetchAttachment(false)->leaveUnread()->get();

          $emails = $emails->leaveUnread()->get();

          foreach ($emails as $email) {
            if ($email->hasHTMLBody()) {
              $content = $email->getHTMLBody();
            } else {
              $content = $email->getTextBody();
            }

            if ($email->getDate()->format('Y-m-d H:i:s') > $latest_email_date->format('Y-m-d H:i:s')) {
              $params = [
                'model_id'        => $supplier->id,
                'model_type'      => Supplier::class,
                'type'            => $type,
                'seen'            => $email->getFlags()['seen'],
                'from'            => $email->getFrom()[0]->mail,
                'to'              => $email->getTo()[0]->mail,
                'subject'         => $email->getSubject(),
                'message'         => $content,
                'template'				=> 'customer-simple',
                'additional_data'	=> "",
                'created_at'      => $email->getDate()
              ];

              Email::create($params);
            }
          }
        } else {
          $emails = $inbox->messages()->where($direction, 'nonexisting@email.com');
          $emails = $emails->setFetchFlags(false)
                          ->setFetchBody(false)
                          ->setFetchAttachment(false)->leaveUnread()->get();
        }
      }

      // if ($purchase->purchase_supplier->agents) {
      //   if ($purchase->purchase_supplier->agents()->count() > 1) {
      //     foreach ($purchase->purchase_supplier->agents as $key => $agent) {
      //       if ($key == 0) {
      //         $emails = $inbox->messages()->where($direction, $agent->email);
      //         $emails = $emails->setFetchFlags(false)
      //                         ->setFetchBody(false)
      //                         ->setFetchAttachment(false)->leaveUnread()->get();
      //       } else {
      //         $additional = $inbox->messages()->where($direction, $agent->email);
      //         $additional = $additional->setFetchFlags(false)
      //                         ->setFetchBody(false)
      //                         ->setFetchAttachment(false)->leaveUnread()->get();
      //
      //         $emails = $emails->merge($additional);
      //       }
      //     }
      //   } else if ($purchase->purchase_supplier->agents()->count() == 1) {
      //     $emails = $inbox->messages()->where($direction, $purchase->purchase_supplier->agents[0]->email);
      //     $emails = $emails->setFetchFlags(false)
      //                     ->setFetchBody(false)
      //                     ->setFetchAttachment(false)->leaveUnread()->get();
      //   } else {
      //     $emails = $inbox->messages()->where($direction, 'nonexisting@email.com');
      //     $emails = $emails->setFetchFlags(false)
      //                     ->setFetchBody(false)
      //                     ->setFetchAttachment(false)->leaveUnread()->get();
      //   }
      // }

      $emails_array = [];
      $count = 0;

      // foreach ($emails as $key => $email) {
      //   $emails_array[$key]['uid'] = $email->getUid();
      //   $emails_array[$key]['subject'] = $email->getSubject();
      //   $emails_array[$key]['date'] = $email->getDate();
      //   $emails_array[$key]['from'] = $email->getFrom()[0]->mail;
      //
      //   $count++;
      // }

      if ($request->type == 'inbox') {
        $db_emails = $supplier->emails()->where('type', 'incoming')->get();

        foreach ($db_emails as $key2 => $email) {
          $emails_array[$count + $key2]['id'] = $email->id;
          $emails_array[$count + $key2]['subject'] = $email->subject;
          $emails_array[$count + $key2]['seen'] = $email->seen;
          $emails_array[$count + $key2]['date'] = $email->created_at;
          $emails_array[$count + $key2]['from'] = $email->from;
        }
      } else {
        $db_emails = $supplier->emails()->where('type', 'outgoing')->get();

        foreach ($db_emails as $key2 => $email) {
          $emails_array[$count + $key2]['id'] = $email->id;
          $emails_array[$count + $key2]['subject'] = $email->subject;
          $emails_array[$count + $key2]['seen'] = $email->seen;
          $emails_array[$count + $key2]['date'] = $email->created_at;
          $emails_array[$count + $key2]['from'] = $email->from;
        }
      }

        // dd($emails_array);
        // dd($emails->merge($db_emails));
        // $emails = $emails->merge($db_emails);
        // $emails = collect($emails_array);
        // dd($emails);

      $emails_array = array_values(array_sort($emails_array, function ($value) {
        return $value['date'];
      }));

      $emails_array = array_reverse($emails_array);


      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      $perPage = 10;
      // $perPage = Setting::get('pagination');
      $currentItems = array_slice($emails_array, $perPage * ($currentPage - 1), $perPage);

      $emails = new LengthAwarePaginator($currentItems, count($emails_array), $perPage, $currentPage);

      // $emails = $emails->setFetchFlags(false)
      //                 ->setFetchBody(false)
      //                 ->setFetchAttachment(false)->get();

                      // $emails2 = $emails2->setFetchFlags(false)
                      //                 ->setFetchBody(false)
                      //                 ->setFetchAttachment(false)->get();
                      // $emails = $emails->sortByDesc('date');
                      // // $related = new Collection();
                      // $emails = $emails->merge($emails2);
                      // dd($emails);

      $view = view('purchase.partials.email', [
        'emails'  => $emails,
        'type'    => $request->type
      ])->render();

      return response()->json(['emails' => $view]);
    }

    public function emailFetch(Request $request)
    {
      $imap = new Client([
        'host'          => env('IMAP_HOST_PURCHASE'),
        'port'          => env('IMAP_PORT_PURCHASE'),
        'encryption'    => env('IMAP_ENCRYPTION_PURCHASE'),
        'validate_cert' => env('IMAP_VALIDATE_CERT_PURCHASE'),
        'username'      => env('IMAP_USERNAME_PURCHASE'),
        'password'      => env('IMAP_PASSWORD_PURCHASE'),
        'protocol'      => env('IMAP_PROTOCOL_PURCHASE')
      ]);

      $imap->connect();

      if ($request->type == 'inbox') {
        $inbox = $imap->getFolder('INBOX');
      } else {
        $inbox = $imap->getFolder('INBOX.Sent');
        $inbox->query();
      }

      if ($request->email_type == 'server') {
        $email = $inbox->getMessage($uid = $request->uid, NULL, NULL, TRUE, TRUE, TRUE);
        // dd($email);
        if ($email->hasHTMLBody()) {
          $content = $email->getHTMLBody();
        } else {
          $content = $email->getTextBody();
        }

        $attachments_array = [];
        $attachments = $email->getAttachments();

        $attachments->each(function ($attachment) use (&$content) {
          file_put_contents(storage_path('app/files/email-attachments/' . $attachment->name), $attachment->content);
          $path = "email-attachments/" . $attachment->name;
          $content .= " <form action='" . route('purchase.download.attachments') . "' method='GET'><input type='hidden' name='path' value='" . $path . "' /><button type='submit' class='btn-link'>Attachment</button></form>";
        });
        // dd($content);

        // if (count($attachments_array) > 0) {
        //   foreach ($attachments_array as $attach) {
        //     $content .= " <form action='" . route('purchase.download.attachments') . "' method='GET'><input type='hidden' name='path' value='" . $attach . "' /><button type='submit' class='btn-link'>Attachment</button></form>";
        //   }
        // }
        // dd($attachments_array);
      } else {
        $email = Email::find($request->uid);
        $email->seen = 1;
        $email->save();

        $to_email = $email->to;
        // if ($email->template == 'customer-simple') {
        //   $content = (new CustomerEmail($email->subject, $email->message))->render();
        // } else {
        //   $content = 'No Template';
        // }
        $array = is_array(json_decode($email->additional_data, true)) ? json_decode($email->additional_data, true) : [];

        if (array_key_exists('attachment', $array)) {
          $attachment = json_decode($email->additional_data, true)['attachment'];

          if (is_array($attachment)) {
            $content = $email->message;
            foreach ($attachment as $attach) {
              $content .= " <form action='" . route('purchase.download.attachments') . "' method='GET'><input type='hidden' name='path' value='" . $attach . "' /><button type='submit' class='btn-link'>Attachment</button></form>";
            }
          } else {
            $content = "$email->message <form action='" . route('purchase.download.attachments') . "' method='GET'><input type='hidden' name='path' value='" . $attachment . "' /><button type='submit' class='btn-link'>Attachment</button></form>";
          }
        } else {
          $content = $email->message;
        }

      }



      return response()->json([
        'email' => $content,
        'to_email'  => isset($to_email) ? $to_email : ''
      ]);
    }

    public function emailSend(Request $request)
    {
      $this->validate($request, [
        'subject' => 'required|min:3|max:255',
        'message' => 'required'
      ]);

      $supplier = Supplier::find($request->supplier_id);

      if ($supplier->default_email != '') {
        // Backup your default mailer
        // $backup = Mail::getSwiftMailer();
        //
        // // Setup your gmail mailer
        // $transport = new \Swift_SmtpTransport('c45729.sgvps.net', 465, 'ssl');
        // $transport->setUsername('buying@amourint.com');
        // $transport->setPassword('Cust123!@#');
        // // Any other mailer configuration stuff needed...
        //
        // $gmail = new \Swift_Mailer($transport);
        //
        // // Set the mailer as gmail
        // Mail::setSwiftMailer($gmail);
        // Send your message

        $file_paths = [];

        if ($request->hasFile('file')) {
          foreach ($request->file('file') as $file) {
            $filename = $file->getClientOriginalName();

            $file->storeAs("documents", $filename, 'files');

            $file_paths[] = "documents/$filename";
          }
        }

        // Restore your original mailer
        Mail::to($supplier->default_email)->send(new PurchaseEmail($request->subject, $request->message, $file_paths));

        // Mail::setSwiftMailer($backup);

        $params = [
          'model_id'        => $supplier->id,
          'model_type'      => Supplier::class,
          'from'            => 'customercare@sololuxury.co.in',
          'to'              => $supplier->default_email,
          'subject'         => $request->subject,
          'message'         => $request->message,
          'template'				=> 'customer-simple',
					'additional_data'	=> json_encode(['attachment' => $file_paths])
        ];

        Email::create($params);

        return redirect()->route('supplier.show', $supplier->id)->withSuccess('You have successfully sent an email!');
      }

      return redirect()->route('purchase.show', $purchase->id)->withError('Please select an Agent first');
    }

    public function emailResend(Request $request)
    {
      $this->validate($request, [
        'purchase_id'   => 'required|numeric',
        'email_id'      => 'required|numeric',
        'recipient'     => 'required|email'
      ]);

      $attachment = [];
      $purchase = Purchase::find($request->purchase_id);

      $imap = new Client([
        'host'          => env('IMAP_HOST_PURCHASE'),
        'port'          => env('IMAP_PORT_PURCHASE'),
        'encryption'    => env('IMAP_ENCRYPTION_PURCHASE'),
        'validate_cert' => env('IMAP_VALIDATE_CERT_PURCHASE'),
        'username'      => env('IMAP_USERNAME_PURCHASE'),
        'password'      => env('IMAP_PASSWORD_PURCHASE'),
        'protocol'      => env('IMAP_PROTOCOL_PURCHASE')
      ]);

      $imap->connect();

      if ($request->type == 'inbox') {
        $inbox = $imap->getFolder('INBOX');
      } else {
        $inbox = $imap->getFolder('INBOX.Sent');
        $inbox->query();
      }

      if ($request->email_type == 'server') {
        $email = $inbox->getMessage($uid = $request->email_id, NULL, NULL, TRUE, TRUE, TRUE);

        if ($email->hasHTMLBody()) {
          $content = $email->getHTMLBody();
        } else {
          $content = $email->getTextBody();
        }

        Mail::to($request->recipient)->send(new PurchaseEmail($email->getSubject(), $content, $attachment));

        $params = [
          'model_id'        => $purchase->id,
          'model_type'      => Purchase::class,
          'from'            => 'customercare@sololuxury.co.in',
          'to'              => $request->recipient,
          'subject'         => "Resent: " . $email->getSubject(),
          'message'         => $content,
          'template'				=> 'customer-simple',
  				'additional_data'	=> json_encode(['attachment' => $attachment])
        ];
      } else {
        $email = Email::find($request->email_id);

        $array = is_array(json_decode($email->additional_data, true)) ? json_decode($email->additional_data, true) : [];

        if (array_key_exists('attachment', $array)) {
          $temp = json_decode($email->additional_data, true)['attachment'];
        }

        if (!is_array($temp)) {
          $attachment[] = $temp;
        } else {
          $attachment = $temp;
        }

        Mail::to($request->recipient)->send(new PurchaseEmail($email->subject, $email->message, $attachment));

        $params = [
          'model_id'        => $purchase->id,
          'model_type'      => Purchase::class,
          'from'            => 'customercare@sololuxury.co.in',
          'to'              => $request->recipient,
          'subject'         => "Resent: $email->subject",
          'message'         => $email->message,
          'template'				=> 'customer-simple',
  				'additional_data'	=> json_encode(['attachment' => $attachment])
        ];
      }

      Email::create($params);

      return redirect()->route('purchase.show', $purchase->id)->withSuccess('You have successfully resent an email!');
    }
}
