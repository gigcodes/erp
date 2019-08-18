<?php

namespace App\Console\Commands;

use App\BulkCustomerRepliesKeyword;
use App\ChatMessage;
use App\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        BulkCustomerRepliesKeyword::where('is_processed', 1)->chunk(5000, function($keywords) {
            $customers = Customer::where('is_categorized_for_bulk_messages', 0)->get();
            $this->assignCustomerAndKeyword($keywords, $customers);
        });

        $keywords = BulkCustomerRepliesKeyword::where('is_processed', 0)->get();
        $customers = Customer::all();
        $this->assignCustomerAndKeyword($keywords, $customers);
        BulkCustomerRepliesKeyword::where('is_processed', 0)->update([
            'is_processed' => 1
        ]);


    }

    /**
     * @param $keywords
     * @param $customers
     */
    private function assignCustomerAndKeyword($keywords, $customers): void
    {
        foreach ($customers as $customer) {

            $message = $this->getCustomerMessages($customer);

            if (!$message) {
                continue;
            }

            $this->info($message);

            $dataToInsert = [];

            foreach ($keywords as $keyword) {
                $keywordValue = strtolower($keyword->value);
                $this->warn($message . " => " .$keywordValue);
                if (stripos($message, $keywordValue) !== false) {
                    $dataToInsert[] = ['keyword_id' => $keyword->id, 'customer_id' => $customer->id];
                }

            }

            if ($dataToInsert === []) {
                continue;
            }

            dump($dataToInsert);

            DB::table('bulk_customer_replies_keyword_customer')->insert($dataToInsert);
            $customer->is_categorized_for_bulk_messages = 1;
            $customer->save();
        }
    }

    private function getCustomerMessages($customer)
    {
        $customerFirstMessage = ChatMessage::where('customer_id')->orderBy('id', 'DESC')->first();
        if (!$customerFirstMessage->user_id) {
            return strtolower($customerFirstMessage->message);
        }

        //more remaining..
    }
}
