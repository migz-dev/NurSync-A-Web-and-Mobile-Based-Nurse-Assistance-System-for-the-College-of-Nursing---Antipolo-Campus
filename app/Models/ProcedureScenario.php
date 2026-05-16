<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcedureScenario extends Model
{
    protected $fillable = ['procedure_id','prompt','choices','answer','rationale'];
    protected $casts = ['choices' => 'array'];

    public function procedure() {
        return $this->belongsTo(Procedure::class);
    }
}