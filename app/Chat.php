<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Chat extends Model
{
    //
    /**
     * @var string
     * @SWG\Property(enum={"userid", "sourceid", "messages"})
     */
    protected $fillable = ['userid','sourceid','messages'];
}
