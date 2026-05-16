<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $table = 'drugs';

    protected $fillable = [
        'generic_name',
        'brand_names',
        'atc_class',
        'pregnancy_category',
        'lactation_notes',
        'renal_adjust_notes',
        'hepatic_adjust_notes',
        'is_core',
        'last_reviewed_at',
    ];

    protected $casts = [
        'brand_names'     => 'array',   // JSON -> array
        'is_core'         => 'boolean',
        'last_reviewed_at'=> 'datetime',
    ];

    /** Relationships */
    public function monograph()
    {
        return $this->hasOne(DrugMonograph::class, 'drug_id');
    }

    public function doses()
    {
        return $this->hasMany(DrugDose::class, 'drug_id');
    }

    public function interactions()
    {
        return $this->hasMany(DrugInteraction::class, 'drug_id');
    }

    /** ---------- Scopes ---------- */

    /** Simple search on generic + brands */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;
        $t = trim($term);
        // NOTE: JSON_SEARCH works on MySQL 5.7+; fallback OR LIKE on generic_name
        return $q->where(function (Builder $w) use ($t) {
            $w->where('generic_name', 'LIKE', "%{$t}%")
              ->orWhereRaw("JSON_SEARCH(brand_names, 'one', ?) IS NOT NULL", [$t]);
        });
    }

    /** Filter by class (ATC / pharmacologic) */
    public function scopeByClass(Builder $q, ?string $class): Builder
    {
        return $class ? $q->where('atc_class', $class) : $q;
    }

    /** Whether it’s part of the “core” offline set */
    public function scopeCore(Builder $q, ?bool $core = true): Builder
    {
        return is_null($core) ? $q : $q->where('is_core', $core ? 1 : 0);
    }

    /** Convenience accessor */
    public function getPrimaryBrandAttribute(): ?string
    {
        $brands = $this->brand_names ?? [];
        return is_array($brands) && count($brands) ? $brands[0] : null;
    }
}
