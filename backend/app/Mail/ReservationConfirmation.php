<?php

namespace App\Mail;

use App\Models\Reservation;
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
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('tickets.pdf', ['reservation' => $this->reservation]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "billet-reservation-{$this->reservation->id}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
