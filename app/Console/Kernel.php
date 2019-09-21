<?php

namespace App\Console;

use App\Console\Commands\DoubleFProductDetailScraper;
use App\Console\Commands\DoubleFScraper;
use App\Console\Commands\EnrichWiseProducts;
use App\Console\Commands\FixCategoryNameBySupplier;
use App\Console\Commands\FlagCustomersIfTheyHaveAComplaint;
use App\Console\Commands\GetGebnegozionlineProductDetails;
use App\Console\Commands\GetGebnegozionlineProductDetailsWithEmulator;
use App\Console\Commands\GetGebnegozionlineProductEntries;
use App\Console\Commands\GetMostUsedWordsInCustomerMessages;
use App\Console\Commands\GrowInstagramAccounts;
use App\Console\Commands\MakeApprovedImagesSchedule;
use App\Console\Commands\MakeKeywordAndCustomersIndex;
use App\Console\Commands\PostScheduledMedia;
use App\Console\Commands\CheckLogins;
use App\Console\Commands\AutoInterestMessage;
use App\Console\Commands\AutoReminder;
use App\Console\Commands\AutoMessenger;
use App\Console\Commands\FetchEmails;
use App\Console\Commands\CheckEmailsErrors;
use App\Console\Commands\SaveProductsImages;
use App\Console\Commands\MessageScheduler;
use App\Console\Commands\SendAutoReplyToCustomers;
use App\Console\Commands\SendMessageToUserIfTheirTaskIsNotComplete;
use App\Console\Commands\SendPendingTasksReminders;
use App\Console\Commands\SendRecurringTasks;
use App\Console\Commands\CheckMessagesErrors;
use App\Console\Commands\SendBroadcastMessageToColdLeads;
use App\Console\Commands\SendProductSuggestion;
use App\Console\Commands\SendActivitiesListing;
use App\Console\Commands\SendDailyPlannerReport;
//use App\Console\Commands\SyncInstagramMessage;
use App\Console\Commands\SendReminderToCustomerIfTheyHaventReplied;
use App\Console\Commands\SendReminderToDubbizlesIfTheyHaventReplied;
use App\Console\Commands\SendReminderToSupplierIfTheyHaventReplied;
use App\Console\Commands\SendReminderToVendorIfTheyHaventReplied;
use App\Console\Commands\UpdateInventory;
use App\Console\Commands\UpdateSkuInGnb;
use App\Console\Commands\CreateScrapedProducts;
use App\Console\Commands\UploadProductsToMagento;
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

use App\Console\Commands\MovePlannedTasks;
use App\Console\Commands\ResetDailyPlanner;

//use App\Console\Commands\SaveProductsImages;

use App\Console\Commands\UpdateMagentoProductStatus;
use App\Console\Commands\ImportCustomersEmail;
use App\Console\Commands\TwilioCallLogs;
use App\Console\Commands\ZoomMeetingRecordings;
use App\Console\Commands\ZoomMeetingDeleteRecordings;

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
        SendBroadcastMessageToColdLeads::class,
        MovePlannedTasks::class,
        SendDailyPlannerReport::class,
        ResetDailyPlanner::class,
