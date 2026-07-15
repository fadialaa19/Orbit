<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'university',
        'content',
        'rating',
        'avatar',
        'is_active',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

