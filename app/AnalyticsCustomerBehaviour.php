<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnalyticsCustomerBehaviour extends Model
{
    /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = array(
        'pages', 'pageviews', 'uniquePageviews', 
        'avgTimeOnPage', 'entrances', 'bounceRate',
        'exitRate', 'pageValue', ''
    );
}
