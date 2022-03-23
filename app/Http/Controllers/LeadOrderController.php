<?php

namespace App\Http\Controllers;

use App\Leads;
use App\Order;
use Illuminate\Http\Request;
use App\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers;
use Illuminate\Support\Facades\DB;
use App\ErpLeads;

class LeadOrderController extends Controller
{

    public function __construct()
    {

    }

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $term             = $request->input('term');
        $orderOrLead    = $request->input('order_or_lead') ?? '';
        $date             = $request->date ?? '';
        $brandList        = \App\Brand::all()->pluck("name", "id")->toArray();
        $brandIds         = $request->get("brand_id");
        
        if ($request->input('orderby') == '') {
            $orderby = 'DESC';
        } else {
            $orderby = 'ASC';
        }
        
        if($orderOrLead != 'lead'):
            $Order = Order::select('orders.id','orders.customer_id','order_products.product_id','order_date','products.name','brands.name as brand_name','products.price_inr','products.price_inr_discounted','customers.name as customer_name','brands.id as brand_id')
                        ->join('order_products','order_products.order_id','=','orders.id')
                        ->join('products','order_products.product_id','=','products.id')
                        ->leftJoin('customers','orders.customer_id','=','customers.id')
                        //->leftJoin('users','orders.customer_id','=','users.id')
                        ->join('brands','products.brand','=','brands.id')->with('storeWebsiteOrder')->with('storeWebsiteOrder.storeWebsiteProductPrice');
            if (empty($term)) {
                $orders = $Order;
            } 
            else { 
                $orders = $Order->orWhere('orders.id', '=', $term)
                                ->orWhere('products.id', '=', $term)
                                ->orWhere('products.name', 'like', '%' . $term . '%')
                                ->orWhere('brands.id', '=', $brandIds)
                                ->orWhere('customers.name', 'like', '%' . $term . '%');                                
                               // ->orWhere('users.name', 'like', '%' . $term . '%');                                
            }
        endif;
        
        $leads = null;

        if($orderOrLead != 'order'):
            if($orderOrLead == 'lead'):                
                $leads = ErpLeads::select('erp_leads.id','erp_leads.customer_id','erp_leads.store_website_id','erp_leads.product_id','erp_leads.created_at as order_date','products.name','brands.name as brand_name','products.price_inr','products.price_inr_discounted','customers.name as customer_name','brands.id as brand_id')
                            ->join('products','erp_leads.product_id','=','products.id')
                            ->leftJoin('customers','erp_leads.customer_id','=','customers.id')
                            //->leftJoin('users','erp_leads.customer_id','=','users.id')
                            ->join('brands','erp_leads.brand_id','=','brands.id')->with('storeWebsite')->with('storeWebsite.storeWebsiteProductPrice');                            
            else:
                $leads = ErpLeads::select('erp_leads.id','erp_leads.customer_id','erp_leads.store_website_id','erp_leads.product_id','erp_leads.created_at as order_date','products.name','brands.name as brand_name','products.price_inr','products.price_inr_discounted','customers.name as customer_name','brands.id as brand_id')
                            ->join('products','erp_leads.product_id','=','products.id')
                            ->leftJoin('customers','erp_leads.customer_id','=','customers.id')
                            //->leftJoin('users','erp_leads.customer_id','=','users.id')
                            ->join('brands','erp_leads.brand_id','=','brands.id')->with('storeWebsite')->with('storeWebsite.storeWebsiteProductPrice');
                            //->union($orders);
            endif;
            

            if (empty($term)) {
                $orders = $leads;
            } 
            else { 
                $orders = $leads->orWhere('erp_leads.id', '=', $term)
                                ->orWhere('erp_leads.product_id', '=', $term)
                                ->orWhere('products.name', 'like', '%' . $term . '%')
                                //->orWhere('users.name', 'like', '%' . $term . '%');
                                ->orWhere('customers.name', 'like', '%' . $term . '%');
            }
        endif;
        
        if($orderOrLead != "order"):
            if (!empty($brandIds)) {
                $orders = $orders->orWhere("brand_id", $brandIds);
            }
        endif;

        
        
		$orders = $orders->orderBy('id','desc')->simplePaginate(20);

        
        //$orders = $orders->orderBy('id','desc')->get()->toArray();
        $leadOrder_array = $orders;
        if($request->ajax()) {
			   return view('lead-order.lead-order-item', compact('leadOrder_array','leads','brandList', 'term', 'orderOrLead'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
		}
        
        return view('lead-order.index', compact('leadOrder_array','leads','brandList', 'term', 'orderOrLead'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    
}