<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\LogRequest;
use App\Website;
use App\StoreWebsite;
use App\WebsiteStore;
use App\MagentoSetting;
use App\WebsiteStoreView;
use App\MagentoSettingLog;
use App\MagentoSettingNameLog;
use App\MagentoSettingPushLog;

class PushMagentoSettings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $magentoSetting;

    protected $selectedWebsite;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($magentoSetting, $selectedWebsite = '')
    {
        // Set product and website
        $this->magentoSetting = $magentoSetting;
        $this->selectedWebsite = $selectedWebsite;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Set time limit
            set_time_limit(0);

            // Load product and website
            $magentoSetting = $this->magentoSetting;
            $entity = $magentoSetting;
            
            $scope = $entity->scope;
            $name = $entity->name;
            $path = $entity->path;
            $value = $entity->value;
            $datatype = $entity->datatype;
            
            if($this->selectedWebsite) {
                $website_ids[] = $this->selectedWebsite;
            } else {
                $website_ids[] = $entity->store_website_id;
            }
            
            
            // #DEVTASK-23677-api implement for admin settings
            \Log::info("Setting Scope : ".$scope);
            // Scope Default
            if ($scope === 'default') {
                $storeWebsites = StoreWebsite::whereIn('id', $website_ids ?? [])->get();
                foreach ($storeWebsites as $storeWebsite) {
                    $store_website_id=$storeWebsite->id;
                    
                    \Log::info("Start Setting Pushed to : ".$store_website_id);
                    $api_token=$storeWebsite->api_token;
                    $magento_url=$storeWebsite->magento_url;

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $store_website_id)->where('path', $path)->first();
                    if (! $m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $store_website_id,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value,
                            'data_type' => $datatype,
                        ]);
                    } else {
                        $m_setting->name = $name;
                        $m_setting->path = $path;
                        $m_setting->value = $value;
                        $m_setting->data_type = $datatype;
                        $m_setting->save();
                    }

                    $startTime  = date("Y-m-d H:i:s", LARAVEL_START);
                    $url=rtrim($magento_url, '/') ."/rest/all/V1/store-info/configuration";
                    $data=[];
                    $data['scopeId']=0;
                    $data['scopeType']="default";
                    $data['configs'][]=['path'=>$path,'value'=>$value];
                    
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $api_token));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    $result = curl_exec($ch);
                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    \Log::info(print_r([json_encode($data), $url, $result], true));
                    
                    LogRequest::log($startTime, $url, 'POST', json_encode($data),json_decode($result),$httpcode,App\Jobs\PushMagentoSettings::class, 'handle');

                    if (curl_errno($ch)) {
                        \Log::info("API Error: ".curl_error($ch));
                        MagentoSettingPushLog::create(['store_website_id' => $store_website_id,'command' => son_encode($data), 'setting_id' => $m_setting->id, 'command_output' =>curl_error($ch), 'status' => 'Error','command_server'=>$url,'job_id'=>$httpcode ]);
                    }

                    $response = json_decode($result);
                    curl_close($ch);
                    if($httpcode=='200'){
                        $m_setting->status ='Success';
                        $m_setting->value_on_magento =$value;
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $m_setting->id, 'command_output' =>'Success', 'status' => 'Success','command_server'=>$url,'job_id'=>$httpcode ]);
                        
                    }else{
                        $m_setting->status ='Error';
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $m_setting->id, 'command_output' =>$result, 'status' => 'Error','command_server'=>$url,'job_id'=>$httpcode ]);
                    }
                    \Log::info("End Setting Pushed to : ".$store_website_id);
                }
            }
            // Scope Default
            if ($scope === 'websites') {
                $store = $entity->website_store_id;
                
                $websiteStores = WebsiteStore::with('website.storeWebsite')->where('id', $entity->scope_id)->get();

                foreach ($websiteStores as $websiteStore) {
                    $store_website_id = isset($websiteStore->website->storeWebsite->id) ? $websiteStore->website->storeWebsite->id : 0;

                    \Log::info("Start Setting Pushed to : ".$store_website_id);
                    \Log::info("Website Store : ".$websiteStore->id);

                    $magento_url = isset($websiteStore->website->storeWebsite->magento_url) ? $websiteStore->website->storeWebsite->magento_url : null;
                    $api_token = isset($websiteStore->website->storeWebsite->api_token) ? $websiteStore->website->storeWebsite->api_token : null;

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
                    if (! $m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStore->id,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value,
                            'data_type' => $datatype,
                        ]);
                    } else {
                        $m_setting->name = $name;
                        $m_setting->path = $path;
                        $m_setting->value = $value;
                        $m_setting->data_type = $datatype;
                        $m_setting->save();
                    }
                    $scopeID = $websiteStore->platform_id;
                    if (! empty($magento_url) && !empty($api_token)) {
                        $startTime  = date("Y-m-d H:i:s", LARAVEL_START);
                        $url=rtrim($magento_url, '/') ."/rest/all/V1/store-info/configuration";
                        $data=[];
                        $data['scopeId']=$scopeID;
                        $data['scopeType']="websites";
                        $data['configs'][]=['path'=>$path,'value'=>$value];

                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $api_token));
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        $result = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        \Log::info(print_r([json_encode($data), $url, $result], true));
                        
                        LogRequest::log($startTime, $url, 'POST', json_encode($data),json_decode($result),$httpcode,App\Jobs\PushMagentoSettings::class, 'handle');

                        if (curl_errno($ch)) {
                            \Log::info("API Error: ".curl_error($ch));
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>curl_error($ch), 'status' => 'Error','command_server'=>$url ,'job_id'=>$httpcode]);
                        }

                        $response = json_decode($result);
                        curl_close($ch);
                        if($httpcode=='200'){
                            $m_setting->status ='Success';
                            $m_setting->value_on_magento =$value;
                            $m_setting->save();
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>'Success', 'status' => 'Success','command_server'=>$url ,'job_id'=>$httpcode]);
                            
                        }else{
                            $m_setting->status ='Error';
                            $m_setting->save();
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>$result, 'status' => 'Error','command_server'=>$url,'job_id'=>$httpcode ]);
                        }

                    }else{
                        $m_setting->status ='Error';
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => '', 'setting_id' => $m_setting->id, 'command_output' =>'Magento URL & API Token is not found', 'status' => 'Error','job_id'=>'500']);
                    }
                    \Log::info("End Setting Pushed to : ".$store_website_id);

                }
            }
            // Scope Default
            if ($scope === 'stores') {
                $websiteStoresViews = WebsiteStoreView::with('websiteStore.website.storeWebsite')->with('websiteStore.website')->where('id', $entity->scope_id)->get();

                foreach ($websiteStoresViews as $websiteStoresView) {
                    $store_website_id = isset($websiteStoresView->websiteStore->website->storeWebsite->id) ? $websiteStoresView->websiteStore->website->storeWebsite->id : 0;

                    \Log::info("Start Setting Pushed to : ".$store_website_id);
                    \Log::info("Website Store View : ".$websiteStoresView->id);

                    $magento_url = isset($websiteStoresView->websiteStore->website->storeWebsite->magento_url) ? $websiteStoresView->websiteStore->website->storeWebsite->magento_url : null;
                    $api_token = isset($websiteStoresView->websiteStore->website->storeWebsite->api_token) ? $websiteStoresView->websiteStore->website->storeWebsite->api_token : null;

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                    if (! $m_setting) {
                        $m_setting = MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStoresView->id,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value,
                            'data_type' => $datatype,
                        ]);
                    } else {
                        $m_setting->name = $name;
                        $m_setting->path = $path;
                        $m_setting->value = $value;
                        $m_setting->data_type = $datatype;
                        $m_setting->save();
                    }
                    $scopeID = $websiteStoresView->platform_id;
                    if (! empty($magento_url) && !empty($api_token)) {
                        $startTime  = date("Y-m-d H:i:s", LARAVEL_START);
                        $url=rtrim($magento_url, '/') ."/rest/all/V1/store-info/configuration";
                        $data=[];
                        $data['scopeId']=$scopeID;
                        $data['scopeType']="stores";
                        $data['configs'][]=['path'=>$path,'value'=>$value];

                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $api_token));
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        $result = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        \Log::info(print_r([json_encode($data), $url, $result], true));
                        
                        LogRequest::log($startTime, $url, 'POST', json_encode($data),json_decode($result),$httpcode,App\Jobs\PushMagentoSettings::class, 'handle');

                        if (curl_errno($ch)) {
                            \Log::info("API Error: ".curl_error($ch));
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>curl_error($ch), 'status' => 'Error','command_server'=>$url,'job_id'=>$httpcode ]);
                        }

                        $response = json_decode($result);
                        curl_close($ch);
                        if($httpcode=='200'){
                            $m_setting->status ='Success';
                            $m_setting->value_on_magento =$value;
                            $m_setting->save();
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>'Success', 'status' => 'Success','command_server'=>$url ,'job_id'=>$httpcode]);
                            
                        }else{
                            $m_setting->status ='Error';
                            $m_setting->save();
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' =>$result, 'status' => 'Error','command_server'=>$url ,'job_id'=>$httpcode]);
                        }

                    }else{
                        $m_setting->status ='Error';
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => '', 'setting_id' => $m_setting->id, 'command_output' =>'Magento URL & API Token is not found', 'status' => 'Error','job_id'=>'500']);
                    }
                    \Log::info("End Setting Pushed to : ".$store_website_id);
                }
            }
            // #DEVTASK-23677-api implement for admin settings
            
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['PushMagentoSettings', $this->magentoSetting->id];
    }
}
