<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SkillMasteryChecklistTag extends Pivot
{
    protected $table = 'skill_mastery_checklist_tag';

    protected $fillable = [
        'checklist_id',
        'tag_id',
    ];

    public $timestamps = false;
}
