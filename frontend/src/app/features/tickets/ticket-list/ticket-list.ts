import { Component, OnInit, computed, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { TicketService } from '../../../core/services/ticket';
import { Ticket } from '../../../core/models/ticket.model';

type SortKey = 'code' | 'type' | 'prix' | 'statut';

@Component({
  selector: 'app-ticket-list',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './ticket-list.html',
  styleUrl: './ticket-list.css',
})
export class TicketList implements OnInit {
  private readonly ticketsSignal = signal<Ticket[]>([]);

  readonly loading = signal(true);
  readonly errorMessage = signal<string | null>(null);
  readonly searchTerm = signal('');
  readonly sortKey = signal<SortKey>('code');
  readonly sortAsc = signal(true);

  readonly filteredTickets = computed(() => {
    const term = this.searchTerm().trim().toLowerCase();
    const key = this.sortKey();
    const asc = this.sortAsc();

    let result = this.ticketsSignal().filter((ticket) => {
      if (!term) {
        return true;
      }

      return (
        ticket.code.toLowerCase().includes(term) ||
        ticket.type.toLowerCase().includes(term) ||
        ticket.statut.toLowerCase().includes(term) ||
        (ticket.reservation?.nom_client ?? '').toLowerCase().includes(term) ||
        (ticket.reservation?.event?.titre ?? '').toLowerCase().includes(term)
      );
    });

    result = [...result].sort((a, b) => {
      const va = a[key];
      const vb = b[key];

      const comparison =
        typeof va === 'string'
          ? va.localeCompare(vb as string)
          : (va as number) - (vb as number);

      return asc ? comparison : -comparison;
    });

    return result;
  });

  constructor(private ticketService: TicketService) {}

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.errorMessage.set(null);

    this.ticketService.list().subscribe({
      next: (tickets) => {
        this.ticketsSignal.set(tickets);
        this.loading.set(false);
      },
      error: () => {
        this.errorMessage.set('Impossible de charger les billets pour le moment.');
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

  remove(ticket: Ticket): void {
    if (!confirm('Supprimer le billet "' + ticket.code + '" ?')) {
      return;
    }

    this.ticketService.delete(ticket.id).subscribe({
      next: () => this.ticketsSignal.update((list) => list.filter((t) => t.id !== ticket.id)),
      error: () => alert('Erreur lors de la suppression du billet.'),
    });
  }
}
