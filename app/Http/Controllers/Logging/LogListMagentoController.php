<?php

namespace App\Http\Controllers\Logging;

use Log;
use App\Brand;
use DataTables;
use App\Product;
use App\Setting;
use App\Category;
use Carbon\Carbon;
use App\StoreWebsite;
use GuzzleHttp\Client;
use App\WebsiteProductCsv;
use App\Jobs\PushToMagento;
use App\ProductPushJourney;
use Illuminate\Http\Request;
use App\Helpers\ProductHelper;
use App\Loggers\LogListMagento;
use App\ProductPushInformation;
use App\PushToMagentoCondition;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LogListMagentoExport;
use App\StoreMagentoApiSearchProduct;
use App\ProductPushInformationHistory;
use App\ProductPushInformationSummery;
use App\Loggers\LogListMagentoSyncStatus;
use GuzzleHttp\Exception\ClientException;
use seo2websites\MagentoHelper\MagentoHelperv2;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use App\Models\DataTableColumn;

class LogListMagentoController extends Controller
{
    const VALID_MAGENTO_STATUS = ['available', 'sold', 'out_of_stock'];

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
            $new->message = 'success';
            $new->created_at = $new->updated_at = time();

            $new->save();
        }
    }

    public function export(Request $request)
    {
        $logListMagentos = \App\Product::join('log_list_magentos', 'log_list_magentos.product_id', '=', 'products.id')
            ->leftJoin('store_websites as sw', 'sw.id', '=', 'log_list_magentos.store_website_id')
            ->join('brands', 'products.brand', '=', 'brands.id')
            ->join('categories', 'products.category', '=', 'categories.id')
            ->orderBy('log_list_magentos.id', 'DESC')->where('log_list_magentos.sync_status', 'success')
            ->select('sw.website as website',
                'sw.title as website_title',
                'log_list_magentos.id as log_list_magento_id',
                'log_list_magentos.created_at as log_created_at',
                'log_list_magentos.total_request_assigned'
            );

        if (! empty($request->start_date)) {
            $logListMagentos->where('log_list_magentos.created_at', '>=', $request->start_date . ' 00:00:00');
        }
        if (! empty($request->end_date)) {
            $logListMagentos->where('log_list_magentos.created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $logListMagentos = $logListMagentos->get();
        $list[0]['website_title'] = 'Website Title';
        $list[0]['website'] = 'Website ';
        $list[0]['total_error'] = 'Error';
        $list[0]['total_success'] = 'Success';
        $list[0]['products_pushed'] = 'Products Pushed';
        $i = 1;
        foreach ($logListMagentos as $key => $item) {
            if ($item->log_list_magento_id) {
                $list[$i]['website_title'] = $item['website_title'];
                $list[$i]['website'] = $item['website'];
                $list[$i]['total_error'] = \App\ProductPushErrorLog::where('log_list_magento_id', $item->log_list_magento_id)->where('response_status', 'error')->count();
                $list[$i]['total_success'] = \App\ProductPushErrorLog::where('log_list_magento_id', $item->log_list_magento_id)->where('response_status', 'success')->count();
                $list[$i]['products_pushed'] = $list[$i]['total_error'] + $list[$i]['total_success'];
            }
            $i++;
        }

        return Excel::download(new LogListMagentoExport($list), 'logListMagentos.xlsx');
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
            ->orderBy('log_list_magentos.id', 'DESC');

        // Filters
        if (! empty($request->product_id)) {
            $logListMagentos->where('products.id', 'LIKE', '%' . $request->product_id . '%');
        }

        if (! empty($request->sku)) {
            $logListMagentos->where('products.sku', 'LIKE', '%' . $request->sku . '%');
        }

        if (! empty($request->brand)) {
            $logListMagentos->where('brands.name', 'LIKE', '%' . $request->brand . '%');
        }

        if (! empty($request->category)) {
            $categories = (new \App\Product)->matchedCategories($request->category);
            $logListMagentos->whereIn('categories.id', $categories);
        }

        if (! empty($request->size_info)) {
            if ($request->size_info == 'yes') {
                $logListMagentos->where('log_list_magentos.size_chart_url', '!=', null);
            } elseif ($request->size_info == 'no') {
                $logListMagentos->where('log_list_magentos.size_chart_url', null);
            }
        }

        if (! empty($request->select_date)) {
            $logListMagentos->whereDate('log_list_magentos.created_at', 'LIKE', '%' . $request->select_date . '%');
        }

        if (! empty($request->job_start_date)) {
            $logListMagentos->whereDate('log_list_magentos.job_start_time', 'LIKE', '%' . $request->job_start_date . '%');
        }

        if (! empty($request->status)) {
            if ($request->status == 'available') {
                $logListMagentos->where('products.stock', '>', 0);
            } elseif ($request->status == 'out_of_stock') {
                $logListMagentos->where('products.stock', '<=', 0);
            }
        }

        if ($request->sync_status != null) {
            $logListMagentos->where('log_list_magentos.sync_status', $request->sync_status);
        }

        if ($request->user != null) {
            $logListMagentos->where('log_list_magentos.user_id', $request->user);
        }

        if ($request->queue != null) {
            $logListMagentos->where('log_list_magentos.queue', $request->queue);
        }

        $selectClause = [
            'log_list_magentos.*',
            'products.*',
            'brands.name as brand_name',
            'categories.title as category_title',
            'log_list_magentos.id as log_list_magento_id',
            'log_list_magentos.created_at as log_created_at',
            'sw.website as website',
            'sw.title as website_title',
            'sw.magento_url as website_url',
            'log_list_magentos.user_id as log_user_id',
        ];
        if ($request->crop_start_date != null && $request->crop_end_date != null) {
            $selectClause[] = 'cri.product_id as cri_product_id';

            $startDate = $request->crop_start_date;
            $endDate = $request->crop_end_date;
            $logListMagentos->leftJoin('cropped_image_references as cri', function ($join) use ($startDate, $endDate) {
                $join->on('cri.product_id', 'products.id');
                $join->whereDate('cri.created_at', '>=', $startDate)->whereDate('cri.created_at', '<=', $endDate);
            });

            $logListMagentos->whereNotNull('cri.product_id');
            $logListMagentos->groupBy('products.id');
        }

        // Get paginated result
        $logListMagentos->select($selectClause);
        $logListMagentos = $logListMagentos->paginate(25);
        $total_count = $logListMagentos->total();
        //dd($logListMagentos);
        foreach ($logListMagentos as $key => $item) {
            if ($item->hasMedia(config('constants.media_tags'))) {
                $logListMagentos[$key]['image_url'] = $item->getMedia(config('constants.media_tags'))->first()->getUrl();
            } else {
                $logListMagentos[$key]['image_url'] = '';
            }
            $logListMagentos[$key]['category_home'] = $item->expandCategory();
            if ($item->log_list_magento_id) {
                $logListMagentos[$key]['total_error'] = \App\ProductPushErrorLog::where('log_list_magento_id', $item->log_list_magento_id)->where('response_status', 'error')->count();
                $logListMagentos[$key]['total_success'] = \App\ProductPushErrorLog::where('log_list_magento_id', $item->log_list_magento_id)->where('response_status', 'success')->count();
            }
            if ($item->log_user_id) {
                $logListMagentos[$key]['log_user_name'] = \App\User::find($item->log_user_id)->name;
            } else {
                $logListMagentos[$key]['log_user_name'] = '';
            }
        }
        $users = \App\User::all();
        $syncStatuses = LogListMagentoSyncStatus::all();
        // dd($logListMagentos);
        // For ajax
        if ($request->ajax() and $request->type == 'product_log_list') {
            return response()->json([
                'tbody' => view('logging.partials.magento_product_data', compact('logListMagentos', 'total_count'))->render(),
                'links' => (string) $logListMagentos->render(),
            ], 200);
        } elseif ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.listmagento_data', compact('logListMagentos', 'total_count'))->render(),
                'links' => (string) $logListMagentos->render(),
            ], 200);
        }
        $filters = $request->all();
        // Show results

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'list-magento')->first();

        $dynamicColumnsToShowListmagento = [];
        if(!empty($datatableModel->column_name)){
            $hideColumns = $datatableModel->column_name ?? "";
            $dynamicColumnsToShowListmagento = json_decode($hideColumns, true);
        }

        return view('logging.listmagento', compact('logListMagentos', 'filters', 'users', 'total_count', 'syncStatuses', 'dynamicColumnsToShowListmagento'))
            ->with('success', \Request::Session()->get('success'))
            ->with('brands', $this->get_brands())
            ->with('categories', $this->get_categories());
    }

    public function listmagentoColumnVisbilityUpdate(Request $request)
    {   
        $userCheck = DataTableColumn::where('user_id',auth()->user()->id)->where('section_name','list-magento')->first();

        if($userCheck)
        {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'list-magento';
            $column->column_name = json_encode($request->column_listmagento); 
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'list-magento';
            $column->column_name = json_encode($request->column_listmagento); 
            $column->user_id =  auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function productPushJourney(Request $request)
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
            ->orderBy('log_list_magentos.id', 'DESC');

        // Filters
        if (! empty($request->product_id)) {
            $logListMagentos->where('products.id', 'LIKE', '%' . $request->product_id . '%');
        }

        if (! empty($request->sku)) {
            $logListMagentos->where('products.sku', 'LIKE', '%' . $request->sku . '%');
        }

        if (! empty($request->brand) && count(array_filter($request->brand)) > 0) {
            $logListMagentos->whereIn('products.brand', $request->brand);
        }

        if (! empty($request->category) && count(array_filter($request->category)) > 0) {
            $categories = (new \App\Product)->matchedCategories($request->category);
            $logListMagentos->whereIn('categories.id', $categories);
        }

        if (! empty($request->size_info)) {
            if ($request->size_info == 'yes') {
                $logListMagentos->where('log_list_magentos.size_chart_url', '!=', null);
            } elseif ($request->size_info == 'no') {
                $logListMagentos->where('log_list_magentos.size_chart_url', null);
            }
        }

        if (! empty($request->select_date)) {
            $logListMagentos->whereDate('log_list_magentos.created_at', 'LIKE', '%' . $request->select_date . '%');
        }

        if (! empty($request->job_start_date)) {
            $logListMagentos->whereDate('log_list_magentos.job_start_time', 'LIKE', '%' . $request->job_start_date . '%');
        }

        if (! empty($request->status)) {
            if ($request->status == 'available') {
                $logListMagentos->where('products.stock', '>', 0);
            } elseif ($request->status == 'out_of_stock') {
                $logListMagentos->where('products.stock', '<=', 0);
            }
        }

        if ($request->sync_status != null) {
            $logListMagentos->where('log_list_magentos.sync_status', $request->sync_status);
        }

        if ($request->user != null) {
            $logListMagentos->where('log_list_magentos.user_id', $request->user);
        }

        if ($request->queue != null) {
            $logListMagentos->where('log_list_magentos.queue', $request->queue);
        }

        $selectClause = [
            'log_list_magentos.*',
            'products.*',
            'brands.name as brand_name',
            'categories.title as category_title',
            'categories.id as category_id',
            'log_list_magentos.id as log_list_magento_id',
            'log_list_magentos.created_at as log_created_at',
            'sw.website as website',
            'sw.title as website_title',
            'sw.magento_url as website_url',
            'log_list_magentos.user_id as log_user_id',
        ];
        if ($request->crop_start_date != null && $request->crop_end_date != null) {
            $selectClause[] = 'cri.product_id as cri_product_id';

            $startDate = $request->crop_start_date;
            $endDate = $request->crop_end_date;
            $logListMagentos->leftJoin('cropped_image_references as cri', function ($join) use ($startDate, $endDate) {
                $join->on('cri.product_id', 'products.id');
                $join->whereDate('cri.created_at', '>=', $startDate)->whereDate('cri.created_at', '<=', $endDate);
            });

            $logListMagentos->whereNotNull('cri.product_id');
            $logListMagentos->groupBy('products.id');
        }

        // Get paginated result
        $logListMagentos->select($selectClause);
        $logListMagentos = $logListMagentos->paginate(25);
        $total_count = $logListMagentos->total();
        //dd($logListMagentos);
        foreach ($logListMagentos as $key => $item) {
            if ($item->hasMedia(config('constants.media_tags'))) {
                $logListMagentos[$key]['image_url'] = $item->getMedia(config('constants.media_tags'))->first()->getUrl();
            } else {
                $logListMagentos[$key]['image_url'] = '';
            }
            $logListMagentos[$key]['category_home'] = $item->expandCategory();
            if ($item->log_list_magento_id) {
                $logListMagentos[$key]['total_error'] = \App\ProductPushErrorLog::where('log_list_magento_id', $item->log_list_magento_id)->where('response_status', 'error')->count();
                $logListMagentos[$key]['total_success'] = \App\ProductPushErrorLog::where('log_list_magento_id', $item->log_list_magento_id)->where('response_status', 'success')->count();
            }
            if ($item->log_user_id) {
                $logListMagentos[$key]['log_user_name'] = \App\User::find($item->log_user_id)->name;
            } else {
                $logListMagentos[$key]['log_user_name'] = '';
            }
        }
        $conditions = PushToMagentoCondition::select('condition', 'status', 'upteam_status');
        if (! empty($request->conditions) && count(array_filter($request->conditions)) > 0) {
            $conditions->whereIn('id', $request->conditions);
        }
        $conditions = $conditions->get();

        $users = \App\User::all();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'logging-log-magento-product-push-journey')->first();

        $dynamicColumnsTologging = [];
        if(!empty($datatableModel->column_name)){
            $hideColumns = $datatableModel->column_name ?? "";
            $dynamicColumnsTologging = json_decode($hideColumns, true);
        }
        
        if ($request->ajax() and $request->type == 'product_log_list') {
            return response()->json([
                'tbody' => view('logging.partials.magento_product_data_push', compact('logListMagentos', 'total_count', 'conditions', 'dynamicColumnsTologging'))->render(),
                'links' => (string) $logListMagentos->render(),
            ], 200);
        } elseif ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.magento_product_data_push', compact('logListMagentos', 'total_count', 'conditions', 'dynamicColumnsTologging'))->render(),
                'links' => (string) $logListMagentos->render(),
            ], 200);
        }
        $filters = $request->all();
        // Show results
        $allCategories = $this->get_categories();
        $categoryPlucks = $allCategories->pluck('title', 'id')->toArray();

        $allbrands = $this->get_brands();
        $brandPlucks = $allbrands->pluck('name', 'id')->toArray();

        $conditionPlucks = PushToMagentoCondition::pluck('condition', 'id')->toArray();

        return view('logging.partials.magento_product_data_push_jouerny', compact('logListMagentos', 'filters', 'users', 'total_count', 'conditions', 'categoryPlucks', 'brandPlucks', 'conditionPlucks', 'dynamicColumnsTologging'))
            ->with('success', \Request::Session()->get('success'))
            ->with('brands', $allbrands)
            ->with('categories', $allCategories);
    }

    public function columnVisbilityUpdate(Request $request)
    {   
        $userCheck = DataTableColumn::where('user_id',auth()->user()->id)->where('section_name','logging-log-magento-product-push-journey')->first();

        if($userCheck)
        {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'logging-log-magento-product-push-journey';
            $column->column_name = json_encode($request->column_ll); 
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'logging-log-magento-product-push-journey';
            $column->column_name = json_encode($request->column_ll); 
            $column->user_id =  auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function updateMagentoStatus(Request $request, $id)
    {
        //LogListMagento::updateMagentoStatus($id,)

        $status = $request->input('status');

        if (! $status) {
            return response()->json(
                [
                    'message' => 'Missing status',
                ],
                400
            );
        }

        if (! in_array($status, LogListMagentoController::VALID_MAGENTO_STATUS)) {
            return response()->json(
                [
                    'message' => 'Invalid status',
                ],
                400
            );
        }

        LogListMagento::updateMagentoStatus($id, $status);

        return response()->json(
            [
                'status' => $status,
                'id' => $id,
            ]
        );
    }

    public function showErrorLogs($product_id, $website_id = null)
    {
        $productErrorLogs = \App\ProductPushErrorLog::where('product_id', $product_id);
        if ($website_id) {
            $productErrorLogs = $productErrorLogs->where('store_website_id', $website_id);
        }
        $productErrorLogs = $productErrorLogs->get();

        return view('logging.partials.magento_error_data', compact('productErrorLogs'));
    }

    public function showJourneyById($id)
    {
        $conditions = PushToMagentoCondition::pluck('condition')->toArray();
        $pushJourney = ProductPushJourney::where('log_list_magento_id', $id)->pluck('condition')->toArray();

        return view('logging.partials.push_journey', compact('conditions', 'pushJourney'));
    }

    public function showJourneyHorizontalById(Request $request, $id)
    {
        $conditions = PushToMagentoCondition::select('condition', 'status', 'upteam_status')->get();
        $pushJourney = ProductPushJourney::where('log_list_magento_id', $id)->pluck('condition')->toArray();
        $productSku = $request->sku_name ?? '';
        $product = \App\Product::find($request->product_id);
        $type = ProductHelper::getTopParent($product->category);
        $category = Category::find($product->category);
        if ($category->parent_id != 0) {
            $useStatus = 'status';
        } else {
            $useStatus = 'upteam_status';
        }

        return view('logging.partials.push_journey_horizontal', compact('conditions', 'pushJourney', 'productSku', 'useStatus'));
    }

    public function showErrorByLogId($id)
    {
        $productErrorLogs = \App\ProductPushErrorLog::where('log_list_magento_id', $id)->where('response_status', '=', 'error')->get();

        return view('logging.partials.magento_error_data', compact('productErrorLogs'));
    }

    public function showProductPushLog($id)
    {
        $productPushLogs = \App\ProductPushErrorLog::where('log_list_magento_id', $id)->where('response_status', '=', 'success')->get();

        return view('logging.partials.magento_product_push_log', compact('productPushLogs'));
    }

    public function showPrices($id)
    {
        $prices = \App\StoreWebsiteProductPrice::select(['store_website_product_prices.*', 'lp.segment_discount_per', 'p.discounted_percentage'])->
        leftJoin('products AS p', 'p.id', 'store_website_product_prices.product_id')->
        leftJoin('lead_product_price_count_logs AS lp', 'lp.product_id', 'store_website_product_prices.product_id')->
        where('store_website_product_prices.product_id', $id)->get();

        return view('logging.partials.magento_prices_data', compact('prices'));
    }

    public function showMagentoProductAPICall(Request $request)
    {
        $data = StoreMagentoApiSearchProduct::orderBy('id', 'DESC')->paginate(10);

        return view('logging.magento-api-call', compact('data'));
    }

    protected function processProductAPIResponce($products)
    {
        $prepared_products_data = [];
        $websites = [];
        $category_names = [];
        $size = '';
        $brands = '';
        $composition = '';
        $brand = '';
        $dimensions = 'N/A';
        $size = 'N/A';
        foreach ($products as $value) {
            $websites[] = \App\StoreWebsite::where('id', $value->store_website_id)->value('title');
            if (isset($value->extension_attributes)) {
                foreach ($value->extension_attributes->website_ids as $vwi) {
                    $websites[] = \App\Website::where('platform_id', $vwi)->value('name');
                }
            }

            if (isset($value->custom_attributes)) {
                foreach ($value->custom_attributes as $v) {
                    if ($v->attribute_code === 'category_ids') {
                        foreach ($v->value as $key => $cat_id) {
                            $category_names[] = \App\StoreWebsiteCategory::join('categories as c', 'c.id', 'store_website_categories.category_id')
                                ->where('remote_id', $cat_id)
                                ->value('title');
                        }
                    }
                    if ($v->attribute_code === 'size_v2' || $v->attribute_code === 'size') {
                        $sizeM = \App\StoreWebsiteSize::join('sizes as s', 's.id', 'store_website_sizes.size_id')->where('platform_id', $v->value)->where('store_website_id', $value->store_website_id)->select('s.*')->first();
                        if ($sizeM) {
                            $size = $sizeM->name;
                        }
                    }

                    if ($v->attribute_code === 'brands') {
                        $brandsModel = \App\StoreWebsiteBrand::join('brands as b', 'b.id', 'store_website_brands.brand_id')
                            ->where('magento_value', $v->value)
                            ->where('store_website_id', $value->store_website_id)
                            ->select('b.*')
                            ->first();
                        if ($brandsModel) {
                            $brand = $brandsModel->name;
                        }
                    }
                    if ($v->attribute_code === 'composition') {
                        $composition = $v->value;
                    }

                    if ($v->attribute_code === 'dimensions') {
                        $dimensions = $v->value;
                    }
                }
            }

            $prepared_products_data[$value->sku] = [
                'store_website_id' => $value->store_website_id,
                'magento_id' => $value->id,
                'sku' => $value->sku,
                'product_name' => $value->name,
                'media_gallery_entries' => $value->media_gallery_entries,
                'websites' => array_filter($websites),
                'category_names' => $category_names,
                'size' => $size,
                'brands' => $brand,
                'composition' => $composition,
                'dimensions' => $dimensions,
                'english' => ! empty($value->english) ? $value->english : 'No',
                'arabic' => ! empty($value->arabic) ? $value->arabic : 'No',
                'german' => ! empty($value->german) ? $value->german : 'No',
                'spanish' => ! empty($value->spanish) ? $value->spanish : 'No',
                'french' => ! empty($value->french) ? $value->french : 'No',
                'italian' => ! empty($value->italian) ? $value->italian : 'No',
                'japanese' => ! empty($value->japanese) ? $value->japanese : 'No',
                'korean' => ! empty($value->korean) ? $value->korean : 'No',
                'russian' => ! empty($value->russian) ? $value->russian : 'No',
                'chinese' => ! empty($value->chinese) ? $value->chinese : 'No',
                'size_chart_url' => ! empty($value->size_chart_url) ? 'Yes' : 'No',
                'success' => true,
            ];
            if (! $value->success) {
                $product_name = \App\Product::with('product_category', 'brands')->where('sku', $value->skuid)->first();
                //dd($product_name);
                if (isset($product_name) && $product_name->product_category != null) {
                    // print_r($product_name->product_category);
                    if ($product_name->product_category) {
                        // foreach($product_name->product_category as $cat){
                        $category_names[] = $product_name->product_category->title;
                        //  }
                    }
                }
                $brand = isset($product_name->brands) ? $product_name->brands->name : '';
                $prepared_products_data[$value->skuid] = [
                    'store_website_id' => $value->store_website_id,
                    'magento_id' => '',
                    'sku' => $value->skuid,
                    'product_name' => $product_name != null ? $product_name->name : '',
                    'media_gallery_entries' => '',
                    'websites' => $websites,
                    'category_names' => $category_names,
                    'size' => $product_name != null ? $product_name->size : '',
                    'brands' => $brand,
                    'composition' => $product_name != null ? $product_name->composition : '',
                    'dimensions' => $product_name != null ? $product_name->lmeasurement . ',' . $product_name->hmeasurement . ',' . $product_name->dmeasurement : '',
                    'english' => 'No',
                    'arabic' => 'No',
                    'german' => 'No',
                    'spanish' => 'No',
                    'french' => 'No',
                    'italian' => 'No',
                    'japanese' => 'No',
                    'korean' => 'No',
                    'russian' => 'No',
                    'chinese' => 'No',
                    'size_chart_url' => 'No',
                    'success' => false,
                ];
            }

            $category_names = [];
            $websites = [];
            $size = '';
            $brands = '';
            $composition = '';
        }

        return $prepared_products_data;
    }

    public function key_value_pair_exists(array $haystack, $key)
    {
        return array_key_exists($key, $haystack);
    }

    public function getMagentoProductAPIAjaxCall(Request $request)
    {
        if ($request->ajax()) {
            //  $sku =$request->productSkus; //'["SB0AB15C50GK92","SW2S0P39JZI","EE4791White-45.5","EE4791White","A0510XXAS5Black-36"]';
            $languages = ['arabic', 'german', 'spanish', 'french', 'italian', 'japanese', 'korean', 'russian', 'chinese'];

            $products = [];
            //$skudata  = json_decode('[{"sku":"RMTR00604468H20081Grey","websiteid":"1"}]'); //json_decode($request->productSkus);
            $skudata = json_decode($request->productSkus);

            $magentoHelper = new MagentoHelperv2;

            $client = new \GuzzleHttp\Client();
            foreach ($skudata as $sku) {
                try {
                    // $get_store_website = \App\StoreWebsite::find($sku->websiteid);
                    $get_store_website = \App\StoreWebsite::find($sku->websiteid);
                    $result = $magentoHelper->getProductBySku($sku->sku, $get_store_website);
                    // $req = $client->get('https://magento-501091-1587493.cloudwaysapps.com/rest/V1/products/6378180NP001000Black-L',[
                    //  https:\/\/magento-501091-1587493.cloudwaysapps.com\/rest\/V1\/PRODUCTS\/6378180NP001000Black-L"//
                    // 'headers' => [
                    //     'Accept'     => 'application/json',
                    //     'Authorization'=>'Bearer 7e9pvvgo4u5kel2xlchlj4hmgjb0lu6s'
                    //                                7e9pvvgo4u5kel2xlchlj4hmgjb0lu6s
                    //   ]
                    // ]);
                    // $response = $req->getBody()->getContents();

                    if (isset($result->id)) {
                        $result->success = true;
                        $result->size_chart_url = '';

                        $englishDescription = '';
                        if (! empty($result->custom_attributes)) {
                            foreach ($result->custom_attributes as $attributes) {
                                if ($attributes->attribute_code == 'size_chart_url') {
                                    $result->size_chart_url = $attributes->value;
                                }
                                if ($attributes->attribute_code == 'description') {
                                    $englishDescription = $attributes->value;
                                    $result->english = 'Yes';
                                }
                            }
                        }

                        // check for all langauge request
                        foreach ($languages as $language) {
                            $firstStore = \App\Website::join('website_stores as ws', 'ws.website_id', 'websites.id')
                                ->join('website_store_views as wsv', 'wsv.website_store_id', 'ws.id')
                                ->where('websites.store_website_id', $get_store_website->id)
                                ->where('wsv.name', 'like', $language)
                                ->groupBy('ws.name')
                                ->select('wsv.*')
                                ->first();

                            if ($firstStore) {
                                $exresult = $magentoHelper->getProductBySku($sku->sku, $get_store_website, $firstStore->code);
                                if (isset($exresult->id)) {
                                    $diffrentDescription = '';

                                    if (! empty($exresult->custom_attributes)) {
                                        foreach ($exresult->custom_attributes as $attributes) {
                                            if ($attributes->attribute_code == 'description') {
                                                $diffrentDescription = $attributes->value;
                                            }
                                        }
                                    }

                                    if (trim(strip_tags(strtolower($englishDescription))) != trim(strip_tags(strtolower($diffrentDescription))) && ! empty($diffrentDescription)) {
                                        $result->{$language} = 'Yes';
                                    } else {
                                        $result->{$language} = 'No';
                                    }
                                }
                            }
                        }
                        $result->skuid = $sku->sku;
                        $result->store_website_id = $sku->websiteid;
                        $products[] = $result;
                    } else {
                        $result->success = false;
                    }
                } catch (\Exception $e) {
                    \Log::info('Error from LogListMagentoController 448' . $e->getMessage());
                }
            }
            // dd($products);
            if (! empty($products)) {
                $data = collect($this->processProductAPIResponce($products));
                // dd($data);
                foreach ($data as $value) {
                    if ($value['success']) {
                        $StoreWebsiteProductCheck = \App\StoreWebsiteProductCheck::where('website_id', $value['store_website_id'])->first();
                        $addItem = [
                            'website_id' => $value['store_website_id'],
                            'website' => implode(',', $value['websites']),
                            'sku' => $value['sku'],
                            'size' => $value['size'],
                            'brands' => $value['brands'],
                            'dimensions' => $value['dimensions'],
                            'composition' => $value['composition'],
                            //'images' => $value->composition,
                            'english' => ! empty($value['english']) ? $value['english'] : 'No',
                            'arabic' => ! empty($value['arabic']) ? $value['arabic'] : 'No',
                            'german' => ! empty($value['german']) ? $value['german'] : 'No',
                            'spanish' => ! empty($value['spanish']) ? $value['spanish'] : 'No',
                            'french' => ! empty($value['french']) ? $value['french'] : 'No',
                            'italian' => ! empty($value['italian']) ? $value['italian'] : 'No',
                            'japanese' => ! empty($value['japanese']) ? $value['japanese'] : 'No',
                            'korean' => ! empty($value['korean']) ? $value['korean'] : 'No',
                            'russian' => ! empty($value['russian']) ? $value['russian'] : 'No',
                            'chinese' => ! empty($value['chinese']) ? $value['chinese'] : 'No',
                        ];

                        if ($StoreWebsiteProductCheck == null) {
                            $StoreWebsiteProductCheck = \App\StoreWebsiteProductCheck::create($addItem);
                        } else {
                            $StoreWebsiteProductCheck->where('website_id', $value['store_website_id'])->update($addItem);
                        }
                    }
                }

                if (! empty($data)) {
                    return DataTables::collection($data)->toJson();
                } else {
                    return response()->json(['data' => null, 'message' => 'success'], 200);
                }
            } else {
                return response()->json(['data' => null, 'message' => 'success'], 200);
            }
        }
        // dd($request->productSkus);
    }

    public function errorReporting(Request $request)
    {
        $log = \App\Loggers\LogListMagento::leftJoin('product_push_error_logs as ppel', 'ppel.log_list_magento_id', 'log_list_magentos.id')
            ->leftJoin('store_websites as sw', 'sw.id', 'log_list_magentos.store_website_id')
            ->groupBy('ppel.url')
            ->where('ppel.response_status', 'error')
            ->where('ppel.url', '!=', '')
            ->where('ppel.created_at', '>=', date('Y-m-d', strtotime('-7 days')))
            ->select([\DB::raw('count(*) as total_error'), 'ppel.url', 'sw.website'])
            ->orderBy('total_error', 'desc')
            ->get();

        return view('logging.partials.log-count-error', compact('log'));
    }

    public function getLatestProductForPush(Request $request)
    {
        $data = StoreMagentoApiSearchProduct::orderBy('id', 'DESC');
        if ($request->website_name && $request->limit) {
            $data = $data->where('website', 'LIKE', "%$request->website_name%")->limit($request->limit)->get();
        } elseif ($request->limit) {
            $data = $data->limit($request->limit)->get();
        } elseif ($request->website_name) {
            $data = $data->where('website', 'LIKE', "%$request->website_name%")->get();
        }

        return view('logging.search-magento-api-call', compact('data'));

        //
        // $produts      = \App\Loggers\LogListMagento::join("products as p", "p.id", "log_list_magentos.product_id")->where("sync_status", "success")->groupBy("product_id", "store_website_id")->limit($request->limit)->orderBy("log_list_magentos.id", "desc")->get();
        // $listToBeSend = [];
        // if (!$produts->isEmpty()) {
        //     foreach ($produts as $p) {
        //         $listToBeSend[] = [
        //             "sku"       => $p->sku . "-" . $p->color,
        //             "websiteid" => $p->store_website_id,
        //         ];
        //     }
        // }

        // return response()->json(["code" => 200, "products" => $listToBeSend]);
    }

    public function productInformation(Request $request)
    {
        $product = \App\Product::find($request->product_id);
        if ($product) {
            $estimated_minimum_days = 0;
            $supplier = \App\Supplier::join('product_suppliers', 'suppliers.id', 'product_suppliers.supplier_id')
                ->where('product_suppliers.product_id', $product->id)
                ->select(['suppliers.*', 'product_suppliers.supplier_link'])
                ->first();
            if ($supplier) {
                $estimated_minimum_days = is_numeric($supplier->est_delivery_time) ? $supplier->est_delivery_time : 0;
            }

            $data = [];
            $data['sku'] = $product->sku . MagentoHelperv2::SKU_SEPERATOR . $product->color;
            $data['description'] = $product->short_description;
            $data['name'] = html_entity_decode(strtoupper($product->name), ENT_QUOTES, 'UTF-8');
            $data['price'] = $product->price;
            $data['composition'] = $product->composition;
            $data['material'] = $product->color;
            $data['country_of_manufacture'] = $product->made_in;
            $data['brands'] = ($product->brands) ? $product->brands->name : '-';
            $data['sizes'] = $product->size_eu;
            $data['dimensions'] = 'L-' . $product->lmeasurement . ',H-' . $product->hmeasurement . ',D-' . $product->dmeasurement;
            $data['stock'] = $product->stock;
            $data['estimated_minimum_days'] = $estimated_minimum_days;
            $data['estimated_maximum_days'] = $estimated_minimum_days + 7;
            $data['supplier_link'] = $product->supplier_link;

            $category = [];
            if ($product->categories) {
                $categories = $product->categories;
                if ($categories) {
                    $category[] = $categories->title;
                    $parent = $categories->parent;
                    if ($parent) {
                        $category[] = $parent->title;
                        $parent = $parent->parent;
                        if ($parent) {
                            $category[] = $parent->title;
                            $parent = $parent->parent;
                            if ($parent) {
                                $category[] = $parent->title;
                            }
                        }
                    }
                }
            }

            $data['category'] = implode(' > ', $category);

            return view('logging.partials.product-information', compact('data'));
        }
    }

    public function productPushInformation(Request $request)
    {
        // ProductPushInformation::truncate();
        // ProductPushInformationHistory::truncate();
        // ProductPushInformationSummery::truncate();
        // dd('asdfsd');

        $logListMagentos = ProductPushInformation::with('storeWebsite')->orderBy('product_id', 'DESC');
        $selected_brands = $request->brand_names;
        $selected_categories = $request->category_names;
        $selected_website = $request->website_name;
        if (($selected_brands && count($selected_brands)) || ($selected_categories && count($selected_categories))) {
            $skus = ProductPushInformation::filterProductSku($selected_categories, $selected_brands);
            foreach ($skus as $sku) {
                $logListMagentos = $logListMagentos->orWhere('sku', 'like', '%' . $sku . '%');
            }
        }
        if ($selected_brands && count($selected_brands)) {
            $selected_brands = Brand::whereIn('id', $selected_brands)->get();
        }

        if ($selected_categories && count($selected_categories)) {
            $selected_categories = Category::whereIn('id', $selected_categories)->get();
        }
        if ($selected_website) {
            $selected_website = StoreWebsite::where('id', $selected_website)->first();
            $logListMagentos = $logListMagentos->whereHas('storeWebsite', function ($q) use ($selected_website) {
                $q->where('id', $selected_website->id);
            });
        }
        $allWebsiteUrl = StoreWebsite::with('productCsvPath')->get();
        // dd($allWebsiteUrl);
        if (! empty($request->filter_product_id)) {
            $logListMagentos->where('product_id', 'LIKE', '%' . $request->filter_product_id . '%');
        }

        if (! empty($request->filter_product_sku)) {
            $logListMagentos->where('sku', 'LIKE', '%' . $request->filter_product_sku . '%');
        }

        if (isset($request->filter_product_status)) {
            $logListMagentos->where('status', 'LIKE', '%' . $request->filter_product_status . '%');
        }
        //status list
        $logListMagentos = $logListMagentos->paginate(Setting::get('pagination'));
        $dropdownList = ProductPushInformation::select('status')->distinct('status')->get();
        $total_count = ProductPushInformation::get()->count();

        $productPushSummeries = ProductPushInformationSummery::with(['brand', 'category', 'storeWebsite'])
            ->whereDate('created_at', '=', Carbon::today())
            ->latest()
            ->get();

        return view('logging.magento-push-information', compact('logListMagentos', 'total_count', 'dropdownList', 'allWebsiteUrl', 'selected_categories', 'selected_brands', 'selected_website', 'productPushSummeries'));
    }

    public function updateProductPushInformation(Request $request)
    {
        $row = 0;
        $arr_id = [];
        $is_file_exists = null;

        // $file_url =public_path('60f89208edcc4_product.csv');
        $file_url = $request->website_url;
        if (! $file_url) {
            return response()->json(['error' => 'Please enter url']);
        }

        $client = new Client();

        try {
            // $response = $client->get($url);
            $promise = $client->request('GET', $file_url);
            $is_file_exists = true;
        } catch (ClientException $e) {
            $is_file_exists = false;

            Log::channel('product_push_information_csv')->info('file-url:' . $file_url . '  and error: ' . $e->getMessage());

            return response()->json(['error' => 'file not exists']);
        }

        if ($is_file_exists) {
            if (($handle = fopen($file_url, 'r')) !== false) {
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $row++;
                    if ($row > 1) {
                        $availableProduct = Product::where('sku', $data[1])->first();
                        $real_product_id = null;
                        if ($availableProduct) {
                            $real_product_id = $availableProduct->id ?? null;
                        }
                        $updated = ProductPushInformation::updateOrCreate(
                            ['product_id' => $data[0],
                                'store_website_id' => $request->store_website_id,
                            ], [
                                'sku' => $data[1],
                                'status' => $data[2],
                                'quantity' => $data[3] > 0 ? $data[3] : 0,
                                'stock_status' => $data[4],
                                'is_added_from_csv' => 1,
                                'is_available' => 1,
                                'real_product_id' => $real_product_id,
                            ]);
                        $arr_id[] = $updated->product_id;
                    }
                }
            }
            fclose($handle);
        }

        ProductPushInformation::whereNotIn('product_id', $arr_id)->where('store_website_id', $request->store_website_id)->where('is_available', 1)->update(['is_available' => 0]);

        return response()->json(['message' => 'Data updated succesfully']);
    }

    public function updateProductPushWebsite(Request $request)
    {
        foreach ($request->all() as $key => $req) {
            if ($key == '_token') {
                continue;
            }

            $updated = WebsiteProductCsv::updateOrCreate(['store_website_id' => $key], [
                'path' => $req,
            ]);

            // WebsiteProductCsv::where('store_website_id',$key)->update(['path'=>$req]);
        }

        return response()->json(['code' => 200, 'message' => 'Paths update succesfully']);
    }

    public function productPushHistories(Request $request, $product_id)
    {
        // dd($request->all());
        $history = ProductPushInformationHistory::with('user')->where('product_id', $product_id)->where('store_website_id', $request->website_id)->latest()->get();

        return response()->json($history);
    }

    public function deleteMagentoApiData(Request $request)
    {
        if ($request->days) {
            if ($request->days == 60) {
                StoreMagentoApiSearchProduct::where('created_at', '>=', now()->subMinutes(60))->delete();
            }
            if ($request->days == 1) {
                StoreMagentoApiSearchProduct::where('created_at', '>=', now()->subDays(1))->delete();
            }
            if ($request->days == 7) {
                StoreMagentoApiSearchProduct::where('created_at', '>=', now()->subDays(7))->delete();
            }
            if ($request->days == 30) {
                StoreMagentoApiSearchProduct::where('created_at', '>=', now()->subDays(30))->delete();
            }
            if ($request->days == 100) {
                StoreMagentoApiSearchProduct::truncate();
            }

            return response()->json(['code' => 200, 'message' => 'Record Deleted Successfull']);
        }
        $data = StoreMagentoApiSearchProduct::find($request->id);
        $data->delete();

        return response()->json(['status' => true]);
    }

    public function retryFailedJob(Request $request)
    {
        $logListMagento = \App\Loggers\LogListMagento::query();

        if (empty($request->start_date) && empty($request->start_date)) {
            return response()->json(['code' => 500, 'message' => 'Please select start date and end date for valid result']);
        }

        if ($request->start_date != null) {
            $logListMagento->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date != null) {
            $logListMagento->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->store_website_id != null) {
            $logListMagento->where('store_website_id', $request->store_website_id);
        }

        if ($request->keyword != null) {
            $logListMagento->where('product_id', $request->keyword);
        }

        $products = $logListMagento->where(function ($q) {
            $q->whereIn('sync_status', ['error', 'size_chart_needed', 'image_not_found', 'translation_not_found'])->orWhereNull('queue_id');
        })->groupBy('store_website_id', 'product_id')->get();

        if (! $products->isEmpty()) {
            foreach ($products as $product) {
                if ($product->product && $product->storeWebsite) {
                    if (empty($product->queue)) {
                        $product->queue = \App\Helpers::createQueueName($product->storeWebsite->title);
                    }
                    $product->tried = $product->tried + 1;
                    $product->save();
                    PushToMagento::dispatch($product->product, $product->storeWebsite, $product)->onQueue($product->queue);
                }
            }
        }

        return response()->json(['code' => 200, 'message' => 'Total Request found :' . $products->count()]);
    }

    public function sendLiveProductCheck(Request $request)
    {
        $logListMagento = \App\Loggers\LogListMagento::query();

        if (empty($request->start_date) && empty($request->start_date)) {
            return response()->json(['code' => 500, 'message' => 'Please select start date and end date for valid result']);
        }

        if ($request->start_date != null) {
            $logListMagento->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date != null) {
            $logListMagento->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->store_website_id != null) {
            $logListMagento->where('store_website_id', $request->store_website_id);
        }

        if ($request->keyword != null) {
            $logListMagento->where('product_id', $request->keyword);
        }

        $products = $logListMagento->where(function ($q) {
            $q->where('sync_status', 'success');
        })->groupBy('store_website_id', 'product_id')->get();

        if (! $products->isEmpty()) {
            $requests = [];

            foreach ($products as $product) {
                if ($product->product && $product->storeWebsite) {
                    $productModel = $product->product;
                    if (isset($requests[$product->store_website_id])) {
                        $requests[$product->store_website_id]['sku'][] = $productModel->sku . '-' . $productModel->color;
                    } else {
                        $requests[$product->store_website_id] = [
                            'website' => $product->storeWebsite->magento_url,
                            'sku' => [$productModel->sku . '-' . $productModel->color],
                        ];
                    }
                }
            }

            if (! empty($requests)) {
                foreach ($requests as $req) {
                    //PRODUCT_CHECK_PY
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('POST', config('constants.product_check_py') . '/sku-scraper-start', [
                        'form_params' => $req,
                    ]);
                }
            }
        }

        return response()->json(['code' => 200, 'message' => 'Total Request send :' . $products->count()]);
    }

    public function updateLiveProductCheck(Request $request)
    {
        $storeWebsite = \App\StoreWebsite::where('magento_url', 'like', '%' . $request->get('website') . '%')->first();
        $message = $request->get('message', 'Error');
        if ($storeWebsite) {
            //get the product based on sku
            $sku = explode('-', $request->get('sku'));
            $product = \App\Product::where('sku', $sku[0])->first();
            if ($product) {
                $sws = \App\StoreWebsiteProductScreenshot::create([
                    'product_id' => $product->id,
                    'sku' => $request->get('sku'),
                    'store_website_name' => $request->get('website'),
                    'store_website_id' => $storeWebsite->id,
                    'status' => $message,
                ]);

                if (strtolower($message) == 'success') {
                    $image = $request->get('image');
                    if (! empty($image)) {
                        $content = base64_decode($image);
                        $media = MediaUploader::fromString($content)->toDirectory('/store-website-product-screeenshot')->useFilename(uniqid(true))->upload();
                        $sws->attachMedia($media, config('constants.media_tags'));
                        $sws->image_path = $media->getUrl();
                        $sws->save();
                    }
                }

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Request has been stored successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Product not found in records']);
            }
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Website not found in records']);
        }
    }

    public function getLiveScreenshot(Request $request)
    {
        $logListMagento = \App\Loggers\LogListMagento::find($request->get('id', 0));

        return view('logging.partials.get-screenshot', compact('logListMagento'));
    }

    public function updateProductPushInformationSummery(Request $request)
    {
        $productPushSummeries = ProductPushInformationSummery::with(['brand', 'category', 'storeWebsite'])->whereDate('created_at', '>=', $request->startDate)->whereDate('created_at', '<=', $request->endDate)->get();

        return view('logging.partials.product-push-information-summery', compact('productPushSummeries'));
    }

    public function dailyPushLog()
    {
        $logListMagentos = \App\Loggers\LogListMagento::orderBy('log_list_magentos.created_at', 'DESC')
            ->selectRaw('COUNT(`product_id`) as count,store_website_id,DATE(log_list_magentos.created_at) as dateonly')
            ->where('log_list_magentos.created_at', '>', now()->subDays(30)->endOfDay())
            ->where('log_list_magentos.sync_status', 'success')
            ->groupBy(['store_website_id', 'dateonly'])
            ->get()->toArray();
        $websites = \App\Loggers\LogListMagento::distinct('store_website_id')->leftJoin('store_websites as sw', 'sw.id', '=', 'log_list_magentos.store_website_id')->pluck('sw.title', 'store_website_id')->toArray();
        // dd($logListMagentos);
        $count = count($websites) + 1;
        $response = [];
        if (count($logListMagentos)) {
            foreach ($logListMagentos as $log) {
                //if(isset($response[$log["created_at"]])){
                $response[$log['dateonly']][$log['store_website_id']] = $log['count'];
                //    }
            }
        }

        return view('logging.magento-push-daily-message', compact('response', 'websites', 'count'));
    }

    /*
    * logMagentoApisAjax : Return log of product api
    */
    public function logMagentoApisAjax(Request $request)
    {
        if ($request->ajax()) {
            $log = \App\LogMagentoApi::where('magento_api_search_product_id', $request->get('id'))->get()->toArray();
            if ($log) {
                return response()->json([
                    'data' => $log,
                ], 200);
            }

            return response()->json([
                'data' => [],
            ], 200);
        }
    }

    public function syncStatusColor(Request $request)
    {
        $statusColor = $request->all();
        $data = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $cronStatus = LogListMagentoSyncStatus::find($key);
            $cronStatus->color = $value;
            $cronStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }
}
