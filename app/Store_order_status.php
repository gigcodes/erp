<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store_order_status extends Model
{
    protected $fillable = ['order_status_id','store_website_id','status'];

    public function order_status() {
        return $this->belongsTo('App\OrderStatus');
    }

    public function store_website() {
        return $this->belongsTo('App\StoreWebsite');
    }
}
