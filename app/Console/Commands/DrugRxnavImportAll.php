<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{Drug, DrugInteraction};
use App\Services\RxNavClient;

class DrugRxnavImportAll extends Command
{
    protected $signature = 'drug:rxnav:import-all
        {--tty=IN : RxNorm term type (IN ingredients, SCD clinical drugs, …)}
        {--class= : Set ATC/class for all imported rows}
        {--core : Mark imported as core}
        {--limit= : Stop after N items}
        {--offset=0 : Skip first N items}
        {--enrich : Also fetch brands & interactions}
        {--overwrite-brands : Replace brands when enriching}
        {--overwrite-interactions : Replace interactions when enriching}
        {--dry-run : Don’t write to DB}';

    protected $description = 'Import a large set of drugs directly from RxNav (all concepts by TTY)';

    /** How many interaction rows to insert per SQL packet */
    private const INTERACTION_CHUNK = 50;

    public function handle(RxNavClient $rx): int
    {
        $tty     = strtoupper((string)($this->option('tty') ?: 'IN'));
        $class   = $this->option('class');
        $isCore  = (bool)$this->option('core');
        $limit   = $this->option('limit') ? (int)$this->option('limit') : null;
        $offset  = (int)$this->option('offset');
        $enrich  = (bool)$this->option('enrich');
        $owBrand = (bool)$this->option('overwrite-brands');
        $owIx    = (bool)$this->option('overwrite-interactions');
        $dry     = (bool)$this->option('dry-run');

        $this->info("Fetching RxNav allconcepts (TTY=$tty)…");
        $slice = collect($rx->allConcepts($tty))->slice($offset, $limit)->values();

        if ($slice->isEmpty()) {
            $this->warn('No concepts returned.');
            return 0;
        }

        $bar = $this->output->createProgressBar($slice->count());
        $bar->start();

        $created = $updated = $enriched = $skipped = $errors = 0;

        foreach ($slice as $row) {
            $generic = $row['name'];
            $rxcui   = $row['rxcui'];

            try {
                if ($dry) { $skipped++; $bar->advance(); continue; }

                DB::transaction(function () use ($generic, $rxcui, $class, $isCore, $enrich, $owBrand, $owIx, &$created, &$updated, &$enriched, $rx) {
                    $drug = Drug::where('generic_name', $generic)->first();

                    $payload = [
                        'atc_class'        => $class ?: ($drug->atc_class ?? null),
                        'is_core'          => $isCore || ($drug->is_core ?? false),
                        'rxcui'            => $rxcui,
                        'last_reviewed_at' => now(),
                    ];

                    if (!$drug) {
                        $drug = Drug::create(array_merge(['generic_name' => $generic], $payload));
                        $created++;
                    } else {
                        $drug->update($payload);
                        $updated++;
                    }

                    if ($enrich) {
                        // be polite to the public API
                        usleep(200000); // 200ms

                        $brands = $rx->brandNames($rxcui);
                        $pairs  = $rx->interactions($rxcui);

                        // merge or overwrite brands
                        $merged = $owBrand
                            ? collect($brands)
                            : collect($drug->brand_names ?? [])->merge($brands)->unique()->values();

                        $drug->update([
                            'brand_names'      => $merged->all() ?: null,
                            'last_reviewed_at' => now(),
                        ]);

                        // overwrite or append interactions
                        if ($owIx) {
                            $drug->interactions()->delete();
                        }

                        if (!empty($pairs)) {
                            // trim long text & insert in chunks to avoid max_allowed_packet errors
                            $records = collect($pairs)->map(fn ($p) => [
                                'drug_id'        => $drug->id,
                                'interacts_with' => $p['with'],
                                'severity'       => $p['severity'],
                                'mechanism'      => Str::limit((string)($p['mechanism'] ?? ''), 1000, ''),
                                'management'     => Str::limit((string)($p['management'] ?? ''), 2000, ''),
                                'created_at'     => now(),
                                'updated_at'     => now(),
                            ]);

                            $records->chunk(self::INTERACTION_CHUNK)
                                ->each(fn ($chunk) => DrugInteraction::insert($chunk->all()));
                        }

                        $enriched++;
                    }
                });

            } catch (\Throwable $e) {
                $errors++;
                $this->line('');
                $this->warn("Error: {$generic} ({$rxcui}) — ".$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line("\n");
        $this->table(['Created', 'Updated', 'Enriched', 'Skipped', 'Errors'], [[
            $created, $updated, $enriched, $skipped, $errors
        ]]);

        return $errors > 0 ? 1 : 0;
    }
}
