<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use App\Order;
use App\Helpers;
use App\StoreWebsite;
use App\User;
use App\Helpers\OrderHelper;
use App\Customer;
use App\SupplierDiscountInfo;
use App\ProductSupplier;
use App\OrderProduct;
use Excel;
use App\Supplier;
use Carbon\Carbon;
use App\Exports\EnqueryExport;
use Storage;
use App\Mails\Manual\PurchaseExport;
use Mail;
use App\InventoryStatus;
class PurchaseProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
		$term = $request->input('term');
		$order_status = $request->status ?? [''];
		$date = $request->date ?? '';
		$brandList = \App\Brand::all()->pluck("name","id")->toArray();
		$brandIds = array_filter($request->get("brand_id",[]));
		$registerSiteList = StoreWebsite::pluck('website', 'id')->toArray();
		if($request->input('orderby') == '')
				$orderby = 'DESC';
		else
				$orderby = 'ASC';


		switch ($request->input('sortby')) {
			case 'type':
					 $sortby = 'order_type';
					break;
			case 'date':
					 $sortby = 'order_date';
					break;
			case 'estdeldate':
					$sortby = 'estimated_delivery_date';
					   break;
			case 'order_handler':
					 $sortby = 'sales_person';
					break;
			case 'client_name':
					 $sortby = 'client_name';
					break;
			case 'status':
					 $sortby = 'order_status_id';
					break;
			case 'advance':
					 $sortby = 'advance_detail';
					break;
			case 'balance':
					 $sortby = 'balance_amount';
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
					 $sortby = 'order_date';
		}
		$orders = (new Order())->newQuery()->with('customer')->leftJoin("store_website_orders as swo","swo.order_id","orders.id");
		if(empty($term))
			$orders = $orders;
		else{
			$orders = $orders->whereHas('customer', function($query) use ($term) {
				return $query->where('name', 'LIKE', '%'.$term.'%')
							->orWhere('id', 'LIKE', '%'.$term.'%')
							->orWhere('email', 'LIKE', '%'.$term.'%');
			})
           ->orWhere('orders.order_id','like','%'.$term.'%')
           ->orWhere('order_type',$term)
           ->orWhere('sales_person',Helpers::getUserIdByName($term))
           ->orWhere('received_by',Helpers::getUserIdByName($term))
           ->orWhere('client_name','like','%'.$term.'%')
           ->orWhere('city','like','%'.$term.'%')
           ->orWhere('order_status_id',(new \App\ReadOnly\OrderStatus())->getIDCaseInsensitive($term));
		}
		if ($order_status[0] != '') {
			$orders = $orders->whereIn('order_status_id', $order_status);
		}

		if ($date != '') {
			$orders = $orders->where('order_date', $date);
		}

		if ($store_site = $request->store_website_id) {
		    $orders = $orders->where('swo.website_id', $store_site);
		}

		$statusFilterList =  clone($orders);
		
		$orders = $orders->leftJoin("order_products as op","op.order_id","orders.id")
		->leftJoin("customers as cs","cs.id","orders.customer_id")
		->leftJoin("products as p","p.id","op.product_id");

		if(!empty($brandIds)) {
			$orders = $orders->whereIn("p.brand",$brandIds);
		}
		$orders = $orders->groupBy("op.id");
		$orders = $orders->select(["orders.*","op.id as order_product_id","op.product_price","op.product_id as product_id","op.supplier_discount_info_id"]);
	
		$users  = Helpers::getUserArray( User::all() );
		$order_status_list = OrderHelper::getStatus();

		if ($sortby != 'communication' && $sortby != 'action' && $sortby != 'due') {
			$orders = $orders->orderBy('is_priority', 'DESC')->orderBy($sortby, $orderby);
		} else {
			$orders = $orders->orderBy('is_priority', 'DESC')->orderBy('created_at', 'DESC');
		}

		$statusFilterList = $statusFilterList->leftJoin("order_statuses as os","os.id","orders.order_status_id")
		->where("order_status","!=", '')->groupBy("order_status")->select(\DB::raw("count(*) as total"),"os.status as order_status","swo.website_id")->get()->toArray();
		$totalOrders = sizeOf($orders->get());
		$orders_array = $orders->paginate(10);
		return view('purchase-product.index', compact('orders_array', 'users','term', 'orderby', 'order_status_list', 'order_status', 'date','statusFilterList','brandList', 'registerSiteList', 'store_site','totalOrders') );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerDetails($type, $order_id) {
        $data = null;
        if($type == 'customer') {
            $data = Customer::join('orders','orders.customer_id','customers.id')->where('orders.id',$order_id)->select('customers.*')->first();
            return view('purchase-product.partials.customer_info',compact('data'));
        }
        if($type == 'order') {
            $data = Order::leftJoin("order_products as op","op.order_id","orders.id")->where('orders.id',$order_id)->leftJoin("products as p","p.id","op.product_id")
            ->leftJoin("brands as b","b.id","p.brand")->select(["orders.*",\DB::raw("group_concat(b.name) as brand_name_list")])->first();
            return view('purchase-product.partials.order_info',compact('data'));
        }
    }
    public function getSupplierDetails($order_product_id, Request $request)
    {
        $order_product = OrderProduct::find($order_product_id);

        $suppliers = \App\ProductSupplier::join('suppliers','suppliers.id','product_suppliers.supplier_id')->where('product_suppliers.product_id',$order_product->product_id)->select('suppliers.*','product_suppliers.id as ps_id','product_suppliers.price as product_price','suppliers.id as supplier_id','product_suppliers.product_id')->get();
        return view('purchase-product.partials.supplier_info',compact('suppliers','order_product'));
    }

    public function saveDiscount(Request $request)
    {
        $discount = SupplierDiscountInfo::where('product_id', $request->product_id)->where('supplier_id',$request->supplier_id)->first();
        if($discount) {
            $discount->discount = $request->discount;
            $discount->save();
        }
        else {
            $discount = new SupplierDiscountInfo;
            $discount->discount = $request->discount;
            $discount->product_id = $request->product_id;
            $discount->supplier_id = $request->supplier_id;
            $discount->save();
        }
        return response()->json(['message' => 'Successfull','code' => 200]);
    }

    

    public function saveFixedPrice(Request $request)
    {
        $fixed_price = SupplierDiscountInfo::where('product_id', $request->product_id)->where('supplier_id',$request->supplier_id)->first();
        if($fixed_price) {
            $fixed_price->fixed_price = $request->fixed_price;
            $fixed_price->save();
        }
        else {
            $fixed_price = new SupplierDiscountInfo;
            $fixed_price->fixed_price = $request->fixed_price;
            $fixed_price->product_id = $request->product_id;
            $fixed_price->supplier_id = $request->supplier_id;
            $fixed_price->save();
        }
        return response()->json(['message' => 'Successfull','code' => 200]);
    }

    public function saveDefaultSupplier(Request $request) {
        if(!$request->product_id || !$request->order_product || !$request->supplier_id) {
            return response()->json(['message' => 'Supplier not found','code' => 500]);
        }
        $discount_info = SupplierDiscountInfo::where('product_id', $request->product_id)->where('supplier_id',$request->supplier_id)->first();
        if($discount_info) {
            $order_product = OrderProduct::find($request->order_product);
            if($order_product) {
                $order_product->supplier_discount_info_id = $discount_info->id;
                $order_product->save();
            }
        }
        else {
            $discount_info = new SupplierDiscountInfo;
            $discount_info->product_id = $request->product_id;
            $discount_info->supplier_id = $request->supplier_id;
            $discount_info->save();
            $order_product = OrderProduct::find($request->order_product);
            if($order_product) {
                $order_product->supplier_discount_info_id = $discount_info->id;
                $order_product->save();
            }
        }
        return response()->json(['message' => 'Successfull','code' => 200]);
    }

    public function getSuppliers(Request $request) {
        $term = $request->term;
        $suppliers =  ProductSupplier::join('suppliers','suppliers.id','product_suppliers.supplier_id');
        if($request->term) {
            $suppliers =  $suppliers->where('suppliers.supplier' ,'like', '%'.$request->term.'%');
        }
        $suppliers = $suppliers->groupBy('product_suppliers.supplier_id')->select('suppliers.*')->get();
        return view('purchase-product.partials.suppliers',compact('suppliers','term'));
    }

    public function getProducts($type, $supplier_id) {
        if($type == 'enquery') {
            $products = ProductSupplier::join('products','products.id','product_suppliers.product_id')->where('product_suppliers.supplier_id',$supplier_id)->groupBy('product_id')->select('product_suppliers.price as product_price','products.*','products.id as product_id','product_suppliers.id as ps_id')->get();
            return view('purchase-product.partials.products',compact('products','type','supplier_id'));
        }
        if($type == 'order') {
            $products = OrderProduct::join('supplier_discount_infos','supplier_discount_infos.id','order_products.supplier_discount_info_id')->join('products','products.id','supplier_discount_infos.product_id')->join('product_suppliers','product_suppliers.product_id','supplier_discount_infos.product_id')->where('supplier_discount_infos.supplier_id',$supplier_id)->groupBy('supplier_discount_infos.id')->select('product_suppliers.price as product_price','products.*','supplier_discount_infos.*','product_suppliers.id as ps_id')->get();
            return view('purchase-product.partials.products',compact('products','type','supplier_id'));
        }
    }

    public function sendProducts($type,$supplier_id,Request $request)
    {
        if($type == 'enquery') {
            $supplier = Supplier::find($supplier_id);            
            $path = "enquiry_exports/" . Carbon::now()->format('Y-m-d-H-m-s') . "_enquiry_exports.xlsx";
            $subject = 'Product enquiry';
            $message = 'Some random message';
            $product_ids = json_decode($request->product_ids, true);
            Excel::store(new EnqueryExport($product_ids,$path), $path, 'files');
            Mail::to($supplier->email)->send(new PurchaseExport($path, $subject, $message));

            $params = [
                'model_id' => $supplier_id,
                'model_type' => Supplier::class,
                'from' => 'buying@amourint.com',
                'to' => $supplier->email,
                'subject' => $subject,
                'message' => $message,
                'template' => 'purchase-simple',
                'additional_data' => json_encode(['attachment' => $path])
            ];
    
            \App\Email::create($params);
            return response()->json(['message' => 'Successfull','code' => 200]);
        }

        if($type == 'order') {
            $supplier = Supplier::find($supplier_id);            
            $path = "order_exports/" . Carbon::now()->format('Y-m-d-H-m-s') . "_order_exports.xlsx";
            $subject = 'Product order';
            $message = 'Some random message for order';
            $product_ids = json_decode($request->product_ids, true);
            Excel::store(new EnqueryExport($product_ids,$path), $path, 'files');
            Mail::to($supplier->email)->send(new PurchaseExport($path, $subject, $message));

            $params = [
                'model_id' => $supplier_id,
                'model_type' => Supplier::class,
                'from' => 'buying@amourint.com',
                'to' => $supplier->email,
                'subject' => $subject,
                'message' => $message,
                'template' => 'purchase-simple',
                'additional_data' => json_encode(['attachment' => $path])
            ];
    
            \App\Email::create($params);
            return response()->json(['message' => 'Successfull','code' => 200]);
        }
    }

    public function createStatus(Request $request) {
        $inventory_status = InventoryStatus::where('name',$request->status)->first();
        if(!$inventory_status) {
            $inventory_status = new InventoryStatus;
            $inventory_status->name = $request->status;
            $inventory_status->save();
            return response()->json(['message' => 'Successfull' , 'code' => 200]);

        }
        else {
            return response()->json(['message' => 'Already exist' , 'code' => 500]);
        }
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
        dd("aa");
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
