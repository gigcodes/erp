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

        // Get active suppliers
        $activeSuppliers = Supplier::where('supplier_status_id', 1)->orderby('supplier')->get();

        // Get scrape data
        $sql = '
            SELECT
                s.supplier,
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
                ls.updated_at > "' . $startDate . '" AND
                ls.updated_at < "' . $endDate . '" AND
                ls.website != "internal_scraper"
            GROUP BY
                ls.website
            ORDER BY
                s.supplier
        ';
        $scrapeData =  DB::select($sql);

        // Return view
        return view('scrap.stats', compact('activeSuppliers', 'scrapeData'));
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


        return response()->json(['remark' => $remark ],200);
    }
}