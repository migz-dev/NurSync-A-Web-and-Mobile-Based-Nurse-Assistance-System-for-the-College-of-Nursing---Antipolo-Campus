<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SkillMasteryChecklist;
use App\Models\Procedure;
use App\Models\EmergencyProtocol;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();

        // Global counts (you can later switch to per-student analytics table)
        $skillsCount = SkillMasteryChecklist::query()
            ->where('status', 'published')
            ->count();

        $proceduresCount = Procedure::query()
            ->where('status', 'published')
            ->count();

        $emergencyCount = EmergencyProtocol::query()
            ->where('status', 'published')
            ->count();

        // Optional Ward Orientation module (only if model exists)
        $wardOrientationCount = 0;
        if (class_exists(\App\Models\WardOrientation::class)) {
            $wardOrientationCount = \App\Models\WardOrientation::query()
                ->where('status', 'published')
                ->count();
        }

        // How many core learning modules are *actually available* right now
        $modulesExplored = 0;
        if ($skillsCount > 0) $modulesExplored++;
        if ($proceduresCount > 0) $modulesExplored++;
        if ($emergencyCount > 0) $modulesExplored++;
        if ($wardOrientationCount > 0) $modulesExplored++;

        // If for some reason all are zero (fresh DB), fall back to 0 not null
        $stats = [
            'modulesExplored' => $modulesExplored,
            'skillsAvailable' => $skillsCount,
            'emergencyAvailable' => $emergencyCount,
        ];

        return view('student.dashboard', compact('student', 'stats'));
    }
}
