<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{

    public function index()
    {
        //return view("product-category.index");    
    }

    public function history(Request $request , $id)
    {
    	$productCategory = \App\ProductCategoryHistory::leftJoin("categories as c","c.id","product_category_histories.category_id")
    	->leftJoin("categories as d","d.id","product_category_histories.old_category_id")
    	->leftJoin("users as u","u.id","product_category_histories.user_id")
    	->where("product_id",$id)
    	->orderBy("product_category_histories.created_at","desc")
    	->select(["product_category_histories.*","c.title as new_cat_name","d.title as old_cat_name","u.name as user_name"])
    	->get();

    	return response()->json(["code" => 200 , "data" => $productCategory]);	
    }

    

}
