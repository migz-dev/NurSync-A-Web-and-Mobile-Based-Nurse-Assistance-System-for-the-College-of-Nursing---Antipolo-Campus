<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnDemoStep extends Model
{
    use HasFactory;

    protected $table = 'return_demo_steps';

    protected $fillable = [
        'return_demo_id','step_no','title','body','rationale','caution',
        'is_archived','archived_at','archived_by_admin',
        'duration_seconds','video_url','video_path',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function skill()
    {
        return $this->belongsTo(ReturnDemoSkill::class, 'return_demo_id');
    }
}
