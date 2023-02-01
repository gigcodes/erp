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
        $this->data    =   $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $reply_id       =   $this->data;

        $Reply          =   new Reply;

        try {

            $replyInfo      =   $Reply->select('magento_url','stage_magento_url','dev_magento_url','stage_api_token','dev_api_token','api_token','replies.reply','rep_cat.name','replies.category_id')
            ->join('store_websites','store_websites.id','=','replies.store_website_id')
            ->join('reply_categories as rep_cat','rep_cat.id','=','replies.category_id')
            ->where('replies.id', $reply_id)
            ->first();



            if(empty($replyInfo)){
                \Log::info('Reply ID not found in table'. $reply_id );
                return false;
            }

            //get the Magento URL and token
            $url            =   $replyInfo->magento_url;
            $api_token      =   $replyInfo->api_token;


            //create a payload for API
            $faqQuestion    =   $replyInfo->name;
            $faqAnswer      =   $replyInfo->reply;
            $faqCategoryId  =   $replyInfo->category_id;

            if(!empty($url) && !empty($api_token)){

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $url.'/rest/V1/faq',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>     '{
                        "faqQuestion":"'.$faqQuestion.'", "faqAnswer":"'.$faqAnswer.'",
                        "faqCategoryId":"'.$faqCategoryId.'" 
                    }',
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$api_token,
                  ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                \Log::info('Got response from API after pushing the FAQ to server');
                \Log::info($response);

                $data   =  json_decode(json_decode($response));
                return $data; 
                
            }
            else{
                \Log::info('URL or API token not found linked with reply id '. $reply_id);
            }   
        }
        catch(\Exception $e){
            \Log::info('Error while pushing faq');
            \Log::info($e->getMessage());
        }
    }
}
