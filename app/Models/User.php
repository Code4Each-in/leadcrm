<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\UserCreatedNotification;
use Illuminate\Notifications\Notifiable;
use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Chatify\Traits\InteractsWithChatify;


class User extends Authenticatable
{
    use SoftDeletes;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, InteractsWithChatify;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'address',
        'profile',
        'city',
        'state',
        'zip',
        'agency_id',
        'date_of_birth',
        'product_id',
        'otp_enabled',
        'is_mobile',
        'is_tablet',
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
            'otp_enabled' => 'boolean',
            'is_mobile' => 'boolean',
            'is_tablet' => 'boolean',
            'product_id' => 'array',

        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isSuperAdmin(): bool
    {
        return (int) $this->role_id === config('roles.super_admin');
    }

    public function isAdmin(): bool
    {
        return (int) $this->role_id === config('roles.admin');
    }

    /**
     * Super Admin and Admin are treated as one "elevated" tier
     * throughout the app (full lead/product visibility, user
     * management, etc).
     */
    public function isAdminOrAbove(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function isMis(): bool
    {
        return (int) $this->role_id === config('roles.mis');
    }

    public function isAe(): bool
    {
        return (int) $this->role_id === config('roles.ae');
    }

    public function isQa(): bool
    {
        return (int) $this->role_id === config('roles.qa');
    }

    public function isManager(): bool
    {
        return (int) $this->role_id === config('roles.manager');
    }
    public function agency()
    {
        return $this->belongsTo(Agency::class);

    }
    /**
     * Leads currently assigned to this user (leads.assigned_to).
     */
    public function leads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    /**
     * Whether this user has been given access to $productId
     * (users.product_id is a JSON array, stored as strings by the
     * user form - compared as strings so ints and strings both match).
     */
    public function hasProductAccess(int|string|null $productId): bool
    {
        if ($productId === null) {
            return false;
        }

        return in_array((string) $productId, array_map('strval', $this->product_id ?? []), true);
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }
    public function LoginLog()
    {
        return $this->hasMany(LoginLog::class);
    }

}
