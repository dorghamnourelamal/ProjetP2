import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './forgot-password.html',
  styleUrl: './forgot-password.css',
})
export class ForgotPassword {
  form: FormGroup;
  readonly loading  = signal(false);
  readonly success  = signal<string | null>(null);
  readonly error    = signal<string | null>(null);

  constructor(private fb: FormBuilder, private http: HttpClient) {
    this.form = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
    });
  }

  get f() { return this.form.controls; }

  submit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }

    this.loading.set(true);
    this.success.set(null);
    this.error.set(null);

    this.http.post<{ message: string }>(`${environment.apiUrl}/forgot-password`, this.form.value)
      .subscribe({
        next: (res) => {
          this.success.set(res.message);
          this.form.reset();
          this.loading.set(false);
        },
        error: (err) => {
          this.error.set(err.error?.errors?.email?.[0] ?? err.error?.message ?? 'Une erreur est survenue.');
          this.loading.set(false);
        },
      });
  }
}
