<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Analytics extends Model
{
    
    /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    /**
     * @var string
     * @SWG\Property(enum={"operatingSystem", "user_type", "time", "page_path", "country", "city", "social_network", "date" ,"device_info", "sessions", "pageviews", "bounceRate", "avgSessionDuration",
        "timeOnPage"})
     */
    protected $fillable = array(
        'operatingSystem', 'user_type', 'time', 'page_path',
        'country', 'city', 'social_network', 'date' ,'device_info',
        'sessions', 'pageviews', 'bounceRate', 'avgSessionDuration',
        'timeOnPage'
    );
}
