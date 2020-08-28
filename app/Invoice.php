<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_date'
    ];

    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
