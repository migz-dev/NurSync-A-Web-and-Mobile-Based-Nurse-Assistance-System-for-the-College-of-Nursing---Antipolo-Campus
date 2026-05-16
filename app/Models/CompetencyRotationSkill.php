<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyRotationSkill extends Model
{
    use HasFactory;

    protected $table = 'competency_rotation_skills';

    protected $fillable = [
        'rotation',
        'skill',
        'description',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Filter by rotation (e.g. DR, OR, MS, ICU, Pedia).
     */
    public function scopeForRotation($query, string $rotation)
    {
        return $query->where('rotation', $rotation);
    }
}
