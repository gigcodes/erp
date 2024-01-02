<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherCouponStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['voucher_coupons_id', 'old_value', 'new_value',  'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newValue()
    {
        return $this->belongsTo(VoucherCouponStatus::class, 'new_value');
    }

    public function oldValue()
    {
        return $this->belongsTo(VoucherCouponStatus::class, 'old_value');
    }
}
