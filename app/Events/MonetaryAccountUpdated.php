<?php

namespace App\Events;

use App\MonetaryAccount;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MonetaryAccountUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $account;

    public function __construct(MonetaryAccount $account)
    {
        $this->account = $account;
    }
}
