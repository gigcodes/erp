<?php

namespace App\Console\Commands;

use App\Services\Scrap\DoubleFProductDetailsScraper;
use Illuminate\Console\Command;

class DoubleFProductDetailScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doublef:get-product-details';

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
     * @param DoubleFProductDetailsScraper $scraper
     */
    public function __construct(DoubleFProductDetailsScraper $scraper)
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
        $this->scraper->scrap();
    }
}
