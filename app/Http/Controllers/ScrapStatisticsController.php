<?php

namespace App\Http\Controllers;

use App\ScrapStatistics;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;
use App\ScrapRemark;
use App\ScrapHistory;
use Auth;

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
        $keyWord = $request->get("term","");
        $madeby  = $request->get("scraper_madeby",0);
        $scrapeType  = $request->get("scraper_type",0);

        $timeDropDown = self::get_times();           

        // Get active suppliers
        $activeSuppliers = Supplier::where('supplier_status_id', 1);

        if(!empty($keyWord)) {
            $activeSuppliers->where(function($q) use($keyWord){
                $q->where("supplier","like","%{$keyWord}%")->orWhere("scraper_name","like","%{$keyWord}%");
            });
        }

        if($madeby > 0)  {
            $activeSuppliers->where("scraper_madeby",$madeby);
        }

        if($scrapeType > 0) {
            $activeSuppliers->where("scraper_type",$scrapeType);
        }

        $activeSuppliers = $activeSuppliers->orderby('scraper_priority','desc')->get();

        // Get scrape data
        $sql = '
            SELECT
                s.id,
                s.supplier,
                s.inventory_lifetime,
                s.scraper_new_urls,
                s.scraper_existing_urls,
                s.scraper_total_urls,
                s.scraper_start_time,
                s.scraper_logic,
                s.scraper_madeby,
                ls.website,
                ls.ip_address,
                COUNT(ls.id) AS total,
                SUM(IF(ls.validated=0,1,0)) AS failed,
                SUM(IF(ls.validated=1,1,0)) AS validated,
                SUM(IF(ls.validation_result LIKE "%[error]%",1,0)) AS errors,
                SUM(IF(ls.validation_result LIKE "%[warning]%",1,0)) AS warnings,
                MAX(ls.updated_at) AS last_scrape_date,
                IF(MAX(ls.updated_at) < DATE_SUB(NOW(), INTERVAL s.inventory_lifetime DAY),0,1) AS running
            FROM
                suppliers s
            RIGHT JOIN
                log_scraper ls 
            ON  
                s.scraper_name=ls.website
            WHERE
                ls.website != "internal_scraper"
            GROUP BY
                ls.website
            ORDER BY
                s.scraper_priority desc
        ';
        $scrapeData =  DB::select($sql);

        $allScrapperName = [];

        if(!empty($scrapeData)) {
            foreach($scrapeData as $data) {
                if(isset($data->id) && $data->id > 0) {
                   $allScrapperName[$data->id] = $data->website;     
                }                 
            }
        }

        $users = \App\User::all()->pluck("name","id")->toArray();

        //echo '<pre>'; print_r($scrapeData); echo '</pre>';exit;
        // Return view
        return view('scrap.stats', compact('activeSuppliers', 'scrapeData','users','allScrapperName','timeDropDown'));
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
     * @param  \Illuminate\Http\Request $request
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
     * @param  \App\ScrapStatistics $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function show(ScrapStatistics $scrapStatistics)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ScrapStatistics $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function edit(ScrapStatistics $scrapStatistics)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\ScrapStatistics $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ScrapStatistics $scrapStatistics)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ScrapStatistics $scrapStatistics
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

    public function getRemark(Request $request)
    {
        $name   = $request->input( 'name' );

        $remark = ScrapRemark::where('scraper_name', $name)->get();

        return response()->json($remark,200);
    }

    public function addRemark(Request $request)
    {
        $remark = $request->input( 'remark' );
        $name = $request->input( 'id' );
        $created_at = date('Y-m-d H:i:s');
        $update_at = date('Y-m-d H:i:s');
        $remark_entry = ScrapRemark::create([
            'scraper_name' => $name,
            'remark'  => $remark,
            'user_name' => Auth::user()->name
        ]);

        $needToSend = request()->get("need_to_send", false);
        $includeAssignTo = request()->get("inlcude_made_by", false);

        if($needToSend) {
            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi('31629987287', '971502609192', "SCRAPER-REMARK#".$name."\n".$remark);
            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi('919004780634', '971502609192', "SCRAPER-REMARK#".$name."\n".$remark);
            
            if($includeAssignTo) {
                $scraper = \App\Supplier::where("scraper_name",$id)->first();
                if($scraper) {
                    $sendPer = $scraper->scraperMadeBy;
                    if($sendPer) {
                        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($sendPer->phone, $sendPer->whatsapp_number, "SCRAPER-REMARK#".$name."\n".$remark);
                    }
                }
            }
        }


        return response()->json(['remark' => $remark ],200);
    }

    public function updateField(Request $request)
    {

        $fieldName  = request()->get("field");
        $fieldValue = request()->get("field_value");
        $search     = request()->get("search");

        $suplier = \App\Supplier::where("id", $search)->first();
        if($suplier) {
            $oldValue  = $suplier->{$fieldName}; 

            if($fieldName == "scraper_madeby") {
                $oldValue  = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : "";
            }

            if($fieldName == "scraper_parent_id") {
                $oldValue  = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : "";
            }

            $suplier->{$fieldName} = $fieldValue;
            $suplier->save();

            $suplier = \App\Supplier::where("id", $search)->first();

            $newValue = $fieldValue;

            if($fieldName == "scraper_madeby") {
                $newValue  = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : "";
            }

            if($fieldName == "scraper_parent_id") {
                $newValue  = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : "";
            }

            ScrapHistory::create([
              "operation" => "Update",
              "model" =>  \App\Supplier::class, 
              "model_id" => $suplier->id, 
              "text" =>  "{$fieldName} updated old value was $oldValue and new value is $newValue",
              "created_by" => \Auth::id()  
            ]);

        }

        return response()->json(["code" => 200]);

    }

    public function updatePriority(Request $request)
    {
        $ids = $request->get("ids");
        $prio = count($ids);

        if(!empty($ids)) {
            foreach($ids as $k => $id) {
                if(isset($id["id"])) {
                    $scrap = \App\Supplier::where("id",$id["id"])->first();
                    if($scrap) {
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
        $field = $request->get("field","supplier");
        $value = $request->get("search","0");

        $history = [];

        if($value > 0) {
            if($field == "supplier") {
                $history = ScrapHistory::where("model",\App\Supplier::class)->join("users as u","u.id","scrap_histories.created_by")->where("model_id",$value)
                ->orderBy("created_at","DESC")
                ->select("scrap_histories.*","u.name as created_by_name")
                ->get()
                ->toArray();
            }
        }

        return response()->json(["code" => 200 , "data" => $history]);

    }

    private static function get_times( $default = '19:00', $interval = '+30 minutes' ) {

        $output = [];

        $current = strtotime( '00:00' );
        $end = strtotime( '23:59' );

        while( $current <= $end ) {
            $time = date( 'H:i', $current );
            $output[$time] = date( 'h.i A', $current );
            $current = strtotime( $interval, $current );
         }

        return $output;
    }

}