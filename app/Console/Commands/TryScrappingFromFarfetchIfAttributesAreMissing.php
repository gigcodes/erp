<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class TryScrappingFromFarfetchIfAttributesAreMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'farfetch:pull-details-which-are-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $content = $this->getDetailsFromFarfetch('');
        $c = new HtmlPageCrawler($content);
        $data = $c->filter('._659731 div p._87b3a2')->getInnerHtml();

        dd($data);

    }

    public function getDetailsFromFarfetch($url) {
        $request = new Client();
        $response = $request->get('https://www.farfetch.com/ae/shopping/men/balenciaga-rhino-t-shirt-item-13445516.aspx?storeid=10952');

        return $response->getBody()->getContents();
    }

    private function findGoogleSearch($sku) {
        $request = new Client();
        $response = $request->get('');
    }
}