//        SaveProductsImages::class,
        GrowInstagramAccounts::class,
        SendMessageToUserIfTheirTaskIsNotComplete::class,
        SendReminderToCustomerIfTheyHaventReplied::class,
        UploadProductsToMagento::class,
        SendAutoReplyToCustomers::class,
        FixCategoryNameBySupplier::class,
        ImportCustomersEmail::class,
        TwilioCallLogs::class,
        ZoomMeetingRecordings::class,
        ZoomMeetingDeleteRecordings::class,
        FlagCustomersIfTheyHaveAComplaint::class,
        MakeKeywordAndCustomersIndex::class,
        GetMostUsedWordsInCustomerMessages::class,
        SendReminderToCustomerIfTheyHaventReplied::class,
        SendReminderToSupplierIfTheyHaventReplied::class,
        SendReminderToVendorIfTheyHaventReplied::class,
        SendReminderToDubbizlesIfTheyHaventReplied::class

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('reminder:send-to-dubbizle')->everyMinute()->withoutOverlapping()->timezone('Asia/Kolkata');
        $schedule->command('reminder:send-to-vendor')->everyMinute()->withoutOverlapping()->timezone('Asia/Kolkata');
        $schedule->command('reminder:send-to-supplier')->everyMinute()->withoutOverlapping()->timezone('Asia/Kolkata');
        $schedule->command('reminder:send-to-customer')->everyMinute()->withoutOverlapping()->timezone('Asia/Kolkata');

        //This command will set the count of the words used...
        $schedule->command('bulk-customer-message:get-most-used-keywords')->daily();

        //This will run every  five minutes checking and making keyword-customer relationship...
        $schedule->command('index:bulk-messaging-keyword-customer')->everyFiveMinutes()->withoutOverlapping();

        //Flag customer if they have a complaint
        $schedule->command('flag:customers-with-complaints')->daily();

        //This command sends the reply on products if they request...
        $schedule->command('customers:send-auto-reply')->everyFifteenMinutes();

        //assign the category to products, runs twice daily...
        $schedule->command('category:fix-by-supplier')->twiceDaily();

        $schedule->command('message:send-to-users-who-exceeded-limit')->everyThirtyMinutes()->timezone('Asia/Kolkata');


        $schedule->call(function () {
            $report = CronJobReport::create([
                'signature' => 'update:benchmark',
                'start_time' => Carbon::now()
            ]);

            $benchmark = Benchmark::orderBy('for_date', 'DESC')->first()->toArray();
            $tasks = Task::where('is_statutory', 0)->whereNotNull('is_verified')->get();

            if ($benchmark[ 'for_date' ] != date('Y-m-d')) {
                $benchmark[ 'for_date' ] = date('Y-m-d');
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
                'start_time' => Carbon::now()
            ]);

            MagentoController::get_magento_orders();
            //fetched magento orders...

            $report->update(['end_time' => Carbon:: now()]);
        })->hourly();

        $schedule->command('product:replace-text')->everyFiveMinutes();

//        $schedule->command('instagram:grow-accounts')->dailyAt('13:00')->timezone('Asia/Kolkata');
        $schedule->command('send:hourly-reports')->dailyAt('12:00')->timezone('Asia/Kolkata');
        $schedule->command('send:hourly-reports')->dailyAt('15:30')->timezone('Asia/Kolkata');
        $schedule->command('send:hourly-reports')->dailyAt('17:30')->timezone('Asia/Kolkata');
        $schedule->command('run:message-queues')->everyFiveMinutes()->between('07:30', '17:00')->withoutOverlapping(10);
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

        // Tasks
        $schedule->command('send:recurring-tasks')->everyFifteenMinutes()->timezone('Asia/Kolkata');
        $schedule->command('send:pending-tasks-reminders')->dailyAt('07:30')->timezone('Asia/Kolkata');
        $schedule->command('move:planned-tasks')->dailyAt('01:00')->timezone('Asia/Kolkata');

        // Fetches Emails
        $schedule->command('fetch:emails')->everyFifteenMinutes();
        $schedule->command('check:emails-errors')->dailyAt('03:00')->timezone('Asia/Kolkata');

        $schedule->command('send:daily-planner-report')->dailyAt('08:00')->timezone('Asia/Kolkata');
        $schedule->command('send:daily-planner-report')->dailyAt('22:00')->timezone('Asia/Kolkata');
        $schedule->command('reset:daily-planner')->dailyAt('07:30')->timezone('Asia/Kolkata');


        $schedule->command('save:products-images')->cron('0 */3 * * *')->withoutOverlapping()->emailOutputTo('lukas.markeviciuss@gmail.com'); // every 3 hours

        // Update the inventory (every fifteen minutes)
        $schedule->command('inventory:update')->dailyAt('04:00')->timezone('Asia/Dubai');

        // Auto reject listings by empty name, short_description, composition, size and by min/max price (every fifteen minutes)
        $schedule->command('product:reject-if-attribute-is-missing')->everyFifteenMinutes();

         //This command saves the twilio call logs in call_busy_messages table...
        $schedule->command('twilio:allcalls')->everyFifteenMinutes();
        // Saved zoom recordings corresponding to past meetings based on meeting id
        $schedule->command('meeting:getrecordings')->hourly();
        $schedule->command('meeting:deleterecordings')->dailyAt('07:00')->timezone('Asia/Kolkata');

        // Check scrapers
        $schedule->command('scraper:not-running')->hourly()->between('7:00', '23:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
