<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartingDiagnosticResult extends Model
{
    use HasFactory;

    protected $table = 'chartings_diagnostic_results';

    protected $fillable = [
        'patient_id',
        'faculty_id',
        'result_type',
        'result_title',
        'result_date',
        'significant_findings',
        'critical_values',
        'interpretation_notes',
        'actions_taken',
        'attachment_path',
        'is_archived',
    ];

    protected $casts = [
        'result_date' => 'datetime',
        'is_archived' => 'boolean',
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
