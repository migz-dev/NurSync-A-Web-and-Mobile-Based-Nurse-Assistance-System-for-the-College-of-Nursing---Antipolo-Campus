<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ResetPassword as CustomResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path', // student avatar
    ];

    /**
     * Hidden attributes.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /* ============================================================
     |  NOTIFICATIONS
     |============================================================ */

    /**
     * Send the custom NurSync password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }


    /* ============================================================
     |  View Helpers / Accessors
     |============================================================ */

    /**
     * Get initials (e.g., "MC" for Miguel Caluya).
     */
    public function getInitialsAttribute(): string
    {
        $name = trim((string) $this->name);

        if ($name !== '') {
            $parts = preg_split('/\s+/', $name);
            $a = mb_substr($parts[0] ?? '', 0, 1);
            $b = mb_substr($parts[1] ?? '', 0, 1);
            $init = ($a . $b) ?: mb_substr($name, 0, 2);
        } else {
            $init = mb_substr((string) $this->email, 0, 2);
        }

        return mb_strtoupper($init);
    }

    /**
     * Get the public URL of the profile avatar.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path
            ? Storage::url($this->avatar_path)
            : null;
    }

    /**
     * Display name for UI.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: 'Student';
    }

    /**
     * Display email for UI.
     */
    public function getDisplayEmailAttribute(): string
    {
        return (string) $this->email;
    }
}
