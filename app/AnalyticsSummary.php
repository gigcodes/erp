<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AnalyticsSummary extends Model
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
     * @SWG\Property(enum={"brand_name", "gender", "time", "country", "city"})
     */
    protected $fillable = array(
        'brand_name', 'gender', 'time', 
        'country', 'city'
    );
}
