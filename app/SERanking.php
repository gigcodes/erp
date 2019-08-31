<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SERanking extends Model
{
    /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = array(
        'id', 'name', 'group_id', 'link',
        'first_check_date'
    );
}
