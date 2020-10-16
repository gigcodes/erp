<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryStatusHistory extends Model
{
    protected $fillable = ['sku','date','in_stock'];
}
