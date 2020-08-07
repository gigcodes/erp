<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreSocialContentRemark extends Model
{
    protected $fillable = ['remarks', 'store_social_content_id', 'user_id'];
}
