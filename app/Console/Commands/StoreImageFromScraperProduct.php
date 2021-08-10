<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StoreImageFromScraperProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-image-from-scraped-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store image from scraped products';

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
        $images = \App\Product::join("mediables as med",function($q) {
            $q->on("med.mediable_id","products.id");
            $q->where("med.mediable_type",\App\Product::class);
            $q->where("med.tag","original");
        })
        ->leftJoin("media as m","m.id","med.media_id")
        ->select(["products.*","m.id as media_id"])
        ->havingRaw("media_id is null")
        ->groupBy("products.id")
        ->limit(10)
        ->get();

        if(!$images->isEmpty()) {
            foreach($images as $im) {
                \Log::info("Product started => ".$im->id);
                $this->info("Product started => ".$im->id);
                $scrapedProducts = \App\ScrapedProducts::where("sku",$im->sku)->orWhere("product_id",$im->id)->first();
                if($scrapedProducts) {
                    // delete image which is original 
                    //\DB::table("mediables")->where("mediable_type",\App\Product::class)->where("mediable_id",$im->id)->where("tag","original")->delete();
                    $listOfImages = json_decode($scrapedProducts->images,true);
                    $this->info("Product images found => ".count($listOfImages));
                    /*if(!empty($listOfImages)) {
                        $im->attachImagesToProduct($listOfImages);
                    }
                    if(in_array($im->status_id, [9,12])) {
                        $im->status_id = 4;
                        $im->save();
                    }*/
                }

            }
        }

    }
}
