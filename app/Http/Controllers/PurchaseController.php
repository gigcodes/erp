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
use App\User;
use App\Comment;
use App\Reply;
use App\Message;
use App\ReplyCategory;
use App\Task;
use App\Brand;
use App\Email;
use App\Mail\CustomerEmail;
use App\Mail\PurchaseEmail;
use App\Supplier;
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
      }]);

      // $purchases_new = DB::table('purchases');



  		if(!empty($term)) {
        $purchases = $purchases
        ->orWhere('id','like','%'.$term.'%')
        ->orWhere('purchase_handler',Helpers::getUserIdByName($term))
        ->orWhere('supplier','like','%'.$term.'%')
        ->orWhere('status','like','%'.$term.'%');
      }

      if ($sortby != 'communication') {
  			$purchases = $purchases->orderBy($sortby, $orderby);
  		}

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

  		$purchases_array = $purchases->select(['id', 'purchase_handler', 'supplier', 'status', 'created_at'])->get()->toArray();

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

      if ($request->ajax()) {
  			$html = view('purchase.purchase-item', ['purchases_array' => $purchases_array, 'orderby' => $orderby, 'users'  => $users])->render();

  			return response()->json(['html' => $html]);
  		}

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
          })->whereHas('Product', function ($q) use ($supplier) {
            $q->whereHas('Suppliers', function ($qs) use ($supplier) {
              $qs->where('suppliers.id', $supplier);
            });
          })->get();
        } else {
          $orders = OrderProduct::select('sku')->with(['Order', 'Product']);

          if ($page == 'canceled-refunded') {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereIn('order_status', ['Cancel', 'Refund to be processed']);
            });
          } elseif ($page == 'ordered') {

          } elseif ($page == 'delivered') {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereIn('order_status', ['Delivered']);
            });
          } else {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereNotIn('order_status', ['Cancel', 'Refund to be processed', 'Delivered']);
            });
          }

          $orders = $orders->whereHas('Product', function($q) use ($supplier) {
            $q->whereHas('Suppliers', function ($qs) use ($supplier) {
              $qs->where('suppliers.id', $supplier);
            });
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

          if ($page == 'canceled-refunded') {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereIn('order_status', ['Cancel', 'Refund to be processed']);
            });
          } elseif ($page == 'ordered') {

          } elseif ($page == 'delivered') {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereIn('order_status', ['Delivered']);
            });
          } else {
            $orders = $orders->whereHas('Order', function($q) {
              $q->whereNotIn('order_status', ['Cancel', 'Refund to be processed', 'Delivered']);
            });
          }

          $orders = $orders->whereHas('Product', function($q) use ($brand) {
            $q->where('brand', $brand);
          })->get();
        }
      }

      if ($request->status[0] == null && $request->supplier[0] == null && $request->brand[0] == null) {
        if ($page == 'canceled-refunded') {
          $orders = OrderProduct::with('Order')->whereHas('Order', function($q) {
            $q->whereIn('order_status', ['Cancel', 'Refund to be processed']);
          });
        } elseif ($page == 'ordered') {
          $orders = OrderProduct::with('Order');
        } elseif ($page == 'delivered') {
          $orders = OrderProduct::with('Order')->whereHas('Order', function($q) {
            $q->whereIn('order_status', ['Delivered']);
          });
        } else {
          $orders = OrderProduct::with('Order')->whereHas('Order', function($q) {
            $q->whereNotIn('order_status', ['Cancel', 'Refund to be processed', 'Delivered']);
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
      $suppliers = Supplier::select(['id', 'supplier'])->get();

      $suppliers_array = [];

      foreach ($suppliers as $supplier) {
        $suppliers_array[$supplier->id] = $supplier->supplier;
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

        $new_products[$key]['id'] = $product->id;
        $new_products[$key]['sku'] = $product->sku;
        $new_products[$key]['supplier'] = $product->supplier;
        $new_products[$key]['supplier_list'] = $supplier_list;
        $new_products[$key]['single_supplier'] = $single_supplier;
        $new_products[$key]['image'] = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
        $new_products[$key]['customer_id'] = $product->orderproducts->first()->order ? ($product->orderproducts->first()->order->customer ? $product->orderproducts->first()->order->customer->id : 'No Customer') : 'No Order';
        $new_products[$key]['customer_name'] = $product->orderproducts->first()->order ? ($product->orderproducts->first()->order->customer ? $product->orderproducts->first()->order->customer->name : 'No Customer') : 'No Order';
        $new_products[$key]['order_price'] = $product->orderproducts->first()->product_price;
        $new_products[$key]['order_date'] = $product->orderproducts->first()->order ? $product->orderproducts->first()->order->order_date : 'No Order';
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

      Excel::store(new PurchasesExport($selected_purchases), $path, 'uploads');

      Mail::to('yogeshmordani@icloud.com')->send(new PurchaseExport($path));

      return redirect()->route('purchase.index')->with('success', 'You have successfully exported purchases');
    }

    public function downloadFile(Request $request, $id)
    {
      $file = File::find($id);

      return Storage::disk('uploads')->download('files/' . $file->filename);
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

      return redirect()->route('purchase.index')->with('success', 'You have successfully replaced product!');
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
      $purchase->supplier_id = $request->supplier;
      $purchase->agent_id = $request->agent_id;
      $purchase->bill_number = $request->bill_number;
      $purchase->supplier_phone = $request->supplier_phone;
      $purchase->whatsapp_number = $request->whatsapp_number;
      $purchase->save();

      if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
          $original_name = $file->getClientOriginalName();
          $filename = pathinfo($original_name, PATHINFO_FILENAME);
          $extension = $file->getClientOriginalExtension();

          $full_name = $filename . '.' . $extension;
          // return response()->json(['data' => $full_name]);

          $file->storeAs("files", $full_name, 'uploads');

          $new_file = new File;
          $new_file->filename = $full_name;
          $new_file->model_id = $id;
          $new_file->model_type = Purchase::class;
          $new_file->save();
        }
      }

      return response()->json(['data' => $request->all()]);
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

      $purchase = Purchase::find($request->purchase_id);

      if ($request->type == 'inbox') {
        $inbox_name = 'INBOX';
        $direction = 'from';
      } else {
        $inbox_name = 'INBOX.Sent';
        $direction = 'to';
      }

      $inbox = $imap->getFolder($inbox_name);

      if ($purchase->purchase_supplier->agents) {
        if ($purchase->purchase_supplier->agents()->count() > 1) {
          foreach ($purchase->purchase_supplier->agents as $key => $agent) {
            if ($key == 0) {
              $emails = $inbox->messages()->where($direction, $agent->email);
              $emails = $emails->setFetchFlags(false)
                              ->setFetchBody(false)
                              ->setFetchAttachment(false)->leaveUnread()->get();
            } else {
              $additional = $inbox->messages()->where($direction, $agent->email);
              $additional = $additional->setFetchFlags(false)
                              ->setFetchBody(false)
                              ->setFetchAttachment(false)->leaveUnread()->get();

              $emails = $emails->merge($additional);
            }
          }
        } else if ($purchase->purchase_supplier->agents()->count() == 1) {
          $emails = $inbox->messages()->where($direction, $purchase->purchase_supplier->agents[0]->email);
          $emails = $emails->setFetchFlags(false)
                          ->setFetchBody(false)
                          ->setFetchAttachment(false)->leaveUnread()->get();
        } else {
          $emails = $inbox->messages()->where($direction, 'nonexisting@email.com');
          $emails = $emails->setFetchFlags(false)
                          ->setFetchBody(false)
                          ->setFetchAttachment(false)->leaveUnread()->get();
        }
      }

      $emails_array = [];
      $count = 0;

      foreach ($emails as $key => $email) {
        $emails_array[$key]['uid'] = $email->getUid();
        $emails_array[$key]['subject'] = $email->getSubject();
        $emails_array[$key]['date'] = $email->getDate();

        $count++;
      }

      if ($request->type != 'inbox') {
        $db_emails = $purchase->emails;

        foreach ($db_emails as $key2 => $email) {
          $emails_array[$count + $key2]['id'] = $email->id;
          $emails_array[$count + $key2]['subject'] = $email->subject;
          $emails_array[$count + $key2]['date'] = $email->created_at;
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
      } else {
        $email = Email::find($request->uid);

        // if ($email->template == 'customer-simple') {
        //   $content = (new CustomerEmail($email->subject, $email->message))->render();
        // } else {
        //   $content = 'No Template';
        // }

        $content = $email->message;
      }



      return response()->json(['email' => $content]);
    }

    public function emailSend(Request $request)
    {
      $this->validate($request, [
        'subject' => 'required|min:3|max:255',
        'message' => 'required'
      ]);

      $purchase = Purchase::find($request->purchase_id);

      if ($purchase->agent) {
        // Backup your default mailer
        $backup = Mail::getSwiftMailer();

        // Setup your gmail mailer
        $transport = new \Swift_SmtpTransport('c45729.sgvps.net', 465, 'ssl');
        $transport->setUsername('buying@amourint.com');
        $transport->setPassword('Buy@123');
        // Any other mailer configuration stuff needed...

        $gmail = new \Swift_Mailer($transport);

        // Set the mailer as gmail
        Mail::setSwiftMailer($gmail);
        // Send your message

        // Restore your original mailer
        Mail::to($purchase->agent->email)->send(new PurchaseEmail($request->subject, $request->message));

        Mail::setSwiftMailer($backup);

        $params = [
          'model_id'        => $purchase->id,
          'model_type'      => Purchase::class,
          'from'            => 'customercare@sololuxury.co.in',
          'to'              => $purchase->agent->email,
          'subject'         => $request->subject,
          'message'         => $request->message,
          'template'				=> 'customer-simple',
					'additional_data'	=> ''
        ];

        Email::create($params);

        return redirect()->route('purchase.show', $purchase->id)->withSuccess('You have successfully sent an email!');
      }

      return redirect()->route('purchase.show', $purchase->id)->withError('Please select an Agent first');
    }
}
