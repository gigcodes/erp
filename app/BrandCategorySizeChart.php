<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BrandCategorySizeChart extends Model
{

    use Mediable;
    /**
     * @var string
     * @SWG\Property(enum={"brand_category_size_charts"})
     */
    protected $table = 'brand_category_size_charts';
    /**
     * @var string
     * @SWG\Property(enum={"brand_id", "category_id", "store_website_id"})
     */
    protected $fillable = ['brand_id', 'category_id', 'store_website_id'];


    public static function getSizeChat($brandId, $categoryId , $siteId, $absPath = false)
    {
        $img = [];
        
        $sizeChart = self::where("brand_id",$brandId)->where("category_id",$categoryId)->first();
        if(!empty($sizeChart) && $sizeChart->hasMedia(["size_chart"])) {
            $medias = $sizeChart->getMedia(["size_chart"]);
            foreach($medias as $media) {
                if($absPath) {
                    $img[] = $media->getAbsolutePath();
                }else{
                    $img[] = $media->getUrl();
                }
            }
        }
        
        return $img;
    }
}
