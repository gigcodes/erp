<?php

namespace App\Models;
use App\User;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VarnishStats extends Model
{
    use HasFactory;

    protected $fillable = ['created_by', 'store_website_id', 'assets_manager_id', 'server_name', 'server_ip', 'website_name', 'cache_name' ,'cache_hit' ,'cache_miss', 'cache_hitpass', 'cache_hitrate', 'cache_missrate', 'request_data', 'response_data'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->select('name', 'id');
    }

    public function storewebsite()
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id')->select('title', 'id');
    }
}