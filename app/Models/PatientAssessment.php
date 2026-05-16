<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientAssessment extends Model
{
    use SoftDeletes;

    protected $table = 'chartings_patient_assessments';

    protected $fillable = [
        'patient_id','faculty_id','assessed_at','assessment_type',
        'chief_complaint','subjective','objective','assessment','plan','notes',
    ];

    protected $casts = [
        'assessed_at' => 'datetime',
    ];

    public function scopeOwned($q, $facultyId) { return $q->where('faculty_id', $facultyId); }
    public function scopeForPatient($q, $pid)  { return $q->where('patient_id', $pid); }
    public function scopeLatestFirst($q)       { return $q->orderByDesc('assessed_at')->orderByDesc('id'); }

    public function patient() { return $this->belongsTo(Patient::class); }
}
