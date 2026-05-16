<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugCompany extends Model
{
    protected $table = 'drug_companies';

    protected $fillable = [
        'name',
        'country',
        'type',     // manufacturer / importer / distributor / trader
    ];

    // Optional: useful relationship for DrugProducts
    public function manufacturedProducts()
    {
        return $this->hasMany(DrugProduct::class, 'manufacturer_id');
    }

    public function importedProducts()
    {
        return $this->hasMany(DrugProduct::class, 'importer_id');
    }

    public function distributedProducts()
    {
        return $this->hasMany(DrugProduct::class, 'distributor_id');
    }

    public function tradedProducts()
    {
        return $this->hasMany(DrugProduct::class, 'trader_id');
    }
}
