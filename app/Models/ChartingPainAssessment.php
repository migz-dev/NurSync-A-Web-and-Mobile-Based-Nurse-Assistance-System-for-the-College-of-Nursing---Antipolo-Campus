<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartingPainAssessment extends Model
{
    use HasFactory;

    protected $table = 'chartings_pain_assessment';

    protected $fillable = [
        'patient_id',
        'faculty_id',
        'pain_score',
        'location',
        'characteristics',
        'aggravating_factors',
        'relieving_factors',
        'interventions',
        'response_to_intervention',
        'assessment_time',
        'is_archived',
    ];

    protected $casts = [
        'pain_score'      => 'integer',
        'assessment_time' => 'datetime',
        'is_archived'     => 'boolean',
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