<?php

namespace App\Console\Commands;

use App\ScrapedProducts;
use App\Product;
use App\Brand;
use App\Services\Bots\CucLoginEmulator;
use App\Services\Bots\CucProductDataEmulator;
use App\Setting;
use App\Services\Bots\WebsiteEmulator;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Wa72\HtmlPageDom\HtmlPageCrawler;


class GetCuccuiniDetailsWithEmulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cuccu:get-product-details';

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
        $letters = env('SCRAP_ALPHAS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        if (strpos($letters, 'C') === false) {
            return;
        }

        $this->authenticate();
    }

    private function authenticate() {
        $url = 'http://shop.cuccuini.it/it/register.html';

        $duskShell = new CucProductDataEmulator();
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $content = $duskShell->emulate($this, $url, '');
        } catch (Exception $exception) {
            $content = ['', ''];
        }
    }

    public function doesProductExist($url) {
        $duskShell = new CucLoginEmulator();
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $content = $duskShell->emulate($this, $url, '');
        } catch (Exception $exception) {
            $content = ['', ''];
        }

        if (strlen($content[0]) > 3 && strlen($content[1]) > 4) {
            return true;
        }

        return false;
    }

    private function setCountry(): void
    {

        $this->country = 'IT';
    }

    private function updateProductOnServer(ScrapedProducts $image)
    {

        $this->info('here saving to server');
        $client = new Client();
        $response = $client->request('POST', 'http://erp.sololuxury.co.in/api/sync-product', [
//        $response = $client->request('POST', 'https://erp.sololuxury.co.in/api/sync-product', [
            'form_params' => [
                'sku' => $image->sku,
                'website' => $image->website,
                'has_sku' => $image->has_sku,
                'title' => $image->title,
                'brand_id' => $image->brand_id,
                'description' => $image->description,
//                'images' => $this->imagesToDownload,
                'price' => $image->price,
                'properties' => $image->properties,
                'url' => $image->url,
                'is_property_updated' => 0,
                'is_price_updated' => 1,
                'is_enriched' => 0,
                'can_be_deleted' => 0
            ]
        ]);



        if (!$response) {
            dd($response->getBody()->getContents());
        }

        $image->is_updated_on_server = 1;
        $image->save();
    }

    private function setIP(): void
    {
        $this->IP = '5.61.4.70	' . ':' . '8080';
    }
}
