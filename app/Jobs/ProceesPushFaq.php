<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Reply;

class ProceesPushFaq implements ShouldQueue
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
        $reply_id = $this->data;

        //Loop the records one by one
        $this->_processSingleFaq($reply_id);
    }

    private function _processSingleFaq($reply_id)
    {
        $Reply = new Reply();

        $searchArray = (array) $reply_id;

        try {
            $replyInfoArray = $Reply
                ->select("replies.store_website_id", "magento_url", "stage_magento_url", "dev_magento_url", "stage_api_token", "dev_api_token", "api_token", "replies.reply", "rep_cat.name", "replies.category_id", "replies.id")
                ->join("store_websites", "store_websites.id", "=", "replies.store_website_id")
                ->join("reply_categories as rep_cat", "rep_cat.id", "=", "replies.category_id")
                ->whereIn("replies.id", $searchArray)
                ->get();

            if (empty($replyInfoArray)) {
                \Log::info("Reply ID not found in table" . json_encode($reply_id) );
                return false;
            }

            foreach ($replyInfoArray as $key => $replyInfo) {
                //get list of all store websites
                $StoreWebsite   =   new \App\StoreWebsite();
                $allWebsites    =   $StoreWebsite->getAllTaggedWebsite($replyInfo->store_website_id );

               
                if(!empty($allWebsites)){
                    foreach ($allWebsites as $websitekey => $websitevalue) {
                        if (empty($websitevalue->id)) {
                            break;
                        }

                        $store_website_id = $websitevalue->id;

                        //Get stores of every single site

                        $fetchStores = \App\WebsiteStoreView::join("website_stores as ws", "ws.id", "website_store_views.website_store_id") 
                                        ->join("websites as w", "w.id", "ws.website_id") 
                                        ->join("store_websites as sw", "sw.id", "w.store_website_id") 
                                        ->where("sw.id", $store_website_id) 
                                        ->select("website_store_views.website_store_id", "website_store_views.code") 
                                        ->get();

                        if (!$fetchStores->isEmpty()) {
                            foreach ($fetchStores as $fetchStore) {
                                $stores[] = $fetchStore->code;
                            }
                        }

                        //get the Magento URL and token
                        $url = $replyInfo->magento_url;
                        $api_token = $replyInfo->api_token;

                        //create a payload for API
                        $faqQuestion = $replyInfo->name;
                        $faqAnswer = $replyInfo->reply;
                        $faqCategoryId = $replyInfo->category_id;
                        $faqCategoryId = 1;

                        if (!empty($url) && !empty($api_token)) {
                            foreach ($stores as $key => $storeValue) {

                                $platformInfo = \App\Models\ReplyPushStore::where(["reply_id" => $replyInfo->id, "store_id" => $storeValue, ])->first();

                                if ($platformInfo && !empty($platformInfo->id)) {
                                    $postdata = "{\n    \"faq\": {\n        \"faq_category_id\": \"$faqCategoryId\",\n        \"id\": \"$platformInfo->platform_id\",\n        \"faq_question\": \"$faqQuestion\",\n        \"faq_answer\": \"$faqAnswer\",\n        \"is_active\": true,\n        \"sort_order\": 10\n    }\n}";
                                } else {
                                    $postdata = "{\n    \"faq\": {\n        \"faq_category_id\": \"$faqCategoryId\",\n        \"faq_question\": \"$faqQuestion\",\n        \"faq_answer\": \"$faqAnswer\",\n        \"is_active\": true,\n        \"sort_order\": 10\n    }\n}";
                                }

                                $ch = curl_init();

                                curl_setopt($ch, CURLOPT_URL, $url . "/" . $storeValue . "/rest/V1/faq"); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

                                $headers = [];
                                $headers[] = "Authorization: Bearer " . $api_token;
                                $headers[] = "Content-Type: application/json";
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                                $response = curl_exec($ch);

                                $response = json_decode($response);

                                curl_close($ch);

                                //if we got the response
                                if (!empty($response->id) && empty($platformInfo->id) ) {
                                    \App\Models\ReplyPushStore::create([
                                        "reply_id" => $replyInfo->id,
                                        "store_id" => $storeValue,
                                        "platform_id" => $response->id,
                                    ]);
                                }

                                \Log::info("Got response from API after pushing the FAQ to server"); \Log::info($postdata);
                                \Log::info(json_encode($response));
                            }
                        } else {
                            \Log::info(
                                "URL or API token not found linked with reply id " .
                                    json_encode($reply_id)
                            );
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::info("Error while pushing faq");
            \Log::info($e->getMessage());
        }
    }
}
