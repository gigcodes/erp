<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BloggerEmailTemplate extends Model
{
    protected $fillable = ['from','subject','message','cc','bcc','other','type'];
}
