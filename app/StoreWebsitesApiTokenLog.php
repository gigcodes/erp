<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class StoreWebsitesApiTokenLog extends Model
{
    protected $fillable = [
        'user_id',
        'store_website_id',
        'store_website_users_id',
        'response',
        'status_code',
        'status',
    ];

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, 'id', 'store_website_id');
    }

    public function StoreWebsiteUsers()
    {
        return $this->hasOne(\App\StoreWebsiteUsers::class, 'id', 'store_website_users_id');
    }

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id');
    }
}
