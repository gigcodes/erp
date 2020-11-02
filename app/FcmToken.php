<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = ['token','store_website_id'];
}
