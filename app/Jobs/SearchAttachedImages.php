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
use Symfony\Component\Process\Process;
use \Plank\Mediable\Media;
use Illuminate\Support\Facades\DB;

class SearchAttachedImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $req_data; 
    protected $url; 

    public function __construct($id, $url, $req_data)
    {
        $this->id = $id; 
        $this->url = $url; 
        $this->req_data = $req_data; 
    }

    public function handle()
    {
        set_time_limit(0);

        $id = $this->id;
        $ref_file = str_replace('|', '/', $this->url);
        $ref_file = str_replace("'", '', $ref_file);
        $params = $this->req_data;
        $media_array = [];
        $is_matched = false;
        $chat_message = \App\ChatMessage::where('id', $id)->first();
        if(@file_get_contents($ref_file)){
            $i1 = CompareImagesHelper::createImage($ref_file);
                
            $i1 = CompareImagesHelper::resizeImage($i1,$ref_file);
            
            imagefilter($i1, IMG_FILTER_GRAYSCALE);
            
            $colorMean1 = CompareImagesHelper::colorMeanValue($i1);
            
            $bits1 = CompareImagesHelper::bits($colorMean1);

            $bits = implode($bits1); 
            $first_time = true;

            $xx = 0;
            $count = 0;
            $compared_media = 0;
            DB::table('media')->whereNotNull('bits')->where('bits', '!=', 0)->where('directory', 'like', '%product/%')->orderBy('id')->chunk(1000, function($medias) use ($bits, $xx, $count)
            {
                foreach ($medias as $k => $m)
                {
                    $hammeringDistance = 0;
                    $m_bits = $m->bits;
                    if($m_bits == '0'){
                        continue;
                    }
                    for($a = 0;$a<64;$a++)
                    {
                        if($bits[$a] != $m_bits[$a])
                        {
                            $hammeringDistance++;
                        }
                        
                    } 
                    if($hammeringDistance < 10){
                        $is_matched = true;
                        if($first_time){
                            $sp = SuggestedProduct::create([
                                'total' => 0,
                                'customer_id' => $chat_message->customer_id,
                                'chat_message_id' => $chat_message->id,
                            ]);
                            $first_time = false;
                        } 
                        $mediable = Mediable::where('media_id', $m->id)->first();
                        if($mediable && $mediable->mediable_id !== null){
                            SuggestedProductList::create([
                                'customer_id' => $chat_message->customer_id,
                                'product_id' => $mediable->mediable_id,
                                'chat_message_id' => $chat_message->id,
                                'suggested_products_id' => $sp->id
                            ]); 
                        }
                    }
                }
            });
        }

        $user = Auth::user();
        if($is_matched){
            $msg = 'Your image find process is completed.';
        }else{
            $msg = 'Your image find process is completed, No results found';
        } 
        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);

    }

}
