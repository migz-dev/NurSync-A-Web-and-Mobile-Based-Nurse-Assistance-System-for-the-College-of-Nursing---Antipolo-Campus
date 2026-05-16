<?php

namespace App\Policies;

use App\Models\Faculty;
use App\Models\ClinicalExperience;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClinicalExperiencePolicy
{
    use HandlesAuthorization;

    /**
     * Any logged-in faculty can see the list.
     */
    public function viewAny(Faculty $user): bool
    {
        return true;
    }

    /**
     * View a specific experience – only owner.
     */
    public function view(Faculty $user, ClinicalExperience $experience): bool
    {
        return $experience->faculty_id === $user->id;
    }

    /**
     * Any faculty can create their own story.
     */
    public function create(Faculty $user): bool
    {
        return true;
    }

    /**
     * Update – only the CI who owns the record.
     */
    public function update(Faculty $user, ClinicalExperience $experience): bool
    {
        return $experience->faculty_id === $user->id;
    }

    /**
     * Archive / delete – owner only.
     */
    public function delete(Faculty $user, ClinicalExperience $experience): bool
    {
        return $experience->faculty_id === $user->id;
    }

    public function restore(Faculty $user, ClinicalExperience $experience): bool
    {
        return false;
    }

    public function forceDelete(Faculty $user, ClinicalExperience $experience): bool
    {
        return false;
    }
}
