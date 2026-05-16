<?php

namespace App\Notifications\Sim;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CaseDecision extends Notification
{
    use Queueable;

    public function __construct(public int $caseId, public string $title, public string $decision, public ?string $note=null) {}

    public function via($notifiable) { return ['database']; }

    public function toArray($notifiable)
    {
        return ['type'=>'case.decision','case_id'=>$this->caseId,'title'=>$this->title,'decision'=>$this->decision,'note'=>$this->note];
    }
}