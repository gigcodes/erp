<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store_order_status extends Model
{
    protected $fillable = ['order_status_id','store_website_id','status','store_master_status_id'];

    public function order_status() {
        return $this->belongsTo('App\OrderStatus');
    }

    public function store_website() {
        return $this->belongsTo('App\StoreWebsite');
    }

    public function store_master_status() {
        return $this->belongsTo('App\StoreMasterStatus');
    }
}
