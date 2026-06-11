import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';

import { EventService } from '../../../core/services/event';
import { SalleService } from '../../../core/services/salle';
import { FileService } from '../../../core/services/file';

import { Salle } from '../../../core/models/salle.model';
import { Event as AppEvent, EventInput } from '../../../core/models/event.model';

@Component({
  selector: 'app-event-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './event-form.html',
  styleUrl: './event-form.css',
})
export class EventForm implements OnInit {
  private readonly fb = inject(FormBuilder);

  readonly salles = signal<Salle[]>([]);
  readonly events = signal<AppEvent[]>([]);
  readonly loading = signal(false);
  readonly saving = signal(false);
  readonly errorMessage = signal<string | null>(null);

  eventId: number | null = null;
  isEditMode = false;
  selectedFile: File | null = null;

  readonly minDate = this.getTodayDate();

  readonly form = this.fb.nonNullable.group({
    titre: ['', [Validators.required, Validators.minLength(3)]],
    description: [''],
    date_event: ['', Validators.required],
    heure: ['', Validators.required],
    heure_fin: ['', Validators.required],
    places_disponibles: [0, [Validators.required, Validators.min(1)]],
    prix: [0, [Validators.required, Validators.min(0)]],
    salle_id: [0, [Validators.required, Validators.min(1)]],
  });

  constructor(
    private eventService: EventService,
    private salleService: SalleService,
    private fileService: FileService,
    private route: ActivatedRoute,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.salleService.list().subscribe({
      next: (salles) => this.salles.set(salles),
      error: (err) => console.error(err),
    });

    this.eventService.list().subscribe({
      next: (events) => this.events.set(events),
      error: (err) => console.error(err),
    });

    this.form.controls.salle_id.valueChanges.subscribe((salleId) => {
      const salle = this.salles().find((s) => s.id === Number(salleId));

      if (salle) {
        this.form.controls.places_disponibles.setValidators([
          Validators.required,
          Validators.min(1),
          Validators.max(salle.capacite),
        ]);

        this.form.patchValue({ places_disponibles: salle.capacite });
        this.form.controls.places_disponibles.updateValueAndValidity();
      }
    });

    const id = this.route.snapshot.paramMap.get('id');

    if (id) {
      this.eventId = Number(id);
      this.isEditMode = true;
      this.loading.set(true);

      this.eventService.get(this.eventId).subscribe({
        next: (data) => {
          this.form.patchValue(
            {
              titre: data.titre,
              description: data.description ?? '',
              date_event: data.date_event,
              heure: this.normalizeTime(data.heure),
              heure_fin: this.normalizeTime(data.heure_fin),
              places_disponibles: data.places_disponibles,
              prix: data.prix,
              salle_id: data.salle_id,
            },
            { emitEvent: false },
          );

          this.loading.set(false);
        },
        error: () => {
          this.errorMessage.set('Impossible de charger cet événement.');
          this.loading.set(false);
        },
      });
    }
  }

  get f() {
    return this.form.controls;
  }

  onFileSelected(event: globalThis.Event): void {
    const input = event.target as HTMLInputElement;

    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];
    }
  }

  onSubmit(): void {
    this.errorMessage.set(null);

    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    const payload: EventInput = this.form.getRawValue();

    if (this.isDateTimeInPast(payload.date_event, payload.heure)) {
      this.errorMessage.set("La date et l'heure de début doivent être supérieures à la date et l'heure actuelles.");
      return;
    }

    if (!this.isEndTimeAfterStartTime(payload.heure, payload.heure_fin)) {
      this.errorMessage.set("L'heure de fin doit être supérieure à l'heure de début.");
      return;
    }

    const conflict = this.findSalleConflict(payload);

    if (conflict) {
      this.errorMessage.set(
        `Cette salle est déjà occupée de ${this.normalizeTime(conflict.heure)} à ${this.normalizeTime(conflict.heure_fin)} pour l'événement "${conflict.titre}".`,
      );
      return;
    }

    this.saving.set(true);

    const request$ =
      this.isEditMode && this.eventId
        ? this.eventService.update(this.eventId, payload)
        : this.eventService.create(payload);

    request$.subscribe({
      next: (eventSaved) => {
        const id = this.isEditMode && this.eventId ? this.eventId : eventSaved.id;

        if (this.selectedFile && id) {
          this.fileService.upload(this.selectedFile, 'Event', id).subscribe({
            next: () => {
              this.saving.set(false);
              this.router.navigate(this.isEditMode ? ['/events', id] : ['/events']);
            },
            error: () => {
              this.saving.set(false);
              this.errorMessage.set(
                "L'événement a été enregistré, mais l'image n'a pas pu être uploadée.",
              );
            },
          });
        } else {
          this.saving.set(false);
          this.router.navigate(this.isEditMode ? ['/events', id] : ['/events']);
        }
      },
      error: (error) => {
        this.saving.set(false);

        const backendMessage =
          error?.error?.errors?.date_event?.[0] ||
          error?.error?.errors?.heure_fin?.[0] ||
          error?.error?.errors?.salle_id?.[0] ||
          error?.error?.message ||
          "Une erreur est survenue lors de l'enregistrement.";

        this.errorMessage.set(backendMessage);
      },
    });
  }

  private isDateTimeInPast(date: string, heure: string): boolean {
    const selectedDateTime = new Date(`${date}T${this.normalizeTime(heure)}`);
    const now = new Date();

    return selectedDateTime <= now;
  }

  private isEndTimeAfterStartTime(heureDebut: string, heureFin: string): boolean {
    return this.normalizeTime(heureFin) > this.normalizeTime(heureDebut);
  }

  private findSalleConflict(payload: EventInput): AppEvent | null {
    const newStart = this.toDate(payload.date_event, payload.heure);
    const newEnd = this.toDate(payload.date_event, payload.heure_fin);

    return this.events().find((event) => {
      const sameSalle = Number(event.salle_id) === Number(payload.salle_id);
      const sameDate = event.date_event === payload.date_event;
      const isSameEditedEvent = this.isEditMode && this.eventId === event.id;

      if (!sameSalle || !sameDate || isSameEditedEvent) {
        return false;
      }

      const existingStart = this.toDate(event.date_event, event.heure);
      const existingEnd = this.toDate(event.date_event, event.heure_fin);

      return newStart < existingEnd && newEnd > existingStart;
    }) ?? null;
  }

  private toDate(date: string, heure: string): Date {
    return new Date(`${date}T${this.normalizeTime(heure)}`);
  }

  private normalizeTime(time: string): string {
    return time.slice(0, 5);
  }

  private getTodayDate(): string {
    return new Date().toISOString().slice(0, 10);
  }
}
