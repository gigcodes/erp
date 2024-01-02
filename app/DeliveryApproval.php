<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class DeliveryApproval extends Model
{
    use Mediable;

    public function voucher()
    {
        return $this->hasOne(\App\Voucher::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'assigned_user_id');
    }

    public function order()
    {
        return $this->belongsTo(\App\Order::class);
    }

    public function status_changes()
    {
        return $this->hasMany(\App\StatusChange::class, 'model_id')->where('model_type', \App\DeliveryApproval::class)->latest();
    }

    public function private_view()
    {
        return $this->belongsTo(\App\PrivateView::class);
    }
}
