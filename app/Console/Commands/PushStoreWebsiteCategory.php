<?php

namespace App\Console\Commands;

use App\Category;
use App\StoreWebsiteCategory;
use Illuminate\Console\Command;
use seo2websites\MagentoHelper\MagentoHelper;

class PushStoreWebsiteCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push-to-magento:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Category to magento';

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
        $notInclude = [1,143,144];
        //
        $limitOfCat = $this->ask('Which category need to push ?');
        $limit      = $this->ask('Which website you need to push');

        if(!empty($limitOfCat)) {
            $catIds = explode(",", $limitOfCat);
            $categories    = Category::query()->whereIn("id",$catIds)->orderBy("parent_id", "asc")->get();
        }else{
            $categories    = Category::query()->whereNotIn('id',$notInclude)->whereNotIn('parent_id',$notInclude)->orderBy("parent_id", "asc")->get();
        }
        
        if(!empty($limit)) {
            $limit = explode(",", $limit);
            $storeWebsites = \App\StoreWebsite::whereIn("id",$limit)->where("api_token", "!=", "")->where("website_source", "magento")->get();
        }else{
            $storeWebsites = \App\StoreWebsite::where("api_token", "!=", "")->where("website_source", "magento")->get();
        }

        if (!$categories->isEmpty()) {
            foreach ($categories as $category) {
                echo "$category->title started to push\n";
                if (!$storeWebsites->isEmpty()) {
                    foreach ($storeWebsites as $store) {
                        $swi = $store->id;
                        if ($category->parent_id == 0) {
                            $case = 'single';
                        } elseif ($category->parent->parent_id == 0) {
                            $case = 'second';
                        } elseif ($category->parent->parent->parent_id == 0) {
                            $case = 'third';
                        } else {
                            $case = 'fourth';
                        }
                        echo "$store->website started to $case push\n";
                        // start to push category on site
                        if ($case == 'single') {

                            $mainCategory = StoreWebsiteCategory::where('store_website_id', $swi)
                                ->where('category_id', $category->id)
                                ->where('remote_id','>',0)
                                ->first();

                            if(!$mainCategory) {
                                $data['id']       = $category->id;
                                $data['level']    = 1;
                                $data['name']     = ucwords($category->title);
                                $data['parentId'] = 0;
                                $parentId         = 0;

                                $categ = MagentoHelper::createCategory($parentId, $data, $swi);
                                if ($category) {
                                    $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)
                                    ->where('category_id', $category->id)
                                    ->where('remote_id', $categ)
                                    ->first();
                                    if (empty($checkIfExist)) {
                                        $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                        $storeWebsiteCategory->category_id      = $category->id;
                                        $storeWebsiteCategory->store_website_id = $swi;
                                        $storeWebsiteCategory->remote_id        = $categ;
                                        $storeWebsiteCategory->save();
                                    }
                                }
                            }
                        }

                        //if case second
                        if ($case == 'second') {
                            $parentCategory = StoreWebsiteCategory::where('store_website_id', $swi)
                                ->where('category_id', $category->parent->id)
                                ->where('remote_id','>',0)
                                ->first();
                            //if parent remote null then send to magento first
                            if (empty($parentCategory)) {

                                $data['id']       = $category->parent->id;
                                $data['level']    = 1;
                                $data['name']     = ucwords($category->parent->title);
                                $data['parentId'] = 0;
                                $parentId         = 0;

                                $parentCategoryDetails = MagentoHelper::createCategory($parentId, $data, $swi);

                                if ($parentCategoryDetails) {
                                    $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)
                                        ->where('category_id', $category->parent->id)
                                        ->where('remote_id', $parentCategoryDetails)
                                        ->first();

                                    if (empty($checkIfExist)) {
                                        $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                        $storeWebsiteCategory->category_id      = $category->parent->id;
                                        $storeWebsiteCategory->store_website_id = $swi;
                                        $storeWebsiteCategory->remote_id        = $parentCategoryDetails;
                                        $storeWebsiteCategory->save();
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

                            $categoryDetail = MagentoHelper::createCategory($parentRemoteId, $data, $swi);

                            if ($categoryDetail) {

                                $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)
                                    ->where('category_id', $category->id)
                                    ->where('remote_id', $categoryDetail)
                                    ->first();

                                if (empty($checkIfExist)) {
                                    $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                    $storeWebsiteCategory->category_id      = $category->id;
                                    $storeWebsiteCategory->store_website_id = $swi;
                                    $storeWebsiteCategory->remote_id        = $categoryDetail;
                                    $storeWebsiteCategory->save();
                                }
                            }
                        }

                        //if case third
                        if ($case == 'third') {
                            //Find Parent
                            $parentCategory = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $category->id)->where('remote_id','>',0)->first();

                            //Check if parent had remote id
                            if (empty($parentCategory)) {

                                //check for grandparent
                                $grandCategory       = Category::find($category->parent->id);
                                $grandCategoryDetail = StoreWebsiteCategory::where('store_website_id', $swi)
                                    ->where('category_id', $grandCategory->parent->id)
                                    ->where('remote_id','>',0)
                                    ->first();

                                if (empty($grandCategoryDetail)) {

                                    $data['id']       = $grandCategory->parent->id;
                                    $data['level']    = 1;
                                    $data['name']     = ucwords($grandCategory->parent->title);
                                    $data['parentId'] = 0;
                                    $parentId         = 0;

                                    $grandCategoryDetails = MagentoHelper::createCategory($parentId, $data, $swi);

                                    if ($grandCategoryDetails) {
                                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)
                                            ->where('category_id', $grandCategory->parent->id)
                                            ->where('remote_id', $grandCategoryDetails)
                                            ->first();

                                        if (empty($checkIfExist)) {
                                            $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                            $storeWebsiteCategory->category_id      = $grandCategory->parent->id;
                                            $storeWebsiteCategory->store_website_id = $swi;
                                            $storeWebsiteCategory->remote_id        = $grandCategoryDetails;
                                            $storeWebsiteCategory->save();
                                        }

                                    }

                                    $grandRemoteId = $grandCategoryDetails;

                                } else {
                                    $grandRemoteId = $grandCategoryDetail->remote_id;
                                }
                                //Search for child category

                                $childCategoryE = StoreWebsiteCategory::where('store_website_id', $swi)
                                    ->where('category_id', $category->parent->id)
                                    ->where('remote_id','>',0)
                                    ->first();

                                if(!$childCategoryE) {
                                    $data['id']       = $category->parent->id;
                                    $data['level']    = 2;
                                    $data['name']     = ucwords($category->parent->title);
                                    $data['parentId'] = $grandRemoteId;
                                    $parentId         = $grandRemoteId;

                                    $childCategoryDetails = MagentoHelper::createCategory($parentId, $data, $swi);

                                    $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)
                                        ->where('category_id', $category->parent->id)
                                        ->where('remote_id', $childCategoryDetails)
                                        ->first();

                                    if (empty($checkIfExist)) {
                                        $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                        $storeWebsiteCategory->category_id      = $category->parent->id;
                                        $storeWebsiteCategory->store_website_id = $swi;
                                        $storeWebsiteCategory->remote_id        = $childCategoryDetails;
                                        $storeWebsiteCategory->save();
                                    }

                                }else{
                                    $childCategoryDetails = $childCategoryE->remote_id;
                                } 

                                $data['id']       = $category->id;
                                $data['level']    = 3;
                                $data['name']     = ucwords($category->title);
                                $data['parentId'] = $childCategoryDetails;

                                $categoryDetail = MagentoHelper::createCategory($childCategoryDetails, $data, $swi);
                                if ($categoryDetail) {
                                    $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)
                                        ->where('category_id', $category->id)
                                        ->where('remote_id', $categoryDetail)
                                        ->first();

                                    if (empty($checkIfExist)) {
                                        $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                        $storeWebsiteCategory->category_id      = $category->id;
                                        $storeWebsiteCategory->store_website_id = $swi;
                                        $storeWebsiteCategory->remote_id        = $categoryDetail;
                                        $storeWebsiteCategory->save();
                                    }
                                }
                            }
                        }


                        if ($case == 'fourth') {
                            //Find Parent
                            $main = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $category->id)->where('remote_id','>',0)->first();

                            //Check if parent had remote id
                            if (empty($main)) {

                                //check for grandparent
                                $first = $category->parent->parent->parent->id;
                                
                                $storewebsiteFirst = StoreWebsiteCategory::where('store_website_id', $swi)
                                    ->where('category_id', $first)
                                    ->where('remote_id','>',0)
                                    ->first();

                                if(empty($storewebsiteFirst)) {

                                    $firstModel = Category::find($first); 

                                    $data['id']       = $firstModel->id;
                                    $data['level']    = 1;
                                    $data['name']     = ucwords($firstModel->title);
                                    $data['parentId'] = 0;
                                    $parentId         = 0;

                                    $grandGrandCategoryDetails = MagentoHelper::createCategory($parentId, $data, $swi);

                                    if ($grandGrandCategoryDetails) {
                                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)
                                            ->where('category_id', $firstModel->id)
                                            ->where('remote_id', $grandGrandCategoryDetails)
                                            ->first();

                                        if (empty($checkIfExist)) {
                                            $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                            $storeWebsiteCategory->category_id      = $firstModel->id;
                                            $storeWebsiteCategory->store_website_id = $swi;
                                            $storeWebsiteCategory->remote_id        = $grandGrandCategoryDetails;
                                            $storeWebsiteCategory->save();
                                        }

                                    }

                                    $grandGrandRemoteId = $grandGrandCategoryDetails;
                                }else{
                                    $grandGrandRemoteId = $storewebsiteFirst->remote_id;
                                }



                                $grandCategory       = Category::find($category->parent->id);
                                $grandCategoryDetail = StoreWebsiteCategory::where('store_website_id', $swi)
                                    ->where('category_id', $grandCategory->parent->id)
                                    ->where('remote_id','>',0)
                                    ->first();

                                if (empty($grandCategoryDetail)) {

                                    $data['id']       = $grandCategory->parent->id;
                                    $data['level']    = 2;
                                    $data['name']     = ucwords($grandCategory->parent->title);
                                    $data['parentId'] = $grandGrandRemoteId;
                                    $parentId         = $grandGrandRemoteId;

                                    $grandCategoryDetails = MagentoHelper::createCategory($parentId, $data, $swi);

                                    if ($grandCategoryDetails) {
                                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)
                                            ->where('category_id', $grandCategory->parent->id)
                                            ->where('remote_id', $grandCategoryDetails)
                                            ->first();

                                        if (empty($checkIfExist)) {
                                            $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                            $storeWebsiteCategory->category_id      = $grandCategory->parent->id;
                                            $storeWebsiteCategory->store_website_id = $swi;
                                            $storeWebsiteCategory->remote_id        = $grandCategoryDetails;
                                            $storeWebsiteCategory->save();
                                        }

                                    }

                                    $grandRemoteId = $grandCategoryDetails;

                                } else {
                                    $grandRemoteId = $grandCategoryDetail->remote_id;
                                }
                                //Search for child category

                                $childCategoryE = StoreWebsiteCategory::where('store_website_id', $swi)
                                    ->where('category_id', $category->parent->id)
                                    ->where('remote_id','>',0)
                                    ->first();

                                if(!$childCategoryE) {
                                    $data['id']       = $category->parent->id;
                                    $data['level']    = 3;
                                    $data['name']     = ucwords($category->parent->title);
                                    $data['parentId'] = $grandRemoteId;
                                    $parentId         = $grandRemoteId;

                                    $childCategoryDetails = MagentoHelper::createCategory($parentId, $data, $swi);

                                    $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)
                                        ->where('category_id', $category->parent->id)
                                        ->where('remote_id', $childCategoryDetails)
                                        ->first();

                                    if (empty($checkIfExist)) {
                                        $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                        $storeWebsiteCategory->category_id      = $category->parent->id;
                                        $storeWebsiteCategory->store_website_id = $swi;
                                        $storeWebsiteCategory->remote_id        = $childCategoryDetails;
                                        $storeWebsiteCategory->save();
                                    }

                                }else{
                                    $childCategoryDetails = $childCategoryE->remote_id;
                                } 

                                $data['id']       = $category->id;
                                $data['level']    = 4;
                                $data['name']     = ucwords($category->title);
                                $data['parentId'] = $childCategoryDetails;

                                $categoryDetail = MagentoHelper::createCategory($childCategoryDetails, $data, $swi);
                                if ($categoryDetail) {
                                    $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)
                                        ->where('category_id', $category->id)
                                        ->where('remote_id', $categoryDetail)
                                        ->first();

                                    if (empty($checkIfExist)) {
                                        $storeWebsiteCategory                   = new StoreWebsiteCategory();
                                        $storeWebsiteCategory->category_id      = $category->id;
                                        $storeWebsiteCategory->store_website_id = $swi;
                                        $storeWebsiteCategory->remote_id        = $categoryDetail;
                                        $storeWebsiteCategory->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

    }
}
