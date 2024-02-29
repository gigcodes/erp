<?php

namespace App\Console\Commands;

use Google\Client;
use App\GoogleDeveloper;
use App\Helpers\LogHelper;
use App\GoogleDeveloperLogs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DevAPIReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DevAPIReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crash and ANR report using playdeveloperreporting check and store DB every hour';

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
     * @return int
     */
    public function handle()
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron started to run']);

            if (env('GOOGLE_PLAY_STORE_DELETE_LOGS') == '1') {
                GoogleDeveloperLogs::truncate();

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'truncate all the records from google_developer_logs table']);
            }

            $log          = new GoogleDeveloperLogs();
            $log->api     = 'crash/anr';
            $redirect_uri = 'https://erpstage.theluxuryunlimited.com/google/developer-api/crash';

            $client = new Client();
            $client->setApplicationName(env('GOOGLE_PLAY_STORE_APP_ID'));
            $client->setDeveloperKey(env('GOOGLE_PLAY_STORE_DEV_KEY'));
            $client->setClientId(env('GOOGLE_PLAY_STORE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_PLAY_STORE_CLIENT_SECRET'));
            $SERVICE_ACCOUNT_NAME = env('GOOGLE_PLAY_STORE_SERVICE_ACCOUNT');
            $KEY_FILE             = storage_path() . env('GOOGLE_PLAY_STORE_SERVICE_CREDENTIALS');
            $client->setRedirectUri($redirect_uri);
            $client->setAuthConfig($KEY_FILE);
            $user_to_impersonate = env('GOOGLE_PLAY_STORE_SERVICE_ACCOUNT');
            $client->setSubject($user_to_impersonate);
            $client->setScopes([env('GOOGLE_PLAY_STORE_SCOPES')]);

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'set all the required params for connecting to google api']);

            $token = null;
            if ($client->isAccessTokenExpired()) {
                $token = $client->fetchAccessTokenWithAssertion();
            } else {
                $token = $client->getAccessToken();
            }
            $_SESSION['token'] = $token;

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'connected to google api and token stored to session']);

            if (! $token && ! isset($_SESSION['token'])) {
                $authUrl = $client->createAuthUrl();

                $output  = 'connect';
                $output2 = $authUrl;
            } else {
                $array_app = explode(',', env('GOOGLE_PLAY_STORE_APP'));
                foreach ($array_app as $app_value) {
                    $at = $_SESSION['token']['access_token'];
                    //crash report

                    $res = Http::get('https://playdeveloperreporting.googleapis.com/v1beta1/apps/' . $app_value . '/crashRateMetricSet?access_token=' . $at);

                    if (gettype($res) != 'string') {
                        if (isset($res['error'])) {
                            if ($res['error']['code'] == 401) {
                                session_unset();
                                echo '401 error';
                            }
                        }
                        if (isset($res['name'])) {
                            $year                  = $res['freshnessInfo']['freshnesses'][0]['latestEndTime']['year'];
                            $day                   = $res['freshnessInfo']['freshnesses'][0]['latestEndTime']['day'];
                            $month                 = $res['freshnessInfo']['freshnesses'][0]['latestEndTime']['month'];
                            $date                  = $year . '-' . $month . '-' . $day;
                            $r                     = new GoogleDeveloper();
                            $r->name               = $res['name'];
                            $r->aggregation_period = $res['freshnessInfo']['freshnesses'][0]['aggregationPeriod'];
                            $r->latestEndTime      = $date;
                            $r->timezone           = $res['freshnessInfo']['freshnesses'][0]['latestEndTime']['timeZone']['id'];
                            $r->report             = 'crash';
                            $r->save();

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved google developer record by ID:' . $r->id]);
                            $postData = [
                                'timeline_spec' => ['aggregation_period' => 'DAILY', 'start_time' => ['year' => $year, 'month' => $month, 'day' => $day - 2], 'end_time' => ['year' => $year, 'month' => $month, 'day' => $day - 1]],

                                'dimensions' => ['apiLevel'],
                                'metrics'    => ['crashRate', 'distinctUsers', 'crashRate28dUserWeighted'],
                            ];

                            // Setup cURL

                            $ch = curl_init('https://playdeveloperreporting.googleapis.com/v1beta1/apps/' . $app_value . '/crashRateMetricSet:query');
                            curl_setopt_array($ch, [
                                CURLOPT_POST           => true,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_HTTPHEADER     => [
                                    'Authorization: OAuth ' . $at,

                                    'Content-Type: application/json',
                                ],
                                CURLOPT_POSTFIELDS => json_encode($postData),
                            ]);

                            // Send the request
                            $response      = curl_exec($ch);
                            $log           = new GoogleDeveloperLogs();
                            $log->api      = 'crash report';
                            $log->log_name = 'CRASH REPORT';
                            $log->result   = $response;
                            $log->save();

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved google developer logs record by ID:' . $log->id]);
                            echo 'crash report of ' . $app_value . ' added';
                        }
                    } else {
                        $log = new GoogleDeveloperLogs();

                        $log->api      = 'crash error';
                        $log->log_name = 'ERROR';
                        $log->result   = $res;
                        $log->save();
                        echo 'crash report of ' . $app_value . ' failed';

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved google developer logs record by ID:' . $log->id]);
                    }

                    //ANR Report

                    $res = Http::get('https://playdeveloperreporting.googleapis.com/v1beta1/apps/' . $app_value . '/anrRateMetricSet?access_token=' . $at);
                    $log = new GoogleDeveloperLogs();

                    if (gettype($res) != 'string') {
                        if (isset($res['error'])) {
                            if ($res['error']['code'] == 401) {
                                session_unset();
                                echo '401 error';
                            }
                        }
                        if (isset($res['name'])) {
                            $year                  = $res['freshnessInfo']['freshnesses'][0]['latestEndTime']['year'];
                            $day                   = $res['freshnessInfo']['freshnesses'][0]['latestEndTime']['day'];
                            $month                 = $res['freshnessInfo']['freshnesses'][0]['latestEndTime']['month'];
                            $date                  = $year . '-' . $month . '-' . $day;
                            $r                     = new GoogleDeveloper();
                            $r->name               = $res['name'];
                            $r->aggregation_period = $res['freshnessInfo']['freshnesses'][0]['aggregationPeriod'];
                            $r->latestEndTime      = $date;
                            $r->timezone           = $res['freshnessInfo']['freshnesses'][0]['latestEndTime']['timeZone']['id'];
                            $r->report             = 'anr';
                            $r->save();

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved google developer record by ID:' . $r->id]);
                            $postData = [
                                'timeline_spec' => ['aggregation_period' => 'DAILY', 'start_time' => ['year' => $year, 'month' => $month, 'day' => $day - 2], 'end_time' => ['year' => $year, 'month' => $month, 'day' => $day - 1]],

                                'dimensions' => ['apiLevel'],
                                'metrics'    => ['distinctUsers'],
                            ];

                            // Setup cURL

                            $ch = curl_init('https://playdeveloperreporting.googleapis.com/v1beta1/apps/' . $app_value . '/crashRateMetricSet:query');
                            curl_setopt_array($ch, [
                                CURLOPT_POST           => true,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_HTTPHEADER     => [
                                    'Authorization: OAuth ' . $at,

                                    'Content-Type: application/json',
                                ],
                                CURLOPT_POSTFIELDS => json_encode($postData),
                            ]);

                            // Send the request
                            $response      = curl_exec($ch);
                            $log           = new GoogleDeveloperLogs();
                            $log->api      = 'anr report';
                            $log->log_name = 'ANR REPORT';
                            $log->result   = $response;
                            $log->save();

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved google developer logs record by ID:' . $log->id]);

                            echo 'anr report of ' . $app_value . ' added';
                        }
                    } else {
                        $log = new GoogleDeveloperLogs();

                        $log->api      = 'anr error';
                        $log->log_name = 'ANR ERROR';
                        $log->result   = $res;
                        $log->save();

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved google developer logs record by ID:' . $log->id]);

                        echo 'anr report of ' . $app_value . ' failed';
                    }
                }
            }
        } catch(\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
