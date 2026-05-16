<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnDemoAttachment extends Model
{
    use HasFactory;

    protected $table = 'return_demo_attachments';

    protected $fillable = [
        'return_demo_id','type','label','path','uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function skill()
    {
        return $this->belongsTo(ReturnDemoSkill::class, 'return_demo_id');
    }
}
