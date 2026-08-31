<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginOtp extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'otp',
        'expires_at',
        'used_at',
        'verification_attempts',
        'resend_count',
        'locked_until',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'locked_until' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
