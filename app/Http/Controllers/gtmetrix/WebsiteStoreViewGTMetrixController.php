<?php

namespace App\Http\Controllers\gtmetrix;

use App\Http\Controllers\Controller;
use App\Setting;
use App\StoreViewsGTMetrix;
use App\StoreGTMetrixAccount;
use Entrecore\GTMetrixClient\GTMetrixClient;
use Entrecore\GTMetrixClient\GTMetrixTest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebsiteStoreViewGTMetrixController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = StoreViewsGTMetrix::select(\DB::raw('store_views_gt_metrix.*'));

        if (request('date')) {
            $query->whereDate('created_at', request('date'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $columns = ['error', 'report_url', 'report_url', 'html_load_time', 'html_bytes', 'page_load_time', 'page_bytes', 'page_elements', 'pagespeed_score', 'yslow_score'];
        if (request('keyword')) {
            foreach ($columns as $column) {
                $query->orWhere('store_views_gt_metrix.' . $column, 'LIKE', '%' . request('keyword') . '%');
            }
        }

        $list = $query->from(\DB::raw('(SELECT MAX( id) as id, status, store_view_id, website_url, html_load_time FROM store_views_gt_metrix  GROUP BY store_views_gt_metrix.website_url ) as t'))
            ->leftJoin('store_views_gt_metrix', 't.id', '=', 'store_views_gt_metrix.id')->orderBy('id', 'desc')
            ->paginate(25);

        //$list = $query->orderBy('id','desc')->paginate(25);

        $cronStatus = Setting::where('name', "gtmetrixCronStatus")->get()->first();
        $cronTime   = Setting::where('name', "gtmetrixCronType")->get()->first();
        return view('gtmetrix.index', compact('list', 'cronStatus', 'cronTime'));
    }

    public function saveGTmetrixCronStatus($status = null)
    {

        if (empty($status)) {
            return redirect()->back()->with('error', 'Error');
        }

        $statusExit = Setting::where('name', "gtmetrixCronStatus")->get()->first();
        if (empty($statusExit)) {
            $status_date['name'] = "gtmetrixCronStatus";
            $status_date['type'] = "string";
            $status_date['val']  = $status;
            Setting::create($status_date);
        } else {
            $statusExit->val = $status;
            $statusExit->save();
        }
        return redirect()->back()->with('success', 'Success');

    }

    public function saveGTmetrixCronType(Request $request)
    {

        $request->validate([
            'type' => 'required',
        ]);

        $type = Setting::where('name', "gtmetrixCronType")->get()->first();

        if (empty($type)) {

            $type['name'] = "gtmetrixCronType";
            $type['type'] = "string";
            $type['val']  = $request->type;
            Setting::create($type);

        } else {

            $type->val = $request->type;
            $type->save();

        }
        return redirect()->back()->with('success', 'Success');
    }

    /**
     * Show the store view history.
     *
     * @return \Illuminate\Http\Response
     */
    public function history($id)
    {
        $title = 'History';
        if ($id) {
            $history = StoreViewsGTMetrix::where('store_view_id', $id)->orderBy("created_at", "desc")->paginate(25);

            return view('gtmetrix.history', compact('history','title'));
            //return response()->json(["code" => 200, "data" => $history]);
        }
    }

    public function runErpEvent(Request $request)
    {
        $gtmatrixAccount = StoreGTMetrixAccount::select(\DB::raw('store_gt_metrix_account.*'));
        $gtmatrix = StoreViewsGTMetrix::where('id', $request->id)->first();

        if ($gtmatrix) {
            $gt_metrix['store_view_id'] = $gtmatrix->store_view_id;
            $gt_metrix['website_url'] = $gtmatrix->website_url;
            $new_id = StoreViewsGTMetrix::create($gt_metrix)->id;
            $gtmetrix = StoreViewsGTMetrix::where('id', $new_id)->first();
            $gtmatrix = StoreViewsGTMetrix::where('store_view_id', $gt_metrix['store_view_id'])->where('website_url',$gt_metrix['website_url'])->first();
            try {

                if(!empty($gtmatrix->account_id)){
                    $gtmatrixAccountData = StoreGTMetrixAccount::where('account_id', $gtmatrix->account_id)->first();

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://gtmetrix.com/api/2.0/status',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_USERPWD => $gtmatrixAccountData->account_id . ":" . '',
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                   // $stdClass = json_decode(json_encode($response));
                    $data = json_decode($response);
                   $credits = $data->data->attributes->api_credits;
                   // print_r($data->data->attributes->api_credits);
                    if($credits!= 0){
                        $client = new GTMetrixClient();
                        $client->setUsername($gtmatrixAccountData->email);
                        $client->setAPIKey($gtmatrixAccountData->account_id);
                        $client->getLocations();
                        $client->getBrowsers();  
                        $test   = $client->startTest($gtmetrix->website_url);
                        $update = [
                            'test_id' => $test->getId(),
                            'status'  => 'queued',
                        ];
                        $gtmetrix->update($update);
                        
                    }
                }
                else{
                    $AccountData = $gtmatrixAccount->orderBy('id','desc')->get();

                    foreach ($AccountData as $key => $value) {
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://gtmetrix.com/api/2.0/status',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_USERPWD => $value['account_id'] . ":" . '',
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
                        // decode the response 
                        $data = json_decode($response);
                        $credits = $data->data->attributes->api_credits;
                        if($credits!= 0){
                            $client = new GTMetrixClient();
                            $client->setUsername($value['email']);
                            $client->setAPIKey($value['account_id']);
                            $client->getLocations();
                            $client->getBrowsers();  
                            $test   = $client->startTest($gtmetrix->website_url);
                            $update = [
                                'test_id' => $test->getId(),
                                'status'  => 'queued',
                                'account_id'  => $value['account_id'],
                            ];
                            $gtmetrix->update($update);
                            break;
                            
                        }
                    }

                }
                
                return response()->json(["code" => 200, "message" => "Request has been send for queue successfully"]);
                // $client = new GTMetrixClient();
                // $client->setUsername(env('GTMETRIX_USERNAME'));
                // $client->setAPIKey(env('GTMETRIX_API_KEY'));
                // $client->getLocations();
                // $client->getBrowsers();
                // $test   = $client->startTest($gtmetrix->website_url);

                // $update = [
                //     'test_id' => $test->getId(),
                //     'status'  => 'queued',
                //     //'account_id'  => 'queued',
                // ];
                // $gtmetrix->update($update);

            } 
            catch (\Exception $e) {
                return response()->json(["code" => 500, "message" => "Error :" . $e->getMessage()]);
            }
        }
    }
    function remove_http($url) {
       $disallowed = array('http://', 'https://');
       foreach($disallowed as $d) {
          if(strpos($url, $d) === 0) {
             return str_replace($d, '', $url);
          }
       }
       return $url;
    }

    /**
     * Show the pagespeed or yslow stats.
     *
     * @return \Illuminate\Http\Response
     */
    public function getstats($type = null, $id)
    {
        $Insightdata = array();
        $InsightTypeData = array();
        $data = array();
        $typeData = array();
        $resourcedata = StoreViewsGTMetrix::select('pagespeed_json', 'yslow_json', 'pagespeed_insight_json')->where('test_id', $id)->orderBy("created_at", "desc")->get();
        foreach ($resourcedata as $value) {
            if($type == 'pagespeed' && $id){
                $title = 'PageSpeed';
                $typeData['type'] = 'GT Metrix';
                if(!empty($value['pagespeed_json'])){
                    $pagespeeddata = strip_tags(file_get_contents(public_path().$value['pagespeed_json']));
                    $jsondata = json_decode($pagespeeddata, true);
                    foreach ($jsondata['rules'] as $key=>$pagespeed) {
                        $data[$key]['name'] = $pagespeed['name'];
                        if(isset($pagespeed['score'])){
                            $data[$key]['score'] = $pagespeed['score']; 
                        }else{
                            $data[$key]['score'] = 'n/a';
                        }  
                    }
                }
                
            }
            if($type == 'pagespeed' && $id){
                $title = 'PageSpeed';
                $InsightTypeData['type'] = 'PageSpeed Insight';
                if(!empty($value['pagespeed_insight_json'])){
                    $pagespeedInsightdata = strip_tags(file_get_contents(public_path().$value['pagespeed_insight_json']));
                    $jsondata = json_decode($pagespeedInsightdata, true);
                    foreach ($jsondata['lighthouseResult']['audits'] as $key=>$pagespeed) {
                        $Insightdata[$key]['name'] = $pagespeed['id'];
                        if(isset($pagespeed['score'])){
                            $Insightdata[$key]['score'] = $pagespeed['score']; 
                        }else{
                            $Insightdata[$key]['score'] = 'n/a';
                        }  
                    }
                }
                
            }

            if($type == 'yslow' && $id){
                $title = 'YSlow';
                if(!empty($value['yslow_json'])){
                    $typeData['type'] = 'YSlow';
                    $yslowdata = strip_tags(file_get_contents(public_path().$value['yslow_json']));
                    $jsondata = json_decode($yslowdata, true);
                    $i=0;
                    foreach ($jsondata['g'] as $key=>$yslow) {
                        $data[$i]['name'] = trans('lang.'.$key);
                        if(isset($yslow['score'])){
                            $data[$i]['score'] = $yslow['score']; 
                        }else{
                            $data[$i]['score'] = 'n/a'; 
                        } 
                        $i++;                 
                    }
                }
            }
        }
        return view('gtmetrix.stats', compact('data','title','typeData','Insightdata','InsightTypeData','Insightdata'));
        //return response()->json(["code" => 200, "data" => $data]);
    }

    public function getstatsComparison($id)
    {
        $page_data = array();
        $yslow_data = array();
        $PageResourcedata = StoreViewsGTMetrix::select('pagespeed_json', 'yslow_json')->where('test_id', $id)->where('pagespeed_json','!=', null)->orderBy("created_at", "desc")->limit(5)->get();
        $YslowResourcedata = StoreViewsGTMetrix::select('pagespeed_json', 'yslow_json')->where('test_id', $id)->where('yslow_json','!=', null)->orderBy("created_at", "desc")->limit(5)->get();
        
            if($PageResourcedata){
                foreach ($PageResourcedata as $value) {
                    if(!empty($value['pagespeed_json'])){
                        $pagespeeddata = strip_tags(file_get_contents(public_path().$value['pagespeed_json']));
                        $jsondata = json_decode($pagespeeddata, true);
                        foreach ($jsondata['rules'] as $key=>$pagespeed) {
                            if(!isset($page_data[$pagespeed['name']])){
                                $page_data[$pagespeed['name']] = [];
                            }
                            if(isset($pagespeed['score'])){
                                $page_data[$pagespeed['name']][] = $pagespeed['score'];
                            }else{
                                $page_data[$pagespeed['name']][] = 'NA';
                            } 
                        }
                    }
                }
                
            }
            if($YslowResourcedata){
                foreach ($PageResourcedata as $value) {
                    if(!empty($value['yslow_json'])){
                        $yslowdata = strip_tags(file_get_contents(public_path().$value['yslow_json']));
                        $jsondata = json_decode($yslowdata, true);
                        $i=0;
                        foreach ($jsondata['g'] as $key=>$yslow) {
                            if(!isset($yslow_data[trans('lang.'.$key)])){
                                $yslow_data[trans('lang.'.$key)] = [];
                            }
                            if(isset($yslow['score'])){
                                $yslow_data[trans('lang.'.$key)][] = $yslow['score'];
                            }else{
                                $yslow_data[trans('lang.'.$key)][] = 'NA';
                            }               
                        }
                    }
                }
            }
            $Colname = [
                'First', 'Second', 'Third', 'Fourth', 'Fifth'
            ];
        return view('gtmetrix.comparison', compact('yslow_data','page_data', 'Colname'));
        //return response()->json(["code" => 200, "data" => $data]);
    }

    public function MultiRunErpEvent(Request $request)
    {


        foreach ($request->arrayList as $key => $value) {
            $gtmatrixAccount = StoreGTMetrixAccount::select(\DB::raw('store_gt_metrix_account.*'));
            $gtmatrix = StoreViewsGTMetrix::where('id', $value)->first();

            if ($gtmatrix) {
                $gt_metrix['store_view_id'] = $gtmatrix->store_view_id;
                $gt_metrix['website_url'] = $gtmatrix->website_url;
                $new_id = StoreViewsGTMetrix::create($gt_metrix)->id;
                $gtmetrix = StoreViewsGTMetrix::where('id', $new_id)->first();
                $gtmatrix = StoreViewsGTMetrix::where('store_view_id', $gt_metrix['store_view_id'])->where('website_url',$gt_metrix['website_url'])->first();
                try {

                    if(!empty($gtmatrix->account_id)){
                        $gtmatrixAccountData = StoreGTMetrixAccount::where('account_id', $gtmatrix->account_id)->where('status', 'active')->first();

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://gtmetrix.com/api/2.0/status',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_USERPWD => $gtmatrixAccountData->account_id . ":" . '',
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
                    // $stdClass = json_decode(json_encode($response));
                        $data = json_decode($response);
                    $credits = $data->data->attributes->api_credits;
                    // print_r($data->data->attributes->api_credits);
                        if($credits!= 0){
                            $client = new GTMetrixClient();
                            $client->setUsername($gtmatrixAccountData->email);
                            $client->setAPIKey($gtmatrixAccountData->account_id);
                            $client->getLocations();
                            $client->getBrowsers();  
                            $test   = $client->startTest($gtmetrix->website_url);
                            $update = [
                                'test_id' => $test->getId(),
                                'status'  => 'queued',
                            ];
                            $gtmetrix->update($update);
                            
                        }
                    }
                    else{
                        $AccountData = $gtmatrixAccount->where('status', 'active')->orderBy('id','desc')->get();

                        foreach ($AccountData as $key => $value) {
                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://gtmetrix.com/api/2.0/status',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_USERPWD => $value['account_id'] . ":" . '',
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            ));

                            $response = curl_exec($curl);

                            curl_close($curl);
                            // decode the response 
                            $data = json_decode($response);
                            $credits = $data->data->attributes->api_credits;
                            if($credits!= 0){
                                $client = new GTMetrixClient();
                                $client->setUsername($value['email']);
                                $client->setAPIKey($value['account_id']);
                                $client->getLocations();
                                $client->getBrowsers();  
                                $test   = $client->startTest($gtmetrix->website_url);
                                $update = [
                                    'test_id' => $test->getId(),
                                    'status'  => 'queued',
                                    'account_id'  => $value['account_id'],
                                ];
                                $gtmetrix->update($update);
                                break;
                                
                            }
                        }

                    }

                } 
                catch (\Exception $e) {
                    return response()->json(["code" => 500, "message" => "Error :" . $e->getMessage()]);
                }
            }
        }
        return response()->json(["code" => 200, "message" => "Request has been send for queue successfully"]);
       
     
    }
}