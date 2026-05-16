<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class MarEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chartings_mar';

    protected $fillable = [
        'patient_id', 'faculty_id',
        'scheduled_time', 'administered_at',
        'drug_name', 'dose', 'route', 'frequency',
        'status', 'given_by', 'indication', 'remarks',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'administered_at'=> 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
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
    public function scopeLatestFirst($q) { return $q->orderByDesc('scheduled_time')->orderByDesc('id'); }
    public function scopeStatus($q, $status) { return $q->where('status', $status); }

    /** Helpers */
    public static function statuses(): array
    {
        return ['Given','Held','Omitted','Refused','Late','Pending'];
    }
}