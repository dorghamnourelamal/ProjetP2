<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email transmis au propriétaire du site (eventify439@gmail.com) lorsqu'un visiteur
 * utilise le formulaire "Support par email" de la page d'accueil. Le champ "reply-to"
 * est positionné sur l'adresse du client pour permettre une réponse directe.
 */
class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $message,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouveau message de contact — {$this->name}",
            replyTo: [$this->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'messageBody' => $this->message,
            ],
        );
    }
}
