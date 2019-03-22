<?php

namespace App\Console;

use App\Console\Commands\DoubleFProductDetailScraper;
use App\Console\Commands\DoubleFScraper;
use App\Console\Commands\EnrichWiseProducts;
use App\Console\Commands\GetGebnegozionlineProductDetails;
use App\Console\Commands\GetGebnegozionlineProductEntries;
use App\Console\Commands\MakeApprovedImagesSchedule;
use App\Console\Commands\PostScheduledMedia;
use App\Console\Commands\CheckLogins;
use App\Console\Commands\AutoInterestMessage;
use App\Console\Commands\AutoReminder;
//use App\Console\Commands\SyncInstagramMessage;
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
use App\Console\Commands\SendVoucherReminder;

use App\Http\Controllers\MagentoController;
use App\Http\Controllers\NotificaitonContoller;
use App\Http\Controllers\NotificationQueueController;
use App\NotificationQueue;
use App\Benchmark;
use App\Task;
use Carbon\Carbon;
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
        RunMessageQueue::class,
        SendVoucherReminder::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

      $schedule->call(function() {
        $benchmark = Benchmark::orderBy('for_date', 'DESC')->first()->toArray();
        $tasks = Task::where('is_statutory', 0 )->whereNotNull('is_completed')->get();

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
      })->dailyAt('00:00');

	    $schedule->call(function () {
		    \Log::debug('deQueueNotficationNew Start');
	    	NotificationQueueController::deQueueNotficationNew();
	    })->everyMinute();

         $schedule->call(function () {
//            MagentoController::get_magento_orders();
        })->hourly();

        $schedule->command('post:scheduled-media')
            ->everyMinute();

        $schedule->command('check:user-logins')->everyMinute();
        $schedule->command('send:image-interest')->cron('0 07 * * 1,4'); // runs at 7AM Monday and Thursday
        $schedule->command('send:auto-reminder')->hourly();

        $schedule->command('gebnegozionline:get-products-list')
            ->hourly()
            ->withoutOverlapping()
        ;

        $schedule->command('gebnegozionline:get-products-detail')
            ->hourly()
            ->withoutOverlapping()
        ;

        $schedule->command('enrich:wiseboutique')
            ->everyMinute()
            ->withoutOverlapping()
        ;

        $schedule->command('wiseboutique:get-product-details')
            ->hourly()
            ->withoutOverlapping()
        ;

        $schedule->command('scrap:doublef-list')
            ->everyFifteenMinutes()
            ->withoutOverlapping()
        ;

        $schedule->command('doublef:get-product-details')
            ->everyFifteenMinutes()
            ->withoutOverlapping()
        ;

        $schedule->command('scrap:wiseboutique-list')
            ->hourly()
            ->withoutOverlapping()
        ;

        $schedule->command('image:create-schedule')->dailyAt(14);
        $schedule->command('image:create-schedule')->dailyAt(17);
        $schedule->command('image:create-schedule')->dailyAt(20);
        $schedule->command('gnb:get-sku')->everyMinute();
        // $schedule->command('create:scraped-products')->everyMinute();

//        $schedule->command('sync:instagram-messages')
//            ->everyMinute();

          $schedule->command('send:hourly-reports')->dailyAt('12:00')->timezone('Asia/Kolkata');
        $schedule->command('send:hourly-reports')->dailyAt('15:30')->timezone('Asia/Kolkata');
        $schedule->command('send:hourly-reports')->dailyAt('17:30')->timezone('Asia/Kolkata');
        $schedule->command('run:message-queues')->everyMinute()->withoutOverlapping();

        // Voucher Reminders
        $schedule->command('send:voucher-reminder')->daily();
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
