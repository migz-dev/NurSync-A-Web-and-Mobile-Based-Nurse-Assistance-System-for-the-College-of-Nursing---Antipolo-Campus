<?php

namespace App\Policies;

use App\Models\{Faculty, SimCase};
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class SimCasePolicy
{
    use HandlesAuthorization;

    /** Faculty can list their own cases. */
    public function viewAny(Faculty $user): bool
    {
        return true; // already gated by auth:faculty + faculty.approved
    }

    /** Faculty can view their own case. */
    public function view(Faculty $user, SimCase $case): bool
    {
        return (int) $case->faculty_id === (int) $user->id;
    }

    /** Faculty can create cases. */
    public function create(Faculty $user): bool
    {
        return true;
    }

    /** Faculty can edit only Draft/Rejected cases they own. */
    public function update(Faculty $user, SimCase $case): Response|bool
    {
        if ((int) $case->faculty_id !== (int) $user->id) {
            return false;
        }
        return in_array($case->status, ['draft','rejected'], true)
            ? Response::allow()
            : Response::deny('Only draft or rejected cases can be edited.');
    }

    /** Submit for approval (only Draft/Rejected and owner). */
    public function submit(Faculty $user, SimCase $case): Response|bool
    {
        if ((int) $case->faculty_id !== (int) $user->id) {
            return false;
        }
        return in_array($case->status, ['draft','rejected'], true)
            ? Response::allow()
            : Response::deny('Only draft or rejected cases can be submitted.');
    }

    /** Delete (only Draft/Rejected and owner). */
    public function delete(Faculty $user, SimCase $case): Response|bool
    {
        if ((int) $case->faculty_id !== (int) $user->id) {
            return false;
        }
        return in_array($case->status, ['draft','rejected'], true)
            ? Response::allow()
            : Response::deny('Only draft or rejected cases can be deleted.');
    }
}