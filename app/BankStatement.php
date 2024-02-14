<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    protected $table = 'bank_statement';

    protected $fillable = [
        'bank_statement_file_id',
        'transaction_date',
        'transaction_reference_no',
        'debit_amount',
        'credit_amount',
        'balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}