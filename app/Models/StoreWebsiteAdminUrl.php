<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class StoreWebsiteAdminUrl extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'store_website_id',
        'admin_url',
        'store_dir',
        'server_ip_address',
        'request_data',
        'response_data',
    ];
}