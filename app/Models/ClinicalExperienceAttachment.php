<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalExperienceAttachment extends Model
{
    use HasFactory;

    protected $table = 'clinical_experience_attachments';

    protected $fillable = [
        'clinical_experience_id',
        'file_type',
        'storage_path',
        'original_name',
        'mime_type',
        'file_size',
        'caption',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'file_size'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ==========================
     *  Relationships
     * ========================*/

    public function experience()
    {
        return $this->belongsTo(ClinicalExperience::class, 'clinical_experience_id');
    }

    /* ==========================
     *  Helpers
     * ========================*/

    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->file_type === 'video';
    }

    /**
     * Full URL if you’re using storage disks (optional helper)
     */
    public function getUrlAttribute(): string
    {
        // adjust disk if you use a custom one
        return \Storage::disk('public')->url($this->storage_path);
    }
}
