<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErpLeadSendingHistory extends Model
{
    protected $table = 'erp_lead_sending_histories';
    protected $fillable = [
        'product_id',
        'customer_id',
        'lead_id',
    ];
}
