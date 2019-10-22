<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;
use App\Brand;

class SkuFormat extends Model
{
    protected $fillable = ['brand_id','category_id','sku_format'];

    public function category(){
        return $this->hasOne(Category::class ,'id','category_id');
    }

    public function brand(){
        return $this->hasOne(Brand::class,'id','brand_id');
    }
}
