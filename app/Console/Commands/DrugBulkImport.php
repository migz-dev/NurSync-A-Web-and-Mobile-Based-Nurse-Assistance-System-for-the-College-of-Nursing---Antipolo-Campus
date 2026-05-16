<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\{Drug, DrugMonograph, DrugDose, DrugInteraction};
use App\Services\RxNavClient;
use SplFileObject;

class DrugBulkImport extends Command
{
    protected $signature = 'drug:bulk-import 
        {csv : Absolute or relative path to the CSV file}
        {--enrich : Enrich each drug from RxNav (brands + interactions)}
        {--overwrite-brands : Replace brands instead of merging during enrich}
        {--overwrite-interactions : Replace interactions instead of appending during enrich}
        {--dry-run : Parse and validate only; do not write to DB}
        {--delimiter=, : CSV delimiter (default: ,)}';

    protected $description = 'Import drugs from CSV (optionally enrich from RxNav)';

    public function handle(RxNavClient $rx): int
    {
        $path = $this->argument('csv');
        $delim = (string) $this->option('delimiter');

        if (!is_file($path)) {
            $this->error("CSV not found: {$path}");
            return 1;
        }

        $dry = (bool) $this->option('dry-run');
        $doEnrich = (bool) $this->option('enrich');
        $owBrands = (bool) $this->option('overwrite-brands');
        $owIx     = (bool) $this->option('overwrite-interactions');

        $fh = new SplFileObject($path, 'r');
        $fh->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
        $fh->setCsvControl($delim);

        // --- headers ---
        if ($fh->eof()) { $this->warn('CSV is empty.'); return 0; }
        $headers = $this->normalizeRow($fh->fgetcsv());
        if (!$headers || count($headers) < 1) {
            $this->error('Could not read CSV header.');
            return 1;
        }

        // Required header
        if (!in_array('generic_name', $headers, true)) {
            $this->error('CSV must include a "generic_name" column.');
            return 1;
        }

        // Optional supported headers
        $supported = [
            'generic_name','atc_class','is_core',
            'pregnancy_category','lactation_notes','renal_adjust_notes','hepatic_adjust_notes',
            'brand_names','indications','mechanism','contraindications','adverse_effects',
            'nursing_responsibilities','patient_teaching','monitoring_params','references',
            // Dose columns (repeatable with suffix _1, _2, ...)
            // dose_population_1, dose_route_1, dose_form_1, dose_text_1, dose_max_1
            // Interaction columns (repeatable)
            // ix_with_1, ix_severity_1, ix_mechanism_1, ix_management_1
        ];

        $rows = [];
        while (!$fh->eof()) {
            $row = $this->normalizeRow($fh->fgetcsv());
            if ($row === [null] || $row === [] || $row === false) continue;

            // handle rows shorter than header
            $row = array_pad($row, count($headers), null);
            $assoc = array_combine($headers, $row) ?: [];
            if (trim((string)($assoc['generic_name'] ?? '')) === '') continue;

            $rows[] = $assoc;
        }

        if (empty($rows)) {
            $this->warn('No data rows found.');
            return 0;
        }

        $this->info('Parsing CSV…');
        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        $stats = ['processed'=>0,'created'=>0,'updated'=>0,'enriched'=>0,'skipped'=>0,'errors'=>0];

        foreach ($rows as $r) {
            $stats['processed']++;

            $generic = trim((string)$r['generic_name']);
            try {
                if ($dry) { $bar->advance(); continue; }

                DB::transaction(function () use (&$stats, $r, $generic, $rx, $doEnrich, $owBrands, $owIx) {
                    $drug = Drug::where('generic_name', $generic)->first();

                    $payload = [
                        'atc_class'            => $this->nullable($r['atc_class'] ?? null),
                        'is_core'              => (bool)($r['is_core'] ?? 0),
                        'pregnancy_category'   => $this->nullable($r['pregnancy_category'] ?? null),
                        'lactation_notes'      => $this->nullable($r['lactation_notes'] ?? null),
                        'renal_adjust_notes'   => $this->nullable($r['renal_adjust_notes'] ?? null),
                        'hepatic_adjust_notes' => $this->nullable($r['hepatic_adjust_notes'] ?? null),
                        'brand_names'          => $this->csvToArray($r['brand_names'] ?? null, ','),
                        'last_reviewed_at'     => now(),
                    ];

                    if (!$drug) {
                        $drug = Drug::create(array_merge(['generic_name'=>$generic], $payload));
                        $stats['created']++;
                    } else {
                        $drug->update($payload);
                        $stats['updated']++;
                    }

                    // Monograph (optional columns)
                    $hasMonographInput = $this->anyFilled($r, [
                        'indications','mechanism','contraindications','adverse_effects',
                        'nursing_responsibilities','patient_teaching','monitoring_params','references'
                    ]);

                    if ($hasMonographInput) {
                        $drug->monograph()->updateOrCreate(
                            ['drug_id'=>$drug->id],
                            [
                                'indications'              => $this->nullable($r['indications'] ?? null),
                                'mechanism'                => $this->nullable($r['mechanism'] ?? null),
                                'contraindications'        => $this->nullable($r['contraindications'] ?? null),
                                'adverse_effects'          => $this->nullable($r['adverse_effects'] ?? null),
                                'nursing_responsibilities' => $this->nullable($r['nursing_responsibilities'] ?? null),
                                'patient_teaching'         => $this->nullable($r['patient_teaching'] ?? null),
                                'monitoring_params'        => $this->nullable($r['monitoring_params'] ?? null),
                                'references'               => $this->csvToArray($r['references'] ?? null, ';'),
                            ]
                        );
                    }

                    // Doses (optional groups: dose_*_N)
                    $doseGroups = $this->groupedColumns($r, 'dose_', ['population','route','form','text'=>'dose_text','max'=>'dose_max']);
                    if (!empty($doseGroups)) {
                        $drug->doses()->delete();
                        foreach ($doseGroups as $g) {
                            if (empty($g['dose_text'])) continue; // require main text
                            $drug->doses()->create([
                                'population'    => $g['population'] ?? 'adult',
                                'route'         => $g['route'] ?? null,
                                'form'          => $g['form'] ?? null,
                                'dose_text'     => $g['dose_text'],
                                'max_dose_text' => $g['dose_max'] ?? null,
                            ]);
                        }
                    }

                    // Interactions (optional groups: ix_*_N)
                    $ixGroups = $this->groupedColumns($r, 'ix_', ['with','severity','mechanism','management']);
                    if (!empty($ixGroups)) {
                        $drug->interactions()->delete();
                        foreach ($ixGroups as $g) {
                            if (empty($g['with'])) continue;
                            $drug->interactions()->create([
                                'interacts_with' => $g['with'],
                                'severity'       => $g['severity'] ?? 'moderate',
                                'mechanism'      => $g['mechanism'] ?? null,
                                'management'     => $g['management'] ?? null,
                            ]);
                        }
                    }

                    // Enrich from RxNav
                    if ($doEnrich) {
                        $rxcui = $drug->rxcui ?: $rx->rxcuiFromName($drug->generic_name);
                        if ($rxcui) {
                            $brands = $rx->brandNames($rxcui);
                            $pairs  = $rx->interactions($rxcui);

                            // brands
                            $merged = $owBrands
                                ? collect($brands)
                                : collect($drug->brand_names ?? [])->merge($brands)->unique()->values();

                            $drug->update([
                                'rxcui'            => $rxcui,
                                'brand_names'      => $merged->all() ?: null,
                                'last_reviewed_at' => now(),
                            ]);

                            // interactions
                            if ($owIx) {
                                $drug->interactions()->delete();
                            }
                            if (!empty($pairs)) {
                                $insert = collect($pairs)->map(fn($p)=>[
                                    'drug_id'        => $drug->id,
                                    'interacts_with' => $p['with'],
                                    'severity'       => $p['severity'],
                                    'mechanism'      => $p['mechanism'],
                                    'management'     => $p['management'],
                                    'created_at'     => now(),
                                    'updated_at'     => now(),
                                ])->all();
                                if ($insert) DrugInteraction::insert($insert);
                            }

                            $stats['enriched']++;
                        }
                    }
                });

            } catch (\Throwable $e) {
                $stats['errors']++;
                $this->line(""); // keep progress bar neat
                $this->warn("Row error (generic={$generic}): ".$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line("\n");

        // Summary
        $this->table(
            ['Processed','Created','Updated','Enriched','Skipped','Errors','Dry-run'],
            [[
                $stats['processed'], $stats['created'], $stats['updated'],
                $stats['enriched'], $stats['skipped'], $stats['errors'],
                $dry ? 'yes' : 'no'
            ]]
        );

        return $stats['errors'] > 0 ? 1 : 0;
    }

    // --- helpers ---

    private function normalizeRow($row): array
    {
        if (!is_array($row)) return [];
        // Remove BOM from first cell if present
        if (isset($row[0])) $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$row[0]);
        return array_map(fn($v)=> is_string($v) ? trim($v) : $v, $row);
    }

    private function anyFilled(array $row, array $keys): bool
    {
        foreach ($keys as $k) {
            if (isset($row[$k]) && trim((string)$row[$k]) !== '') return true;
        }
        return false;
    }

    private function nullable($v)
    {
        $v = is_string($v) ? trim($v) : $v;
        return $v === '' ? null : $v;
    }

    private function csvToArray($v, string $sep = ','): ?array
    {
        if (!isset($v)) return null;
        $v = trim((string)$v);
        if ($v === '') return null;
        $parts = array_filter(array_map('trim', explode($sep, $v)), fn($x)=>$x!=='');
        return empty($parts) ? null : array_values(array_unique($parts));
    }

    /**
     * Group columns of pattern prefix + field + '_' + index
     * Example: dose_population_1, dose_route_1, dose_text_1
     * @param array $row
     * @param string $prefix e.g. 'dose_' or 'ix_'
     * @param array $fields map or list (if map, you can rename: ['text'=>'dose_text'])
     * @return array list of associative groups
     */
    private function groupedColumns(array $row, string $prefix, array $fields): array
    {
        // find max index by scanning keys
        $max = 0;
        foreach ($row as $k => $_) {
            if (strpos($k, $prefix) !== 0) continue;
            if (preg_match('/_(\d+)$/', $k, $m)) {
                $max = max($max, (int)$m[1]);
            }
        }
        if ($max === 0) return [];

        $groups = [];
        for ($i=1; $i<=$max; $i++) {
            $g = [];
            foreach ($fields as $k => $col) {
                // allow rename via assoc mapping
                if (is_int($k)) { $k = $col; }
                $key = $prefix . $col . '_' . $i;
                $g[$k] = isset($row[$key]) ? trim((string)$row[$key]) : null;
            }
            // skip if all empty
            if (count(array_filter($g, fn($x)=>$x!==null && $x!=='')) === 0) continue;
            $groups[] = $g;
        }
        return $groups;
    }
}
