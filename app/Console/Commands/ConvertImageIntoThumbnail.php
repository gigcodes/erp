<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use Illuminate\Support\Facades\DB;
use App\Product;
use Plank\Mediable\Media;
use Plank\Mediable\Mediable;
use Illuminate\Support\Facades\Log;



class ConvertImageIntoThumbnail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convertImage:toThumbnail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


        // Media::create([
        //     'disk'=>'uploads',
        //     'directory'=>'aaaa',
        //     'filename'=>'D.O.N._Issue_2_Shoes_Red_FX6519_01_standard',
        //     'extension'=>'jpeg',
        //     'mime_type'=>'application/octet-stream',
        //     'aggregate_type'=>'image',
        //     'size'=>96622,
        // ]); 
    Product::orderBy('id')->chunk(1000, function($products)
       {
       foreach ($products as $key => $product)
           {
             
            $mediables  = DB::table('mediables')->where('mediable_id', $product->id)->get();
            foreach($mediables as $m){
                $media = Media::where('id',$m->media_id)->where('is_processed',0)->first();
                
                if($media){
                    
                    $m_url = $media->getAbsolutePath();
                    $file=@file_get_contents($m_url);
                    
                    if(!$file || !$m_url){
                     
                        $media->is_processed =2;
                        $media->save();
                        continue;
                    }

                    $file_info = pathinfo($m_url);

                   // $file_path = $file_info['dirname'] . '/'.$file_info['basename'];

                    $thumb_file_name = $file_info['filename'] . '_thumb.' . $file_info['extension'];
                    $thumb_file_path  =$file_info['dirname'] .'/' . $thumb_file_name;

                    list($original_width,$original_height) =getimagesize($m_url);
                    $thumbnail_width = 150;
                    $thumbnail_height= ($original_height/$original_width) * $thumbnail_width;
                    $is_thumbnail_made =  resizeCropImage($thumbnail_width,$thumbnail_height,$m_url,$thumb_file_path,80);

                    Log::channel('product-thumbnail')->info("['product_id'=>{$product->id},'media_id'=>{$media->id},'thumbnail_path'=>${thumb_file_path}]");

                    if($is_thumbnail_made){
                        $media->is_processed =1;
                        $media->save();
                      
                    }else{
                        $media->is_processed =3;
                        $media->save();
                    }
                }

            }
           }
       });

    }
}
