<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Category;
use App\Language;
use App\StoreWebsite;
use App\StoreWebsiteCategory;
use Illuminate\Http\Response;
use App\StoreWebsiteCategorySeo;
use seo2websites\MagentoHelper\MagentoHelper;

class PushCategorySeoToMagento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $category;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($category)
    {
        $this->category = $category;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Set time limit
        set_time_limit(0);
        $id    = $this->category;

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

                    if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                        $categ = MagentoHelper::createCategory($parentId, $data, $storeId);
                    }
                    if ($categ) {
                        $storeWebsiteCategory->remote_id = $categ;
                        $storeWebsiteCategory->save();
                    }
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

                        if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {

                            $parentCategoryDetails = MagentoHelper::createCategory($parentId, $data, $storeId);

                        }
                        if ($parentCategoryDetails) {
                            $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->id)->first();
                            if (empty($checkIfExist)) {
                                $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                $storeWebsiteCategory->category_id      = $category->id;
                                $storeWebsiteCategory->store_website_id = $storeId;
                                $storeWebsiteCategory->remote_id        = $parentCategoryDetails;
                                $storeWebsiteCategory->save();
                            }else{
                                $checkIfExist->update(['remote_id' => $parentCategoryDetails]);
                            }
                        }

                        $parentRemoteId = $parentCategoryDetails;

                    } else {
                        $parentRemoteId = $parentCategory->remote_id;
                    }

                    $data['id']       = $category->id;
                    $data['level']    = 2;
                    $data['name']     = ucwords($category->title);
                    $data['parentId'] = $parentRemoteId;

                    if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {

                        $categoryDetail = MagentoHelper::createCategory($parentRemoteId, $data, $storeId);

                    }

                    if ($categoryDetail) {
                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->id)->first();
                        if (empty($checkIfExist)) {
                            $storeWebsiteCategory                   = new StoreWebsiteCategory();
                            $storeWebsiteCategory->category_id      = $category->id;
                            $storeWebsiteCategory->store_website_id = $storeId;
                            $storeWebsiteCategory->remote_id        = $categoryDetail;
                            $storeWebsiteCategory->save();
                        }else{
                            $checkIfExist->update(['remote_id' => $categoryDetail]);
                        }
                    }
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

                            $data['id']       = $category->id;
                            $data['level']    = 1;
                            $data['name']     = ($request->category_name) ? ucwords($request->category_name) : ucwords($category->title);
                            $data['parentId'] = 0;
                            $data['meta_title'] = $store_website_category_seo->meta_title;
                            $data['meta_keywords'] = $store_website_category_seo->meta_keywords;
                            $data['meta_description'] = $store_website_category_seo->meta_description;
                            $parentId         = 0;

                            if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {

                                $grandCategoryDetails = MagentoHelper::createCategory($parentId, $data, $storeId);

                            }

                            if ($grandCategoryDetails) {
                                $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->parent->id)->first();
                                if (empty($checkIfExist)) {
                                    $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                    $storeWebsiteCategory->category_id      = $category->parent->id;
                                    $storeWebsiteCategory->store_website_id = $storeId;
                                    $storeWebsiteCategory->remote_id        = $grandCategoryDetails;
                                    $storeWebsiteCategory->save();
                                }else{
                                    $checkIfExist->update(['remote_id' => $grandCategoryDetails]);
                                }
                            }

                            $grandRemoteId = $grandCategoryDetails;

                        } else {
                            $grandRemoteId = $grandCategoryDetail->remote_id;
                        }
                        //Search for child category

                        $data['id']       = $category->parent->id;
                        $data['level']    = 2;
                        $data['name']     = ucwords($category->parent->title);
                        $data['parentId'] = $grandRemoteId;
                        $parentId         = $grandRemoteId;

                        if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {

                            $childCategoryDetails = MagentoHelper::createCategory($parentId, $data, $storeId);

                        }

                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->parent->id)->first();
                        if (empty($checkIfExist)) {
                            $storeWebsiteCategory                   = new StoreWebsiteCategory();
                            $storeWebsiteCategory->category_id      = $category->parent->id;
                            $storeWebsiteCategory->store_website_id = $storeId;
                            $storeWebsiteCategory->remote_id        = $childCategoryDetails;
                            $storeWebsiteCategory->save();
                        }else{
                            $checkIfExist->update(['remote_id' => $childCategoryDetails]);
                        }

                        $data['id']       = $category->id;
                        $data['level']    = 3;
                        $data['name']     = ucwords($category->title);
                        $data['parentId'] = $childCategoryDetails;

                        if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {

                            $categoryDetail = MagentoHelper::createCategory($childCategoryDetails, $data, $storeId);

                        }

                        if ($categoryDetail) {
                            $checkIfExist = StoreWebsiteCategory::where('store_website_id', $storeId)->where('category_id', $category->id)->first();
                            if (empty($checkIfExist)) {
                                $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                $storeWebsiteCategory->category_id      = $category->id;
                                $storeWebsiteCategory->store_website_id = $storeId;
                                $storeWebsiteCategory->remote_id        = $categoryDetail;
                                $storeWebsiteCategory->save();
                            }else{
                                $checkIfExist->update(['remote_id' => $categoryDetail]);
                            }
                        }

                    }

                }

            }

            return response()->json(["code" => 200, "data" => [], "message" => "Records copied succesfully"]);
        }

        return response()->json(["code" => 500, "data" => [], "message" => "Page does not exist"]);
    }
}
