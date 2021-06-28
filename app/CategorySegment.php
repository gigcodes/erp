<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CategorySegment extends Model
{


    public function categorySegmentDiscount()
    {
        return $this->hasMany(CategorySegmentDiscount::class,'category_segment_id','id');
    }


    // public function brandsSegmenets()
    // {
    //     $this->belongsToMany(Brand::class,'category_segment_discounts','brand_id','category_segment_id')->withPivot('amount');
    // }

}
