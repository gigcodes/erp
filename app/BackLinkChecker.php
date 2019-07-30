<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BackLinkChecker extends Model
{
    /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = array(
        'domains', 'links', 'link_type',
        'review_numbers', 'rank',
        'rating', 'serp_id', 'snippet',
        'title', 'visible_link'
    );

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
