<?php

namespace App\Http\Controllers;

use App\ScrapStatistics;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;
use App\ScrapRemark;
use Auth;

class ScrapStatisticsController extends Controller
{


    private $suppliers = [
        'angelominetti' => 23,
        'Wiseboutique' => 18,
        'G&B' => 25,
        'DoubleF' => 15,
        'cuccuini' => 27,
        'Tory' => 1,
        'lidiashopping' => 14,
        'Spinnaker' => 19,
        'alducadaosta' => 16,
        'biffi' => 11,
        'brunarosso' => 22,
        'conceptstore' => 9,
        'deliberti' => 14,
        'griffo210' => 13,
        'linoricci' => 8,
        'les-market' => 3,
        'leam' => 17,
        'laferramenta' => 9,
        'montiboutique' => 21,
        'mimmaninnishop' => 5,
        'nugnes1920' => 21,
        'railso' => 9,
        'savannahs' => 6,
        'stilmoda' => 6,
        'tessabit' => 17,
        'tizianafausti' => 24,
        'vinicio' => 24,
        'coltorti' => 16,
        'italiani' => 9,
        'giglio' => 28,
        'mariastore' => 7,
        'Divo' => 20,
        'aldogibiralo' => 8
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Set dates
        $startDate = date('Y-m-d H:i:s', time() - (2 * 86400));
        $endDate = date('Y-m-d H:i:s');

        // Get scrape data
        $sql = '
            SELECT
                id,
                website,
                COUNT(id) AS total,
                SUM(IF(validated=0,1,0)) AS failed,
                SUM(IF(validated=1,1,0)) AS validated,
                SUM(IF(validation_result LIKE "%[error]%",1,0)) AS errors,
                SUM(IF(validation_result LIKE "%[warning]%",1,0)) AS warnings,
                MAX(created_at) AS last_scrape_date,
                IF(MAX(created_at) < DATE_SUB(NOW(), INTERVAL 2 DAY),0,1) AS running
            FROM
                log_scraper
            WHERE
                created_at > "' . $startDate . '" AND
                created_at < "' . $endDate . '"
            GROUP BY
                website
            ORDER BY
                website
        ';
        $scrapeData = DB::select($sql);

        // Return view
        return view('scrap.stats', compact('scrapeData'));
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
        $id   = $request->input( 'id' );

       $remark = ScrapRemark::where('scrap_id', $id)->get();
        
       return response()->json($remark,200);
    }

    public function addRemark(Request $request)
    {
        $remark = $request->input( 'remark' );
        $id = $request->input( 'id' );
        $created_at = date('Y-m-d H:i:s');
        $update_at = date('Y-m-d H:i:s');
        if($request->module_type=="scrap"){
          $remark_entry = ScrapRemark::create([
            'scrap_id' => $id,
            'remark'  => $remark,
            'module_type' => $request->module_type,
            'scraper_name' => $request->user_name ? $request->user_name : Auth::user()->name
          ]);
        }

      return response()->json(['remark' => $remark ],200);
    }
}