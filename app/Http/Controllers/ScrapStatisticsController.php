<?php

namespace App\Http\Controllers;

use App\ScrapStatistics;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScrapStatisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date = $request->get('date');

        $scrapedExistingProducts = DB::table('scrap_statistics')->selectRaw('COUNT(*) as total, supplier')->where('type', 'EXISTING_SCRAP_PRODUCT');
        $scrapedNewProducts = DB::table('scrap_statistics')->selectRaw('COUNT(*) as total, supplier')->where('type', 'NEW_SCRAP_PRODUCT');



        $suppliers = ScrapStatistics::distinct()->get(['supplier']);


        if (strlen($date) === 10) {
            $scrapedNewProducts = $scrapedNewProducts->whereRaw('DATE(created_at) = "'. $date.'"');
            $scrapedExistingProducts = $scrapedExistingProducts->whereRaw('DATE(created_at) = "'. $date.'"');
        }

        $scrapedNewProducts = $scrapedNewProducts->groupBy(['supplier'])->get();
        $scrapedExistingProducts = $scrapedExistingProducts->groupBy(['supplier'])->get();


        $progressStats = [];

        foreach ($suppliers as $supplier) {
            $data = DB::table('scrap_statistics')->selectRaw('COUNT(*) as total, brand')->where('supplier', $supplier->supplier)->groupBy('brand');
            if (strlen($date) === 10) {
                $data = $data->whereRaw('DATE(created_at) = "'. $date . '"');
            }
            $progressStats[$supplier->supplier] = $data->get();
        }

        return view('scrap.stats', compact('scrapedExistingProducts', 'scrapedNewProducts', 'request', 'progressStats'));
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
     * @param  \Illuminate\Http\Request  $request
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
     * @param  \App\ScrapStatistics  $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function show(ScrapStatistics $scrapStatistics)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ScrapStatistics  $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function edit(ScrapStatistics $scrapStatistics)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ScrapStatistics  $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ScrapStatistics $scrapStatistics)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ScrapStatistics  $scrapStatistics
     * @return \Illuminate\Http\Response
     */
    public function destroy(ScrapStatistics $scrapStatistics)
    {
        //
    }
}
