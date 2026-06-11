import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { Event } from '../../../core/models/event.model';

@Component({
  selector: 'app-event-card',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './event-card.html',
  styleUrl: './event-card.css',
})
export class EventCard {
  @Input({ required: true }) event!: Event;
  @Input() canManage = false;

  @Output() reserve = new EventEmitter<Event>();
  @Output() edit = new EventEmitter<Event>();
  @Output() remove = new EventEmitter<Event>();

  get imageUrl(): string | null {
    return this.event.image_url ?? null;
  }

  get isComplet(): boolean {
    return this.event.places_disponibles <= 0;
  }

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
