<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErpPriority extends Model
{
    /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = array(
        'model_id', 'model_type'
    );

}
