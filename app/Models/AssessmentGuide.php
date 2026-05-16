<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentGuide extends Model
{
    use HasFactory;

    protected $table = 'assessment_guides';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'faculty_id',
        'title',
        'summary',
        'content_rubric',
        'content_documentation',
        'content_tips',
        'content_mistakes',
        'status',
        'tags_json',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'tags_json' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The Clinical Instructor who authored this guide.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Convenience accessor: return tags as a simple array.
     * If tags_json is null, always return [].
     */
    public function getTagsAttribute(): array
    {
        $raw = $this->tags_json;

        if (is_array($raw)) {
            return $raw;
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Mutator to store tags back into tags_json when you assign $model->tags.
     *
     * Example:
     *   $guide->tags = ['DAR', 'SOAP', 'evaluation'];
     */
    public function setTagsAttribute($value): void
    {
        if (is_null($value)) {
            $this->attributes['tags_json'] = null;
            return;
        }

        if (is_string($value)) {
            // allow passing a JSON string or comma separated raw input
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->attributes['tags_json'] = json_encode(array_values(array_unique($decoded)));
                return;
            }

            // treat as free text like "DAR SOAP safety"
            $normalized = str_replace([',', ';'], ' ', $value);
            $parts = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $this->attributes['tags_json'] = !empty($parts)
                ? json_encode(array_values(array_unique($parts)))
                : null;

            return;
        }

        if (is_array($value)) {
            $clean = [];
            foreach ($value as $item) {
                $item = trim((string) $item);
                if ($item !== '') {
                    $clean[] = $item;
                }
            }
            $clean = array_values(array_unique($clean));
            $this->attributes['tags_json'] = !empty($clean) ? json_encode($clean) : null;
            return;
        }

        // fallback
        $this->attributes['tags_json'] = null;
    }

    /**
     * Helper: is this guide published?
     */
    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Helper: is this guide archived?
     */
    public function getIsArchivedAttribute(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Scope for published guides (useful on student side).
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
