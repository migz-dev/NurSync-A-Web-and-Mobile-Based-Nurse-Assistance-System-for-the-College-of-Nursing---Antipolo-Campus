<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     */
    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * The channels the notification will be sent through.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation using our custom template.
     */
    public function toMail($notifiable): MailMessage
    {
        // Generate the reset URL
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Your Password · NurSync')
            ->view('emails.password-reset', [
                'url'   => $resetUrl,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
    }

    /**
     * Array representation (unused).
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
