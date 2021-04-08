<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteDatabaseItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:database-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete database items';

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
        //
        $datebeforetenday = date("Y-m-d",strtotime("-10 day"));
        // delete scraper position history
        \App\ScraperPositionHistory::whereDate("created_at","<=",$datebeforetenday)->delete();
        // delete scraper screenshot
        \App\ScraperScreenshotHistory::whereDate("created_at","<=",$datebeforetenday)->delete();

    }
}


