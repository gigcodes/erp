<?php

namespace App\Console;

use App\Console\Commands\DoubleFProductDetailScraper;
use App\Console\Commands\DoubleFScraper;
use App\Console\Commands\EnrichWiseProducts;
use App\Console\Commands\GetGebnegozionlineProductDetails;
use App\Console\Commands\GetGebnegozionlineProductDetailsWithEmulator;
use App\Console\Commands\GetGebnegozionlineProductEntries;
use App\Console\Commands\MakeApprovedImagesSchedule;
use App\Console\Commands\PostScheduledMedia;
use App\Console\Commands\CheckLogins;
use App\Console\Commands\AutoInterestMessage;
use App\Console\Commands\AutoReminder;
use App\Console\Commands\AutoMessenger;
use App\Console\Commands\FetchEmails;
use App\Console\Commands\CheckEmailsErrors;
use App\Console\Commands\SaveProductsImages;
use App\Console\Commands\MessageScheduler;
use App\Console\Commands\SendPendingTasksReminders;
use App\Console\Commands\SendRecurringTasks;
use App\Console\Commands\CheckMessagesErrors;
use App\Console\Commands\SendBroadcastMessageToColdLeads;
use App\Console\Commands\SendProductSuggestion;
use App\Console\Commands\SendActivitiesListing;
//use App\Console\Commands\SyncInstagramMessage;
use App\Console\Commands\UpdateInventory;
use App\Console\Commands\UpdateSkuInGnb;
use App\Console\Commands\CreateScrapedProducts;
use App\Console\Commands\WiseboutiqueProductDetailScraper;
use App\Console\Commands\WiseBoutiqueScraper;
use App\Console\Commands\UpdateGnbPrice;
use App\Console\Commands\DeleteGnbProducts;
use App\Console\Commands\DeleteWiseProducts;
use App\Console\Commands\UpdateWiseProducts;
use App\Console\Commands\UpdateWiseCategory;
use App\Console\Commands\UpdateDoubleProducts;

use App\Console\Commands\SendHourlyReports;
use App\Console\Commands\RunMessageQueue;
use App\Console\Commands\MonitorCronJobs;
use App\Console\Commands\SendVoucherReminder;

use App\Console\Commands\UpdateMagentoProductStatus;

use App\Http\Controllers\MagentoController;
use App\Http\Controllers\NotificaitonContoller;
use App\Http\Controllers\NotificationQueueController;
use App\NotificationQueue;
use App\Benchmark;
use App\Task;
use Carbon\Carbon;
use App\CronJobReport;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        PostScheduledMedia::class,
        CheckLogins::class,
//        SyncInstagramMessage::class,
        GetGebnegozionlineProductDetails::class,
        GetGebnegozionlineProductEntries::class,
        AutoInterestMessage::class,
        AutoReminder::class,
        AutoMessenger::class,
        FetchEmails::class,
        CheckEmailsErrors::class,
        MessageScheduler::class,
        SendRecurringTasks::class,
        CheckMessagesErrors::class,
        SendProductSuggestion::class,
        SendActivitiesListing::class,
        SendPendingTasksReminders::class,
        MakeApprovedImagesSchedule::class,
        UpdateSkuInGnb::class,
        CreateScrapedProducts::class,
        WiseBoutiqueScraper::class,
        WiseboutiqueProductDetailScraper::class,
        UpdateGnbPrice::class,
        DeleteGnbProducts::class,
        DeleteWiseProducts::class,
        UpdateWiseProducts::class,
        UpdateWiseCategory::class,
        UpdateDoubleProducts::class,
        EnrichWiseProducts::class,
        DoubleFProductDetailScraper::class,
        DoubleFScraper::class,
        SendHourlyReports::class,
        SaveProductsImages::class,
        RunMessageQueue::class,
        MonitorCronJobs::class,
        SendVoucherReminder::class,
        GetGebnegozionlineProductDetailsWithEmulator::class,
        UpdateInventory::class,
        UpdateMagentoProductStatus::class,
        SendBroadcastMessageToColdLeads::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
      $schedule->call(function() {
        $report = CronJobReport::create([
          'signature' => 'update:benchmark',
          'start_time'  => Carbon::now()
        ]);

        $benchmark = Benchmark::orderBy('for_date', 'DESC')->first()->toArray();
        $tasks = Task::where('is_statutory', 0 )->whereNotNull('is_verified')->get();

        if ($benchmark['for_date'] != date('Y-m-d')) {
          $benchmark['for_date'] = date('Y-m-d');
          Benchmark::create($benchmark);
        }

        foreach ($tasks as $task) {
          $time_diff = Carbon::parse($task->is_completed)->diffInDays(Carbon::now());

          if ($time_diff >= 2) {
            $task->delete();
          }
        }

        $report->update(['end_time' => Carbon:: now()]);
      })->dailyAt('00:00');

  	    $schedule->call(function () {
  		    \Log::debug('deQueueNotficationNew Start');
  	    	NotificationQueueController::deQueueNotficationNew();
  	    })->everyFiveMinutes();

        $schedule->call(function () {
          $report = CronJobReport::create([
            'signature' => 'update:benchmark',
            'start_time'  => Carbon::now()
          ]);

          MagentoController::get_magento_orders();

          $report->update(['end_time' => Carbon:: now()]);
        })->hourly();

        $schedule->command('send:hourly-reports')->dailyAt('12:00')->timezone('Asia/Kolkata');
        $schedule->command('send:hourly-reports')->dailyAt('15:30')->timezone('Asia/Kolkata');
        $schedule->command('send:hourly-reports')->dailyAt('17:30')->timezone('Asia/Kolkata');
        $schedule->command('run:message-queues')->everyFiveMinutes()->between('9:00', '18:00')->withoutOverlapping(10);
        $schedule->command('monitor:cron-jobs')->everyMinute();
