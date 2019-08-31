<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Analytics;
use App\AnalyticsSummary;
use App\AnalyticsCustomerBehaviour;
use Spatie\Analytics\Period;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use DB;
use function Opis\Closure\unserialize;

class AnalyticsController extends Controller
{
   public function showData(Request $request){
      $visitors = ['New Visitor' => 'New Visitor', 'Returning Visitor' => 'Returning Visitor'];
      if (!empty($_GET['location'])) {
         $location = $_GET['location'];
         $data = Analytics::where('country', 'like', '%' . $location . '%')->get()->toArray();
     } elseif(!empty($_GET['user'])) {
         $data = Analytics::where('user_type', $request['user'])->get()->toArray();
     } elseif(!empty($_GET['device_os'])) {
         $data = Analytics::where('operatingSystem', 'like', '%' . $request['device_os'] . '%')->
            orWhere('device_info', 'like', '%' . $request['device_os'] . '%')
            ->get()->toArray();
      } else {
         include(app_path() . '/Functions/Analytics.php');
	 }
      // Analytics::get()->toArray();
            // foreach ($data as $key => $new_item) {
            // DB::table('analytics')->insert(
            //       [
            //             "operatingSystem" => $new_item['operatingSystem'], 
            //             "user_type" => $new_item['user_type'],
            //             "time" => $new_item['time'],
            //             "page_path" => $new_item['page_path'],
            //             "country" => $new_item['country'],
            //             "city" => $new_item['city'],
            //             "social_network" => $new_item['social_network'],
            //             "date" => $new_item['date'],
            //             "device_info" => $new_item['device_info'],
            //             "sessions" => $new_item['sessions'],
            //             "pageviews" => $new_item['pageviews'],
            //             "bounceRate" => $new_item['bounceRate'],
            //             "avgSessionDuration" => $new_item['avgSessionDuration'],
            //             "timeOnPage" => $new_item['timeOnPage'],
                        
            //       ]
            //    );
            // }
      return View('analytics.index', compact('data', 'visitors'));
   }
   
	public function analyticsDataSummary(Request $request)
	{
		$brands = [
			'balenciaga' => 'Balenciaga', 
			'gucci' => 'Gucci',
			'jimmy-choo' => 'Jimmy Choo',
			'saint-laurent' => 'Saint Laurent',
			'givenchy' => 'Givenchy',
			'christian-dior' => 'Christian Dior',
			'prada' => 'Prada',
			'dolce-gabbana' => 'Dolce Gabbana',
			'fendi' => 'Fendi',
			'bottega-veneta' => 'Bottega Vneta',
			'burberry' => 'Burberry',
		];
		$genders = [
			'mens' => 'Mens', 
			'women' => 'Womens',
		];
		if (!empty($_GET['location'])) {
			$location = $_GET['location'];
			$data = AnalyticsSummary::where('country', 'like', '%' . $location . '%')->get()->toArray();
		} elseif(!empty($_GET['brand'])) {
			$data = AnalyticsSummary::where('brand_name', $request['brand'])->get()->toArray();
		} elseif(!empty($_GET['gender'])) {
			$data = AnalyticsSummary::where('gender', $request['gender'])->get()->toArray();
		 } else {
			include(app_path() . '/Functions/Analytics.php');
		}
		return View('analytics.summary', compact('data', 'brands', 'genders'));
	}
	/**
	 * Customer Behaviour By Page
	 */
	public function customerBehaviourByPage(Request $request)
	{
		$pages = AnalyticsCustomerBehaviour::select('ID', 'pages')->pluck('pages', 'ID')->toArray();
		if(!empty($request['page'])) {
			$data = AnalyticsCustomerBehaviour::where('pages', $request['page'])->get()->toArray();
		} else {
			include(app_path() . '/Functions/Analytics.php');
		}
		return View('analytics.customer-behaviour', compact('data', 'pages'));
	}
}
