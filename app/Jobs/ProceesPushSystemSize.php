<?php

namespace App\Jobs;

use App\Reply;
use App\Models\ReplyLog;
use App\StoreWebsite;
use App\SystemSizeManager;
use App\SystemSizeRelation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProceesPushSystemSize implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $data;

    public function __construct($data)
    {
        // Assign the variable received from Request
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('ProceesPushSystemSize Handle function');
        $systemSizeManagerId = $this->data;

        //Loop the records one by one
        $this->_processSingleSystemSize($systemSizeManagerId);
    }

    private function _processSingleSystemSize($systemSizeManagerId)
    {
        try {
            $systemSizeRelations = SystemSizeRelation::select(
                'system_size_relations.id',
                'system_size_relations.size',
                'system_size_managers.category_id'
            )
            ->leftjoin('system_size_managers', 'system_size_managers.id', 'system_size_relations.system_size_manager_id')
            ->where('system_size_manager_id', $systemSizeManagerId)
            ->get();
                                                    
            if (empty($systemSizeRelations)) {
                \Log::info('System size manager ID not found in table' . json_encode($systemSizeManagerId));

                return false;
            }

            \Log::info('System size manager Found');

            foreach ($systemSizeRelations as $key => $systemSizeRelation) {
                $categoryId = $systemSizeRelation->category_id;
                $size = $systemSizeRelation->size;

                $allWebsites = StoreWebsite::all();

                if (! empty($allWebsites)) {
                    foreach ($allWebsites as $websitekey => $websitevalue) {
                        if (empty($websitevalue->id)) {
                            break;
                        }

                        $store_website_id = $websitevalue->id;

                        //Get stores of every single site
                        //where('website_store_views.name', $replyInfo->language ?? 'English') ->

                        $fetchStores = \App\WebsiteStoreView::join('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')
                                        ->join('websites as w', 'w.id', 'ws.website_id')
                                        ->join('store_websites as sw', 'sw.id', 'w.store_website_id')
                                        ->where('sw.id', $store_website_id)
                                        ->select('website_store_views.website_store_id', 'website_store_views.*', 'website_store_views.code')
                                        ->get();

                        $stores = [];
                        if (! $fetchStores->isEmpty()) {
                            foreach ($fetchStores as $fetchStore) {
                                $stores[] = $fetchStore->code;
                            }
                        }

                        \Log::info('Stores');
                        \Log::info($stores);

                        //get the Magento URL and token
                        $url = $websitevalue->magento_url;
                        $api_token = $websitevalue->api_token;

                        if (! empty($url) && ! empty($api_token) && ! empty($stores)) {
                            foreach ($stores as $key => $storeValue) {
                                $urlSystemSizePush = $url . "/rest/V1/size/config";
                                $postdata = "{\n    \"sizeConfig\": {\n        \"value\": \"$size\",\n        \"category_id\": \"$categoryId\",\n        \"store_id\": \"$storeValue\"\n    }\n}";
                                $ch = curl_init();

                                curl_setopt($ch, CURLOPT_URL, $urlSystemSizePush);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

                                $headers = [];
                                $headers[] = 'Authorization: Bearer ' . $api_token;
                                $headers[] = 'Content-Type: application/json';
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                                $response = curl_exec($ch);

                                $response = json_decode($response);

                                curl_close($ch);

                                if (! empty($response->path)) { //This means latest is pushed to server
                                    // $replyInfo->is_pushed = 1;
                                    // $replyInfo->save();
                                    // (new ReplyLog)->addToLog($replyInfo->id, 'System pushed FAQ on ' . $url . ' with ID ' . $store_website_id . ' on store ' . $storeValue . ' ', 'Push');
                                } else {
                                    // (new ReplyLog)->addToLog($replyInfo->id, ' Error while pushing FAQ on Store ' . $storeValue . ' : ' . json_encode($response), 'Push');
                                }

                                \Log::info('Got response from API after pushing the system size  to server');
                                \Log::info($postdata);
                                \Log::info(json_encode($response));
                            }
                        } else {
                            // (new ReplyLog)->addToLog($replyInfo->id, ' URL or API token not found linked with this FAQ ', 'Push');
                            \Log::info(
                                'URL or API token not found linked with reply id ' . json_encode($systemSizeManagerId)
                            );
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::info('Error while pushing system size');
            \Log::info($e->getMessage());
            \Log::info($e->getLine());
        }
    }
}
