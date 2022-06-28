<?php

namespace App\Console\Commands;

use App\StoreWebsite;
use App\MagentoDevScripUpdateLog;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class MagentoDevServerScriptUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoDevUpdateScript {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Magento Dev Script Updates';

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
            $websites = StoreWebsite::where('id', $this->argument('id'))->get();
            foreach($websites as $website){
                //dd($website->site_folder);
                if($website->site_folder !='' && $website->server_ip !=''){
                    $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-dev.sh --server ' . $website->server_ip . ' -site ' . $website->site_folder; 
                    $allOutput = array();
                    $allOutput[] = $cmd;
                    $result = exec($cmd, $allOutput);
                    if($result == '')
                        $result = "Not any response";
                    MagentoDevScripUpdateLog::create(
                        [
                            "store_website_id" =>  $website->id,
                            "website" =>  $website->website,
                            "response" => $result,
                            "site_folder" => $website->site_folder,
                        ]
                    );
                } else {
                    MagentoDevScripUpdateLog::create(
                        [
                            "store_website_id" =>  $website->id ?? '',
                            "website" =>  $website->website ?? '',
                            "response" => "Please check Site folder and server ip",
                            "error" => 'Error',
                            "site_folder" => $website->site_folder ?? '',
                        ]);     
                }
                
            } //end website foreach
        } catch (\Exception $e) {
            MagentoDevScripUpdateLog::create(
                [
                    "store_website_id" =>  $website[0]->id ?? '',
                    "website" =>  $website[0]->website ?? '',
                    "error" => $e->getMessage(),
                    "site_folder" => $website[0]->site_folder ?? '',
                ]
            );
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        } 
    }
}
