<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BroadcastMessageNumber extends Model
{
    
    /**
     * @var string
     * @SWG\Property(property="broadcast_message_id",type="integer")
     * @SWG\Property(property="type_id",type="integer")
     * @SWG\Property(property="type",type="string")
     */
    protected $fillable = [
        'broadcast_message_id', 'type_id', 'type',
    ];

}
