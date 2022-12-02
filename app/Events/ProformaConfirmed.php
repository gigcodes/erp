<?php

namespace App\Events;

use App\Purchase;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProformaConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $purchase;

    public $purchase_amount;

    public function __construct(Purchase $purchase, $purchase_amount)
    {
        $this->purchase = $purchase;
        $this->purchase_amount = $purchase_amount;
    }
}
