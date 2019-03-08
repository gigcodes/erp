<?php

namespace App\Console\Commands;

use App\ScrapEntries;
use App\Services\Bots\WebsiteEmulator;
use Carbon\Carbon;
use Illuminate\Console\Command;


class GetGebnegozionlineProductDetailsWithEmulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:traffic-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $country;
    protected $IP;

    public function handle(): void
    {
        $products = ScrapEntries::where('is_scraped', 0)->where('is_product_page', 1)->where('site_name', 'GNB')->take(250)->get();
        foreach ($products as $product) {
            $this->runFakeTraffic($product->url);
        }
    }


    private function runFakeTraffic($url): void
    {
        $url = explode('/category', $url);
        $url = $url[0];
        $duskShell = new WebsiteEmulator();
//        $duskShell->setProxyList();
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $content = $duskShell->emulate($this, $url, '');
        } catch (\Exception $exception) {
            $content = '';
        }

        dd($content);
    }

    private function setCountry(): void
    {

        $this->country = 'IT';
    }

    private function setIP(): void
    {
        $this->IP = '5.61.4.70	' . ':' . '8080';
    }
}