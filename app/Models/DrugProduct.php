<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugProduct extends Model
{
    protected $table = 'drug_products';

    protected $fillable = [
        'registration_number',
        'substance_id',
        'brand_name',
        'dosage_form_id',
        'dosage_strength',
        'classification',
        'packaging',               // raw text from FDA
        'packaging_type',          // normalized category (Tablet, IV bag, etc.)
        'pharmacologic_category_id',
        'class_group',             // normalized class (Antibiotic, Analgesic, etc.)
        'manufacturer_id',
        'importer_id',
        'distributor_id',
        'trader_id',
        'country_of_origin',
        'application_type',
        'issued_at',
        'expires_at',
    ];

    protected $casts = [
        'issued_at'  => 'date',
        'expires_at' => 'date',
    ];

    // Relationships
    public function substance()
    {
        return $this->belongsTo(DrugSubstance::class, 'substance_id');
    }

    public function dosageForm()
    {
        return $this->belongsTo(DrugDosageForm::class, 'dosage_form_id');
    }

    public function category()
    {
        return $this->belongsTo(DrugPharmacologicCategory::class, 'pharmacologic_category_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(DrugCompany::class, 'manufacturer_id');
    }

    public function importer()
    {
        return $this->belongsTo(DrugCompany::class, 'importer_id');
    }

    public function distributor()
    {
        return $this->belongsTo(DrugCompany::class, 'distributor_id');
    }

    public function trader()
    {
        return $this->belongsTo(DrugCompany::class, 'trader_id');
    }

    /* Optional helper scopes for filters on CI/Student pages */

    public function scopePackagingType($query, ?string $type)
    {
        if ($type) {
            $query->where('packaging_type', $type);
        }
        return $query;
    }

    public function scopeClassGroup($query, ?string $class)
    {
        if ($class) {
            $query->where('class_group', $class);
        }
        return $query;
    }
}
