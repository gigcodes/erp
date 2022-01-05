<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Product;
use App\Brand;
use App\Category;
use App\UpteamLog;

class SyncUpteamProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync_upteam_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync upteam products';

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
		$api = "https://staging.upteamco.com/1api/files/1627/in/20220104-18-Cache-cd24d9267e8f2c8f866c7bc41d4a72af-1.json";
		UpteamLog::create(['log_description'=>'Api called '.$api]);
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $api);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);
		$products = json_decode($output);
        // close curl resource to free up system resources
        curl_close($ch);   
		$headings = $products[0];
		unset($products[0]);
		$productWithKeys = [];
		$i = 0;
		foreach($products as $product) {
			foreach($product as $key=>$value) {
				$productWithKeys[$i][$headings[$key]] = $value;
			}
			$i++;
		} 
		UpteamLog::create(['log_description'=>'Total Results Found '.count($productWithKeys)]);
		
		foreach($productWithKeys as $product) {
			UpteamLog::create(['log_description'=>'Product importing '.$product['product_name'].' with details '.json_encode($product)]);
			$brand = Brand::firstOrCreate(['name'=>$product['brand']]);
			$category = Category::where(['title'=>$product['category']])->first();
			if($category == null) {
				$mainCategory = Category::firstOrCreate(['title'=>$product['main_category']]);
				$category = Category::create(['title'=>$product['category'], 'parent_id'=>$mainCategory['id']]);
			}
			$measurement_size_type = 'measurement';
			$size_value = null;
			if($product['ring_size'] > 0) {
				$measurement_size_type = 'size';
				$size_value = $product['ring_size'];
			}else if($product['belt_size'] > 0) {
				$measurement_size_type = 'size';
				$size_value = $product['belt_size'];
			}
			/*$photos = explode(',',$product['photos']);
			foreach() {
				
			}
			$contents = file_get_contents($url);
			$file = '/tmp/' . $info['basename'];
			file_put_contents($file, $contents);*/
			
			Product::updateOrCreate(['sku'=>$product['sku']], 
				[
					'sku'=>$product['sku'], 
					'short_description'=>$product['description'], 
					'stock'=>$product['stock'],
					'brand'=>$brand['id'],
					'name'=>$product['product_name'],
					'category'=>$category['id'],
					'composition'=>$product['type_of_material'].' '.$product['name_of_material'].'/'.$product['type_of_material2'].' '.$product['name_of_material2'],
					'color'=>$product['color1'].' '.$product['shade1'],
					'lmeasurement'=>$product['length'],
					'hmeasurement'=>$product['width'],
					'dmeasurement'=>$product['depth'],
					'size'=>$size_value,
					'measurement_size_type'=>$measurement_size_type,
					'made_in'=>$product['country_of_origin'],
					'supplier'=>'UPTEAM',
					'supplier_id'=>5633,
				]
			);
			UpteamLog::create(['log_description'=>'Product imported '.$product['product_name']]);
		}
    }
}
