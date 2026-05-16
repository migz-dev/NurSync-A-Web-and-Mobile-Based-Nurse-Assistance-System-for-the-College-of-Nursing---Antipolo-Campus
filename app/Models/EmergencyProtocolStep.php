<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyProtocolStep extends Model
{
    public $timestamps = false;

    protected $table = 'emergency_protocol_steps';

    protected $fillable = [
        'protocol_id',
        'step_no',
        'title',
        'description',
        'expected_action',
    ];

    protected $casts = [
        'protocol_id' => 'integer',
        'step_no'     => 'integer',
    ];

    /**
     * Parent emergency protocol.
     */
    public function protocol()
    {
        return $this->belongsTo(EmergencyProtocol::class, 'protocol_id');
    }

    /**
     * Scope: order steps in natural sequence.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('step_no');
    }

    /**
     * Accessor: short version of the title (useful for UI truncation).
     */
    public function getShortTitleAttribute()
    {
        return strlen($this->title ?? '') > 60
            ? substr($this->title, 0, 57) . '...'
            : $this->title;
    }
}
