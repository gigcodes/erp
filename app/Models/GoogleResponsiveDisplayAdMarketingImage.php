<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleResponsiveDisplayAdMarketingImage extends Model
{
    use HasFactory;

    public $fillable = [
        'google_responsive_display_ad_id',
        'google_asset_id',
        'type',
        'name',
    ];
}
