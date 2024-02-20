<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Scrapermissingdata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Scraper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scraper';

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
        $this->checkdata('scraped_products.title', 'Title');
        $this->checkdata('scraped_products.composition', 'Composition');
        $this->checkdata('scraped_products.description', 'Description');
        $this->checkdata('scraped_products.price', 'Price');
    }

    public function checkdata($field, $title)
    {
        $date = date('Y-m-d');
        $ss = \App\ScrapedProducts::join('scrapers', 'scraped_products.website', 'scrapers.scraper_name')
            ->select(DB::raw('COUNT(*) as totalproduct'), 'scrapers.id as scraper_id', 'scrapers.scraper_name as scraper_name')
            ->groupBy('scrapers.scraper_name')
            ->whereRaw("date(scraped_products.created_at)=date('$date')")
            ->whereRaw(" ( $field is null or $field ='' )")->get();

        foreach ($ss as $s) {
            $msg = $s->scraper_name . ' ' . $s->totalproduct . " Product $title Missing";
            $this->sendmessage($s, $msg);
        }
    }

    public function sendmessage($s, $msg)
    {
        if ($msg != '') {
            $u = \App\DeveloperTask::where('scraper_id', $s->scraper_id)->orderBy('created_at', 'desc')->first();
            if ($u) {
                if ($u->user_id > 0) {
                    $user_id = $u->user_id;
                } else {
                    $user_id = 6;
                }

                $params = [];
                $params['message'] = $msg;
                $params['erp_user'] = $user_id;
                $params['user_id'] = $user_id;
                $params['approved'] = 1;
                $params['status'] = 2;
                $params['developer_task_id'] = $u->id;
                $chat_message = \App\ChatMessage::create($params);

                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add(['user_id' => $user_id, 'developer_task_id' => $u->id, 'message' => $msg, 'status' => 1]);
                app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($requestData, 'developer_task');
            }
        }
    }
}
