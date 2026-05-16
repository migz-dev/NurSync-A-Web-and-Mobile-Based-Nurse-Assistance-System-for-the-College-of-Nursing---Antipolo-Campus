<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Procedure;

class AdminResourcesPageController extends Controller
{
    public function index(Request $r)
    {
        $q      = trim((string) $r->get('q', ''));
        $status = (string) $r->get('status', '');
        $ward   = (string) $r->get('ward', ''); // NEW: ward filter

        $procedures = Procedure::query()
            // search (title + optional description/hazards to be a bit more helpful)
            ->when($q !== '', function ($qrb) use ($q) {
                $qrb->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                       ->orWhere('description', 'like', "%{$q}%")
                       ->orWhere('hazards_text', 'like', "%{$q}%");
                });
            })
            // status filter
            ->when($status !== '', fn ($qrb) => $qrb->where('status', $status))
            // NEW: ward filter (ignore if empty or 'all')
            ->when($ward !== '' && strtolower($ward) !== 'all', fn ($qrb) => $qrb->where('clinical_wards', $ward))
            // relations used by the rows partial
            ->with(['author','adminCreator'])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        // AJAX: return HTML fragments for JS fetchList()
        if ($r->ajax()) {
            return response()->json([
                'rows'  => view('admin.procedures._rows', compact('procedures'))->render(),
                'pager' => view('admin.procedures._pager', compact('procedures'))->render(),
            ]);
        }

        // First load: render the full page
        return view('admin.admin-resources', compact('procedures'));
    }
}
