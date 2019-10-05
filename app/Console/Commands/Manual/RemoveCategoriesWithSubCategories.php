<?php

namespace App\Console\Commands\Manual;

use App\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Product;

class RemoveCategoriesWithSubCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:remove-categories-with-subcategories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Correct the pricing in the product table based on the scraped pricing';

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
     * Execute the console command
     *
     * @return mixed
     */
    public function handle()
    {
        // Get all products
        $products = Product::all();

        // Loop over products
        foreach ( $products as $product ) {
            // Get category ID
            $categoryId = $product->category;

            // Do we have a category ID?
            if ( $categoryId > 0 ) {
                // Check for parent ID
                $category = Category::find($categoryId);

                // Check for parent 2 (women) or 3 (men)
                if ( in_array($category->parent_id, [2,3]) ) {
                    // Remove category from product
                    $product->category = 0;
                    $product->save();
                }
            }
        }
    }
}
