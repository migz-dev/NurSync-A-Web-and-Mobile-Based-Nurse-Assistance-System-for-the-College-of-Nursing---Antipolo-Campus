<?php
// app/Models/EmergencyProtocolTag.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyProtocolTag extends Model
{
    public $timestamps = false;

    protected $table = 'emergency_protocol_tags';

    protected $fillable = [
        'name',
        'color',
    ];

    /**
     * All emergency protocols using this tag.
     */
    public function protocols()
    {
        return $this->belongsToMany(
            EmergencyProtocol::class,
            'emergency_protocol_tag_map',
            'tag_id',
            'protocol_id'
        );
    }

    /**
     * Scope: find or create by name (for tag creation in controller).
     */
    public function scopeNamed($query, string $name)
    {
        return $query->where('name', trim($name));
    }

    /**
     * Accessor for display color (default fallback).
     */
    public function getColorAttribute($value)
    {
        return $value ?: 'slate'; // default color if missing
    }
}
