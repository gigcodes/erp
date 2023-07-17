<?php

namespace App\Http\Controllers;

use App\Website;
use App\StoreWebsite;
use App\WebsiteStore;
use App\MagentoSetting;
use App\WebsiteStoreView;
use App\MagentoSettingLog;
use Illuminate\Http\Request;
use App\MagentoSettingNameLog;
use App\MagentoSettingPushLog;
use Illuminate\Support\Facades\Auth;
use App\AssetsManager;
use App\LogRequest;
use App\Jobs\PushMagentoSettings;
use App\MagentoSettingStatus;
use App\User;

class MagentoSettingsController extends Controller
{
    public function index(Request $request)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $magentoSettings = MagentoSetting::with(
            'storeview.websiteStore.website.storeWebsite',
            'store.website.storeWebsite',
            'website',
            'fromStoreId', 'fromStoreIdwebsite');

        $magentoSettings->leftJoin('users', 'magento_settings.created_by', 'users.id');
        $magentoSettings->select('magento_settings.*', 'users.name as uname');
        if ($request->scope) {
            $magentoSettings->where('scope', $request->scope);
        }
        $pushLogs = MagentoSettingPushLog::leftJoin('store_websites', 'store_websites.id', '=', 'magento_setting_push_logs.store_website_id')
            ->select('store_websites.website',  'magento_setting_push_logs.status', 'magento_setting_push_logs.command', 'magento_setting_push_logs.created_at')->orderBy('magento_setting_push_logs.created_at', 'DESC')->get();

        if (is_array($request->website)) {
            foreach ($request->website as $website) {
                if (empty($request->scope)) {
                    $magentoSettings->whereHas('storeview.websiteStore.website.storeWebsite', function ($q) use ($website) {
                        $q->where('id', $website);
                    })->orWhereHas('store.website.storeWebsite', function ($q) use ($website) {
                        $q->where('id', $website);
                    })->orWhereHas('website', function ($q) use ($website) {
                        $q->where('id', $website);
                    });
                } else {
                    if ($request->scope == 'default') {
                        $website_ids = StoreWebsite::where('id', $website)->get()->pluck('id')->toArray();
                        $magentoSettings->whereIn('scope_id', $website_ids ?? []);
                    } elseif ($request->scope == 'websites') {
                        $website_ids = Website::where('store_website_id', $website)->get()->pluck('id')->toArray();
                        $website_store_ids = WebsiteStore::whereIn('website_id', $website_ids ?? [])->get()->pluck('id')->toArray();
                        $magentoSettings->whereIn('scope_id', $website_store_ids ?? []);
                    } elseif ($request->scope == 'stores') {
                        $website_ids = Website::where('store_website_id', $website)->get()->pluck('id')->toArray();
                        $website_store_ids = WebsiteStore::whereIn('website_id', $website_ids ?? [])->get()->pluck('id')->toArray();
                        $website_store_view_ids = WebsiteStoreView::whereIn('website_store_id', $website_store_ids ?? [])->get()->pluck('id')->toArray();
                        $magentoSettings->whereIn('scope_id', $website_store_view_ids ?? []);
                    }
                }
                //$pushLogs->where('magento_setting_push_logs.store_website_id', $request->website);
            }
        }

