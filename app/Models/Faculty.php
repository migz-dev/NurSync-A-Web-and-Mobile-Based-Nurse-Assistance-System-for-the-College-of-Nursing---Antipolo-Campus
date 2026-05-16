<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ResetPassword as CustomResetPasswordNotification;

class Faculty extends Authenticatable
{
    use Notifiable;

    protected $table = 'faculty';

    protected $fillable = [
        'full_name',
        'nurse_type',
        'email',
        'faculty_id',
        'password',
        'id_file_path',
        'status',
        'profile_image',
        // 'card_density', // uncomment if you want to mass-assign this later
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'name',
        'avatar_url',
        'initials',
        'display_name',
        'display_email',
    ];

    /* ============================================================
     |  RELATIONSHIPS
     |============================================================ */

    /**
     * All "My Clinical Experience" entries authored by this faculty.
     */
    public function clinicalExperiences()
    {
        return $this->hasMany(ClinicalExperience::class, 'faculty_id');
    }


    /* ============================================================
     |  NOTIFICATIONS
     |============================================================ */

    /**
     * Send the custom NurSync password reset email.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }


    /* ============================================================
     |  Accessors / Mutators
     |============================================================ */

    /**
     * Alias "name" → "full_name"
     */
    public function getNameAttribute(): string
    {
        return (string)($this->attributes['full_name'] ?? '');
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['full_name'] = (string)$value;
    }

    /**
     * Avatar URL resolver (local + remote support)
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $val = (string)($this->profile_image ?? '');
        if ($val === '') return null;

        // already a full URL
        if (preg_match('#^https?://#i', $val)) {
            return $val;
        }

        // stored path
        $path = ltrim($val, '/');
        if (!str_contains($path, '/')) {
            $path = 'avatars/' . $path;
        }

        return Storage::url($path);
    }

    /**
     * Initials ("John Doe" → "JD")
     */
    public function getInitialsAttribute(): string
    {
        $name = trim($this->name);
        if ($name === '') return 'F';

        $parts = preg_split('/\s+/', $name);
        $a = mb_substr($parts[0] ?? '', 0, 1);
        $b = mb_substr($parts[1] ?? '', 0, 1);
        $init = ($a . $b) ?: mb_substr($name, 0, 2);

        return mb_strtoupper($init);
    }

    /**
     * UI-safe display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: 'Faculty User';
    }

    /**
     * UI-safe email
     */
    public function getDisplayEmailAttribute(): string
    {
        return (string)($this->email ?? 'faculty@sys.test.ph');
    }

    /**
     * Human-readable nurse type
     */
    public function getNurseTypeLabelAttribute(): string
    {
        return $this->nurse_type ?: 'Not Specified';
    }
}
