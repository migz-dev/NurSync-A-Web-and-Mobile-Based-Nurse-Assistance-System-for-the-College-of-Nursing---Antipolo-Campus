<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugMonograph extends Model
{
    protected $table = 'drug_monographs';

    protected $fillable = [
        'drug_id',
        'indications',
        'mechanism',
        'contraindications',
        'adverse_effects',
        'nursing_responsibilities',
        'patient_teaching',
        'monitoring_params',
        'references',
    ];

    protected $casts = [
        'references' => 'array', // JSON -> array
    ];

    /** Relationships */
    public function drug()
    {
        return $this->belongsTo(Drug::class, 'drug_id');
    }
}
