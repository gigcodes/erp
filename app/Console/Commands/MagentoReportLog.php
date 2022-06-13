<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MagentoReportLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoReportLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento Report Log';

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
        //echo 'test';exit;
        $storewebsite = \App\StoreWebsite::whereNotNull('server_ip')->get();
        //echo (getenv('DEPLOYMENT_SCRIPTS_PATH'));exit;
        $arrays = array('unit','integration');
        foreach($storewebsite AS $stroewebsite){
            //print_r($stroewebsite->server_ip);exit;
            foreach($arrays as $array){
                //$cmd = ' bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-commands.sh --server '. $stroewebsite->server_ip .' --type tests --test '. $array;
                $cmd = 'bash '.$stroewebsite->website.' ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-commands.sh --server '. $stroewebsite->server_ip .' --type tests --test '. $array;
                //echo $cmd; 
                //$result = exec($cmd);
                //chdir($old_path);
                $allOutput = array();
                $allOutput[] = $cmd;
                $result = exec($cmd, $allOutput); //Execute command
                $status = 'Error';
                dd($result);
            }
        }
    }
}
