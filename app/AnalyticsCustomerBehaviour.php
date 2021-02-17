<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AnalyticsCustomerBehaviour extends Model
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
     * @SWG\Property(enum={"pages", "pageviews", "uniquePageviews", "avgTimeOnPage", "entrances", "bounceRate",
        "exitRate", "pageValue"})
     */
    protected $fillable = array(
        'pages', 'pageviews', 'uniquePageviews', 
        'avgTimeOnPage', 'entrances', 'bounceRate',
        'exitRate', 'pageValue', ''
    );
}
