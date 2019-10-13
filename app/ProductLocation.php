<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductLocation extends Model
{
    public $table = "product_location";
    protected $fillable = [
        'name',
    ];
}
