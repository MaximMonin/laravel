<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;

class EmailVerification extends VerifyEmail
{
    use Queueable;
    protected $checkUrl;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        if ($this->checkUrl) {
          $verificationUrl = $this->checkUrl;
        }
        else {
          $verificationUrl = $this->verificationUrl($notifiable);
          $this->checkUrl = $verificationUrl;
        }

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        // You can change here default Email Verification text on New User Registration
        return (new MailMessage)
            ->subject(Lang::get('Verify Email Address'))
            ->line(Lang::get('Please click the button below to verify your email address.'))
            ->action(Lang::get('Verify Email Address'), $verificationUrl)
            ->line(Lang::get('If you did not create an account, no further action is required.'));
    }

    // Save to database user-id + verification url
    public function toDatabase($notifiable)
    {
        if ($this->checkUrl) {
          $verificationUrl = $this->checkUrl;
        }
        else {
          $verificationUrl = $this->verificationUrl($notifiable);
          $this->checkUrl = $verificationUrl;
        }
        return ['verifylink' => $verificationUrl];
    }
}
