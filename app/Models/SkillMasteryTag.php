<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillMasteryTag extends Model
{
    use HasFactory;

    protected $table = 'skill_mastery_tags';

    protected $fillable = [
        'name',
    ];

    public function checklists()
    {
        return $this->belongsToMany(
            SkillMasteryChecklist::class,
            'skill_mastery_checklist_tag',
            'tag_id',
            'checklist_id'
        );
    }

    // in SkillMasteryChecklist
public function tags()
{
    return $this->belongsToMany(SkillMasteryTag::class, 'skill_mastery_checklist_tag')
                ->using(SkillMasteryChecklistTag::class);
}

}
