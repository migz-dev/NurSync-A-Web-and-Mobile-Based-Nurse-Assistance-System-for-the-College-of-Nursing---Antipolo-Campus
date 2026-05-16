<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\WardOrientation;
use Illuminate\Http\Request;

class WardOrientationController extends Controller
{
    /**
     * List all published ward orientations (read-only).
     */
    public function index(Request $request)
    {
        $orientations = WardOrientation::query()
            ->published()
            ->orderBy('ward_code')
            ->orderBy('title')
            ->get();

        return view('student.ward_orientation.index', [
            'orientations' => $orientations,
        ]);
    }

    /**
     * Show a single ward orientation (only if published).
     */
    public function show(WardOrientation $orientation)
    {
        if ($orientation->status !== WardOrientation::STATUS_PUBLISHED) {
            abort(404);
        }

        return view('student.ward_orientation.show', [
            'orientation' => $orientation,
        ]);
    }
}