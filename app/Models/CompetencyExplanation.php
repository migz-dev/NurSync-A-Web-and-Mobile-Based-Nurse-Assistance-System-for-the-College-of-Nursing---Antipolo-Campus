<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyExplanation extends Model
{
    use HasFactory;

    protected $table = 'competency_explanations';

    protected $fillable = [
        'competency_item_id',
        'title',
        'content',
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
     * The competency item this explanation belongs to.
     */
    public function item()
    {
        return $this->belongsTo(CompetencyItem::class, 'competency_item_id');
    }
}
