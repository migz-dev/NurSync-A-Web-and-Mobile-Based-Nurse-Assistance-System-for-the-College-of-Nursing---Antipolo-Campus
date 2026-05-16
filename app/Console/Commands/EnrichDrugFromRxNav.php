<?php

// app/Console/Commands/EnrichDrugFromRxNav.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\{Drug, DrugInteraction};
use App\Services\RxNavClient;

class EnrichDrugFromRxNav extends Command
{
    protected $signature = 'drug:rxnav:enrich {drug_id* : One or more Drug IDs} {--overwrite-brands} {--overwrite-interactions}';
    protected $description = 'Fetch RxCUI, brands, and interactions from RxNav and attach to a Drug';

    public function handle(RxNavClient $rx): int
    {
        $ids = $this->argument('drug_id');
        foreach ($ids as $id) {
            $drug = Drug::find($id);
            if (!$drug) { $this->warn("Drug #$id not found."); continue; }

            $this->info("→ Enriching [#{$drug->id}] {$drug->generic_name}");

            // Resolve RxCUI (use existing if available)
            $rxcui = $drug->rxcui ?: $rx->rxcuiFromName($drug->generic_name);
            if (!$rxcui) { $this->warn('  ! RxCUI not found'); continue; }

            // Gather data
            $brands = $rx->brandNames($rxcui);
            $ix     = $rx->interactions($rxcui);

            DB::transaction(function () use ($drug, $rxcui, $brands, $ix) {
                // Update drug core (brands merged by default)
                $newBrands = collect($brands);
                $existing  = collect($drug->brand_names ?? []);
                $merged    = $this->option('overwrite-brands') ? $newBrands
                              : $existing->merge($newBrands)->unique()->values();

                $drug->update([
                    'rxcui'            => $rxcui,
                    'brand_names'      => $merged->all() ?: null,
                    'last_reviewed_at' => now(),
                ]);

                // Interactions
                if ($this->option('overwrite-interactions')) {
                    $drug->interactions()->delete();
                }
                $toInsert = collect($ix)->map(fn($p) => [
                    'drug_id'       => $drug->id,
                    'interacts_with'=> $p['with'],
                    'severity'      => $p['severity'],
                    'mechanism'     => $p['mechanism'],
                    'management'    => $p['management'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ])->all();

                if (!empty($toInsert)) {
                    DrugInteraction::insert($toInsert);
                }
            });

            $this->info('  ✓ Done');
        }

        return self::SUCCESS;
    }
}
