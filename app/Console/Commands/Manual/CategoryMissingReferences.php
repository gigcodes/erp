<?php

namespace App\Console\Commands\Manual;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\CronJobReport;
use App\Loggers\LogScraper;
use Carbon\Carbon;

class CategoryMissingReferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:missing-references';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all the missing references for categories submitted by scrapers';

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

        // Set empty
        $arrUnknown = [];

        // Get all categories from log_scraper
        $categories = LogScraper::whereNotNull('category')->get(['category']);
        //$categories = LogScraper::whereNotNull('category')->where('url', 'LIKE', '%farfetch%')->get(['category']);

        // Loop over result
        foreach ( $categories as $category ) {
            // Get product to update
            $arrCategories = unserialize($category->category);

            // Is it an array?
            if ( is_array($arrCategories) ) {
                // Get last category
                $lastCategory = array_pop($arrCategories);

                // Check if the category is in the references
                $exists = Category::where('title', '=', $lastCategory)->first();

                // Exists?
                if ($exists == null) {
                    $exists = Category::where('references', 'LIKE', '%' . $lastCategory . '%')->first();
                }

                // Still null
                if ($exists == null) {
                    $arrUnknown[] = $lastCategory;
                }
            }
        }

        // Make array unique
        $arrUnknown = array_unique($arrUnknown);

        // Loop over arrUnknown
        foreach ( $arrUnknown as $unknown ) {
            echo $unknown . "\n";
        }

        $report->update(['end_time' => Carbon:: now()]);
    }
}
