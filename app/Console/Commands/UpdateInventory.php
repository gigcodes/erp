<?php

namespace App\Console\Commands;

use App\Product;
use App\Services\Bots\CucProductExistsEmulator;
use App\Services\Bots\WebsiteEmulator;
use App\Supplier;
use App\ScrapedProducts;
use App\ScrapActivity;
use App\Services\Scrap\DoubleFProductDetailsScraper;
use App\Services\Scrap\ToryDetailsScraper;
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
    private $toryScrapService;
    private $GNBCommand;

    /**
     * Create a new command instance.
     *
     * @param WiseBoutiqueProductDetailsScraper $boutiqueProductDetailsScraper
     * @param DoubleFProductDetailsScraper $doubleFProductDetailsScraper
     * @param GetGebnegozionlineProductDetailsWithEmulator $getGebnegozionlineProductDetailsWithEmulator
     * @param ToryDetailsScraper $toryDetailsScraper
     */
    public function __construct(WiseBoutiqueProductDetailsScraper $boutiqueProductDetailsScraper,
                                DoubleFProductDetailsScraper $doubleFProductDetailsScraper,
                                GetGebnegozionlineProductDetailsWithEmulator $getGebnegozionlineProductDetailsWithEmulator,
                                ToryDetailsScraper $toryDetailsScraper)
    {
        $this->wiseScrapService = $boutiqueProductDetailsScraper;
        $this->doubleFScrapService = $doubleFProductDetailsScraper;
        $this->GNBCommand = $getGebnegozionlineProductDetailsWithEmulator;
        $this->toryScrapService = $toryDetailsScraper;
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
                $letters = env('SCRAP_ALPHAS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
                if (strpos($letters, 'G') === false) {
                    return;
                }
                $url = $scraped_product->url;
                $duskShell = new WebsiteEmulator();
                $this->setCountry('IT');
                $duskShell->prepare();

                try {
                    $content = $duskShell->emulate($this, $url, '');
                } catch (Exception $exception) {
                    $content = ['', ''];
                }

                if (strlen($content[0]) > 3 && strlen($content[1]) > 4) {
                    $status = true;
                }

                $params = [
                  'website'             => 'G&B',
                  'scraped_product_id'  => $scraped_product->id,
                  'status'              => $status ? 1 : 0
                ];

                $supplier = 'G & B Negozionline';
            }

            if ($scraped_product->website == 'Wiseboutique') {

                $letters = env('SCRAP_ALPHAS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
                if (strpos($letters, 'W') === false) {
                    return;
                }

                $status = $this->wiseScrapService->doesProductExist($scraped_product);

                $params = [
                  'website'             => 'Wiseboutique',
                  'scraped_product_id'  => $scraped_product->id,
                  'status'              => $status ? 1 : 0
                ];

                $supplier = 'Wise Boutique';
            }

            if ($scraped_product->website == 'DoubleF') {

                $letters = env('SCRAP_ALPHAS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
                if (strpos($letters, 'D') === false) {
                    return;
                }

                $status = $this->doubleFScrapService->doesProductExist($scraped_product);

                $params = [
                  'website'             => 'DoubleF',
                  'scraped_product_id'  => $scraped_product->id,
                  'status'              => $status ? 1 : 0
                ];

                $supplier = 'Double F';
            }

            if ($scraped_product->website == 'Cuccuini') {
                $letters = env('SCRAP_ALPHAS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
                if (strpos($letters, 'C') === false) {
                    return;
                }

                $url = 'http://shop.cuccuini.it/it/register.html';

                $duskShell = new CucProductExistsEmulator();
                $this->setCountry('IT');
                $duskShell->prepare();

                try {
                    $status = $duskShell->emulate($this, $url, '', $scraped_product);
                } catch (Exception $exception) {
                    $status = false;
                }

                $params = [
                    'website'             => 'Cuccini',
                    'scraped_product_id'  => $scraped_product->id,
                    'status'              => $status ? 1 : 0
                ];

                $supplier = 'Cuccini';

            }

            if ($scraped_product->website == 'Tory') {

                $letters = env('SCRAP_ALPHAS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
                if (strpos($letters, 'T') === false) {
                    return;
                }

                $status = $this->toryScrapService->doesProductExist($scraped_product->url);

                $params = [
                    'website'             => 'Tory',
                    'scraped_product_id'  => $scraped_product->id,
                    'status'              => $status ? 1 : 0
                ];

                $supplier = 'Tory Burch';
            }

            // Updates the stock
            $sku = $scraped_product->sku;
            if ($product = Product::where('sku', $sku)->first()) {
              $product->update([
                  'stock' => $status ? 1 : 0
              ]);

              // Attaches suppliers
              if ($db_supplier = Supplier::where('supplier', $supplier)->first()) {
                $product->suppliers()->syncWithoutDetaching($db_supplier->id);
              }

              $result = app('App\Http\Controllers\ProductInventoryController')->magentoSoapUpdateStock($product, $status ? 1 : 0);
            }

            ScrapActivity::create($params);
        }
    }
}
