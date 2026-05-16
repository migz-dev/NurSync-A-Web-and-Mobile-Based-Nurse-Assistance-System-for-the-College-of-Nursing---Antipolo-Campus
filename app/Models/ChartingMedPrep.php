<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartingMedPrep extends Model
{
    use HasFactory;

    protected $table = 'chartings_med_prep';

    protected $fillable = [
        'patient_id',
        'faculty_id',
        'medication_name',
        'dose',
        'route',
        'preparation_steps',
        'double_checked_by',
        'safety_checks_completed',
        'time_prepared',
        'remarks',
        'is_archived',
    ];

    protected $casts = [
        'time_prepared'          => 'datetime',
        'safety_checks_completed'=> 'boolean',
        'is_archived'            => 'boolean',
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