import { Component, OnInit, computed, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { EventService } from '../../../core/services/event';
import { AuthService } from '../../../core/services/auth';
import { ContactService } from '../../../core/services/contact';
import { Event } from '../../../core/models/event.model';

/**
 * Page d'accueil publique : présente l'application, met en avant
 * quelques événements à venir et oriente le visiteur vers la liste complète,
 * la réservation ou le contact.
 */
@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home implements OnInit {
  private readonly eventsSignal = signal<Event[]>([]);

  readonly loading = signal(true);
  readonly errorMessage = signal<string | null>(null);

  readonly showContactForm = signal(false);
  readonly contactName = signal('');
  readonly contactEmail = signal('');
  readonly contactMessage = signal('');
  readonly contactSending = signal(false);
  readonly contactFeedback = signal<{ type: 'success' | 'error'; text: string } | null>(null);

  /**
   * Met en avant jusqu'à 3 événements à venir, triés par date la plus proche.
   */
  readonly featuredEvents = computed(() => {
    const today = new Date().toISOString().slice(0, 10);

    return [...this.eventsSignal()]
      .filter((event) => event.date_event >= today)
      .sort((a, b) => a.date_event.localeCompare(b.date_event))
      .slice(0, 3);
  });

  constructor(
    private eventService: EventService,
    public auth: AuthService,
    private contactService: ContactService,
  ) {}

  ngOnInit(): void {
    this.loading.set(true);
    this.errorMessage.set(null);

    this.eventService.list().subscribe({
      next: (events) => {
        this.eventsSignal.set(events);
        this.loading.set(false);
      },
      error: () => {
        this.errorMessage.set('Impossible de charger les événements à la une pour le moment.');
        this.loading.set(false);
      },
    });
  }

  formatDate(value: string): string {
    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
      return value;
    }

    return date.toLocaleDateString('fr-FR', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  }

  openContactForm(): void {
    const user = this.auth.currentUser();

    this.contactName.set(user?.name ?? '');
    this.contactEmail.set(user?.email ?? '');
    this.contactMessage.set('');
    this.contactFeedback.set(null);
    this.showContactForm.set(true);
  }

  closeContactForm(): void {
    this.showContactForm.set(false);
  }

  submitContact(): void {
    const name = this.contactName().trim();
    const email = this.contactEmail().trim();
    const message = this.contactMessage().trim();

    if (!name || !email || !message) {
      this.contactFeedback.set({
        type: 'error',
        text: 'Merci de renseigner votre nom, votre email et votre message.',
      });
      return;
    }

    this.contactSending.set(true);
    this.contactFeedback.set(null);

    this.contactService.send({ name, email, message }).subscribe({
      next: (res) => {
        this.contactSending.set(false);
        this.contactFeedback.set({
          type: 'success',
          text: res.message ?? 'Votre message a bien été envoyé.',
        });
        this.contactMessage.set('');
      },
      error: (err) => {
        this.contactSending.set(false);
        this.contactFeedback.set({
          type: 'error',
          text: err?.error?.message ?? "Une erreur est survenue lors de l'envoi du message.",
        });
      },
    });
  }
}
