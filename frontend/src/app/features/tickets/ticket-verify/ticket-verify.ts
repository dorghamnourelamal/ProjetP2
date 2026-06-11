import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { TicketService } from '../../../core/services/ticket';
import { AuthService } from '../../../core/services/auth';
import { TicketVerification } from '../../../core/models/ticket.model';

@Component({
  selector: 'app-ticket-verify',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './ticket-verify.html',
  styleUrl: './ticket-verify.css',
})
export class TicketVerify implements OnInit {
  readonly loading = signal(true);
  readonly validating = signal(false);
  readonly errorMessage = signal<string | null>(null);
  readonly successMessage = signal<string | null>(null);
  readonly verification = signal<TicketVerification | null>(null);

  code = '';

  constructor(
    private route: ActivatedRoute,
    private ticketService: TicketService,
    public auth: AuthService,
  ) {}

  ngOnInit(): void {
    this.code = this.route.snapshot.paramMap.get('code') ?? '';

    if (!this.code) {
      this.errorMessage.set('Code billet manquant.');
      this.loading.set(false);
      return;
    }

    this.loadVerification();
  }

  loadVerification(): void {
    this.loading.set(true);
    this.errorMessage.set(null);
    this.successMessage.set(null);

    this.ticketService.verifyByCode(this.code).subscribe({
      next: (verification) => {
        this.verification.set(verification);
        this.loading.set(false);
      },
      error: (error) => {
        this.verification.set(error?.error ?? null);
        this.errorMessage.set(error?.error?.message ?? 'Billet introuvable.');
        this.loading.set(false);
      },
    });
  }

  validateEntry(): void {
    if (!this.code) {
      return;
    }

    this.validating.set(true);
    this.errorMessage.set(null);
    this.successMessage.set(null);

    this.ticketService.useByCode(this.code).subscribe({
      next: (verification) => {
        this.verification.set(verification);
        this.successMessage.set(verification.message);
        this.validating.set(false);
      },
      error: (error) => {
        if (error?.error) {
          this.verification.set(error.error);
          this.errorMessage.set(error.error.message);
        } else {
          this.errorMessage.set("Impossible de valider l'entrée.");
        }

        this.validating.set(false);
      },
    });
  }

  getStatusClass(): string {
    const statut = this.verification()?.statut;

    if (statut === 'valide') {
      return 'status--valid';
    }

    if (statut === 'utilisé') {
      return 'status--used';
    }

    return 'status--cancelled';
  }
}
