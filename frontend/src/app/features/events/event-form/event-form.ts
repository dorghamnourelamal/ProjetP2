import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { EventService } from '../../../core/services/event';
import { SalleService } from '../../../core/services/salle';
import { Salle } from '../../../core/models/salle.model';
import { EventInput } from '../../../core/models/event.model';

/**
 * Formulaire Ajout/Édition d'événement basé sur les Reactive Forms
 * (FormBuilder + Validators) : validation déclarative, accès typé aux contrôles,
 * et même composant réutilisé pour la création et la modification (mode déduit de l'URL).
 */
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
    private route: ActivatedRoute,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.salleService.list().subscribe({
      next: (salles) => this.salles.set(salles),
      error: (err) => console.error(err),
    });

    // La capacité d'accueil d'un événement ne peut pas dépasser la capacité de sa salle :
    // dès qu'une salle est choisie, on aligne automatiquement "places disponibles" sur sa
    // capacité et on empêche de saisir un nombre de places supérieur.
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
          // emitEvent: false : on ne veut pas que le pré-remplissage du salle_id existant
          // déclenche la réinitialisation de places_disponibles à la capacité de la salle
          // (en édition, ce nombre peut légitimement être inférieur suite à des réservations).
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
          this.errorMessage.set("Impossible de charger cet événement.");
          this.loading.set(false);
        },
      });
    }
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
    const payload: EventInput = this.form.getRawValue();

    const request$ =
      this.isEditMode && this.eventId
        ? this.eventService.update(this.eventId, payload)
        : this.eventService.create(payload);

    request$.subscribe({
      next: () => {
        this.saving.set(false);
        this.router.navigate(this.isEditMode ? ['/events', this.eventId] : ['/events']);
      },
      error: () => {
        this.saving.set(false);
        this.errorMessage.set("Une erreur est survenue lors de l'enregistrement.");
      },
    });
  }
}
