import { Component, OnInit, computed, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { ReservationService } from '../../../core/services/reservation';
import { AuthService } from '../../../core/services/auth';
import { Reservation } from '../../../core/models/reservation.model';

type SortKey = 'nom_client' | 'nombre_places' | 'created_at';

@Component({
  selector: 'app-reservation-list',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './reservation-list.html',
  styleUrl: './reservation-list.css',
})
export class ReservationList implements OnInit {
  private readonly reservationsSignal = signal<Reservation[]>([]);

  readonly loading = signal(true);
  readonly errorMessage = signal<string | null>(null);
  readonly searchTerm = signal('');
  readonly sortKey = signal<SortKey>('created_at');
  readonly sortAsc = signal(false);

  readonly filteredReservations = computed(() => {
    const term = this.searchTerm().trim().toLowerCase();
    const key = this.sortKey();
    const asc = this.sortAsc();

    let result = this.reservationsSignal().filter((reservation) => {
      if (!term) {
        return true;
      }
      return (
        reservation.nom_client.toLowerCase().includes(term) ||
        reservation.email_client.toLowerCase().includes(term) ||
        (reservation.event?.titre ?? '').toLowerCase().includes(term)
      );
    });

    result = [...result].sort((a, b) => {
      const va = a[key] ?? '';
      const vb = b[key] ?? '';
      const comparison = typeof va === 'string' ? va.localeCompare(vb as string) : (va as number) - (vb as number);
      return asc ? comparison : -comparison;
    });

    return result;
  });

  constructor(
    private reservationService: ReservationService,
    public auth: AuthService,
  ) {}

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.errorMessage.set(null);

    this.reservationService.list().subscribe({
      next: (reservations) => {
        this.reservationsSignal.set(reservations);
        this.loading.set(false);
      },
      error: () => {
        this.errorMessage.set('Impossible de charger les réservations pour le moment.');
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

  deleteReservation(reservation: Reservation): void {
    if (!confirm(`Supprimer la réservation de "${reservation.nom_client}" ?`)) {
      return;
    }

    this.reservationService.delete(reservation.id).subscribe({
      next: () => this.reservationsSignal.update((list) => list.filter((r) => r.id !== reservation.id)),
      error: () => alert('Erreur lors de la suppression de la réservation.'),
    });
  }
}
