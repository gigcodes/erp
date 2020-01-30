<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\StoreWebsite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\StoreWebsiteCategory;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request, $id)
    {
        $title = "Attached Category | Store Website";

        if ($request->ajax()) {
            // send response into the json
            $categoryDropDown = \App\Category::attr([
                'name'  => 'category_id',
                'class' => 'form-control select-searchable',
            ])->renderAsDropdown();

            $storeWebsite = StoreWebsiteCategory::join("categories as c","c.id","store_website_categories.category_id")->where("store_website_id",$id)->get();

            return response()->json([
                "code"             => 200,
                "store_website_id" => 20,
                "data"             => $storeWebsite,
                'scdropdown'       => $categoryDropDown,
            ]);
        }

        return view('storewebsite::index', compact('title'));
    }

    /**
     * store cateogories
     *
     */

    public function store(Request $request)
    {
        $storeWebsiteId = $request->get("store_website_id");
        $post           = $request->all();

        $validator = Validator::make($post, [
            'store_website_id' => 'required',
            'remote_id'        => 'required',
            'category_id'      => 'unique:store_website_categories,category_id,NULL,id,store_website_id,' . $storeWebsiteId . '|required',
        ]);

        if ($validator->fails()) {
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }
            return response()->json(["code" => 500, "error" => $outputString]);
        }

        $storeWebsiteCategory = new StoreWebsiteCategory();
        $storeWebsiteCategory->fill($post);
        $storeWebsiteCategory->save();

        return response()->json(["code" => 200, "data" => $storeWebsiteCategory]);

    }

}
