<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldPayment extends Model
{
    protected $fillable = array(
        'old_id', 'currency', 'payment_date', 'pending_amount', 'paid_amount', 'service_provided','module','description','other','paid_date','work_hour','payable_amount' 
    );
}
