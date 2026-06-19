import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { RouterLink } from '@angular/router';
import { StatService, StatsOverview, ActivityLogEntry } from '../../../core/services/stat';
import { environment } from '../../../../environments/environment';

type DashboardTab = 'overview' | 'powerbi' | 'audit';

interface PowerBiExport {
  label: string;
  description: string;
  endpoint: string;
  filename: string;
}

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

  readonly activeTab = signal<DashboardTab>('overview');
  readonly selectedTicketStatus = signal<string | null>(null);
  readonly selectedActivityAction = signal<string | null>(null);

  readonly exportingFile = signal<string | null>(null);
  readonly exportMessage = signal<string | null>(null);
  readonly exportError = signal<string | null>(null);

  readonly powerBiExports: PowerBiExport[] = [
    {
      label: 'Événements',
      description: 'Événements, salles, capacité, places réservées et chiffre d’affaires.',
      endpoint: 'events.csv',
      filename: 'powerbi_events.csv',
    },
    {
      label: 'Réservations',
      description: 'Réservations clients, événements associés, nombre de places et montant total.',
      endpoint: 'reservations.csv',
      filename: 'powerbi_reservations.csv',
    },
    {
      label: 'Billets',
      description: 'Billets générés, statuts, prix, clients et événements associés.',
      endpoint: 'tickets.csv',
      filename: 'powerbi_tickets.csv',
    },
    {
      label: 'Salles',
      description: 'Salles, capacité, événements associés et taux d’occupation global.',
      endpoint: 'salles.csv',
      filename: 'powerbi_salles.csv',
    },
    {
      label: 'Activité MongoDB',
      description: 'Journaux d’activité : connexions, créations, modifications et suppressions.',
      endpoint: 'activity.csv',
      filename: 'powerbi_activity.csv',
    },
    {
      label: 'Statistiques MongoDB',
      description: 'Indicateurs d’usage enregistrés dans MongoDB pour l’analyse décisionnelle.',
      endpoint: 'stats.csv',
      filename: 'powerbi_stats.csv',
    },
  ];

  private readonly powerBiApiUrl = `${environment.apiUrl}/powerbi`;

  constructor(
    private statService: StatService,
    private http: HttpClient,
  ) {}

  ngOnInit(): void {
    this.loadOverview();
  }

  loadOverview(): void {
    this.loading.set(true);
    this.errorMessage.set(null);

    this.statService.overview().subscribe({
      next: (overview) => {
        this.overview.set(overview);
        this.loading.set(false);
      },
      error: () => {
        this.errorMessage.set('Impossible de charger les statistiques pour le moment.');
        this.loading.set(false);
      },
    });
  }

  setActiveTab(tab: DashboardTab): void {
    this.activeTab.set(tab);
  }

  downloadPowerBiExport(exportFile: PowerBiExport): void {
    this.exportingFile.set(exportFile.filename);
    this.exportMessage.set(null);
    this.exportError.set(null);

    this.http
      .get(`${this.powerBiApiUrl}/${exportFile.endpoint}`, {
        responseType: 'blob',
      })
      .subscribe({
        next: (blob) => {
          this.saveBlob(blob, exportFile.filename);
          this.exportingFile.set(null);
          this.exportMessage.set(`Export téléchargé : ${exportFile.filename}`);
        },
        error: () => {
          this.exportingFile.set(null);
          this.exportError.set(`Impossible de télécharger ${exportFile.filename}.`);
        },
      });
  }

  getFormattedRevenue(): string {
    const amount = this.overview()?.totals.chiffre_affaires ?? 0;

    return `${Number(amount).toLocaleString('fr-FR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })} €`;
  }

  getAverageTicketPrice(): string {
    const overview = this.overview();

    if (!overview || overview.totals.tickets === 0) {
      return '0,00 €';
    }

    const average = overview.totals.chiffre_affaires / overview.totals.tickets;

    return `${average.toLocaleString('fr-FR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })} €`;
  }

  getTopEventMax(): number {
    const rows = this.overview()?.top_events_by_reservations ?? [];

    return Math.max(...rows.map((row) => Number(row.total_places)), 1);
  }

  getSalleMax(): number {
    const rows = this.overview()?.places_by_salle ?? [];

    return Math.max(...rows.map((row) => Number(row.places_reservees)), 1);
  }

  getBarWidth(value: number, max: number): string {
    if (max <= 0) {
      return '0%';
    }

    return `${Math.max(4, Math.round((Number(value) / max) * 100))}%`;
  }

  getTicketsTotal(): number {
    return (this.overview()?.tickets_by_status ?? [])
      .reduce((sum, row) => sum + Number(row.total), 0);
  }

  getTicketStatusPercentage(value: number): number {
    const total = this.getTicketsTotal();

    if (total === 0) {
      return 0;
    }

    return Math.round((Number(value) / total) * 100);
  }

  toggleTicketStatus(status: string): void {
    this.selectedTicketStatus.update((current) => current === status ? null : status);
  }

  getFilteredTicketsByStatus() {
    const rows = this.overview()?.tickets_by_status ?? [];
    const selected = this.selectedTicketStatus();

    if (!selected) {
      return rows;
    }

    return rows.filter((row) => row.statut === selected);
  }

  getDonutBackground(): string {
    const rows = this.overview()?.tickets_by_status ?? [];
    const total = this.getTicketsTotal();

    if (rows.length === 0 || total === 0) {
      return 'conic-gradient(#e5e7eb 0% 100%)';
    }

    const colors = ['#06b6d4', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'];
    let start = 0;

    const parts = rows.map((row, index) => {
      const value = (Number(row.total) / total) * 100;
      const end = start + value;
      const part = `${colors[index % colors.length]} ${start}% ${end}%`;

      start = end;

      return part;
    });

    return `conic-gradient(${parts.join(', ')})`;
  }

  getStatusColor(index: number): string {
    const colors = ['#06b6d4', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'];

    return colors[index % colors.length];
  }

  getActivityActions(): string[] {
    const actions = this.overview()?.recent_activity.map((entry) => entry.action) ?? [];

    return [...new Set(actions)].sort();
  }

  getActivityCount(action: string): number {
    return (this.overview()?.recent_activity ?? [])
      .filter((entry) => entry.action === action)
      .length;
  }

  setActivityActionFilter(action: string | null): void {
    this.selectedActivityAction.set(action);
  }

  getFilteredActivity(): ActivityLogEntry[] {
    const entries = this.overview()?.recent_activity ?? [];
    const selectedAction = this.selectedActivityAction();

    if (!selectedAction) {
      return entries;
    }

    return entries.filter((entry) => entry.action === selectedAction);
  }

  getActionLabel(action: string): string {
    const labels: Record<string, string> = {
      login: 'Connexion',
      logout: 'Déconnexion',
      create: 'Création',
      update: 'Modification',
      delete: 'Suppression',
      register: 'Inscription',
    };

    return labels[action] ?? action;
  }

  getMetricLabel(id: string | null): string {
    if (!id) return 'Activité générale';
    const labels: Record<string, string> = {
      event_view:           'Vues d\'événements',
      reservation_created:  'Réservations créées',
      ticket_sold:          'Billets vendus',
    };
    return labels[id] ?? id;
  }

  private saveBlob(blob: Blob, filename: string): void {
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');

    link.href = url;
    link.download = filename;
    link.click();

    window.URL.revokeObjectURL(url);
  }
}
