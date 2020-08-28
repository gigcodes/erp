<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model{

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'note',
        'amount',
        'paid',
        'payment_receipt_id',
        'date',
        'currency'
    ];

    public static function getConsidatedUserPayments(){
        return self::groupBy('user_id')
            ->selectRaw('user_id, SUM(amount) as paid')
            ->get();
    }
        
}