<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SkillMasteryChecklist extends Model
{
    use HasFactory;

    protected $table = 'skill_mastery_checklists';

    protected $fillable = [
        'faculty_id',
        'title',
        'slug',
        'category',
        'skill_area',
        'summary',
        'pre_procedure',
        'post_procedure',
        'safety_notes',
        'status',
    ];

    protected $casts = [
        'faculty_id' => 'integer',
    ];

    /* -------------------------------------------------
     | Relationships
     * ------------------------------------------------*/

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function steps()
    {
        // skill_mastery_steps.checklist_id → skill_mastery_checklists.id
        return $this->hasMany(SkillMasteryStep::class, 'checklist_id')
            ->orderBy('step_no');
    }

    public function equipment()
    {
        // skill_mastery_equipment.checklist_id → skill_mastery_checklists.id
        return $this->hasMany(SkillMasteryEquipment::class, 'checklist_id');
    }

    /**
     * Tags (many-to-many via pivot table).
     *
     * Pivot table (recommended structure):
     *  - name: skill_mastery_checklist_tag
     *  - columns:
     *      skill_mastery_checklist_id (FK → skill_mastery_checklists.id)
     *      skill_mastery_tag_id       (FK → skill_mastery_tags.id)
     */
    public function tags()
    {
        return $this->belongsToMany(
                SkillMasteryTag::class,
                'skill_mastery_checklist_tag',      // pivot table
                'skill_mastery_checklist_id',       // FK on pivot → this model
                'skill_mastery_tag_id'              // FK on pivot → tags table
            )
            ->using(SkillMasteryChecklistTag::class);
    }

    /* -------------------------------------------------
     | Scopes
     * ------------------------------------------------*/

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

    /* -------------------------------------------------
     | Helpers
     * ------------------------------------------------*/

    // Auto-generate slug if empty
    protected static function booted()
    {
        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . Str::random(6);
            }
        });
    }

    /**
     * Optional: nice accessor for displaying status.
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'draft');
    }
}
