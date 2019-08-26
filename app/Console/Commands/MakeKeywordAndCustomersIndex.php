<?php

namespace App\Console\Commands;

use App\BulkCustomerRepliesKeyword;
use App\Customer;
use App\Services\BulkCustomerMessage\KeywordsChecker;
use Illuminate\Console\Command;

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
     *
     * @param KeywordsChecker $checker
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
        BulkCustomerRepliesKeyword::where('is_processed', 1)->chunk(5000, function($keywords) {
            $customers = Customer::where('is_categorized_for_bulk_messages', 0)->get();
            $this->checker->assignCustomerAndKeyword($keywords, $customers);
        });

        $keywords = BulkCustomerRepliesKeyword::where('is_processed', 0)->get();
        $customers = Customer::all();
        $this->checker->assignCustomerAndKeyword($keywords, $customers);
        BulkCustomerRepliesKeyword::where('is_processed', 0)->update([
            'is_processed' => 1
        ]);
    }
}