        if ($request->name != '') {
            $magentoSettings->where('magento_settings.name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->path != '') {
            $magentoSettings->where('magento_settings.path', 'LIKE', '%' . $request->path . '%');
        }
        if ($request->status != '') {
            $magentoSettings->where('magento_settings.status', 'LIKE', '%' . $request->status . '%');
        }
        if ($request->user_name != null and $request->user_name != 'undefined') {
            $magentoSettings->whereIn('magento_settings.created_by', $request->user_name);
        }

        $magentoSettings = $magentoSettings->orderBy('magento_settings.id', 'DESC')->paginate(25);
        $storeWebsites = StoreWebsite::get();
        $websitesStores = WebsiteStore::get()->pluck('name')->unique()->toArray();
        $websiteStoreViews = WebsiteStoreView::get()->pluck('code')->unique()->toArray();
        $allUsers = User::where('is_active', '1')->get();
        $magentoSettingStatuses = MagentoSettingStatus::all();
        $data = $magentoSettings;
        $data = $data->groupBy('store_website_id')->toArray();
        $newValues = [];

        $countList = MagentoSetting::all();
        if (is_array($request->website) || $request->name || $request->path || $request->status || $request->scope) {
            $counter = $magentoSettings->count();
        } else {
            $counter = $countList->count();
        }
        //dd($magentoSettings);
        if ($request->ajax()) {
            return view('magento.settings.index_ajax', [
                'magentoSettings' => $magentoSettings,
                'newValues' => $newValues,
                'storeWebsites' => $storeWebsites,
                'websitesStores' => $websitesStores,
                'websiteStoreViews' => $websiteStoreViews,
                'pushLogs' => $pushLogs,
                'counter' => $counter,
                'allUsers' => $allUsers,
                'magentoSettingStatuses' => $magentoSettingStatuses,
            ]);
        } else {
            return view('magento.settings.index', [
                'magentoSettings' => $magentoSettings,
                'newValues' => $newValues,
                'storeWebsites' => $storeWebsites,
                'websitesStores' => $websitesStores,
                'websiteStoreViews' => $websiteStoreViews,
                'pushLogs' => $pushLogs,
                'counter' => $counter,
                'allUsers' => $allUsers,
                'magentoSettingStatuses' => $magentoSettingStatuses,
            ]);
        }
    }


