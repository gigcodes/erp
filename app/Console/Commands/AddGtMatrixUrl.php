<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\Setting;
use App\StoreViewsGTMetrix;
use App\WebsiteStoreView;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AddGtMatrixUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gt-matrix:add-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add GT matrix url';

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
        //
        try {
            \Log::info('GTMetrix :: Daily cron start ');
            $cronStatus = Setting::where('name', "gtmetrixCronStatus")->get()->first();

            if (!empty($cronStatus) && $cronStatus->val == 'stop') {
                \Log::info('GTMetrix :: stopped');
                return false;
            }

            $cronType    = Setting::where('name', "gtmetrixCronType")->get()->first();
            $cronRunTime = Setting::where('name', "gtmetrixCronRunDate")->get()->first();

            if (!empty($cronRunTime)) {

                if ($cronRunTime->val != now()->format('Y-m-d') && $cronType->val != 'daily') {
                    \Log::info('GTMetrix :: cron run time false');
                    return false;
                }
            }

            if (!empty($cronType) && $cronType->val == 'weekly') {
                $nextDate = now()->addWeeks(1)->format('Y-m-d');
            } else {
                $nextDate = now()->tomorrow()->format('Y-m-d');
            }
            $this->nextCronRunTime($nextDate);
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $storeViewList = WebsiteStoreView::whereNotNull('website_store_id')
            // ->where('website_store_views.id',977)
                ->join("website_stores as ws", "ws.id", "website_store_views.website_store_id")
                ->join("websites as w", "w.id", "ws.website_id")
                ->join("store_websites as sw", "sw.id", "w.store_website_id")
                ->select("website_store_views.code", "website_store_views.id", "sw.website", "sw.magento_url")
                ->get()->toArray();

            $request_too_many_pending = false;

            foreach ($storeViewList as $value) {
                $webite = $value['magento_url'] . '/' . $value['code'];
                $create = [
                    'store_view_id' => $value['id'],
                    'status'        => 'not_queued',
                    'website_url'   => $webite,
                ];
                StoreViewsGTMetrix::create($create);
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \Log::error('GTMetrix :: ' . $e->getMessage());
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
