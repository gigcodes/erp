<?php

namespace App\Http\Controllers;

use App\MagentoSetting;
use App\MagentoSettingNameLog;
use App\StoreWebsite;
use App\Website;
use App\WebsiteStore; 
use App\MagentoSettingLog;
use App\WebsiteStoreView;
use App\MagentoSettingPushLog;
use App\Github\GithubRepository;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;


class MagentoSettingsController extends Controller
{

    public function index(Request $request)
    {
		
        $magentoSettings = MagentoSetting::with(
            'storeview.websiteStore.website.storeWebsite', 
            'store.website.storeWebsite',
            'website');
        
          $magentoSettings->leftJoin('users','magento_settings.created_by','users.id');
          $magentoSettings->select('magento_settings.*','users.name as uname');
        if($request->scope){
            $magentoSettings->where('scope', $request->scope);
        }
		$pushLogs = MagentoSettingPushLog::leftJoin('store_websites', 'store_websites.id', '=', 'magento_setting_push_logs.store_website_id')
		->select('website', 'command', 'magento_setting_push_logs.created_at')->get();
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
			//$pushLogs->where('magento_setting_push_logs.store_website_id', $request->website);
        }
		
        if ($request->name!='')
        {
            $magentoSettings->where('magento_settings.name', $request->name); 
        }
        if ($request->path!='')
        {
            $magentoSettings->where('magento_settings.path', $request->path); 
        }
        $magentoSettings = $magentoSettings->orderBy('magento_settings.created_at', 'DESC')->paginate(25);    
        $storeWebsites = StoreWebsite::get();
        $websitesStores = WebsiteStore::get()->pluck('name')->unique()->toArray();
        $websiteStoreViews = WebsiteStoreView::get()->pluck('code')->unique()->toArray();
		
