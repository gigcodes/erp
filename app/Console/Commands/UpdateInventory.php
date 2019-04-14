<?php

namespace App\Console\Commands;

use App\Product;
use App\ScrapedProducts;
use App\ScrapActivity;
use App\Services\Scrap\DoubleFProductDetailsScraper;
use App\Services\Scrap\WiseBoutiqueProductDetailsScraper;
use Illuminate\Console\Command;

class UpdateInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:refresh-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $wiseScrapService;
    private $doubleFScrapService;
    private $GNBCommand;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(WiseBoutiqueProductDetailsScraper $boutiqueProductDetailsScraper, DoubleFProductDetailsScraper $doubleFProductDetailsScraper, GetGebnegozionlineProductDetailsWithEmulator $getGebnegozionlineProductDetailsWithEmulator)
    {
        $this->wiseScrapService = $boutiqueProductDetailsScraper;
        $this->doubleFScrapService = $doubleFProductDetailsScraper;
        $this->GNBCommand = $getGebnegozionlineProductDetailsWithEmulator;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $scraped_products = ScrapedProducts::where('website', '!=', 'EXCEL_IMPORT_TYPE_1')->get();

        foreach ($scraped_products as $scraped_product) {
            $status = false;
            if ($scraped_product->website == 'G&B') {
                continue;
//                $status = $this->GNBCommand->doesProductExist($scraped_product->url);
                $params = [
                  'website'             => 'G&B',
                  'scraped_product_id'  => $scraped_product->id,
                  'status'              => $status ? 1 : 0
                ];
            }

            if ($scraped_product->website == 'Wiseboutique') {
                $status = $this->wiseScrapService->doesProductExist($scraped_product->url);

                $params = [
                  'website'             => 'Wiseboutique',
                  'scraped_product_id'  => $scraped_product->id,
                  'status'              => $status ? 1 : 0
                ];
            }

            if ($scraped_product->website == 'DoubleF') {
                $status = $this->doubleFScrapService->doesProductExist($scraped_product->url);

                $params = [
                  'website'             => 'DoubleF',
                  'scraped_product_id'  => $scraped_product->id,
                  'status'              => $status ? 1 : 0
                ];
            }

            $sku = $scraped_product->sku;
            Product::where('sku', $sku)->update([
                'stock' => $status ? 1 : 0
            ]);

            ScrapActivity::create($params);
        }
    }
}
