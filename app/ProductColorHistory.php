<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductColorHistory extends Model
{
    protected $fillable = ['product_id','old_color','color','user_id'];

    public function product()
    {
        return $this->hasOne(\App\Product::class, "id","product_id");
    }
}
