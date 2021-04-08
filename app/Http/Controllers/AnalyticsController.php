<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Analytics;
use App\AnalyticsSummary;
use App\AnalyticsCustomerBehaviour;
use App\StoreWebsiteAnalytic;
use App\GoogleAnalytics;
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
    public function showData(Request $request)
    {
        $visitors = ['New Visitor' => 'New Visitor', 'Returning Visitor' => 'Returning Visitor'];
        $dimensionsList = GoogleAnalytics::groupBy('dimensions')->pluck('dimensions');
        $data = GoogleAnalytics::query();
        $data->select('google_analytics.*','store_website_analytics.website');
        $data->leftJoin('store_website_analytics','google_analytics.website_analytics_id','=','store_website_analytics.id');

        if ( request('dimensionsList') ) {
            $data = $data->where('dimensions',request('dimensionsList'));
        } 

        // if (!empty($_GET[ 'user' ])) {
        //     $data = $data->where('user_type', $request[ 'user' ]);
        // } 

        // if (!empty($_GET[ 'device_os' ])) {
        //     $data = $data->where(function($q) use($request) {
        //         $q->where('operatingSystem', 'like', '%' . $request[ 'device_os' ] . '%')->orWhere('device_info', 'like', '%' . $request[ 'device_os' ] . '%');
        //     });
        // }

        // if (!empty($_GET[ 'start_date' ]) && !empty($_GET[ 'end_date' ])) {
        //     $data = $data->where('date', '>=', $_GET[ 'start_date' ])->where('date', '<=', $_GET[ 'end_date' ]);
        // }

        $data = $data->orderBy('created_at','desc')->paginate(30);
        
        return View('analytics.index-new', compact('data', 'visitors', 'dimensionsList'));
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
        if (!empty($_GET[ 'location' ])) {
            $location = $_GET[ 'location' ];
            $data = AnalyticsSummary::where('country', 'like', '%' . $location . '%')->get()->toArray();
        } elseif (!empty($_GET[ 'brand' ])) {
            $data = AnalyticsSummary::where('brand_name', $request[ 'brand' ])->get()->toArray();
        } elseif (!empty($_GET[ 'gender' ])) {
            $data = AnalyticsSummary::where('gender', $request[ 'gender' ])->get()->toArray();
        } else {
            include(app_path() . '/Functions/Analytics.php');
        }
        return View('analytics.summary', compact('data', 'brands', 'genders'));
    }

    public function displayLinksToPostData()
    {
        $data = LinksToPost::orderBy('id', 'desc')->paginate(15);
        $category = ArticleCategory::all();
        return View('analytics.linkstopost', compact('data', 'category'));
    }

    public function updateCategoryPost(Request $request)
    {

        $post = LinksToPost::findorfail($request->link_id);
        $post->category_id = $request->id;
        $post->save();

        return Response::json(array(
            'success' => true,
            'message' => 'Post Updated'
        ));

    }

    public function addArticleCategory(Request $request)
    {
        $category = new ArticleCategory;
        $category->name = $request->name;
        $category->save();

        return redirect()->back()->with(['message', 'Category Saved', 'success', 'true']);

    }

    /**
     * Customer Behaviour By Page
     */
    public function customerBehaviourByPage(Request $request)
    {
        $pages = AnalyticsCustomerBehaviour::select('ID', 'pages')->pluck('pages', 'ID')->toArray();
        if (!empty($request[ 'page' ])) {
            $data = AnalyticsCustomerBehaviour::where('pages', $request[ 'page' ])->get()->toArray();
        } else { 
            include(app_path() . '/Functions/Analytics.php');
        }
        return View('analytics.customer-behaviour', compact('data', 'pages'));
    }

    public function cronShowData(){

        \Log::channel('daily')->info("Google Analytics Started running ...");
        $analyticsDataArr = [];

        include(app_path() . '/Functions/Analytics.php');
        $data = StoreWebsiteAnalytic::all()->toArray();
        foreach ($data as $value) {

            $ERPlogArray = [
                'model_id' => $value['id'],
                'url'      => 'https://www.googleapis.com/auth/analytics.readonly',
                'model'    => StoreWebsiteAnalytic::class,
                'type'     => 'success',
                'request'  => $value,
            ];

            try {
                
                $response   = getReport($analytics, $value);
                $resultData = printResults($response);

                if(!empty($resultData)) {
                    foreach ($resultData as $new_item) {
                         $analyticsDataArr = [
                                "operatingSystem" => $new_item['operatingSystem'],
                                "user_type" => $new_item['user_type'],
                                "time" => $new_item['time'],
                                "page_path" => $value['website'].$new_item['page_path'],
                                "country" => $new_item['country'],
                                "city" => $new_item['city'],
                                "social_network" => $new_item['social_network'],
                                "date" => $new_item['date'],
                                "device_info" => $new_item['device_info'],
                                "sessions" => $new_item['sessions'],
                                "pageviews" => $new_item['pageviews'],
                                "bounceRate" => $new_item['bounceRate'],
                                "avgSessionDuration" => $new_item['avgSessionDuration'],
                                "timeOnPage" => $new_item['timeOnPage'],

                          ];
                         Analytics::insert($analyticsDataArr);
                    }
                }

                $ERPlogArray['request'] = $value;
                $ERPlogArray['response'] = $resultData;

            }catch(\Exception  $e) {
                $ERPlogArray['type']    = 'error';
                $ERPlogArray['response'] = $e->getMessage();
            }
            storeERPLog($ERPlogArray);
        }
    }

    public function cronGetUserShowData(){

        \Log::channel('daily')->info("Google Analytics User Started running ...");
        $analyticsDataArr = [];

        include(app_path() . '/Functions/Analytics_user.php');
        $data = StoreWebsiteAnalytic::all()->toArray();
        // $data = StoreWebsiteAnalytic::limit(1)->get()->toArray();
        // dd( $data);
        foreach ($data as $value) {

            $ERPlogArray = [
                'model_id' => $value['id'],
                'url'      => 'https://www.googleapis.com/auth/analytics.readonly',
                'model'    => StoreWebsiteAnalytic::class,
                'type'     => 'success',
                'request'  => $value,
            ];

            try {
                
                // $response   = getReport($analytics, $value);
                $response   = getReportRequest($analytics, $value);
                extract($response);
                $dimensionArr = ['ga:operatingSystem','ga:browser','ga:country','ga:pagePath','ga:userType'];
                foreach ($dimensionArr as $key => $dimensionValue) {
                    $resultData = getDimensionWiseData( $analyticsObj ,$requestObj, $dimensionValue);
                    $resultData = printResults( $resultData , $value['id']);
                    // dd( $resultData );
                    if( $resultData ){
                        GoogleAnalytics::insert($resultData);
                    }
                }

                return redirect()->back()->with('success','success');
            }catch(\Exception  $e) {
                return redirect()->back()->with('error',$e->getMessage());
            }
            return redirect()->back();
        }
    }
}
