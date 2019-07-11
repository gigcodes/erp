<?php

namespace App\Console\Commands;

use App\Services\Scrap\WiseBoutiqueProductDetailsScraper;
use Illuminate\Console\Command;

class WiseboutiqueProductDetailScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wiseboutique:get-product-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private $scraper;

    /**
     * Create a new command instance.
     *
     * @param WiseBoutiqueProductDetailsScraper $scraper
     */
    public function __construct(WiseBoutiqueProductDetailsScraper $scraper)
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
