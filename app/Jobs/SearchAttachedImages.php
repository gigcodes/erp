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
    protected $first_time; 
    protected $is_matched; 

    public function __construct($id, $url, $req_data)
    {
        $this->id = $id; 
        $this->url = $url; 
        $this->req_data = $req_data; 
        $this->first_time = true; 
        $this->is_matched = false; 
    }

    public function handle()
    {
        set_time_limit(0);

        $id = $this->id;
        $ref_file = str_replace('|', '/', $this->url);
        $ref_file = str_replace("'", '', $ref_file);
        $params = $this->req_data;
        $chat_message = \App\ChatMessage::where('id', $id)->first();
        if(@file_get_contents($ref_file)){
            $i1 = CompareImagesHelper::createImage($ref_file);
                
            $i1 = CompareImagesHelper::resizeImage($i1,$ref_file);
            
            imagefilter($i1, IMG_FILTER_GRAYSCALE);
            
            $colorMean1 = CompareImagesHelper::colorMeanValue($i1);
            
            $bits1 = CompareImagesHelper::bits($colorMean1);

            $bits = implode($bits1); 

            DB::table('media')->whereNotNull('bits')->where('bits', '!=', 0)->where('bits', '!=', 1)->where('directory', 'like', '%product/%')->orderBy('id')->chunk(1000, function($medias)
             use ($bits, $chat_message)
            {
            foreach ($medias as $k => $m)
                {
                    $hammeringDistance = 0;
                    $m_bits = $m->bits; 
                    for($a = 0;$a<64;$a++)
                    {
                        if($bits[$a] != $m_bits[$a])
                        {
                            $hammeringDistance++;
                        }
                        
                    } 
                    if($hammeringDistance < 10){
                        $this->is_matched = true;
                        if($this->first_time){
                            $sp = SuggestedProduct::create([
                                'total' => 0,
                                'customer_id' => $chat_message->customer_id,
                                'chat_message_id' => $chat_message->id,
                            ]);
                            $this->first_time = false;
                        } 
                        $mediables = DB::table('mediables')->where('media_id', $m->id)->get();
                        if(count($mediables)){
                            foreach($mediables as $mediable){
                                SuggestedProductList::create([
                                    'customer_id' => $chat_message->customer_id,
                                    'product_id' => $mediable->mediable_id,
                                    'chat_message_id' => $chat_message->id,
                                    'suggested_products_id' => $sp->id
                                ]); 
                            }
                        }
                    }
                }
            });
        }

        $user = Auth::user();
        if($this->is_matched){
            $msg = 'Your image find process is completed.';
        }else{
            $msg = 'Your image find process is completed, No results found';
        } 
        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);

    }

}
