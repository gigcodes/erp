<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;
use App\Brand;

class SkuFormatHistory extends Model
{
    protected $fillable = ['sku_format_id','old_sku_format','sku_format','user_id'];

    public function user()
    {
        return $this->hasOne(\App\User::class,'id','user_id');
    }

    public function skuFormat()
    {
        return $this->hasOne(\App\SkuFormat::class,'id','sku_format_id');
    }
    
}
