<?php

namespace App\Observers;

use App\Category;
use App\ScrappedCategoryMapping;

class ScrappedCategoryMappingObserver
{
    /**
     * Handle the category "created" event.
     *
     * @return void
     */
    public function created(Category $category)
    {
        //
    }

    /**
     * Handle the category "updated" event.
     *
     * @return void
     */
    public function updated(Category $category)
    {
        //
    }

    /**
     * Handle the category "deleted" event.
     *
     * @return void
     */
    public function deleted(Category $category)
    {
        //
    }

    /**
     * Handle the category "restored" event.
     *
     * @return void
     */
    public function restored(Category $category)
    {
        //
    }

    /**
     * Handle the category "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        //
    }

    protected function create($category)
    {
        $unKnownCategory   = Category::where('title', 'LIKE', '%Unknown Category%')->first();
        $unKnownCategories = explode(',', $unKnownCategory->references);
        $unKnownCategories = array_unique($unKnownCategories);

        $exist_data = ScrappedCategoryMapping::whereIn('name', $unKnownCategories)->get()->toArray();

        foreach ($unKnownCategories as $key => $val) {
            if (! in_array($val, $exist_data)) {
                ScrappedCategoryMapping::updateOrCreate([
                    'name' => $val,
                ], [
                    'name' => $val,
                ]);
            }
        }
    }
}
