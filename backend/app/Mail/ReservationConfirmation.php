<?php

namespace App\Mail;

use App\Models\Reservation;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email envoyé au client juste après la création d'une réservation : récapitulatif
 * de la réservation et billet généré au format PDF (à présenter à l'entrée de
 * l'événement), joint automatiquement en pièce jointe.
 */
class ReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Confirmation de votre réservation — {$this->reservation->event->titre}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-confirmation',
            with: ['reservation' => $this->reservation],
        );
    }

    /**
     * Génère le billet PDF à la volée (via DomPDF) et le joint au mail.
     * Chaque billet contient son QR code encodé en SVG base64.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $this->reservation->load('tickets');

        $qrCodes = [];

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:4200'), '/');

        foreach ($this->reservation->tickets as $ticket) {
            $url = "{$frontendUrl}/tickets/verify/{$ticket->code}";
            $svg = $writer->writeString($url);
            $qrCodes[$ticket->code] = base64_encode($svg);
        }

        $pdf = Pdf::loadView('tickets.pdf', [
            'reservation' => $this->reservation,
            'qrCodes'     => $qrCodes,
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "billet-reservation-{$this->reservation->id}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
