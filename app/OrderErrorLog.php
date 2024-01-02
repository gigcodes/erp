<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class OrderErrorLog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="id",type="integer")
     * @SWG\Property(property="order_id",type="string")
     * @SWG\Property(property="event_type",type="integer")
     * @SWG\Property(property="log",type="text")
     * @SWG\Property(property="created_at",type="date")
     */
    protected $fillable = ['id', 'order_id', 'event_type', 'log', 'created_at'];

    public function order()
    {
        return $this->belongsTo(\App\Order::class);
    }
}
