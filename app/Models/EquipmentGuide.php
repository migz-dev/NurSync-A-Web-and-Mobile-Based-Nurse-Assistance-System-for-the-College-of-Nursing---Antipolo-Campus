<?php
// app/Models/EquipmentGuide.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentGuide extends Model
{
    protected $fillable = [
        'category','ward_scope','item_name','variants_or_examples',
        'typical_uses','related_procedures_or_tasks','notes'
    ];
}
