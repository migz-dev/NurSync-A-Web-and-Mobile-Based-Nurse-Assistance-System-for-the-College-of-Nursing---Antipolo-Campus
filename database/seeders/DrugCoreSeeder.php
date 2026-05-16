<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DrugCoreSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $drugs = [
            [
                'generic_name' => 'Amoxicillin',
                'brand_names'  => json_encode(['Amoxil', 'Himox']),
                'atc_class'    => 'Penicillin antibiotic',
                'pregnancy_category' => 'B',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Cefalexin',
                'brand_names'  => json_encode(['Keflex', 'Rancef']),
                'atc_class'    => 'Cephalosporin antibiotic',
                'pregnancy_category' => 'B',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Paracetamol',
                'brand_names'  => json_encode(['Biogesic', 'Tempra']),
                'atc_class'    => 'Analgesic/antipyretic',
                'pregnancy_category' => 'B',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Ibuprofen',
                'brand_names'  => json_encode(['Advil', 'Medicol']),
                'atc_class'    => 'NSAID',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Metoprolol',
                'brand_names'  => json_encode(['Betaloc', 'Lopressor']),
                'atc_class'    => 'Beta-blocker',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Amlodipine',
                'brand_names'  => json_encode(['Norvasc']),
                'atc_class'    => 'Calcium channel blocker',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Losartan',
                'brand_names'  => json_encode(['Cozaar']),
                'atc_class'    => 'Angiotensin II receptor blocker',
                'pregnancy_category' => 'D',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Furosemide',
                'brand_names'  => json_encode(['Lasix']),
                'atc_class'    => 'Loop diuretic',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Omeprazole',
                'brand_names'  => json_encode(['Losec', 'Omez']),
                'atc_class'    => 'Proton pump inhibitor',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Metformin',
                'brand_names'  => json_encode(['Glucophage']),
                'atc_class'    => 'Biguanide antidiabetic',
                'pregnancy_category' => 'B',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Insulin Regular',
                'brand_names'  => json_encode(['Humulin R']),
                'atc_class'    => 'Short-acting insulin',
                'pregnancy_category' => 'B',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Diazepam',
                'brand_names'  => json_encode(['Valium']),
                'atc_class'    => 'Benzodiazepine',
                'pregnancy_category' => 'D',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Morphine Sulfate',
                'brand_names'  => json_encode(['Morphine', 'Duramorph']),
                'atc_class'    => 'Opioid analgesic',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Salbutamol',
                'brand_names'  => json_encode(['Ventolin']),
                'atc_class'    => 'Beta-2 agonist bronchodilator',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Prednisone',
                'brand_names'  => json_encode(['Deltasone']),
                'atc_class'    => 'Corticosteroid',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Captopril',
                'brand_names'  => json_encode(['Capoten']),
                'atc_class'    => 'ACE inhibitor',
                'pregnancy_category' => 'D',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Digoxin',
                'brand_names'  => json_encode(['Lanoxin']),
                'atc_class'    => 'Cardiac glycoside',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Enoxaparin',
                'brand_names'  => json_encode(['Clexane']),
                'atc_class'    => 'Low-molecular-weight heparin',
                'pregnancy_category' => 'B',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Ciprofloxacin',
                'brand_names'  => json_encode(['Ciprox']),
                'atc_class'    => 'Fluoroquinolone antibiotic',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
            [
                'generic_name' => 'Hydrocortisone',
                'brand_names'  => json_encode(['Solu-Cortef']),
                'atc_class'    => 'Glucocorticoid',
                'pregnancy_category' => 'C',
                'is_core' => 1,
                'last_reviewed_at' => $now,
            ],
        ];

        DB::table('drugs')->insert($drugs);

        // --- Attach monographs ---
        $drugRows = DB::table('drugs')->get();
        foreach ($drugRows as $d) {
            DB::table('drug_monographs')->insert([
                'drug_id' => $d->id,
                'indications' => "Refer to standard nursing references for {$d->generic_name}.",
                'contraindications' => "Known hypersensitivity or specific contraindications for {$d->generic_name}.",
                'adverse_effects' => 'May include nausea, vomiting, dizziness, or rash.',
                'nursing_responsibilities' => 'Monitor vital signs, assess for side effects, educate patient regarding adherence.',
                'patient_teaching' => 'Take as prescribed, report unusual symptoms, avoid abrupt discontinuation unless advised.',
                'monitoring_params' => 'Observe therapeutic effect and possible adverse reactions.',
                'references' => json_encode(['Davis’s Drug Guide for Nurses', 'OpenFDA label']),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // --- Sample Doses ---
        $doseSamples = [
            ['PO', 'adult', 'As prescribed; typically standard adult dosing.'],
            ['PO', 'pediatric', 'Weight-based dosing per protocol.'],
        ];

        foreach ($drugRows as $d) {
            foreach ($doseSamples as [$route, $pop, $text]) {
                DB::table('drug_doses')->insert([
                    'drug_id' => $d->id,
                    'population' => $pop,
                    'route' => $route,
                    'form' => 'tablet',
                    'dose_text' => $text,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // --- Sample Interactions (generic examples) ---
        $interactions = [
            ['Amoxicillin', 'Warfarin', 'moderate', 'May increase INR', 'Monitor PT/INR closely'],
            ['Metoprolol', 'Verapamil', 'major', 'Additive bradycardia', 'Avoid combination or monitor ECG'],
            ['Ibuprofen', 'Aspirin', 'moderate', 'Competition at platelet binding sites', 'Avoid prolonged combination'],
        ];

        foreach ($interactions as [$drugName, $with, $severity, $mech, $manage]) {
            $drug = DB::table('drugs')->where('generic_name', $drugName)->first();
            if ($drug) {
                DB::table('drug_interactions')->insert([
                    'drug_id' => $drug->id,
                    'interacts_with' => $with,
                    'severity' => $severity,
                    'mechanism' => $mech,
                    'management' => $manage,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command->info('✅ DrugCoreSeeder: inserted 20 core drugs with sample monographs, doses, and interactions.');
    }
}
