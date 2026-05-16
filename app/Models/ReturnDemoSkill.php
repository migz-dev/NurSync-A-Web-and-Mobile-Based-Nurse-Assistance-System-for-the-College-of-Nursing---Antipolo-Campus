<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;

class ReturnDemoSkill extends Model
{
    use HasFactory;

    protected $table = 'return_demo_skills';

    protected $fillable = [
        'slug','title','description','level','status','is_archived',
        'hazards_text','ppe_json','tags_json','clinical_wards',
        'video_url','video_path','pdf_path','procedure_id',
        'created_by','created_by_admin','updated_by','updated_by_admin',
        'archived_by_admin','archived_at','published_at',
    ];

    protected $casts = [
        'is_archived'  => 'boolean',
        // NOTE: Do NOT cast ppe_json/tags_json to array here because we normalize via accessors below
        'published_at' => 'datetime',
        'archived_at'  => 'datetime',
    ];

    /* ------------------------------ Route key ------------------------------ */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ---------------------------- Relationships --------------------------- */
    public function steps()
    {
        return $this->hasMany(ReturnDemoStep::class, 'return_demo_id')
                    ->orderBy('step_no');
    }

    public function attachments()
    {
        return $this->hasMany(ReturnDemoAttachment::class, 'return_demo_id');
    }

    /* ------------------------- Normalized JSON fields ---------------------- */
    /**
     * Normalize possibly double-encoded JSON into a flat array of strings.
     */
    protected function normalizeJsonArray(mixed $value): array
    {
        // Decode once if string
        $v = is_string($value) ? json_decode($value, true) : $value;

        // If still string after first decode, try a second time (double-encoded case)
        if (is_string($v)) {
            $v = json_decode($v, true);
        }

        // Flatten to a simple array of non-empty strings
        return array_values(
            array_filter(
                Arr::flatten((array) $v),
                fn ($x) => $x !== null && $x !== ''
            )
        );
    }

    /**
     * tags_json accessor/mutator (handles double-encoded JSON)
     */
    protected function tagsJson(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->normalizeJsonArray($value),
            set: fn ($value) => json_encode(
                $this->normalizeJsonArray($value),
                JSON_UNESCAPED_UNICODE
            ),
        );
    }

    /**
     * ppe_json accessor/mutator (handles double-encoded JSON)
     */
    protected function ppeJson(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->normalizeJsonArray($value),
            set: fn ($value) => json_encode(
                $this->normalizeJsonArray($value),
                JSON_UNESCAPED_UNICODE
            ),
        );
    }

    /* -------------------------------- Scopes ------------------------------- */
    public function scopePublished($q)
    {
        return $q->where('status', 'published')->where('is_archived', 0);
    }

    /**
     * Visible to students:
     * - In production (design_mode=false): published only
     * - In design/dev (design_mode=true): include drafts
     */
    public function scopeVisible($q)
    {
        $includeDrafts = (bool) config('app.design_mode', true);

        return $q->where('is_archived', 0)
                 ->when(!$includeDrafts, fn ($qq) => $qq->where('status', 'published'))
                 ->when($includeDrafts,   fn ($qq) => $qq->whereIn('status', ['published','draft','']));
    }

    public function scopeSearch($q, ?string $needle)
    {
        $needle = trim((string) $needle);
        if ($needle === '') return $q;

        return $q->where(function ($qq) use ($needle) {
            $qq->where('title', 'like', "%{$needle}%")
               ->orWhere('description', 'like', "%{$needle}%")
               ->orWhere('hazards_text', 'like', "%{$needle}%")
               ->orWhere('tags_json', 'like', "%{$needle}%");
        });
    }

    public function scopeWard($q, ?string $ward)
    {
        $ward = trim((string) $ward);
        if ($ward === '' || strtolower($ward) === 'all') return $q;

        return $q->where('clinical_wards', $ward);
    }
}
