<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyCaseRequirement extends Model
{
    use HasFactory;

    protected $table = 'competency_case_requirements';

    protected $fillable = [
        'rotation',
        'case_type',
        'required_number',
        'description',
        'reason',
    ];

    protected $casts = [
        'required_number' => 'integer',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Filter by rotation/area.
     */
    public function scopeForRotation($query, string $rotation)
    {
        return $query->where('rotation', $rotation);
    }
}
