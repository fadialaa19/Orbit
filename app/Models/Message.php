<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
    'messageable_id',
    'messageable_type',
    'sender_id',
    'sender_type',
    'message_text',
    'file_path',
];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function messageable()
    {
        return $this->morphTo();
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function scopeForMessageable($query, $messageable)
    {
        return $query->where('messageable_id', $messageable->id)
                     ->where('messageable_type', get_class($messageable))
                     ->orderBy('created_at');
    }
}

