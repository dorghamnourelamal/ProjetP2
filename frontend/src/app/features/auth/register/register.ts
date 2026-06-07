import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AbstractControl, FormBuilder, ReactiveFormsModule, ValidationErrors, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../core/services/auth';

/** Validateur de groupe : vérifie que password et password_confirmation sont identiques. */
function passwordsMatchValidator(control: AbstractControl): ValidationErrors | null {
  const password = control.get('password')?.value;
  const confirmation = control.get('password_confirmation')?.value;
  return password && confirmation && password !== confirmation ? { passwordsMismatch: true } : null;
}

/**
 * Page d'inscription : formulaire réactif avec validations croisées
 * (confirmation du mot de passe) et création automatique d'une session.
 */
@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './register.html',
  styleUrl: './register.css',
})
export class Register {
  private readonly fb = inject(FormBuilder);

  readonly loading = signal(false);
  readonly errorMessage = signal<string | null>(null);

  readonly form = this.fb.group(
    {
      name: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      password_confirmation: ['', [Validators.required]],
    },
    { validators: passwordsMatchValidator },
  );

  constructor(
    private authService: AuthService,
    private router: Router,
  ) {}

  get f() {
    return this.form.controls;
  }

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.loading.set(true);
    this.errorMessage.set(null);

    this.authService.register(this.form.getRawValue() as any).subscribe({
      next: () => {
        this.loading.set(false);
        this.router.navigate(['/events']);
      },
      error: (err) => {
        this.loading.set(false);
        const messages = err.error?.errors ? Object.values(err.error.errors).flat() : [err.error?.message];
        this.errorMessage.set((messages.filter(Boolean) as string[]).join(' ') || "Erreur lors de l'inscription.");
      },
    });
  }
}
