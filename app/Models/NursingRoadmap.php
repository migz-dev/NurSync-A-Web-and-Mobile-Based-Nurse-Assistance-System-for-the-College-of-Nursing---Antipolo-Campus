<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NursingRoadmap extends Model
{
    protected $table = 'nursing_roadmaps';

    protected $fillable = [
        'career_level', 'category', 'role', 'slug', 'description',
        'requirements', 'steps_text', 'steps_json',
    ];

    // If your table doesn’t have timestamps, uncomment:
    // public $timestamps = false;
}
