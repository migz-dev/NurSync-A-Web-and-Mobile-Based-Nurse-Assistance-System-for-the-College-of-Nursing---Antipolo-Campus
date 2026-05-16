<?php
// app/Console/Commands/ImportDrugProducts.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Models\{
    DrugProduct,
    DrugSubstance,
    DrugDosageForm,
    DrugPharmacologicCategory,
    DrugCompany
};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportDrugProducts extends Command
{
    /** Column length guards (match your migrations) */
    private const LEN_SUBSTANCE_NAME = 1024; // drug_substances.name
    private const LEN_LOOKUP_NAME    = 512;  // dosage_forms.name, categories.name, companies.name
    private const LEN_BRAND_NAME     = 255;  // drug_products.brand_name
    private const LEN_REG_NO         = 191;  // drug_products.registration_number (safe for indexing)
    private const LEN_TEXT_SAFE      = 65000; // for TEXT columns (packaging, classification, etc.)
    private const LEN_COUNTRY        = 512;   // companies.country or product.country_of_origin

    /** Default path points to storage/app */
    protected $signature   = 'nursync:import-drug-products {csv_path=storage/app/ALL_DrugProducts.csv} {--chunk=1000}';
    protected $description = 'Import ALL_DrugProducts.csv into normalized drug tables';

    public function handle(): int
    {
        $argPath = (string) $this->argument('csv_path');
        $csvPath = $this->resolveCsvPath($argPath);
        $chunk   = max(1, (int) $this->option('chunk'));

        if (!is_file($csvPath) || !is_readable($csvPath)) {
            $this->error("CSV not found or unreadable: {$csvPath}");
            $this->line('Tip: put the file in storage/app and run:');
            $this->line('  php artisan nursync:import-drug-products "storage/app/ALL_DrugProducts.csv"');
            return self::FAILURE;
        }

        $this->info("Reading: {$csvPath}");
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);

        $stmt    = (new Statement());
        $records = $stmt->process($csv);

        $buffer = [];
        $count  = 0;
        $since  = microtime(true);

        // (MySQL) small speed-up for bulk load
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($records as $row) {
            // Trim strings
            $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

            // ----- Lookups (case-insensitive, create if missing) -----
            $substance  = $this->firstOrCreateLookup(DrugSubstance::class, $row['Generic Name'] ?? null);
            $dosageForm = $this->firstOrCreateLookup(DrugDosageForm::class, $row['Dosage Form'] ?? null);
            $category   = $this->firstOrCreateLookup(DrugPharmacologicCategory::class, $row['Pharmacologic Category'] ?? null);

            // Companies (one row per unique name; no "type")
            $manufacturer = $this->firstOrCreateCompany($row['Manufacturer'] ?? null, $row['Country of Origin'] ?? null);
            $importer     = $this->firstOrCreateCompany($row['Importer'] ?? null);
            $distributor  = $this->firstOrCreateCompany($row['Distributor'] ?? null);
            $trader       = $this->firstOrCreateCompany($row['Trader'] ?? null);

            // ----- Build product row with length guards -----
            $buffer[] = [
                'registration_number'       => $this->clip($row['Registration Number'] ?? null, self::LEN_REG_NO),
                'substance_id'              => $substance?->id,
                'brand_name'                => $this->clip($row['Brand Name'] ?? null, self::LEN_BRAND_NAME),
                'dosage_form_id'            => $dosageForm?->id,
                'dosage_strength'           => $this->clip($row['Dosage Strength'] ?? null, self::LEN_TEXT_SAFE),
                'classification'            => $this->clip($row['Classification'] ?? null, self::LEN_TEXT_SAFE),
                'packaging'                 => $this->clip($row['Packaging'] ?? null, self::LEN_TEXT_SAFE),
                'pharmacologic_category_id' => $category?->id,

                'manufacturer_id'           => $manufacturer?->id,
                'importer_id'               => $importer?->id,
                'distributor_id'            => $distributor?->id,
                'trader_id'                 => $trader?->id,

                'country_of_origin'         => $this->clip($row['Country of Origin'] ?? null, self::LEN_COUNTRY),
                'application_type'          => $this->clip($row['Application Type'] ?? null, self::LEN_TEXT_SAFE),
                'issued_at'                 => $this->toDate($row['Issuance Date'] ?? null),
                'expires_at'                => $this->toDate($row['Expiry Date'] ?? null),

                'created_at'                => now(),
                'updated_at'                => now(),
            ];

            if (count($buffer) >= $chunk) {
                $this->flush($buffer);
                $count += $chunk;
                $this->info("Imported {$count} rows...");
            }
        }

        if (!empty($buffer)) {
            $this->flush($buffer);
            $count += count($buffer);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $elapsed = round(microtime(true) - $since, 2);
        $this->info("Done. Imported {$count} records in {$elapsed}s.");
        return self::SUCCESS;
    }

    /* ======================= Helpers ======================= */

    /** Accepts absolute path, project-relative path, or storage/app path */
    private function resolveCsvPath(string $arg): string
    {
        if (Str::startsWith($arg, ['storage/app/', 'storage\\app\\'])) {
            $rel = Str::after($arg, 'storage/app/');
            return storage_path('app' . DIRECTORY_SEPARATOR . $rel);
        }
        if (!str_contains($arg, DIRECTORY_SEPARATOR) && Storage::disk()->exists($arg)) {
            return Storage::path($arg);
        }
        $candidate = base_path($arg);
        if (File::exists($candidate)) {
            return realpath($candidate) ?: $candidate;
        }
        return $arg; // absolute
    }

    /** Normalize to reduce dupes from spacing/punctuation */
    private function norm(?string $s): string
    {
        $s = trim((string) $s);
        $s = preg_replace('/\s+/', ' ', $s);
        $s = rtrim($s, ",. ");
        return $s;
    }

    /** Clip by bytes (utf8-safe) to respect column limits */
    private function clip(?string $s, int $maxBytes): ?string
    {
        if ($s === null) return null;
        return mb_strcut($s, 0, $maxBytes, 'UTF-8');
    }

    private function toDate($val): ?string
    {
        if (!$val) return null;
        try {
            return Carbon::parse($val)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Case-insensitive first-or-create for lookup tables with correct length guards */
    private function firstOrCreateLookup(string $modelClass, ?string $name)
    {
        $name = $this->norm($name ?? '');
        if ($name === '') return null;

        // decide max length per table
        $max = ($modelClass === DrugSubstance::class)
            ? self::LEN_SUBSTANCE_NAME
            : self::LEN_LOOKUP_NAME;

        $name = $this->clip($name, $max);

        $existing = $modelClass::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
        return $existing ?: $modelClass::create(['name' => $name]);
    }

    /**
     * One company per unique name (no "type"). Backfills country if missing.
     */
    private function firstOrCreateCompany(?string $name, ?string $country = null): ?DrugCompany
    {
        $name = $this->clip($this->norm($name ?? ''), self::LEN_LOOKUP_NAME);
        if ($name === '') return null;

        $existing = DrugCompany::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
        if ($existing) {
            $country = $this->clip($this->norm($country ?? ''), self::LEN_COUNTRY);
            if (!$existing->country && $country) {
                $existing->country = $country;
                $existing->save();
            }
            return $existing;
        }

        return DrugCompany::create([
            'name'    => $name,
            'country' => $this->clip($this->norm($country ?? ''), self::LEN_COUNTRY),
        ]);
    }

    /** Insert a buffered chunk */
    private function flush(array &$buffer): void
    {
        DB::table('drug_products')->insert($buffer);
        $buffer = [];
    }
}