<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'icon',
        'is_active',
        'pinned_message_id',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    public function mutes()
    {
        return $this->hasMany(CommunityMute::class);
    }

    public function pinnedMessage()
    {
        return $this->belongsTo(Message::class, 'pinned_message_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * The member's currently-active mute in this community, or null if they can post freely.
     */
    public function activeMuteFor(int $userId): ?CommunityMute
    {
        return $this->mutes()
            ->where('user_id', $userId)
            ->where('muted_until', '>', now())
            ->latest('muted_until')
            ->first();
    }
}
