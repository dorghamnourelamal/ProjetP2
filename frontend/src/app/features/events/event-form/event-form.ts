import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';

import { EventService } from '../../../core/services/event';
import { SalleService } from '../../../core/services/salle';
import { FileService } from '../../../core/services/file';

import { Salle } from '../../../core/models/salle.model';
import { EventInput } from '../../../core/models/event.model';

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
  readonly loading = signal(false);
  readonly saving = signal(false);
  readonly errorMessage = signal<string | null>(null);

  eventId: number | null = null;
  isEditMode = false;

  // Fichier image sélectionné dans le formulaire
  selectedFile: File | null = null;

  readonly form = this.fb.nonNullable.group({
    titre: ['', [Validators.required, Validators.minLength(3)]],
    description: [''],
    date_event: ['', Validators.required],
    heure: ['', Validators.required],
    places_disponibles: [0, [Validators.required, Validators.min(0)]],
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

    this.form.controls.salle_id.valueChanges.subscribe((salleId) => {
      const salle = this.salles().find((s) => s.id === Number(salleId));

      if (salle) {
        this.form.controls.places_disponibles.setValidators([
          Validators.required,
          Validators.min(0),
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
              heure: data.heure,
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

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;

    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];
    }
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.saving.set(true);
    this.errorMessage.set(null);

    const payload: EventInput = this.form.getRawValue();

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
      error: () => {
        this.saving.set(false);
        this.errorMessage.set("Une erreur est survenue lors de l'enregistrement.");
      },
    });
  }
}
