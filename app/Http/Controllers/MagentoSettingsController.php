<?php

namespace App\Http\Controllers;

use App\MagentoSetting;
use App\StoreWebsite;
use App\Website;
use App\WebsiteStore; 
use App\WebsiteStoreView;
use Illuminate\Http\Request; 


class MagentoSettingsController extends Controller
{

    public function index(Request $request)
    {

        $magentoSettings = MagentoSetting::with(
            'storeview.websiteStore.website.storeWebsite', 
            'store.website.storeWebsite',
            'website');

        if($request->scope){
            $magentoSettings = $magentoSettings->where('scope', $request->scope);
        }

        if($request->website){
            $magentoSettings = $magentoSettings->whereHas('storeview.websiteStore.website.storeWebsite', function($q) use ($request){
                $q->where('id', $request->website);
            })->orWhereHas('store.website.storeWebsite', function($q) use ($request){
                $q->where('id', $request->website);
            })->orWhereHas('website', function($q) use ($request){
                $q->where('id', $request->website);
            });
        }

        if($request->store){
            $magentoSettings = $magentoSettings->whereHas('storeview.websiteStore', function($q) use ($request){
                $q->where('name', $request->store);
            })->orWhereHas('store', function($q) use ($request){
                $q->where('name', $request->store);
            });
        }

        if($request->store_view){
            $magentoSettings = $magentoSettings->whereHas('storeview', function($q) use ($request){
                $q->where('code', $request->store_view);
            });
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
        foreach ($request->scope as $scope) {

            if ($scope === 'default') {

                $storeWebsites = StoreWebsite::whereIn('id', $request->website)->get();

                foreach($storeWebsites as $storeWebsite){

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id',$storeWebsite->id)->where('path', $path)->first();
                    if(!$m_setting){
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $storeWebsite->id,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value
                        ]);
                    }

                }
            }

            if($scope === 'websites'){

                $websiteStores = WebsiteStore::whereIn('id', $request->website_store)->get();

                foreach($websiteStores as $websiteStore){

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id',$websiteStore->id)->where('path', $path)->first();
                    if(!$m_setting){
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStore->id,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value
                        ]);
                    }

                }
                
            }

            if($scope === 'stores'){

                $websiteStoresViews = WebsiteStoreView::whereIn('id', $request->website_store_view)->get();

                foreach($websiteStoresViews as $websiteStoresView){

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id',$websiteStoresView->id)->where('path', $path)->first();
                    if(!$m_setting){
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStoresView->id,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value
                        ]);
                    }

                }
                
            }

        }

        return response()->json(['status' => true]);

    }

    public function update(Request $request)
    {
        $scope = $request->scope;
        $name = $request->name;
        $path = $request->path;
        $value = $request->value;
        $is_live = isset($request->live);
        $is_development = isset($request->development);
        $website_ids = $request->websites;
        MagentoSetting::where('id', $request->id)->update([
            'name' => $name,
            'path' => $path,
            'value' => $value
        ]);
        if ($scope === 'default' && $website_ids != null) {

            $storeWebsites = StoreWebsite::whereIn('id', $website_ids)->get();

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
                        $scopeID = 0;
                        if($is_live){
                            $token = $storeWebsite->api_token;
                            \Cache::forever('key', $token);
                            $postURL = 'https://' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeID='.$scopeID;
                            $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                            \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        }
                        if($is_development){
                            $token = $storeWebsite->dev_api_token;
                            \Cache::forever('key', $token);
                            $postURL = 'https://dev.' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeID='.$scopeID;
                            $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                            \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        }
                    }
                  
                }
            }
        }else if($scope === 'websites' && $website_ids != null){

            $store = $request->store;
            $website = $request->website;
            $websiteStores = WebsiteStore::with('website.storeWebsite')->whereHas('website', function($q) use ($store, $website_ids){
                $q->whereIn('store_website_id', $website_ids)->where('name', $store);
            })->get();

            foreach($websiteStores as $websiteStore){

                $magento_url = $websiteStore->website->storeWebsite->magento_url;
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
                        $scopeID = $websiteStore->id;
                        if($is_live){
                            $token = $websiteStore->website->storeWebsite->api_token;
                            \Cache::forever('key', $token); 
                            $postURL = 'https://' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeID='.$scopeID;
                            $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                            \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        }
                        if($is_development){
                            $token = $websiteStore->website->storeWebsite->dev_api_token;
                            \Cache::forever('key', $token); 
                            $postURL = 'https://dev.' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeID='.$scopeID;
                            $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                            \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        }
                    } 
                   
                }

            }
            
        }else if($scope === 'stores' && $website_ids != null){

            $store = $request->store;
            $store_view = $request->store_view; 
            
            $websiteStoresViews = WebsiteStoreView::with('websiteStore.website.storeWebsite')->whereHas('websiteStore.website', function($q) use ($store, $website_ids){
                $q->where('name', $store)->whereIn('store_website_id', $website_ids);
            })->where('code', $store_view)->get();

            foreach($websiteStoresViews as $websiteStoresView){

                $magento_url = $websiteStoresView->websiteStore->website->storeWebsite->magento_url;
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
                        $scopeID = $websiteStoresView->id;
                        if($is_live){
                            $token = $websiteStoresView->websiteStore->website->storeWebsite->api_token;
                            \Cache::forever('key', $token);
                            $postURL = 'https://' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeID='.$scopeID;
                            $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                            \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        }
                        if($is_development){
                            $token = $websiteStoresView->websiteStore->website->storeWebsite->dev_api_token;
                            \Cache::forever('key', $token);
                            $postURL = 'https://dev.' . $magento_url . '/rest/V1/configvalue/set?path='.$path.'&value='.$value.'&scope='.$scope.'&scopeID='.$scopeID;
                            $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, [], 'application/json', true, 'POST');
                            \Log::info("postURL : " . $postURL . " | magento_setting : " . json_encode($m_setting) . ' | response : ' . json_encode($result) );
                        }
                    } 
                  
                }

            }
            
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
    
}