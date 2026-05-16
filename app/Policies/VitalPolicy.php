<?php

namespace App\Policies;

use App\Models\Vital;
use App\Models\Faculty;

class VitalPolicy
{
    public function viewAny(Faculty $user): bool { return true; }
    public function view(Faculty $user, Vital $vital): bool { return $vital->faculty_id === $user->id; }
    public function create(Faculty $user): bool { return true; }
    public function update(Faculty $user, Vital $vital): bool { return $vital->faculty_id === $user->id; }
    public function delete(Faculty $user, Vital $vital): bool { return $vital->faculty_id === $user->id; }
    public function restore(Faculty $user, Vital $vital): bool { return $vital->faculty_id === $user->id; }
    public function forceDelete(Faculty $user, Vital $vital): bool { return false; }
}
