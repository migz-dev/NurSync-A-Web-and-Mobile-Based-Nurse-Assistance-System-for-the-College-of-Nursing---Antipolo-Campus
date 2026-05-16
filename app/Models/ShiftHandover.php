<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftHandover extends Model
{
    use SoftDeletes;

    protected $table = 'chartings_shift_handover';

    protected $fillable = [
        'patient_id','faculty_id','handed_over_at','shift',
        'from_nurse','to_nurse','summary','pending_orders',
        'tasks','safety_risks','code_status','remarks',
    ];

    protected $casts = [
        'handed_over_at' => 'datetime',
    ];

    public function scopeOwned($q, $facultyId) { return $q->where('faculty_id', $facultyId); }
    public function scopeForPatient($q, $pid)  { return $q->where('patient_id', $pid); }
    public function scopeLatestFirst($q)       { return $q->orderByDesc('handed_over_at')->orderByDesc('id'); }

    public function patient() { return $this->belongsTo(Patient::class); }
}
