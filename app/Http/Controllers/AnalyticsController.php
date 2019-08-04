<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Analytics;
use Spatie\Analytics\Period;
use Config;

class AnalyticsController extends Controller
{
    /**
     * Return google analytics Data
     */
    public function showData(){
         $top_referers = Analytics::fetchTopReferrers(Period::days(4), 20);
         $user_types = Analytics::fetchUserTypes(Period::days(7));
        // $most_visited_pages = Analytics::fetchMostVisitedPages(Period::days(1), 5);
         $total_visitors_page_views = Analytics::fetchTotalVisitorsAndPageViews(Period::days(7));
        // dd($total_visitors_page_views);
         return View('analytics.index', compact('top_referers', 'user_types', 'total_visitors_page_views'));
    }
}
