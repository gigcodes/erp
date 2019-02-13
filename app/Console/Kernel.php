<?php

namespace App\Console;

use App\Console\Commands\GetGebnegozionlineProductDetails;
use App\Console\Commands\GetGebnegozionlineProductEntries;
use App\Console\Commands\MakeApprovedImagesSchedule;
use App\Console\Commands\PostScheduledMedia;
use App\Console\Commands\CheckLogins;
use App\Console\Commands\AutoInterestMessage;
use App\Console\Commands\AutoReminder;
//use App\Console\Commands\SyncInstagramMessage;
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
        MakeApprovedImagesSchedule::class
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

        $schedule->command('image:create-schedule')->dailyAt(14);
        $schedule->command('image:create-schedule')->dailyAt(17);
        $schedule->command('image:create-schedule')->dailyAt(20);

//        $schedule->command('sync:instagram-messages')
//            ->everyMinute();
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
