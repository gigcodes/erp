<?php

namespace App\Console\Commands;
use App\AssetsManager;
use App\StoreWebsite;
use App\Models\VarnishStats;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VarnishRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert-varnish-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Varnish Records';

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
        try {
            Log::info('Start Varnish Records');

            $storeWebsites = StoreWebsite::get();
            if(!empty($storeWebsites)){
                foreach ($storeWebsites as $storeWebsite) {

                    Log::info($storeWebsite->title);

                    if((!empty($storeWebsite->assets_manager_id)) && ($storeWebsite->assets_manager_id >0)){

                        Log::info('asset -'.$storeWebsite->assets_manager_id);
    
                        $assetsmanager = AssetsManager::where('id', $storeWebsite->assets_manager_id)->first();
                
                        if(!empty($storeWebsite->server_ip) && !empty($storeWebsite->title) && !empty($assetsmanager->ip_name)){

                            Log::info('server_ip -'.$storeWebsite->server_ip.'--- title -'.$storeWebsite->title.'--- ip_name -'.$assetsmanager->ip_name);

                            $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

                            Log::info($scriptsPath);

                            $cmd = "bash $scriptsPath" . "varnish_get_details.sh -s \"$assetsmanager->ip_name\" -i \"$storeWebsite->server_ip\" -w \"$storeWebsite->title\"";

                            Log::info($cmd);

                            // NEW Script
                            $result = exec($cmd, $output, $return_var);

                            \Log::info('store command:' . $cmd);
                            \Log::info('store output:' . print_r($output, true));
                            \Log::info('store return_var:' . $return_var);

                            /*VarnishStats::create([
                                'created_by' => 0,
                                'store_website_id' => $storeWebsite->id,
                                'assets_manager_id' => $storeWebsite->assets_manager_id,
                                'server_name' => $storeWebsite->title,
                                'server_ip' => $storeWebsite->server_ip,
                                'website_name' => $assetsmanager->ip_name,
                                'request_data' => $cmd,
                                'response_data' => json_encode($result),
                            ]);*/
                        }
                    }
                }
            }

            Log::info('End Sonar Qube');
            
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
