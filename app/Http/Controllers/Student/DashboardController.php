<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SkillMasteryChecklist;
use App\Models\Procedure;
use App\Models\EmergencyProtocol;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Logged-in student (default web guard)
        $student = Auth::user();   // 🔁 changed from Auth::guard('student')->user();

        // === COUNTS FOR THE DASHBOARD ===

        // Published skill mastery checklists
        $skillsCount = SkillMasteryChecklist::query()
            ->where('status', 'published')
            ->count();

        // Published procedure guides
        $proceduresCount = Procedure::query()
            ->where('status', 'published')
            ->count();

        // Published emergency protocols
        $emergencyCount = EmergencyProtocol::query()
            ->where('status', 'published')
            ->count();

        // Optional: Ward Orientation, only if the model exists
        $wardOrientationCount = 0;
        if (class_exists('\App\Models\WardOrientation')) {
            $wardOrientationCount = \App\Models\WardOrientation::query()
                ->where('status', 'published')
                ->count();
        }

        // How many different learning modules are actually available
        $modulesExplored = 0;
        if ($skillsCount > 0) $modulesExplored++;
        if ($proceduresCount > 0) $modulesExplored++;
        if ($emergencyCount > 0) $modulesExplored++;
        if ($wardOrientationCount > 0) $modulesExplored++;

        $stats = [
            'modulesExplored'    => $modulesExplored,
            'skillsAvailable'    => $skillsCount,
            'emergencyAvailable' => $emergencyCount,
        ];

        return view('student.dashboard.index', [
            'student' => $student,
            'stats'   => $stats,
        ]);
    }
}
