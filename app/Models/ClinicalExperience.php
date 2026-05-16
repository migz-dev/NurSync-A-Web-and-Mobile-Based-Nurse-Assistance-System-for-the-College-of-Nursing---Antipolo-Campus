<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalExperience extends Model
{
    use HasFactory;

    protected $table = 'clinical_experiences';

    protected $fillable = [
        'faculty_id',
        'title',
        'slug',
        'ward',
        'summary',
        'story',
        'key_takeaways',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ==========================
     *  Relationships
     * ========================*/

    public function faculty()
    {
        // even though there is no DB FK, this is fine
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function attachments()
    {
        return $this->hasMany(ClinicalExperienceAttachment::class, 'clinical_experience_id');
    }

    public function primaryAttachment()
    {
        return $this->hasOne(ClinicalExperienceAttachment::class, 'clinical_experience_id')
            ->where('is_primary', 1)
            ->orderBy('sort_order');
    }

    /* ==========================
     *  Scopes
     * ========================*/

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeForFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    public function scopeWard($query, ?string $ward)
    {
        if (!empty($ward)) {
            $query->where('ward', $ward);
        }

        return $query;
    }
}
