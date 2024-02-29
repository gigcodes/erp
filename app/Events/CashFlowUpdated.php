<?php

namespace App\Events;

use App\CashFlow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CashFlowUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param publicCashFlow $cashflow
     *
     * @return void
     */
    public function __construct(public CashFlow $cashflow)
    {
    }
}