    public function getLogs(Request $request){
        $storeWebsites = StoreWebsite::get();
        $magentoSettings = MagentoSetting::get();
        $pushLogs = MagentoSettingPushLog::leftJoin('store_websites', 'store_websites.id', '=', 'magento_setting_push_logs.store_website_id')
        ->select('store_websites.website','magento_setting_push_logs.id','magento_setting_push_logs.command_output', 'magento_setting_push_logs.status', 'magento_setting_push_logs.command', 'magento_setting_push_logs.created_at', 'magento_setting_push_logs.store_website_id', 'magento_setting_push_logs.command_server','magento_setting_push_logs.job_id','magento_setting_push_logs.setting_id')
        ->orderBy('magento_setting_push_logs.id', 'DESC');
        if($request->website){
            $pushLogs->where('store_website_id',$request->website);
        }
        if($request->date){
            $pushLogs->whereDate('magento_setting_push_logs.created_at',$request->date);
        }

        $counter = MagentoSettingPushLog::select('*');
        if($request->website){
            $counter->where('store_website_id',$request->website);
        }
        if($request->search_status){
            $pushLogs = $pushLogs->where('status',  $request->search_status);
        }
        if ($request->search_url) {
            $pushLogs = $pushLogs->where('command_server', 'LIKE', '%' . $request->search_url . '%');
        }
        if ($request->request_data) {
            $pushLogs = $pushLogs->where('command', 'LIKE', '%' . $request->request_data . '%');
        }
        if ($request->request_setting) {
            $pushLogs = $pushLogs->whereHas('setting', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->request_setting . '%');
            });
        }
        
        $pushLogs = $pushLogs->paginate(25)->withQueryString();

        $counter = $counter->count();

        return view('magento.settings.sync_logs', [
            'pushLogs' => $pushLogs,
            'storeWebsites' => $storeWebsites,
            'counter' => $counter,
            'magentoSettings' =>$magentoSettings
        ]);

    }

    public function magentoSyncLogSearch(Request $request)
    {
        $pushLogs = MagentoSettingPushLog::leftJoin('store_websites', 'store_websites.id', '=', 'magento_setting_push_logs.store_website_id')
        ->select('store_websites.website', 'magento_setting_push_logs.status', 'magento_setting_push_logs.command', 'magento_setting_push_logs.created_at');
        if ($request->sync_date != '') {
            $pushLogs = $pushLogs->whereDate('magento_setting_push_logs.created_at', date('Y-m-d', strtotime($request->sync_date)));
        }

        $pushLogs = $pushLogs->orderBy('magento_setting_push_logs.created_at', 'DESC')->get();
        if (! empty($pushLogs)) {
            return response()->json(['status' => 200, 'data' => $pushLogs, 'msg' => 'Data Listed successfully!']);
        } else {
            return response()->json(['status' => 500, 'data' => [], 'msg' => 'Could, not find data!']);
        }
    }

    public function create(Request $request)
    {
        $name = $request->name;
        $path = $request->path;
        $value = $request->value;
        $datatype = $request->datatype;
        $copyWebsites = (! empty($request->websites)) ? $request->websites : [];
        $save_record_status = 0;
        foreach ($request->scope as $scope) {
            if ($scope === 'default') {
                $totalWebsites = array_merge($request->website, $copyWebsites);
                $storeWebsites = StoreWebsite::whereIn('id', $totalWebsites)->get();

                foreach ($storeWebsites as $storeWebsite) {
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $storeWebsite->id)->where('path', $path)->first();
                    if (! $m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $storeWebsite->id,
                            'store_website_id' => $storeWebsite->id,
                            'website_store_id' => 0,
                            'website_store_view_id' => 0,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value,
                            'data_type' => $datatype,
                            'created_by' => Auth::id(),
                        ]);
                        $save_record_status = 1;
                    }
                }
            }

            if ($scope === 'websites') {
                $websiteStores = [];
                $stores = [];
                if ($request->website_store != null) {
                    $websiteStores = WebsiteStore::whereIn('id', $request->website_store)->get();
                }
                foreach ($websiteStores as $websiteStore) {
                    $stores[] = $websiteStore->code;
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
                    if (! $m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStore->id,
                            'store_website_id' => $request->single_website,
                            'website_store_id' => $websiteStore->id,
                            'website_store_view_id' => 0,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value,
                            'data_type' => $datatype,
                            'created_by' => Auth::id(),
                        ]);
                        $save_record_status = 1;
                    }
                }

                if (! empty($copyWebsites) && ! empty($stores)) {
                    foreach ($copyWebsites as $cw) {
                        $websiteStores = WebsiteStore::join('websites as w', 'w.id', 'website_stores.website_id')->where('w.store_website_id', $cw)->whereIn('website_stores.code', $stores)->whereNotIn('website_stores.id', $request->website_store)->get();
                        foreach ($websiteStores as $websiteStore) {
                            $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
                            if (! $m_setting) {
                                $m_setting = MagentoSetting::Create([
                                    'scope' => $scope,
                                    'scope_id' => $websiteStore->id,
                                    'store_website_id' => $cw,
                                    'website_store_id' => $websiteStore->id,
                                    'website_store_view_id' => 0,
                                    'name' => $name,
                                    'path' => $path,
                                    'value' => $value,
                                    'data_type' => $datatype,
                                    'created_by' => Auth::id(),
                                ]);
                                $save_record_status = 1;
                            }
                        }
                    }
                }
            }

            if ($scope === 'stores') {
                $websiteStoresViews = [];
                if ($request->website_store_view != null) {
                    $websiteStoresViews = WebsiteStoreView::whereIn('id', $request->website_store_view)->get();
                }
                //  $websiteStoresViews = WebsiteStoreView::whereIn('id', $request->website_store_view)->get();dd($websiteStoresViews);
                $stores = [];
                foreach ($websiteStoresViews as $websiteStoresView) {
                    $stores[] = $websiteStoresView->code;
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                    if (! $m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStoresView->id,
                            'store_website_id' => $request->single_website,
                            'website_store_id' => $websiteStoresView->website_store_id,
                            'website_store_view_id' => $websiteStoresView->id,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value,
                            'data_type' => $datatype,
                            'created_by' => Auth::id(),
                        ]);
                        $save_record_status = 1;
                    }
                }

                if (! empty($copyWebsites)) {
                    foreach ($copyWebsites as $cw) {
                        //$websiteStoresViews = WebsiteStoreView::join("websites as w","w.id","website_store_views.website_store_id")->where("w.store_website_id",$cw)->whereIn('website_stores.code', $stores)->whereNotIn('website_store_id.id', $request->website_store_view)->get();
                        $websiteStoresViews = WebsiteStoreView::join('websites as w', 'w.id', 'website_store_views.website_store_id')->where('w.store_website_id', $cw)->whereIn('website_stores.code', $stores);

                        foreach ($websiteStoresViews as $websiteStoresView) {
                            $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                            if (! $m_setting) {
                                $m_setting = MagentoSetting::Create([
                                    'scope' => $scope,
                                    'scope_id' => $websiteStoresView->id,
                                    'store_website_id' => $cw,
                                    'website_store_id' => $websiteStoresView->website_store_id,
                                    'website_store_view_id' => $websiteStoresView->id,
                                    'name' => $name,
                                    'path' => $path,
                                    'value' => $value,
                                    'data_type' => $datatype,
                                    'created_by' => Auth::id(),
                                ]);
                                $save_record_status = 1;
                            }
                        }
                    }
                }
            }
        }

        $return = [];
        if ($save_record_status == 1) {
            $return = ['code' => 200, 'message' => 'Magento setting has been created.'];
        } else {
            $return = ['code' => 500, 'message' => 'Magento setting has not been created.'];
        }

        return response()->json($return);
    }

    public function update(Request $request)
    {
        $entity_id = $request->id;
        $scope = $request->scope;
        $name = $request->name;
        $path = $request->path;
        $value = $request->value;
        $datatype = $request->datatype;
        $is_live = isset($request->live);
        $is_development = isset($request->development);
        $is_stage = isset($request->stage);
        $website_ids = $request->websites;
        
        $m = MagentoSetting::where('id', $request->id)->first();
        if ($m) {
            MagentoSettingNameLog::insert([
                'old_value' => $m->name,
                'new_value' => $name,
                'updated_by' => Auth::id(),
                'magento_settings_id' => $request->id,
                'updated_at' => date('Y-m-d H:i'),
            ]);
        }

        MagentoSetting::where('id', $request->id)->update([
            'name' => $name,
            'path' => $path,
            'value' => $value,
        ]);

        $entity = MagentoSetting::find($entity_id);

        // // #DEVTASK-23677-api implement for admin settings
        // \Log::info("Setting Scope : ".$scope);
        // // Scope Default
        // if ($scope === 'default') {
        //     $storeWebsites = StoreWebsite::whereIn('id', $website_ids ?? [])->get();
        //     foreach ($storeWebsites as $storeWebsite) {
        //         $store_website_id=$storeWebsite->id;
        //         \Log::info("Start Setting Pushed to : ".$store_website_id);
        //         $api_token=$storeWebsite->api_token;
        //         $magento_url=$storeWebsite->magento_url;

        //         $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $store_website_id)->where('path', $path)->first();
        //         if (! $m_setting) {
        //             $m_setting = MagentoSetting::Create([
        //                 'scope' => $scope,
        //                 'scope_id' => $store_website_id,
        //                 'name' => $name,
        //                 'path' => $path,
        //                 'value' => $value,
        //                 'data_type' => $datatype,
        //             ]);
        //         } else {
        //             $m_setting->name = $name;
        //             $m_setting->path = $path;
        //             $m_setting->value = $value;
        //             $m_setting->data_type = $datatype;
        //             $m_setting->save();
        //         }

        //         $startTime  = date("Y-m-d H:i:s", LARAVEL_START);
        //         $url=rtrim($magento_url, '/') ."/rest/all/V1/store-info/configuration";
        //         $data=[];
        //         $data['scopeId']=0;
        //         $data['scopeType']="default";
        //         $data['configs'][]=['path'=>$path,'value'=>$value];
                
        //         $ch = curl_init($url);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        //         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $api_token));
        //         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        //         $result = curl_exec($ch);
        //         $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //         \Log::info(print_r([json_encode($data), $url, $result], true));
                
        //         LogRequest::log($startTime, $url, 'POST', json_encode($data),json_decode($result),$httpcode,\App\Http\Controllers\MagentoSettingsController::class, 'update');

        //         if (curl_errno($ch)) {
        //             \Log::info("API Error: ".curl_error($ch));
        //             MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => son_encode($data), 'setting_id' => $m_setting->id, 'command_output' =>curl_error($ch), 'status' => 'Error','command_server'=>$url,'job_id'=>$httpcode ]);
        //         }

        //         $response = json_decode($result);
        //         curl_close($ch);
        //         if($httpcode=='200'){
        //             $m_setting->status ='Success';
        //             $m_setting->value_on_magento =$value;
        //             $m_setting->save();
        //             MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $m_setting->id, 'command_output' =>'Success', 'status' => 'Success','command_server'=>$url,'job_id'=>$httpcode ]);
                    
        //         }else{
        //             $m_setting->status ='Error';
        //             $m_setting->save();
        //             MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $m_setting->id, 'command_output' =>$result, 'status' => 'Error','command_server'=>$url,'job_id'=>$httpcode ]);
        //         }
        //         \Log::info("End Setting Pushed to : ".$store_website_id);
        //     }
        //     return response()->json(['code' => 200, 'message' => 'Request pushed on selected website successfully. Please check logs for more details']);
        // }
        // // Scope Default
        // if ($scope === 'websites') {
        //     $store = $request->store;
        //     $website = $request->website;
            
        //     $websiteStores = WebsiteStore::with('website.storeWebsite')->whereHas('website', function ($q) use ($store, $website_ids) {
        //         $q->whereIn('store_website_id', $website_ids ?? [])->where('name', $store);
        //     })->orWhere('id', $entity->scope_id)->get();

        //     foreach ($websiteStores as $websiteStore) {
        //         $store_website_id = isset($websiteStore->website->storeWebsite->id) ? $websiteStore->website->storeWebsite->id : 0;

        //         \Log::info("Start Setting Pushed to : ".$store_website_id);
        //         \Log::info("Website Store : ".$websiteStore->id);

        //         $magento_url = isset($websiteStore->website->storeWebsite->magento_url) ? $websiteStore->website->storeWebsite->magento_url : null;
        //         $api_token = isset($websiteStore->website->storeWebsite->api_token) ? $websiteStore->website->storeWebsite->api_token : null;

        //         $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
        //         if (! $m_setting) {
        //             $m_setting = MagentoSetting::Create([
        //                 'scope' => $scope,
        //                 'scope_id' => $websiteStore->id,
        //                 'name' => $request->name,
        //                 'path' => $request->path,
        //                 'value' => $request->value,
        //                 'data_type' => $datatype,
        //             ]);
        //         } else {
        //             $m_setting->name = $name;
        //             $m_setting->path = $path;
        //             $m_setting->value = $value;
        //             $m_setting->data_type = $datatype;
        //             $m_setting->save();
        //         }
        //         $scopeID = $websiteStore->platform_id;
        //         if (! empty($magento_url) && !empty($api_token)) {
        //             $startTime  = date("Y-m-d H:i:s", LARAVEL_START);
        //             $url=rtrim($magento_url, '/') ."/rest/all/V1/store-info/configuration";
        //             $data=[];
        //             $data['scopeId']=$scopeID;
        //             $data['scopeType']="websites";
        //             $data['configs'][]=['path'=>$path,'value'=>$value];

        //             $ch = curl_init($url);
        //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        //             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $api_token));
        //             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        //             $result = curl_exec($ch);
        //             $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //             \Log::info(print_r([json_encode($data), $url, $result], true));
                    
        //             LogRequest::log($startTime, $url, 'POST', json_encode($data),json_decode($result),$httpcode,\App\Http\Controllers\MagentoSettingsController::class, 'update');

        //             if (curl_errno($ch)) {
        //                 \Log::info("API Error: ".curl_error($ch));
        //                 MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>curl_error($ch), 'status' => 'Error','command_server'=>$url ,'job_id'=>$httpcode]);
        //             }

        //             $response = json_decode($result);
        //             curl_close($ch);
        //             if($httpcode=='200'){
        //                 $m_setting->status ='Success';
        //                 $m_setting->value_on_magento =$value;
        //                 $m_setting->save();
        //                 MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>'Success', 'status' => 'Success','command_server'=>$url ,'job_id'=>$httpcode]);
                        
        //             }else{
        //                 $m_setting->status ='Error';
        //                 $m_setting->save();
        //                 MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>$result, 'status' => 'Error','command_server'=>$url,'job_id'=>$httpcode ]);
        //             }

        //         }else{
        //             $m_setting->status ='Error';
        //             $m_setting->save();
        //             MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => '', 'setting_id' => $m_setting->id, 'command_output' =>'Magento URL & API Token is not found', 'status' => 'Error','job_id'=>'500']);
        //         }
        //         \Log::info("End Setting Pushed to : ".$store_website_id);

        //     }
        //     return response()->json(['code' => 200, 'message' => 'Request pushed on selected website successfully. Please check logs for more details']);
        // }
        // // Scope Default
        // if ($scope === 'stores') {
        //     $store = $request->store;
        //     $store_view = $request->store_view;
        //     $websiteStoresViews = WebsiteStoreView::with('websiteStore.website.storeWebsite')->whereHas('websiteStore.website', function ($q) use ($store, $website_ids) {
        //         $q->where('name', $store)->whereIn('store_website_id', $website_ids ?? []);
        //     })->where('code', $store_view)->orWhere('id', $entity->scope_id)->get();

        //     foreach ($websiteStoresViews as $websiteStoresView) {
        //         $store_website_id = isset($websiteStoresView->websiteStore->website->storeWebsite->id) ? $websiteStoresView->websiteStore->website->storeWebsite->id : 0;

        //         \Log::info("Start Setting Pushed to : ".$store_website_id);
        //         \Log::info("Website Store View : ".$websiteStoresView->id);

        //         $magento_url = isset($websiteStoresView->websiteStore->website->storeWebsite->magento_url) ? $websiteStoresView->websiteStore->website->storeWebsite->magento_url : null;
        //         $api_token = isset($websiteStoresView->websiteStore->website->storeWebsite->api_token) ? $websiteStoresView->websiteStore->website->storeWebsite->api_token : null;

        //         $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
        //         if (! $m_setting) {
        //             $m_setting = MagentoSetting::Create([
        //                 'scope' => $scope,
        //                 'scope_id' => $websiteStoresView->id,
        //                 'name' => $request->name,
        //                 'path' => $request->path,
        //                 'value' => $request->value,
        //                 'data_type' => $datatype,
        //             ]);
        //         } else {
        //             $m_setting->name = $name;
        //             $m_setting->path = $path;
        //             $m_setting->value = $value;
        //             $m_setting->data_type = $datatype;
        //             $m_setting->save();
        //         }
        //         $scopeID = $websiteStoresView->platform_id;
        //         if (! empty($magento_url) && !empty($api_token)) {
        //             $startTime  = date("Y-m-d H:i:s", LARAVEL_START);
        //             $url=rtrim($magento_url, '/') ."/rest/all/V1/store-info/configuration";
        //             $data=[];
        //             $data['scopeId']=$scopeID;
        //             $data['scopeType']="stores";
        //             $data['configs'][]=['path'=>$path,'value'=>$value];

        //             $ch = curl_init($url);
        //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        //             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $api_token));
        //             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        //             $result = curl_exec($ch);
        //             $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //             \Log::info(print_r([json_encode($data), $url, $result], true));
                    
        //             LogRequest::log($startTime, $url, 'POST', json_encode($data),json_decode($result),$httpcode,\App\Http\Controllers\MagentoSettingsController::class, 'update');

        //             if (curl_errno($ch)) {
        //                 \Log::info("API Error: ".curl_error($ch));
        //                 MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>curl_error($ch), 'status' => 'Error','command_server'=>$url,'job_id'=>$httpcode ]);
        //             }

        //             $response = json_decode($result);
        //             curl_close($ch);
        //             if($httpcode=='200'){
        //                 $m_setting->status ='Success';
        //                 $m_setting->value_on_magento =$value;
        //                 $m_setting->save();
        //                 MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>'Success', 'status' => 'Success','command_server'=>$url ,'job_id'=>$httpcode]);
                        
        //             }else{
        //                 $m_setting->status ='Error';
        //                 $m_setting->save();
        //                 MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>$result, 'status' => 'Error','command_server'=>$url ,'job_id'=>$httpcode]);
        //             }

        //         }else{
        //             $m_setting->status ='Error';
        //             $m_setting->save();
        //             MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => '', 'setting_id' => $m_setting->id, 'command_output' =>'Magento URL & API Token is not found', 'status' => 'Error','job_id'=>'500']);
        //         }
        //         \Log::info("End Setting Pushed to : ".$store_website_id);
        //     }
        //     return response()->json(['code' => 200, 'message' => 'Request pushed on selected website successfully. Please check logs for more details']);
        // }
        // // #DEVTASK-23677-api implement for admin settings

        // #DEVTASK-23690-Magento Admin Settings - The above logic was not need, So I hide that. 
        return response()->json(['code' => 200, 'message' => 'Updated successfully !']);
    }

    public function pushMagentoSettings(Request $request)
    {
        if($request->has('store_website_id') && $request->store_website_id!=''){

            $store_website_id = $request->store_website_id;
            $magentoSettings = MagentoSetting::where('store_website_id', $store_website_id)->get();
            $website_ids[]=$store_website_id;
            foreach ($magentoSettings as $magentoSetting) {
                \App\Jobs\PushMagentoSettings::dispatch($magentoSetting,$website_ids)->onQueue('pushmagentosettings');
            }

            return redirect(route('magento.setting.index'))->with('success', 'Successfully pushed Magento settings to the store website');
        }
        return redirect(route('magento.setting.index'))->with('error', 'Please select the store website!');
    }

    public function websiteStores(Request $request)
    {
        $website_ids = Website::where('store_website_id', $request->website_id)->get()->pluck('id')->toArray();

        return response()->json([
            'data' => WebsiteStore::select('id', 'name')->whereNotNull('name')->whereIn('website_id', $website_ids)->get(),
        ]);
    }

    public function websiteStoreViews(Request $request)
    {
        $website_store_ids = $request->website_id;
        $website_store_view_data = [];
        if (! empty($website_store_ids)) {
            $website_store_view_data = WebsiteStoreView::select('id', 'code')->whereNotNull('code')->whereIn('website_store_id', $website_store_ids)->get();
        }

        return response()->json([
            'data' => $website_store_view_data,
        ]);
    }

    public function deleteSetting($id)
    {
        $m_setting = MagentoSetting::find($id);
        if ($m_setting) {
            $m_setting->delete();
            $log = $id . ' Id Deleted successfully';
            $formData = ['event' => 'delete', 'log' => $log];
            MagentoSettingLog::create($formData);
        }

        return redirect()->route('magento.setting.index');
    }

    public function namehistrory($id)
    {
        $ms = MagentoSettingNameLog::select('magento_setting_name_logs.*', 'users.name')->leftJoin('users', 'magento_setting_name_logs.updated_by', 'users.id')->where('magento_settings_id', $id)->get();
        $table = "<table class='table table-bordered text-nowrap' style='border: 1px solid #ddd;'><thead><tr><th>Date</th><th>Old Value</th><th>New Value</th><th>Created By</th></tr></thead><tbody>";
        foreach ($ms as $m) {
            $table .= '<tr><td>' . $m->updated_at . '</td>';
            $table .= '<td>' . $m->old_value . '</td>';
            $table .= '<td>' . $m->new_value . '</td>';
            $table .= '<td>' . $m->name . '</td></tr>';
        }
        $table .= '</tbody></table>';
        echo $table;
    }

    public function magentoPushLogs($settingId)
    {
        $logs = MagentoSettingPushLog::where('setting_id', $settingId)->orderBy('id','desc')->get();
        $data = '';
        foreach ($logs as $log) {
            
            $data .= '<tr><td>' . $log['created_at'] . '</td><td style="overflow-wrap: anywhere;">' . $log['command_server'] . '</td><td style="overflow-wrap: anywhere;">' . $log['command'] . '</td><td style="overflow-wrap: anywhere;">' . $log['command_output'] . '</td><td>'. $log['job_id'].'</td><td>'. $log['status'].'</td></tr>';
        }
        echo $data;
    }

    public function getAllStoreWebsites($id)
    {
        $storeWebsites = StoreWebsite::where('parent_id', '=', $id)->get();

        return response()->json($storeWebsites);
    }

    public function getMagentoSetting($id)
    {
        $magentoSetting = MagentoSetting::where('id', $id)->first();
        $taggedStoreWebsites = '';

        if ($magentoSetting) {
            if ($magentoSetting->store_website_id) {
                $storeWebsite = StoreWebsite::find($magentoSetting->store_website_id);
                if ($storeWebsite->parent_id) {
                    $taggedStoreWebsites = StoreWebsite::where('parent_id', '=', $storeWebsite->parent_id)->orWhere('id', $storeWebsite->parent_id)->get();
                    return response()->json(['code' => 200, 'taggedWebsites' => $taggedStoreWebsites]);
                } else {
                    $taggedStoreWebsites = StoreWebsite::where('parent_id', '=', $storeWebsite->id)->orWhere('id', $storeWebsite->id)->get();
                    return response()->json(['code' => 200, 'taggedWebsites' => $taggedStoreWebsites]);
                }
            }

            return response()->json(['code' => 500, 'error' => 'No data found']);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function pushRowMagentoSettings(Request $request)
    {
        if($request->has('tagged_websites') && $request->has('row_id')){
            
            // Find individual setting 
            $individualSetting = MagentoSetting::with(
                'storeview.websiteStore.website.storeWebsite',
                'store.website.storeWebsite',
                'website',
                'fromStoreId', 'fromStoreIdwebsite')->find($request->row_id);

            // Assign new value when push
            if ($request->has('new_value')) {
                $individualSetting->value = $request->new_value;
                $individualSetting->save();
            }
            
            // Push individual setting to selected websites
            \App\Jobs\PushMagentoSettings::dispatch($individualSetting, $request->tagged_websites)->onQueue('pushmagentosettings');
           
            return redirect(route('magento.setting.index'))->with('success', 'Successfully pushed Magento settings to the store website');
        }
        return redirect(route('magento.setting.index'))->with('error', 'Please select the store website!');
    }

    public function statusColor(Request $request)
    {
        $statusColor = $request->all();
        $data = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $magentoSettingStatus = MagentoSettingStatus::find($key);
            $magentoSettingStatus->color = $value;
            $magentoSettingStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function assignSetting(Request $request)
    {
        $storeWebsite = StoreWebsite::find($request->store_website_id);
        if ($storeWebsite->parent_id) {
            $allInstances = StoreWebsite::where('parent_id', '=', $storeWebsite->parent_id)->orWhere('id', $storeWebsite->parent_id)->get();
        } else {
            $allInstances = StoreWebsite::where('parent_id', '=', $storeWebsite->id)->orWhere('id', $storeWebsite->id)->get();
        }

        $allInstancesIds = $allInstances->pluck("id");
        // Find all the Magento settings for these instances & assing it to the selected user
        $allMagentoSettings = MagentoSetting::whereIn('store_website_id', $allInstancesIds)->get();
        if($allMagentoSettings) {
            $user = User::find($request->assign_user);
            $user->magentoSettings()->sync($allMagentoSettings);
        }

        return redirect()->back()->with('success', 'Assigned successfully.');
    }

    public function assignIndividualSetting(Request $request)
    {
        $magentoSetting = MagentoSetting::where('id', $request->row_id)->first();
        $allInstances = '';

        if ($magentoSetting) {
            if ($magentoSetting->store_website_id) {
                $storeWebsite = StoreWebsite::find($magentoSetting->store_website_id);
                if ($storeWebsite->parent_id) {
                    $allInstances = StoreWebsite::where('parent_id', '=', $storeWebsite->parent_id)->orWhere('id', $storeWebsite->parent_id)->get();
                } else {
                    $allInstances = StoreWebsite::where('parent_id', '=', $storeWebsite->id)->orWhere('id', $storeWebsite->id)->get();
                }

                $allInstancesIds = $allInstances->pluck("id");
                // Find all the Magento settings for these instances & assing it to the selected user
                $allMagentoSettings = MagentoSetting::whereIn('store_website_id', $allInstancesIds)->get();
                if($allMagentoSettings) {
                    $user = User::find($request->assign_user);
                    $user->magentoSettings()->sync($allMagentoSettings);
                }

                return redirect()->back()->with('success', 'Assigned successfully.');
            }

            return redirect()->back()->with('error', 'Not Assigned, No store website found for this selected row');

        }

        return redirect()->back()->with('error', 'Not Assigned, MagentoSetting not found');
    }
}
