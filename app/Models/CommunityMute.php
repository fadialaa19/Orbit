<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityMute extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'user_id',
        'muted_until',
        'muted_by',
        'reason',
    ];

    protected $casts = [
        'muted_until' => 'datetime',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mutedBy()
    {
        return $this->belongsTo(User::class, 'muted_by');
    }
}
