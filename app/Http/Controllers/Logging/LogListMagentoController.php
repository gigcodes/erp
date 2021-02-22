<?php

namespace App\Http\Controllers\Logging;

use App\ListingPayments;
use App\Loggers\LogListMagento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogListMagentoController extends Controller
{
    private const VALID_MAGENTO_STATUS = [
        'available',
        'sold',
        'out_of_stock'
    ];

    protected function get_brands()
    {
        $brands = \App\Brand::all();

        return $brands;
    }

    protected function get_categories()
    {
        $categories = \App\Category::all();

        return $categories;
    }

    private function check_successfully_listed_products()
    {
        $successfull_products = \App\Product::where('status_id', '=', '12')
            ->leftJoin('log_list_magentos', 'log_list_magentos.product_id', '=', 'products.id')
            ->whereNull('log_list_magentos.id')
            ->select('products.*', 'log_list_magentos.id as exist')
            ->get();

        foreach ($successfull_products as $item) {
            $new = new LogListMagento;
            $new->product_id = $item->id;
            $new->message = "success";
            $new->created_at = $new->updated_at = time();

            $new->save();
        }
    }

    public function index(Request $request)
    {
        //$this->check_successfully_listed_products();
        /*
        $logListMagentos = LogListMagento::join('products', 'log_list_magentos.product_id', '=', 'products.id')
            ->join('brands', 'products.brand', '=', 'brands.id')
            ->join('categories', 'products.category', '=', 'categories.id')
            ->orderBy('log_list_magentos.created_at', 'DESC');
        */

        // Get results
        $logListMagentos = \App\Product::join('log_list_magentos', 'log_list_magentos.product_id', '=', 'products.id')
            ->leftJoin('store_websites as sw', 'sw.id', '=', 'log_list_magentos.store_website_id')
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

        if (!empty($request->status)) {
            if($request->status == 'available'){
                $logListMagentos->where('products.stock', '>',  0);
            }else if($request->status == 'out_of_stock'){
                $logListMagentos->where('products.stock', '<=',  0);
            }
        }

        // Get paginated result
        $logListMagentos->select(
            'log_list_magentos.*',
            'products.*',
            'brands.name as brand_name',
            'categories.title as category_title',
            'log_list_magentos.id as log_list_magento_id',
            'log_list_magentos.created_at as log_created_at',
            'sw.website as website',
            'sw.title as website_title',
            'sw.magento_url as website_url'
        );
        $logListMagentos = $logListMagentos->paginate(25);
        //dd($logListMagentos);
        foreach ($logListMagentos as $key => $item) {
            if($request->sync_status){
               if($item->sync_status != $request->sync_status)
               {
                    unset($logListMagentos[$key]);
                    continue;
               }
               
            }
            if ($item->hasMedia(config('constants.media_tags'))) {
                $logListMagentos[$key]['image_url'] = $item->getMedia(config('constants.media_tags'))->first()->getUrl();
            } else {
                $logListMagentos[$key]['image_url'] = '';
            }
            $logListMagentos[$key]['category_home'] = $item->expandCategory();
            if($item->log_list_magento_id){
                $logListMagentos[$key]['total_error'] = \App\ProductPushErrorLog::where('log_list_magento_id',$item->log_list_magento_id)->where('response_status','error')->count();
                $logListMagentos[$key]['total_success'] = \App\ProductPushErrorLog::where('log_list_magento_id',$item->log_list_magento_id)->where('response_status','success')->count();
            }
            
        }
        //dd($logListMagentos);
        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.listmagento_data', compact('logListMagentos'))->render(),
                'links' => (string) $logListMagentos->render()
            ], 200);
        }
        $filters = $request->all();

        // Show results
        return view('logging.listmagento', compact('logListMagentos', 'filters'))
            ->with('success', \Request::Session()->get("success"))
            ->with('brands', $this->get_brands())
            ->with('categories', $this->get_categories());
    }

    public function updateMagentoStatus(Request $request, $id)
    {
        //LogListMagento::updateMagentoStatus($id,)

        $status = $request->input('status');



        if (!$status) {
            return response()->json(
                [
                    'message' => 'Missing status'
                ],
                400
            );
        }

        if (!in_array($status, LogListMagentoController::VALID_MAGENTO_STATUS)) {
            return response()->json(
                [
                    'message' => 'Invalid status'
                ],
                400
            );
        }

        LogListMagento::updateMagentoStatus($id, $status);

        return response()->json(
            [
                'status' => $status,
                'id' => $id
            ]
        );
    }

    public function showErrorLogs($product_id, $website_id = null)  {
        $productErrorLogs = \App\ProductPushErrorLog::where('product_id',$product_id);
        if($website_id) {
            $productErrorLogs = $productErrorLogs->where('store_website_id',$website_id);
        }
        $productErrorLogs = $productErrorLogs->get();
        return view('logging.partials.magento_error_data',compact('productErrorLogs'));
    }

    public function showErrorByLogId($id) 
    {
        $productErrorLogs = \App\ProductPushErrorLog::where('log_list_magento_id',$id)->get();
        return view('logging.partials.magento_error_data',compact('productErrorLogs'));
    }
}
