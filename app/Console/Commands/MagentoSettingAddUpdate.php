<?php

namespace App\Console\Commands;

use App\StoreWebsite;
use App\MagentoSetting;
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
        $websites = StoreWebsite::whereNull("api_token")->whereNull("server_ip")->get();
        foreach($websites as $website){
            $findMegntoSetting = MagentoSetting::where('store_website_id', $website->id)->get();
            if(empty($findMegntoSetting[0])) {
                MagentoSetting::create(
                    [
                        "config_id" => "1",
                        "scope" => "default",
                        "store_website_id" => $website->id,
                        "website_store_id" => $website->id,
                        "scope_id" => $website->id,
                        "path" => "yotpo/module_info/yotpo_installation_date",
                        "value" =>  date('Y-m-d'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ],
                    [
                        "config_id" => "2",
                        "scope" => "default",
                        "store_website_id" => $website->id,
                        "website_store_id" => $website->id,
                        "scope_id" => $website->id,
                        "path" => "yotpo/sync_settings/orders_sync_start_date",
                        "value" =>  date('Y-m-d'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]
                );
                \Log::info('Magento log created : '.$website);
            }

           // echo '===DONE===';
        }
        
    }
}
