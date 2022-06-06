<?php

namespace App\Console\Commands;

use App\StoreWebsite;
use App\GoogleBigQueryData;
use Google\Cloud\BigQuery\BigQueryClient;
use Illuminate\Console\Command;

class GoogleBigQuery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:googleBigData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google big query data';

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
        $storewebsites = StoreWebsite::all();  
        
        foreach($storewebsites as $storewebsite){ 
            $config = [
                'keyFilePath' => '/Users/satyamtripathi/Work/sololux-erp/public/big.json',
                'projectId' => 'brandsandlabels',
            ];

            $bigQuery = new BigQueryClient($config);
            $query = 'SELECT * FROM `brandsandlabels.firebase_crashlytics.com_app_brandslabels_ANDROID_REALTIME` WHERE DATE(event_timestamp) = "2022-06-03"';
            $queryJobConfig = $bigQuery->query($query)
            ->parameters([]);
            $queryResults = $bigQuery->runQuery($queryJobConfig);
            foreach ($queryResults as $row) {
                dd($row);
                /*
                $gBigQ = new GoogleBigQueryData();
                $gBigQ->google_project_id = $row->;
                $gBigQ->platform = $row->;
                $gBigQ->bundle_identifier = $row->;
                $gBigQ->event_id = $row->;
                $gBigQ->is_fatal = $row->;
                $gBigQ->issue_id = $row->;
                $gBigQ->issue_title = $row->;
                $gBigQ->issue_subtitle = $row->;
                $gBigQ->event_timestamp = $row->;
                $gBigQ->received_timestamp = $row->;
                $gBigQ->device = $row->;
                $gBigQ->memory = $row->;
                $gBigQ->storage = $row->;
                $gBigQ->operating_system = $row->;
                $gBigQ->application = $row->;
                $gBigQ->user = $row->;
                $gBigQ->custom_keys = $row->;
                $gBigQ->installation_uuid = $row->;
                $gBigQ->crashlytics_sdk_version = $row->;
                $gBigQ->app_orientation = $row->;
                $gBigQ->device_orientation = $row->;
                $gBigQ->process_state = $row->;
                $gBigQ->logs = $row->;
                $gBigQ->breadcrumbs = $row->;
                $gBigQ->blame_frame = $row->;
                $gBigQ->exceptions = $row->;
                $gBigQ->errors = $row->;
                $gBigQ->threads = $row->;*/
            }
        }
    }
}
