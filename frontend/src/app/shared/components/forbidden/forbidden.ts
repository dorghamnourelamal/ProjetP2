import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-forbidden',
  standalone: true,
  imports: [RouterLink],
  template: `
    <div class="empty-state">
      <h2>🚫 Accès refusé</h2>
      <p>Vous n'avez pas les droits nécessaires pour accéder à cette page.</p>
      <a routerLink="/events" class="btn btn--primary">Retour aux événements</a>
    </div>
  `,
})
export class Forbidden {}
