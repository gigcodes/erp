<?php

namespace App\Console\Commands;

use App\Helpers\LogHelper;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteDatabaseItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:database-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete database items';

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
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $datebeforetenday = date('Y-m-d', strtotime('-10 day'));
            $datebeforefifteenday = date('Y-m-d', strtotime('-15 day'));
            $datebeforethreeday = date('Y-m-d', strtotime('-3 day'));
            // delete scraper position history
            \App\ScraperPositionHistory::whereDate('created_at', '<=', $datebeforetenday)->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraper position history deleted.']);
            // delete scraper screenshot
            \App\ScraperScreenshotHistory::whereDate('created_at', '<=', $datebeforetenday)->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraper screenshot history deleted.']);

            \App\ScraperServerStatusHistory::whereDate('created_at', '<=', $datebeforetenday)->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraper server status history deleted.']);

            \App\LogRequest::whereDate('created_at', '<=', $datebeforethreeday)->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Log request deleted.']);

            \seo2websites\GoogleVision\LogGoogleVision::whereDate('created_at', '<=', $datebeforefifteenday)->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Log google vision deleted.']);

            \App\Loggers\LogScraper::where('created_at', '<=', Carbon::now()->subDays(15)->toDateTimeString())->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Log scraper deleted.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch(\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
