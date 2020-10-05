<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use seo2websites\PriceComparisonScraper\PriceComparisonScraperSites;
use seo2websites\PriceComparisonScraper\PriceComparisonScraper;


class PriceComparisionController extends Controller
{
    public function index($name)
    {
    	if(empty($name)){
    		return response()->json([
			    'message' => 'No Name Found'
			]);	
    	}

    	$sites = PriceComparisonScraperSites::where('name','LIKE','%'.$name.'%')->first();
    	//dd($sites);
    	if(!$sites){
    		return response()->json([
			    'message' => 'No Site Found'
			]);
    	}else{
    		return response()->json([
			    'name' => $sites->name,
			    'url' => $sites->url,
			    'shoes' => $sites->url_cat_shoes,
			    'bags' => $sites->url_cat_bags,
			    'clothing' => $sites->url_cat_clothing,
			    'accessories' => $sites->url_cat_accessories,
			]);
    	}
    }


    public function storeComparision(Request $request)
    {
    	$name = $request->name;

    	$site = PriceComparisonScraperSites::where('name','LIKE','%'.$name.'%')->first();

    	if(!$site){
    		return response()->json([
			    'message' => 'No Site Found'
			]);
    	}else{

    		$category = $request->category;
    		$sku = $request->sku;
    		$product_url = $request->product_url;
    		$country_code = $request->country_code;
    		$currency = $request->currency;
    		$price = $request->price;
    		$shipping = $request->shipping;
    		$checkout_price = $request->checkout_price;

    		//validation
    		$empty = [];
    		if(empty($category) || empty($sku)  || empty($product_url)  || empty($country_code)  || empty($currency)  || empty($price)  || empty($shipping)  || empty($checkout_price)){

    			if(empty($category)){
    				array_push($empty,"category");
    			}

    			if(empty($sku)){
    				array_push($empty,"sku");
    			}

    			if(empty($product_url)){
    				array_push($empty,"product_url");
    			}

    			if(empty($currency)){
    				array_push($empty,"currency");
    			}

    			if(empty($country_code)){
    				array_push($empty,"country_code");
    			}

    			if(empty($price)){
    				array_push($empty,"price");
    			}

    			if(empty($shipping)){
    				array_push($empty,"shipping");
    			}

    			if(empty($checkout_price)){
    				array_push($empty,"checkout_price");
    			}

    			$message = implode(' , ', $empty);

				return response()->json([
				    'message' => 'Cannot be empty '.$message,
				]);    			
    			

    		}else{
    			$checkIfExist = PriceComparisonScraper::where('price_comparison_site_id',$site->id)->where('category',$request->category)->where('product_url',$request->product_url)->where('sku',$request->sku)->where('country_code',$request->country_code)->where('currency',$request->currency)->where('price',$request->price)->where('shipping',$request->shipping)->where('checkout_price',$request->checkout_price)->first();

    			try {
    				if(!$checkIfExist){
		    			$priceSave = new PriceComparisonScraper();
		    			$priceSave->price_comparison_site_id = $site->id;
		    			$priceSave->category = $request->category;
			    		$priceSave->sku = $request->sku;
			    		$priceSave->product_url = $request->product_url;
			    		$priceSave->country_code = $request->country_code;
			    		$priceSave->currency = $request->currency;
			    		$priceSave->price = $request->price;
			    		$priceSave->shipping = $request->shipping;
			    		$priceSave->checkout_price = $request->checkout_price;
			    		$priceSave->save();
					}else{
						$checkIfExist->price_comparison_site_id = $site->id;
		    			$checkIfExist->category = $request->category;
			    		$checkIfExist->sku = $request->sku;
			    		$checkIfExist->product_url = $request->product_url;
			    		$checkIfExist->country_code = $request->country_code;
			    		$checkIfExist->currency = $request->currency;
			    		$checkIfExist->price = $request->price;
			    		$checkIfExist->shipping = $request->shipping;
			    		$checkIfExist->checkout_price = $request->checkout_price;
			    		$checkIfExist->save();
					}

					return response()->json([
					    'message' => 'Saved SuccessFully',
					]); 
    			} catch (\Exception $e) {
    				return response()->json([
					    'message' => 'Something Went Wrong',
					]); 
    			}
					 


    		}
    		
    	}

    	
    }
}
