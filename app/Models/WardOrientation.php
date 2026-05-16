<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WardOrientation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ward_orientations';

    /**
     * Mass-assignable fields.
     */
    protected $fillable = [
        'ward_code',
        'title',
        'slug',
        'summary',
        'culture_text',
        'routines_text',
        'patient_cases_text',
        'workload_text',
        'emergencies_text',
        'layout_locations_text',
        'tips_text',
        'status',
        'estimated_watch_minutes',
        'created_by_faculty_id',
        'published_at',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'published_at'             => 'datetime',
        'estimated_watch_minutes'  => 'integer',
        'created_at'               => 'datetime',
        'updated_at'               => 'datetime',
        'deleted_at'               => 'datetime',
    ];

    /**
     * Status constants.
     */
    public const STATUS_DRAFT     = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED  = 'archived';

    /**
     * Ward codes (keep in sync with DB enum).
     */
    public const WARD_CHN      = 'CHN';
    public const WARD_OB       = 'OB';
    public const WARD_DR       = 'DR';
    public const WARD_PEDIA    = 'PEDIA';
    public const WARD_CDN      = 'CDN';
    public const WARD_ONCO     = 'ONCO';
    public const WARD_MS       = 'MS';
    public const WARD_OR       = 'OR';
    public const WARD_GERIA    = 'GERIA';
    public const WARD_ORTHO    = 'ORTHO';
    public const WARD_PSYCH    = 'PSYCH';
    public const WARD_ICU      = 'ICU';
    public const WARD_ER       = 'ER';
    public const WARD_DN       = 'DN';
    public const WARD_MEDICINE = 'MEDICINE';
    public const WARD_SURGERY  = 'SURGERY';

    /**
     * Author (CI) relationship.
     */
    public function author()
    {
        return $this->belongsTo(Faculty::class, 'created_by_faculty_id');
    }

    /**
     * Scope: only published.
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope: only draft.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope: not archived.
     */
    public function scopeNotArchived($query)
    {
        return $query->where('status', '!=', self::STATUS_ARCHIVED);
    }

    /**
     * Scope: for a specific ward.
     */
    public function scopeForWard($query, string $wardCode)
    {
        return $query->where('ward_code', $wardCode);
    }

    /**
     * Scope: owned by a specific faculty (CI).
     */
    public function scopeOwnedBy($query, int $facultyId)
    {
        return $query->where('created_by_faculty_id', $facultyId);
    }

    /**
     * Accessor: is published?
     */
    public function getIsPublishedAttribute(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Accessor: nice ward label (for chips in UI).
     */
    public function getWardLabelAttribute(): string
    {
        $map = [
            self::WARD_CHN      => 'Community Health Nursing',
            self::WARD_OB       => 'Obstetrics',
            self::WARD_DR       => 'Delivery Room',
            self::WARD_PEDIA    => 'Pediatrics',
            self::WARD_CDN      => 'CDN',
            self::WARD_ONCO     => 'Oncology',
            self::WARD_MS       => 'Medical-Surgical',
            self::WARD_OR       => 'Operating Room',
            self::WARD_GERIA    => 'Geriatric',
            self::WARD_ORTHO    => 'Orthopedics',
            self::WARD_PSYCH    => 'Psychiatric',
            self::WARD_ICU      => 'ICU',
            self::WARD_ER       => 'Emergency Room',
            self::WARD_DN       => 'Dialysis / DN',
            self::WARD_MEDICINE => 'Medicine Ward',
            self::WARD_SURGERY  => 'Surgery Ward',
        ];

        return $map[$this->ward_code] ?? $this->ward_code;
    }
}
