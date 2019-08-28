<?php

namespace App\Http\Controllers;

use App\ScrapStatistics;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScrapStatisticsController extends Controller
{

    //List of supplier with their brand count
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
     *
     * generate the scrap statistics
     */
    public function index(Request $request)
    {
        $date = $request->get('date');


//        $inactiveScrapping = DB::table('scrap_statistics')
//            ->selectRaw('supplier, MAX(created_at) as created_at, TIMESTAMPDIFF(HOUR, created_at, NOW()) as last_scraped`')
//            ->groupBy('supplier')
//            ->get();
//
//        dd($inactiveScrapping);

//         get stats for new and existing ones
        $scrapedExistingProducts = DB::table('scrap_statistics')->selectRaw('COUNT(DISTINCT description) as total, supplier')->where('type', 'EXISTING_SCRAP_PRODUCT');
        $scrapedNewProducts = DB::table('scrap_statistics')->selectRaw('COUNT(DISTINCT description) as total, supplier')->where('type', 'NEW_SCRAP_PRODUCT');



        $suppliers = ScrapStatistics::distinct()->get(['supplier']);

        $supplierList = $this->suppliers;
        $progress = [];

        $totalBrands = 0;
        $doneBrands = 0;

//        loop through the supplier list and then add count & stat by date with created at and ended at
        foreach ($supplierList as $key=>$item) {
            $count = ScrapStatistics::where('supplier', $key);
            $stat = ScrapStatistics::selectRaw('MIN(created_at) as started_at, MAX(created_at) as ended_at')->where('supplier', $key);
            if (strlen($date) === 10) {
                $count = $count->whereRaw('DATE(created_at) = "'. $date . '"');
                $stat = $stat->whereRaw('DATE(created_at) = "'. $date . '"');
            } else {
                $count = $count->whereRaw('DATE(created_at) = "'. date('Y-m-d'.'"'));
                $stat = $stat->whereRaw('DATE(created_at) = "'. date('Y-m-d'.'"'));
            }

            $count = $count->distinct()->pluck('brand')->toArray();

            $doneBrands+=count($count);
            $totalBrands+=$item;

            $blist = implode(', ', $count);

            $progress[$key] = [count($count), round((count($count)/$item)*100),$item, $blist, $stat->first()];
        }


        if (strlen($date) === 10) {
            $scrapedNewProducts = $scrapedNewProducts->whereRaw('DATE(created_at) = "'. $date.'"');
            $scrapedExistingProducts = $scrapedExistingProducts->whereRaw('DATE(created_at) = "'. $date.'"');
        }

        $totalPercent = $doneBrands/$totalBrands;
        //calculate the percent for particular supplier..
        $totalProgress = round($totalPercent*100);

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

        return view('scrap.stats', compact('scrapedExistingProducts', 'scrapedNewProducts', 'request', 'progressStats', 'progress', 'totalProgress'));
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
