<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ScrappedCategoryMapping;
use App\ScrapedProducts;
use App\ScrappedProductCategoryMapping;

class CategoryProductMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapping:product-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scraped Products Category Mapping';

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
        //
        $all_category = ScrappedCategoryMapping::get()->pluck('name','id')->toArray();
        $pro_arr = [];

        foreach ($all_category as $k => $v){
                $products = \App\ScrapedProducts::where("properties", "like", '%' . $v . '%')->select('website','id')->distinct()->get()->pluck('website','id')->toArray();

                foreach($products as $kk => $vv)
                {
                    // $web_name = implode(", ",$vv);
                    $web_name = $vv;
                    if($web_name)
                    {
                        $pro_arr[] = [
                            'category_mapping_id' => $k,
                            'product_id' => $kk
                        ];
                    }
                }
                
        }

        ScrappedProductCategoryMapping::insert($pro_arr);


    }
}
