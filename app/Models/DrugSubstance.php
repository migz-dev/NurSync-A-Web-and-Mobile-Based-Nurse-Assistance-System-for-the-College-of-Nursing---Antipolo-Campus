<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugSubstance extends Model
{
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(DrugProduct::class, 'substance_id');
    }
}
