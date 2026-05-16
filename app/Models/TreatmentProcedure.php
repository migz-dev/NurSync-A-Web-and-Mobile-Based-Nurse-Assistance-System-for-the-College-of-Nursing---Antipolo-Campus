<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TreatmentProcedure extends Model
{
    use SoftDeletes;

    protected $table = 'chartings_treatment_records';

    protected $fillable = [
        'patient_id','faculty_id','performed_at',
        'procedure_name','indication','details','outcome',
        'performed_by','observed_by','complications','remarks',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    /* Scopes */
    public function scopeOwned($q, $facultyId)   { return $q->where('faculty_id', $facultyId); }
    public function scopeForPatient($q, $pid)    { return $q->where('patient_id', $pid); }
    public function scopeLatestFirst($q)         { return $q->orderByDesc('performed_at')->orderByDesc('id'); }

    /* Relations */
    public function patient() { return $this->belongsTo(Patient::class); }
}
