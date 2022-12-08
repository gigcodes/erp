<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CouponCodeRules extends Model
{
    protected $table = 'coupon_code_rules';

    public function store_labels()
    {
        return $this->hasMany(\App\WebsiteStoreViewValue::class, 'rule_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
