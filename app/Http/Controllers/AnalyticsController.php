<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Analytics;
use Spatie\Analytics\Period;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use DB;
use function Opis\Closure\unserialize;
use Illuminate\Pagination\LengthAwarePaginator;

class AnalyticsController extends Controller
{
   public function showData(Request $request){
      include(app_path() . '/Functions/Analytics.php');
      // if(!empty($request['location'])){
      //    foreach($data as $key => $new_data){
      //       $collection = collect($new_data);
      //       $filtered = $collection->filter(function ($new_data, $key) {
      //           $new_data === $request['location'];
      //      });
      //    }
      // }
      // $visitors = Analytics::fetchVisitorsAndPageViews(Period::days(1));
      // DB::table('google_analytics')->where('key', '=', 'visitors')->delete();
      // DB::table('google_analytics')->insert(
      //    [
      //       'key' => 'visitors',
      //       'value' => serialize($visitors)
      //    ]
      // );
      // $top_referers = Analytics::fetchTopReferrers(Period::days(4), 20);
      // $user_types = Analytics::fetchUserTypes(Period::days(4));
      // $total_visitors_page_views = Analytics::fetchTotalVisitorsAndPageViews(Period::days(4));
      // DB::table('google_analytics')->where('key', '=', 'top_referers')->delete();
      // DB::table('google_analytics')->insert(
      //    [
      //       'key' => 'top_referers',
      //       'value' => serialize($top_referers)
      //    ]
      // );
      // DB::table('google_analytics')->where('key', '=', 'user_types')->delete();
      // DB::table('google_analytics')->insert(
      //    [
      //       'key' => 'user_types',
      //       'value' => serialize($user_types)
      //    ]
      // );
      // DB::table('google_analytics')->where('key', '=', 'total_visitors_page_views')->delete();
      // DB::table('google_analytics')->insert(
      //    [
      //       'key' => 'total_visitors_page_views',
      //       'value' => serialize($total_visitors_page_views)
      //    ]
      // );
      // $top_referers_ser = DB::table('google_analytics')->select('value')->where('key', 'top_referers')->pluck('value')->toArray();
      // $user_types_ser = DB::table('google_analytics')->select('value')->where('key', 'user_types')->pluck('value')->toArray();
      // $total_visitors_page_views_ser = DB::table('google_analytics')->select('value')->where('key', 'total_visitors_page_views')->pluck('value')->toArray();
      // $visitors_ser = DB::table('google_analytics')->select('value')->where('key', 'visitors')->pluck('value')->toArray();
      // $total_views = unserialize($total_visitors_page_views_ser[0]);
      
      // if (!empty($request['date'])) {
      //       foreach($total_views as $views) {
      //          $dates[] = Carbon::parse($views['date'])->toDateString();
      //       }
      //       if (in_array(Carbon::parse($request['date'])->toDateTimeString(), $dates)) {
      //          $total_views = unserialize($total_visitors_page_views_ser[0]);
      //       }
      //       $total_views = unserialize($total_visitors_page_views_ser[0]);
      // }
      // return View('analytics.index', compact('top_referers_ser', 'user_types_ser', 'total_views', 'visitors_ser', 'results'));
      return View('analytics.index', compact('data'));
   }
   


   
   //   * Return google analytics Data
   //   */
   //  public function showData(Request $request){
   //       $visitors = Analytics::fetchVisitorsAndPageViews(Period::days(1));
   //       DB::table('google_analytics')->where('key', '=', 'visitors')->delete();
   //       DB::table('google_analytics')->insert(
   //          [
   //           'key' => 'visitors',
   //           'value' => serialize($visitors)
   //          ]
   //       );
   //       $top_referers = Analytics::fetchTopReferrers(Period::days(4), 20);
   //       $user_types = Analytics::fetchUserTypes(Period::days(4));
   //       $total_visitors_page_views = Analytics::fetchTotalVisitorsAndPageViews(Period::days(4));
   //       DB::table('google_analytics')->where('key', '=', 'top_referers')->delete();
   //       DB::table('google_analytics')->insert(
   //          [
   //           'key' => 'top_referers',
   //           'value' => serialize($top_referers)
   //          ]
   //       );
   //       DB::table('google_analytics')->where('key', '=', 'user_types')->delete();
   //       DB::table('google_analytics')->insert(
   //          [
   //           'key' => 'user_types',
   //           'value' => serialize($user_types)
   //          ]
   //       );
   //       DB::table('google_analytics')->where('key', '=', 'total_visitors_page_views')->delete();
   //       DB::table('google_analytics')->insert(
   //          [
   //           'key' => 'total_visitors_page_views',
   //           'value' => serialize($total_visitors_page_views)
   //          ]
   //       );
   //       $top_referers_ser = DB::table('google_analytics')->select('value')->where('key', 'top_referers')->pluck('value')->toArray();
   //       $user_types_ser = DB::table('google_analytics')->select('value')->where('key', 'user_types')->pluck('value')->toArray();
   //       $total_visitors_page_views_ser = DB::table('google_analytics')->select('value')->where('key', 'total_visitors_page_views')->pluck('value')->toArray();
   //       $visitors_ser = DB::table('google_analytics')->select('value')->where('key', 'visitors')->pluck('value')->toArray();
   //       $total_views = unserialize($total_visitors_page_views_ser[0]);
         
   //       if (!empty($request['date'])) {
   //           foreach($total_views as $views) {
   //               $dates[] = Carbon::parse($views['date'])->toDateString();
   //           }
   //           if (in_array(Carbon::parse($request['date'])->toDateTimeString(), $dates)) {
   //              $total_views = unserialize($total_visitors_page_views_ser[0]);
   //           }
   //           $total_views = unserialize($total_visitors_page_views_ser[0]);
   //       }
   //       return View('analytics.index', compact('top_referers_ser', 'user_types_ser', 'total_views', 'visitors_ser'));
   //  }

    /**
     * Custom paginator
     *
     * @param mixed $request        $request        attributes
     * @param array $values         $values         array values to be paginated
     * @param mixed $posts_per_page $posts_per_page posts to show per page
     *
     * @return $items
     */
    public static function customPaginator($request, $values = array(), $posts_per_page = '10')
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $itemCollection = collect($values);
        $perPage = intval($posts_per_page);
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        $items = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);
        $items->setPath($request->url());
        return $items;
    }
}
