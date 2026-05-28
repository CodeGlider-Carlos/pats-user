<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;

    public function __construct(
        public readonly string $nombre,
        string $token,
        string $correo,
    ) {
        $this->resetUrl = route('password.reset', ['token' => $token])
            . '?email=' . urlencode($correo);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Restablecer contraseña · PATS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset_password',
        );
    }
}
