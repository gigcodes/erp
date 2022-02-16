<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class FlowCondition extends Model
{
    /**
     * @var string
        * @SWG\Property(property="condition_name",type="string")
        * @SWG\Property(property="message",type="string")
        * @SWG\Property(property="status",type="integer")
     */
    protected $fillable = [
        'condition_name',
        'message',
        'status',
    ];
}
