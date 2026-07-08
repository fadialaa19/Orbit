<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Room;
use App\Models\ScholarshipApplication;


class User extends Authenticatable
{
    /**
     * Required by Laravel's verification logic.
     */
    public function getEmailForVerification(): string
    {
        return (string) $this->email;
    }

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
protected $fillable = [
        'name',
        'name_en',
        'gender',
        'email',
        'password',
        'role',
        'xp',          // 👈 أضف هذا السطر
        'referred_by',
        'permissions',
        'preferences',
        'status',
        'phone',
        'birthdate',
        'country',
        'city',
        'degree',
        'field_of_study',
        'university',
        'graduation_year',
        'bio',
        'avatar',
        'languages',
        'achievements',
        'documents',
        'profile_completion',
        'xp',
        'level',
        // Education - High School (Required)
        'high_school_name',
        'high_school_country',
        'high_school_gpa',
        'high_school_certificate',
        'high_school_year',
        'high_school_branch',
        // Education - Diploma (Optional)
        'diploma_institute',
        'diploma_country',
        'diploma_year',
        'diploma_degree',
        'diploma_gpa',
        // Education - Bachelor's (Optional)
        'bachelor_university',
        'bachelor_country',
        'bachelor_year',
        'bachelor_degree',
        'bachelor_gpa',
        // Education - Master's (Optional)
        'master_university',
        'master_country',
        'master_year',
        'master_degree',
        'master_gpa',
        'master_certificate',
        // Personal IDs
        'national_id',
        'passport_number',
        'passport_expiry',
        'passport_country',
        // Documents
        'required_documents',
        'optional_documents',
    ];

    protected $casts = [
        'permissions' => 'array', // هاد السطر سيعامل الحقل كمصفوفة PHP طبيعية
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthdate' => 'date',
            'passport_expiry' => 'date',
            'languages' => 'array',
            'achievements' => 'array',
            'documents' => 'array',
            'required_documents' => 'array',
            'optional_documents' => 'array',
            'education_summary' => 'array',
            'preferences' => 'array',
        ];
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['super_admin', 'scholarship_admin', 'support_admin']);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'scholarship_admin', 'support_admin'], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function favoriteScholarships()
    {
        return $this->belongsToMany(Scholarship::class, 'scholarship_favorites');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function scholarshipApplications()
    {
        return $this->hasMany(ScholarshipApplication::class);
    }


    /**
     * Calculate profile completion percentage
     */
    public function calculateProfileCompletion(): int
    {
        $basicFields = [
            'name', 'phone', 'birthdate', 'country', 'city',
            'national_id', 'passport_number', 'avatar'
        ];
        
        $requiredEducation = [
            'high_school_name', 'high_school_country', 'high_school_year'
        ];

        $filled = 0;
        $total = count($basicFields) + count($requiredEducation);

        // Basic fields
        foreach ($basicFields as $field) {
            if (!empty($this->$field)) $filled++;
        }

        // Required education
        foreach ($requiredEducation as $field) {
            if (!empty($this->$field)) $filled++;
        }

        // Required documents (new JSON fields)
        $requiredDocsFilled = 0;
        $reqDocs = $this->required_documents ?? [];
        $requiredDocKeys = ['passport', 'national_id', 'high_school_cert', 'birth_cert', 'cv'];
        foreach ($requiredDocKeys as $key) {
            if (!empty($reqDocs[$key] ?? null)) $requiredDocsFilled++;
        }
        $filled += $requiredDocsFilled;
        $total += count($requiredDocKeys);

        // Bonus for optional docs (max 20%)
        $optDocsFilled = 0;
        $optDocs = $this->optional_documents ?? [];
        $optionalDocKeys = ['language_cert', 'courses_cert', 'recommendation', 'intent_letter'];
        foreach ($optionalDocKeys as $key) {
            if (!empty($optDocs[$key] ?? null)) $optDocsFilled++;
        }
        $filled += min($optDocsFilled * 0.5, 4); // Half points, max 4
        $total += 8; // Max bonus

        return min(100, round(($filled / $total) * 100));
    }

    /**
     * Get education summary for view consistency
     */
    public function getEducationSummaryAttribute()
    {
        return [
            'high_school' => !empty($this->high_school_name),
            'diploma' => !empty($this->diploma_institute),
            'bachelor' => !empty($this->bachelor_university),
            'master' => !empty($this->master_university),
        ];
    }

    /**
     * Get the initials of the user's name
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= mb_substr($word, 0, 1);
        }
        return mb_strtoupper(mb_substr($initials, 0, 2));
    }
}
