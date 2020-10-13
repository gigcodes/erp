<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrappedFacebookUser extends Model
{
     protected $fillable = ['url','owner','bio','keyword'];
}
