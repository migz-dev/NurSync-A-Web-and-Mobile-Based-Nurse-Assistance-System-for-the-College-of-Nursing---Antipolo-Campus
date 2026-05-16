<?php

namespace App\Models\Traits;

trait Archivable
{
    public function scopeNotArchived($q) { return $q->whereNull('archived_at'); }
    public function scopeArchived($q)    { return $q->whereNotNull('archived_at'); }

    public function archive(?int $by = null, ?string $reason = null): void
    {
        $this->forceFill([
            'archived_at'    => now(),
            'archived_by'    => $by,
            'archive_reason' => $reason,
        ])->save();
    }

    public function unarchive(): void
    {
        $this->forceFill([
            'archived_at'    => null,
            'archived_by'    => null,
            'archive_reason' => null,
        ])->save();
    }
}
