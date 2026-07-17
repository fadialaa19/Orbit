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
        $this->message = $message->loadMissing(['sender:id,name,avatar', 'replyTo.sender:id,name']);
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
                'reply_to' => ($this->message->replyTo && !$this->message->replyTo->is_removed) ? [
                    'id' => $this->message->replyTo->id,
                    'sender_name' => $this->message->replyTo->sender?->name ?? 'مستخدم',
                    'message_text' => $this->message->replyTo->message_text,
                ] : null,
                'created_at' => $this->message->created_at->toISOString(),
            ]
        ];
    }
}
