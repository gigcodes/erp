<?php

namespace App\Console\Commands;

use DB;
use App\Scraper;
use App\ScrapLog;
use Illuminate\Http\Request;
use Illuminate\Console\Command;

class FetchScrapeMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:FetchScrapeMissing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Scrape Missing Quatity';

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
        $date = date('Y-m-d');
        $scrapped_query = DB::table('scraped_products as p')
            ->selectRaw(' count(*) as total_product ,
				   sum(CASE WHEN p.category = ""
			           OR p.category IS NULL THEN 1 ELSE 0 END) AS missing_category,
			       sum(CASE WHEN p.color = ""
			           OR p.color IS NULL THEN 1 ELSE 0 END) AS missing_color,
			       sum(CASE WHEN p.composition = ""
			           OR p.composition IS NULL THEN 1 ELSE 0 END) AS missing_composition,
			       sum(CASE WHEN p.title = ""
			           OR p.title IS NULL THEN 1 ELSE 0 END) AS missing_name,
			       sum(CASE WHEN p.description = ""
			           OR p.description IS NULL THEN 1 ELSE 0 END) AS missing_short_description,
			       sum(CASE WHEN p.price = ""
			           OR p.price IS NULL THEN 1 ELSE 0 END) AS missing_price,
			       sum(CASE WHEN p.size = ""
			           OR p.size IS NULL THEN 1 ELSE 0 END) AS missing_size,
			       `p`.`supplier`,
			       `p`.`id`,
			       `p`.`website`
				')
            ->where('p.website', '<>', '')
            ->whereRaw(" date(created_at) = date('$date') ");
        $scrapped_query = $scrapped_query->groupBy('p.website')->havingRaw('missing_category > 1 or missing_color > 1 or missing_composition > 1 or missing_name > 1 or missing_short_description >1 ');

        $scrappedReportData = $scrapped_query->get();
        foreach ($scrappedReportData as $d) {
            $missingdata = '';
            $data = [
                'website' => $d->website,
                'total_product' => $d->total_product,
                'missing_category' => $d->missing_category,
                'missing_color' => $d->missing_color,
                'missing_composition' => $d->missing_composition,
                'missing_name' => $d->missing_name,
                'missing_short_description' => $d->missing_short_description,
                'missing_price' => $d->missing_price,
                'missing_size' => $d->missing_size,
                'created_at' => date('Y-m-d H:m'),
            ];

            $missingdata .= 'Total Product - ' . $d->total_product . ', ';
            $missingdata .= 'Missing Category - ' . $d->missing_category . ', ';
            $missingdata .= 'Missing Color - ' . $d->missing_color . ', ';
            $missingdata .= 'Missing Composition - ' . $d->missing_composition . ', ';
            $missingdata .= 'Missing Name - ' . $d->missing_name . ', ';
            $missingdata .= 'Missing Short Description - ' . $d->missing_short_description . ', ';
            $missingdata .= 'Missing Price - ' . $d->missing_price . ', ';
            $missingdata .= 'Missing Size - ' . $d->missing_size . ', ';

            $scrapers = Scraper::where('scraper_name', $d->website)->get();
            foreach ($scrapers as $scrapperDetails) {
                $hasAssignedIssue = \App\DeveloperTask::where('scraper_id', $scrapperDetails->id)
                    ->whereNotNull('assigned_to')->where('is_resolved', 0)->first();
                if ($hasAssignedIssue != null) {
                    $userName = \App\User::where('id', $hasAssignedIssue->assigned_to)->pluck('name')->first();
                    $requestData = new Request();
                    $requestData->setMethod('POST');
                    $requestData->request->add(['issue_id' => $hasAssignedIssue->id, 'message' => 'Missing data', 'status' => 1]);
                    ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'missing data', 'log_messages' => $missingdata]);
                    try {
                        app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($requestData, 'issue');
                        ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'missing data', 'log_messages' => $missingdata . ' and message sent to ' . $userName]);
                    } catch (\Exception $e) {
                        ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'missing data', 'log_messages' => "Coundn't send message to " . $userName]);
                    }
                } else {
                    ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'missing data', 'log_messages' => 'Not assigned to any user']);
                }
            }

            $s = DB::table('scraped_product_missing_log')->where('website', $d->website)
                ->whereRaw(" date(created_at) = date('$date') ")->first();
            if ($s) {
                DB::table('scraped_product_missing_log')->where('id', $s->id)->update($data);
            } else {
                DB::table('scraped_product_missing_log')->insert($data);
            }
        }
    }
}
