<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SimRunStep extends Model
{
    protected $table = 'sim_run_steps';

    protected $fillable = [
        'sim_run_id',
        'procedure_step_id',   // can reference return_demo_steps.id or procedure_steps.id
        'is_done',
        'done_at',
        'time_spent_sec',
        'flag',
        'notes',
    ];

    protected $casts = [
        'is_done' => 'boolean',
        'done_at' => 'datetime',
    ];

    /* -----------------------------------------
     | Relationships (kept for compatibility)
     |------------------------------------------*/
    public function run()
    {
        return $this->belongsTo(SimRun::class, 'sim_run_id');
    }

    /**
     * Legacy relationship to ProcedureStep model.
     * If you’re fully on Return Demo, prefer the accessors below.
     */
    public function step()
    {
        return $this->belongsTo(ProcedureStep::class, 'procedure_step_id');
    }

    /* -----------------------------------------
     | Unified accessors for Return Demo or Legacy
     |------------------------------------------*/

    /**
     * Returns the underlying step record regardless of source:
     * - If a row exists in return_demo_steps with id = procedure_step_id, returns that (stdClass)
     * - Else tries the legacy ProcedureStep Eloquent model
     */
    public function getStepRecordAttribute(): ?object
    {
        // Prefer Return Demo step if table/row exists
        if (Schema::hasTable('return_demo_steps')) {
            $rd = DB::table('return_demo_steps')->where('id', $this->procedure_step_id)->first();
            if ($rd) {
                return $rd;
            }
        }

        // Fallback to legacy procedure_steps via relation
        try {
            return $this->relationLoaded('step') ? $this->getRelation('step') : $this->step()->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Convenience: step title (works for either source). */
    public function getStepTitleAttribute(): ?string
    {
        $s = $this->step_record;
        return $s->title ?? null;
    }

    /** Convenience: step text/body (works for either source). */
    public function getStepBodyAttribute(): ?string
    {
        $s = $this->step_record;
        // Legacy uses 'body'; Return Demo screenshot also shows 'body'
        return $s->body ?? null;
    }

    /** Convenience: step number/order (works for either source). */
    public function getStepNoAttribute(): ?int
    {
        $s = $this->step_record;
        // Legacy likely 'step_no'; Return Demo uses 'step_no'
        return isset($s->step_no) ? (int) $s->step_no : null;
    }

    /** Convenience: hint from rationale/caution (Return Demo fields). */
    public function getStepHintAttribute(): ?string
    {
        $s = $this->step_record;
        if (!$s) return null;
        return $s->rationale ?? $s->caution ?? null;
    }

    /** Convenience: expected duration in seconds if available. */
    public function getExpectedDurationAttribute(): ?int
    {
        $s = $this->step_record;
        return isset($s->duration_seconds) ? (int) $s->duration_seconds : null;
    }

    /* -----------------------------------------
     | Scopes
     |------------------------------------------*/
    public function scopeForRun($q, int $runId)
    {
        return $q->where('sim_run_id', $runId);
    }
}
