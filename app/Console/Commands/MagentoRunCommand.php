<?php

namespace App\Console\Commands;

use App\StoreWebsite;
use App\MagentoCommand;
use App\MagentoCommandRunLog;
use Illuminate\Console\Command;
use App\MagentoDevScripUpdateLog;
use Illuminate\Support\Facades\Artisan;
use App\AssetsManager;

class MagentoRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoCreatRunCommand {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento Create Command';

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
            $magCom = MagentoCommand::find($this->argument('id'));
            if ($magCom->website_ids == 'ERP') {
                $cmd = $magCom->command_type;
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
                    ]
                );
            } else {
                $websites = StoreWebsite::whereIn('id', explode(',', $magCom->website_ids))->get();
                
                foreach ($websites as $website) {
                    if ($magCom->command_name != '' && $website->server_ip != '') {
                        $job_id='';
                        $website_id=$website->id;
                        $assetsmanager = AssetsManager::where('website_id', $website_id)->first();
                        
                        if($assetsmanager && $assetsmanager->client_id!=''){
                            $client_id=$assetsmanager->client_id;
                            $url="https://s10.theluxuryunlimited.com:5000/api/v1/clients/".$client_id."/commands";
                            $key=base64_encode("admin:86286706-032e-44cb-981c-588224f80a7d");
                            
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL,$url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                                'command' => $magCom->command_type, 
                                //'cwd' => "/var/www/erp.theluxuryunlimited.com", 
                            ]));

                            $headers = [];
                            $headers[] = 'Authorization: Basic '.$key;
                            $headers[] = 'Content-Type: application/json';
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                            $result = curl_exec($ch);
                            if (curl_errno($ch)) {
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
                            $response = json_decode($result);
                            curl_close($ch);
                            
                            if(isset($response->errors)){ 
                                foreach($response->errors as $error){
                                    $message=$error->code.":".$error->title.":".$error->detail;
                                    
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
                                }
                            }
                            
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
                        //\DB::enableQueryLog();
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
                        //dd(\DB::getQueryLog());
                    }
                } //end website foreach
            }
        } catch (\Exception $e) {
            MagentoDevScripUpdateLog::create(
                [
                    'command_id' => $magCom->id,
                    'user_id' => \Auth::user()->id ?? '',
                    'website_ids' => $magCom->website_ids,
                    'command_name' => $cmd,
                    'server_ip' => '',
                    'command_type' => $magCom->command_type,
                    'response' => ' Error ' . $e->getMessage(),
                ]
            );
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
