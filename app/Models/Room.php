<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Message;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'name', 
        'type', 
        'status', 
        'avatar',
        'description'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable');
    }
}
