<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyItem extends Model
{
    use HasFactory;

    protected $table = 'competency_items';

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'reason',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Category this competency belongs to.
     */
    public function category()
    {
        return $this->belongsTo(CompetencyCategory::class, 'category_id');
    }

    /**
     * Long-form explanations / narratives written by nurses.
     */
    public function explanations()
    {
        return $this->hasMany(CompetencyExplanation::class, 'competency_item_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Only published competency items.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Exclude archived competency items.
     */
    public function scopeNotArchived($query)
    {
        return $query->where('status', '!=', 'archived');
    }
}
