<?php

namespace App\Hubstaff;

use Illuminate\Database\Eloquent\Model;

class HubstaffPaymentAccount extends Model
{
    protected $fillable = [
        'user_id',
        'accounted_at',
        'amount'
    ];

    public static function getConsidatedUserAmountToBePaid(){
        return self::groupBy('user_id')
            ->selectRaw('user_id, SUM(amount) as amount')
            ->get();
    }
}
