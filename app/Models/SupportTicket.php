<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'priority',
        'status',
        'ai_summary',
        'chat_history',
        'last_reply_at',
    ];

    protected $casts = [
        'chat_history' => 'array',
        'last_reply_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }
}

