<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenovacionAvisoMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $loginUrl;

    public function __construct(
        public readonly string $nombre,
        public readonly string $estado,
        public readonly ?string $fechaVencimiento,
        public readonly int $diasRestantes,
        public readonly int $mesesVencidos,
        public readonly float $recargoAcumulado,
    ) {
        $this->loginUrl = route('login');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->estado === 'vencido'
                ? 'Tu pasaporte PATS está vencido · Renueva ahora'
                : 'Tu pasaporte PATS está por vencer',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.renovacion_aviso',
        );
    }
}
