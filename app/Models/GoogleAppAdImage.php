<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleAppAdImage extends Model
{
    use HasFactory;

    public $fillable = [
        'google_app_ad_id',
        'google_asset_id',
        'name',
    ];
}
