<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\StoreWebsite;
use App\AssetsManager;
use App\MagentoCommand;
use App\MagentoCommandRunLog;
use Illuminate\Console\Command;
use App\MagentoDevScripUpdateLog;
use Illuminate\Support\Facades\Log;

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
        $request = [];
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        Log::info('Start Rum Magento Command');
        try {
            $magCom = MagentoCommand::find($this->argument('id'));
            Log::info('Magento Command: ' . $magCom->command_type);
            Log::info('Magento Command website_ids: ' . $magCom->website_ids);
            if ($magCom->website_ids == 'ERP') {
                $job_id = '';
                Log::info('Start Rum Magento Command for website_id: ERP');
                $cmd = $magCom->command_type;
                $assets_manager_id = $magCom->assets_manager_id;
                $assetsmanager = AssetsManager::where('id', $assets_manager_id)->first();
                if ($assetsmanager && $assetsmanager->client_id != '') {
                    Log::info('client_id: ' . $assetsmanager->client_id);
                    $client_id = $assetsmanager->client_id;
                    $url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
                    $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                        'command' => $magCom->command_type,
                        'cwd' => $magCom->working_directory,
                        'is_sudo' => true,
                    ]));

                    $request = [
                        'command' => $magCom->command_type,
                        'cwd' => $magCom->working_directory,
                        'is_sudo' => true,
                        'url' => $url
                    ];

                    $headers = [];
                    $headers[] = 'Authorization: Basic ' . $key;
                    $headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    Log::info('API result: ' . $result);
                    if (curl_errno($ch)) {
                        Log::info('API Error: ' . curl_error($ch));
                        MagentoCommandRunLog::create(
                            [
                                'command_id' => $magCom->id,
                                'user_id' => \Auth::user()->id ?? '',
                                'website_ids' => 'ERP',
                                'command_name' => $magCom->command_type,
                                'server_ip' => '',
                                'command_type' => $magCom->command_type,
                                'response' => curl_error($ch),
                                'request' =>  json_encode($request), // Store the request as JSON
                            ]
                        );
                    }
                    $response = json_decode($result);
                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    LogRequest::log($startTime, $url, 'POST', json_encode(['command' => $magCom->command_type,
                        'cwd' => $magCom->working_directory,
                        'is_sudo' => true]), json_decode($result), $httpcode, \App\Console\Commands\MagentoRunCommand::class, 'handle');

                    if (isset($response->errors)) {
                        foreach ($response->errors as $error) {
                            $message = $error->code . ':' . $error->title . ':' . $error->detail;
                            Log::info('API Response Error: ' . $message);
                            MagentoCommandRunLog::create(
                                [
                                    'command_id' => $magCom->id,
                                    'user_id' => \Auth::user()->id ?? '',
                                    'website_ids' => 'ERP',
                                    'command_name' => $magCom->command_type,
                                    'server_ip' => '',
                                    'command_type' => $magCom->command_type,
                                    'response' => $message,
                                    'request' =>  json_encode($request), // Store the request as JSON
                                ]
                            );
                        }
                    }
                    $message = '';
                    if (isset($response->data) && isset($response->data->jid)) {
                        $job_id = $response->data->jid;
                        Log::info('API Response job_id: ' . $job_id);
                        $client_id = $assetsmanager->client_id;
                        $url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands/' . $job_id;
                        $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        $headers = [];
                        $headers[] = 'Authorization: Basic ' . $key;
                        //$headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $request = [
                            'command' => $magCom->command_type,
                            'cwd' => $magCom->working_directory,
                            'is_sudo' => true,
                            'url' => $url
                        ];

                        $result = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($result), $httpcode, \App\Console\Commands\MagentoRunCommand::class, 'handle');
                        $response = json_decode($result);
                        curl_close($ch);
                        if (isset($response->data) && isset($response->data->result)) {
                            $result = $response->data->result;

                            if (isset($result->stdout) && $result->stdout != '') {
                                $message .= 'Output: ' . $result->stdout;
                            }
                            if (isset($result->stderr) && $result->stderr != '') {
                                $message .= 'Error: ' . $result->stderr;
                            }
                            if (isset($result->summary) && $result->summary != '') {
                                $message .= 'summary: ' . $result->summary;
                            }
                        } else {
                            $message = 'Not get any response';
                        }
                    } else {
                        $message = 'Job ID not found in response';
                    }

                    MagentoCommandRunLog::create(
                        [
                            'command_id' => $magCom->id,
                            'user_id' => \Auth::user()->id ?? '',
                            'website_ids' => 'ERP',
                            'command_name' => $magCom->command_type,
                            'server_ip' => '',
                            'command_type' => $magCom->command_type,
                            'response' => $message,
                            'job_id' => $job_id,
                            'request' =>  json_encode($request), // Store the request as JSON
                        ]
                    );
                } else {

                    $request = [
                        'command' => $magCom->command_type,
                        'command_type' => $magCom->command_type,
                    ];

                    MagentoCommandRunLog::create(
                        [
                            'command_id' => $magCom->id,
                            'user_id' => \Auth::user()->id ?? '',
                            'website_ids' => 'ERP',
                            'command_name' => $magCom->command_type,
                            'server_ip' => '',
                            'command_type' => $magCom->command_type,
                            'response' => 'Assets Manager & Client id not found for this command!',
                            'request' =>  json_encode($request), // Store the request as JSON
                        ]
                    );
                }
                Log::info('End Rum Magento Command for website_id: ERP');
            } else {
                $websites = StoreWebsite::whereIn('id', explode(',', $magCom->website_ids))->get();

                if ($websites->isEmpty()) {

                    $request = [
                        'command' => $magCom->command_type,
                        'command_type' => $magCom->command_type,
                    ];

                    MagentoCommandRunLog::create(
                        [
                            'command_id' => $magCom->id,
                            'user_id' => \Auth::user()->id ?? '',
                            'website_ids' => '',
                            'command_name' => $magCom->command_type,
                            'server_ip' => '',
                            'command_type' => $magCom->command_type,
                            'response' => 'The command website is not found!',
                            'request' =>  json_encode($request), // Store the request as JSON
                        ]
                    );
                }
                foreach ($websites as $website) {
                    Log::info('Start Rum Magento Command for website_id: ' . $website->id);
                    if ($magCom->command_name != '' && $website->server_ip != '') {
                        Log::info('Command Name: ' . $magCom->command_name);
                        Log::info('website server_ip: ' . $website->server_ip);
                        $job_id = '';
                        $website_id = $website->id;
                        $assetsmanager = AssetsManager::where('id', $website->assets_manager_id)->first();

                        if ($assetsmanager && $assetsmanager->client_id != '') {
                            Log::info('client_id: ' . $assetsmanager->client_id);

                            $client_id = $assetsmanager->client_id;
                            $url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
                            $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                                'command' => $magCom->command_type,
                                'cwd' => $website->working_directory,
                                'is_sudo' => true,
                            ]));

                            $request = [
                                'command' => $magCom->command_type,
                                'cwd' => $magCom->working_directory,
                                'is_sudo' => true,
                                'url' => $url
                            ];

                            $headers = [];
                            $headers[] = 'Authorization: Basic ' . $key;
                            $headers[] = 'Content-Type: application/json';
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                            $result = curl_exec($ch);
                            Log::info('API result: ' . $result);
                            if (curl_errno($ch)) {
                                Log::info('API Error: ' . curl_error($ch));
                                MagentoCommandRunLog::create(
                                    [
                                        'command_id' => $magCom->id,
                                        'user_id' => \Auth::user()->id ?? '',
                                        'website_ids' => $website->id,
                                        'command_name' => $magCom->command_type,
                                        'server_ip' => $website->server_ip,
                                        'command_type' => $magCom->command_type,
                                        'response' => curl_error($ch),
                                        'request' =>  json_encode($request), // Store the request as JSON
                                    ]
                                );
                            }
                            $response = json_decode($result);
                            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);

                            LogRequest::log($startTime, $url, 'POST', json_encode(['command' => $magCom->command_type,
                                'cwd' => $website->working_directory,
                                'is_sudo' => true]), json_decode($result), $httpcode, \App\Console\Commands\MagentoRunCommand::class, 'handle');

                            if (isset($response->errors)) {
                                foreach ($response->errors as $error) {
                                    $message = $error->code . ':' . $error->title . ':' . $error->detail;
                                    Log::info('API Response Error: ' . $message);
                                    MagentoCommandRunLog::create(
                                        [
                                            'command_id' => $magCom->id,
                                            'user_id' => \Auth::user()->id ?? '',
                                            'website_ids' => $website->id,
                                            'command_name' => $magCom->command_type,
                                            'server_ip' => $website->server_ip,
                                            'command_type' => $magCom->command_type,
                                            'response' => $message,
                                            'request' =>  json_encode($request), // Store the request as JSON
                                        ]
                                    );
                                }
                            }
                            $message = '';
                            if (isset($response->data) && isset($response->data->jid)) {
                                $job_id = $response->data->jid;
                                Log::info('API Response job_id: ' . $job_id);
                                $client_id = $assetsmanager->client_id;
                                $url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands/' . $job_id;
                                $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POST, 0);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                                $headers = [];
                                $headers[] = 'Authorization: Basic ' . $key;
                                //$headers[] = 'Content-Type: application/json';
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                                $result = curl_exec($ch);
                                $response = json_decode($result);
                                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                curl_close($ch);
                                LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($result), $httpcode, \App\Console\Commands\MagentoRunCommand::class, 'handle');

                                if (isset($response->data) && isset($response->data->result)) {
                                    $result = $response->data->result;

                                    if (isset($result->stdout) && $result->stdout != '') {
                                        $message .= 'Output: ' . $result->stdout;
                                    }
                                    if (isset($result->stderr) && $result->stderr != '') {
                                        $message .= 'Error: ' . $result->stderr;
                                    }
                                    if (isset($result->summary) && $result->summary != '') {
                                        $message .= 'summary: ' . $result->summary;
                                    }
                                } else {
                                    $message = 'Not get any response';
                                }
                            } else {
                                $message = 'Job ID not found in response';
                            }

                            $request = [
                                'command' => $magCom->command_type,
                                'cwd' => $magCom->working_directory,
                                'is_sudo' => true,
                                'url' => $url
                            ];

                            MagentoCommandRunLog::create(
                                [
                                    'command_id' => $magCom->id,
                                    'user_id' => \Auth::user()->id ?? '',
                                    'website_ids' => $website->id,
                                    'command_name' => $magCom->command_type,
                                    'server_ip' => $website->server_ip,
                                    'command_type' => $magCom->command_type,
                                    'response' => $message,
                                    'job_id' => $job_id,
                                    'request' =>  json_encode($request), // Store the request as JSON
                                ]
                            );
                        } else {
                            MagentoCommandRunLog::create(
                                [
                                    'command_id' => $magCom->id,
                                    'user_id' => \Auth::user()->id ?? '',
                                    'website_ids' => '',
                                    'command_name' => $magCom->command_type,
                                    'server_ip' => '',
                                    'command_type' => $magCom->command_type,
                                    'response' => "Assets Manager ID #{$website->assets_manager_id}  not exists in DB for server_ip {$website->server_ip} for website {$website->title} OR Client id is empty for this asset {$website->assets_manager_id} for this command!",
                                    'request' =>  json_encode($request), 
                                ]
                            );
                        }
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
                                'request' =>  json_encode($request), 
                            ]);
                        //dd(\DB::getQueryLog());
                    }
                    Log::info('End Run Magento Command for website_id: ' . $website->id);
                } //end website foreach
            }
        } catch (\Exception $e) {
            Log::info(' Rum Magento Command catch error: ' . $e->getMessage());
            MagentoDevScripUpdateLog::create(
                [
                    'command_id' => $magCom->id,
                    'user_id' => \Auth::user()->id ?? '',
                    'website_ids' => $magCom->website_ids,
                    'command_name' => $magCom->command_type,
                    'server_ip' => '',
                    'command_type' => $magCom->command_type,
                    'response' => ' Error ' . $e->getMessage(),
                    'request' =>  json_encode($request), 
                ]
            );
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
        Log::info('End Rum Magento Command');
    }
}
