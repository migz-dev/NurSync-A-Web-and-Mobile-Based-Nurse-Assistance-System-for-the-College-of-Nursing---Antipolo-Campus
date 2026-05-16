<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class NursesNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chartings_nurses_notes';

    protected $fillable = [
        'patient_id', 'faculty_id', 'logged_at', 'note_type',
        'subjective', 'objective', 'assessment', 'plan', 'note', 'status',
    ];

    protected $casts = [
        'logged_at'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /** Relationships */
    public function patient() { return $this->belongsTo(Patient::class, 'patient_id'); }
    public function faculty() { return $this->belongsTo(Faculty::class, 'faculty_id'); }

    /** Scopes */
    public function scopeOwned($q, $facultyId = null) {
        $fid = $facultyId ?? Auth::guard('faculty')->id();
        return $fid ? $q->where('faculty_id', $fid) : $q;
    }
    public function scopeForPatient($q, $patientId) { return $q->where('patient_id', $patientId); }
    public function scopeLatestFirst($q) { return $q->orderByDesc('logged_at')->orderByDesc('id'); }
    public function scopeSearch($q, $needle) {
        $like = '%'.$needle.'%';
        return $q->where(function ($qq) use ($like) {
            $qq->where('note', 'like', $like)
               ->orWhere('subjective', 'like', $like)
               ->orWhere('objective', 'like', $like)
               ->orWhere('assessment', 'like', $like)
               ->orWhere('plan', 'like', $like);
        });
    }

    /** Helpers */
    public static function types(): array { return ['Narrative','SOAP','DAR','PIE','Focus']; }
    public static function statuses(): array { return ['draft','final']; }
}
