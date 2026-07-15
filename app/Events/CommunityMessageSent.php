<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommunityMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public int $communityId;

    public function __construct(Message $message, int $communityId)
    {
        $this->message = $message->load('sender:id,name');
        $this->communityId = $communityId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('community.' . $this->communityId),
        ];
    }

    public function broadcastAs()
    {
        return 'CommunityMessageSent';
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'community_id' => $this->communityId,
                'sender' => $this->message->sender,
                'sender_type' => $this->message->sender_type,
                'message_text' => $this->message->message_text,
                'created_at' => $this->message->created_at->toISOString(),
            ]
        ];
    }
}
