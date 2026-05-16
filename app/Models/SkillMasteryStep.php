<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillMasteryStep extends Model
{
    use HasFactory;

    protected $table = 'skill_mastery_steps';

    protected $fillable = [
        'checklist_id',
        'step_no',
        'action',
        'rationale',
        'safety_point',
    ];

    public function checklist()
    {
        return $this->belongsTo(SkillMasteryChecklist::class, 'checklist_id');
    }
}
