<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class PrivateView extends Model
{
    use Mediable;

    public function customer()
    {
        return $this->belongsTo(\App\Customer::class);
    }

    public function delivery_approval()
    {
        return $this->hasOne(\App\DeliveryApproval::class);
    }

    public function order_product()
    {
        return $this->belongsTo(\App\OrderProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(\App\Product::class, 'private_view_products', 'private_view_id', 'product_id');
    }

    public function status_changes()
    {
        return $this->hasMany(\App\StatusChange::class, 'model_id')->where('model_type', \App\PrivateView::class)->latest();
    }
}
