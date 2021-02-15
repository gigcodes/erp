<?php

namespace App\Http\Controllers\Logging;

use App\ListingPayments;
use App\Loggers\LogListMagento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DataTables;
use seo2websites\MagentoHelper\MagentoHelperv2;

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
    ->orderBy('log_list_magentos.created_at', 'ASC');

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
          'sw.magento_url as website_url',
          'log_list_magentos.user_id as log_user_id'
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
          if($request->user){
            if($item->log_user_id != $request->user)
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
          if($item->log_user_id){
            $logListMagentos[$key]['log_user_name'] = \App\User::find($item->log_user_id)->name;
          }else{
            $logListMagentos[$key]['log_user_name'] = "";
          }
          
      }
      $users = \App\User::all();
     // dd($logListMagentos);
      // For ajax
      if ($request->ajax()) {
          return response()->json([
              'tbody' => view('logging.partials.listmagento_data', compact('logListMagentos'))->render(),
              'links' => (string) $logListMagentos->render()
          ], 200);
      }
      $filters = $request->all();
    // Show results
    return view('logging.listmagento', compact('logListMagentos', 'filters', 'users'))
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

  public function showMagentoProductAPICall(Request $request){
    return view('logging.magento-api-call');
  }
  protected function processProductAPIResponce($products){
    $prepared_products_data = array();
    $websites=array();
    $category_names = array();
    $size ='';
    $brands='';
    $composition='';
    foreach ($products as $value) {
      if(isset($value->extension_attributes)){
        foreach ($value->extension_attributes->website_ids as $vwi) {
          $websites[]=\App\StoreWebsite::where('id',$vwi)->value('title');
        }
      }
      if(isset($value->custom_attributes)){
      foreach ($value->custom_attributes as $v) {
        if($v->attribute_code === "category_ids"){
          foreach ($v->value as $key =>$cat_id) {
            $category_names[] = \App\Category::where('id',$key+1)->value('title');
          }
        }
         if($v->attribute_code === "size_v2" || $v->attribute_code ===  "size"){
          $size = $v->value;
        }
        if($v->attribute_code === "brands" ){
          $brands = $v->value;
        }
        if($v->attribute_code === "composition" ){
          $composition = $v->value;
        }
        $prepared_products_data[$value->sku]=[
          'store_website_id' => $value->store_website_id,
          'magento_id'=>$value->id,
          'sku'=>$value->sku,
          'product_name'=>$value->name,
          'media_gallery_entries'=>$value->media_gallery_entries,
          'websites'=>array_filter($websites),
          'category_names'=>$category_names,
          'size'=>$size,
          'brands'=>$brands,
          'composition'=>$composition,
          'dimensions'=> $value->size ?? 0,
          'english'=>'Yes',
          'arabic'=>'Yes',
          'german'=>'Yes',
          'spanish'=>'No',
          'french'=>'No',
          'italian'=>'No',
          'japanese'=>'No',
          'korean'=>'No',
          'russian'=>'No',
          'chinese'=>'No'
        ];

      }
    }

      $category_names =[];
      $websites=[];
      $size ='';
      $brands='';
      $composition='';
    }
    return $prepared_products_data;
  }
  function key_value_pair_exists(array $haystack, $key) {
    return array_key_exists($key, $haystack);
  }
  public function getMagentoProductAPIAjaxCall(Request $request){
    if($request->ajax()){
    //  $sku =$request->productSkus; //'["SB0AB15C50GK92","SW2S0P39JZI","EE4791White-45.5","EE4791White","A0510XXAS5Black-36"]';
      $products = array();
      $skudata = json_decode($request->productSkus);
      $magentoHelper = new MagentoHelperv2;
        
      $client = new \GuzzleHttp\Client();
      foreach ($skudata as $sku) {
        try {
          // $get_store_website = \App\StoreWebsite::find($sku->websiteid);
           $get_store_website = \App\StoreWebsite::find($sku->websiteid);
          $result = $magentoHelper->getProductBySku($sku->sku,$get_store_website);
          // $req = $client->get('https://magento-501091-1587493.cloudwaysapps.com/rest/V1/products/6378180NP001000Black-L',[
          //  https:\/\/magento-501091-1587493.cloudwaysapps.com\/rest\/V1\/PRODUCTS\/6378180NP001000Black-L"//  
          // 'headers' => [
          //     'Accept'     => 'application/json',
          //     'Authorization'=>'Bearer 7e9pvvgo4u5kel2xlchlj4hmgjb0lu6s'
          //                                7e9pvvgo4u5kel2xlchlj4hmgjb0lu6s
          //   ]
          // ]);
          // $response = $req->getBody()->getContents();
          $result->store_website_id = $sku->websiteid;
          $products []= $result;
          
        } catch (\Exception $e) {
        }
      }
      if(!empty($products)){

        $data =collect($this->processProductAPIResponce($products));
        //print_r($data);
         foreach ($data as $value) {
          $StoreWebsiteProductCheck = \App\StoreWebsiteProductCheck::where('website_id',$value['store_website_id'])->first();
          $addItem =  ['website_id' => $value['store_website_id'],
          'website' => implode(",",$value['websites']),
          'sku' => $value['sku'],
          'size' => $value['size'],
          'brands' => $value['brands'],
          'dimensions' => $value['dimensions'],
          'composition' => $value['composition'],
          //'images' => $value->composition,
          'english'=>'Yes',
          'arabic'=>'Yes',
          'german'=>'Yes',
          'spanish'=>'No',
          'french'=>'No',
          'italian'=>'No',
          'japanese'=>'No',
          'korean'=>'No',
          'russian'=>'No',
          'chinese'=>'No'];
          if($StoreWebsiteProductCheck == null){
            $StoreWebsiteProductCheck = \App\StoreWebsiteProductCheck::create($addItem);
          }else{
            $StoreWebsiteProductCheck->where('website_id',$value['store_website_id'])->update($addItem);
          }
         }
        if(!empty($data)){
          return DataTables::collection($data)->toJson();
        }else{
          return response()->json(['data'=>null,'message'=>'success'],200);
        }

      }else{
        return response()->json(['data'=>null,'message'=>'success'],200);
      }
    }
    // dd($request->productSkus);
  }
}
