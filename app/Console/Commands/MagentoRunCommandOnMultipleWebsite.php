<?php

namespace App\Console\Commands;

use App\StoreWebsite;
use App\MagentoCommand;
use App\MagentoCommandRunLog;
use Illuminate\Console\Command;
use App\MagentoDevScripUpdateLog;
use Illuminate\Support\Facades\Artisan;
use App\AssetsManager;
use Illuminate\Support\Facades\Log;
use App\LogRequest;

class MagentoRunCommandOnMultipleWebsite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoRunCommandOnMultipleWebsite {id?} {websites_ids?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento Run Command On Multiple Website';

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
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        Log::info("Start Rum Magento Command On Multiple Website");
        try {
            $command_id=$this->argument('id');
            $websites_ids=$this->argument('websites_ids');
            $magCom = MagentoCommand::find($command_id);
            Log::info("Magento Command ID: ".$command_id);
            Log::info("Magento Command Type: ".$magCom->command_type);
            Log::info("Magento Command Name: ".$magCom->command_name);
            foreach($websites_ids as $websites_id){
                if ($websites_id == 'ERP') {
                    $job_id='';
                    Log::info("Start Rum Magento Command for website_id: ERP");
                    $cmd = $magCom->command_type;
                    if($magCom->website_ids=='ERP' && $magCom->assets_manager_id){
                        $assets_manager_id = $magCom->assets_manager_id;
                        $assetsmanager = AssetsManager::where('id', $assets_manager_id)->first();
                    }else{
                        $assetsmanager = AssetsManager::where('name', 'like', '%ERP%')->first();
                        
                    }

                    if($assetsmanager && $assetsmanager->client_id!=''){
                        Log::info("client_id: ".$assetsmanager->client_id);
                        $client_id=$assetsmanager->client_id;
                        $url="https://s10.theluxuryunlimited.com:5000/api/v1/clients/".$client_id."/commands";
                        $key=base64_encode("admin:86286706-032e-44cb-981c-588224f80a7d");
                        $requestParams = [
                            'command' => $magCom->command_type,
                            'cwd' => $magCom->working_directory,
                            'is_sudo' => true
                        ];
                                
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL,$url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestParams));
    
                        $headers = [];
                        $headers[] = 'Authorization: Basic '.$key;
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
                        $result = curl_exec($ch);
                        Log::info("API result: ".$result);
                        if (curl_errno($ch)) {
                            Log::info("API Error: ".curl_error($ch));
                            MagentoCommandRunLog::create(
                                [
                                    'command_id' => $magCom->id,
                                    'user_id' => \Auth::user()->id ?? '',
                                    'website_ids' => 'ERP',
                                    'command_name' => $magCom->command_type,
                                    'server_ip' => '',
                                    'command_type' => $magCom->command_type,
                                    'response' => curl_error($ch),
                                ]
                            );
                        }
                        $response = json_decode($result); //response decoded
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        LogRequest::log($startTime, $url, 'POST', json_encode($requestParams), $response, $httpcode, \App\Console\Commands\MagentoRunCommandOnMultipleWebsite::class, 'handle');
                        
                        curl_close($ch);
                        
                        if(isset($response->errors)){ 
                            foreach($response->errors as $error){
                                $message=$error->code.":".$error->title.":".$error->detail;
                                Log::info("API Response Error: ".$message);
                                MagentoCommandRunLog::create(
                                    [
                                        'command_id' => $magCom->id,
                                        'user_id' => \Auth::user()->id ?? '',
                                        'website_ids' =>'ERP',
                                        'command_name' => $magCom->command_type,
                                        'server_ip' => '',
                                        'command_type' => $magCom->command_type,
                                        'response' => $message,
                                    ]
                                );
                            }
                        }
                        if(isset($response->data) ){
                            if(isset($response->data->jid)){
                                $job_id=$response->data->jid;
                                Log::info("API Response job_id: ".$job_id);
                            }
                        }
    
                    }else{
                        MagentoCommandRunLog::create(
                            [
                                'command_id' => $magCom->id,
                                'user_id' => \Auth::user()->id ?? '',
                                'website_ids' => 'ERP',
                                'command_name' => $magCom->command_name,
                                'server_ip' => '',
                                'command_type' => $magCom->command_type,
                                'response' => 'Assets Manager & Client id not found for ERP!',
                            ]
                        );
                        Log::info("Assets Manager & Client id not found for ERP!");
                    }
                    $contains = \Str::contains($cmd, 'php artisan');
                    if ($contains) {
                        $result = '';
                        try {
                            $cmd = str_replace('php artisan', '', $cmd);
                            Artisan::call($cmd, []);
                            $result = Artisan::output();
                        } catch (\Exception $e) {
                            $result = $e->getMessage();
                        }
                    } else {
                        $cmd = 'cd ' . base_path() . ' ' . $cmd;
                        $allOutput = [];
                        $allOutput[] = $cmd;
                        $result = exec($cmd, $allOutput, $statusCode);
                        if ($statusCode == '') {
                            $result = 'Not any response';
                        } elseif ($statusCode == 0) {
                            $result = 'Command run success Response ' . $result;
                        } elseif ($statusCode == 1) {
                            $result = 'Command run Fail Response ' . $result;
                        } else {
                            $result = is_array($result) ? json_encode($result, true) : $result;
                        }
                    }
                    MagentoCommandRunLog::create(
                        [
                            'command_id' => $magCom->id,
                            'user_id' => \Auth::user()->id ?? '',
                            'website_ids' => 'ERP',
                            'command_name' => $cmd,
                            'server_ip' => '',
                            'command_type' => $magCom->command_type,
                            'response' => $result,
                            'job_id' => $job_id,
                        ]
                    );
                    Log::info("End Rum Magento Command for website_id: ERP");
                }else{
                    $websites = StoreWebsite::where('id', $websites_id)->get();
                    if ($websites->isEmpty() ) {
                        MagentoCommandRunLog::create(
                            [
                                'command_id' => $magCom->id,
                                'user_id' => \Auth::user()->id ?? '',
                                'website_ids' => $websites_id,
                                'command_name' => $magCom->command_name,
                                'server_ip' => '',
                                'command_type' => $magCom->command_type,
                                'response' => 'The website is not found!',
                            ]
                        );
                    }
                    foreach ($websites as $website) {
                        Log::info("Start Rum Magento Command for website_id: ".$website->id);
                        if ($magCom->command_name != '' && $website->server_ip != '') {
                            Log::info("Command Name: ".$magCom->command_name);
                            Log::info("website server_ip: ".$website->server_ip);
                            $job_id='';
                            $website_id=$website->id;
                            $assetsmanager = AssetsManager::where('id', $website->assets_manager_id)->first();
                            if($assetsmanager && $assetsmanager->client_id!=''){
                                Log::info("client_id: ".$assetsmanager->client_id);
                                $client_id=$assetsmanager->client_id;
                                $url="https://s10.theluxuryunlimited.com:5000/api/v1/clients/".$client_id."/commands";
                                $key=base64_encode("admin:86286706-032e-44cb-981c-588224f80a7d");
                                $requestParams = [
                                    'command' => $magCom->command_type, 
                                    'cwd' => $website->working_directory, 
                                    'is_sudo' => true 
                                ];
                                
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL,$url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestParams));

                                $headers = [];
                                $headers[] = 'Authorization: Basic '.$key;
                                $headers[] = 'Content-Type: application/json';
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                                $result = curl_exec($ch);
                                Log::info("API result: ".$result);
                                if (curl_errno($ch)) {
                                    Log::info("API Error: ".curl_error($ch));
                                    MagentoCommandRunLog::create(
                                        [
                                            'command_id' => $magCom->id,
                                            'user_id' => \Auth::user()->id ?? '',
                                            'website_ids' => $website->id,
                                            'command_name' => $magCom->command_type,
                                            'server_ip' => $website->server_ip,
                                            'command_type' => $magCom->command_type,
                                            'response' => curl_error($ch),
                                        ]
                                    );
                                }
                                $response = json_decode($result); //response decode
                                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                LogRequest::log($startTime, $url, 'POST', json_encode($requestParams), $response, $httpcode, \App\Console\Commands\MagentoRunCommandOnMultipleWebsite::class, 'handle');
                                
                                curl_close($ch);
                                            
                                if(isset($response->errors)){ 
                                    foreach($response->errors as $error){
                                        $message=$error->code.":".$error->title.":".$error->detail;
                                        Log::info("API Response Error: ".$message);
                                        MagentoCommandRunLog::create(
                                            [
                                                'command_id' => $magCom->id,
                                                'user_id' => \Auth::user()->id ?? '',
                                                'website_ids' => $website->id,
                                                'command_name' => $magCom->command_type,
                                                'server_ip' => $website->server_ip,
                                                'command_type' => $magCom->command_type,
                                                'response' => $message,
                                            ]
                                        );
                                    }
                                }
                                if(isset($response->data) ){
                                    if(isset($response->data->jid)){
                                        $job_id=$response->data->jid;
                                        Log::info("API Response job_id: ".$job_id);
                                    }
                                }

                            }else{
                                MagentoCommandRunLog::create(
                                    [
                                        'command_id' => $magCom->id,
                                        'user_id' => \Auth::user()->id ?? '',
                                        'website_ids' => $website_id,
                                        'command_name' => $magCom->command_name,
                                        'server_ip' => '',
                                        'command_type' => $magCom->command_type,
                                        'response' => 'Assets Manager & Client id not found for this website-'.$website_id,
                                    ]
                                );
                                Log::info("Assets Manager & Client id not found for website!-".$website_id);
                            }

                            //$cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH').$magCom->command_name.' --server ' . $magCom->server_ip.' --type custom --command ' . $website->command_type;
                            $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-commands.sh  --server ' . $website->server_ip . " --type custom --command '" . $magCom->command_type . "'";
                            if ($magCom->command_name == 'bin/magento cache:f' || $magCom->command_name == "'bin/magento cache:f'") {
                                $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-commands.sh  --server ' . $website->server_ip . " --type custom --command 'bin/magento cache:f'";
                            }
                            $allOutput = [];
                            $allOutput[] = $cmd;
                            $result = exec($cmd, $allOutput, $statusCode);
                            if ($statusCode == '') {
                                $result = 'Not any response';
                            } elseif ($statusCode == 0) {
                                $result = 'Command run success Response ' . $result;
                            } elseif ($statusCode == 1) {
                                $result = 'Command run Fail Response ' . $result;
                            } else {
                                $result = is_array($result) ? json_encode($result, true) : $result;
                            }
                            MagentoCommandRunLog::create(
                                [
                                    'command_id' => $magCom->id,
                                    'user_id' => \Auth::user()->id ?? '',
                                    'website_ids' => $website->id,
                                    'command_name' => $cmd,
                                    'server_ip' => $website->server_ip,
                                    'command_type' => $magCom->command_type,
                                    'response' => $result,
                                    'job_id' => $job_id,
                                ]
                            );


                        } else {
                            
                            $add = MagentoCommandRunLog::create(
                                [
                                    'command_id' => $magCom->id ?? '',
                                    'user_id' => \Auth::user()->id ?? '',
                                    'website_ids' => $website->id,
                                    'command_name' => $cmd ?? '',
                                    'server_ip' => $website->server_ip ?? '',
                                    'command_type' => $magCom->command_type ?? '',
                                    'response' => 'Server IP and Command not found',
                                ]);
                            Log::info("Server IP and Command not found");
                        }

                        Log::info("End Run Magento Command for website_id: ".$website->id);
                    }
                }
                
            }

            
            
            
        } catch (\Exception $e) {
            Log::info(" Error on Rum Magento Command On Multiple Websit: ".$e->getMessage());
            MagentoDevScripUpdateLog::create(
                [
                    'command_id' => $command_id,
                    'user_id' => \Auth::user()->id ?? '',
                    'website_ids' => '',
                    'command_name' => '' ,
                    'server_ip' => '',
                    'command_type' => '',
                    'response' => ' Error ' . $e->getMessage(),
                ]
            );
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
        Log::info("End Rum Magento Command On Multiple Website");
    }
}
