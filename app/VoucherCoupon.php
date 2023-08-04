<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Platform;

class VoucherCoupon extends Model
{
    /**
     * Get the voucher coupon remark history associated with the voucher coupon table.
     */
    public function voucherCouponRemarks()
    {
        return $this->hasMany(VoucherCouponRemark::class, 'voucher_coupons_id', 'id');
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id', 'id');
    }
}
