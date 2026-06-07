import { Component, OnInit, computed, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { SalleService } from '../../../core/services/salle';
import { AuthService } from '../../../core/services/auth';
import { Salle } from '../../../core/models/salle.model';

type SortKey = 'nom' | 'capacite';

/**
 * Liste des salles : lecture publique, gestion (ajout/édition/suppression)
 * réservée aux administrateurs (roleGuard côté routes + vérification d'affichage ici).
 * Recherche et tri appliqués côté client via signaux + computed.
 */
@Component({
  selector: 'app-salle-list',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './salle-list.html',
  styleUrl: './salle-list.css',
})
export class SalleList implements OnInit {
  private readonly sallesSignal = signal<Salle[]>([]);

  readonly loading = signal(true);
  readonly errorMessage = signal<string | null>(null);
  readonly searchTerm = signal('');
  readonly sortKey = signal<SortKey>('nom');
  readonly sortAsc = signal(true);

  readonly filteredSalles = computed(() => {
    const term = this.searchTerm().trim().toLowerCase();
    const key = this.sortKey();
    const asc = this.sortAsc();

    let result = this.sallesSignal().filter((salle) => {
      if (!term) {
        return true;
      }
      return salle.nom.toLowerCase().includes(term) || (salle.adresse ?? '').toLowerCase().includes(term);
    });

    result = [...result].sort((a, b) => {
      const va = a[key];
      const vb = b[key];
      const comparison = typeof va === 'string' ? va.localeCompare(vb as string) : (va as number) - (vb as number);
      return asc ? comparison : -comparison;
    });

    return result;
  });

  constructor(
    private salleService: SalleService,
    public auth: AuthService,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.errorMessage.set(null);

    this.salleService.list().subscribe({
      next: (salles) => {
        this.sallesSignal.set(salles);
        this.loading.set(false);
      },
      error: () => {
        this.errorMessage.set('Impossible de charger les salles pour le moment.');
        this.loading.set(false);
      },
    });
  }

  changeSort(key: SortKey): void {
    if (this.sortKey() === key) {
      this.sortAsc.update((asc) => !asc);
    } else {
      this.sortKey.set(key);
      this.sortAsc.set(true);
    }
  }

  add(): void {
    this.router.navigate(['/salles/add']);
  }

  edit(salle: Salle): void {
    this.router.navigate(['/salles', salle.id, 'edit']);
  }

  remove(salle: Salle): void {
    if (!confirm('Supprimer la salle "' + salle.nom + '" ?')) {
      return;
    }

    this.salleService.delete(salle.id).subscribe({
      next: () => this.sallesSignal.update((list) => list.filter((s) => s.id !== salle.id)),
      error: () => alert('Erreur lors de la suppression de la salle.'),
    });
  }
}
