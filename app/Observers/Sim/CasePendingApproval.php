<?php

namespace App\Notifications\Sim;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CasePendingApproval extends Notification
{
    use Queueable;

    public function __construct(public int $caseId, public string $title, public int $facultyId) {}

    public function via($notifiable) { return ['database']; }

    public function toArray($notifiable)
    {
        return ['type'=>'case.pending','case_id'=>$this->caseId,'title'=>$this->title,'faculty_id'=>$this->facultyId];
    }
}