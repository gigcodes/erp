<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BackLinkChecker extends Model
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
     * @SWG\Property(enum={"domains", "links", "link_type", "review_numbers", "rank", "rating", "serp_id", "snippet", "title", "visible_link"})
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
