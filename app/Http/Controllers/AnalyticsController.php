<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Analytics;
use App\AnalyticsSummary;
use Spatie\Analytics\Period;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use DB;
use function Opis\Closure\unserialize;
use App\LinksToPost;
use App\ArticleCategory;
use Response;

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

    public function displayLinksToPostData()
   {
      $data = LinksToPost::orderBy('id','desc')->paginate(15); 
      $category = ArticleCategory::all();
     return View('analytics.linkstopost', compact('data','category'));
   }

   public function updateCategoryPost(Request $request)
   {

      $post = LinksToPost::findorfail($request->link_id);
      $post->category_id = $request->id;
      $post->save();

      return Response::json(array(
      'success' => true,
      'message'   => 'Post Updated'
      ));

   }

   public function addArticleCategory(Request $request)
   {
     $category =  new ArticleCategory;
     $category->name = $request->name;
     $category->save();

     return redirect()->back()->with(['message','Category Saved','success','true']); 
     
   }
}
