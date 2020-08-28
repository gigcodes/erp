<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class BrandCategorySizeChart extends Model
{
    use Mediable;
    protected $table = 'brand_category_size_charts';
    protected $fillable = ['brand_id', 'category_id', 'store_website_id'];


    public static function getSizeChat($brandId, $categoryId , $siteId)
    {
        $img = [];
        
        $sizeChart = self::where("brand_id",$brandId)->where("category_id",$categoryId)->where("store_website_id",$siteId)->first();
        if(!empty($sizeChart) && $sizeChart->hasMedia(["size_chart"])) {
            $medias = $sizeChart->getMedia(["size_chart"]);
            foreach($medias as $media) {
                $img[] = $media->getUrl();
            }
        }
        
        return $img;
    }
}
