<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\NursingRoadmap;

class NursingRoadmapSeeder extends Seeder
{
    public function run(): void
    {
$path = database_path('seeders/data/Nursing_Career_Roadmap.csv');

if (!file_exists($path)) {
    $this->command->error("CSV not found at {$path}");
    return;
}

$file = fopen($path, 'r');
fgetcsv($file); // Skip header

$count = 0;
while (($row = fgetcsv($file)) !== false) {
    [$level, $category, $role, $description, $requirements, $steps] = $row;

    \App\Models\NursingRoadmap::updateOrCreate(
        ['category' => $category, 'role' => $role],
        [
            'career_level' => (int) $level,
            'description'  => $description ?: null,
            'requirements' => $requirements ?: null,
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

$this->command->info("✅ Imported {$count} nursing roadmap records successfully!");

    }
}
