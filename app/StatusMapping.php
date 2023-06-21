<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class StatusMapping extends Model
{
    public function orderStatus()
    {
        return $this->belongsTo(\App\OrderStatus::class);
    }

    public function purchaseStatus()
    {
        return $this->belongsTo(\App\PurchaseStatus::class);
    }

    public function statusMappingHistories()
    {
        return $this->hasMany(\App\StatusMappingHistory::class)->latest();
    }
}
