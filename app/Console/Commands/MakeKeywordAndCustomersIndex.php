<?php

namespace App\Console\Commands;

use App\Customer;
use Carbon\Carbon;
use App\CronJobReport;
use App\Helpers\LogHelper;
use Illuminate\Console\Command;
use App\BulkCustomerRepliesKeyword;
use App\Services\BulkCustomerMessage\KeywordsChecker;

class MakeKeywordAndCustomersIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:bulk-messaging-keyword-customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $checker;

    /**
     * Create a new command instance.
     */
    public function __construct(KeywordsChecker $checker)
    {
        parent::__construct();

        $this->checker = $checker;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            BulkCustomerRepliesKeyword::where('is_processed', 1)->chunk(5000, function ($keywords) {
                $customers = Customer::where('is_categorized_for_bulk_messages', 0)->get();
                $this->checker->assignCustomerAndKeyword($keywords, $customers);
            });

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'BulkCustomerRepliesKeyword model query finished']);

            $keywords  = BulkCustomerRepliesKeyword::where('is_processed', 0)->get();
            $customers = Customer::all();
            $this->checker->assignCustomerAndKeyword($keywords, $customers);

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Assign customers and keyword process was finished']);

            BulkCustomerRepliesKeyword::where('is_processed', 0)->update([
                'is_processed' => 1,
            ]);

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
