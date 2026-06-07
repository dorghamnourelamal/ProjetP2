import { Salle } from './salle.model';
import { Reservation } from './reservation.model';

export interface Event {
  id: number;
  titre: string;
  description?: string | null;
  date_event: string;
  heure: string;
  places_disponibles: number;
  prix: number;
  salle_id: number;
  salle?: Salle;
  reservations?: Reservation[];
  created_at?: string;
  updated_at?: string;
}

/** Forme utilisée par le formulaire de création/édition (sans champs calculés/relations). */
export interface EventInput {
  titre: string;
  description?: string | null;
  date_event: string;
  heure: string;
  places_disponibles: number;
  prix: number;
  salle_id: number;
}
