<?php

namespace App\Console\Commands;

use App\Category;
use App\CategoryMap;
use App\Product;
use App\ScrapedProducts;
use Illuminate\Console\Command;

class EnrichCategoryInProductsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encrich:category-on-products';

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
        $altList = CategoryMap::all();
        $self = $this;
        ScrapedProducts::chunk(5000, function($scrapProducts) use ($altList, $self) {
            foreach ($scrapProducts as $scrapProduct) {
                $productEntry = Product::where('sku', $scrapProduct->sku)->first();

                if (!$productEntry) {
                    continue;
                }

                $category = $scrapProduct->properties['category'] ?? [];

                if ($category === []) {
                    continue;
                }

                foreach ($altList as $item) {
                    $status = $self->doesCategoryExists($category, $item);

                    if ($status === true) {
                        $genderId = $self->gender($category);
                        $self->saveCategory($genderId, $item, $productEntry);
                        break;
                    }
                }
            }
        });
    }

    private function saveCategory($genderId, $item, $product) {
        $category = Category::where('parent_id', $genderId)->get();
        $title = $item->title;

        foreach ($category as $subCategory) {
            if ($subCategory->title == $title) {
                $product->category = $subCategory->id;
                $product->save();
                return;
            }

            foreach ($subCategory->childs as $child) {
                if ($child->title == $title) {
                    $product->category = $subCategory->id;
                    $product->save();
                    return;
                }
            }
        }
    }

    private function doesCategoryExists($scrapedProductCategory, $alternativeList) {
        $alts = $alternativeList->alternatives;

        if (is_array($scrapedProductCategory)) {
            $scrapedProductCategory = implode(',', $scrapedProductCategory);
        }

        foreach ($alts as $alt) {
            if (strpos($scrapedProductCategory, $alt) !== false) {
                return true;
            }
        }

        return false;
    }

    private function gender($category) {
        if (is_array($category)) {
            foreach ($category as $cat) {
                if (strtoupper($cat) === 'MAN' ||
                    strtoupper($cat) === 'MEN' ||
                    strtoupper($cat) === 'UOMO' ||
                    strtoupper($cat) === 'UOMONI') {
                    return 3;
                }
            }

            return 2;
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

        return 2;
    }
}
