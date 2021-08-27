<?php

namespace App\Http\Controllers;

use App\MagentoSetting;
use App\StoreWebsite;
use App\Website;
use App\WebsiteStore; 
use App\MagentoSettingLog;
use App\WebsiteStoreView;
use Illuminate\Http\Request; 

use Carbon\Carbon;

class MagentoSettingsController extends Controller
{

    public function index(Request $request)
    {

        $magentoSettings = MagentoSetting::with(
            'storeview.websiteStore.website.storeWebsite', 
            'store.website.storeWebsite',
            'website');
        
        if($request->scope){
            $magentoSettings->where('scope', $request->scope);
        }

        if($request->website){
            
            if(empty($request->scope)){
                $magentoSettings->whereHas('storeview.websiteStore.website.storeWebsite', function($q) use ($request){
                    $q->where('id', $request->website);
                })->orWhereHas('store.website.storeWebsite', function($q) use ($request){
                    $q->where('id', $request->website);
                })->orWhereHas('website', function($q) use ($request){
                    $q->where('id', $request->website);
                });
            }else{
                if($request->scope == 'default'){
                    $website_ids = StoreWebsite::where('id', $request->website)->get()->pluck('id')->toArray();
                    $magentoSettings->whereIn('scope_id', $website_ids ?? []);
                }else if($request->scope == 'websites'){
                    $website_ids = Website::where('store_website_id', $request->website)->get()->pluck('id')->toArray();
                    $website_store_ids = WebsiteStore::whereIn('website_id', $website_ids ?? [])->get()->pluck('id')->toArray();
                    $magentoSettings->whereIn('scope_id', $website_store_ids ?? []);
                }else if($request->scope == 'stores'){
                    $website_ids = Website::where('store_website_id', $request->website)->get()->pluck('id')->toArray();
                    $website_store_ids = WebsiteStore::whereIn('website_id', $website_ids ?? [])->get()->pluck('id')->toArray();
                    $website_store_view_ids = WebsiteStoreView::whereIn('website_store_id', $website_store_ids ?? [])->get()->pluck('id')->toArray();
                    $magentoSettings->whereIn('scope_id', $website_store_view_ids ?? []);
                }
            } 
        }

        $magentoSettings = $magentoSettings->orderBy('created_at', 'DESC')->paginate(25);    
        $storeWebsites = StoreWebsite::get();
        $websitesStores = WebsiteStore::get()->pluck('name')->unique()->toArray();
        $websiteStoreViews = WebsiteStoreView::get()->pluck('code')->unique()->toArray();
        
        return view('magento.settings.index', [
            'magentoSettings' => $magentoSettings,
            'storeWebsites' => $storeWebsites,
            'websitesStores' => $websitesStores ,
            'websiteStoreViews' => $websiteStoreViews,
        ]);
    }

    public function create(Request $request)
    { 
        $name = $request->name;
        $path = $request->path;
        $value = $request->value;
        $scope = "default";
        foreach ($request->scope as $scope) :
            $scope = $scope;
        endforeach;
        
        if ($scope === 'default') {
            
            $storeWebsites = StoreWebsite::whereIn('id', $request->website)->get();
            foreach($storeWebsites as $storeWebsite){

                $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id',$storeWebsite->id)->where('path', $path)->first();
                if(!$m_setting){
                    $m_setting = MagentoSetting::Create([
                        'scope' => $scope,
                        'scope_id' => $storeWebsite->id,
                        'store_website_id' => $storeWebsite->id,
                        'website_store_id' => 0,
                        'website_store_view_id' => 0,
                        'name' => $name,
                        'path' => $path,
                        'value' => $value
                    ]);
                }

            }
        }

        if($scope === 'websites'){

            $storeWebsites = StoreWebsite::whereIn('id', $request->website)->get();
            foreach($storeWebsites as $storeWebsite):
            
                $websiteStores = WebsiteStore::whereIn('id', $request->website_store)->get();
                foreach($websiteStores as $websiteStore){
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id',$websiteStore->id)->where('store_website_id',$storeWebsite->id)->where('website_store_id',$websiteStore->id)->where('path', $path)->first();
                    if(!$m_setting){
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStore->id,
                            'store_website_id' => $storeWebsite->id,
                            'website_store_id' => $websiteStore->id,
                            'website_store_view_id' => 0,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value
                        ]);
                    }
                }
                
