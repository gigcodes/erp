<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Waybill extends Model
{
    
    protected $appends = ['dimension'];

    /**
     * Get order detail
     */
    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id');
    }

    public function getDimensionAttribute()
    {
        return $this->box_length * $this->box_width * $this->box_height;
    }
}
