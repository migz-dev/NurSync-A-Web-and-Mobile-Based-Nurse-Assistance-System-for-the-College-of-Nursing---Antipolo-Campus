<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientSummary extends Model
{
    use SoftDeletes;

    protected $table = 'chartings_patient_summary';

    protected $fillable = [
        'patient_id','faculty_id','logged_at',
        'summary','plan','status','remarks',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    public function scopeOwned($q, $facultyId) { return $q->where('faculty_id', $facultyId); }
    public function scopeForPatient($q, $pid)  { return $q->where('patient_id', $pid); }
    public function scopeLatestFirst($q)       { return $q->orderByDesc('logged_at')->orderByDesc('id'); }

    public function patient() { return $this->belongsTo(Patient::class); }
}
