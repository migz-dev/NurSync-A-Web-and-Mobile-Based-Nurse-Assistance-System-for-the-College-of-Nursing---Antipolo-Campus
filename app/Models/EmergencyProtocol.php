<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyProtocol extends Model
{
    protected $table = 'emergency_protocols';

    protected $fillable = [
        'faculty_id',
        'created_by_admin_id',   // <- NEW: who created it (admin)
        'title',
        'slug',
        'category',
        'ward',
        'severity',
        'summary',
        'description',
        'video_url',
        'pdf_path',
        'status',
    ];

    protected $casts = [
        'faculty_id'          => 'integer',
        'created_by_admin_id' => 'integer',
        'view_count'          => 'integer',
    ];

    /* ---------------- Relations ---------------- */

    /** Owning faculty (optional). */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /** Admin who created this protocol (optional). */
    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    /** Ordered steps for this protocol. */
    public function steps()
    {
        return $this->hasMany(EmergencyProtocolStep::class, 'protocol_id')
                    ->orderBy('step_no');
    }

    /** Tags mapped to this protocol. */
    public function tags()
    {
        return $this->belongsToMany(
            EmergencyProtocolTag::class,
            'emergency_protocol_tag_map',
            'protocol_id',
            'tag_id'
        );
    }

    /* ---------------- Query scopes (handy) ---------------- */

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function scopeNotArchived($q)
    {
        return $q->where('status', '!=', 'archived');
    }

    public function scopeAdminOwned($q)
    {
        return $q->whereNull('faculty_id');
    }

    public function scopeFacultyOwned($q, $facultyId)
    {
        return $q->where('faculty_id', $facultyId);
    }
}
