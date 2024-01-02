<?php

namespace App\Events;

use App\LegalCase;
use App\CaseReceivable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CaseReceivableCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $case;

    public $receivable;

    public $status;

    public function __construct(LegalCase $case, CaseReceivable $case_receivable, $status)
    {
        $this->case = $case;
        $this->receivable = $case_receivable;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
