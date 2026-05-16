<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegcardFile extends Model
{
    protected $table = 'regcard_files';

    // Your table uses uploaded_at only
    const CREATED_AT = 'uploaded_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'student_id',
        'term_id',
        'original_filename',
        'storage_path',
        'mime_type',
        'size_bytes',
        'sha256',
        'replaced_by_id',
    ];
}
