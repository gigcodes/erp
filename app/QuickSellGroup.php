<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickSellGroup extends Model
{
    protected $fillable = ['group','name','suppliers','brands','price','special_price'];

    public function getProductsIds(){
        return $this->hasMany('\App\ProductQuicksellGroup','quicksell_group_id','group');
    }
}
