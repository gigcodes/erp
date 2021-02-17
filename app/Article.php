<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Article extends Model
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
     * @SWG\Property(enum={"title", "description", "status", "remark", "assign_to", "posted_to" })
     */
    protected $fillable = array(
        'title', 'description', 'status', 'remark', 'assign_to', 'posted_to' 
    );

}
