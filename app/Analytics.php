<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = array(
        'operatingSystem', 'user_type', 'time', 'page_path',
        'country', 'city', 'social_network', 'date' ,'device_info',
        'sessions', 'pageviews', 'bounceRate', 'avgSessionDuration',
        'timeOnPage'
    );
}
