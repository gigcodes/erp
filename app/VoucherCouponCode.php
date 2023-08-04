<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CouponType;

class VoucherCouponCode extends Model
{
    public function user()
    {
        return $this->belongsTo(\App\User::class ,'user_id');
    }

    public function coupon_type()
    {
        return $this->belongsTo(\App\CouponType::class ,'coupon_type_id');
    }

    public function voucherCoupon()
    {
        return $this->belongsTo(VoucherCoupon::class, 'voucher_coupons_id', 'id');
    }
    

}
