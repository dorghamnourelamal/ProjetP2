import { Component, EventEmitter, Input, OnChanges, Output, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { Event } from '../../../core/models/event.model';
import { FileService } from '../../../core/services/file';

/**
 * Composant réutilisable "carte événement".
 */
@Component({
  selector: 'app-event-card',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './event-card.html',
  styleUrl: './event-card.css',
})
export class EventCard implements OnChanges {
  @Input({ required: true }) event!: Event;
  @Input() canManage = false;

  @Output() reserve = new EventEmitter<Event>();
  @Output() edit = new EventEmitter<Event>();
  @Output() remove = new EventEmitter<Event>();

  imageUrl: string | null = null;

  constructor(private fileService: FileService) {}

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['event'] && this.event?.id) {
      this.loadEventImage();
    }
  }

  private loadEventImage(): void {
    this.fileService.list('Event', this.event.id).subscribe({
      next: (files) => {
        if (files.length > 0) {
          this.imageUrl = this.fileService.url(files[0].path);
        } else {
          this.imageUrl = null;
        }
      },
      error: () => {
        this.imageUrl = null;
      },
    });
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
