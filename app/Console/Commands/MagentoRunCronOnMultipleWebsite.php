<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\StoreWebsite;
use App\AssetsManager;
use App\MagentoCommand;
use App\MagentoCommandRunLog;
use App\Models\MagentoCronList;
use App\Models\MagentoCronRunLog;
use Illuminate\Console\Command;
use App\MagentoDevScripUpdateLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class MagentoRunCronOnMultipleWebsite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoRunCronOnMultipleWebsite {id?} {websites_ids?}';

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
        Log::info('Start Rum Magento Command On Multiple Website');
        try {
            $command_id = $this->argument('id');
            $websites_ids = $this->argument('websites_ids');
            $magCom = MagentoCronList::find($command_id);

            Log::info('Magento Command ID: ' . $command_id);
            Log::info('Magento Cron Name: ' . $magCom->cron_name);

            foreach ($websites_ids as $websites_id) {
                
                $websites = StoreWebsite::where('id', $websites_id)->get();
                if ($websites->isEmpty()) {
                    MagentoCronRunLog::create(
                        [
                            'command_id' => $magCom->id,
                            'user_id' => \Auth::user()->id ?? '',
                            'website_ids' => $websites_id,
                            'command_name' => $magCom->cron_name,
                            'server_ip' => '',
                            'working_directory' => $website->working_directory,
                            'response' => 'The website is not found!',
                        ]
                    );
                }

                foreach ($websites as $website) {
                    Log::info('Start Rum Magento Cron for website_id: ' . $website->id);

                    if ($magCom->cron_name != '' && $website->server_ip != '') {

                        Log::info('Cron Name: ' . $magCom->cron_name);
                        Log::info('website server_ip: ' . $website->server_ip);

                        $job_id = '';
                        $website_id = $website->id;
                        
                        //$url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
                        $url = getenv('MAGENTO_COMMAND_API_URL');
                        $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');
                        $requestParams = [
                            'command' => $magCom->cron_name,
                            'dir' => $website->working_directory,
                            'server' => $website->server_ip,
                        ];

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestParams));

                        $headers = [];
                        $headers[] = 'Authorization: Basic ' . $key;
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);
                        Log::info('API result: ' . $result);
                        if (curl_errno($ch)) {
                            Log::info('API Error: ' . curl_error($ch));
                            MagentoCronRunLog::create(
                                [
                                    'command_id' => $magCom->id,
                                    'user_id' => \Auth::user()->id ?? '',
                                    'website_ids' => $website->id,
                                    'command_name' => $magCom->cron_name,
                                    'server_ip' => $website->server_ip,
                                    'working_directory' => $website->working_directory,
                                    'response' => curl_error($ch),
                                    'request' =>  json_encode($requestParams),
                                ]
                            );
                        }
                        $response = json_decode($result); //response decode
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        LogRequest::log($startTime, $url, 'POST', json_encode($requestParams), $response, $httpcode, \App\Console\Commands\MagentoRunCronOnMultipleWebsite::class, 'handle');

                        curl_close($ch);

                        if (isset($response->errors)) {
                            foreach ($response->errors as $error) {
                                $message = $error->code . ':' . $error->title . ':' . $error->detail;
                                Log::info('API Response Error: ' . $message);
                                MagentoCronRunLog::create(
                                    [
                                        'command_id' => $magCom->id,
                                        'user_id' => \Auth::user()->id ?? '',
                                        'website_ids' => $website->id,
                                        'command_name' => $magCom->cron_name,
                                        'server_ip' => $website->server_ip,
                                        'working_directory' => $website->working_directory,
                                        'response' => $message,
                                        'request' =>  json_encode($requestParams),
                                    ]
                                );
                            }
                        }

                        if (isset($response->data)) {
                            if (isset($response->data->jid)) {
                                $job_id = $response->data->jid;
                                Log::info('API Response job_id: ' . $job_id);
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
                            $status = 'Not any response';
                        } elseif ($statusCode == 0) {
                            $result = 'Cron run success Response ' . $result;
                            $status = 'Success';
                        } elseif ($statusCode == 1) {
                            $result = 'Cron run Fail Response ' . $result;
                            $status = 'Fail';
                        } else {
                            $result = is_array($result) ? json_encode($result, true) : $result;
                            $status = '-';
                        }

                        $magCom->last_execution_time = date('Y-m-d H:i:s');
                        $magCom->last_message = $result;
                        $magCom->cron_status = $status;
                        $magCom->save();

                        MagentoCronRunLog::create(
                            [
                                'command_id' => $magCom->id,
                                'user_id' => \Auth::user()->id ?? '',
                                'website_ids' => $website->id,
                                'command_name' => $cmd,
                                'server_ip' => $website->server_ip,
                                'working_directory' => $website->working_directory,
                                'response' => $result,
                                'job_id' => $job_id,
                                'request' =>  json_encode($requestParams),
                            ]
                        );
                    } else {
                        $add = MagentoCronRunLog::create(
                            [
                                'command_id' => $magCom->id ?? '',
                                'user_id' => \Auth::user()->id ?? '',
                                'website_ids' => $website->id,
                                'command_name' => $cmd ?? '',
                                'server_ip' => $website->server_ip ?? '',
                                'working_directory' => $website->working_directory ?? '',
                                'response' => 'Server IP and Cron not found',
                            ]);
                        Log::info('Server IP and Cron not found');
                    }

                    Log::info('End Run Magento Cron for website_id: ' . $website->id);
                }
            }
        } catch (\Exception $e) {
            Log::info(' Error on Rum Magento Cron On Multiple Websit: ' . $e->getMessage());
            MagentoDevScripUpdateLog::create(
                [
                    'command_id' => $command_id,
                    'user_id' => \Auth::user()->id ?? '',
                    'website_ids' => '',
                    'command_name' => '',
                    'server_ip' => '',
                    'command_type' => '',
                    'response' => ' Error ' . $e->getMessage(),
                ]
            );
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
        Log::info('End Rum Magento Command On Multiple Website');
    }
}
