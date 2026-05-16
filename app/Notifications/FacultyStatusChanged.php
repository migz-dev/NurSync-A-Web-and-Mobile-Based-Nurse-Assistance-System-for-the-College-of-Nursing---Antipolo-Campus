<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // remove this + interface if you won't queue
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FacultyStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param string      $status 'pending' | 'approved' | 'rejected'
     * @param string|null $note   optional reason (shown when rejected)
     */
    public function __construct(
        public string $status,
        public ?string $note = null
    ) {
        $this->status = strtolower($this->status);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail message.
     */
// app/Notifications/FacultyStatusChanged.php
public function toMail(object $notifiable): MailMessage
{
    $status = $this->status;

    $subject = match ($status) {
        'approved' => 'Your Faculty Account Has Been Approved',
        'rejected' => 'Your Faculty Application Result',
        default     => 'Your Faculty Account Is Pending Review',
    };

    $intro = match ($status) {
        'approved' => 'Good news! Your faculty account has been approved.',
        'rejected' => 'We’re sorry to inform you that your faculty application was not approved.',
        default     => 'Thanks for registering. Your faculty account is now pending admin review.',
    };

    $next = match ($status) {
        'approved' => 'You can now sign in to your dashboard.',
        'rejected' => $this->note
            ? "Reason: {$this->note}"
            : 'If you believe this was an error, please contact the administrator.',
        default     => 'You’ll receive another email once a decision has been made.',
    };

    $actionText = $status === 'approved' ? 'Sign in' : 'Open NurSync';
    $actionUrl  = $status === 'approved' ? url('/faculty/login') : url('/');

    return (new MailMessage)
        ->subject($subject)
        ->markdown('emails.faculty.status', [
            'status'     => $status,
            'intro'      => $intro,
            'next'       => $next,
            'actionText' => $actionText,
            'actionUrl'  => $actionUrl,
        ]);
}

}
