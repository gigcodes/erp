<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use App\Sentry\SentryAccount;
use App\Sentry\SentryErrorLog;
use Illuminate\Console\Command;

class LoadSentryLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sentry:load_error_logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load error logs for Sentry';

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
            $report = \App\CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
          
            $accounts = SentryAccount::get();
            if($accounts){
                SentryErrorLog::truncate();
            }            
            $sentryLogsData = [];
            foreach($accounts as $account){
                $url = 'https://sentry.io/api/0/projects/'.$account->sentry_organization.'/'.$account->sentry_project.'/issues/';        
                $httpClient = new Client();

                $response = $httpClient->get(
                    $url,
                    [
                        RequestOptions::HEADERS => [
                            'Authorization' => 'Bearer '.$account->sentry_token,
                        ],
                    ]
                );
                $responseJson = json_decode($response->getBody()->getContents());

                foreach( $responseJson as $error_log){
                    SentryErrorLog::create([
                        'error_id'=>$error_log->id,
                        'error_title'=>$error_log->title,
                        'issue_type'=>$error_log->issueType,
                        'issue_category'=>$error_log->issueCategory,
                        'is_unhandled'=>($error_log->isUnhandled == 'false') ? 0:1,
                        'first_seen'=>date("d-m-y H:i:s", strtotime($error_log->firstSeen)),
                        'last_seen'=>date("d-m-y H:i:s", strtotime($error_log->lastSeen)),
                        'project_id'=> $account->id,
                    ]);
                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {      
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
            return false;
        }
    }
}
