<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Waybill extends Model
{
    protected $table = 'waybills';

    protected $fillable = ['order_id', 'customer_id', 'awb', 'box_length', 'box_width', 'box_height', 'actual_weight', 'volume_weight', 'package_slip', 'pickup_date'];

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

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    public function waybill_track_histories()
    {
        return $this->hasMany('App\waybillTrackHistories');
    }
}
