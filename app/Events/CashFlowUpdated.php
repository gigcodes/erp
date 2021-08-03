<?php

namespace App\Events;

use App\CashFlow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CashFlowUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $cashflow;

    public function __construct(CashFlow $cashflow)
    {
        $this->cashflow = $cashflow;
    }
}
