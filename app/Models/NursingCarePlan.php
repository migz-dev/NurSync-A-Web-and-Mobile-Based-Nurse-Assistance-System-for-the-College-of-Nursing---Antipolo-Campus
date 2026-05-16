<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class NursingCarePlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chartings_ncp';

    protected $fillable = [
        'patient_id', 'faculty_id',
        'started_at', 'reviewed_at',
        'dx_primary', 'dx_related_to', 'dx_as_evidenced_by',
        'goals', 'interventions', 'outcomes_evaluation',
        'status',
    ];

    protected $casts = [
        'started_at'  => 'date',
        'reviewed_at' => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /** Relationships */
    public function patient() { return $this->belongsTo(Patient::class, 'patient_id'); }
    public function faculty() { return $this->belongsTo(Faculty::class, 'faculty_id'); }

    /** Scopes */
    public function scopeOwned($q, $facultyId = null) {
        $fid = $facultyId ?? Auth::guard('faculty')->id();
        return $fid ? $q->where('faculty_id', $fid) : $q;
    }
    public function scopeForPatient($q, $patientId) { return $q->where('patient_id', $patientId); }
    public function scopeLatestFirst($q) { return $q->orderByDesc('started_at')->orderByDesc('id'); }
    public function scopeStatus($q, $status) { return $q->where('status', $status); }

    /** Helpers */
    public static function statuses(): array
    {
        return ['Ongoing','Met','Partially met','Not met'];
    }
}