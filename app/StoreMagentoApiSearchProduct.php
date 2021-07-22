<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreMagentoApiSearchProduct extends Model
{
    protected $fillable =
    [
        'website_id' ,
        'website' ,
        'sku' ,
        'size' ,
        'brands' ,
        'dimensions' ,
        'composition' ,
        'english',
        'arabic',
        'german',
        'spanish',
        'french',
        'italian',
        'japanese',
        'korean',
        'russian',
        'chinese',
    ];
}
