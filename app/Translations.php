<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Translations extends Model 
{
    /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = [
        'text',
        'text_original',
        'from',
        'to'
    ];

    /**
     * Protected Date
     *
     * @access protected
     * @var    array $dates
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}