import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-not-found',
  standalone: true,
  imports: [RouterLink],
  template: `
    <div class="empty-state">
      <h2>404 — Page introuvable</h2>
      <p>La page que vous recherchez n'existe pas.</p>
      <a routerLink="/events" class="btn btn--primary">Retour à l'accueil</a>
    </div>
  `,
})
export class NotFound {}
