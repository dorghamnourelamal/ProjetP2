import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { StatService, StatsOverview } from '../../../core/services/stat';

/**
 * Tableau de bord administrateur : agrège les indicateurs métier (MySQL : événements,
 * réservations, places vendues) et les statistiques d'usage stockées dans MongoDB
 * (métriques par type, activité récente) — base pour des rapports type Power BI.
 * Accès protégé par roleGuard(['admin']) au niveau des routes.
 */
@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.css',
})
export class Dashboard implements OnInit {
  readonly overview = signal<StatsOverview | null>(null);
  readonly loading = signal(true);
  readonly errorMessage = signal<string | null>(null);

  constructor(private statService: StatService) {}

  ngOnInit(): void {
    this.statService.overview().subscribe({
      next: (overview) => {
        this.overview.set(overview);
        this.loading.set(false);
      },
      error: () => {
        this.errorMessage.set("Impossible de charger les statistiques pour le moment.");
        this.loading.set(false);
      },
    });
  }
}
