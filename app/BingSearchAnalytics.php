<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BingSearchAnalytics extends Model
{
    protected $fillable = ['clicks', 'impression', 'site_id', 'ctr', 'position', 'query', 'page', 'date', 'crawl_requests', 'crawl_errors', 'index_pages', 'crawl_information', 'keywords', 'pages'];

    public function site()
    {
        return $this->belongsTo(\App\BingSite::class, 'site_id', 'id');
    }
}
