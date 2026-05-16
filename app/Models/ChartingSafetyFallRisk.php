<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartingSafetyFallRisk extends Model
{
    use HasFactory;

    protected $table = 'chartings_safety_fallrisk';

    protected $fillable = [
        'patient_id',
        'faculty_id',
        'fall_risk_score',
        'environment_check',
        'restraints_in_use',
        'restraint_notes',
        'safety_measures',
        'assessment_time',
        'is_archived',
    ];

    protected $casts = [
        'fall_risk_score'   => 'integer',
        'restraints_in_use' => 'boolean',
        'assessment_time'   => 'datetime',
        'is_archived'       => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}