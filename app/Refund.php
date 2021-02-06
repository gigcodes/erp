<?php

namespace App;

use App\Events\RefundCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'order_id', 'type', 'chq_number', 'awb', 'date_of_refund', 'date_of_issue', 'details', 'dispatch_date', 'date_of_request', 'credited'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    protected $dispatchesEvents = [
        'created' => RefundCreated::class,
    ];

    public function cashFlows()
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }
}
