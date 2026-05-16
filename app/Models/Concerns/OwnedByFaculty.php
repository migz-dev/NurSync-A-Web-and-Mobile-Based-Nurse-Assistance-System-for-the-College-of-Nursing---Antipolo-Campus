<?php

namespace App\Models\Concerns;

trait OwnedByFaculty
{
    public function scopeOwned($q, $facultyId = null)
    {
        return $q->where('faculty_id', $facultyId ?? auth('faculty')->id());
    }
}
