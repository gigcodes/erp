<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'magento_id', 'code', 'description', 'start', 'expiration', 'details', 'currency', 'discount_fixed', 'discount_percentage', 'minimum_order_amount', 'maximum_usage', 'usage_count','coupon_type','email','status','initial_amount','uuid'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $appends = ['discount'];

    public function getDiscountAttribute()
    {
        $discount = '';
        if ($this->currency) {
            $discount .= $this->currency . ' ';
        }
        if ($this->discount_fixed) {
            $discount .= $this->discount_fixed . ' fixed plus ';
        }
        if ($this->discount_percentage) {
            $discount .= $this->discount_percentage . '% discount';
        }
        return $discount;
    }

    public static function usageCount($couponIds)
    {
        $query =  DB::table('orders')
            ->select('coupon_id', DB::raw('count(*) as count'))
            ->groupBy('coupon_id');

        foreach ($couponIds as $couponId) {
            $query->orHaving('coupon_id', '=', $couponId);
        }

        return $query->get();
    }

    public function usage()
    {
        return $this->hasMany(
            'App\Order',
            'coupon_id'
        );
    }
}
