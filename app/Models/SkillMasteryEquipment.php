<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillMasteryEquipment extends Model
{
    use HasFactory;

    protected $table = 'skill_mastery_equipment';

    protected $fillable = [
        'checklist_id',
        'item_name',
        'item_details',
    ];

    public function checklist()
    {
        return $this->belongsTo(SkillMasteryChecklist::class, 'checklist_id');
    }
}
