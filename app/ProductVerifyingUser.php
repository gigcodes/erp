<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductVerifyingUser extends Model
{
    //

    protected $fillable = [
        'product_id',
        'user_id',
        'created_at',
        'updated_at'
    ];

}
