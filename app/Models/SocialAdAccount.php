<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAdAccount extends Model
{
    protected $fillable = [
        'store_website_id',
        'name',
        'ad_account_id',
        'page_token',
        'status',
    ];
}
