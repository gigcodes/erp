<?php

namespace App\Console\Commands;

use App\Services\Scrap\GebnegozionlineProductDetailsScraper;
use Illuminate\Console\Command;

class GetGebnegozionlineProductDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gebnegozionline:get-products-detail';

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
     * @param GebnegozionlineProductDetailsScraper $scraper
     */
    public function __construct(GebnegozionlineProductDetailsScraper $scraper)
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
