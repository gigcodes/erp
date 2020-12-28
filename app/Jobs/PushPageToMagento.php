<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use seo2websites\MagentoHelper\MagentoHelper;

class PushPageToMagento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page)
    {
        // Set product and website
        $this->page = $page;
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

        // Load product and website
        $page    = $this->page;
        $website = $page->storeWebsite;

        if ($website) {
            if ($website->website_source) {

                $params         = [];
                $params['page'] = [
                    "identifier"       => $page->url_key,
                    "title"            => $page->title,
                    "meta_title"       => $page->meta_title,
                    "meta_keywords"    => $page->meta_keywords,
                    "meta_description" => $page->meta_description,
                    "content_heading"  => $page->content_heading,
                    "content"          => $page->content,
                    "active"           => $page->active,
                    "platform_id"      => $page->platform_id,
                ];


                $stores = array_filter(explode(",", $page->stores));

                if (!empty($stores)) {
                    foreach ($stores as $s) {
                        $params['page']['store'] = $s;
                        $id = MagentoHelper::pushWebsitePage($params, $website);
                        if(!empty($id) && is_numeric($id)) {
                            $page->platform_id = $id;
                            $page->save();

                            // upload page here
                            $history = \App\StoreWebsitePageHistory::create([
                                "content" => $page->content,
                                "store_website_page_id" => $page->id,
                            ]);
                        }
                    }
                }

            }
        }
    }
}
