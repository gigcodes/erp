<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Scrap\DoubleFScraper as DoubleF;

class DoubleFScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:doublef-list';
    private $scraper;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @param DoubleF $scraper
     */
    public function __construct(DoubleF $scraper)
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
        $this->scraper->scrap('man');
        $this->scraper->scrap('woman');
    }
}
