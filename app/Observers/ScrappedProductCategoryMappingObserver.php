<?php

namespace App\Observers;

use App\ScrapedProducts;

class ScrappedProductCategoryMappingObserver
{
    /**
     * Handle the ScrapedProducts "created" event.
     *
     * @return void
     */
    public function created(ScrapedProducts $scrapedproducts)
    {
        $this->create($scrapedproducts);
    }

    /**
     * Handle the ScrapedProducts "updated" event.
     *
     * @return void
     */
    public function updated(ScrapedProducts $scrapedproducts)
    {
        $this->create($scrapedproducts);
    }

    /**
     * Handle the ScrapedProducts "deleted" event.
     *
     * @return void
     */
    public function deleted(ScrapedProducts $scrapedproducts)
    {
        //
    }

    /**
     * Handle the ScrapedProducts "restored" event.
     *
     * @return void
     */
    public function restored(ScrapedProducts $scrapedproducts)
    {
        //
    }

    /**
     * Handle the ScrapedProducts "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(ScrapedProducts $scrapedproducts)
    {
        //
    }

    protected function create($scrapedproducts)
    {
    }
}
