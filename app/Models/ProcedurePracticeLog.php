<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcedurePracticeLog extends Model
{
    protected $fillable = [
        'procedure_id','user_id','scenario_id','mode',
        'steps_completed','hints_used','decision_errors','elapsed_seconds','meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function procedure() { return $this->belongsTo(Procedure::class); }
    public function user() { return $this->belongsTo(User::class); }
}
