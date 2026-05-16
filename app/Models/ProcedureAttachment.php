<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureAttachment extends Model
{
    use HasFactory;

    // Table: procedure_attachments (default matches)
    protected $fillable = [
        'procedure_id',
        'type',     // pdf | image | other
        'label',
        'path',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }
}
