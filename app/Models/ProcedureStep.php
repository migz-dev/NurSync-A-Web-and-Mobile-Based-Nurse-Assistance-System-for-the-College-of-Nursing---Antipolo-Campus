<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureStep extends Model
{
    use HasFactory;

    // Table: procedure_steps
    protected $fillable = [
        'procedure_id',
        'step_no',
        'title',
        'body',
        'rationale',
        'caution',
        'duration_seconds',
        'video_url',
        'video_path',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    /* -----------------------------------------------------------------
     | Helpers
     |-----------------------------------------------------------------*/
    /** Check if this step has an uploaded video (local file). */
    public function hasVideoFile(): bool
    {
        return !empty($this->video_path);
    }

    /** Check if this step has an external video link (YouTube/Vimeo). */
    public function hasVideoUrl(): bool
    {
        return !empty($this->video_url);
    }
}