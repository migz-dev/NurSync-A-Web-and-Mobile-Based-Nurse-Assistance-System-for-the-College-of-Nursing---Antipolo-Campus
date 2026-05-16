<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class Procedure extends Model
{
    use HasFactory;

    protected $table = 'procedures';

    public const WARD_OPTIONS = [
        'Community Health (CHN)',
        'OB Ward',
        'Delivery Room (DR)',
        'Nursery',
        'Pediatrics (PEDIA)',
        'Medical-Surgical (MS)',
        'ICU',
        'Oncology',
        'Isolation Unit',
        'Endocrine Unit',
        'Neurology Unit',
        'Psychiatric (PSYCH)',
        'Emergency Room (ER)',
        'Operating Room (OR)',
        'Trauma Unit',
        'Disaster Response / Community Field',
    ];

    protected $fillable = [
        'slug',
        'title',
        'description',
        'clinical_wards',
        'status',                  // draft | published
        'hazards_text',
        'ppe_json',
        'tags_json',
        'video_url',
        'video_path',
        'pdf_path',
        'created_by',
        'created_by_admin',
        'updated_by',
        'updated_by_admin',
        'published_at',
        'is_archived',
        'archived_at',
        'archived_by_admin',
    ];

    protected $casts = [
        'ppe_json'     => 'array',
        'tags_json'    => 'array',
        'published_at' => 'datetime',
        'archived_at'  => 'datetime',
        'is_archived'  => 'boolean',
    ];

    protected $appends = [
        'created_by_name',
        'updated_by_name',
        'is_published',
    ];

    protected $with = [
        'adminCreator',
        'author',
        'adminCreatorLegacy',
        'adminEditor',
        'editorFaculty',
        'adminEditorLegacy',
    ];

    /* -----------------------------------------------------------------
     | Relationships
     |-----------------------------------------------------------------*/
    public function steps()
    {
        return $this->hasMany(ProcedureStep::class)->orderBy('step_no');
    }

    public function attachments()
    {
        return $this->hasMany(ProcedureAttachment::class);
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\Faculty::class, 'created_by');
    }

    public function adminCreator()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by_admin');
    }

    public function adminCreatorLegacy()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }

    public function editorFaculty()
    {
        return $this->belongsTo(\App\Models\Faculty::class, 'updated_by');
    }

    public function adminEditor()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'updated_by_admin');
    }

    public function adminEditorLegacy()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'updated_by');
    }

    public function adminArchiver()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'archived_by_admin');
    }

    public function scenarios()
    {
        return $this->hasMany(ProcedureScenario::class);
    }

    /* -----------------------------------------------------------------
     | Accessors / Mutators
     |-----------------------------------------------------------------*/
    public function getCreatedByNameAttribute(): string
    {
        return $this->adminCreator?->name
            ?? $this->adminCreatorLegacy?->name
            ?? $this->author?->name
            ?? '—';
    }

    public function getUpdatedByNameAttribute(): string
    {
        $name = $this->adminEditor?->name
            ?? $this->adminEditorLegacy?->name
            ?? $this->editorFaculty?->name;

        return $name
            ?? ($this->adminCreator?->name
                ?? $this->adminCreatorLegacy?->name
                ?? $this->author?->name
                ?? '—');
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Robust array normalizer for PPE JSON (handles arrays, JSON strings, CSV).
     */
    public function getPpeJsonAttribute($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }
        if (is_string($value)) {
            $s = trim($value);
            if ($s === '') return [];
            // JSON-like array (allow single quotes)
            if (preg_match('/^\[.*\]$/', $s)) {
                $json = preg_replace("/'([^']*)'/", '"$1"', $s);
                $arr  = json_decode($json, true);
                if (is_array($arr)) {
                    return array_values(array_filter(array_map('trim', $arr)));
                }
            }
            // CSV fallback
            $arr = preg_split('/\s*,\s*|\s*;\s*/', $s, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            return array_values(array_filter(array_map('trim', $arr)));
        }
        return [];
    }

    /**
     * TAGS — accessor: always return a clean array even if DB has legacy strings.
     */
    public function getTagsJsonAttribute($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        if (is_string($value)) {
            $s = trim($value);
            if ($s === '') return [];

            // Looks like ["a","b"] or ['a','b']
            if (preg_match('/^\[.*\]$/', $s)) {
                $json = preg_replace("/'([^']*)'/", '"$1"', $s);
                $decoded = json_decode($json, true);
                if (is_array($decoded)) {
                    return array_values(array_filter(array_map('trim', $decoded)));
                }
            }

            // Fallback: CSV like "a,b,c" or "a; b; c"
            $parts = preg_split('/\s*,\s*|\s*;\s*/', $s, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            return array_values(array_filter(array_map('trim', $parts)));
        }

        return [];
    }

    /**
     * TAGS — mutator: accept array/CSV/JSON-like string and store as proper JSON.
     */
    public function setTagsJsonAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['tags_json'] = json_encode(
                array_values(array_filter(array_map('trim', $value)))
            );
            return;
        }

        if (is_string($value)) {
            $s = trim($value);
            $arr = [];
            if ($s !== '') {
                if (preg_match('/^\[.*\]$/', $s)) {
                    $json = preg_replace("/'([^']*)'/", '"$1"', $s);
                    $arr  = json_decode($json, true) ?? [];
                } else {
                    $arr = preg_split('/\s*,\s*|\s*;\s*/', $s, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                }
            }
            $this->attributes['tags_json'] = json_encode(
                array_values(array_filter(array_map('trim', $arr)))
            );
            return;
        }

        $this->attributes['tags_json'] = json_encode([]);
    }

    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = is_string($value) ? trim($value) : $value;
    }

    public function setClinicalWardsAttribute($value): void
    {
        $this->attributes['clinical_wards'] = is_string($value) ? trim($value) : $value;
    }

    /* -----------------------------------------------------------------
     | Scopes
     |-----------------------------------------------------------------*/
    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function scopeDraft($q)
    {
        return $q->where('status', 'draft');
    }

    public function scopeSearch($q, ?string $term)
    {
        if (!$term) return $q;
        $term = trim($term);

        return $q->where(function ($qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
               ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeStatus($q, ?string $status)
    {
        if (!$status) return $q;
        return $q->where('status', $status);
    }

    public function scopeWard($q, ?string $ward)
    {
        if (!$ward) return $q;
        return $q->where('clinical_wards', $ward);
    }

    public function scopeActive($q)
    {
        return $q->where('is_archived', 0);
    }

    public function scopeArchived($q)
    {
        return $q->where('is_archived', 1);
    }

    /* -----------------------------------------------------------------
     | Route binding by slug
     |-----------------------------------------------------------------*/
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /* -----------------------------------------------------------------
     | Legacy helpers
     |-----------------------------------------------------------------*/
    public function publish(): void
    {
        $this->forceFill([
            'status'       => 'published',
            'published_at' => now(),
        ])->save();
    }

    public function unpublish(): void
    {
        $this->forceFill([
            'status'       => 'draft',
            'published_at' => null,
        ])->save();
    }

    public function ensureSlug(): void
    {
        if ($this->slug) return;

        $base = Str::slug((string) $this->title) ?: Str::random(8);
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->whereKeyNot($this->getKey())->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        $this->slug = $slug;
    }

    public function markArchived(int $adminId = null): void
    {
        $this->forceFill([
            'is_archived'       => 1,
            'archived_at'       => now(),
            'archived_by_admin' => $adminId,
        ])->save();
    }

    public function markRestored(): void
    {
        $this->forceFill([
            'is_archived'       => 0,
            'archived_at'       => null,
            'archived_by_admin' => null,
        ])->save();
    }

    /* =================================================================
     |                 RETURN DEMO – NON-BREAKING HELPERS
     |=================================================================*/

    public static function rdAvailable(): bool
    {
        return Schema::hasTable('return_demo_skills')
            && Schema::hasTable('return_demo_steps');
    }

    public static function rdQuery(array $filters = [])
    {
        $q = DB::table('return_demo_skills');

        if (!empty($filters['status']) && Schema::hasColumn('return_demo_skills', 'status')) {
            $q->where('status', $filters['status']);
        }

        if (!empty($filters['q'])) {
            $term = trim($filters['q']);
            $q->where(function ($w) use ($term) {
                $w->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");

                if (Schema::hasColumn('return_demo_skills', 'tags_json')) {
                    try {
                        $w->orWhereJsonContains('tags_json', $term);
                    } catch (\Throwable $e) {
                        $w->orWhere('tags_json', 'like', "%{$term}%");
                    }
                }
            });
        }

        if (!empty($filters['level']) && strtolower($filters['level']) !== 'all') {
            $q->whereRaw('LOWER(level) = ?', [strtolower($filters['level'])]);
        }

        $order = $filters['order'] ?? 'title';
        $q->orderBy($order);

        return $q;
    }

    public static function rdList(array $filters = [], int|string|null $perPage = 36)
    {
        $query = static::rdQuery($filters);
        if ($perPage === null || $perPage === 'all') {
            return $query->get();
        }
        /** @var LengthAwarePaginator $p */
        $p = $query->paginate(max(1, (int) $perPage));
        return $p;
    }

    public static function rdFindBySlug(string $slug): ?object
    {
        $q = DB::table('return_demo_skills')->where('slug', $slug);
        if (Schema::hasColumn('return_demo_skills', 'status')) {
            $q->where('status', 'published');
        }
        return $q->first();
    }

    public static function rdSteps(int $skillId): Collection
    {
        return DB::table('return_demo_steps')
            ->where('return_demo_id', $skillId)
            ->orderBy('step_no')
            ->get();
    }

    public static function rdAttachments(int $skillId): Collection
    {
        if (!Schema::hasTable('return_demo_attachments')) {
            return collect();
        }
        return DB::table('return_demo_attachments')
            ->where('return_demo_id', $skillId)
            ->orderBy('id')
            ->get();
    }

    public static function rdToProcedureLike(object $skill, ?Collection $steps = null): array
    {
        $steps = $steps ?? static::rdSteps($skill->id);

        $video = $skill->video_url
            ?? ((isset($skill->video_path) && $skill->video_path) ? Storage::url($skill->video_path) : null);

        $pdf = (isset($skill->pdf_path) && $skill->pdf_path)
            ? Storage::url($skill->pdf_path)
            : null;

        return [
            'id'              => $skill->id,
            'slug'            => $skill->slug,
            'title'           => $skill->title,
            'description'     => $skill->description ?? null,
            'level'           => $skill->level ?? null,
            'clinical_wards'  => $skill->clinical_wards ?? null,
            'video_url'       => $skill->video_url ?? null,
            'video_path'      => $skill->video_path ?? null,
            'pdf_path'        => $skill->pdf_path ?? null,
            'video'           => $video,
            'pdf'             => $pdf,
            'steps' => $steps->map(function ($s) {
                return [
                    'step_no'          => $s->step_no,
                    'title'            => $s->title,
                    'body'             => $s->body,
                    'rationale'        => $s->rationale,
                    'caution'          => $s->caution,
                    'duration_seconds' => $s->duration_seconds,
                ];
            })->values(),
        ];
    }
}
