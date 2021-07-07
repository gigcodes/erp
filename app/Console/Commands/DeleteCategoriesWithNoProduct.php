<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Category;

class DeleteCategoriesWithNoProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-categories:with-no-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete categories with no products';

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
        set_time_limit(0);
        
        ini_set("memory_limit", "-1");
        
        $unKnownCategory  = Category::where('title', 'LIKE', '%Unknown Category%')->first();
        
        if ($unKnownCategory) {
        
            $unKnownCatArr = array_unique(explode(',', $unKnownCategory->references));
            
            if (!empty($unKnownCatArr)) {
                
                $storeUnUserCategory = [];
                
                foreach ($unKnownCatArr as $key => $unKnownC) {
                    
                    $count = \App\Category::ScrapedProducts($unKnownC);
                    if ($count > 1) {
                    
                        // echo "Added in  {$unKnownC} categories";
                        // echo  PHP_EOL;
                    
                    }else{
                        $storeUnUserCategory[] = $unKnownC;

                        //$key = array_search ($unKnownC, $unKnownCatArr);
                        
                        unset($unKnownCatArr[$key]);
                        
                        // echo "removed from  {$unKnownC} categories";
                        // echo  PHP_EOL;
                    }
                }

                $unKnownCategory->references      = implode(',',array_filter($unKnownCatArr));
                $unKnownCategory->ignore_category = implode(',',array_filter($storeUnUserCategory));
                $unKnownCategory->save();
            }
        }
    }
}
