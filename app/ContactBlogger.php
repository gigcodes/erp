<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactBlogger extends Model
{
    protected $fillable = ['name','email','instagram_handle','quote','status','other'];

}
