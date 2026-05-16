<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmergencyProtocol;

class EmergencyProtocolController extends Controller
{
    /**
     * Student index – list published (non-archived) protocols.
     * View-only, same filters + UI as CI but no authoring actions.
     */
    public function index(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $severity = (string) $request->get('severity', '');
        $category = (string) $request->get('category', '');
        $status   = (string) $request->get('status', ''); // optional future use
        $ward     = (string) $request->get('ward', '');

        $perPage  = (int) $request->get('per', 12);
        if ($perPage < 6 || $perPage > 60) {
            $perPage = 12;
        }

        $query = EmergencyProtocol::query();

        // For students: show only published + not archived
        $query->where('status', 'published');

        // Optional: ignore faculty scoping so students see both admin-made
        // and CI-made protocols (common library).
        // If you ever want to hide drafts or faculty-private protocols,
        // you can further constrain this here.

        if ($q !== '') {
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder
                    ->where('title', 'like', '%' . $q . '%')
                    ->orWhere('summary', 'like', '%' . $q . '%')
                    ->orWhere('category', 'like', '%' . $q . '%')
                    ->orWhere('ward', 'like', '%' . $q . '%');
            });
        }

        if ($severity !== '') {
            $query->where('severity', $severity);
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        if ($ward !== '') {
            $query->where('ward', $ward);
        }

        // No archived toggle for students for now – they only see active published
        $protocols = $query
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Filter dropdown data (from all published protocols)
        $categories = EmergencyProtocol::published()
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $severities = ['Critical', 'Moderate', 'Mild'];

        $wards = [
            'Community Health (CHN)',
            'OB Ward',
            'Delivery Room (DR)',
            'Nursery',
            'Pediatrics (PEDIA)',
            'Medical-Surgical (MS)',
            'ICU',
            'Oncology',
            'Isolation Unit',
            'Endocrine Unit',
            'Neurology Unit',
            'Psychiatric (PSYCH)',
            'Emergency Room (ER)',
            'Operating Room (OR)',
            'Trauma Unit',
            'Disaster Response / Community Field',
        ];

        return view('student.emergency.index', [
            'protocols'  => $protocols,
            'categories' => $categories,
            'severities' => $severities,
            'wards'      => $wards,
            'filters'    => [
                'q'        => $q,
                'severity' => $severity,
                'category' => $category,
                'status'   => $status,
                'ward'     => $ward,
                'per'      => $perPage,
            ],
        ]);
    }

    /**
     * Student show – view a single published protocol.
     */
    public function show(string $slug)
    {
        $protocol = EmergencyProtocol::with(['steps' => function ($q) {
                $q->orderBy('step_no');
            }, 'tags'])
            ->where('slug', $slug)
            ->where('status', 'published') // students can only open published
            ->firstOrFail();

        // Increment view count (same as CI)
        $protocol->increment('view_count');

        return view('student.emergency.show', [
            'protocol' => $protocol,
        ]);
    }
}
