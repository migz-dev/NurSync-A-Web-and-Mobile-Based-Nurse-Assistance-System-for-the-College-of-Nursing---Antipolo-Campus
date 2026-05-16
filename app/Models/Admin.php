<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $table = 'admins';

    protected $fillable = [
        'full_name',
        'email',
        'password_hash',
        'is_active',
        'profile_image',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token', // optional, safe to hide
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Use password_hash column for Laravel Auth
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Automatically include a default avatar if none is set.
     */
    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image
            ? asset('storage/' . $this->profile_image)
            : asset('assets/img/default-avatar.png');
    }

    /**
     * Quick scope for active admins.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
