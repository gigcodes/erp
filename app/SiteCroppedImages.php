<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteCroppedImages extends Model
{
    //
    protected $fillable = [
        'website_id', 'product_id',
    ];

}
