<?php

namespace App\Http\Controllers;

use App\BrandCategoryPriceRange;
use App\Category;
use App\CategorySegment;
use App\ScrapedProducts;
use App\ScrappedCategoryMapping;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class CategoryController extends Controller
{

    public function __construct()
    {
        // $this->middleware( 'permission:category-edit', [ 'only' => [ 'addCategory', 'edit', 'manageCategory', 'remove' ] ] );
    }
    //

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function manageCategory(Request $request)
    {
        $category_segments = CategorySegment::where('status', 1)->get()->pluck('name', 'id');
        $categories        = Category::where('parent_id', '=', 0)->get();
        $allCategories     = Category::pluck('title', 'id')->all();

        $old = $request->old('parent_id');

        $allCategoriesDropdown = Category::attr(['name' => 'parent_id', 'class' => 'form-control'])
            ->selected($old ? $old : 1)
            ->renderAsDropdown();

        $allCategoriesDropdownEdit = Category::attr(['name' => 'edit_cat', 'class' => 'form-control'])
            ->selected($old ? $old : 1)
            ->renderAsDropdown();

        return view('category.treeview', compact('category_segments', 'categories', 'allCategories', 'allCategoriesDropdown', 'allCategoriesDropdownEdit'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function addCategory(Request $request)
    {
        $this->validate($request, [
            'title'       => 'required',
            'magento_id'  => 'required|numeric',
            'show_all_id' => 'numeric|nullable',
        ]);
        $input              = $request->all();
        $input['parent_id'] = empty($input['parent_id']) ? 0 : $input['parent_id'];

        Category::create($input);
        return back()->with('success', 'New Category added successfully.');
    }

    public function edit(Category $category, Request $request)
    {

        $data                        = [];
        $data['id']                  = $category->id;
        $data['title']               = $category->title;
        $data['magento_id']          = $category->magento_id;
        $data['show_all_id']         = $category->show_all_id;
        $data['category_segment_id'] = $category->category_segment_id;
        $data['category_segments']   = CategorySegment::where('status', 1)->get()->pluck('name', 'id');

        if ($request->method() === 'POST') {
            $this->validate($request, [
                'title'       => 'required',
                'magento_id'  => 'required|numeric',
                'show_all_id' => 'numeric|nullable',
            ]);

            $category->title       = $request->input('title');
            $category->magento_id  = $request->input('magento_id');
            $category->show_all_id = $request->input('show_all_id');
            if ($request->has('category_segment_id')) {
                $category->category_segment_id = $request->category_segment_id;
            }
            $category->save();

            return redirect()->route('category')
                ->with('success-remove', 'Category updated successfully');
        }

        return view('category.edit', $data);
    }

    public function remove(Request $request)
    {

        $category_instance = new Category();
        $category          = $category_instance->find($request->input('edit_cat'));

        if (Category::isParent($category->id)) {
            return back()->with('error-remove', 'Can\'t delete Parent category. Please delete all the childs first');
        }

        if (Category::hasProducts($category->id)) {
            return back()->with('error-remove', 'Can\'t delete category is associated with products. Please remove all the association first');
        }

        if ($category->id == 1) {
            return back()->with('error-remove', 'Can\'t be delete');
        }

        $title = $category->title;
        $category->delete();

        return back()->with('success-remove', $title . ' category Deleted');
    }

    public static function getCategoryTree($id)
    {

        $category          = new Category();
        $category_instance = $category->find($id);
        $categoryTree      = [];

        if ($category_instance == null) {
            return false;
        }

        $parent_id = $category_instance->parent_id;

        while ($parent_id != 0) {

            $category_instance = $category->find($parent_id);
            $categoryTree[]    = $category_instance->title;
            $parent_id         = $category_instance->parent_id;
        }

        return array_reverse($categoryTree);
    }

    public static function brandMinMaxPricing()
    {
        // Get all data
        $results = \Illuminate\Support\Facades\DB::select("
            SELECT
                categories.title,
                categories.id as cat_id,
                MIN(price*1) AS minimumPrice,
                MAX(price*1) AS maximumPrice
            FROM
                products
            JOIN
                categories
            ON
                products.category=categories.id
            GROUP BY
                products.category
            ORDER BY
                categories.title
        ");

        // Get all form data
        $resultsBrandCategoryPriceRange = BrandCategoryPriceRange::all();

        // Create array with brand segments
        $brandSegments = ['A', 'B', 'C'];

        // Create empty array
        $formResults = [];

        // Loop over results
        foreach ($resultsBrandCategoryPriceRange as $result) {
            $formResults[$result->brand_segment][$result->category_id]['min'] = $result->min_price;
            $formResults[$result->brand_segment][$result->category_id]['max'] = $result->max_price;
        }

        return view('category.minmaxpricing', compact('results', 'brandSegments', 'formResults'));
    }

    public static function updateBrandMinMaxPricing(Request $request)
    {
        // Check minimum price first
        if ($request->ajax() && $request->type == 'min' && (int) $request->price > 0) {
            return BrandCategoryPriceRange::updateOrCreate(
                ['brand_segment' => $request->brand_segment, 'category_id' => $request->category_id],
                ['min_price' => $request->price]
            );
        }

        // Check minimum price first
        if ($request->ajax() && $request->type == 'max' && (int) $request->price > 0) {
            return BrandCategoryPriceRange::updateOrCreate(
                ['brand_segment' => $request->brand_segment, 'category_id' => $request->category_id],
                ['max_price' => $request->price]
            );
        }
    }

    public static function getCategoryTreeMagentoIds($id)
    {

        $category          = new Category();
        $category_instance = $category->find($id);
        $categoryTree      = [];

        $categoryTree[] = $category_instance->magento_id;
        $parent_id      = $category_instance->parent_id;

        while ($parent_id != 0) {

            $category_instance = $category->find($parent_id);
            $categoryTree[]    = $category_instance->magento_id;

            if (!empty($category_instance->show_all_id)) {
                $categoryTree[] = $category_instance->show_all_id;
            }

            $parent_id = $category_instance->parent_id;
        }

        //Adding root category.
        //        array_push($categoryTree,'2');

        return array_reverse($categoryTree);
    }

    public static function getCategoryIdByName($term)
    {
        $category = Category::where('title', '=', $term)->first();

        return $category ? $category->id : 0;
    }

    public function mapCategory()
    {
        $fillerCategories = Category::where('id', '>', 1)->where('parent_id', 0)->whereIn('id', [143, 144])->get();

        $categories = Category::where('id', '>', 1)->where('parent_id', 0)->whereNotIn('id', [143, 144])->get();

        $allStatus = ["" => "N/A"]+\App\Helpers\StatusHelper::getStatus();

        $allCategoriesDropdown = Category::attr(['name' => 'new_cat_id', 'class' => 'form-control new-category-update', 'style' => "width:100%"])->renderAsDropdown();

        return view('category.references', compact('fillerCategories', 'categories', 'allStatus', 'allCategoriesDropdown'));
    }

    public function saveReferences(Request $request)
    {

        $categories = $request->get('category');
        $info       = $request->get('info');

        if (!empty($info)) {
            foreach ($info as $catId => $reference) {
                list($catId, $reference) = explode("#", $reference);
                $catId                   = str_replace("cat_", "", $catId);
                $category                = Category::find($catId);
                $category->references    = $reference;
                $category->save();
            }

        } else {
            foreach ($categories as $catId => $reference) {
                $catId                = str_replace("cat_", "", $catId);
                $category             = Category::find($catId);
                $category->references = implode(',', $reference);
                $category->save();
            }
        }

        //if(request()->is("ajax")) {
        return response()->json(["code" => 200]);
        //}

        return redirect()->back()->with('message', 'Category updated successfully!');
    }

    public function saveReference(Request $request)
    {
        $oldCatId = $request->get("old_cat_id");
        $newcatId = $request->get("new_cat_id");
        $catName  = strtolower($request->get("cat_name"));

        // assigned new category
        $newCategory = null;
        $oldCategory = null;

        // checking category id
        if (!empty($oldCatId) && !empty($newcatId)) {
            $oldCategory = Category::find($oldCatId);
            if (!empty($oldCategory)) {
                $catArray = explode(",", $oldCategory->references);
                $catArray = array_map('strtolower', $catArray);

                // check matched array we got
                $findMe = array_search($catName, $catArray);
                if ($findMe !== false) {
                    unset($catArray[$findMe]);
                }

                // update new category
                $oldCategory->references = implode(",", array_unique(array_filter($catArray)));
                $oldCategory->save();
            }
            // update with new category id
            $newCategory = Category::find($newcatId);

            if ($newCategory) {

                $newCatArr               = explode(",", $newCategory->references);
                $newCatArr               = array_map('strtolower', $newCatArr);
                $newCatArr[]             = strtolower($catName);
                $newCategory->references = implode(",", array_unique(array_filter($newCatArr)));
                $newCategory->save();

                // once we have new category id then we need to update all product from that old category
                $products = \App\Product::where("category", $oldCatId)->select(["products.id", "products.sku"])->get();
                if (!$products->isEmpty()) {
                    foreach ($products as $product) {
                        $scraped_products = $product->many_scraped_products;
                        if (!$scraped_products->isEmpty()) {
                            foreach ($scraped_products as $scraped_product) {
                                if (isset($scraped_product->properties['category'])) {
                                    if (is_array($scraped_product->properties['category'])) {
                                        $namesList = array_map('strtolower', $scraped_product->properties['category']);
                                        if (in_array(strtolower($catName), $namesList)) {
                                            $product->category = $newcatId;
                                            $product->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

        return response()->json(["code" => 200, "data" => $newCategory]);

    }

    public function updateField(Request $request)
    {
        $id    = $request->get("id");
        $field = $request->get("_f");
        $value = $request->get("_v");

        $category = Category::where("id", $id)->first();
        if ($category) {
            $category->{$field} = $value;
            $category->save();

            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500]);

    }

    public function saveForm(Request $request)
    {
        $id = $request->id;
        if ($id != null) {
            $category = \App\Category::find($id);
            if (!empty($category)) {
                $findChild = \App\Category::whereNull("simplyduty_code")->where("parent_id", $category->id)->get();
                if (!empty($findChild) && !$findChild->isEmpty()) {
                    foreach ($findChild as $child) {
                        $child->simplyduty_code = $request->simplyduty_code;
                        $child->save();
                    }
                }
                $category->simplyduty_code = $request->simplyduty_code;
                $category->save();
            }
        }

        return response()->json(["code" => 200, "message" => "Success"]);

    }

    public function usedProducts(Request $request)
    {
        $q = $request->q;

        if ($q) {
            // check the type and then
            $q        = '"' . $q . '"';
            $products = \App\ScrapedProducts::where("properties", "like", '%' . $q . '%')->latest()->limit(5)->get();

            $view = (string) view("compositions.preview-products", compact('products'));
            return response()->json(["code" => 200, "html" => $view]);
        }

        return response()->json(["code" => 200, "html" => ""]);
    }

    public function affectedProduct(Request $request)
    {
        $old  = $request->old_cat_id;
        $from = $request->cat_name;
        $to   = $request->new_cat_id;

        if (!empty($from)) {
            // check the type and then
            $total = \App\ScrapedProducts::matchedCategory($from)->count();
            return response()->json(["code" => 200, "total" => $total]);
        }
    }

    public function affectedProductNew(Request $request)
    {
        $old         = $request->old_cat_id;
        $from        = $request->cat_name;
        $to          = $request->new_cat_id;
        $wholeString = $request->wholeString;

        if (!empty($from)) {
            // check the type and then
            $total = \App\ScrapedProducts::matchedCategory($from)->count();

            $view = (string) view("category.partials.affected-products", compact('total', 'from', 'to', 'wholeString'));

            return response()->json(["code" => 200, "html" => $view]);

        }
    }

    public function updateCategoryReference(Request $request)
    {
        $old         = $request->old_cat_id;
        $from        = $request->cat_name;
        $to          = $request->new_cat_id;
        $change      = $request->with_product;
        $wholeString = $request->wholeString;
        if (!isset($wholeString)) {
            $wholeString = $from;
        }

        if (isset($change)) {
            if ($change == 'yes') {
                \App\Jobs\UpdateProductCategoryFromErp::dispatch([
                    "from"    => $from,
                    "to"      => $to,
                    "user_id" => \Auth::user()->id,
                ])->onQueue("supplier_products");
            }
        }

        $c = Category::where("id", $old)->first();

        if ($c) {
            $allrefernce = explode(",", $c->references);
            $newRef      = [];
            if (!empty($allrefernce)) {
                foreach ($allrefernce as $ar) {
                    if ($ar != $wholeString) {
                        $newRef[] = $ar;
                    }
                }
            }
            $c->references = implode(",", $newRef);
            $c->save();

            // new category reference store
            $new = Category::where("id", $to)->first();
            if ($new) {
                $existingRef   = explode(",", $new->references);
                $existingRef[] = $from;

                $userUpdatedAttributeHistory = \App\UserUpdatedAttributeHistory::create([
                    'old_value'      => $new->references,
                    'new_value'      => implode(",", array_unique($existingRef)),
                    'attribute_name' => 'category',
                    'attribute_id'   => $new->id,
                    'user_id'        => \Auth::user()->id,
                ]);

                $new->references = implode(",", $existingRef);
                $new->save();
            }
        }

        return response()->json(["code" => 200, "message" => "Your request has been pushed successfully"]);
    }

    public function updateMultipleCategoryReference(Request $request)
    {
        $old  = $request->old_cat_id;
        $from = $request->from;
        $to   = $request->to;

        //$change = $request->with_product;
        /*$wholeString = $request->wholeString;
        if(!isset($wholeString)){
        $wholeString = $from;
        }*/

        if (!empty($from) && is_array($from)) {
            foreach ($from as $f) {
                $original = $f;
                $f        = explode('/', $f);
                $f        = end($f);

                \App\Jobs\UpdateProductCategoryFromErp::dispatch([
                    "from"    => $f,
                    "to"      => $to,
                    "user_id" => \Auth::user()->id,
                ])->onQueue("supplier_products");

                $c = Category::where("id", $old)->first();

                if ($c) {
                    $allrefernce = explode(",", $c->references);
                    $newRef      = [];
                    if (!empty($allrefernce)) {
                        foreach ($allrefernce as $ar) {
                            if ($ar != $original) {
                                $newRef[] = $ar;
                            }
                        }
                    }

                    $c->references = implode(",", array_unique($newRef));
                    $c->save();

                    $new = Category::where("id", $to)->first();
                    if ($new) {
                        $existingRef   = explode(",", $new->references);
                        $existingRef[] = $f;

                        $userUpdatedAttributeHistory = \App\UserUpdatedAttributeHistory::create([
                            'old_value'      => $new->references,
                            'new_value'      => implode(",", array_unique($existingRef)),
                            'attribute_name' => 'category',
                            'attribute_id'   => $new->id,
                            'user_id'        => \Auth::user()->id,
                        ]);

                        $new->references = implode(",", array_unique($existingRef));
                        $new->save();
                    }
                }

            }
        }

        return response()->json(["code" => 200, "message" => "Your request has been pushed successfully"]);
    }

    public function newCategoryReferenceIndex(Request $request)
    {
        $unKnownCategory   = Category::where('title', 'LIKE', '%Unknown Category%')->first();
        $unKnownCategories = explode(',', $unKnownCategory->references);
        $unKnownCategories = array_unique($unKnownCategories);

        $unKnownCategory->references = implode(",", $unKnownCategories);
        $unKnownCategory->save();

        $input             = preg_quote($request->get('search'), '~');
        $unKnownCategories = preg_grep('~' . $input . '~', $unKnownCategories);
        

        // $mainArr = [];
        // foreach ($unKnownCategories as $cat) {

        //     $q = '"'.$cat.'"';
        //     $count = ScrapedProducts::where("properties","like",'%'.$q.'%')->count();

        //     $subArr = [];
        //     $subArr['categoryName'] = $cat;
        //     $subArr['cat_product_count'] = $count;
        //     $mainArr[] = $subArr;
        // }

        $unKnownCategories = $this->paginate($unKnownCategories,50);
        $unKnownCategories->setPath($request->url());


        // $scrapped_category_mapping = ScrappedCategoryMapping::withCount('cat_count')->with(['scmSPCM'])->limit(5)->get();

        $scrapped_category_mapping = ScrappedCategoryMapping::withCount('cat_count')->with(['scmSPCM']);
        $scrapped_category_mapping = $scrapped_category_mapping->paginate(Setting::get('pagination'));


        //check if the items is not empty

        // $TotalProductCount = array_sum(array_column($mainArr,'cat_product_count'));

        $categoryAll   = Category::where('id', '!=', $unKnownCategory->ide)->where('magento_id', '!=', '0')->get();
        $categoryArray = [];
        foreach ($categoryAll as $category) {
            $categoryArray[] = array('id' => $category->id, 'value' => $category->title);
            $childs          = Category::where('parent_id', $category->id)->get();
            foreach ($childs as $child) {
                $categoryArray[] = array('id' => $child->id, 'value' => $category->title . ' > ' . $child->title);
                $grandChilds     = Category::where('parent_id', $child->id)->get();
                if ($grandChilds != null) {
                    foreach ($grandChilds as $grandChild) {
                        $categoryArray[] = array('id' => $grandChild->id, 'value' => $category->title . ' > ' . $child->title . ' > ' . $grandChild->title);
                    }
                }
            }
        }

        // START - Purpose : Check need_to_skip Status - #DEVTASK-4143

        // $need_to_skip_rec = \App\UserUpdatedAttributeHistory::where("user_id", \Auth::user()->id)->where('need_to_skip',1)->first(); 

        $need_to_skip_rec = \App\TemporaryCategoryUpdation::where("user_id", \Auth::user()->id)->where('need_to_skip',1)->first(); 

        if($need_to_skip_rec)
            $need_to_skip_status = 1;
        else
            $need_to_skip_status = 0;    
        // END - #DEVTASK-4143

        return view('category.new-reference', ['unKnownCategories' => $unKnownCategories, 'categoryAll' => $categoryArray, 'unKnownCategoryId' => $unKnownCategory->id , 'need_to_skip_status' => $need_to_skip_status, 'scrapped_category_mapping' => $scrapped_category_mapping]);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        $items = $items->sortByDesc("cat_product_count");

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function history(Request $request, $id)
    {
        $records = \App\UserUpdatedAttributeHistory::where("attribute_id", $id)->where("attribute_name", "category")->latest()->get();
        return view("compositions.partials.show-update-history", compact('records'));
    }

    public function deleteUnused()
    {
        \Artisan::call("delete-categories:with-no-products");
        return redirect()->back()->with('success', 'Your request has been finished successfully!');
    }

    public function fixAutoSuggested(Request $request)
    {
        //START - Purpose : Check Show skip checknox value and get data - #DEVTASK-4143
        $show_skipeed_btn_value = $request->show_skipeed_btn_value;

        if($show_skipeed_btn_value == 'false')
        {
            $need_ro_skip_true_record = \App\UserUpdatedAttributeHistory::where("user_id", \Auth::user()->id)->where('need_to_skip',1)->get()->toArray(); 
            
            $category_ids = array_column($need_ro_skip_true_record, 'attribute_id');

            $category_name = \App\TemporaryCategoryUpdation::where("user_id", \Auth::user()->id)->where('need_to_skip',1)->get()->toArray(); 

            $category_name_ar = array_column($category_name, 'category_name');
        }
        //END - #DEVTASK-4143

        $unKnownCategory   = Category::where('title', 'LIKE', '%Unknown Category%')->first();
        
        $unKnownCategories = explode(',', $unKnownCategory->references);
        $unKnownCategories = array_unique($unKnownCategories);

        $input             = preg_quote($request->get('search'), '~');
        $unKnownCategories = preg_grep('~' . $input . '~', $unKnownCategories);

        //$unKnownCategories[] = "women/clothing/trousers/trousers/alexander mcqueen prince of wales trousers";
        //$unKnownCategories[] = "women/clothing/tops/tops/alexander mcqueen flounced top";
        //$unKnownCategories[] = "men/bags/business and travel bags/prada document holder in saffiano";
        
        $unKnownCategories = $this->paginate($unKnownCategories,50);
        $unKnownCategories->setPath($request->url());

       


        $links = [];
        if (!$unKnownCategories->isEmpty()) {
            foreach ($unKnownCategories as $i => $unkc) {
                $filter = \App\Category::updateCategoryAuto($unkc);

                //START - Purpose : Added if Condition for Alreday exist or not - #DEVTASK-4143
                if($show_skipeed_btn_value == 'false')
                {
                    // if($filter)
                    // {
                    //     if(!in_array($filter->id, $category_ids))
                    //     {
                    //         $links[] = [
                    //             "from" => $unkc,
                    //             "to"   => ($filter) ? $filter->id : null,
                    //         ];
                    //     }
                    // }else{
                        
                        if(!in_array($unkc, $category_name_ar))
                        {
                            $links[] = [
                                "from" => $unkc,
                                "to"   => ($filter) ? $filter->id : null,
                            ];
                        }
                    // }
                }//END - #DEVTASK-4143
                else{
                    $links[] = [
                        "from" => $unkc,
                        "to"   => ($filter) ? $filter->id : null,
                    ];
                }
            }
        }

        $view = (string) view("category.partials.preview-categories", compact('links'));
        return response()->json(["code" => 200, "html" => $view]);

    }

    public function fixAutoSuggestedString(Request $request)
    {
        $unKnownCategory   = Category::where('title', 'LIKE', '%Unknown Category%')->first();
        $unKnownCategories = explode(',', $unKnownCategory->references);
        $unKnownCategories = array_unique($unKnownCategories);

        $input             = preg_quote($request->get('search'), '~');
        $unKnownCategories = preg_grep('~' . $input . '~', $unKnownCategories);

        //$unKnownCategories[] = "woman/small gg marmont shoulder bag";
        //$unKnownCategories[] = "women/clothing/tops/tops/alexander mcqueen flounced top";
        //$unKnownCategories[] = "men/bags/business and travel bags/prada document holder in saffiano";
        
        $unKnownCategories = $this->paginate($unKnownCategories,50);
        $unKnownCategories->setPath($request->url());


        $links = [];
        if (!$unKnownCategories->isEmpty()) {
            foreach ($unKnownCategories as $i => $unkc) {
                $filter = \App\Category::updateCategoryAutoSpace($unkc);
                $links[] = [
                    "from" => $unkc,
                    "to"   => ($filter) ? $filter->id : null,
                ];
            }
        }

        $view = (string) view("category.partials.preview-categories", compact('links'));
        return response()->json(["code" => 200, "html" => $view]);

    }

    public function saveCategoryReference(Request $request)
    {
        $unKnownCategory   = Category::where('title', 'LIKE', '%Unknown Category%')->first();
        $items = $request->updated_category;
        if(!empty($items)) {
            foreach($items as $k => $item) {
                
                if($item != 1) {
                    $filter = Category::find($item);
                    if ($filter) {
                        $old         = $unKnownCategory->id;
                        $from        = $k;
                        $to          = $item;
                        $change      = 'yes';
                        $wholeString = $k;
                        if ($change == 'yes') {
                            \App\Jobs\UpdateProductCategoryFromErp::dispatch([
                                "from"    => $from,
                                "to"      => $to,
                                "user_id" => \Auth::user()->id,
                            ])->onQueue("supplier_products");
                        }
                        $c = $unKnownCategory;
                        if ($c) {
                            $allrefernce = explode(",", $c->references);
                            $newRef      = [];
                            if (!empty($allrefernce)) {
                                foreach ($allrefernce as $ar) {
                                    if ($ar != $wholeString) {
                                        $newRef[] = $ar;
                                    }
                                }
                            }
                            $c->references = implode(",", $newRef);
                            $c->save();
                            // new category reference store
                            if ($filter) {

                                $existingRef   = explode(",", $filter->references);
                                $existingRef[] = $from;

                                $userUpdatedAttributeHistory = \App\UserUpdatedAttributeHistory::create([
                                    'old_value'      => $filter->references,
                                    'new_value'      => implode(",", array_unique($existingRef)),
                                    'attribute_name' => 'category',
                                    'attribute_id'   => $filter->id,
                                    'user_id'        => \Auth::user()->id,
                                    'need_to_skip'   => 1, // Purpose : Added need_to_skip for Notify to user - #DEVTASK-4143
                                ]);

                                $filter->references = implode(",", array_unique($existingRef));
                                $filter->save();

                                //START - Add CategoryEntry in TemporaryCategoryUpdation Table - #DEVTASK-4143
                                $category_status_updation = \App\TemporaryCategoryUpdation::create([
                                    'category_name'      => $k,
                                    'attribute_id'      => $filter->id,
                                    'need_to_skip' => 1,
                                    'user_id'        => \Auth::user()->id,
                                ]);
                                //END - #DEVTASK-4143
                            }
                        }
                    }
                }
                //START - Add CategoryEntry in TemporaryCategoryUpdation Table - #DEVTASK-4143
                else{
                    $category_status_updation =  \App\TemporaryCategoryUpdation::create([
                        'category_name'      => $k,
                        'attribute_id'      => 0,
                        'need_to_skip' => 1,
                        'user_id'        => \Auth::user()->id,
                    ]);
                }
                //END - #DEVTASK-4143
            }
        }

        return response()->json(["code" => 200, "message" => "Category updated successfully"]);
    }

}
