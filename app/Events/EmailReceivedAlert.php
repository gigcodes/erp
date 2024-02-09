<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Email;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class EmailReceivedAlert implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private $email;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function broadcastWith(): array
    {
        return [ 'email' => [
                'id'=> $this->email->id,
                'subject' => $this->email->subject,
                'message' => $this->email->message,
                'from' => $this->email->from,
                'to' => $this->email->to,
                'created_at'=>$this->email->created_at,
        ]

        ];
    }

    
    public function broadcastAs(): string
    {
        return 'email.received';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('emails');
        // return [
        //     new Channel('public-channel'),
        // ];
    }
}
