<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudDistribucionRecibida extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $nombreSolicitante,
        public readonly string $referencia,
        public readonly string $correo,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Solicitud recibida · {$this->referencia} · PATS",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud_distribucion_recibida',
        );
    }
}