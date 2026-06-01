<?php

namespace App\Mail;

use App\Models\PatsSoporteContacto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SoporteContactoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly PatsSoporteContacto $contacto,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nuevo mensaje de soporte · {$this->contacto->nombre} · PATS",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.soporte_contacto',
        );
    }
}
