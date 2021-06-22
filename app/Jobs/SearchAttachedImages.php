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
        set_time_limit(0);

        $id = $this->id;
        $params = $this->req_data;
    
        $chat_messages = \App\ChatMessage::where('id', $id)->get();
        $process = Process::fromShellCommandline('find $(pwd) -maxdepth 5 -type f -not -path "*/\.*" | sort ', public_path('uploads/product'));
        $process->run(); 
        $files = explode("\n", $process->getOutput());
        $match_image_path = [];
        foreach($files as $key => $file){
            if($key == 0){
                $base_path = explode('public', $file)[0] . 'public';
                $chat_messages = \App\ChatMessage::where('id', $id)->get();
                $ref_file = explode(".com", $chat_messages[0]->media_url);
                $ref_file = $ref_file[count($ref_file) - 1];
                $ref_file = $base_path . $ref_file;
            }
            $compare = Process::fromShellCommandline('compare -metric ae -fuzz XX% ' . $ref_file . ' ' . $file . ' null: 2>&1', public_path('uploads/product'));
            $compare->run();
            $match = $compare->getOutput() == '0' ? true : false;
            if($match){
                dump([$file, $ref_file, $match]);
                if($file != $ref_file){
                    $match_image_path[] = $file;
                }
            }
        }
        
        $first_time = true;
        foreach ($match_image_path as $key => $value) {
            $name = explode('/', $value)[count(explode('/', $value)) - 1 ];
            $ext = explode('.', $name)[count(explode('.', $name)) - 1];
            $name = str_replace('.' . $ext, '', $name);
            $media = Media::where('filename', explode('.', $name)[0])->first();
            if($media !== null){
                $dir = explode('/', $media->directory);
                if(isset($dir[2])){
                    if($first_time){
                        $sp = SuggestedProduct::create([
                            'total' => 0,
                            'customer_id' => $chat_messages[0]->customer_id,
                            'chat_message_id' => $chat_messages[0]->id,
                        ]);
                        $first_time = false;
                    } 
                    SuggestedProductList::create([
                        'total' => 0,
                        'customer_id' => $chat_messages[0]->customer_id,
                        'product_id' => $dir[2],
                        'chat_message_id' => $chat_messages[0]->id,
                        'suggested_products_id' => $sp->id
                    ]); 
                    dump($dir[2]);
                }
            }
            dump([$name, $ext, $media]);
        }

        $user = Auth::user();
        $msg = 'Your image find process is completed.';
        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);
        // return view('ErpCustomer::product-search', compact('matched_images'));

    }

}
