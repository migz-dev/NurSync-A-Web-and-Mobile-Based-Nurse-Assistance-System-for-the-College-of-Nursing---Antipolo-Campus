<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NursingRoadmap;

class NursingProfessionsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/All_Nursing_Professions_With_Steps.csv');

        if (!file_exists($path)) {
            $this->command->error("❌ CSV not found at {$path}");
            return;
        }

        $file = fopen($path, 'r');
        fgetcsv($file); // Skip header row

        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            [$category, $level, $profession, $description, $steps] = $row;

            NursingRoadmap::updateOrCreate(
                ['category' => $category, 'role' => $profession],
                [
                    'career_level' => (int) $level,
                    'description'  => $description ?: null,
                    'requirements' => null, // left null, not in CSV
                    'steps_text'   => $steps ?: null,
                    'steps_json'   => json_encode(
                        array_values(array_filter(array_map('trim', explode('→', $steps)))),
                        JSON_UNESCAPED_UNICODE
                    ),
                ]
            );

            $count++;
        }

        fclose($file);
        $this->command->info("✅ Imported {$count} nursing profession records successfully!");
    }
}
