<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickSellGroup extends Model
{
    protected $fillable = ['group','name','suppliers','brands','price','special_price'];
}
