import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-reset-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './reset-password.html',
  styleUrl: './reset-password.css',
})
export class ResetPassword implements OnInit {
  form: FormGroup;
  readonly loading = signal(false);
  readonly success = signal<string | null>(null);
  readonly error   = signal<string | null>(null);

  private token = '';
  private email = '';

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private route: ActivatedRoute,
    private router: Router,
  ) {
    this.form = this.fb.group({
      password:              ['', [Validators.required, Validators.minLength(8)]],
      password_confirmation: ['', Validators.required],
    }, { validators: this.passwordMatchValidator });
  }

  ngOnInit(): void {
    this.token = this.route.snapshot.queryParamMap.get('token') ?? '';
    this.email = this.route.snapshot.queryParamMap.get('email') ?? '';

    if (!this.token || !this.email) {
      this.error.set('Lien invalide. Veuillez refaire une demande de réinitialisation.');
    }
  }

  get f() { return this.form.controls; }

  passwordMatchValidator(group: FormGroup) {
    const pw  = group.get('password')?.value;
    const cpw = group.get('password_confirmation')?.value;
    return pw === cpw ? null : { mismatch: true };
  }

  submit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }

    this.loading.set(true);
    this.error.set(null);

    const payload = {
      token:                 this.token,
      email:                 this.email,
      password:              this.form.value.password,
      password_confirmation: this.form.value.password_confirmation,
    };

    this.http.post<{ message: string }>(`${environment.apiUrl}/reset-password`, payload)
      .subscribe({
        next: (res) => {
          this.success.set(res.message);
          this.loading.set(false);
          setTimeout(() => this.router.navigate(['/login']), 3000);
        },
        error: (err) => {
          this.error.set(err.error?.message ?? 'Token invalide ou expiré.');
          this.loading.set(false);
        },
      });
  }
}
