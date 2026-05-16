<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class VitalSign extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chartings_vitals';

    protected $fillable = [
        'patient_id',
        'faculty_id',
        'taken_at',
        'temp_c',
        'heart_rate_bpm',
        'resp_rate_cpm',
        'bp_systolic',
        'bp_diastolic',
        'spo2_pct',
        'pain_score',
        'height_cm',
        'weight_kg',
        'bmi',           // NEW
        'bsa_m2',        // NEW
        'bmi_category',  // NEW
        'position',
        'remarks',
    ];

    protected $casts = [
        'taken_at'   => 'datetime',
        'temp_c'     => 'decimal:1',
        'heart_rate_bpm' => 'integer',
        'resp_rate_cpm'  => 'integer',
        'bp_systolic'    => 'integer',
        'bp_diastolic'   => 'integer',
        'spo2_pct'       => 'integer',
        'pain_score'     => 'integer',
        'height_cm'  => 'decimal:1',
        'weight_kg'  => 'decimal:2',
        'bmi'        => 'decimal:2',   // NEW
        'bsa_m2'     => 'decimal:2',   // NEW
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /** Relationships */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /** Scopes */
    public function scopeOwned($q, $facultyId = null)
    {
        $fid = $facultyId ?? Auth::guard('faculty')->id();
        return $fid ? $q->where('faculty_id', $fid) : $q;
    }

    public function scopeForPatient($q, $patientId)
    {
        return $q->where('patient_id', $patientId);
    }

    public function scopeLatestFirst($q)
    {
        return $q->orderByDesc('taken_at')->orderByDesc('id');
    }

    public function scopeBetween($q, $from, $to)
    {
        return $q->whereBetween('taken_at', [$from, $to]);
    }

    /** Accessors */
    public function getBpAttribute(): ?string
    {
        if (is_null($this->bp_systolic) || is_null($this->bp_diastolic)) {
            return null;
        }

        return $this->bp_systolic . '/' . $this->bp_diastolic;
    }
}
