<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Brand;

class BrandReferenceMergeAndDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brand:merge-delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Takes brands reference and if brand is present it will delete it';

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
        $brands = Brand::all();

        foreach ($brands as $brand) {
            $brandId = $brand->id;
            $reference = $brand->references;
            if(!empty($reference)){
                $brandReferences = explode(',', $reference); 
                foreach ($brandReferences as $ref) {
                       if(!empty($ref)){
                            $similarBrands = Brand::where('name','LIKE','%'.$ref.'%')->where('id', '!=', $brandId)->get();
                            foreach ($similarBrands as $similarBrand) {
                                $product = \App\Product::where("brand", $similarBrand->id)->get();
                                if (!$product->isEmpty()) {
                                    foreach ($product as $p) {
                                        $lastBrand     = $p->brand;
                                        $p->brand      = $brandId;
                                        $p->last_brand = $lastBrand;
                                        $p->save();
                                        \Log::info("{$brandId} updated with product" . $p->sku);
                                    }
                                }
                                $similarBrand->delete();
                            }
                       }
                   }   
            }
        }
    }
}
