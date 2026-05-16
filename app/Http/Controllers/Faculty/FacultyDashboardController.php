<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;

class FacultyDashboardController extends Controller
{
    /**
     * Display the faculty dashboard.
     */
    public function index(Request $request)
    {
        $faculty = auth('faculty')->user();
        $facultyId = $faculty->id;

        // Base query: only the patients assigned/owned by this CI.
        $baseQuery = Patient::query()
            ->where('faculty_id', $facultyId);

        // 1. Count all patients handled by this CI (any status)
        $totalPatientsInChartings = (clone $baseQuery)->count();

        // 2. Active patients
        $activePatientsCount = (clone $baseQuery)
            ->where('status', 'Active')      // adjust if you use lowercase
            ->count();

        // 3. Discharged patients
        $dischargedPatientsCount = (clone $baseQuery)
            ->where('status', 'Discharged')  // adjust if lowercase
            ->count();

        return view('faculty.dashboard', [
            'totalPatientsInChartings' => $totalPatientsInChartings,
            'activePatientsCount'      => $activePatientsCount,
            'dischargedPatientsCount'  => $dischargedPatientsCount,
        ]);
    }
}
