<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSummary extends Model
{
    /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = array(
        'brand_name', 'gender', 'time', 
        'country', 'city'
    );
}
