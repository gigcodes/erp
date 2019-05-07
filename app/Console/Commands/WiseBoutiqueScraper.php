<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Scrap\WiseBoutiqueScraper as WiseBoutiqueScraperService;

class WiseBoutiqueScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:wiseboutique-list';
    private $scraper;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @param WiseBoutiqueScraperService $scraper
     */
    public function __construct(WiseBoutiqueScraperService $scraper)
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
        if (strpos($letters, 'W') === false) {
            return;
        }
        $this->scraper->scrap();
    }
}
