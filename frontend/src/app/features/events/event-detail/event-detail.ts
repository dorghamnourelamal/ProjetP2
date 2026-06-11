import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { switchMap } from 'rxjs';
import { EventService } from '../../../core/services/event';
import { AuthService } from '../../../core/services/auth';
import { Event } from '../../../core/models/event.model';

@Component({
  selector: 'app-event-detail',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './event-detail.html',
  styleUrl: './event-detail.css',
})
export class EventDetail implements OnInit {
  readonly event = signal<Event | null>(null);
  readonly loading = signal(true);
  readonly errorMessage = signal<string | null>(null);

  constructor(
    private route: ActivatedRoute,
    private eventService: EventService,
    public auth: AuthService,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.route.paramMap
      .pipe(
        switchMap((params) => {
          this.loading.set(true);
          this.errorMessage.set(null);

          return this.eventService.get(Number(params.get('id')));
        }),
      )
      .subscribe({
        next: (event) => {
          this.event.set(event);
          this.loading.set(false);
        },
        error: () => {
          this.errorMessage.set("Cet événement n'existe pas ou a été supprimé.");
          this.loading.set(false);
        },
      });
  }

  reserve(): void {
    const event = this.event();

    if (event) {
      this.router.navigate(['/events', event.id, 'reserve']);
    }
  }

  edit(): void {
    const event = this.event();

    if (event) {
      this.router.navigate(['/events', event.id, 'edit']);
    }
  }

  delete(): void {
    const event = this.event();

    if (!event || !confirm(`Supprimer l'événement "${event.titre}" ?`)) {
      return;
    }

    this.eventService.delete(event.id).subscribe({
      next: () => this.router.navigate(['/events']),
      error: () => alert('Erreur lors de la suppression.'),
    });
  }
}
