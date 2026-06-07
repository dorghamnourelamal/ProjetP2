import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { Event } from '../../../core/models/event.model';

/**
 * Composant réutilisable "carte événement".
 * Démontre la communication par décorateurs :
 *  - @Input  : reçoit les données et le contexte (lecture seule, mode admin) du parent,
 *  - @Output : notifie le parent des actions utilisateur (réserver / éditer / supprimer)
 *              sans connaître la logique métier (faible couplage, cycle de vie clair).
 */
@Component({
  selector: 'app-event-card',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './event-card.html',
  styleUrl: './event-card.css',
})
export class EventCard {
  /** Événement à afficher (donnée descendante parent -> enfant). */
  @Input({ required: true }) event!: Event;

  /** Active les actions de gestion (édition/suppression) réservées aux administrateurs. */
  @Input() canManage = false;

  /** Émis quand l'utilisateur souhaite réserver des places pour cet événement. */
  @Output() reserve = new EventEmitter<Event>();

  /** Émis quand l'administrateur souhaite éditer l'événement. */
  @Output() edit = new EventEmitter<Event>();

  /** Émis quand l'administrateur souhaite supprimer l'événement. */
  @Output() remove = new EventEmitter<Event>();

  get isComplet(): boolean {
    return this.event.places_disponibles <= 0;
  }

  /**
   * Met en avant (badge "Populaire") les événements à venir déjà bien réservés
   * (au moins la moitié de la capacité de la salle), façon maquette Eventify.
   */
  get isPopular(): boolean {
    const today = new Date().toISOString().slice(0, 10);
    const capacite = this.event.salle?.capacite;
    if (!capacite || this.event.date_event < today || this.isComplet) {
      return false;
    }
    const tauxRemplissage = 1 - this.event.places_disponibles / capacite;
    return tauxRemplissage >= 0.5;
  }
}
