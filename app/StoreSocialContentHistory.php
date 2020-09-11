<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreSocialContentHistory extends Model
{
    protected $fillable = [
        'type','store_social_content_id','message','username'
    ];
}
