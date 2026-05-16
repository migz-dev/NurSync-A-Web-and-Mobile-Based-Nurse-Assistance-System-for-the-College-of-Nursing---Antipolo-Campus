<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NursingReference extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'nursing_references';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'category',
        'url',
        'source',
        'description',
        'tags_json',
        'is_featured',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'tags_json'   => 'array',
    ];

    /**
     * Scope: only active references.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: featured references only.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Accessor: get tags array (safe fallback).
     */
    public function getTagsAttribute(): array
    {
        return is_array($this->tags_json)
            ? $this->tags_json
            : (json_decode($this->tags_json, true) ?: []);
    }

    /**
     * Mutator: set tags as JSON.
     */
    public function setTagsAttribute($value): void
    {
        $this->attributes['tags_json'] = json_encode(array_values((array) $value));
    }
}