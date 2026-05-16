<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KardexEntry extends Model
{
    use SoftDeletes;

    protected $table = 'chartings_kardex';

    protected $fillable = [
        'patient_id','faculty_id','updated_for',
        'diagnosis','allergies','diet','activity',
        'iv_fluids','procedures','medications','nursing_orders',
        'labs_monitoring','isolation_prec','notes',
    ];

    protected $casts = [
        'updated_for' => 'datetime',
    ];

    public function scopeOwned($q, $facultyId) { return $q->where('faculty_id', $facultyId); }
    public function scopeForPatient($q, $pid)  { return $q->where('patient_id', $pid); }
    public function scopeLatestFirst($q)       { return $q->orderByDesc('updated_for')->orderByDesc('id'); }

    public function patient() { return $this->belongsTo(Patient::class); }
}
