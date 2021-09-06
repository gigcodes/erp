<?php

namespace App\Http\Controllers;

use App\StoreWebsiteSalesPrice;
use App\Helpers;
use App\MonetaryAccount;
use App\Purchase;
use App\ReadOnly\CashFlowCategories;
use App\Setting;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Storage;

class DiscountSalePriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
       
       
        $discountsaleprice=StoreWebsiteSalesPrice::select('store_website_sales_prices.*','suppliers.supplier')
           ->leftJoin('suppliers','store_website_sales_prices.supplier_id','suppliers.id')->get();
        $supplier=\App\Supplier::get();
        if ($request->ajax()) 
        {
            return view('discountsaleprice.index_page', [
                'discountsaleprice' => $discountsaleprice,
                 'supplier'  =>$supplier,
            ]);
        }
        else
        {
        return view('discountsaleprice.index', [
            'discountsaleprice' => $discountsaleprice,
            'supplier'  =>$supplier,
        ]);
        }
    }

    public function type(Request $request)
    {
         
         $type=$request->type;
         $select="<select class='form-control' name='type_id' required id='type_id'>";

         if ($type=='brand')
         {
                $model_type="\App\Brand";
                $rs=$model_type::get();
                $data='';
                foreach($rs as $r)
                {
                   
                      $select.="<option value='".$r->id."'>".$r->name."</option>";
                    

                }
                
               
         } 
         if ($type=='category')
         {
                $model_type="\App\Category";
                $rs=$model_type::all();
                $data='';
                foreach($rs as $r)
                {
                    $select.="<option value='".$r->id."'>".$r->title."</option>";

                }
                
                
         } 
         
         if ($type=='product')
         {
                $model_type="\App\Product";
                $rs=$model_type::get();
                $data='';
                foreach($rs as $r)
                {
                    
                    $select.="<option value='".$r->id."'>".$r->name."</option>";

                }
                
               
         } 
         
         if ($type=='store_website')
         {
                $model_type="\App\StoreWebsite";
                $rs=$model_type::get();
                $data='';
                foreach($rs as $r)
                {
                    
                   
                    $select.="<option value='".$r->id."'>".$r->title."</option>";
                   

                }
                
                
         }  
         $select.="</select>";
         echo $select;

    }  
    
    
    public function create(Request $request)
    {
        $data            = $request->except(['_token', 'file']);
        $data['created_by'] = Auth::id();
       
        StoreWebsiteSalesPrice::insert($data);

        return redirect('discount-sale-price')->withSuccess('You have successfully added a record!');
    }


   
}
    