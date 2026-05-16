<?php

namespace App\Observers;

use App\Models\Faculty;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FacultyStatusChanged;

class FacultyObserver
{
    /**
     * Fire on new record creation.
     * Tip: If you already send the "pending" email in your register controller,
     * you can comment this out to avoid duplicate notifications.
     */
    public function created(Faculty $faculty): void
    {
        $status = strtolower($faculty->status ?? 'pending');

        if ($status === 'pending' && !empty($faculty->email)) {
            Notification::route('mail', $faculty->email)
                ->notify(new FacultyStatusChanged('pending'));
        }
    }

    /**
     * Fire on updates; if the status changed, notify accordingly.
     */
    public function updated(Faculty $faculty): void
    {
        if (! $faculty->wasChanged('status')) {
            return;
        }

        $status = strtolower($faculty->status ?? 'pending');
        if (empty($faculty->email)) {
            return;
        }

        // Optional: if you store a reason on rejection, try to include it.
        // Change 'rejection_note' to whatever column you use (or leave null).
        $note = $status === 'rejected'
            ? ($faculty->rejection_note ?? null)
            : null;

        Notification::route('mail', $faculty->email)
            ->notify(new FacultyStatusChanged($status, $note));
    }
}
