<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    protected $fillable = ['vendor_id', 'payment_date', 'paid_date', 'payable_amount', 'paid_amount', 'service_provided', 'module', 'work_hour', 'description', 'other', 'status', 'user_id', 'updated_by','currency'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
