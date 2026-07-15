<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipMatchScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scholarship_id',
        'score',
        'summary',
        'matched_criteria',
        'gaps',
        'computed_at',
    ];

    protected $casts = [
        'matched_criteria' => 'array',
        'gaps' => 'array',
        'computed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    /**
     * Whether this cached score is still valid, i.e. neither the student's
     * profile nor the scholarship's conditions changed since it was computed.
     */
    public function isFresh(User $user, Scholarship $scholarship): bool
    {
        return $this->computed_at->gte($user->updated_at)
            && $this->computed_at->gte($scholarship->updated_at);
    }
}
