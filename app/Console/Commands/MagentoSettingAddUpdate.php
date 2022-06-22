<?php

namespace App\Console\Commands;

use App\StoreWebsite;
use App\MagentoSetting;
use App\MagentoSettingUpdateResponseLog;
use Illuminate\Console\Command;

class MagentoSettingAddUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoSettingUpdates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento setting Updates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            $websites = StoreWebsite::whereNull("api_token")->whereNull("server_ip")->get();
            foreach($websites as $website){
                $findMegntoSetting = MagentoSetting::where('store_website_id', $website->id)->get();
                $curl = curl_init();
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://dev6.sololuxury.com/rest/V1/core/config/",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_HTTPHEADER => array(
                                "authorization:Bearer 7e9pvvgo4u5kel2xlchlj4hmgjb0lu6s" //.$website->api_token
                            ),
                        ));

                        $response = curl_exec($curl);
                        $err = curl_error($curl);

                        if (curl_errno($curl)) {
                            $error_msg = curl_error($curl);
                        }

                        $response = curl_exec($curl);
                        $http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
                        $resArr = json_decode($response);
                        //print_r($resArr); exit;
                        curl_close($curl);
                        echo '<pre>'; //echo gettype($resArr);
                        print_r($resArr);
                         exit;
                        if(is_array($resArr) && isset($resArr[0][0]['config_id'])) {
                            foreach($resArr[0] as $res){
                                if(empty($findMegntoSetting[0])) {
                                    MagentoSetting::create(
                                        [
                                            "config_id" =>  $res['config_id'],
                                            "scope" => $res["default"],
                                            "store_website_id" => $website->id,
                                            "website_store_id" => $website->id,
                                            "scope_id" => $website->id,
                                            "path" => $res["path"],
                                            "value" =>  $res["value"],
                                            "updated_at" => $res["updated_at"]
                                        ],
                                    );
                                } else {
                                    MagentoSettingUpdateResponseLog::create(
                                        [
                                            'website_id' => $website->id,
                                            'magento_setting_id' => $findMegntoSetting[0]->id ?? '',
                                            'response' => is_array($response) ? json_encode($response) : $response,
                                        ]
                                    );
                                }
                            }
                        } else {
                            MagentoSettingUpdateResponseLog::create(
                                [
                                    'website_id' => $website->id,
                                    'magento_setting_id' => $findMegntoSetting[0]->id ?? '',
                                    'response' => is_array($response) ? json_encode($response) : $response,
                                ]
                            );
                        }
                
                // \Log::info('Magento log created : '.$website);
                }
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        } 
        
    }
}
