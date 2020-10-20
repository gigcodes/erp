<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryStatusHistory extends Model
{
    protected $fillable = ['product_id','date','in_stock'];
}
