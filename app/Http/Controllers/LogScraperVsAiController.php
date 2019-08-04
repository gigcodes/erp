<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\LogScraperVsAi;

class logScraperVsAiController extends Controller
{
    public function index( Request $request )
    {
        // Get results
        $results = LogScraperVsAi::where( 'product_id', $request->id )->orderBy( 'created_at', 'desc' )->get();

        // Return view
        return view( 'log-scraper-vs-ai.index', compact('results') );
    }
}
