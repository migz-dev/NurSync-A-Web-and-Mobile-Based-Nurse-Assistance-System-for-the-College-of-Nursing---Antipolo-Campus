<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugDosageForm extends Model
{
    protected $table = 'drug_dosage_forms';

    protected $fillable = [
        'name',
    ];

    /**
     * Dosage form is used by many drug products
     */
    public function products()
    {
        return $this->hasMany(DrugProduct::class, 'dosage_form_id');
    }

    /**
     * Optional: alphabetical ordering for dropdowns
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }
}
