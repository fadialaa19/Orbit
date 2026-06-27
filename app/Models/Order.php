<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scholarship_id',
        'amount',
        'status',
        'payment_method',
        'transaction_id',
        'bank_name',
        'transfer_from',
        'receipt_image',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForUserAndScholarship($query, int $userId, int $scholarshipId)
    {
        return $query->where('user_id', $userId)
                     ->where('scholarship_id', $scholarshipId);
    }
}

