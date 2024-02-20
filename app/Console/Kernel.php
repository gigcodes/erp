<?php

namespace App\Console;

use App\Jobs\CheckAppointment;
use App\Console\Commands\ParseLog;
use App\Console\Commands\ScrapLogs;
use App\Console\Commands\RoutesSync;
use App\Console\Commands\BuildStatus;
use App\Console\Commands\CheckLogins;
use App\Console\Commands\FetchEmails;
use App\Console\Commands\RunErpLeads;
use App\Console\Commands\StoreBrands;
use App\Console\Commands\UserPayment;
use App\Console\Commands\VisitorLogs;
use App\Console\Commands\ZabbixStore;
use App\Console\Commands\AutoReminder;
use App\Console\Commands\DevAPIReport;
use App\Console\Commands\GetPytonLogs;
use App\Console\Commands\RunErpEvents;
use App\Console\Commands\ScheduleList;
use App\Console\Commands\TwilioErrors;
use App\Console\Commands\AddGroupTheme;
use App\Console\Commands\AutoMessenger;
use App\Console\Commands\SkuErrorCount;
use App\Console\Commands\DoubleFScraper;
use App\Console\Commands\FetchAllEmails;
use App\Console\Commands\GtMetrixReport;
use App\Console\Commands\StoreLiveChats;
use App\Console\Commands\TwilioCallLogs;
use App\Console\Commands\UpdateGnbPrice;
use App\Console\Commands\UpdateSkuInGnb;
use App\Console\Commands\VarnishRecords;
use App\Console\Commands\ChannelDataSync;
use App\Console\Commands\CreateMailBoxes;
use App\Console\Commands\DatabaseLogCron;
use App\Console\Commands\MonitorCronJobs;
use App\Console\Commands\RunMessageQueue;
use App\Console\Commands\scrappersImages;
use App\Console\Commands\UpdateCharities;
use App\Console\Commands\UpdateInventory;
use App\Console\Commands\ZabbixHostItems;
use App\Console\Commands\CheckScrapersLog;
use App\Console\Commands\DocumentReciever;
use App\Console\Commands\InsertPleskEmail;
use App\Console\Commands\LogScraperDelete;
use App\Console\Commands\MagentoReportLog;
use App\Console\Commands\MessageScheduler;
use App\Console\Commands\MovePlannedTasks;
use App\Console\Commands\ProjectDirectory;
use App\Console\Commands\SendDailyReports;
use App\Console\Commands\SonarQubeRecords;
use App\Console\Commands\WebsiteCreateLog;
use App\Console\Commands\AddRoutesToGroups;
use App\Console\Commands\CheckEmailsErrors;
use App\Console\Commands\DeleteGnbProducts;
use App\Console\Commands\ErrorAlertMessage;
use App\Console\Commands\ResetDailyPlanner;
use App\Console\Commands\SendHourlyReports;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\CacheMasterControl;
use App\Console\Commands\DeleteChatMessages;
use App\Console\Commands\DeleteWiseProducts;
use App\Console\Commands\EnrichWiseProducts;
use App\Console\Commands\RunGoogleAnalytics;
use App\Console\Commands\SaveProductsImages;
use App\Console\Commands\SendQueuedMessages;
use App\Console\Commands\SendRecurringTasks;
use App\Console\Commands\UpdateCronSchedule;
use App\Console\Commands\UpdateWiseCategory;
use App\Console\Commands\UpdateWiseProducts;
use App\Console\Commands\AutoInterestMessage;
use App\Console\Commands\CheckMessagesErrors;
use App\Console\Commands\CheckWhatsAppActive;
use App\Console\Commands\MailingListSendMail;
use App\Console\Commands\SendEmailNewsletter;
use App\Console\Commands\SendVoucherReminder;
use App\Console\Commands\ZabbixProblemImport;
use App\Console\Commands\AuthenticateWhatsapp;
use App\Console\Commands\FetchMagentoCronData;
use App\Console\Commands\productActivityStore;
use App\Console\Commands\RemoveScrapperImages;
use App\Console\Commands\UpdateDoubleProducts;
use App\Console\Commands\CompareScrapperImages;
use App\Console\Commands\CreateScrapedProducts;
use App\Console\Commands\getLiveChatIncTickets;
use App\Console\Commands\GetOrdersFromnMagento;
use App\Console\Commands\ImageBarcodeGenerator;
use App\Console\Commands\InfluencerDescription;
use App\Console\Commands\RecieveResourceImages;
use App\Console\Commands\scrappersImagesDelete;
use App\Console\Commands\SendActivitiesListing;
use App\Console\Commands\SendProductSuggestion;
use App\Console\Commands\SendTasksTimeReminder;
use App\Console\Commands\UpdateLanguageToGroup;
use App\Console\Commands\WayBillTrackHistories;
use App\Console\Commands\ZoomMeetingRecordings;
use App\Console\Commands\FetchStoreWebsiteOrder;
use App\Console\Commands\SendDailyPlannerReport;
use App\Console\Commands\SetTemplatesForProduct;
use App\Console\Commands\TwillioMessagesCommand;
use App\Console\Commands\CustomerListToEmailLead;
use App\Console\Commands\HubstuffActivityCommand;
use App\Console\Commands\MagentoSettingAddUpdate;
use App\Console\Commands\NegativeCouponResponses;
use App\Console\Commands\UploadProductsToMagento;
use App\Console\Commands\AssetsManagerPaymentCron;
use App\Console\Commands\RunPriorityKeywordSearch;
use App\Console\Commands\SendAutoReplyToCustomers;
use App\Console\Commands\SendDailyLearningReports;
use App\Console\Commands\SyncCustomersFromMagento;
use App\Console\Commands\UpdatePricesWithDecimals;
use App\Console\Commands\AccountHubstaffActivities;
use App\Console\Commands\FixCategoryNameBySupplier;
use App\Console\Commands\NumberOfImageCroppedCheck;
use App\Console\Commands\SaveZoomMeetingRecordings;
use App\Console\Commands\SendPendingTasksReminders;
use App\Console\Commands\DailyHubstaffActivityLevel;
use App\Console\Commands\DeleteStoreWebsiteCategory;
use App\Console\Commands\GenerateProductPricingJson;
use App\Console\Commands\MakeApprovedImagesSchedule;
use App\Console\Commands\UpdateMagentoProductStatus;
use App\Console\Commands\ConnectGoogleClientAccounts;
use App\Console\Commands\DoubleFProductDetailScraper;
use App\Console\Commands\UpdateCustomerSizeFromOrder;
use App\Console\Commands\UpdateImageBarcodeGenerator;
use App\Console\Commands\ZoomMeetingDeleteRecordings;
use App\Console\Commands\DailyTimeDoctorActivityLevel;
use App\Console\Commands\MakeKeywordAndCustomersIndex;
use App\Console\Commands\SendDailyPlannerNotification;
use App\Console\Commands\SendQueuePendingChatMessages;
use App\Console\Commands\ProjectFileManagerDateAndSize;
use App\Console\Commands\GoogleWebMasterFetchAllRecords;
use App\Console\Commands\SendEventNotificationBefore2hr;
use App\Console\Commands\SendEventNotificationBefore24hr;
use App\Console\Commands\UpdateProductInformationFromCsv;
use App\Http\Controllers\Marketing\MailinglistController;
use App\Console\Commands\GetGebnegozionlineProductDetails;
use App\Console\Commands\GetGebnegozionlineProductEntries;
use App\Console\Commands\IncrementFrequencyWhatsappConfig;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\FlagCustomersIfTheyHaveAComplaint;
use App\Console\Commands\SendQueuePendingChatMessagesGroup;
use App\Console\Commands\CreateErpLeadFromCancellationOrder;
use App\Console\Commands\GetMostUsedWordsInCustomerMessages;
use App\Console\Commands\SendReminderToTaskIfTheyHaventReplied;
use App\Console\Commands\SendReminderToVendorIfTheyHaventReplied;
use App\Console\Commands\StoreChatMessagesToAutoCompleteMessages;
use App\Console\Commands\SendMessageToUserIfTheirTaskIsNotComplete;
use App\Console\Commands\SendReminderToCustomerIfTheyHaventReplied;
use App\Console\Commands\SendReminderToSupplierIfTheyHaventReplied;
use App\Console\Commands\UpdateShoeAndClothingSizeFromChatMessages;
use App\Console\Commands\SendReminderToDubbizlesIfTheyHaventReplied;
use App\Console\Commands\GetGebnegozionlineProductDetailsWithEmulator;
use App\Console\Commands\SendReminderToDevelopmentIfTheyHaventReplied;
use seo2websites\ErpExcelImporter\Console\Commands\EmailExcelImporter;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        FetchMagentoCronData::class,
        GoogleWebMasterFetchAllRecords::class,
        CheckLogins::class,
        GetGebnegozionlineProductDetails::class,
        GetGebnegozionlineProductEntries::class,
        AutoInterestMessage::class,
        AutoReminder::class,
        AutoMessenger::class,
        FetchEmails::class,
        TwilioErrors::class,
        FetchAllEmails::class,
        CheckEmailsErrors::class,
        MessageScheduler::class,
        SendRecurringTasks::class,
        SendTasksTimeReminder::class,
        CheckMessagesErrors::class,
        SendProductSuggestion::class,
        SendActivitiesListing::class,
        SendPendingTasksReminders::class,
        MakeApprovedImagesSchedule::class,
        UpdateSkuInGnb::class,
        CreateScrapedProducts::class,
        UpdateGnbPrice::class,
        UpdatePricesWithDecimals::class,
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
        MovePlannedTasks::class,
        SendDailyPlannerReport::class,
        ResetDailyPlanner::class,
        SendMessageToUserIfTheirTaskIsNotComplete::class,
        SendReminderToCustomerIfTheyHaventReplied::class,
        UploadProductsToMagento::class,
        SendAutoReplyToCustomers::class,
        FixCategoryNameBySupplier::class,
        TwilioCallLogs::class,
        ZoomMeetingRecordings::class,
        ZoomMeetingDeleteRecordings::class,
        SaveZoomMeetingRecordings::class,
        FlagCustomersIfTheyHaveAComplaint::class,
        MakeKeywordAndCustomersIndex::class,
        GetMostUsedWordsInCustomerMessages::class,
        SendReminderToCustomerIfTheyHaventReplied::class,
        SendReminderToSupplierIfTheyHaventReplied::class,
        SendReminderToVendorIfTheyHaventReplied::class,
        SendReminderToTaskIfTheyHaventReplied::class,
        SendReminderToDevelopmentIfTheyHaventReplied::class,
        SendReminderToDubbizlesIfTheyHaventReplied::class,
        UpdateShoeAndClothingSizeFromChatMessages::class,
        UpdateCustomerSizeFromOrder::class,
        DocumentReciever::class,
        RecieveResourceImages::class,
        CreateErpLeadFromCancellationOrder::class,
        SendQueuePendingChatMessages::class,
        SendQueuePendingChatMessagesGroup::class,
        ScheduleList::class,
        CheckWhatsAppActive::class,
        IncrementFrequencyWhatsappConfig::class,
        UpdateCronSchedule::class,
        RunErpEvents::class,
        RunErpLeads::class,
        ParseLog::class,
        SkuErrorCount::class,
        VisitorLogs::class,
        ImageBarcodeGenerator::class,
        UpdateImageBarcodeGenerator::class,
        GetOrdersFromnMagento::class,
        SyncCustomersFromMagento::class,
        NumberOfImageCroppedCheck::class,
        SetTemplatesForProduct::class,
        CheckScrapersLog::class,
        StoreBrands::class,
        MailingListSendMail::class,
        StoreLiveChats::class,
        RunPriorityKeywordSearch::class,
        CacheMasterControl::class,
        InfluencerDescription::class,
        SendEventNotificationBefore24hr::class,
        SendEventNotificationBefore2hr::class,
        AccountHubstaffActivities::class,
        DailyHubstaffActivityLevel::class,
        DailyTimeDoctorActivityLevel::class,
        EmailExcelImporter::class,
        GenerateProductPricingJson::class,
        FetchStoreWebsiteOrder::class,
        UserPayment::class,
        ScrapLogs::class,
        AuthenticateWhatsapp::class,
        getLiveChatIncTickets::class,
        RoutesSync::class,
        DeleteChatMessages::class,
        WayBillTrackHistories::class,
        CustomerListToEmailLead::class,
        WayBillTrackHistories::class,
        ProjectDirectory::class,
        LogScraperDelete::class,
        AssetsManagerPaymentCron::class,
        SendEmailNewsletter::class,
        DeleteStoreWebsiteCategory::class,
        RunGoogleAnalytics::class,
        RunGoogleAnalytics::class,
        scrappersImages::class,
        scrappersImagesDelete::class,
        productActivityStore::class,
        ErrorAlertMessage::class,
        SendDailyReports::class,
        SendDailyLearningReports::class,
        SendDailyPlannerNotification::class,
        InsertPleskEmail::class,
        StoreChatMessagesToAutoCompleteMessages::class,
        RemoveScrapperImages::class,
        AddGroupTheme::class,
        UpdateProductInformationFromCsv::class,
        ProjectFileManagerDateAndSize::class,
        AddRoutesToGroups::class,
        ConnectGoogleClientAccounts::class,
        UpdateCharities::class,
        UpdateLanguageToGroup::class,
        BuildStatus::class,
        GetPytonLogs::class,
        HubstuffActivityCommand::class,
        WebsiteCreateLog::class,
        GtMetrixReport::class,
        MagentoReportLog::class,
        MagentoSettingAddUpdate::class,
        NegativeCouponResponses::class,
        ZabbixStore::class,
        ZabbixHostItems::class,
        ZabbixProblemImport::class,
        SendQueuedMessages::class,
        DatabaseLogCron::class,
        TwillioMessagesCommand::class,
        DevAPIReport::class,
        ChannelDataSync::class,
        CreateMailBoxes::class,
        SonarQubeRecords::class,
        VarnishRecords::class,
        CompareScrapperImages::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            MailinglistController::sendAutoEmails();
        })->hourly();

        //Telescope Remove Logs Every 72Hrs
        $schedule->command('telescope:prune --hours=72')->daily();
        $schedule->command('reindex:messages')->dailyAt('00:00');
        $schedule->command('store:zabbix')->everyFiveMinutes();
        $schedule->command('zabbix:problem')->everyFiveMinutes();
        $schedule->command('store:zabbixhostitems')->everyFiveMinutes();
        $schedule->command('insert-sonar-qube')->dailyAt('23:58');
        $schedule->command('insert-varnish-records')->everyFiveMinutes();
        $schedule->command('compare-scrapper-images')->dailyAt('23:58');

        $schedule->call(function () {
            \Log::info('kernal triggered');
            dispatch(new CheckAppointment());
        })->everyMinute();
    }

    /**`
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
