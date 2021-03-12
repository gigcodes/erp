<?php

namespace App\Http\Controllers;

use App\Library\Watson\Model as WatsonManager;
use App\ScrapStatistics;
use App\Services\Whatsapp\ChatApi\ChatApi;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;
use App\ScrapRemark;
use App\ScrapHistory;
use App\Scraper;
use App\User;
use Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Zend\Diactoros\Response\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScrapRemarkExport;

class ScrapStatisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Set dates
        $endDate = date('Y-m-d H:i:s');
        $keyWord = $request->get("term", "");
        $madeby = $request->get("scraper_made_by", 0);
        $scrapeType = $request->get("scraper_type", 0);

        $timeDropDown = self::get_times();

        $serverIds = Scraper::groupBy('server_id')->where('server_id','!=',NULL)->pluck('server_id');

        // Get active suppliers
        $activeSuppliers = Scraper::join("suppliers as s", "s.id", "scrapers.supplier_id")
            ->select('scrapers.id as scrapper_id','scrapers.*', "s.*", "scrapers.full_scrape as scrapers_status")
            ->where('supplier_status_id', 1)
            ->whereIn("scrapper",[1,2])
            ->whereNull('parent_id');

        if (!empty($keyWord)) {
            $activeSuppliers->where(function ($q) use ($keyWord) {
                $q->where("s.supplier", "like", "%{$keyWord}%")->orWhere("scrapers.scraper_name", "like", "%{$keyWord}%");
            });
        }

        if ($madeby > 0) {
            $activeSuppliers->where("scrapers.scraper_made_by", $madeby);
        }

        // if ($request->get("scrapers_status", "") != '') {
        //     $activeSuppliers->where("scrapers.status", $request->get("scrapers_status", ""));
        // }

        if ($scrapeType > 0) {
            $activeSuppliers->where("scraper_type", $scrapeType);
        }

        $activeSuppliers = $activeSuppliers->orderby('s.supplier', 'asc')->get();
        // Get scrape data
        $sql = '
            SELECT
                s.id,
                s.supplier,
                sc.inventory_lifetime,
                sc.scraper_new_urls,
                sc.scraper_existing_urls,
                sc.scraper_total_urls,
                sc.scraper_start_time,
                sc.scraper_logic,
                sc.scraper_made_by,
                sc.server_id,
                sc.id as scraper_id, 
                ls.website,
                ls.ip_address,
                COUNT(ls.id) AS total,
                SUM(IF(ls.validated=0,1,0)) AS failed,
                SUM(IF(ls.validated=1,1,0)) AS validated,
                SUM(IF(ls.validation_result LIKE "%[error]%",1,0)) AS errors,
                SUM(IF(ls.validation_result LIKE "%[warning]%",1,0)) AS warnings,
                MAX(ls.last_inventory_at) AS last_scrape_date,
                IF(MAX(ls.last_inventory_at) < DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY),0,1) AS running
            FROM
                suppliers s
            JOIN
                scrapers sc
            ON 
                sc.supplier_id = s.id
            JOIN
                scraped_products ls 
            ON  
                sc.scraper_name=ls.website
            WHERE
                sc.scraper_name IS NOT NULL AND
                ls.website != "internal_scraper" AND 
                ' . ($request->excelOnly == 1 ? 'ls.website LIKE "%_excel" AND' : '') . '
                ' . ($request->excelOnly == -1 ? 'ls.website NOT LIKE "%_excel" AND' : '') . '
                ls.last_inventory_at > DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)
            GROUP BY
                ls.website
            ORDER BY
                sc.scraper_priority desc
        ';
        $scrapeData = DB::select($sql);

        $allScrapperName = [];

        if (!empty($scrapeData)) {
            foreach ($scrapeData as $data) {
                if (isset($data->id) && $data->id > 0) {
                    $allScrapperName[$data->id] = $data->website;
                }
            }
        }

        $lastRunAt = \DB::table("scraped_products")->groupBy("website")->select([\DB::raw("MAX(last_inventory_at) as last_run_at"),"website"])->pluck("last_run_at","website")->toArray();

        $users = \App\User::all()->pluck("name", "id")->toArray();
        $allScrapper = Scraper::whereNull('parent_id')->pluck('scraper_name', 'id')->toArray();
        // Return view
        return view('scrap.stats', compact('activeSuppliers','serverIds', 'scrapeData', 'users', 'allScrapperName', 'timeDropDown', 'lastRunAt', 'allScrapper'));
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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier' => 'required',
            'type' => 'required',
            'url' => 'required',
        ]);

        $stat = new ScrapStatistics();
        $stat->supplier = $request->get('supplier');
        $stat->type = $request->get('type');
        $stat->url = $request->get('url');
        $stat->description = $request->get('description');
        $stat->save();


        return response()->json([
            'status' => 'Added successfully!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ScrapStatistics $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function show(ScrapStatistics $scrapStatistics)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ScrapStatistics $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function edit(ScrapStatistics $scrapStatistics)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\ScrapStatistics $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ScrapStatistics $scrapStatistics)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ScrapStatistics $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function destroy(ScrapStatistics $scrapStatistics)
    {
        //
    }

    public function assetManager()
    {
        $start = Carbon::now()->format('Y-m-d 00:00:00');
        $end = Carbon::now()->format('Y-m-d 23:59:00');
        // dd('hello');
        return view('scrap.asset-manager');
    }


    public function showHistory(Request $request)
    {

        $remarks = ScrapRemark::where('scrap_id', $request->search)->where('scrap_field',$request->field)->get();

        return response()->json($remarks, 200);
    }

    public function getRemark(Request $request)
    {
        $name = $request->input('name');
        
        $remarks = ScrapRemark::where('scraper_name', $name)->latest()->get();
        $download = $request->input('download');
        return response()->json($remarks, 200);
    }

    public function addRemark(Request $request)
    {
        $remark = $request->input('remark');
        $name = $request->input('id');
        $created_at = date('Y-m-d H:i:s');
        $update_at = date('Y-m-d H:i:s');

        if (!empty($remark)) {
            $remark_entry = ScrapRemark::create([
                'scraper_name' => $name,
                'remark' => $remark,
                'user_name' => Auth::user()->name
            ]);

            $needToSend = request()->get("need_to_send", false);
            $includeAssignTo = request()->get("inlcude_made_by", false);

            if ($needToSend == 1) {
                app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi('31629987287', '971502609192', "SCRAPER-REMARK#" . $name . "\n" . $remark);
                app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi('919004780634', '971502609192', "SCRAPER-REMARK#" . $name . "\n" . $remark);
                if ($includeAssignTo == 1) {
                    $scraper = \App\Scraper::where("scraper_name", $name)->first();
                    if ($scraper) {
                        $sendPer = $scraper->scraperMadeBy;
                        if ($sendPer) {
                            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($sendPer->phone, $sendPer->whatsapp_number, "SCRAPER-REMARK#" . $name . "\n" . $remark);
                        }
                    }
                }
            }
        }

        return response()->json(['remark' => $remark], 200);
    }

    public function updateField(Request $request)
    {

        $fieldName = request()->get("field");
        $fieldValue = request()->get("field_value");
        $search = request()->get("search");
        //dd($search);
        $suplier = \App\Scraper::where("supplier_id", $search)->first();
        
        if(!$suplier){
            $suplier = \App\Scraper::find($search);
        }

            
        if ($suplier) {
            $oldValue = $suplier->{$fieldName};

            if ($fieldName == "scraper_made_by") {
                $oldValue = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : "";
            }

            if ($fieldName == "parent_supplier_id") {
                $oldValue = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : "";
            }

            $suplier->{$fieldName} = $fieldValue;
            $suplier->save();

            
            $suplier = \App\Scraper::where("supplier_id", $search)->first();
            
            if(!$suplier){
                $suplier = \App\Scraper::find($search);
            }



            $newValue = $fieldValue;

            if ($fieldName == "scraper_made_by") {
                $newValue = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : "";
            }

            if ($fieldName == "parent_supplier_id") {
                $newValue = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : "";
            }


            $remark_entry = ScrapRemark::create([
                'scraper_name' => $suplier->scraper_name,
                'remark' => "{$fieldName} updated old value was $oldValue and new value is $newValue",
                'user_name' => Auth::user()->name,
                'scrap_field' => $fieldName
            ]);

        }

        return response()->json(["code" => 200]);

    }


    public function updateScrapperField(Request $request)
    {
        $fieldName = request()->get("field");
        $fieldValue = request()->get("field_value");
        $search = request()->get("search");

        $suplier = \App\Scraper::find($search);
        
        if(!$suplier){
            return response()->json(["code" => 500]);
        }

            
        if ($suplier) {
            $oldValue = $suplier->{$fieldName};

            if ($fieldName == "scraper_made_by") {
                $oldValue = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : "";
            }

            if ($fieldName == "parent_supplier_id") {
                $oldValue = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : "";
            }

            $suplier->{$fieldName} = $fieldValue;
            $suplier->save();

            
            $suplier = \App\Scraper::where("supplier_id", $search)->first();
            
            if(!$suplier){
                $suplier = \App\Scraper::find($search);
            }



            $newValue = $fieldValue;

            if ($fieldName == "scraper_made_by") {
                $newValue = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : "";
            }

            if ($fieldName == "parent_supplier_id") {
                $newValue = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : "";
            }


            $remark_entry = ScrapRemark::create([
                'scrap_id' => $suplier->id,
                'scraper_name' => $suplier->scraper_name,
                'remark' => "{$fieldName} updated old value was $oldValue and new value is $newValue",
                'user_name' => Auth::user()->name,
                'scrap_field' => $fieldName
            ]);

        }

        return response()->json(["code" => 200]);

    }

    public function updatePriority(Request $request)
    {
        $ids = $request->get("ids");
        $prio = count($ids);

        if (!empty($ids)) {
            foreach ($ids as $k => $id) {
                if (isset($id["id"])) {
                    $scrap = \App\Scraper::where("supplier_id", $id["id"])->first();
                    if ($scrap) {
                        $scrap->scraper_priority = $prio;
                        $scrap->save();
                    }
                }
                $prio--;
            }
        }

        return response()->json(["code" => 200]);
    }

    public function getHistory(Request $request)
    {
        $field = $request->get("field", "supplier");
        $value = $request->get("search", "0");

        $history = [];

        if ($value > 0) {
            if ($field == "supplier") {
                $history = ScrapHistory::where("model", \App\Supplier::class)->join("users as u", "u.id", "scrap_histories.created_by")->where("model_id", $value)
                    ->orderBy("created_at", "DESC")
                    ->select("scrap_histories.*", "u.name as created_by_name")
                    ->get()
                    ->toArray();
            }
        }

        return response()->json(["code" => 200, "data" => $history]);

    }

    private static function get_times($default = '19:00', $interval = '+60 minutes')
    {

        $output = [];

        $current = strtotime('00:00');
        $end = strtotime('23:59');

        while ($current <= $end) {
            $time = date('G', $current);
            $output[$time] = date('h.i A', $current);
            $current = strtotime($interval, $current);
        }

        return $output;
    }

    public function getLastRemark(Request $request)
    {
        $lastRemark = \DB::select("select * from scrap_remarks as sr join ( select max(id) as id from scrap_remarks group by scraper_name) as max_s on sr.id =  max_s.id order by sr.scraper_name asc");

        $download = $request->input('download');
        if(!empty($download)) {
            return Excel::download(new ScrapRemarkExport($lastRemark), 'remarks.csv');
        }

        return response()->json(["code" => 200 , "data" => $lastRemark]);
    }

    public function addNote(Request $request)
    {
        try {
            $this->validate($request, [
                'scraper_name' => 'required',
                'remark' => 'required',
            ]);
            $remark = $request->remark;

            if (!empty($remark)) {
                $note = ScrapRemark::create([
                    'scraper_name' => $request->scraper_name,
                    'remark' => $request->remark,
                    'user_name' => Auth::user()->name
                ]);

                if ($request->hasfile('image')) {
                    $media = MediaUploader::fromSource($request->file('image'))
                                            ->toDirectory('scrap-note')
                                            ->upload();
                    $note->attachMedia($media, config('constants.media_tags'));
                }
            }
            session()->flash('success', 'Note added successfully.');
            return redirect()->back();
        } catch(Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function serverStatistics(Request $request)
    {
        
        
        try {
            $scrappers = Scraper::query();
            $scrap = $scrappers->where('inventory_lifetime','!=',0)->where('server_id','!=','');
            
            if($request->type){

                if($request->type == 'server_id_filter'){
                    if(!empty($request->order)){
                        $scrappers->where('server_id',$request->order);
                    }
                    
                }elseif($request->type == 'filter_by_text'){
                    if(!empty($request->order)){
                        $scrappers->where('scraper_name','LIKE','%'.$request->order.'%');
                    }
                }else{
                   $scrappers->orderBy($request->type,$request->order); 
                }
                
            }

            $scrappers = $scrap->paginate(50);

            $servers = Scraper::select('server_id')->whereNotNull('server_id')->groupBy('server_id')->get();

            if ($request->ajax()) {
            return response()->json([
                    'tbody' => view('scrap.partials.scrap-server-status-data', compact('scrappers','servers'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                    'links' => (string)$scrappers->render(),
                    'count' => $scrappers->total(),
                ], 200);
            } 
            
            return view('scrap.scrap-server-status',compact('scrappers','servers'));
        } catch (\Exception $e) {
            //session()->flash('error', $e->getMessage());
            //return redirect()->back();
        }
    }

    public function serverStatisticsHistory($scrap_name)
    {
        try {
            $scrap_history = Scraper::where(['scraper_name' => $scrap_name])
                                        ->where('created_at','>=',Carbon::now()->subDays(25)->toDateTimeString())
                                        ->get();
            return new JsonResponse(['status' => 1, 'message' => 'Scrapping history', 'data' => $scrap_history, 'name' => $scrap_name]);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function getScreenShot(Request $request)
    {
        $screenshots = \App\ScraperScreenshotHistory::where("scraper_id",$request->id)->latest()->paginate(15); 

        return view("scrap.partials.screenshot-history",compact('screenshots'));
    }


}