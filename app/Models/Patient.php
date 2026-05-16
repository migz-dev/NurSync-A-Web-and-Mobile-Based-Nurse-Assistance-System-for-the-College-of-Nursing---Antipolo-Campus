<?php
// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Faculty;   // ✅ add this

class Patient extends Model
{
    use SoftDeletes;

    protected $table = 'chartings_patients';

    protected $fillable = [
        'faculty_id',
        'hospital_no',
        'last_name', 'first_name', 'middle_name', 'suffix',
        'sex', 'dob', 'age',
        'contact_no', 'address',
        'attending_physician', 'admitting_diagnosis',
        'ward', 'bed_no',
        'admission_date', 'discharge_date',
        'status', 'archived_at', 'notes',
    ];

    public $timestamps = false;

    protected $dates = [
        'dob',
        'admission_date',
        'discharge_date',
        'archived_at',
        'deleted_at',
    ];

    protected $casts = [
        'faculty_id'     => 'integer',
        'age'            => 'integer',
        'dob'            => 'date',
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
        'archived_at'    => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * Clinical Instructor / faculty owner of this patient.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Convenience accessor: "Lastname, Firstname Middlename Suffix"
     */
    public function getDisplayNameAttribute(): string
    {
        $last   = trim((string) $this->last_name);
        $first  = trim((string) $this->first_name);
        $middle = trim((string) $this->middle_name);
        $suffix = trim((string) $this->suffix);

        $core = trim($last . ($last && $first ? ', ' : '') . $first);
        $mid  = $middle ? ' ' . $middle : '';
        $suf  = $suffix ? ' ' . $suffix : '';

        return $this->full_name ?: trim($core . $mid . $suf) ?: 'Unnamed Patient';
    }

    /**
     * Scope: limit to the currently signed-in CI, or a given faculty_id.
     */
    public function scopeOwned($query, ?int $facultyId = null)
    {
        $fid = $facultyId ?? optional(auth('faculty')->user())->id;
        return $fid ? $query->where('faculty_id', $fid) : $query;
    }
}
