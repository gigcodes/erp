<?php

namespace App\Http\Controllers;

use App\MessageQueue;
use App\Product;
use App\Task;
use App\Helpers;
use App\User;
use App\Instruction;
use App\InstructionCategory;
use App\ReplyCategory;
use App\DeveloperTask;
use App\DailyActivity;
use App\Order;
use App\Purchase;
use App\Email;
use App\Supplier;
use App\Review;
use App\PushNotification;
use App\CronJob;
use App\UserProduct;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CroppedImageReference;
use App\Helpers\StatusHelper;
use Cache;
use App\Vendor;
use App\ChatMessage;
use App\Customer;



class MasterControlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $start = $request->range_start ?  "$request->range_start 00:00" : Carbon::now()->subDay()->format('Y-m-d 00:00');
      $end = $request->range_end ? "$request->range_end 23:59" : Carbon::now()->subDay()->format('Y-m-d 23:59');

      Cache::remember('cropped_image_references', 15, function() {
            return CroppedImageReference::count(); 
        });

      $cropReference = Cache::get( 'cropped_image_references' );
      
      Cache::remember('pending_crop_reference', 15, function() {
            return Product::where('status_id',StatusHelper::$autoCrop)->where('stock','>=',1)->count();
        });

      $pendingCropReferenceProducts = Cache::get( 'pending_crop_reference' );


      Cache::remember('crop_reference_week_count', 15, function() {
            return CroppedImageReference::where('created_at', '>=', \Carbon\Carbon::now()->subDays(7)->startOfDay())->count();
        });

      $cropReferenceWeekCount = Cache::get( 'crop_reference_week_count' );
      
      Cache::remember('crop_reference_daily_count', 15, function() {
            return CroppedImageReference::whereDate('created_at', Carbon::today())->count();
        });

      $cropReferenceDailyCount = Cache::get( 'crop_reference_daily_count' );
      
      Cache::remember('pending_crop_category', 15, function() {
            return Product::where('status_id',StatusHelper::$attributeRejectCategory)->where('stock','>=',1)->count();
        });

      $pendingCropReferenceCategory =  Cache::get( 'pending_crop_category' );

      Cache::remember('status_count', 15, function() {
            return StatusHelper::getStatusCount();
        });

      $productStats = Cache::get( 'status_count' );

      Cache::remember('result_scraped_product_in_stock', 15, function() {
            $sqlScrapedProductsInStock = "
                SELECT
                    COUNT(DISTINCT(ls.sku)) as ttl
                FROM
                    suppliers s
                JOIN 
                    scrapers sc 
                ON 
                    s.id=sc.supplier_id    
                JOIN 
                    log_scraper ls 
                ON 
                    ls.website=sc.scraper_name
                WHERE
                    s.supplier_status_id=1 AND 
                    ls.validated=1 AND
                    ls.website!='internal_scraper' AND
                    ls.updated_at > DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY) 
            ";

            return DB::select($sqlScrapedProductsInStock);
        });

      $resultScrapedProductsInStock = Cache::get( 'result_scraped_product_in_stock' );
        
        //Getting All chats
        $chat = ChatMessage::where('created_at','>=', Carbon::now()->subDay()->toDateTimeString());

        $vendorChats = $chat->select('vendor_id')->whereNotNull('vendor_id')->groupBy('vendor_id')->get()->toArray();
        foreach ($vendorChats as $vendorChat) {
            $vendorArrays[] = $vendorChat['vendor_id'];
        }
        
        $customerChats = $chat->select('customer_id')->whereNotNull('customer_id')->groupBy('customer_id')->get()->toArray();
        foreach ($customerChats as $customerChat) {
            $customerArrays[] = $customerChat['customer_id'];
        }
        
        $supplierChats = $chat->select('supplier_id')->whereNotNull('supplier_id')->groupBy('supplier_id')->get()->toArray();
        foreach ($supplierChats as $supplierChat) {
            $supplierArrays[] = $supplierChat['supplier_id'];
        }

        if(!isset($vendorArrays)){
            $vendorArrays = [];
        }

        if(!isset($customerArrays)){
            $customerArrays = [];
        }

        if(!isset($supplierArrays)){
            $supplierArrays = [];
        }

        $vendors = Vendor::whereIn('id',$vendorArrays)->get();

        $customers = Customer::select('name','phone')->whereIn('id',$customerArrays)->get();
        
        $suppliers = Supplier::whereIn('id',$supplierArrays)->get();
        
        $replies = \App\Reply::where("model","Vendor")->whereNull("deleted_at")->pluck("reply","id")->toArray();

     return view('mastercontrol.index', [
        'start' => $start, 
        'end' => $end , 
        'cropReference' => $cropReference,
        'pendingCropReferenceProducts' => $pendingCropReferenceProducts , 
        'pendingCropReferenceCategory' => $pendingCropReferenceCategory,
        'productStats' => $productStats,
        'resultScrapedProductsInStock' => $resultScrapedProductsInStock,
        'cropReferenceWeekCount' => $cropReferenceWeekCount,
        'cropReferenceDailyCount' => $cropReferenceDailyCount,
        'chatSuppliers' => $suppliers,
        'chatCustomers' => $customers,
        'chatVendors' => $vendors,
        'replies' => $replies,
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

    public function clearAlert(Request $request)
    {
      PushNotification::where('model_type', 'MasterControl')->delete();

      return redirect()->route('mastercontrol.index');
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
        //
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
