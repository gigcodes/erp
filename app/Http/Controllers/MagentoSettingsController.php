<?php

namespace App\Http\Controllers;

use App\MagentoSetting;
use App\MagentoSettingLog;
use App\MagentoSettingNameLog;
use App\MagentoSettingPushLog;
use App\StoreWebsite;
use App\Website;
use App\WebsiteStore;
use App\WebsiteStoreView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\LogRequest;

class MagentoSettingsController extends Controller
{

    public function index(Request $request)
    {
        $startTime  = date("Y-m-d H:i:s", LARAVEL_START);

        $magentoSettings = MagentoSetting::with(
            'storeview.websiteStore.website.storeWebsite',
            'store.website.storeWebsite',
            'website');

        $magentoSettings->leftJoin('users', 'magento_settings.created_by', 'users.id');
        $magentoSettings->select('magento_settings.*', 'users.name as uname');
        if ($request->scope) {
            $magentoSettings->where('scope', $request->scope);
        }
        $pushLogs = MagentoSettingPushLog::leftJoin('store_websites', 'store_websites.id', '=', 'magento_setting_push_logs.store_website_id')
            ->select('store_websites.website', 'magento_setting_push_logs.status', 'magento_setting_push_logs.command', 'magento_setting_push_logs.created_at')->orderBy('magento_setting_push_logs.created_at', 'DESC')->get();
        if ($request->website) {

            if (empty($request->scope)) {
                $magentoSettings->whereHas('storeview.websiteStore.website.storeWebsite', function ($q) use ($request) {
                    $q->where('id', $request->website);
                })->orWhereHas('store.website.storeWebsite', function ($q) use ($request) {
                    $q->where('id', $request->website);
                })->orWhereHas('website', function ($q) use ($request) {
                    $q->where('id', $request->website);
                });
            } else {
                if ($request->scope == 'default') {
                    $website_ids = StoreWebsite::where('id', $request->website)->get()->pluck('id')->toArray();
                    $magentoSettings->whereIn('scope_id', $website_ids ?? []);
                } else if ($request->scope == 'websites') {
                    $website_ids       = Website::where('store_website_id', $request->website)->get()->pluck('id')->toArray();
                    $website_store_ids = WebsiteStore::whereIn('website_id', $website_ids ?? [])->get()->pluck('id')->toArray();
                    $magentoSettings->whereIn('scope_id', $website_store_ids ?? []);
                } else if ($request->scope == 'stores') {
                    $website_ids            = Website::where('store_website_id', $request->website)->get()->pluck('id')->toArray();
                    $website_store_ids      = WebsiteStore::whereIn('website_id', $website_ids ?? [])->get()->pluck('id')->toArray();
                    $website_store_view_ids = WebsiteStoreView::whereIn('website_store_id', $website_store_ids ?? [])->get()->pluck('id')->toArray();
                    $magentoSettings->whereIn('scope_id', $website_store_view_ids ?? []);
                }
            }
            //$pushLogs->where('magento_setting_push_logs.store_website_id', $request->website);
        }

        if ($request->name != '') {
            $magentoSettings->where('magento_settings.name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->path != '') {
            $magentoSettings->where('magento_settings.path', 'LIKE', '%' . $request->path . '%');
        }
        $magentoSettings   = $magentoSettings->orderBy('magento_settings.created_at', 'DESC')->paginate(25);
        $storeWebsites     = StoreWebsite::get();
        $websitesStores    = WebsiteStore::get()->pluck('name')->unique()->toArray();
        $websiteStoreViews = WebsiteStoreView::get()->pluck('code')->unique()->toArray();
        $data              = $magentoSettings;
        $data              = $data->groupBy('store_website_id')->toArray();
        $newValues         = [];
        foreach ($data as $websiteId => $settings) {
            $websiteUrl = StoreWebsite::where('id', $websiteId)->pluck('magento_url')->first();
            if ($websiteUrl != null and $websiteUrl != '') {
                $bits = parse_url($websiteUrl);
                if (isset($bits['host'])) {
                    $web = $bits['host'];
                    if (!str_contains($websiteUrl, 'www')) {
                        $web = 'www.' . $bits['host'];
                    }
                    $websiteUrl   = 'https://' . $web;
                    $conf['data'] = [];
                    foreach ($settings as $setting) {
                        $conf['data'][] = ['path' => $setting['path'], 'scope' => $setting['scope'], 'scope_id' => $setting['scope_id']];
                    }
                    $curl = curl_init();
                    // Set cURL options
                    curl_setopt_array($curl, array(
                        CURLOPT_URL            => $websiteUrl . "/rest/V1/configvalue/get",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING       => "",
                        CURLOPT_MAXREDIRS      => 10,
                        CURLOPT_TIMEOUT        => 300,
                        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST  => "POST",
                        CURLOPT_POSTFIELDS     => json_encode($conf),
                        CURLOPT_HTTPHEADER     => array(
                            "content-type: application/json",
                        ),
                    ));

                    // Get response
                    $response = curl_exec($curl);

                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    LogRequest::log($startTime,$websiteUrl . "/rest/V1/configvalue/get",'POST',json_encode($conf),json_decode($response),$httpcode,'index','App\Http\Controllers\MagentoSettingsController');


                    $response = json_decode($response, true);


                    foreach ($settings as $key => $setting) {
                        $newValues[$setting['id']] = isset($response[$key]) ? $response[$key]['value'] : null;
                    }
                    curl_close($curl);
                }
            }
        }

        if ($request->ajax()) {
            return view('magento.settings.index_ajax', [
                'magentoSettings'   => $magentoSettings,
                'newValues'         => $newValues,
                'storeWebsites'     => $storeWebsites,
                'websitesStores'    => $websitesStores,
                'websiteStoreViews' => $websiteStoreViews,
                'pushLogs'          => $pushLogs,
            ]);
        } else {

            return view('magento.settings.index', [
                'magentoSettings'   => $magentoSettings,
                'newValues'         => $newValues,
                'storeWebsites'     => $storeWebsites,
                'websitesStores'    => $websitesStores,
                'websiteStoreViews' => $websiteStoreViews,
                'pushLogs'          => $pushLogs,
            ]);
        }
    }
    public function magentoSyncLogSearch(Request $request) 
    {
        $pushLogs = MagentoSettingPushLog::leftJoin('store_websites', 'store_websites.id', '=', 'magento_setting_push_logs.store_website_id')
        ->select('store_websites.website', 'magento_setting_push_logs.status', 'magento_setting_push_logs.command', 'magento_setting_push_logs.created_at');
        if($request->sync_date !='')
            $pushLogs = $pushLogs->whereDate('magento_setting_push_logs.created_at', date('Y-m-d',strtotime($request->sync_date)));
        
        $pushLogs = $pushLogs->orderBy('magento_setting_push_logs.created_at', 'DESC')->get();
        if(!empty($pushLogs))
            return response()->json(['status' => 200, 'data' => $pushLogs, 'msg' => "Data Listed successfully!"]);
        else
            return response()->json(['status' => 500, 'data' => [], 'msg' => "Could, not find data!"]);
    }

    public function create(Request $request)
    {
        $name         = $request->name;
        $path         = $request->path;
        $value        = $request->value;
        $datatype     = $request->datatype;
        $copyWebsites = (!empty($request->websites)) ? $request->websites : array();

        foreach ($request->scope as $scope) {

            if ($scope === 'default') {

                $totalWebsites = array_merge($request->website, $copyWebsites);
                $storeWebsites = StoreWebsite::whereIn('id', $totalWebsites)->get();

                foreach ($storeWebsites as $storeWebsite) {

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $storeWebsite->id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope'                 => $scope,
                            'scope_id'              => $storeWebsite->id,
                            'store_website_id'      => $storeWebsite->id,
                            'website_store_id'      => 0,
                            'website_store_view_id' => 0,
                            'name'                  => $name,
                            'path'                  => $path,
                            'value'                 => $value,
                            'data_type'             => $datatype,
                            'created_by'            => Auth::id(),
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
                            'scope'                 => $scope,
                            'scope_id'              => $websiteStore->id,
                            'store_website_id'      => $request->single_website,
                            'website_store_id'      => $websiteStore->id,
                            'website_store_view_id' => 0,
                            'name'                  => $name,
                            'path'                  => $path,
                            'value'                 => $value,
                            'data_type'             => $datatype,
                            'created_by'            => Auth::id(),
                        ]);

                    }
                }

                if (!empty($copyWebsites)) {
                    foreach ($copyWebsites as $cw) {
                        $websiteStores = WebsiteStore::join("websites as w", "w.id", "website_stores.website_id")->where("w.store_website_id", $cw)->whereIn('website_stores.code', $stores)->whereNotIn('website_stores.id', $request->website_store)->get();
                        foreach ($websiteStores as $websiteStore) {
                            $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
                            if (!$m_setting) {
                                $m_setting = MagentoSetting::Create([
                                    'scope'                 => $scope,
                                    'scope_id'              => $websiteStore->id,
                                    'store_website_id'      => $cw,
                                    'website_store_id'      => $websiteStore->id,
                                    'website_store_view_id' => 0,
                                    'name'                  => $name,
                                    'path'                  => $path,
                                    'value'                 => $value,
                                    'data_type'             => $datatype,
                                    'created_by'            => Auth::id(),
                                ]);
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
                    $stores[]  = $websiteStoresView->code;
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope'                 => $scope,
                            'scope_id'              => $websiteStoresView->id,
                            'store_website_id'      => $request->single_website,
                            'website_store_id'      => $websiteStoresView->website_store_id,
                            'website_store_view_id' => $websiteStoresView->id,
                            'name'                  => $name,
                            'path'                  => $path,
                            'value'                 => $value,
                            'data_type'             => $datatype,
                            'created_by'            => Auth::id(),
                        ]);
                    }
                }

                if (!empty($copyWebsites)) {
                    foreach ($copyWebsites as $cw) {

                        //$websiteStoresViews = WebsiteStoreView::join("websites as w","w.id","website_store_views.website_store_id")->where("w.store_website_id",$cw)->whereIn('website_stores.code', $stores)->whereNotIn('website_store_id.id', $request->website_store_view)->get();
                        $websiteStoresViews = WebsiteStoreView::join("websites as w", "w.id", "website_store_views.website_store_id")->where("w.store_website_id", $cw)->whereIn('website_stores.code', $stores);

                        foreach ($websiteStoresViews as $websiteStoresView) {
                            $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                            if (!$m_setting) {
                                $m_setting = MagentoSetting::Create([
                                    'scope'                 => $scope,
                                    'scope_id'              => $websiteStoresView->id,
                                    'store_website_id'      => $cw,
                                    'website_store_id'      => $websiteStoresView->website_store_id,
                                    'website_store_view_id' => $websiteStoresView->id,
                                    'name'                  => $name,
                                    'path'                  => $path,
                                    'value'                 => $value,
                                    'data_type'             => $datatype,
                                    'created_by'            => Auth::id(),
                                ]);
                            }
                        }
                    }
                }

            }

        }

        return response()->json(['status' => true]);

    }

    public function update(Request $request)
    {

        $entity_id      = $request->id;
        $scope          = $request->scope;
        $name           = $request->name;
        $path           = $request->path;
        $value          = $request->value;
        $datatype       = $request->datatype;
        $is_live        = isset($request->live);
        $is_development = isset($request->development);
        $is_stage       = isset($request->stage);
        $website_ids    = $request->websites;

        $m = MagentoSetting::where('id', $request->id)->first();
        if ($m) {
            MagentoSettingNameLog::insert([
                'old_value'           => $m->name,
                'new_value'           => $name,
                'updated_by'          => Auth::id(),
                'magento_settings_id' => $request->id,
                'updated_at'          => date('Y-m-d H:i'),
            ]);
        }

        MagentoSetting::where('id', $request->id)->update([
            'name'  => $name,
            'path'  => $path,
            'value' => $value,
        ]);

        $entity = MagentoSetting::find($entity_id);

        if ($scope === 'default') {

            $storeWebsites = StoreWebsite::whereIn('id', $website_ids ?? [])->orWhere('website', $request->website)->get();

            foreach ($storeWebsites as $storeWebsite) {
                $git_repository = $storeWebsite->repository;
                $magento_url    = $storeWebsite->magento_url;
                $server_name    = config('database.connections.' . $git_repository . '.host');
                if ($magento_url != null) {
                    $magento_url = explode('//', $magento_url);
                    $magento_url = isset($magento_url[1]) ? $magento_url[1] : $storeWebsite->magento_url;
                    $m_setting   = MagentoSetting::where('scope', $scope)->where('scope_id', $storeWebsite->id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope'     => $scope,
                            'scope_id'  => $storeWebsite->id,
                            'name'      => $name,
                            'path'      => $path,
                            'value'     => $value,
                            'data_type' => $datatype,
                        ]);
                    } else {
                        $m_setting->name      = $name;
                        $m_setting->path      = $path;
                        $m_setting->value     = $value;
                        $m_setting->data_type = $datatype;
                        $m_setting->save();
                    }
                    $scopeID     = 0;
                    $magento_url = str_replace('www.', '', $magento_url);
                    $magento_url = str_replace('.com', '', $magento_url);

                    //BASE SCRIPT
                    if (!empty($git_repository)):
                        $cmd         = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-config-deployment.sh -r ' . $git_repository . ' -s ' . $scope . ' -c ' . $scopeID . ' -p ' . $path . " -v  '" . $value . "' -t " . $datatype . ' -h ' . $server_name;
                        $allOutput   = array();
                        $allOutput[] = $cmd;
                        $result      = exec($cmd, $allOutput); //Execute command
                        $status      = 'Error';
                        for ($i = 0; $i < count($allOutput); $i++) {
                            if (strtolower($allOutput[$i]) == strtolower("Pull Request Successfully merged") ) {
                                $status = 'Success';
                                break;
                            }
                        }
                        $m_setting->status = $status;
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $storeWebsite['id'], 'command' => $cmd, 'setting_id' => $m_setting['id'], 'command_output' => json_encode($result), 'status' => $status]);
                        \Log::info(print_r(["Command Output", $allOutput], true));
                    else:
                        return response()->json(["code" => 500, "message" => "Request has been failed on stage server please check laravel log"]);
                    endif;

                }
            }

            return response()->json(["code" => 200, "message" => "Request pushed on website successfully"]);

        } else if ($scope === 'websites') {

            $store         = $request->store;
            $website       = $request->website;
            $websiteStores = WebsiteStore::with('website.storeWebsite')->whereHas('website', function ($q) use ($store, $website_ids, $entity_id) {
                $q->whereIn('store_website_id', $website_ids ?? [])->where('name', $store);
            })->orWhere('id', $entity->scope_id)->get();

            foreach ($websiteStores as $websiteStore) {
                $git_repository = isset($websiteStore->website->storeWebsite->repository) ? $websiteStore->website->storeWebsite->repository : null;
                $magento_url    = isset($websiteStore->website->storeWebsite->magento_url) ? $websiteStore->website->storeWebsite->magento_url : null;
                $server_name    = config('database.connections.' . $git_repository . '.host');
                if ($magento_url != null) {
                    $magento_url = explode('//', $magento_url);
                    $magento_url = isset($magento_url[1]) ? $magento_url[1] : $websiteStore->website->storeWebsite->magento_url;
                    $m_setting   = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope'     => $scope,
                            'scope_id'  => $websiteStore->id,
                            'name'      => $request->name,
                            'path'      => $request->path,
                            'value'     => $request->value,
                            'data_type' => $datatype,
                        ]);
                    } else {
                        $m_setting->name      = $name;
                        $m_setting->path      = $path;
                        $m_setting->value     = $value;
                        $m_setting->data_type = $datatype;
                        $m_setting->save();
                    }
                    $scopeID     = $websiteStore->platform_id;
                    $magento_url = str_replace('www.', '', $magento_url);
                    $magento_url = str_replace('.com', '', $magento_url);

                    //BASE SCRIPT
                    if (!empty($git_repository)):
                        $cmd         = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-config-deployment.sh -r ' . $git_repository . ' -s ' . $scope . ' -c ' . $scopeID . ' -p ' . $path . " -v  '" . $value . "' -t " . $datatype . ' -h ' . $server_name;
                        $allOutput   = array();
                        $allOutput[] = $cmd;
                        $result      = exec($cmd, $allOutput); //Execute command
                        $status      = 'Error';
                        for ($i = 0; $i < count($allOutput); $i++) {
                            if (strtolower($allOutput[$i]) == strtolower("Pull Request Successfully merged") ) {
                                $status = 'Success';
                                break;
                            }
                        }
                        $m_setting->status = $status;
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $websiteStore->website->storeWebsite->id, 'command' => $cmd, 'setting_id' => $m_setting['id'], 'command_output' => json_encode($allOutput), 'status' => $status]);
                        \Log::info(print_r(["Command Output", $allOutput], true));
                    else:
                        return response()->json(["code" => 500, "message" => "Request has been failed on stage server please check laravel log"]);
                    endif;

                }

            }

            return response()->json(["code" => 200, "message" => "Request pushed on website successfully"]);

        } else if ($scope === 'stores') {

            $store      = $request->store;
            $store_view = $request->store_view;

            $websiteStoresViews = WebsiteStoreView::with('websiteStore.website.storeWebsite')->whereHas('websiteStore.website', function ($q) use ($store, $website_ids) {
                $q->where('name', $store)->whereIn('store_website_id', $website_ids ?? []);
            })->where('code', $store_view)->orWhere('id', $entity->scope_id)->get();

            foreach ($websiteStoresViews as $websiteStoresView) {
                $git_repository = isset($websiteStore->website->storeWebsite->repository) ? $websiteStore->website->storeWebsite->repository : null;
                $magento_url    = isset($websiteStoresView->websiteStore->website->storeWebsite->magento_url) ? $websiteStoresView->websiteStore->website->storeWebsite->magento_url : null;
                $server_name    = config('database.connections.' . $git_repository . '.host');
                if ($magento_url != null) {
                    $magento_url = explode('//', $magento_url);
                    $magento_url = isset($magento_url[1]) ? $magento_url[1] : $websiteStoresView->websiteStore->website->storeWebsite->magento_url;
                    $m_setting   = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope'     => $scope,
                            'scope_id'  => $websiteStoresView->id,
                            'name'      => $request->name,
                            'path'      => $request->path,
                            'value'     => $request->value,
                            'data_type' => $datatype,
                        ]);
                    } else {
                        $m_setting->name      = $name;
                        $m_setting->path      = $path;
                        $m_setting->value     = $value;
                        $m_setting->data_type = $datatype;
                        $m_setting->save();
                    }
                    $scopeID     = $websiteStoresView->platform_id;
                    $magento_url = str_replace('www.', '', $magento_url);
                    $magento_url = str_replace('.com', '', $magento_url);

                    //BASE SCRIPT
                    if (!empty($git_repository)):

                        $cmd         = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-config-deployment.sh -r ' . $git_repository . ' -s ' . $scope . ' -c ' . $scopeID . ' -p ' . $path . " -v  '" . $value . "' -t " . $datatype . ' -h ' . $server_name;
                        $allOutput   = array();
                        $allOutput[] = $cmd;
                        $result      = exec($cmd, $allOutput); //Execute command
                        $status      = 'Error';
                        for ($i = 0; $i < count($allOutput); $i++) {
                            if (strtolower($allOutput[$i]) == strtolower("Pull Request Successfully merged")) {
                                $status = 'Success';
                                break;
                            }
                        }
                        $m_setting->status = $status;
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $websiteStoresView->websiteStore->website->storeWebsite->id, 'command' => $cmd, 'setting_id' => $m_setting['id'], 'command_output' => json_encode($allOutput), 'status' => $status]);
                        \Log::info(print_r(["Command Output", $allOutput], true));
                    else:
                        return response()->json(["code" => 500, "message" => "Request has been failed on stage server please check laravel log"]);
                    endif;

                }

            }

            return response()->json(["code" => 200, "message" => "Request pushed on website successfully"]);

        }

    }

    public function pushMagentoSettings(Request $request)
    {
        $store_website_id    = $request->store_website_id;
        $magentoSettings     = MagentoSetting::where('store_website_id', $store_website_id)->get();
        $settings            = '';
        $storeWebsiteDetails = StoreWebsite::leftJoin('github_repositories', 'github_repositories.id', '=', 'store_websites.repository_id')
            ->where('store_websites.id', $store_website_id)->select('github_repositories.name as repo_name')->first();

        foreach ($magentoSettings as $magentoSetting) {
            if ($magentoSetting['scope'] == 'default') {
                $scopeId = 0;
            } else if ($scope === 'websites') {
                $scopeId = WebsiteStore::where('id', $magentoSetting['scope_id'])->pluck('platform_id')->first();
            } else if ($scope === 'stores') {
                $scopeId = WebsiteStoreView::where('id', $magentoSetting['scope_id'])->pluck('platform_id')->first();
            }
            $settings .= $magentoSetting['scope'] . ',' . $scopeId . ',' . $magentoSetting['path'] . ',' . $magentoSetting['value'] . PHP_EOL;
        }
        if ($settings != '') {
            $filePath = public_path() . "/uploads/temp-sync.txt";
            $myfile   = fopen($filePath, "w") or die("Unable to open file!");
            fwrite($myfile, $settings);
            fclose($myfile);

           $cmd         = "bash " . "magento-config-deployment.sh -r " . $storeWebsiteDetails['repo_name'] . " -f '" . $filePath."'"; 
            $allOutput   = array();
            $allOutput[] = $cmd;
            $result      = exec($cmd, $allOutput); //Execute command

            \Log::info(print_r(["Command Output", $allOutput], true));
            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => $cmd, 'setting_id' => $magentoSetting['id'], 'command_output' => json_encode($allOutput)]);
        }
        return redirect(route('magento.setting.index'));
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
        return response()->json([
            'data' => WebsiteStoreView::select('id', 'code')->whereNotNull('code')->where('website_store_id', $request->website_id)->get(),
        ]);
    }

    public function deleteSetting($id)
    {
        $m_setting = MagentoSetting::find($id);
        if ($m_setting) {
            $m_setting->delete();
            $log      = $id . " Id Deleted successfully";
            $formData = ['event' => "delete", 'log' => $log];
            MagentoSettingLog::create($formData);
        }
        return redirect()->route('magento.setting.index');
    }

    public function namehistrory($id)
    {

        $ms    = MagentoSettingNameLog::select('magento_setting_name_logs.*', 'users.name')->leftJoin('users', 'magento_setting_name_logs.updated_by', 'users.id')->where('magento_settings_id', $id)->get();
        $table = "<table class='table table-bordered text-nowrap' style='border: 1px solid #ddd;'><thead><tr><th>Date</th><th>Old Value</th><th>New Value</th><th>Created By</th></tr></thead><tbody>";
        foreach ($ms as $m) {
            $table .= "<tr><td>" . $m->updated_at . "</td>";
            $table .= "<td>" . $m->old_value . "</td>";
            $table .= "<td>" . $m->new_value . "</td>";
            $table .= "<td>" . $m->name . "</td></tr>";
        }
        $table .= "</tbody></table>";
        echo $table;
    }

    public function magentoPushLogs($settingId)
    {
        $logs = MagentoSettingPushLog::where('setting_id', $settingId)->get();
        $data = '';
        foreach ($logs as $log) {
            $cmdOutputs = json_decode($log['command_output']);
            $data .= '<tr><td>' . $log['created_at'] . '</td><td>' . $log['command'] . '</td><td>' . $log['status'] . '</td><td>';
            if (!empty($cmdOutputs)) {
                foreach ($cmdOutputs as $cmdOutput) {
                    $data .= $cmdOutput . '<br/>';
                }
            }
            $data .= '</td></tr>';
        }
        echo $data;
    }

    public function updateViaFile(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data = file_get_contents($file->getRealPath());
            if (!empty($data)) {
                $config = preg_split('/\r\n|\r|\n/', $data);
                if (!empty($config) && is_array($config)) {
                    $total = 0;
                    foreach ($config as $c) {
                        $entity = MagentoSetting::where('path', $c)->get();
                        if (!$entity->isEmpty()) {
                            foreach ($entity as $m_setting) {
                                if ($m_setting->scope === 'default') {
                                    $storeWebsite = $m_setting->website;
                                    if ($storeWebsite) {
                                        $git_repository = $storeWebsite->repository;
                                        $magento_url    = $storeWebsite->magento_url;
                                        $server_name    = config('database.connections.' . $git_repository . '.host');
                                        if ($magento_url != null) {
                                            $magento_url          = explode('//', $magento_url);
                                            $magento_url          = isset($magento_url[1]) ? $magento_url[1] : $storeWebsite->magento_url;
                                            $m_setting->data_type = 'sensitive';
                                            $m_setting->save();


                                            $scopeID     = 0;
                                            $magento_url = str_replace('www.', '', $magento_url);
                                            $magento_url = str_replace('.com', '', $magento_url);

                                            //BASE SCRIPT
                                            if (!empty($git_repository)) {
                                                $cmd         = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-config-deployment.sh -r ' . $git_repository . ' -s ' . $m_setting->scope . ' -c ' . $scopeID . ' -p ' . $c . ' -v ' . $m_setting->value . ' -t ' . $m_setting->data_type . ' -h ' . $server_name;
                                                $allOutput   = array();
                                                $allOutput[] = $cmd;
                                                $result      = exec($cmd, $allOutput); //Execute command
                                                $status      = 'Error';
                                                for ($i = 0; $i < count($allOutput); $i++) {
                                                    if (strtolower($allOutput[$i]) == strtolower("Pull Request Successfully merged") ) {
                                                        $status = 'Success';
                                                        break;
                                                    }
                                                }
                                                $m_setting->status = $status;
                                                $m_setting->save();
                                                MagentoSettingPushLog::create(['store_website_id' => $storeWebsite['id'], 'command' => $cmd, 'setting_id' => $m_setting['id'], 'command_output' => json_encode($allOutput), 'status' => $status]);
                                                \Log::info(print_r(["Command Output", $allOutput], true));
                                            }
                                        }
                                    }
                                } else if ($m_setting->scope === 'websites') {
                                    $storeWebsite = $m_setting->website;
                                    if ($storeWebsite) {
                                        $git_repository = $storeWebsite->repository;
                                        $magento_url    = $storeWebsite->magento_url;
                                        $server_name    = config('database.connections.' . $git_repository . '.host');
                                        if ($magento_url != null) {
                                            $magento_url = explode('//', $magento_url);
                                            $magento_url = isset($magento_url[1]) ? $magento_url[1] : $storeWebsite->magento_url;

                                            $m_setting->data_type = 'sensitive';
                                            $m_setting->save();

                                            $scopeID     = $m_setting->store->platform_id;
                                            $magento_url = str_replace('www.', '', $magento_url);
                                            $magento_url = str_replace('.com', '', $magento_url);

                                            //BASE SCRIPT
                                            if (!empty($git_repository)) {
                                                $cmd         = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-config-deployment.sh -r ' . $git_repository . ' -s ' . $m_setting->scope . ' -c ' . $scopeID . ' -p ' . $c . ' -v ' . $m_setting->value . ' -t ' . $m_setting->data_type . ' -h ' . $server_name;
                                                $allOutput   = array();
                                                $allOutput[] = $cmd;
                                                $result      = exec($cmd, $allOutput); //Execute command
                                                $status      = 'Error';
                                                for ($i = 0; $i < count($allOutput); $i++) {
                                                    if (strtolower($allOutput[$i]) == strtolower("Pull Request Successfully merged") ) {
                                                        $status = 'Success';
                                                        break;
                                                    }
                                                }
                                                $m_setting->status = $status;
                                                $m_setting->save();
                                                MagentoSettingPushLog::create(['store_website_id' => $websiteStore->website->storeWebsite->id, 'command' => $cmd, 'setting_id' => $m_setting['id'], 'command_output' => json_encode($allOutput), 'status' => $status]);
                                                \Log::info(print_r(["Command Output", $allOutput], true));
                                            }
                                        }
                                    }

                                } else if ($m_setting->scope === 'stores') {
                                    $storeWebsite = $m_setting->website;
                                    if ($storeWebsite) {
                                        $git_repository = isset($storeWebsite->repository) ? $storeWebsite->repository : null;
                                        $magento_url    = isset($storeWebsite->magento_url) ? $storeWebsite->magento_url : null;
                                        $server_name    = config('database.connections.' . $git_repository . '.host');
                                        if ($magento_url != null) {
                                            $magento_url = explode('//', $magento_url);
                                            $magento_url = isset($magento_url[1]) ? $magento_url[1] : $storeWebsite->magento_url;

                                            $m_setting->data_type = 'sensitive';
                                            $m_setting->save();

                                            $scopeID     = $m_setting->storeview->platform_id;
                                            $magento_url = str_replace('www.', '', $magento_url);
                                            $magento_url = str_replace('.com', '', $magento_url);

                                            //BASE SCRIPT
                                            if (!empty($git_repository)) {
                                                $cmd         = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-config-deployment.sh -r ' . $git_repository . ' -s ' . $m_setting->scope . ' -c ' . $scopeID . ' -p ' . $c . ' -v ' . $m_setting->value . ' -t ' . $m_setting->data_type . ' -h ' . $server_name;
                                                $allOutput   = array();
                                                $allOutput[] = $cmd;
                                                $result      = exec($cmd, $allOutput); //Execute command
                                                $status      = 'Error';
                                                for ($i = 0; $i < count($allOutput); $i++) {
                                                    if (strtolower($allOutput[$i]) == strtolower("Pull Request Successfully merged") ) {
                                                        $status = 'Success';
                                                        break;
                                                    }
                                                }
                                                $m_setting->status = $status;
                                                $m_setting->save();
                                                MagentoSettingPushLog::create(['store_website_id' => $websiteStoresView->websiteStore->website->storeWebsite->id, 'command' => $cmd, 'setting_id' => $m_setting['id'], 'command_output' => json_encode($allOutput), 'status' => $status]);
                                                \Log::info(print_r(["Command Output", $allOutput], true));
                                            }
                                        }
                                    }

                                }
                            }
                        }
                    }
                    return redirect()->back()->withSuccess('Job has been finished');
                }else{
                    return redirect()->back()->withErrors('Oops, no path found on file');
                }
            }else{
                return redirect()->back()->withErrors('Oops, Looks like submitted empty file');
            }
        }else{
            return redirect()->back()->withErrors('Please select valid file for update sensitive paths');
        }
    }

}
