<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender:id,name');
    }

    public function broadcastOn(): array
{
    $message = $this->message;
    $messageable = $message->messageable;

    return [
        // 1. قناة الطالب (صاحب التذكرة)
        new PrivateChannel('App.Models.User.' . $messageable->user_id),
        
        // 2. قناة الدعم الفني للأدمن
        new PrivateChannel('admin.support'),
    ];
}

    public function broadcastAs()
    {
        return 'ChatMessageSent';
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'messageable_id' => $this->message->messageable_id,
                'messageable_type' => $this->message->messageable_type,
                'sender' => $this->message->sender,
                'message_text' => $this->message->message_text,
                'sender_type' => $this->message->sender_type,
                'created_at' => $this->message->created_at->toISOString(),
            ]
        ];
    }
}

