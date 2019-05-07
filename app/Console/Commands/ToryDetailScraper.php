<?php

namespace App\Console\Commands;

use App\Services\Scrap\ToryDetailsScraper;
use Illuminate\Console\Command;

class ToryDetailScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tory:get-product-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private $scraper;

    /**
     * Create a new command instance.
     * @param ToryDetailsScraper $scraper
     */
    public function __construct(ToryDetailsScraper $scraper)
    {
        $this->scraper = $scraper;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $letters = env('SCRAP_ALPHAS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        if (strpos($letters, 'T') === false) {
            return;
        }
        $this->scraper->scrap();
    }
}
