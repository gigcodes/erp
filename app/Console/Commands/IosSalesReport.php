<?php

namespace App\Console\Commands;

use App\AppSalesReport;
use Illuminate\Console\Command;
use App\Helpers\LogHelper;
use App\LogRequest;

class IosSalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IosSalesReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sales report using Appfigure which sync with Appstore connect check and store DB every day';

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

            $group_by = 'network';
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
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://api.appfigures.com/v2/reports/sales?group_by=' . $group_by . '&start_date=' . $start_date . '&end_date=' . $end_date . '&products=' . $app_value,
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
                $res = json_decode($result, true);
                // print_r($res);
                // print_r($res["apple:ios"]);
                // print($res["apple:ios"]["downloads"]);
                curl_close($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $url ="https://api.appfigures.com/v2/reports/sales?group_by=' . $group_by . '&start_date=' . $start_date . '&end_date=' . $end_date . '&products=' . $app_value";
                LogRequest::log($startTime, $url, 'POST', [], json_decode($result), $httpcode, \App\Console\Commands\IosSalesReport::class, 'handle');
                LogHelper::createCustomLogForCron($this->signature, ['message' => "CURL api was called."]);

                if ($res) {
                    $r = new AppSalesReport();
                    $r->product_id = $array_app_name[$i] . ' [' . $product_id . ']';
                    $r->group_by = $group_by;
                    $r->start_date = $start_date;
                    $r->end_date = $end_date;

                    $r->downloads = $res['apple:ios']['downloads'];
                    $r->re_downloads = $res['apple:ios']['re_downloads'];
                    $r->uninstalls = $res['apple:ios']['uninstalls'];
                    $r->updates = $res['apple:ios']['updates'];
                    $r->returns = $res['apple:ios']['returns'];
                    $r->net_downloads = $res['apple:ios']['net_downloads'];
                    $r->promos = $res['apple:ios']['promos'];
                    $r->revenue = $res['apple:ios']['revenue'];
                    $r->returns_amount = $res['apple:ios']['returns_amount'];
                    $r->edu_downloads = $res['apple:ios']['edu_downloads'];
                    $r->gifts = $res['apple:ios']['gifts'];
                    $r->gift_redemptions = $res['apple:ios']['gift_redemptions'];
                    $r->edu_revenue = $res['apple:ios']['edu_revenue'];
                    $r->gross_revenue = $res['apple:ios']['gross_revenue'];
                    $r->gross_returns_amount = $res['apple:ios']['gross_returns_amount'];
                    $r->gross_edu_revenue = $res['apple:ios']['gross_edu_revenue'];
                    $r->business_downloads = $res['apple:ios']['business_downloads'];
                    $r->business_revenue = $res['apple:ios']['business_revenue'];
                    $r->gross_business_revenue = $res['apple:ios']['gross_business_revenue'];
                    $r->standard_downloads = $res['apple:ios']['standard_downloads'];
                    $r->standard_revenue = $res['apple:ios']['standard_revenue'];
                    $r->gross_standard_revenue = $res['apple:ios']['gross_standard_revenue'];
                    $r->app_downloads = $res['apple:ios']['app_downloads'];
                    $r->app_returns = $res['apple:ios']['app_returns'];
                    $r->iap_amount = $res['apple:ios']['iap_amount'];
                    $r->iap_returns = $res['apple:ios']['iap_returns'];
                    $r->subscription_purchases = $res['apple:ios']['subscription_purchases'];
                    $r->subscription_returns = $res['apple:ios']['subscription_returns'];
                    $r->app_revenue = $res['apple:ios']['app_revenue'];
                    $r->app_returns_amount = $res['apple:ios']['app_returns_amount'];
                    $r->gross_app_revenue = $res['apple:ios']['gross_app_revenue'];
                    $r->gross_app_returns_amount = $res['apple:ios']['gross_app_returns_amount'];
                    $r->iap_revenue = $res['apple:ios']['iap_revenue'];
                    $r->iap_returns_amount = $res['apple:ios']['iap_returns_amount'];
                    $r->gross_iap_revenue = $res['apple:ios']['gross_iap_revenue'];
                    $r->gross_iap_returns_amount = $res['apple:ios']['gross_iap_returns_amount'];
                    $r->subscription_revenue = $res['apple:ios']['subscription_revenue'];
                    $r->subscription_returns_amount = $res['apple:ios']['subscription_returns_amount'];
                    $r->gross_subscription_revenue = $res['apple:ios']['gross_subscription_revenue'];
                    $r->gross_subscription_returns_amount = $res['apple:ios']['gross_subscription_returns_amount'];
                    $r->pre_orders = $res['apple:ios']['pre_orders'];
                    $r->storefront = $res['apple:ios']['storefront'];
                    $r->store = $res['apple:ios']['store'];
                    $r->save();
                }
                
                $i += 1;
            }
            
            LogHelper::createCustomLogForCron($this->signature, ['message' => "App sales report was added."]);
            return $this->info('Sales Report added');
        }catch(\Exception $e){
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
