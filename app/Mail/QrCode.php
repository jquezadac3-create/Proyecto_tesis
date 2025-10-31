<?php

namespace App\Mail;

use App\Models\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QrCode extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected $cliente,
        protected $numero_factura,
        protected $qr_code
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Gracias por la compra!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.qr-code',
            with: [
                'cliente' => $this->cliente,
                'numero_factura' => $this->numero_factura,
                'qr_code' => $this->qr_code,
                'config' => $this->getStoredConfig(),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->decodeQrCode(), 'qr_code.png')->withMime('image/png')->as("qr_{$this->numero_factura}.png")
        ];
    }

    private function getStoredConfig()
    {
        return Config::first();
    }

    private function decodeQrCode()
    {
        return base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->qr_code));
    }
}
