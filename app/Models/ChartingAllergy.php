<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartingAllergy extends Model
{
    use HasFactory;

    protected $table = 'chartings_allergies';

    protected $fillable = [
        'patient_id',
        'faculty_id',
        'allergen',
        'reaction',
        'severity',
        'date_observed',
        'notes',
        'action_taken',
        'is_archived',
    ];

    protected $casts = [
        'date_observed' => 'datetime',
        'is_archived'   => 'boolean',
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