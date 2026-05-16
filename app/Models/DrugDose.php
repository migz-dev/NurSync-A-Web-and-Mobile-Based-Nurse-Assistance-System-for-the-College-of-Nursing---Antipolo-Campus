<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DrugDose extends Model
{
    protected $table = 'drug_doses';

    protected $fillable = [
        'drug_id',
        'population',     // adult | pediatric | geriatric | neonate
        'route',          // PO | IV | IM | Topical ...
        'form',           // tablet | capsule | syrup ...
        'dose_text',
        'max_dose_text',
    ];

    /** Relationships */
    public function drug()
    {
        return $this->belongsTo(Drug::class, 'drug_id');
    }

    /** Scopes */
    public function scopeForPopulation(Builder $q, ?string $pop): Builder
    {
        return $pop ? $q->where('population', $pop) : $q;
    }

    public function scopeForRoute(Builder $q, ?string $route): Builder
    {
        return $route ? $q->where('route', $route) : $q;
    }
}