//        $schedule->command('cold-leads:send-broadcast-messages')->everyMinute()->withoutOverlapping();
        // $schedule->exec('/usr/local/php72/bin/php-cli artisan queue:work --once --timeout=120')->everyMinute()->withoutOverlapping(3);

        // $schedule->command('save:products-images')->hourly();

        // Voucher Reminders
        // $schedule->command('send:voucher-reminder')->daily();

        // Updates Magento Products status on ERP
        // $schedule->command('update:magento-product-status')->dailyAt(03);

//        $schedule->command('post:scheduled-media')
//            ->everyMinute();

        // $schedule->command('check:user-logins')->everyFiveMinutes();
        $schedule->command('send:image-interest')->cron('0 07 * * 1,4'); // runs at 7AM Monday and Thursday

        // Sends Auto messages
        $schedule->command('send:auto-reminder')->hourly();
        $schedule->command('send:auto-messenger')->hourly();
        // $schedule->command('check:messages-errors')->hourly();
        $schedule->command('send:product-suggestion')->dailyAt('07:00')->timezone('Asia/Kolkata');
        $schedule->command('send:activity-listings')->dailyAt('23:45')->timezone('Asia/Kolkata');
        $schedule->command('run:message-scheduler')->dailyAt('01:00')->timezone('Asia/Kolkata');
        $schedule->command('send:recurring-tasks')->everyFifteenMinutes()->timezone('Asia/Kolkata');
        $schedule->command('send:pending-tasks-reminders')->dailyAt('07:30')->timezone('Asia/Kolkata');

        // Fetches Emails
        $schedule->command('fetch:emails')->everyFifteenMinutes();
        $schedule->command('check:emails-errors')->dailyAt('03:00')->timezone('Asia/Kolkata');

//        $schedule->command('gebnegozionline:get-products-list')
//            ->hourly()
//            ->withoutOverlapping()
//        ;
//
//        $schedule->command('gebnegozionline:get-products-detail')
//            ->everyThirtyMinutes()
//            ->withoutOverlapping()
//        ;

//        $schedule->command('gnb:update-price-via-dusk')
//            ->hourly()
//            ->withoutOverlapping()
//        ;

        // $schedule->command('enrich:wiseboutique')
        //     ->daily()
        //     ->withoutOverlapping()
        // ;

//        $schedule->command('wiseboutique:get-product-details')
//            ->hourly()
//            ->withoutOverlapping()
//        ;
//
//        $schedule->command('scrap:doublef-list')
//            ->hourly()
//            ->withoutOverlapping()
//        ;

//        $schedule->command('doublef:get-product-details')
//            ->hourly()
//            ->withoutOverlapping()
//        ;
//
//        $schedule->command('scrap:wiseboutique-list')
//            ->hourly()
//            ->withoutOverlapping()
//        ;

//        $schedule->command('scrap:tory-list')
//            ->hourly()
//            ->withoutOverlapping()
//        ;
//
//        $schedule->command('tory:get-product-details')
//            ->hourly()
//            ->withoutOverlapping()
//        ;

//        $schedule->command('image:create-schedule')->dailyAt(14);
//        $schedule->command('image:create-schedule')->dailyAt(17);
//        $schedule->command('image:create-schedule')->dailyAt(20);
        // $schedule->command('inventory:refresh-stock')->dailyAt(12);
        // $schedule->command('gnb:get-sku')->hourly();
        // $schedule->command('create:scraped-products')->everyMinute();
//        $schedule->command('gnb:get-sku')->everyMinute();

        // $schedule->command('sync:instagram-messages')
        //     ->hourly();


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
