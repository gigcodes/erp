<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Waybill extends Model
{
    protected $table = 'waybills';

    protected $fillable = [
        'order_id', 
        'customer_id', 
        'awb', 
        'box_length', 
        'box_width', 
        'box_height', 
        'actual_weight', 
        'volume_weight', 
        'package_slip', 
        'pickup_date',
        'from_customer_id',
        'from_customer_name',
        'from_city',
        'from_country_code',
        'from_customer_phone',
        'from_customer_address_1',
        'from_customer_address_2',
        'from_customer_pincode',
        'from_company_name',
        'to_customer_id',
        'to_customer_name',
        'to_city',
        'to_country_code',
        'to_customer_phone',
        'to_customer_address_1',
        'to_customer_address_2',
        'to_customer_pincode',
        'to_company_name',
        'cost_of_shipment',
        'duty_cost',
        'pickuprequest',
        'customer_id',
    ];

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
