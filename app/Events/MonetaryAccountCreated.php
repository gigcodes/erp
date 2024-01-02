<?php

namespace App\Events;

use App\MonetaryAccount;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class MonetaryAccountCreated
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
