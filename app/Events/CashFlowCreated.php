<?php

namespace App\Events;

use App\CashFlow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CashFlowCreated
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
