<?php

namespace App\Events;

use App\Models\SupportTicket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTicketCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket->load('user:id,name');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.support'),
        ];
    }

    public function broadcastAs()
    {
        return 'NewTicketCreated';
    }

    public function broadcastWith()
    {
        return [
            'ticket' => [
                'id' => $this->ticket->id,
                'subject' => $this->ticket->subject,
                'priority' => $this->ticket->priority,
                'status' => $this->ticket->status,
                'user' => $this->ticket->user,
                'created_at' => $this->ticket->created_at->toISOString(),
            ]
        ];
    }
}
