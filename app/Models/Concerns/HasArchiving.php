<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Adds archived_at / archived_by behavior to a model.
 */
trait HasArchiving
{
    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function scopeArchived(Builder $q): Builder
    {
        return $q->whereNotNull($this->getTable().'.archived_at');
    }

    public function scopeUnarchived(Builder $q): Builder
    {
        return $q->whereNull($this->getTable().'.archived_at');
    }

    public function archive(?int $userId = null): bool
    {
        $this->archived_at = Carbon::now();
        if ($this->isFillable('archived_by')) {
            $this->archived_by = $userId;
        }
        return $this->save();
    }

    public function unarchive(): bool
    {
        $this->archived_at = null;
        if ($this->isFillable('archived_by')) {
            $this->archived_by = null;
        }
        return $this->save();
    }

    public function isArchived(): bool
    {
        return ! is_null($this->archived_at);
    }
}
