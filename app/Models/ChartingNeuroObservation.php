<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartingNeuroObservation extends Model
{
    use HasFactory;

    protected $table = 'chartings_neuro_observation';

    protected $fillable = [
        'patient_id',
        'faculty_id',
        'gcs_eye',
        'gcs_verbal',
        'gcs_motor',
        'pupil_left_size',
        'pupil_left_reaction',
        'pupil_right_size',
        'pupil_right_reaction',
        'motor_strength',
        'sensation',
        'orientation_status',
        'notes',
        'assessment_time',
        'is_archived',
    ];

    protected $casts = [
        'gcs_eye'          => 'integer',
        'gcs_verbal'       => 'integer',
        'gcs_motor'        => 'integer',
        'assessment_time'  => 'datetime',
        'is_archived'      => 'boolean',
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