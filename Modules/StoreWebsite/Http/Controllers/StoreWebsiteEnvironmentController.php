<?php

namespace Modules\StoreWebsite\Http\Controllers;

use Auth;
use App\User;
use App\Service;
use App\Setting;
use App\Website;
use Carbon\Carbon;
use App\ChatMessage;
use App\StoreWebsite;
use App\WebsiteStore;
use App\AssetsManager;
use App\SocialStrategy;
use App\SiteDevelopment;
use App\StoreWebsiteGoal;
use App\StoreWebsitePage;
use App\StoreWebsiteSize;
use App\WebsiteStoreView;
use App\StoreWebsiteBrand;
use App\StoreWebsiteColor;
use App\StoreWebsiteImage;
use App\StoreWebsiteUsers;
use App\StoreWebsitesApiTokenLog;
use Illuminate\Support\Str;
use App\BuildProcessHistory;
use App\LogStoreWebsiteUser;
use App\StoreReIndexHistory;
use App\StoreWebsiteProduct;
use Illuminate\Http\Request;
use App\StoreWebsiteAnalytic;
use App\StoreWebsiteCategory;
use Illuminate\Http\Response;
use App\SocialStrategySubject;
use App\StoreWebsiteSeoFormat;
use App\Models\WebsiteStoreTag;
use App\StoreViewCodeServerMap;
use App\StoreWebsiteAttributes;
use App\Github\GithubRepository;
use App\SiteDevelopmentCategory;
use App\StoreWebsiteCategorySeo;
use App\StoreWebsiteUserHistory;
use App\MagentoDevScripUpdateLog;
use App\StoreWebsiteProductPrice;
use App\StoreWebsiteTwilioNumber;
use Illuminate\Routing\Controller;
use App\ProductCancellationPolicie;
use App\StoreWebsiteProductAttribute;
use App\StoreWebsitesCountryShipping;
use App\Jobs\DuplicateStoreWebsiteJob;
use App\StoreWebsiteProductScreenshot;
use App\MagentoSettingUpdateResponseLog;
use App\StoreWebsiteEnvironment;
use App\StoreWebsiteEnvironmentHistory;
use Illuminate\Support\Facades\Validator;
use seo2websites\MagentoHelper\MagentoHelperv2;
use Illuminate\Support\Facades\Http;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class StoreWebsiteEnvironmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $title = 'Store Website Environment ';

        $storeWebsites = StoreWebsite::all()->pluck('title', 'id');
        
        $paths = StoreWebsiteEnvironment::pluck('path', 'path');

        return view('storewebsite::environment.environment', [
            'title' => $title,
            'storeWebsites' => $storeWebsites,
            'paths' => $paths,
        ]);
    }
    
    public function matrix(Request $request)
    {
        $title = 'Store Website Environment ';

        $storeWebsites = StoreWebsite::all()->pluck('title', 'id');
        
        $paths = StoreWebsiteEnvironment::pluck('path', 'path');
        
        $env_paths = StoreWebsiteEnvironment::groupBy('path');
        $env_store_websites = StoreWebsiteEnvironment::leftJoin('store_websites as sw', 'sw.id', 'store_website_environments.store_website_id')->select(['store_website_environments.store_website_id', 'sw.title as store_website_name', 'store_website_environments.path']);
        if($request->store_websites){
            $env_paths->whereIn('store_website_id',$request->store_websites);
            $env_store_websites->whereIn('store_website_id',$request->store_websites);
        }
        if($request->paths){
            $env_paths->where('path',$request->paths);
            $env_store_websites->where('path',$request->paths);
        }
        
        $env_paths=$env_paths->pluck('path', 'id');
        $env_store_websites=$env_store_websites->groupBy('store_website_id')->pluck('store_website_name', 'store_website_id');
        
        $environments = StoreWebsiteEnvironment::select('id', 'store_website_id','path','value')->get()->toArray();
        
       
        $result = [];
        array_walk($environments, function ($value, $key) use (&$result) {
            $result[$value['store_website_id']][] = $value;
        });

        return view('storewebsite::environment.environment-matrix', [
            'title' => $title,
            'storeWebsites' => $storeWebsites,
            'paths' => $paths,
            'env_paths' => $env_paths,
            'env_store_websites' => $env_store_websites,
            'environments' => $result,
        ]);
    }

    public function records(Request $request){
        $environments = StoreWebsiteEnvironment::leftJoin('store_websites as sw', 'sw.id', 'store_website_environments.store_website_id');

        if ($request->store_website_id != null) {
            $environments = $environments->where('store_website_environments.store_website_id', $request->store_website_id);
        }
        if ($request->paths != null) {
            $environments = $environments->where('store_website_environments.path', $request->paths);
        }

        $environments = $environments->orderBy('store_website_environments.id', 'desc')->select(['store_website_environments.*', 'sw.title as store_website_name'])->paginate();

        $items = $environments->items();

        $recItems = [];
        foreach ($items as $item) {
            $attributes = $item->getAttributes();
            $recItems[] = $attributes;
        }
        return response()->json(['code' => 200, 'pageUrl' => $request->page_url, 'data' => $recItems, 'total' => $environments->total(),
            'pagination' => (string) $environments->links(),
        ]);
    }
    
    /**
     * records Page
     *
     * @param  Request  $request [description]
     */
    public function updateValue(Request $request)
    {
        $post = $request->all();
        $id = $request->get('id', 0);

        $params = [
            'value'           => 'required',
            'command'           => 'required',
        ];
        $validator = Validator::make($post, $params);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . '<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $records = StoreWebsiteEnvironment::find($id);

        if (! $records) {
            return response()->json(['code' => 500, 'error' => 'Store Website Environment data not found!']);
        }
        $environment_id=$records->id;
        $old_value=$records->value;
        $new_value=$request->value;
        $updated_by=$request->user_id;
        $cmd=$request->command;
        $store_website_id=$records->store_website_id;
        $path=$records->path;
        //$records->save();

        \Log::info("Start Environment Pushed");
        $storeWebsite=StoreWebsite::where('id', $store_website_id)->first();
        $cwd='';
        $assetsmanager = new AssetsManager;
        if($storeWebsite){
            $cwd=$storeWebsite->working_directory;
            $assetsmanager = AssetsManager::where('id', $storeWebsite->assets_manager_id)->first();
        }
        
        if($assetsmanager && $assetsmanager->client_id!='')
        {
            
            
            $client_id=$assetsmanager->client_id;
            $url="https://s10.theluxuryunlimited.com:5000/api/v1/clients/".$client_id."/commands";
            $key=base64_encode("admin:86286706-032e-44cb-981c-588224f80a7d");
            
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $parameters = [
                'command' => $cmd, 
                'cwd' => $cwd,
                'is_sudo' => true, 
                'timeout_sec' => 300, 
            ];
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

            $headers = [];
            $headers[] = 'Authorization: Basic '.$key;
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            \App\LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($result), 
            $httpcode,\Modules\StoreWebsite\Http\Controllers\StoreWebsiteEnvironmentController::class, 'update');
            
            \Log::info("API result: ".$result);
            \Log::info("API Error Number: ".curl_errno($ch));
            if (curl_errno($ch)) {
                \Log::info("API Error: ".curl_error($ch));
                
                StoreWebsiteEnvironmentHistory::create(['environment_id' => $environment_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'key' => $path, 'old_value' => $old_value, 'new_value' => $new_value, 'command' => $cmd, 'status' => "Error", 'response' => curl_error($ch)]);
                
                return response()->json(['code' => 500, 'error' =>curl_error($ch)]);
            }
            
            $response = json_decode($result);

            curl_close($ch);
                
            if(isset($response->errors)){ 
                $message='';
                foreach($response->errors as $error){
                    $message.=" ".$error->code.":".$error->title.":".$error->detail;
                }
                
                StoreWebsiteEnvironmentHistory::create(['environment_id' => $environment_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'key' => $path, 'old_value' => $old_value, 'new_value' => $new_value, 'command' => $cmd, 'status' => "Error", 'response' => $message]);
                \Log::info($message);
                return response()->json(['code' => 500, 'error' => $message]);

            }else{
                if(isset($response->data) && isset($response->data->jid) ){
                    $job_id=$response->data->jid;
                    $status="Success";
                    
                    StoreWebsiteEnvironmentHistory::create(['environment_id' => $environment_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'key' => $path, 'old_value' => $old_value, 'new_value' => $new_value, 'command' => $cmd, 'status' => "Success", 'response' => 'Success', 'job_id' => $job_id]);
                    $records->value=$new_value;
                    $records->save();
                    \Log::info("Job Id:".$job_id);
                    return response()->json(['code' => 200, 'data' => $records,'message'=>'Request pushed on website successfully']);
                }else{
                    StoreWebsiteEnvironmentHistory::create(['environment_id' => $environment_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'key' => $path, 'old_value' => $old_value, 'new_value' => $new_value, 'command' => $cmd, 'status' => "Error", 'response' =>"Job Id not found in response"]);
                    
                    \Log::info("Job Id not found in response!");
                    return response()->json(['code' => 500, 'error' => 'Job Id not found in response!']);
                }
            }
        }else{
            
            StoreWebsiteEnvironmentHistory::create(['environment_id' => $environment_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'key' => $path, 'old_value' => $old_value, 'new_value' => $new_value, 'command' => $cmd, 'status' => "Error", 'response' => "Assets Manager & Client id not found the Store Website!"]);
            
            \Log::info("Assets Manager & Client id not found the Store Website!");
            
            return response()->json(['code' => 500, 'error' => 'Assets Manager & Client id not found the Store Website!']);
        }

        \Log::info("End Environment Pushed");

        return response()->json(['code' => 200, 'data' => $records,'message'=>'update']);
    }

    /**
     * records Page
     *
     * @param  Request  $request [description]
     */
    public function store(Request $request)
    {
        $post = $request->all();
        $id = $request->get('id', 0);

        $params = [
            'path' => 'required',
            'store_website_id' => 'required',
            'command'           => 'required',
        ];
        if (empty($id)) {
            $params['value'] ='required';
        }
        $validator = Validator::make($post, $params);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . '<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $records = StoreWebsiteEnvironment::find($id);

        if (! $records) {
            $records = new StoreWebsiteEnvironment;
        }
        $records->fill($post);
        $records->save();
        return response()->json(['code' => 200, 'data' => $records]);
    }

   
    /**
     * Edit Page
     *
     * @param  Request  $request [description]
     */
    public function edit(Request $request, $id)
    {
        $StoreWebsiteEnvironment = StoreWebsiteEnvironment::where('id', $id)->first();

        if ($StoreWebsiteEnvironment) {
            return response()->json(['code' => 200, 'data' => $StoreWebsiteEnvironment]);
        }

        return response()->json(['code' => 500, 'error' => 'Data Not Found']);
    }

    public function history(Request $request, $id)
    {
        $histories = \App\StoreWebsiteEnvironmentHistory::select('store_website_environment_histories.*', 'u.name AS userName')->leftJoin('users AS u', 'u.id', 'store_website_environment_histories.updated_by')->where('environment_id', $id)->latest()->get();

        foreach($histories as $logs){
            if($logs->store_website_id !='' && $logs->job_id!=''){
                
                $storeWebsite=StoreWebsite::where('id', $logs->store_website_id)->first();
                $assetsmanager = new AssetsManager;
                if($storeWebsite){
                    $assetsmanager = AssetsManager::where('id', $storeWebsite->assets_manager_id)->first();
                }
                
                if($assetsmanager && $assetsmanager->client_id!=''){
                    $client_id=$assetsmanager->client_id;
                        $job_id=$logs->job_id;
                        $url="https://s10.theluxuryunlimited.com:5000/api/v1/clients/".$client_id."/commands/".$job_id;
                        $key=base64_encode("admin:86286706-032e-44cb-981c-588224f80a7d");
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL,$url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                        
                        $headers = [];
                        $headers[] = 'Authorization: Basic '.$key;
                        //$headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);
                        $response = json_decode($result);
                        \Log::info("API Response: ".$result);
                        if(isset($response->data) && isset($response->data->result) ){
                            $logs->status=$response->data->status;
                            $result=$response->data->result;
                            $message='';
                            if(isset($result->stdout) && $result->stdout!=''){
                                $message.='Output: '.$result->stdout;
                            }
                            if(isset($result->stderr) && $result->stderr!=''){
                                $message.='Error: '.$result->stderr;
                            }
                            if(isset($result->summary) && $result->summary!=''){
                                $message.='summary: '.$result->summary;
                            }
                            if($message!=''){
                                $logs->response=$message;
                            }
                        }

                        curl_close($ch);
                }
            }  
        }
        return response()->json(['code' => 200, 'data' => $histories]);
    }
   
}
