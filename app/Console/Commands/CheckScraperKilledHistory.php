<?php

namespace App\Console\Commands;

use App\CronJobReport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckScraperKilledHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:scraper-killed-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check scraper killed histories';

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
            'start_time' => Carbon::now(),
        ]);

        $path = getenv('SCRAPER_RESTART_PATH') . 'scrap.txt';

        $data = file_get_contents($path);
        $output = explode('.js', $data);

        if (count($output) > 0) {
            foreach ($output as $_data) {
                $scraper_name = trim($_data);
                if ($scraper_name) {
                    $scrapers = \App\Scraper::where("scraper_name", $scraper_name)->get();
                    if ($scrapers) {
                        foreach ($scrapers as $_scrap) {
                            $status = \App\ScraperKilledHistory::create([
                                "scraper_id" => $_scrap->id,
                                "scraper_name" => $_scrap->scraper_name,
                                "comment" => 'Scraper killed',
                            ]);
                        }
                    }
                }

            }
        }

        $report->update(['end_time' => Carbon::now()]);

    }
}
