<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WatsonAccount extends Model
{
    protected $fillable = [
        'store_website_id',
        'api_key',
        'work_space_id',
        'assistant_id',
        'url',
        'is_active'
    ];

    public function storeWebsite()
    {
        return $this->belongsTo('App\StoreWebsite', 'store_website_id');
    }
}
