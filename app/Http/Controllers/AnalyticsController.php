<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Analytics;
use Spatie\Analytics\Period;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use DB;
use function Opis\Closure\unserialize;

class AnalyticsController extends Controller
{
    /**
     * Return google analytics Data
     */
    public function showData(Request $request){
         $top_referers = Analytics::fetchTopReferrers(Period::days(4), 20);
         $user_types = Analytics::fetchUserTypes(Period::days(7));
         $total_visitors_page_views = Analytics::fetchTotalVisitorsAndPageViews(Period::days(7));
         DB::table('google_analytics')->where('key', '=', 'top_referers')->delete();
         DB::table('google_analytics')->insert(
            [
             'key' => 'top_referers',
             'value' => serialize($top_referers)
            ]
         );
         DB::table('google_analytics')->where('key', '=', 'user_types')->delete();
         DB::table('google_analytics')->insert(
            [
             'key' => 'user_types',
             'value' => serialize($user_types)
            ]
         );
         DB::table('google_analytics')->where('key', '=', 'total_visitors_page_views')->delete();
         DB::table('google_analytics')->insert(
            [
             'key' => 'total_visitors_page_views',
             'value' => serialize($total_visitors_page_views)
            ]
         );
         $top_referers_ser = DB::table('google_analytics')->select('value')->where('key', 'top_referers')->pluck('value')->toArray();
         $user_types_ser = DB::table('google_analytics')->select('value')->where('key', 'user_types')->pluck('value')->toArray();
         $total_visitors_page_views_ser = DB::table('google_analytics')->select('value')->where('key', 'total_visitors_page_views')->pluck('value')->toArray();
         $total_views = unserialize($total_visitors_page_views_ser[0]);
         
         if (!empty($request['date'])) {
             foreach($total_views as $views) {
                 $dates[] = Carbon::parse($views['date'])->toDateString();
             }
             if (in_array(Carbon::parse($request['date'])->toDateTimeString(), $dates)) {
                $total_views = unserialize($total_visitors_page_views_ser[0]);
             }
             $total_views = unserialize($total_visitors_page_views_ser[0]);
         }
         return View('analytics.index', compact('top_referers_ser', 'user_types_ser', 'total_views'));
    }
}
