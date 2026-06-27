<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
'title_ar',
'title_en',
'main_image',
        'logo_image',
        'country',
        'university',
        'deadline',
        'description',
        'overview',
        'conditions',
        'documents',
        'features',
        'financial_value',
        'applicants_count',
        'recommended_tags',
        'coverage',
        'category',
        'tags',
        'status',
        'price',
        'application_url',
    ];

    protected $casts = [
        'coverage' => 'array',
        'tags' => 'array',
        'recommended_tags' => 'array',
        'deadline' => 'date',
        'price' => 'decimal:2',
    ];

    protected $appends = ['formatted_deadline'];

public function getFormattedDeadlineAttribute()
    {
        return $this->deadline->format('Y-m-d');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeMatchScore($query, $minScore = 0)
    {
        $user = auth()->user();
        if (!$user) return $query;

        $profile = $user->profile_data ?? [];
        
        // Simple match logic - extend as needed
        return $query->where(function($q) use ($profile) {
            $q->whereJsonContains('tags', $profile['category'] ?? '')
              ->orWhere('country', $profile['country'] ?? '');
        })->orderByRaw('
            CASE 
                WHEN JSON_CONTAINS(tags, ?,  "$") THEN 100
                WHEN country = ? THEN 80
                ELSE 50 
            END DESC', [$profile['category'] ?? '', $profile['country'] ?? '']);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'scholarship_favorites');
    }
}

