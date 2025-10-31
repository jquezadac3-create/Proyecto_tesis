<?php

namespace App\Mail;

use App\Models\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SriInvoice extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected $pdfPath,
        protected $authorizedXMLPath,
        protected $accessKey,
        protected $numero_Factura,
        protected $cliente,
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura Electrónica',
            tags: ['factura-electronica', 'factura', 'sri', 'invoice']
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            text: 'emails.invoice-plain-text',
            with: [
                'clave_acceso' => $this->accessKey,
                'cliente' => $this->cliente,
                'config' => $this->getStoredConfig(),
                'numero_factura' => $this->numero_Factura
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
            Attachment::fromStorage($this->pdfPath)->withMime('application/pdf')->as("factura_{$this->accessKey}.pdf"),
            Attachment::fromStorage($this->authorizedXMLPath)->withMime('application/xml')->as("{$this->accessKey}.xml"),
            // Attachment::fromData($this->qrData, 'qr_code.png')->withMime('image/png')->as("qr_{$this->accessKey}.png"),
        ];
    }

    public function getStoredConfig()
    {
        return Config::first();
    }
}
