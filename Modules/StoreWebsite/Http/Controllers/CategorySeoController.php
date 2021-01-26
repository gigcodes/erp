<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\Category;
use App\Language;
use App\StoreWebsite;
use Illuminate\Http\Request;
use App\StoreWebsiteCategory;
use Illuminate\Http\Response;
use App\StoreWebsiteCategorySeo;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use seo2websites\MagentoHelper\MagentoHelper;

class CategorySeoController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $title = "Category SEO | Store Website";
        $storeWebsites = StoreWebsite::all()->pluck("website", "id");
        $categories = Category::all();
        $categories_list = Category::pluck('title', 'id')->toArray();
        return view('storewebsite::category-seo.index',[
            'title' => $title,
            'storeWebsites' => $storeWebsites,
            'categories' => $categories,
            'categories_list' => $categories_list,
        ]);
    }

    public function records(Request $request)
    {
        $storewebsite_category_seos = StoreWebsiteCategorySeo::join("categories as cat", "cat.id", "store_website_category_seos.category_id");

        if ($request->has('category_id')) {
            $storewebsite_category_seos = $storewebsite_category_seos->where(function ($q) use ($request) {
                $q->where("cat.id", "like", "%" . $request->category_id . "%");
            });
        }

        // Check for keyword search
        if ($request->has('keyword')) {
            $storewebsite_category_seos = $storewebsite_category_seos->where(function ($q) use ($request) {
                $q->where("cat.title", "like", "%" . $request->keyword . "%")->orWhere('store_website_category_seos.meta_title', "like", "%" . $request->keyword . "%");
            });
        }

        $storewebsite_category_seos = $storewebsite_category_seos->orderBy("store_website_category_seos.id","asc")->select(["cat.title", "store_website_category_seos.*"])->paginate();

        $items = $storewebsite_category_seos->items();

        return response()->json(["code" => 200, "data" => $items, "total" => $storewebsite_category_seos->total(),
            "pagination"  => (string) $storewebsite_category_seos->links(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('storewebsite::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $post = $request->all();
        $id   = $request->get("id", 0);

        $params = [
            'meta_title'    => 'required',
            'category_id'  => 'required',
        ];

        $validator = Validator::make($post, $params);

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

        $records = StoreWebsiteCategorySeo::find($id);

        if (!$records) {
            $records = new StoreWebsiteCategorySeo;
        }

        $records->fill($post);

        // if records has been save then call a request to push
        if ($records->save()) {

        }

        return response()->json(["code" => 200, "data" => $records]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('storewebsite::show');
    }

    public function edit(Request $request, $id)
    {
        $storewebsite_category_seo = StoreWebsiteCategorySeo::where("id", $id)->first();

        if ($storewebsite_category_seo) {
            return response()->json(["code" => 200, "data" => $storewebsite_category_seo]);
        }

        return response()->json(["code" => 500, "error" => "Wrong category seo id!"]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $storewebsite_category_seo = StoreWebsiteCategorySeo::where("id", $id)->first();

        if ($storewebsite_category_seo) {
            $storewebsite_category_seo->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong category seo id!"]);
    }

    public function translateForOtherLanguage(Request $request, $id)
    {
        $store_website_category_seo = StoreWebsiteCategorySeo::find($id);

        if ($store_website_category_seo) {
            // find the language all active and then check that record page is exist or not
            $storeWebsiteCategory = StoreWebsiteCategory::where("category_id", $store_website_category_seo->category_id)->first();

            $category = Category::find($store_website_category_seo->category_id);

            $storeId = $storeWebsiteCategory->store_website_id;

            $website = StoreWebsite::find($storeId);

            if ($website && $category) {

                if ($category->parent_id == 0) {
                    $case = 'single';
                } elseif ($category->parent->parent_id == 0) {
                    $case = 'second';
                } else {
                    $case = 'third';
                }

                //Check if category
                if ($case == 'single') {
                    $data['id']       = $category->id;
                    $data['level']    = 1;
                    $data['name']     = ($request->category_name) ? ucwords($request->category_name) : ucwords($category->title);
                    $data['meta_title'] = $store_website_category_seo->meta_title;
                    $data['meta_keywords'] = $store_website_category_seo->meta_keywords;
                    $data['meta_description'] = $store_website_category_seo->meta_description;
                    $data['parentId'] = 0;
                    $parentId         = 0;

                    $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->id)->first();
                    if (empty($checkIfExist)) {
                        $storeWebsiteCategory                   = new StoreWebsiteCategory();
                        $storeWebsiteCategory->category_id      = $category->id;
                        $storeWebsiteCategory->store_website_id = $storeId;
                        // $storeWebsiteCategory->remote_id        = $categ;
                        $storeWebsiteCategory->save();
                    }


                    // if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                    //     $categ = MagentoHelper::createCategory($parentId, $data, $storeId);
                    // }
                    // if ($category) {
                    //     $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->id)->where('remote_id', $categ)->first();
                    //     if (empty($checkIfExist)) {
                    //         $storeWebsiteCategory                   = new StoreWebsiteCategory();
                    //         $storeWebsiteCategory->category_id      = $category->id;
                    //         $storeWebsiteCategory->store_website_id = $storeId;
                    //         $storeWebsiteCategory->remote_id        = $categ;
                    //         $storeWebsiteCategory->save();
                    //     }
                    // }
                }

                //if case second
                if ($case == 'second') {
                    $parentCategory = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->parent->id)->whereNotNull('remote_id')->first();
                    //if parent remote null then send to magento first
                    if (empty($parentCategory)) {

                        $data['id']       = $category->id;
                        $data['level']    = 1;
                        $data['name']     = ($request->category_name) ? ucwords($request->category_name) : ucwords($category->title);
                        $data['meta_title'] = $store_website_category_seo->meta_title;
                        $data['meta_keywords'] = $store_website_category_seo->meta_keywords;
                        $data['meta_description'] = $store_website_category_seo->meta_description;
                        $data['parentId'] = 0;
                        $parentId         = 0;

                        if ($parentCategoryDetails) {
                            $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->id)->first();
                            if (empty($checkIfExist)) {
                                $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                $storeWebsiteCategory->category_id      = $category->id;
                                $storeWebsiteCategory->store_website_id = $storeId;
                                // $storeWebsiteCategory->remote_id        = $parentCategoryDetails;
                                $storeWebsiteCategory->save();
                            }
                        }

                        /*if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                            $parentCategoryDetails = MagentoHelper::createCategory($parentId, $data, $storeId);
                        }
                        if ($parentCategoryDetails) {
                            $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->id)->where('remote_id', $parentCategoryDetails)->first();
                            if (empty($checkIfExist)) {
                                $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                $storeWebsiteCategory->category_id      = $category->id;
                                $storeWebsiteCategory->store_website_id = $storeId;
                                $storeWebsiteCategory->remote_id        = $parentCategoryDetails;
                                $storeWebsiteCategory->save();
                            }
                        }*/
                        // $parentRemoteId = $parentCategoryDetails;
                    } else {
                        $parentRemoteId = $parentCategory->remote_id;
                    }

                    // $data['id']       = $category->id;
                    // $data['level']    = 2;
                    // $data['name']     = ucwords($category->title);
                    // $data['parentId'] = isset($parentRemoteId) ? $parentRemoteId : NULL;

                    // if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                    //     $categoryDetail = MagentoHelper::createCategory($parentRemoteId, $data, $storeId);
                    // }
                    // if ($categoryDetail) {
                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->id)->first();
                        if (empty($checkIfExist)) {
                            $storeWebsiteCategory                   = new StoreWebsiteCategory();
                            $storeWebsiteCategory->category_id      = $category->id;
                            $storeWebsiteCategory->store_website_id = $storeId;
                            // $storeWebsiteCategory->remote_id        = $categoryDetail;
                            $storeWebsiteCategory->save();
                        }
                    // }
                }

                //if case third
                if ($case == 'third') {
                    //Find Parent
                    $parentCategory = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->id)->whereNotNull('remote_id')->first();

                    //Check if parent had remote id
                    if (empty($parentCategory)) {

                        //check for grandparent
                        $grandCategory       = Category::find($category->parent->id);
                        $grandCategoryDetail = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $grandCategory->parent->id)->whereNotNull('remote_id')->first();

                        if (empty($grandCategoryDetail)) {

                            // $data['id']       = $category->id;
                            // $data['level']    = 1;
                            // $data['name']     = ($request->category_name) ? ucwords($request->category_name) : ucwords($category->title);
                            // $data['parentId'] = 0;
                            // $data['meta_title'] = $store_website_category_seo->meta_title;
                            // $data['meta_keywords'] = $store_website_category_seo->meta_keywords;
                            // $data['meta_description'] = $store_website_category_seo->meta_description;
                            // $parentId         = 0;

                            // if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {

                            //     $grandCategoryDetails = MagentoHelper::createCategory($parentId, $data, $storeId);

                            // }

                            // if ($grandCategoryDetails) {
                                $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->parent->id)->where('remote_id', $grandCategoryDetails)->first();
                                if (empty($checkIfExist)) {
                                    $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                    $storeWebsiteCategory->category_id      = $category->parent->id;
                                    $storeWebsiteCategory->store_website_id = $storeId;
                                    // $storeWebsiteCategory->remote_id        = $grandCategoryDetails;
                                    $storeWebsiteCategory->save();
                                }

                            // }

                            // $grandRemoteId = $grandCategoryDetails;

                        } else {
                            $grandRemoteId = $grandCategoryDetail->remote_id;
                        }
                        //Search for child category

                        // $data['id']       = $category->parent->id;
                        // $data['level']    = 2;
                        // $data['name']     = ucwords($category->parent->title);
                        // $data['parentId'] = $grandRemoteId;
                        // $parentId         = $grandRemoteId;

                        // if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {

                        //     $childCategoryDetails = MagentoHelper::createCategory($parentId, $data, $storeId);

                        // }

                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->parent->id)->first();
                        if (empty($checkIfExist)) {
                            $storeWebsiteCategory                   = new StoreWebsiteCategory();
                            $storeWebsiteCategory->category_id      = $category->parent->id;
                            $storeWebsiteCategory->store_website_id = $storeId;
                            $storeWebsiteCategory->remote_id        = $childCategoryDetails;
                            $storeWebsiteCategory->save();
                        }

                        // $data['id']       = $category->id;
                        // $data['level']    = 3;
                        // $data['name']     = ucwords($category->title);
                        // $data['parentId'] = $childCategoryDetails;

                        // if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {

                        //     $categoryDetail = MagentoHelper::createCategory($childCategoryDetails, $data, $storeId);

                        // }

                        // if ($categoryDetail) {
                            $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->id)->first();
                            if (empty($checkIfExist)) {
                                $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                $storeWebsiteCategory->category_id      = $category->id;
                                $storeWebsiteCategory->store_website_id = $storeId;
                                // $storeWebsiteCategory->remote_id        = $categoryDetail;
                                $storeWebsiteCategory->save();
                            }
                        // }

                    }

                }

            }

            return response()->json(["code" => 200, "data" => [], "message" => "Records copied succesfully"]);
        }

        return response()->json(["code" => 500, "data" => [], "message" => "Page does not exist"]);

    }
    public function push($id){
        $category = StoreWebsiteCategorySeo::where("id", $id)->first();

        if ($category) {
            \App\Jobs\PushCategorySeoToMagento::dispatch($id);
            return response()->json(["code" => 200, 'message' => "category send for push"]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }
}
