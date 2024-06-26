<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleSearchAnalytics extends Model
{
    protected $fillable = ['clicks', 'impressions', 'site_id', 'ctr', 'position', 'country', 'device', 'query', 'page', 'search_apperiance', 'date', 'indexed', 'not_indexed', 'not_indexed_reason', 'mobile_usable', 'enhancements'];

    public function site()
    {
        return $this->belongsTo(\App\Site::class, 'site_id', 'id');
    }
}
