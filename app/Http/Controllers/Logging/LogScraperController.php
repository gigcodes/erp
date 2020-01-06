<?php

namespace App\Http\Controllers\Logging;

use App\Loggers\LogScraper;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\DeveloperTask;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Setting;

class LogScraperController extends Controller
{
    public function index(Request $request)
    {
        $scraperLogs = DB::table('log_scraper')->where('validated', 0);

        if (!empty($request->website)) {
            $scraperLogs = $scraperLogs->where('website', 'LIKE', '%' . $request->website . '%');
        }

        if (!empty($request->result)) {
            $scraperLogs = $scraperLogs->where('validation_result', 'LIKE', '%' . $request->result . '%');
        }

        $scraperLogs = $scraperLogs->orderBy('created_at', 'DESC')->paginate(25);

        return view('log.scraper', compact('scraperLogs'));
    }

    public function logSKU(Request $request)
    {
       
        $logScrapper = LogScraper::select('log_scraper.*','scrapers.inventory_lifetime')->leftJoin('scrapers', function($join) {
            $join->on('log_scraper.website', '=', 'scrapers.scraper_name');
        });

        

        // Filters
        if (!empty($request->product_id)) {
            $logScrapper->where('id', $request->product_id);
        }

       if (!empty($request->sku)) {
            $logScrapper->where('sku', 'LIKE', '%' . $request->sku . '%');
        }

        if (!empty($request->brand)) {
            $logScrapper->whereIn('brand', $request->brand);
        }

        if (!empty($request->category)) {
                $cats = explode(',',$request->category);
            foreach ($cats as $cat) {
                $cat = preg_replace('/\s+/', '', $cat);
               $logScrapper->where('category', 'LIKE', '%'.$cat.'%');
               
            }
        }

        if (!empty($request->supplier)) {
            $logScrapper->whereHas('scraper', function ($qu) use ($request) {
                   $qu->whereIn('scraper_name', request('supplier'));
            });       
        }

        if (!empty($request->validate)) {
            if($request->validate == 2){
                $logScrapper->where('validated', 0);
            }else{
               $logScrapper->where('validated', $request->validate); 
            }
        }

        $failed = $logScrapper->where('validation_result', 'LIKE', '%SKU failed regex test%')->count();
        //last_update < DATE_SUB(NOW(), INTERVAL sp.inventory_lifetime DAY)
        
        // Get paginated result
        $logScrapper->whereRaw('log_scraper.updated_at > DATE_SUB(NOW(), INTERVAL scrapers.inventory_lifetime DAY)');

        $logScrappers = $logScrapper->paginate(25)->appends(request()->except(['page']));

        $existingIssues = DeveloperTask::whereNotNull('reference')->get();

        $pendingIssues = DeveloperTask::whereNotNull('reference')->where('status','Issue')->count();

        $lastCreatedIssue = DeveloperTask::whereNotNull('reference')->orderBy('created_at','desc')->first();

        $logs = LogScraper::select('id','category','properties')->whereNotNull('category')->groupBy('category')->get();
        foreach ($logs as $log) {
            $category_selection[] = str_replace(',','>',$log->dataUnserialize($log->category));
        }

        $requestParam = request()->except(['page']);   
        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.listsku_data', compact('logScrappers','category_selection','failed','existingIssues','pendingIssues','lastCreatedIssue','requestParam'))->render(),
                'links' => (string)$logScrappers->render(),
                'totalFailed' => $failed,
            ], 200);
        }

        

        // Show results
        return view('logging.product-sku', compact('logScrappers','category_selection','failed','existingIssues','lastCreatedIssue','pendingIssues','requestParam'));
    
    }

    public function logSKUErrors(Request $request)
    {
       

        $logScrapper = LogScraper::select('log_scraper.*','brands.sku_search_url','sku_formats.sku_examples','sku_formats.sku_format','scrapers.inventory_lifetime')->leftJoin('scrapers', function($join) {
            $join->on('log_scraper.website', '=', 'scrapers.scraper_name');
        })->leftJoin('brands', function($join){
            $join->on('log_scraper.brand','=','brands.name');
        })->leftJoin('sku_formats',function($join){
            $join->on('brands.id','sku_formats.brand_id');
        });

        
        // Filters
        if (!empty($request->product_id)) {
            $logScrapper->where('id', $request->product_id);
        }

       if (!empty($request->sku)) {
            $logScrapper->where('sku', 'LIKE', '%' . $request->sku . '%');
        }

        if (!empty($request->brand)) {
            $logScrapper->whereIn('brand', $request->brand);
        }

        if (!empty($request->category)) {
                $cats = explode(',',$request->category);
            foreach ($cats as $cat) {
                $cat = preg_replace('/\s+/', '', $cat);
               $logScrapper->where('category', 'LIKE', '%'.$cat.'%');
               
            }
        }

        if (!empty($request->supplier)) {
            $logScrapper->whereHas('scraper', function ($qu) use ($request) {
                   $qu->whereIn('scraper_name', request('supplier'));
            });       
        }

        if (!empty($request->validate)) {
            if($request->validate == 2){
                $logScrapper->where('validated', 0);
            }else{
               $logScrapper->where('validated', $request->validate); 
            }
        }

        $logScrapper->where('validation_result', 'LIKE', '%SKU failed regex test%');


        if(!empty($request->order) || $request->order == 0){
            if($request->order == 1){
                $logScrapper->select('*', \DB::raw('count("log_scraper.website") as total'))->orderBy('total','asc');
            }else{
                $logScrapper->select('*', \DB::raw('count("log_scraper.website") as total'))->orderBy('total','DESC');
            }
        }
        
        $logScrapper->groupBy('website')->groupBy('brand');

        if($request->custom != null && $request->custom != 0){
            $scraps = $logScrapper->get();
            foreach ($scraps as $scrap) {
               $example = $scrap->sku_examples;
               if($example == null){
                    continue;
               } 
               $sample = explode(',',$example);
               $string = str_replace(' ', '-', $sample[0]); // Replaces all spaces with hyphens.
               $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
                
                if(strlen($string) < strlen($scrap->sku)){
                    $scrapArray[] = $scrap;
                }
            }
            
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = Setting::get('pagination');
            $currentItems = array_slice($scrapArray, $perPage * ($currentPage - 1), $perPage);

            $log = new LengthAwarePaginator($currentItems, count($scrapArray), $perPage, $currentPage, [
                'path' => LengthAwarePaginator::resolveCurrentPath()
            ]);

            $logScrappers = $log->appends(request()->except(['page']));
            
        }else{

            $logScrappers = $logScrapper->paginate(25)->appends(request()->except(['page']));

        }
        

        $failed = $logScrappers->total();

        $existingIssues = DeveloperTask::whereNotNull('reference')->get();

        $pendingIssues = DeveloperTask::whereNotNull('reference')->where('status','Issue')->groupBy('responsible_user_id')->groupBy('status')->get();

        $pendingIssuesCount = DeveloperTask::whereNotNull('reference')->where('status','Issue')->count();

        $lastCreatedIssue = DeveloperTask::whereNotNull('reference')->orderBy('created_at','desc')->first();

        $logs = LogScraper::select('id','category','properties')->whereNotNull('category')->groupBy('category')->get();
        foreach ($logs as $log) {
            $category_selection[] = str_replace(',','>',$log->dataUnserialize($log->category));
        }

        $requestParam = request()->except(['page']);   
        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.listsku_errors_data', compact('logScrappers','category_selection','failed','existingIssues','pendingIssues','lastCreatedIssue','requestParam','pendingIssuesCount'))->render(),
                'links' => (string)$logScrappers->render(),
                'totalFailed' => $failed,
            ], 200);
        }

        

        // Show results
        return view('logging.product-sku-errors', compact('logScrappers','category_selection','failed','existingIssues','lastCreatedIssue','pendingIssues','requestParam','pendingIssuesCount'));
    
    }
}