        if ($request->ajax()) {
            return view('magento.settings.index_ajax', [
                'magentoSettings' => $magentoSettings,
                'storeWebsites' => $storeWebsites,
                'websitesStores' => $websitesStores ,
                'websiteStoreViews' => $websiteStoreViews,
                'pushLogs' => $pushLogs,
            ]);
        }
        else
        {
        
        return view('magento.settings.index', [
            'magentoSettings' => $magentoSettings,
            'storeWebsites' => $storeWebsites,
            'websitesStores' => $websitesStores ,
            'websiteStoreViews' => $websiteStoreViews,
            'pushLogs' => $pushLogs,
        ]);
       }
    }

    public function create(Request $request)
    {
        $name         = $request->name;
        $path         = $request->path;
        $value        = $request->value;
        $copyWebsites = (!empty($request->websites)) ? $request->websites : array() ;

        foreach ($request->scope as $scope) {

            if ($scope === 'default') {
                
                $totalWebsites = array_merge($request->website, $copyWebsites);
                $storeWebsites = StoreWebsite::whereIn('id', $totalWebsites)->get();
                
                 foreach ($storeWebsites as $storeWebsite) {

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $storeWebsite->id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope'    => $scope,
                            'scope_id' => $storeWebsite->id,
                            'store_website_id' => $storeWebsite->id,
                            'website_store_id' => 0,
                            'website_store_view_id' => 0,
                            'name'     => $name,
                            'path'     => $path,
                            'value'    => $value,
                            'created_by'=>Auth::id(),
                        ]);
                    }
                }
            }
            
            if ($scope === 'websites') {

                $websiteStores = WebsiteStore::whereIn('id', $request->website_store)->get();
                $stores        = [];
                foreach ($websiteStores as $websiteStore) {
                    $stores[]  = $websiteStore->code;
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope'    => $scope,
                            'scope_id' => $websiteStore->id,
                            'store_website_id' => $request->single_website,
                            'website_store_id' => $websiteStore->id,
                            'website_store_view_id' => 0,
                            'name'     => $name,
                            'path'     => $path,
                            'value'    => $value,
                            'created_by'=>Auth::id(),
                        ]);
                    }
                }
                
                if (!empty($copyWebsites)) {
                    foreach ($copyWebsites as $cw) {
                        $websiteStores = WebsiteStore::join("websites as w","w.id","website_stores.website_id")->where("w.store_website_id",$cw)->whereIn('website_stores.code', $stores)->whereNotIn('website_stores.id', $request->website_store)->get();
                        foreach ($websiteStores as $websiteStore) {
                            $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
                            if (!$m_setting) {                                  
                                $m_setting = MagentoSetting::Create([
                                    'scope'    => $scope,
                                    'scope_id' => $websiteStore->id,
                                    'store_website_id' => $cw,
                                    'website_store_id' => $websiteStore->id,
                                    'website_store_view_id' => 0,
                                    'name'     => $name,
                                    'path'     => $path,
                                    'value'    => $value,
                                    'created_by'=>Auth::id(),
                                ]);
                            }                            
                        }
                    }
                }                
               
            }                

            if ($scope === 'stores') {

                $websiteStoresViews = WebsiteStoreView::whereIn('id', $request->website_store_view)->get();
                $stores        = [];
                foreach ($websiteStoresViews as $websiteStoresView) {
                    $stores[]  = $websiteStoresView->code;
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope'    => $scope,
                            'scope_id' => $websiteStoresView->id,
                            'store_website_id' => $request->single_website,
                            'website_store_id' => $websiteStoresView->website_store_id,
                            'website_store_view_id' => $websiteStoresView->id,
                            'name'     => $name,
                            'path'     => $path,
                            'value'    => $value,
                            'created_by'=>Auth::id(),
                        ]);
                    }
                }
                
                if (!empty($copyWebsites)) {
                    foreach ($copyWebsites as $cw) {
                        
                        //$websiteStoresViews = WebsiteStoreView::join("websites as w","w.id","website_store_views.website_store_id")->where("w.store_website_id",$cw)->whereIn('website_stores.code', $stores)->whereNotIn('website_store_id.id', $request->website_store_view)->get();
                        $websiteStoresViews = WebsiteStoreView::join("websites as w","w.id","website_store_views.website_store_id")->where("w.store_website_id",$cw)->whereIn('website_stores.code', $stores);
                        
                        foreach ($websiteStoresViews as $websiteStoresView) {
                            $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                            if (!$m_setting) {                                  
                                $m_setting = MagentoSetting::Create([
                                    'scope'    => $scope,
                                    'scope_id' => $websiteStoresView->id,
                                    'store_website_id' => $cw,
                                    'website_store_id' => $websiteStoresView->website_store_id,
                                    'website_store_view_id' => $websiteStoresView->id,
                                    'name'     => $name,
                                    'path'     => $path,
                                    'value'    => $value,
                                    'created_by'=>Auth::id(),
                                ]);
                            }                            
                        }
                    }
                }
                

            }
            
        }
        
        return response()->json(['status' => true]);

    }

    public function update(Request $request) {
        
        $entity_id = $request->id;
        $scope = $request->scope;
        $name = $request->name;
        $path = $request->path;
        $value = $request->value;
        $git_repository = $request->git_repository;
        $is_live = isset($request->live);
        $is_development = isset($request->development);
        $is_stage = isset($request->stage);
        $website_ids = $request->websites;

        $m = MagentoSetting::where('id', $request->id)->first();
        if($m) {
            MagentoSettingNameLog::insert([
                'old_value' => $m->name,
                'new_value' => $name,
                'updated_by' => Auth::id(),
                'magento_settings_id' => $request->id,
                'updated_at' => date('Y-m-d H:i')
            ]);
        }

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
                    $magento_url = str_replace('.com', '', $magento_url);
                    
                    //BASE SCRIPT
                    if(!empty($git_repository)):                        
                        $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-config-deployment.sh -r '.$git_repository.' -s '.$scope.' -c '.$scopeID.' -p '.$path.' -v '.$value;
                        $allOutput   = array();
                        $allOutput[] = $cmd;
                        $result      = exec($cmd, $allOutput); //Execute command   
                        \Log::info(print_r(["Command Output",$allOutput],true));
                    else:
                        return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                    endif;
                    
                }
            }
            
            return response()->json(["code" => 200 , "message" => "Request pushed on website successfully"]);
            
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
                    $magento_url = str_replace('.com', '', $magento_url);
                    
                    //BASE SCRIPT
                    if(!empty($git_repository)):                        
                        $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-config-deployment.sh -r '.$git_repository.' -s '.$scope.' -c '.$scopeID.' -p '.$path.' -v '.$value;
                        $allOutput   = array();
                        $allOutput[] = $cmd;
                        $result      = exec($cmd, $allOutput); //Execute command 
                        \Log::info(print_r(["Command Output",$allOutput],true));
                    else:
                        return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                    endif;
                   
                }

            }
            
            return response()->json(["code" => 200 , "message" => "Request pushed on website successfully"]);
            
        } else if($scope === 'stores'){

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
                    $magento_url = str_replace('.com', '', $magento_url);
                    
                    //BASE SCRIPT
                    if(!empty($git_repository)):                        
                        
						$cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-config-deployment.sh -r '.$git_repository.' -s '.$scope.' -c '.$scopeID.' -p '.$path.' -v '.$value;
                        $allOutput   = array();
                        $allOutput[] = $cmd;
                        $result      = exec($cmd, $allOutput); //Execute command  
                        \Log::info(print_r(["Command Output",$allOutput],true));
                    else:
                        return response()->json(["code" => 500 , "message" => "Request has been failed on stage server please check laravel log"]);
                    endif;
                    
                }

            }

            return response()->json(["code" => 200 , "message" => "Request pushed on website successfully"]);
            
        }
      
    }
	
	public function pushMagentoSettings(Request $request) {
		$store_website_id = $request->store_website_id;
		$magentoSettings = MagentoSetting::where('store_website_id', $store_website_id)->get();
		$settings = '';
		$storeWebsiteDetails = StoreWebsite::leftJoin('github_repositories', 'github_repositories.id', '=', 'store_websites.repository_id')
		->where('store_websites.id', $store_website_id)->select('github_repositories.name as repo_name')->first();
		
		foreach($magentoSettings as $magentoSetting) {
			if($magentoSetting['scope'] == 'default') {
				$scopeId = 0;
				$settings .= $magentoSetting['scope'].','.$scopeId.','.$magentoSetting['path'].','.$magentoSetting['value'].PHP_EOL;
			}else if($scope === 'websites'){
				$websiteStores = WebsiteStore::with('website.storeWebsite')->whereHas('website', function($q) use ( $store_website_id){
					$q->where('store_website_id', $store_website_id);
				})->get();
				foreach($websiteStores as $websiteStore){
					$scopeID = $websiteStore->platform_id;
					$settings .= $magentoSetting['scope'].','.$scopeId.','.$magentoSetting['path'].','.$magentoSetting['value'].PHP_EOL;
				}
			}else if($scope === 'stores'){
				$websiteStoresViews = WebsiteStoreView::with('websiteStore.website.storeWebsite')->whereHas('websiteStore.website', function($q) use ( $store_website_id){
					$q->whereIn('store_website_id', $store_website_id );
				})->get();

				foreach($websiteStoresViews as $websiteStoresView){
					  $scopeID = $websiteStoresView->platform_id;
					$settings .= $magentoSetting['scope'].','.$scopeId.','.$magentoSetting['path'].','.$magentoSetting['value'].PHP_EOL;
				}
			}
			
		}
		
		if($settings !='') {
			$filePath = public_path()."/uploads/temp-sync.txt";
			$myfile = fopen($filePath, "w") or die("Unable to open file!");
			fwrite($myfile, $settings);
			fclose($myfile);
			
			$cmd = 'bash ' . 'magento-config-deployment.sh -r '.$storeWebsiteDetails['repo_name'].' -f '.$filePath;
            $allOutput   = array();
            $allOutput[] = $cmd;
            $result      = exec($cmd, $allOutput); //Execute command  
            \Log::info(print_r(["Command Output",$allOutput],true));
			MagentoSettingPushLog::create(['store_website_id'=>$store_website_id,'command'=>$cmd]);
		} 
		return redirect(route('magento.setting.index'));
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

    public function  namehistrory($id){
         
       $ms= MagentoSettingNameLog::select('magento_setting_name_logs.*','users.name')->leftJoin('users','magento_setting_name_logs.updated_by','users.id')->where('magento_settings_id',$id)->get();
       $table="<table class='table table-bordered'> <thead><tr><th>Date</th><th>Old Value</old><th>New Value</th><th>Created By</th><tr><thead><tbody";
       foreach($ms as $m)
       {
           $table.="<tr><td>".$m->updated_at."</td>";
           $table.="<td>".$m->old_value."</td>";
           $table.="<td>".$m->new_value."</td>";
           $table.="<td>".$m->name."</td></tr>";
       }
       $table.="</tbody></table>";
       echo $table;


    }
    
}