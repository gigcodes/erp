<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{

    public function index()
    {
        $title = "Product Category";
        $brands = \App\Brand::pluck("name","id")->toArray();
        $users = \App\User::pluck("name","id")->toArray();

        return view("product-category.index",compact('title','brands','users'));
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

    public function records(Request $request)
    {
        $brands = $request->get("brands",[]);
        $usresIds = $request->get("user_ids",[]);


        $productCategory = \App\ProductCategoryHistory::leftJoin("categories as c","c.id","product_category_histories.category_id")
        ->leftJoin("products as p","p.id","product_category_histories.product_id")
        ->leftJoin("categories as d","d.id","product_category_histories.old_category_id")
        ->leftJoin("users as u","u.id","product_category_histories.user_id");

        if(!empty($brands)) {
            $productCategory = $productCategory->whereIn("p.brand",$brands);
        }

        if(!empty($usresIds)) {
           $productCategory = $productCategory->whereIn("product_category_histories.user_id",$usresIds);
        }

        $productCategory = $productCategory->orderBy("product_category_histories.created_at","desc")
        ->select(["product_category_histories.*","c.title as new_cat_name","d.title as old_cat_name","u.name as user_name","p.name as product_name"])
        ->paginate();

        return response()->json([
            "code"        => 200,
            "data"        => $productCategory->items(),
            "pagination"  => (string) $productCategory->render(),
            "total" => $productCategory->total()
        ]);

    }

    

}
