<?php

namespace App\Policies;

use App\Models\{Faculty, SimPatient};
use Illuminate\Auth\Access\HandlesAuthorization;

class SimPatientPolicy
{
    use HandlesAuthorization;

    public function viewAny(Faculty $user): bool { return true; }

    public function view(Faculty $user, SimPatient $patient): bool
    {
        return (int) $patient->faculty_id === (int) $user->id;
    }

    public function create(Faculty $user): bool { return true; }

    public function update(Faculty $user, SimPatient $patient): bool
    {
        return (int) $patient->faculty_id === (int) $user->id;
    }

    public function delete(Faculty $user, SimPatient $patient): bool
    {
        return (int) $patient->faculty_id === (int) $user->id;
    }
}