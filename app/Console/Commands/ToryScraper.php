<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Scrap\ToryScraper as Tory;

class ToryScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:tory-list';
    private $scraper;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @param Tory $scraper
     */
    public function __construct(Tory $scraper)
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
        $this->scraper->scrap('clothing');
        $this->scraper->scrap('shoes');
        $this->scraper->scrap('bags');
    }
}
