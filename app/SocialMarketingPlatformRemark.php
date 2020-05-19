<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialMarketingPlatformRemark extends Model
{
    protected $fillable = [
        'social_marketing_platform_id',
        'remarks',
        'created_by',
        'created_at',
        'updated_at',
    ];
}
