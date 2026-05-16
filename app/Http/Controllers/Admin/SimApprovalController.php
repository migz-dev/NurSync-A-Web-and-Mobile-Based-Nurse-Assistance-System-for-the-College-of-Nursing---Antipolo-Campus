<?php
// app/Http/Controllers/Admin/SimApprovalController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sim\ApprovalDecisionRequest;
use App\Models\SimCase;
use App\Services\Sim\ApprovalService;
use Illuminate\Http\Request;

class SimApprovalController extends Controller
{
    /**
     * List all cases pending approval (filterable).
     * Filters:
     *  - q:        title/summary LIKE
     *  - faculty_id: exact faculty owner
     */
    public function pending(Request $req)
    {
        $q        = trim((string) $req->query('q', ''));
        $facultyId = (int) $req->query('faculty_id', 0);
        $perPage  = (int) $req->integer('per_page', 20);

        $cases = SimCase::query()
            ->where('status', 'pending_approval')
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($x) use ($q) {
                    $x->where('title', 'like', "%{$q}%")
                      ->orWhere('summary', 'like', "%{$q}%");
                });
            })
            ->when($facultyId > 0, fn ($w) => $w->where('faculty_id', $facultyId))
            ->with(['faculty:id,full_name']) // if you have Faculty relation
            ->select('id','faculty_id','title','status','submitted_for_approval_at','updated_at')
            ->latest('submitted_for_approval_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.simulations.pending-approvals', compact('cases','q','facultyId'));
    }

    /**
     * Inspect one case before deciding.
     * Only allowed when status = pending_approval.
     */
    public function show(SimCase $case)
    {
        if ($case->status !== 'pending_approval') {
            return redirect()
                ->route('admin.sim.pending')
                ->with('error', 'Only cases pending approval can be reviewed.');
        }

        // Eager-load all components the admin needs to review
        $case->load([
            'faculty:id,full_name',
            'objectives:id,case_id,text,`order`',
            'tasks:id,case_id,title,required_fields_json,`order`',
            'orders:id,case_id,order_text,order_by,order_at',
            'vitalsTemplate:id,case_id,baseline_vitals_json,cue_schedule_json',
        ]);

        return view('admin.simulations.show', compact('case'));
    }

    /**
     * Approve a case (makes it LIVE).
     * Delegates to ApprovalService which handles status flips, timestamps, events, and audit logs.
     */
    public function approve(SimCase $case, ApprovalService $svc)
    {
        if ($case->status !== 'pending_approval') {
            return back()->with('error', 'This case is not pending approval.');
        }

        $svc->approve($case, auth('admin')->id());

        return back()->with('ok', 'Case approved and set LIVE.');
    }

    /**
     * Reject a case with a note; returns it to DRAFT.
     */
    public function reject(SimCase $case, ApprovalDecisionRequest $request, ApprovalService $svc)
    {
        if ($case->status !== 'pending_approval') {
            return back()->with('error', 'This case is not pending approval.');
        }

        $svc->reject($case, auth('admin')->id(), $request->validated()['note']);

        return back()->with('ok', 'Case rejected and returned to Draft with note.');
    }
}