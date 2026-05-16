<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartingPatientEducation extends Model
{
    use HasFactory;

    protected $table = 'chartings_patient_education';

    protected $fillable = [
        'patient_id',
        'faculty_id',
        'topic',
        'method_used',
        'materials_used',
        'session_notes',
        'patient_understanding',
        'follow_up_required',
        'follow_up_notes',
        'is_archived',
    ];

    protected $casts = [
        'follow_up_required' => 'boolean',
        'is_archived'        => 'boolean',
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