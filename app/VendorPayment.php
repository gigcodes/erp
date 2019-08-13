<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayment extends Model
{
    use SoftDeletes;
    protected $fillable = ['vendor_id', 'payment_date', 'paid_date', 'payable_amount', 'paid_amount', 'service_provided', 'module', 'work_hour', 'description', 'other', 'status', 'user_id', 'updated_by', 'currency'];
    protected $dates = ['deleted_at'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
