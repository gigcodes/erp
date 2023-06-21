<?php

namespace App\Console\Commands;

use App\AppAdsReport;
use Illuminate\Console\Command;
use App\Helpers\LogHelper;
use App\LogRequest;

class IosAdsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IosAdsReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ads report using Appfigure which sync with Appstore connect check and store DB every day';

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
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        LogHelper::createCustomLogForCron($this->signature, ['message' => "cron was started."]);
        try{
            // https://api.appfigures.com/v2/reports/usage?group_by=network&start_date=2023-02-13&end_date=2023-02-14&products=280598515284

            $username = env('APPFIGURE_USER_EMAIL');
            $password = env('APPFIGURE_USER_PASS');
            $key = base64_encode($username . ':' . $password);

            $group_by = env('APPFIGURE_AD_NETWORK');
            $start_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
            $end_date = date('Y-m-d');
            $product_id = env('APPFIGURE_PRODUCT_ID');
            $ckey = env('APPFIGURE_CLIENT_KEY');
            $array_app_name = explode(',', env('APPFIGURE_APP_NAME'));
            $i = 0;
            $array_app = explode(',', env('APPFIGURE_PRODUCT_ID'));
            foreach ($array_app as $app_value) {
                //Usage Report
                $curl = curl_init();
                $url ="https://api.appfigures.com/v2/reports/ads?networks=' . $group_by . '&start_date=' . $start_date . '&end_date=' . $end_date . '&products=' . $app_value";
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'X-Client-Key:' . $ckey,
                        'Authorization: Basic ' . $key,
                    ],
                ]);

                $result = curl_exec($curl);
                // print($result);
                $res = json_decode($result, true); //here response decoded
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'GET', json_encode([]), $res, $httpcode, \App\Console\Commands\IosAdsReport::class, 'handle');
                curl_close($curl);
               
                LogHelper::createCustomLogForCron($this->signature, ['message' => "CURL api called."]);
                print_r($res);
                if ($res) {
                    $r = new AppAdsReport();
                    $r->product_id = $array_app_name[$i] . ' [' . $product_id . ']';
                    $r->networks = $group_by;
                    $r->start_date = $start_date;
                    $r->end_date = $end_date;

                    $r->revenue = $res['revenue'];
                    $r->requests = $res['requests'];
                    $r->impressions = $res['impressions'];
                    $r->ecpm = $res['ecpm'];

                    $r->fillrate = $res['fillrate'];
                    $r->ctr = $res['ctr'];
                    $r->clicks = $res['clicks'];
                    $r->requests_filled = $res['requests_filled'];

                    $r->save();
                }

                $i += 1;
            }

            LogHelper::createCustomLogForCron($this->signature, ['message' => "App payment report was added."]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => "cron was ended."]);
            return $this->info('Ads Report added');
        }catch(\Exception $e){
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
