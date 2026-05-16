<?php

namespace App\Policies;

use App\Models\Sim\SimAssignment;

class SimAssignmentPolicy
{
    public function viewAny($user): bool { return (bool) $user; }

    public function view($user, SimAssignment $a): bool
    {
        return (int) $user->id === (int) $a->faculty_id;
    }

    public function create($user): bool { return (bool) $user; }

    public function update($user, SimAssignment $a): bool
    {
        return (int) $user->id === (int) $a->faculty_id;
    }

    public function attachStudents($user, SimAssignment $a): bool
    {
        return (int) $user->id === (int) $a->faculty_id;
    }
}
