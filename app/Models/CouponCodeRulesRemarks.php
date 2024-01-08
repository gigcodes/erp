<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCodeRulesRemarks extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_code_rules_id', 'remarks', 'added_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
