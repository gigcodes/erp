<?php

namespace App\Social;
use Illuminate\Database\Eloquent\Model;

class SocialConfig extends Model
{
    protected $fillable = [
        'store_website_id','platform','name','email','password','api_key','api_secret','token','status'
    ];
    
    public function storeWebsite()
    {
        return $this->hasOne('\App\StoreWebsite','id','store_website_id');
    }
}
