<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreSocialAccount extends Model
{
    protected $fillable = [
        'store_website_id','platform','url','username','password'
    ];
}
