<?php

namespace App\Console\Commands;

use App\Category;
use App\Product;
use App\ScrapedProducts;
use Illuminate\Console\Command;
use League\Csv\Reader;
use League\Csv\Statement;

class FixCategoryNameBySupplier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:fix-by-supplier';

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
//        $products = Product::where('supplier', "Al Duca d'Aosta")->get();
        $products = Product::orderBy('id', 'DESC')->get();

        $csv = Reader::createFromPath(__DIR__.'/category.csv', 'r');
        $records = $csv->getRecords();

        foreach ($products as $product) {
            $this->classify($product, $records);
        }

    }

    private function classify($product, $records)
    {
        $scrapedProduct = ScrapedProducts::where('sku', $product->sku)->first();
//            $scrapedProduct = ScrapedProducts::where('sku', $product->sku)->where('website', 'alducadaosta')->first();
        $category = $scrapedProduct->properties['category'] ?? [];
        if ($category === []) {
            return;
        }
        if (is_array($category)) {
            $category = implode(' ', $category);
        }
        foreach ($records as $record) {
            $originalCategory = $record[0];
            if (strlen($record[1]) < 3) {
                continue;
            }
            $record = explode(',', $record[1]);
            foreach ($record as $cat) {
                if (stripos(strtoupper($category), strtoupper($cat)) !== false) {
                    $c = Category::where('title', $originalCategory)->first();

                    if (!$c) {
                        continue;
                    }

                    $gender = $this->getMaleOrFemale($category);

                    $parentCategory = Category::find($gender);
                    $childrenCategories = $parentCategory->childs;

                    foreach ($childrenCategories as $childrenCategory) {
                        if ($childrenCategory->title == $originalCategory) {
                            $product->category = $originalCategory;
                            $product->save();
                            return;
                        }

                        $grandChildren = $childrenCategory->childs;
                        foreach ($grandChildren as $grandChild) {
                            if ($grandChild->title == $originalCategory) {
                                $product->category = $originalCategory;
                                $product->save();
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    private function getMaleOrFemale($category) {
        if (is_array($category)) {
            foreach ($category as $cat) {
                if (strtoupper($cat) === 'MAN' ||
                    strtoupper($cat) === 'MEN' ||
                    strtoupper($cat) === 'UOMO' ||
                    strtoupper($cat) === 'UOMONI') {
                    return 3;
                }
            }

            foreach ($category as $cat) {
                if (strtoupper($cat) === 'WOMAN' ||
                    strtoupper($cat) === 'WOMEN' ||
                    strtoupper($cat) === 'DONNA' ||
                    strtoupper($cat) === 'LADIES') {
                    return 3;
                }
            }

            return false;
        }

        $category = strtoupper($category);

        if (strpos($category, 'WOMAN') !== false ||
            strpos($category, 'WOMEN') !== false ||
            strpos($category, 'DONNA') !== false ||
            strpos($category, 'LADY') !== false ||
            strpos($category, 'LADIES') !== false ||
            strpos($category, 'GIRL') !== false
        ) {
            return 2;
        }

        if (strpos($category, 'MAN') !== false ||
            strpos($category, 'MEN') !== false ||
            strpos($category, 'UOMO') !== false ||
            strpos($category, 'GENTS') !== false ||
            strpos($category, 'UOMONI') !== false ||
            strpos($category, 'GENTLEMAN') !== false ||
            strpos($category, 'GENTLEMEN') !== false
        ) {
            return 3;
        }

        return false;
    }
}
