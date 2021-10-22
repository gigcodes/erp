<?php

namespace App\Console\Commands;

use App\ScrapLog;
use DB;
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
        $scrapped_query = $scrapped_query->groupBy('p.website')->havingRaw("missing_category > 1 or missing_color > 1 or missing_composition > 1 or missing_name > 1 or missing_short_description >1 ");

        $scrappedReportData = $scrapped_query->get();
        foreach ($scrappedReportData as $d) {
            if ($d->missing_category) {
                ScrapLog::create(['scraper_id' => $d->id, 'log_messages' => "Missing Category"]);
            }
            if ($d->missing_color) {
                ScrapLog::create(['scraper_id' => $d->id, 'log_messages' => "Missing Color"]);
            }
            if ($d->missing_composition) {
                ScrapLog::create(['scraper_id' => $d->id, 'log_messages' => "Missing Composition"]);
            }
            if ($d->missing_name) {
                ScrapLog::create(['scraper_id' => $d->id, 'log_messages' => "Missing Name"]);
            }
            if ($d->missing_short_description) {
                ScrapLog::create(['scraper_id' => $d->id, 'log_messages' => "Missing Short Description"]);
            }
            if ($d->missing_price) {
                ScrapLog::create(['scraper_id' => $d->id, 'log_messages' => "Missing Price"]);
            }
            if ($d->missing_size) {
                ScrapLog::create(['scraper_id' => $d->id, 'log_messages' => "Missing Size"]);
            }
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
