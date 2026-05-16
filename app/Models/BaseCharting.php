<?php

namespace App\Models;

use App\Models\Concerns\HasArchiving;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseCharting extends Model
{
    use HasFactory, HasArchiving;

    protected $guarded = ['id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Optional: author/created_by relationship if you have users
    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
