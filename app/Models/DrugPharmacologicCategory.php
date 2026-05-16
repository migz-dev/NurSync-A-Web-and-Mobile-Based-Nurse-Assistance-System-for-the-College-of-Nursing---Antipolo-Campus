<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugPharmacologicCategory extends Model
{
    protected $table = 'drug_pharmacologic_categories';

    protected $fillable = [
        'name',
    ];

    /**
     * A pharmacologic category belongs to many products
     */
    public function products()
    {
        return $this->hasMany(DrugProduct::class, 'pharmacologic_category_id');
    }

    /**
     * Optional: for dropdown lists
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }
}
