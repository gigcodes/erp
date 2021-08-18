<?php

namespace App\Http\Controllers\gtmetrix;

use App\Http\Controllers\Controller;
use App\Setting;
use App\StoreViewsGTMetrix;
use Entrecore\GTMetrixClient\GTMetrixClient;
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

        $list = $query->from(\DB::raw('(SELECT MAX( id) as id,  store_view_id, html_load_time FROM store_views_gt_metrix GROUP BY store_views_gt_metrix.store_view_id) as t'))
            ->leftJoin('store_views_gt_metrix', 't.id', '=', 'store_views_gt_metrix.id')->orderBy('id', 'desc')
            ->paginate(25);

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
        $gtmatrix = StoreViewsGTMetrix::where('id', $request->id)->first();

        if ($gtmatrix) {
            $gt_metrix['store_view_id'] = $gtmatrix->store_view_id;
            $gt_metrix['website_url'] = $gtmatrix->website_url;
            $new_id = StoreViewsGTMetrix::create($gt_metrix)->id;
            $gtmetrix = StoreViewsGTMetrix::where('id', $new_id)->first();
            try {

                $client = new GTMetrixClient();
                $client->setUsername(env('GTMETRIX_USERNAME'));
                $client->setAPIKey(env('GTMETRIX_API_KEY'));
                $client->getLocations();
                $client->getBrowsers();

                $test   = $client->startTest($gtmetrix->website_url);
                $update = [
                    'test_id' => $test->getId(),
                    'status'  => 'queued',
                ];
                $gtmetrix->update($update);

                return response()->json(["code" => 200, "message" => "Request has been send for queue successfully"]);

            } catch (\Exception $e) {
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
        $data = array();
        $resourcedata = StoreViewsGTMetrix::select('pagespeed_json', 'yslow_json')->where('test_id', $id)->orderBy("created_at", "desc")->get();
        foreach ($resourcedata as $value) {
            if($type == 'pagespeed' && $id){
                $title = 'PageSpeed';
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
            if($type == 'yslow' && $id){
                $title = 'YSlow';
                if(!empty($value['yslow_json'])){
                    $yslowdata = strip_tags(file_get_contents(public_path().$value['yslow_json']));
                    dd($yslowdata);exit;
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
        return view('gtmetrix.stats', compact('data','title'));
        //return response()->json(["code" => 200, "data" => $data]);
    }
}