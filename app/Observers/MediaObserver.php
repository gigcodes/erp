<?php

namespace App\Observers;

use \Plank\Mediable\Media;
use App\Helpers\CompareImagesHelper;

class MediaObserver
{
    public function created(Media $media)
    { 
        $this->updateBits($media);

        if($media){
            $m_url = $media->getAbsolutePath();
            $file=@file_get_contents($m_url);

            if(!$file || !$m_url){
                $media->is_processed =2;
                $media->save();
            }else{
                
                $file_info = pathinfo($m_url);
                $file_path = $file_info['dirname'] . '/'.$file_info['basename'];
                $thumb_file_name = $file_info['filename'] . '_thumb.' . $file_info['extension'];
                $thumb_file_path  =$file_info['dirname'] .'/' . $thumb_file_name;
                list($original_width,$original_height) =getimagesize($m_url);
                $thumbnail_width = 150;
                $thumbnail_height= ($original_height/$original_width) * $thumbnail_width;
                $is_thumbnail_made = resizeCropImage($thumbnail_width,$thumbnail_height,$m_url,$thumb_file_path,80);

                Log::channel('product-thumbnail')->info("['media_id'=>{$media->id},'thumbnail_path'=>${thumb_file_path}]");

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

    public function updated(Media $media)
    { 
        $this->updateBits($media);
    }

    public function saved(Media $media)
    { 
        $this->updateBits($media);
    }

    public static function updateBits($media)
    { 
        $ref_file = $media->getUrl(); 
        if(@file_get_contents($ref_file) && $media->aggregate_type == "image"){
            $i1 = CompareImagesHelper::createImage($ref_file);
            $i1 = CompareImagesHelper::resizeImage($i1,$ref_file);
            imagefilter($i1, IMG_FILTER_GRAYSCALE);
            $colorMean1 = CompareImagesHelper::colorMeanValue($i1);
            $bits1 = CompareImagesHelper::bits($colorMean1);
            
            Media::where('id', $media->id)->update([
                'bits' => implode($bits1) 
            ]); 
        }
    }
}
