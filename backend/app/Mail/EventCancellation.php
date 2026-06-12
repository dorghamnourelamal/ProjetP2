<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email d'annulation envoyé à chaque participant d'un événement supprimé.
 */
class EventCancellation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public Reservation $reservation,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "❌ Annulation de l'événement — {$this->event->titre}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-cancellation',
            with: [
                'event'       => $this->event,
                'reservation' => $this->reservation,
            ],
        );
    }
}
