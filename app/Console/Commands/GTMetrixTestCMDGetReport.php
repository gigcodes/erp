<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\StoreViewsGTMetrix;
use Carbon\Carbon;
use Entrecore\GTMetrixClient\GTMetrixClient;
use Illuminate\Console\Command;

class GTMetrixTestCMDGetReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GT-metrix-test-get-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GT metrix get site report';

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

        //try {
        \Log::info('GTMetrix :: Report cron start ');
        $report = CronJobReport::create([
            'signature'  => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        // Get site report
        $storeViewList = StoreViewsGTMetrix::whereNotNull('test_id')
            ->whereNotIn('status', ['error', 'not_queued'])
            ->get();

        $client = new GTMetrixClient();
        $client->setUsername(env('GTMETRIX_USERNAME'));
        $client->setAPIKey(env('GTMETRIX_API_KEY'));
        $client->getLocations();
        $client->getBrowsers();

        foreach ($storeViewList as $value) {

            $test   = $client->getTestStatus($value->test_id);
            $model = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->update([
                'status'          => $test->getState(),
                'error'           => $test->getError(),
                'report_url'      => $test->getReportUrl(),
                'html_load_time'  => $test->getHtmlLoadTime(),
                'html_bytes'      => $test->getHtmlBytes(),
                'page_load_time'  => $test->getPageLoadTime(),
                'page_bytes'      => $test->getPageBytes(),
                'page_elements'   => $test->getPageElements(),
                'pagespeed_score' => $test->getPagespeedScore(),
                'yslow_score'     => $test->getYslowScore(),
                'resources'       => json_encode($test->getResources()),
                //'pdf_file'        => $fileName,
            ]);

            $resources = $test->getResources();

            \Log::info(print_r(["Resource started",$resources],true));

            if (!empty($resources['report_pdf'])) {
                $ch = curl_init($resources['report_pdf']);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, env('GTMETRIX_USERNAME') . ':' . env('GTMETRIX_API_KEY'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
                //curl_setopt($ch, CURLOPT_CAINFO, dirname(__DIR__) . '/data/ca-bundle.crt');
                $result     = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlErrNo  = curl_errno($ch);
                $curlError  = curl_error($ch);
                curl_close($ch);

                \Log::info(print_r(["Result started to fetch"],true));

                $fileName = '/uploads/gt-matrix/' . $value->test_id . '.pdf';
                $file     = public_path() . $fileName;
                file_put_contents($file, $result);
                $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();

                \Log::info(print_r(["Store view found",$storeview],true));

                if ($storeview) {
                    $storeview->pdf_file = $fileName;
                    $storeview->save();
                }
            }
            if (!empty($resources['pagespeed'])) {
                $ch = curl_init($resources['pagespeed']);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, env('GTMETRIX_USERNAME') . ':' . env('GTMETRIX_API_KEY'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
                //curl_setopt($ch, CURLOPT_CAINFO, dirname(__DIR__) . '/data/ca-bundle.crt');
                $result     = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlErrNo  = curl_errno($ch);
                $curlError  = curl_error($ch);
                curl_close($ch);

                \Log::info(print_r(["Result started to fetch pagespeed json"],true));

                $fileName = '/uploads/gt-matrix/' . $value->test_id . '_pagespeed.json';
                $file     = public_path() . $fileName;
                file_put_contents($file, $result);
                $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();

                \Log::info(print_r(["Store view found",$storeview],true));

                if ($storeview) {
                    $storeview->pagespeed_json = $fileName;
                    $storeview->save();
                }
            }
            if (!empty($resources['yslow'])) {
                $ch = curl_init($resources['yslow']);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, env('GTMETRIX_USERNAME') . ':' . env('GTMETRIX_API_KEY'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
                //curl_setopt($ch, CURLOPT_CAINFO, dirname(__DIR__) . '/data/ca-bundle.crt');
                $result     = strip_tags(curl_exec($ch));
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlErrNo  = curl_errno($ch);
                $curlError  = curl_error($ch);
                curl_close($ch);

                \Log::info(print_r(["Result started to fetch yslow json"],true));

                $fileName = '/uploads/gt-matrix/' . $value->test_id . '_yslow.json';
                $file     = public_path() . $fileName;
                file_put_contents($file, $result);
                $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();

                \Log::info(print_r(["Store view found",$storeview],true));

                if ($storeview) {
                    $storeview->yslow_json = $fileName;
                    $storeview->save();
                }
            }

        }

        \Log::info('GTMetrix :: Report cron complete ');
        $report->update(['end_time' => Carbon::now()]);

        /*} catch (\Exception $e) {
    \Log::error($this->signature.' :: '.$e->getMessage() );
    \App\CronJob::insertLastError($this->signature, $e->getMessage());
    }*/
    }
}
