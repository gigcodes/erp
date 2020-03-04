<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\StoreWebsite;
use App\StoreWebsiteCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

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

            $storeWebsite = StoreWebsiteCategory::join("categories as c", "c.id", "store_website_categories.category_id")
                ->where("store_website_id", $id)
                ->select(["store_website_categories.*", "c.title"])
                ->get();

            return response()->json([
                "code"             => 200,
                "store_website_id" => $id,
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

    public function delete(Request $request, $id, $store_category_id)
    {
        $storeCategory = StoreWebsiteCategory::where("store_website_id", $id)->where("id", $store_category_id)->first();
        if ($storeCategory) {
            $storeCategory->delete();
        }
        return response()->json(["code" => 200, "data" => []]);
    }

    /**
     * Get child categories
     * @return []
     *
     */

    public function getChildCategories(Request $request, $id)
    {
        $categories = \App\Category::where("id", $id)->first();
        $return     = [];
        if ($categories) {
            $return[] = [
                "id"   => $categories->id,
                "title" => $categories->title,
            ];

            $this->recursiveChildCat($categories, $return);
        }

        return response()->json(["code" => 200, "data" => $return]);
    }

    /**
     * Recursive child category
     * @return []
     *
     */

    public function recursiveChildCat($categories, &$return = [])
    {
        foreach ($categories->childs as $cat) {
            if($cat->title != "") {
                $return[] = [
                    "id"   => $cat->id,
                    "title" => $cat->title,
                ];
            }
            $this->recursiveChildCat($cat, $return);
        }
    }

    public function storeMultipleCategories(Request $request)
    {
        $swi        = $request->get("website_id");
        $categories = $request->get("categories");

        // store website category
        $ccat = StoreWebsiteCategory::where("store_website_id", $swi)->get()
            ->pluck("name")
            ->toArray();

        // check unique records    
        $unique = array_diff($categories, $ccat);

        if(!empty($unique) && is_array($unique)) {
            foreach ($unique as $cat) {
                StoreWebsiteCategory::create([
                    "store_website_id" => $swi,
                    "category_id" => $cat 
                ]);
            }
        }

        // return response
        return response()->json(["code" => 200 , "data" => ["store_website_id" => $swi] , "message" => "Category has been saved successfully"]);
    }
}
