import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { ReservationService } from '../../../core/services/reservation';
import { EventService } from '../../../core/services/event';
import { AuthService } from '../../../core/services/auth';
import { Event } from '../../../core/models/event.model';
import { ReservationInput } from '../../../core/models/reservation.model';

@Component({
  selector: 'app-reservation-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './reservation-form.html',
  styleUrl: './reservation-form.css',
})
export class ReservationForm implements OnInit {
  private readonly fb = inject(FormBuilder);

  readonly event = signal<Event | null>(null);
  readonly loading = signal(true);
  readonly saving = signal(false);
  readonly errorMessage = signal<string | null>(null);

  private eventId = 0;

  readonly form = this.fb.nonNullable.group({
    nom_client: ['', [Validators.required, Validators.minLength(2)]],
    email_client: ['', [Validators.required, Validators.email]],
    nombre_places: [1, [Validators.required, Validators.min(1)]],
  });

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private reservationService: ReservationService,
    private eventService: EventService,
    private auth: AuthService,
  ) {}

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    this.eventId = Number(id);

    const user = this.auth.currentUser();
    if (user) {
      this.form.patchValue({ nom_client: user.name, email_client: user.email });
    }

    this.eventService.get(this.eventId).subscribe({
      next: (event) => {
        this.event.set(event);
        this.form.controls.nombre_places.addValidators(Validators.max(Math.max(event.places_disponibles, 1)));
        this.form.controls.nombre_places.updateValueAndValidity();
        this.loading.set(false);
      },
      error: () => {
        this.errorMessage.set("Cet événement n'existe pas ou a été supprimé.");
        this.loading.set(false);
      },
    });
  }

  get f() {
    return this.form.controls;
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.saving.set(true);
    this.errorMessage.set(null);

    const payload: ReservationInput = { ...this.form.getRawValue(), event_id: this.eventId };

    this.reservationService.create(payload).subscribe({
      next: () => {
        this.saving.set(false);
        this.router.navigate(['/reservations']);
      },
      error: (err) => {
        this.saving.set(false);
        this.errorMessage.set(err.error?.message || 'Erreur lors de la réservation.');
      },
    });
  }
}
