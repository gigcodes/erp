<?php

namespace App\Http\Controllers\Logging;

use App\ListingPayments;
use App\Loggers\LogListMagento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogListMagentoController extends Controller
{
    protected function get_brands(){
        $brands=\App\Brand::all();

        return $brands;
    }
    
    protected function get_categories(){
        $categories=\App\Category::all();

        return $categories;
    }

    private function check_successfully_listed_products(){
        $successfull_products=\App\Product::where('status_id','=','12')
                                            ->leftJoin('log_list_magentos','log_list_magentos.product_id','=','products.id')
                                            ->whereNull('log_list_magentos.id')
                                            ->select('products.*','log_list_magentos.id as exist')
                                            ->get();

        foreach($successfull_products as $item){
            $new=new LogListMagento;
            $new->product_id=$item->id;
            $new->message="success";
            $new->created_at=$new->updated_at=time();

            $new->save();
        }
    }

    public function index(Request $request)
    {
        $this->check_successfully_listed_products();
        /*
        $logListMagentos = LogListMagento::join('products', 'log_list_magentos.product_id', '=', 'products.id')
            ->join('brands', 'products.brand', '=', 'brands.id')
            ->join('categories', 'products.category', '=', 'categories.id')
            ->orderBy('log_list_magentos.created_at', 'DESC');
        */

        // Get results
        $logListMagentos = \App\Product::join('log_list_magentos','log_list_magentos.product_id','=','products.id')
                                        ->join('brands', 'products.brand', '=', 'brands.id')
                                        ->join('categories', 'products.category', '=', 'categories.id')
                                        ->orderBy('log_list_magentos.created_at', 'DESC');

        // Filters
        if (!empty($request->product_id)) {
            $logListMagentos->where('product_id', 'LIKE', '%' . $request->product_id . '%');
        }

        if (!empty($request->sku)) {
            $logListMagentos->where('products.sku', 'LIKE', '%' . $request->sku . '%');
        }

        if (!empty($request->brand)) {
            $logListMagentos->where('brands.name', 'LIKE', '%' . $request->brand . '%');
        }

        if (!empty($request->category)) {
            $logListMagentos->where('categories.title', 'LIKE', '%' . $request->category . '%');
        }

        // Get paginated result
        $logListMagentos->select('log_list_magentos.*','products.*','brands.name as brand_name','categories.title as category_title');
        $logListMagentos = $logListMagentos->paginate(25);

        foreach($logListMagentos as $key=>$item){
            if ($item->hasMedia(config('constants.media_tags'))){
                $logListMagentos[$key]['image_url']=$item->getMedia(config('constants.media_tags'))->first()->getUrl();
            }else{
                $logListMagentos[$key]['image_url']='';
            }
        }
        //echo "<Pre>";print_r($logListMagentos);exit;
        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.listmagento_data', compact('logListMagentos'))->render(),
                'links' => (string)$logListMagentos->render()
            ], 200);
        }
        $filters=$request->all();

        // Show results
        return view('logging.listmagento', compact('logListMagentos','filters'))
                                ->with('success',\Request::Session()->get("success"))
                                ->with('brands',$this->get_brands())
                                ->with('categories',$this->get_categories());
    }
}