            endforeach;    

        }

        if($scope === 'stores'){
            
            $storeWebsites = StoreWebsite::whereIn('id', $request->website)->get();
            foreach($storeWebsites as $storeWebsite):
                
                $websiteStores = $request->website_store;
                $websiteStoresViews = WebsiteStoreView::whereIn('id', $request->website_store_view)->get();
                $i = 0;
                foreach($websiteStoresViews as $websiteStoresView){

                    $websiteStore = $websiteStores[0];

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id',$websiteStoresView->id)->where('path', $path)->where('store_website_id', $storeWebsite->id)->where('website_store_id', $websiteStore)->where('website_store_view_id', $websiteStoresView->id)->first();
                    if(!$m_setting){                    
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStoresView->id,
                            'store_website_id' => $storeWebsite->id,
                            'website_store_id' => $websiteStore,
                            'website_store_view_id' => $websiteStoresView->id,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value
                        ]);
                    }
                    $i++;
                }
                
            endforeach;
                

        }

        
        return response()->json(['status' => true]);

    }

    public function update(Request $request)
    {
        $entity_id = $request->id;
        $scope = $request->scope;
        $name = $request->name;
        $path = $request->path;
        $value = $request->value;
        $is_live = isset($request->live);
        $is_development = isset($request->development);
        $is_stage = isset($request->stage);
        $website_ids = $request->websites;
        MagentoSetting::where('id', $request->id)->update([
            'name' => $name,
            'path' => $path,
            'value' => $value
        ]); 
        $entity = MagentoSetting::find($entity_id);

        if ($scope === 'default') {
            
            $storeWebsites = StoreWebsite::whereIn('id', $website_ids ?? [])->orWhere('website', $request->website)->get();

            foreach($storeWebsites as $storeWebsite){
                
                $magento_url = $storeWebsite->magento_url;
                if($magento_url != null){
                    $magento_url = explode('//', $magento_url); 
                    $magento_url = isset($magento_url[1]) ? $magento_url[1] : $storeWebsite->magento_url;
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id',$storeWebsite->id)->where('path', $path)->first();
                    if(!$m_setting){
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $storeWebsite->id,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value
                        ]);
                    }else{
                        $m_setting->name = $name;
                        $m_setting->path = $path;
                        $m_setting->value = $value;
                        $m_setting->save();
                    }
                    $scopeID = 0;
                    $magento_url = str_replace('www.', '', $magento_url);
                    if($is_live){
                        $token = $storeWebsite->api_token;
                        \Cache::forever('key', $token);
                        $postURL = 'https://' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeId='.$scopeID;
                        $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                        \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        
                        $log = "postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result);
                        $formData = [ 'event'=>"edit", 'log'=>$log ];
                        MagentoSettingLog::create($formData);
                        
                        if(isset($result['response'])) {
                            $response = json_decode($result['response']);
                            if(isset($response[0]) && $response[0] == 1) {

                            }else{
                                return response()->json(["code" => 500 , "message" => "Request has been failed on live server please check laravel log"]);
                            }
                        }else{
                            return response()->json(["code" => 500 , "message" => "Request has been failed on live server please check laravel log"]);
                        }
                    }
                    if($is_stage){
                        $token = $storeWebsite->stage_api_token;
                        \Cache::forever('key', $token);
                        $postURL = 'https://stage.' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeId='.$scopeID;
                        $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                        \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        
                        $log = "postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result);
                        $formData = [ 'event'=>"edit", 'log'=>$log ];
                        MagentoSettingLog::create($formData);
                        
                        if(isset($result['response'])) {
                            $response = json_decode($result['response']);
                            if(isset($response[0]) && $response[0] == 1) {

                            }else{
                                return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                            }
                        }else{
                            return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                        }
                    }
                    if($is_development){
                        $token = $storeWebsite->dev_api_token;
                        \Cache::forever('key', $token);
                        $postURL = 'https://dev.' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeId='.$scopeID;
                        $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                        \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        
                        $log = "postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result);
                        $formData = [ 'event'=>"edit", 'log'=>$log ];
                        MagentoSettingLog::create($formData);
                        
                        if(isset($result['response'])) {
                            $response = json_decode($result['response']);
                            if(isset($response[0]) && $response[0] == 1) {

                            }else{
                                return response()->json(["code" => 500 , "message" => "Request has been failed on dev server please check laravel log"]);
                            }
                        }else{
                            return response()->json(["code" => 500 , "message" => "Request has been failed on dev server please check laravel log"]);
                        }
                    }                  
                }
            }
        }else if($scope === 'websites'){

            $store = $request->store;
            $website = $request->website;
            $websiteStores = WebsiteStore::with('website.storeWebsite')->whereHas('website', function($q) use ($store, $website_ids, $entity_id){
                $q->whereIn('store_website_id', $website_ids ?? [])->where('name', $store);
            })->orWhere('id', $entity->scope_id)->get();

            foreach($websiteStores as $websiteStore){

                $magento_url = isset($websiteStore->website->storeWebsite->magento_url) ? $websiteStore->website->storeWebsite->magento_url : null;
                if($magento_url != null){
                    $magento_url = explode('//', $magento_url); 
                    $magento_url = isset($magento_url[1]) ? $magento_url[1] : $websiteStore->website->storeWebsite->magento_url;
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id',$websiteStore->id)->where('path', $path)->first();
                    if(!$m_setting){
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStore->id,
                            'name' => $request->name,
                            'path' => $request->path,
                            'value' => $request->value
                        ]);
                    }else{
                        $m_setting->name = $name;
                        $m_setting->path = $path;
                        $m_setting->value = $value;
                        $m_setting->save();
                    }
                    $scopeID = $websiteStore->platform_id;
                    $magento_url = str_replace('www.', '', $magento_url);
                    if($is_live){
                        $token = $websiteStore->website->storeWebsite->api_token;
                        \Cache::forever('key', $token); 
                        $postURL = 'https://' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeId='.$scopeID;
                        $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                        \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        
                        $log = "postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result);
                        $formData = [ 'event'=>"edit", 'log'=>$log ];
                        MagentoSettingLog::create($formData);
                        
                        if(isset($result['response'])) {
                            $response = json_decode($result['response']);
                            if(isset($response[0]) && $response[0] == 1) {

                            }else{
                                return response()->json(["code" => 500 , "message" => "Request has been failed on live server please check laravel log"]);
                            }
                        }else{
                            return response()->json(["code" => 500 , "message" => "Request has been failed on live server please check laravel log"]);
                        }
                    }
                    if($is_stage){
                        $token = $websiteStore->website->storeWebsite->stage_api_token;
                        \Cache::forever('key', $token); 
                        $postURL = 'https://stage.' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeId='.$scopeID;
                        $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                        \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        
                        $log = "postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result);
                        $formData = [ 'event'=>"edit", 'log'=>$log ];
                        MagentoSettingLog::create($formData);
                        
                        if(isset($result['response'])) {
                            $response = json_decode($result['response']);
                            if(isset($response[0]) && $response[0] == 1) {

                            }else{
                                return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                            }
                        }else{
                            return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                        }
                    }
                    if($is_development){
                        $token = $websiteStore->website->storeWebsite->dev_api_token;
                        \Cache::forever('key', $token); 
                        $postURL = 'https://dev.' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeId='.$scopeID;
                        $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                        \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        
                        $log = "postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result);
                        $formData = [ 'event'=>"edit", 'log'=>$log ];
                        MagentoSettingLog::create($formData);
                        
                        if(isset($result['response'])) {
                            $response = json_decode($result['response']);
                            if(isset($response[0]) && $response[0] == 1) {

                            }else{
                                return response()->json(["code" => 500 , "message" => "Request has been failed on dev server please check laravel log"]);
                            }
                        }else{
                            return response()->json(["code" => 500 , "message" => "Request has been failed on dev server please check laravel log"]);
                        }
                    }                   
                }

            }
            
        }else if($scope === 'stores'){

            $store = $request->store;
            $store_view = $request->store_view; 
            
            $websiteStoresViews = WebsiteStoreView::with('websiteStore.website.storeWebsite')->whereHas('websiteStore.website', function($q) use ($store, $website_ids){
                $q->where('name', $store)->whereIn('store_website_id', $website_ids ?? []);
            })->where('code', $store_view)->orWhere('id', $entity->scope_id)->get();

            foreach($websiteStoresViews as $websiteStoresView){
                $magento_url = isset($websiteStoresView->websiteStore->website->storeWebsite->magento_url) ? $websiteStoresView->websiteStore->website->storeWebsite->magento_url : null;
                if($magento_url != null){
                    $magento_url = explode('//', $magento_url); 
                    $magento_url = isset($magento_url[1]) ? $magento_url[1] : $websiteStoresView->websiteStore->website->storeWebsite->magento_url;
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id',$websiteStoresView->id)->where('path', $path)->first();
                    if(!$m_setting){
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStoresView->id,
                            'name' => $request->name,
                            'path' => $request->path,
                            'value' => $request->value
                        ]);
                    }else{
                        $m_setting->name = $name;
                        $m_setting->path = $path;
                        $m_setting->value = $value;
                        $m_setting->save();
                    }
                    $scopeID = $websiteStoresView->platform_id;
                    $magento_url = str_replace('www.', '', $magento_url);
                    if($is_live){
                        $token = $websiteStoresView->websiteStore->website->storeWebsite->api_token;
                        \Cache::forever('key', $token);
                        $postURL = 'https://' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeId='.$scopeID;
                        $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                        \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        
                        $log = "postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result);
                        $formData = [ 'event'=>"edit", 'log'=>$log ];
                        MagentoSettingLog::create($formData);
                        
                        if(isset($result['response'])) {
                            $response = json_decode($result['response']);
                            if(isset($response[0]) && $response[0] == 1) {

                            }else{
                                return response()->json(["code" => 500 , "message" => "Request has been failed on live server please check laravel log"]);
                            }
                        }else{
                            return response()->json(["code" => 500 , "message" => "Request has been failed on live server please check laravel log"]);
                        }
                    }
                    if($is_stage){
                        $token = $websiteStoresView->websiteStore->website->storeWebsite->stage_api_token;
                        \Cache::forever('key', $token);
                        $postURL = 'https://stage.' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeId='.$scopeID;
                        $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                        \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        
                        $log = "postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result);
                        $formData = [ 'event'=>"edit", 'log'=>$log ];
                        MagentoSettingLog::create($formData);
                        
                        if(isset($result['response'])) {
                            $response = json_decode($result['response']);
                            if(isset($response[0]) && $response[0] == 1) {

                            }else{
                                return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                            }
                        }else{
                            return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                        }
                    }
                    if($is_development){
                        $token = $websiteStoresView->websiteStore->website->storeWebsite->dev_api_token;
                        \Cache::forever('key', $token);
                        $postURL = 'https://dev.' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeId='.$scopeID;
                        $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                        \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        
                        $log = "postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result);
                        $formData = [ 'event'=>"edit", 'log'=>$log ];
                        MagentoSettingLog::create($formData);
                        
                        if(isset($result['response'])) {
                            $response = json_decode($result['response']);
                            if(isset($response[0]) && $response[0] == 1) {

                            }else{
                                return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                            }
                        }else{
                            return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                        }
                    }
                }

            }

            return response()->json(["code" => 200 , "message" => "Request pushed on website successfully"]);
            
        }
        
    }

    public function websiteStores(Request $request){ 
        $website_ids = Website::where('store_website_id', $request->website_id)->get()->pluck('id')->toArray();
        return response()->json([
            'data' => WebsiteStore::select('id', 'name')->whereNotNull('name')->whereIn('website_id', $website_ids)->get()
        ]);
    }
    
    public function websiteStoreViews(Request $request){
        return response()->json([
            'data' => WebsiteStoreView::select('id', 'code')->whereNotNull('code')->where('website_store_id', $request->website_id)->get()
        ]);
    }
    
    public function deleteSetting($id){
        $m_setting = MagentoSetting::find($id);
        if($m_setting){
            $m_setting->delete();
            $log = $id." Id Deleted successfully";
            $formData = [ 'event'=>"delete", 'log'=>$log ];
            MagentoSettingLog::create($formData);
        }
        return redirect()->route('magento.setting.index');        
    }
    
}