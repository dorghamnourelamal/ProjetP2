import { Component, OnInit, computed, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { EventService } from '../../../core/services/event';
import { AuthService } from '../../../core/services/auth';
import { Event } from '../../../core/models/event.model';
import { EventCard } from '../../../shared/components/event-card/event-card';

type SortKey = 'date_event' | 'titre' | 'prix' | 'places_disponibles';

/**
 * Liste des événements : consommation asynchrone via HttpClient/RxJS,
 * puis tri et filtrage 100% côté client (signaux + computed) sans nouvel appel réseau.
 * Démontre aussi la communication enfant -> parent via les @Output de <app-event-card>.
 */
@Component({
  selector: 'app-event-list',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, EventCard],
  templateUrl: './event-list.html',
  styleUrl: './event-list.css',
})
export class EventList implements OnInit {
  private readonly eventsSignal = signal<Event[]>([]);

  readonly loading = signal(true);
  readonly errorMessage = signal<string | null>(null);

  readonly searchTerm = signal('');
  readonly sortKey = signal<SortKey>('date_event');
  readonly sortAsc = signal(true);
  readonly onlyAvailable = signal(false);

  /** Liste filtrée puis triée, recalculée automatiquement (computed) à chaque changement. */
  readonly filteredEvents = computed(() => {
    const term = this.searchTerm().trim().toLowerCase();
    const onlyAvailable = this.onlyAvailable();
    const key = this.sortKey();
    const asc = this.sortAsc();

    let result = this.eventsSignal().filter((event) => {
      // Les utilisateurs ne voient pas les événements annulés
      if (!this.auth.isAdmin() && event.statut === 'annulé') return false;

      const matchesSearch =
        !term ||
        event.titre.toLowerCase().includes(term) ||
        (event.salle?.nom ?? '').toLowerCase().includes(term);
      const matchesAvailability = !onlyAvailable || event.places_disponibles > 0;
      return matchesSearch && matchesAvailability;
    });

    result = [...result].sort((a, b) => {
      const va = a[key];
      const vb = b[key];
      const comparison = typeof va === 'string' ? va.localeCompare(vb as string) : (va as number) - (vb as number);
      return asc ? comparison : -comparison;
    });

    return result;
  });

  constructor(
    private eventService: EventService,
    public auth: AuthService,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.errorMessage.set(null);

    this.eventService.list().subscribe({
      next: (events) => {
        this.eventsSignal.set(events);
        this.loading.set(false);
      },
      error: () => {
        this.errorMessage.set('Impossible de charger les événements pour le moment.');
        this.loading.set(false);
      },
    });
  }

  changeSort(key: SortKey): void {
    if (this.sortKey() === key) {
      this.sortAsc.update((asc) => !asc);
    } else {
      this.sortKey.set(key);
      this.sortAsc.set(true);
    }
  }

  onReserve(event: Event): void {
    this.router.navigate(['/events', event.id, 'reserve']);
  }

  onEdit(event: Event): void {
    this.router.navigate(['/events', event.id, 'edit']);
  }

  onDelete(event: Event): void {
    if (!confirm(`Annuler l’événement "${event.titre}" ? Un email sera envoyé à tous les participants.`)) {
      return;
    }

    this.eventService.delete(event.id).subscribe({
      next: () => this.eventsSignal.update((list) =>
        list.map((e) => e.id === event.id ? { ...e, statut: 'annulé' as const } : e)      ),
      error: () => alert("Erreur lors de l’annulation de l’événement."),
    });
  }
}
