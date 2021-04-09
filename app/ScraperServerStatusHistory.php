<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class ScraperServerStatusHistory extends Model
{
    protected $fillable = [
        'scraper_name',
        'scraper_string',
        'server_id',
        'start_time'
    ];
}
