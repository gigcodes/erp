<?php

namespace App\Console;

use App\Http\Controllers\MagentoController;
use App\Http\Controllers\NotificaitonContoller;
use App\Http\Controllers\NotificationQueueController;
use App\NotificationQueue;
use App\Benchmark;
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
        //
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

        if ($benchmark['for_date'] != date('Y-m-d')) {
          $benchmark['for_date'] = date('Y-m-d');
          Benchmark::create($benchmark);
        }
      })->dailyAt('00:00');

	    $schedule->call(function () {
		    \Log::debug('deQueueNotficationNew Start');
//	    	NotificationQueueController::deQueueNotficationNew();
	    })->everyMinute();

         $schedule->call(function () {
//            MagentoController::get_magento_orders();
        })->hourly();
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
