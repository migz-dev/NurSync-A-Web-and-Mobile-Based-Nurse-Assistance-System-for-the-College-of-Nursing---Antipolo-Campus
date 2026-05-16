<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DrugInteraction extends Model
{
    protected $table = 'drug_interactions';

    protected $fillable = [
        'drug_id',
        'interacts_with',
        'severity',     // minor | moderate | major
        'mechanism',
        'management',
    ];

    /** Relationships */
    public function drug()
    {
        return $this->belongsTo(Drug::class, 'drug_id');
    }

    /** Scopes */
    public function scopeSeverity(Builder $q, ?string $level): Builder
    {
        return $level ? $q->where('severity', $level) : $q;
    }

    public function scopeWithName(Builder $q, ?string $name): Builder
    {
        return $name ? $q->where('interacts_with', 'LIKE', "%{$name}%") : $q;
    }
}
