<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Faculty;
use App\Models\Admin;
use App\Models\Procedure;
use App\Models\EquipmentGuide;
use App\Models\DrugProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalStudents = Cache::remember('dash.total_students', 300, fn() => Student::count());
        $totalFaculty  = Cache::remember('dash.total_faculty', 300, fn() => Faculty::count());
        $totalAdmins   = Cache::remember('dash.total_admins', 300, fn() => Admin::count());
        $pendingApprovals = Cache::remember('dash.pending_faculty', 300, fn() =>
            Faculty::where('status', 'pending')->count()
        );

        // ✅ NEW
        $totalProcedures = Cache::remember('dash.total_procedures', 300, fn() => Procedure::count());
        $totalEquipment  = Cache::remember('dash.total_equipment', 300, fn() => EquipmentGuide::count());
        $totalDrugs      = Cache::remember('dash.total_drugs', 300, fn() => DrugProduct::count());

        return view('admin.admin-dashboard', compact(
            'totalStudents',
            'totalFaculty',
            'totalAdmins',
            'pendingApprovals',
            'totalProcedures',
            'totalEquipment',
            'totalDrugs'
        ));
    }
}
