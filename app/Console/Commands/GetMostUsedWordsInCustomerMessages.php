<?php

namespace App\Console\Commands;

use App\BulkCustomerRepliesKeyword;
use App\ChatMessage;
use App\CronJobReport;
use Illuminate\Console\Command;

class GetMostUsedWordsInCustomerMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk-customer-message:get-most-used-keywords';

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
        $report = CronJobReport::create([
        'signature' => $this->signature,
        'start_time'  => Carbon::now()
        ]);

        ChatMessage::where('is_processed_for_keyword', 0)->where('customer_id', '>', '0')->chunk(1000, function($messages) {
            foreach ($messages as $message) {
                $text = $message->message;

                if (!$text) {
                    $message->is_processed_for_keyword = 1;
                    $message->save();
                }

                $words = explode(' ', $text);

                foreach ($words as $word) {
                    if (strlen(trim($word)) <= 3) {
                        continue;
                    }

                    $this->addOrUpdateCountOfKeyword(trim($word));

                }

            }
        });

        $report->update(['end_time' => Carbon:: now()]);
    }

    private function addOrUpdateCountOfKeyword($word): void
    {

        $keyword = BulkCustomerRepliesKeyword::where('value', $word)->first();

        if ($keyword) {
            ++$keyword->count;
            $keyword->save();
            return;
        }

        $keyword = new BulkCustomerRepliesKeyword();
        $keyword->value = $word;
        $keyword->text_type = 'keyword';
        $keyword->is_manual = 0;
        $keyword->count = 1;
        $keyword->save();
    }
}
