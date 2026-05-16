<?php
// app/Http/Controllers/Admin/FacultyApprovalsPageController.php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Notifications\FacultyStatusChanged;

class FacultyApprovalsPageController extends Controller
{
    public function index(Request $r)
    {
        $q      = trim($r->get('q', ''));
        $status = $r->get('status', 'pending'); // default to pending

        $rows = DB::table('faculty')
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('full_name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('faculty_id', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn ($qq) => $qq->where('status', $status))
            ->orderByRaw("FIELD(status,'pending','rejected','approved')")
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.faculty-approvals', compact('rows', 'q', 'status'));
    }

    public function approve($id)
    {
        $fac = DB::table('faculty')->where('id', $id)->first();

        if (! $fac) {
            return back()->with('ok', 'Record not found.');
        }

        if (($fac->status ?? '') === 'approved') {
            return back()->with('ok', 'Already approved.');
        }

        DB::table('faculty')->where('id', $id)->update([
            'status'     => 'approved',
            'updated_at' => now(),
        ]);

        if (! empty($fac->email)) {
            Notification::route('mail', $fac->email)
                ->notify(new FacultyStatusChanged('approved'));
        }

        return back()->with('ok', 'Faculty approved'.(!empty($fac->email) ? ' and notified.' : '.'));
    }

    public function reject($id, Request $request)
    {
        $fac = DB::table('faculty')->where('id', $id)->first();

        if (! $fac) {
            return back()->with('ok', 'Record not found.');
        }

        if (($fac->status ?? '') === 'rejected') {
            return back()->with('ok', 'Already rejected.');
        }

        // Optional: accept a short note/reason from a modal or form
        $note = trim((string) $request->input('note', ''));

        DB::table('faculty')->where('id', $id)->update([
            'status'     => 'rejected',
            'updated_at' => now(),
        ]);

        if (! empty($fac->email)) {
            Notification::route('mail', $fac->email)
                ->notify(new FacultyStatusChanged('rejected', $note ?: null));
        }

        return back()->with('ok', 'Faculty rejected'.(!empty($fac->email) ? ' and notified.' : '.'));
    }
}
