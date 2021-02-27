<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RejectedImages extends Model
{
    //
    protected $fillable = [
        'website_id', 'product_id', 'status'
    ];

}
