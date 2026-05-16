<?php

namespace App\Policies;

use App\Models\Drug;
use App\Models\Faculty; // your faculty guard model

class DrugPolicy
{
    public function viewAny(Faculty $user): bool { return true; }
    public function view(Faculty $user, Drug $drug): bool { return true; }

    public function create(Faculty $user): bool
    {
        return (bool) ($user->is_admin ?? false); // adjust to your role flag
    }

    public function update(Faculty $user, Drug $drug): bool
    {
        return (bool) ($user->is_admin ?? false);
    }

    public function delete(Faculty $user, Drug $drug): bool
    {
        return (bool) ($user->is_admin ?? false);
    }
}
