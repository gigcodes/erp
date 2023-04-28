<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use seo2websites\MagentoHelper\MagentoHelper;

class PushPageToMagento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page;

    protected $updatedBy;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page, $updatedBy)
    {
        // Set product and website
        $this->page = $page;
        $this->updatedBy = $updatedBy;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Set time limit
            set_time_limit(0);

            // Load product and website
            $page = $this->page;
            $website = $page->storeWebsite;

            if ($website) {
                $storeWebsite = new \App\StoreWebsite();
                if ((isset($website->tag_id) && $website->tag_id != '')) {
                    $allWebsites = $storeWebsite->where('tag_id', $website->tag_id)->get();
                } else {
                    $allWebsites = $storeWebsite->where('id', $page->store_website_id)->get();
                }

                if (! empty($allWebsites)) {
                    foreach ($allWebsites as $websitekey => $website) {
                        //\Log::info("Store Website Data");
                        //\Log::info(print_r([$website->id,$website->website,$website->tag_id],true));
                        if ($website->website_source) {
                            // assign the stores  column
                            $fetchStores = \App\WebsiteStoreView::where('website_store_views.name', $page->name)
                                ->join('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')
                                ->join('websites as w', 'w.id', 'ws.website_id')
                                ->where('w.store_website_id', $page->store_website_id)
                                ->select('website_store_views.*')
                                ->get();

                            $stores = array_filter(explode(',', $page->stores));

                            if (! $fetchStores->isEmpty()) {
                                foreach ($fetchStores as $fetchStore) {
                                    $stores[] = $fetchStore->code;
                                }
                            }

                            $page->stores = implode(',', $stores);
                            $page->save();

                            $params = [];
                            $params['page'] = [
                                'identifier' => $page->url_key,
                                'title' => $page->title,
                                'meta_title' => $page->meta_title,
                                'meta_keywords' => $page->meta_keywords,
                                'meta_description' => $page->meta_description,
                                'content_heading' => $page->content_heading,
                                'content' => $page->content,
                                'active' => $page->active,
                                'platform_id' => $page->platform_id,
                                'page_id' => $page->id,
                                'updated_by' => optional($this->updatedBy)->id,
                            ];

                            if (! empty($stores)) {
                                foreach ($stores as $s) {
                                    $params['page']['store'] = $s;
                                    $id = MagentoHelper::pushWebsitePage($params, $website);
                                    if (! empty($id) && is_numeric($id)) {
                                        $page->platform_id = $id;
                                        $page->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['PushPageToMagento', $this->page->id];
    }
}
