<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'creator_id',
        'participants',
    ];

    protected $casts = [
        'participants' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class);
    }

    public function isParticipant(User $user)
    {
        return in_array($user->id, $this->participants ?? []);
    }
}

