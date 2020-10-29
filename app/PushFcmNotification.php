<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushFcmNotification extends Model
{
    protected $fillable=['title','token','body','url','store_website_id','sent_at','sent_on','created_by'];
}
