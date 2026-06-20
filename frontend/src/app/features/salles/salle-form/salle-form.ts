import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { SalleService, SalleInput } from '../../../core/services/salle';

@Component({
  selector: 'app-salle-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './salle-form.html',
  styleUrl: './salle-form.css',
})
export class SalleForm implements OnInit {
  private readonly fb = inject(FormBuilder);

  readonly loading = signal(false);
  readonly saving = signal(false);
  readonly errorMessage = signal<string | null>(null);

  salleId: number | null = null;
  isEditMode = false;

  readonly form = this.fb.nonNullable.group({
    nom: ['', [Validators.required, Validators.minLength(2)]],
    capacite: [0, [Validators.required, Validators.min(1)]],
    adresse: [''],
  });

  constructor(
    private salleService: SalleService,
    private route: ActivatedRoute,
    private router: Router,
  ) {}

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');

    if (id) {
      this.salleId = Number(id);
      this.isEditMode = true;
      this.loading.set(true);

      this.salleService.get(this.salleId).subscribe({
        next: (salle) => {
          this.form.patchValue({
            nom: salle.nom,
            capacite: salle.capacite,
            adresse: salle.adresse ?? '',
          });
          this.loading.set(false);
        },
        error: () => {
          this.errorMessage.set('Impossible de charger cette salle.');
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
    const payload: SalleInput = this.form.getRawValue();

    const request$ =
      this.isEditMode && this.salleId
        ? this.salleService.update(this.salleId, payload)
        : this.salleService.create(payload);

    request$.subscribe({
      next: () => {
        this.saving.set(false);
        this.router.navigate(['/salles']);
      },
      error: () => {
        this.saving.set(false);
        this.errorMessage.set("Une erreur est survenue lors de l'enregistrement.");
      },
    });
  }
}
