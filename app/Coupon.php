<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'magento_id', 'code', 'description', 'start', 'expiration', 'details', 'currency', 'discount_fixed', 'discount_percentage', 'minimum_order_amount', 'maximum_usage', 'usage_count'
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

    public function getDiscountAttribute () {
        $discount = '';
        if($this->currency) {
            $discount .= $this->currency . ' ';
        }
        if($this->discount_fixed) {
            $discount .= $this->discount_fixed . ' fixed plus ';
        }
        if($this->discount_percentage) {
            $discount .= $this->discount_percentage . '% discount';
        }
        return $discount;
    }
}
