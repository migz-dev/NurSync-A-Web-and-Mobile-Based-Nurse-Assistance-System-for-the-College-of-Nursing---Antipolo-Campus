<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SimRun extends Model
{
    /* -----------------------------------------------------------------
     | Table & fillable
     |------------------------------------------------------------------*/
    protected $table = 'sim_runs';

    protected $fillable = [
        'student_id',
        'procedure_id',     // NOTE: still used; points to return_demo_skills.id for Return Demo
        'mode',
        'status',
        'started_at',
        'ended_at',
        'reflection_text',
        'meta_json',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'meta_json'  => 'array',
    ];

    /* -----------------------------------------------------------------
     | Enums (string constants used across app)
     |------------------------------------------------------------------*/
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';
    public const STATUS_ABORTED     = 'aborted';

    public const MODE_GUIDED   = 'guided';
    public const MODE_CHECKOFF = 'checkoff';

    /* -----------------------------------------------------------------
     | Relationships (legacy-friendly)
     |------------------------------------------------------------------*/
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Legacy relation to Procedures table (kept for backward compatibility).
     * If you are fully on Return Demo, prefer the accessors below:
     * - $simRun->procedureRecord  (returns either Procedure or Return Demo skill row)
     * - $simRun->procedureTitle
     */
    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    /**
     * Steps recorded for this run (your sim_run_steps table).
     * Unchanged; still useful if you log per-step outcomes.
     */
    public function steps()
    {
        return $this->hasMany(SimRunStep::class, 'sim_run_id');
    }

    /* -----------------------------------------------------------------
     | Query Scopes
     |------------------------------------------------------------------*/
    public function scopeMine(Builder $q, int $studentId): Builder
    {
        return $q->where('student_id', $studentId);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_COMPLETED);
    }

    public function scopeMode(Builder $q, string $mode): Builder
    {
        return $q->where('mode', $mode);
    }

    /* -----------------------------------------------------------------
     | Accessors & Virtuals
     |------------------------------------------------------------------*/

    /** Derived: total duration in seconds (null if not ended). */
    protected function durationSeconds(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->started_at || !$this->ended_at) return null;
            return $this->ended_at->diffInSeconds($this->started_at);
        });
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Unified "procedure" record:
     * - If a row exists in return_demo_skills with id = procedure_id, return that row.
     * - Else, try the legacy procedures table via the Eloquent relation.
     */
    public function getProcedureRecordAttribute(): ?object
    {
        // Prefer Return Demo skill if table/row exists
        if (Schema::hasTable('return_demo_skills')) {
            $skill = DB::table('return_demo_skills')->where('id', $this->procedure_id)->first();
            if ($skill) {
                return $skill;
            }
        }

        // Fallback to legacy Procedure model if available
        try {
            return $this->relationLoaded('procedure') ? $this->getRelation('procedure') : $this->procedure()->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Human title, regardless of whether the run targets a Return Demo skill
     * or a legacy Procedure.
     */
    public function getProcedureTitleAttribute(): ?string
    {
        $rec = $this->procedure_record;
        if (!$rec) return null;

        // return_demo_skills and procedures both have 'title' per your schema
        return $rec->title ?? null;
    }

    /**
     * Return Demo steps for this run’s target (if the target is a Return Demo skill).
     * Returns an empty collection if not a Return Demo or table is missing.
     */
    public function getReturnDemoStepsAttribute()
    {
        if (!Schema::hasTable('return_demo_steps')) {
            return collect();
        }

        // Only fetch when the procedure_id refers to a skill that exists
        $isSkill = Schema::hasTable('return_demo_skills')
            ? DB::table('return_demo_skills')->where('id', $this->procedure_id)->exists()
            : false;

        if (!$isSkill) {
            return collect();
        }

        return DB::table('return_demo_steps')
            ->where('return_demo_id', $this->procedure_id)
            ->orderBy('step_no')
            ->get();
    }

    /* -----------------------------------------------------------------
     | Mutators / Backwards-compat helpers
     |------------------------------------------------------------------*/
    public function setStudentUserIdAttribute($value): void
    {
        $this->attributes['student_id'] = $value;
    }

    /* -----------------------------------------------------------------
     | Domain helpers
     |------------------------------------------------------------------*/
    public function start(?string $mode = null): void
    {
        if (!$this->started_at) {
            $this->started_at = now();
        }
        if ($mode) {
            $this->mode = $mode;
        }
        $this->status = self::STATUS_IN_PROGRESS;
        $this->save();
    }

    public function complete(?string $reflection = null): void
    {
        if (!$this->ended_at) {
            $this->ended_at = now();
        }
        if ($reflection !== null) {
            $this->reflection_text = $reflection;
        }
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    public function abort(?string $reason = null): void
    {
        $meta = $this->meta_json ?? [];
        if ($reason) {
            $meta['abort_reason'] = $reason;
        }
        $this->meta_json = $meta;
        $this->status = self::STATUS_ABORTED;
        $this->ended_at = $this->ended_at ?? now();
        $this->save();
    }
}
