<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Services\BrevoMailService;

class ResetPasswordBrevo extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail']; // 👈 usa canal padrão
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        $html = view('emails.reset-password', [
            'url' => $url,
            'user' => $notifiable
        ])->render();

        // envia via Brevo manualmente
        app(BrevoMailService::class)->send(
            $notifiable->email,
            $notifiable->name ?? '',
            'Recuperação de senha',
            $html
        );

        // retornamos um MailMessage vazio só para satisfazer o Laravel
        return (new MailMessage)->subject(' ');
    }
}
