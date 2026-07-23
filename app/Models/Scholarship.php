<?php

namespace App\Models;

use App\Traits\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory, ResolvesImageUrl;

    protected $fillable = [
'title_ar',
'title_en',
'main_image',
        'main_image_mobile',
        'logo_image',
        'country',
        'university',
        'deadline',
        'description',
        'overview',
        'conditions',
        'documents',
        'features',
        'application_process',
        'financial_value',
        'min_gpa',
        'applicants_count',
        'recommended_tags',
        'coverage',
        'category',
        'categories',
        'tags',
        'status',
        'students_notified_at',
        'price',
        'application_url',
        'apply_via_us_link',
    ];

    protected $casts = [
        'coverage' => 'array',
        'tags' => 'array',
        'recommended_tags' => 'array',
        'categories' => 'array',
        'deadline' => 'date',
        'price' => 'decimal:2',
        'min_gpa' => 'decimal:2',
        'students_notified_at' => 'datetime',
    ];

    protected $appends = ['formatted_deadline'];

    private const CATEGORY_LABELS = [
        'Bachelor' => 'بكالوريوس',
        'Master' => 'ماجستير',
        'PhD' => 'دكتوراه',
        'Short Course' => 'كورس قصير',
    ];

public function getFormattedDeadlineAttribute()
    {
        return $this->deadline->format('Y-m-d');
    }

    /**
     * قائمة المراحل الدراسية للمنحة - بترجع دايماً مصفوفة حتى لو المنحة
     * قديمة ولسا بس عندها category مفرد (توافق رجعي).
     */
    public function getCategoriesListAttribute(): array
    {
        return !empty($this->categories) ? $this->categories : array_filter([$this->category]);
    }

    /**
     * نص عربي جاهز للعرض يجمع كل المراحل الدراسية للمنحة، مثلاً "بكالوريوس، ماجستير".
     */
    public function getCategoryLabelAttribute(): string
    {
        return collect($this->categories_list)
            ->map(fn ($c) => self::CATEGORY_LABELS[$c] ?? $c)
            ->implode('، ');
    }

    public function getLogoImageAttribute($value)
    {
        return $this->resolveImageUrl($value);
    }

    public function getMainImageAttribute($value)
    {
        return $this->resolveImageUrl($value);
    }

    public function getMainImageMobileAttribute($value)
    {
        return $this->resolveImageUrl($value);
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

