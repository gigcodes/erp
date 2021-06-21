<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Library\Product\ProductSearch;
use App\SuggestedProductList;
use App\SuggestedProduct;
use App\Helpers\CompareImagesHelper;
use Auth;

class SearchAttachedImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $req_data; 

    public function __construct($id, $req_data)
    {
        $this->id = $id; 
        $this->req_data = $req_data; 
    }

    public function handle()
    {
        // Set time limit
        set_time_limit(0);

        $id = $this->id;
        $params = $this->req_data;
    
        $chat_messages = \App\ChatMessage::where('id', $id)->get();
        // $params = request()->all();
        $products = (new ProductSearch($params))
            ->getQuery()->get();
        $matched_images = [];
        if (@file_get_contents($chat_messages[0]->media_url)) {
            foreach ($products as $product) {
                $productImages = $product->getMedia(config('constants.media_tags'));
                if(count($productImages)){
                    foreach ($productImages as $productImage) {
                        $url = str_replace(' ', "%20", $productImage->getUrl());
                        if (@file_get_contents($url)) {
                            if(CompareImagesHelper::compare($chat_messages[0]->media_url,$url) < 10){
                                $matched_image = [];
                                $matched_image['image_url'] = $url;
                                $matched_image['product'] = $product;
                                array_push($matched_images, $matched_image);
                            }
                        }
                    }
                }
            }
        }
        if(count($matched_images)){
            $sp = SuggestedProduct::create([
                'total' => 0,
                'customer_id' => $chat_messages[0]->customer_id,
                'chat_message_id' => $chat_messages[0]->id,
            ]); 
            
            foreach($matched_images as $prod){
                $prod = $prod['product'];
                SuggestedProductList::create([
                    'total' => 0,
                    'customer_id' => $chat_messages[0]->customer_id,
                    'product_id' => $prod->id,
                    'chat_message_id' => $chat_messages[0]->id,
                    'suggested_products_id' => $sp->id
                ]); 
            }
        }

        $user = Auth::user();
        $msg = 'Your image find process is completed.';
        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);

    }

}
