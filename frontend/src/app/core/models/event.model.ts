import { Salle } from './salle.model';
import { Reservation } from './reservation.model';

export interface Event {
  id: number;
  titre: string;
  description?: string | null;
  date_event: string;
  heure: string;
  heure_fin: string;
  places_disponibles: number;
  prix: number;
  salle_id: number;
  salle?: Salle;
  reservations?: Reservation[];
  statut?: 'actif' | 'annulé';
  image_url?: string | null;
  created_at?: string;
  updated_at?: string;
}

export interface EventInput {
  titre: string;
  description?: string | null;
  date_event: string;
  heure: string;
  heure_fin: string;
  places_disponibles: number;
  prix: number;
  salle_id: number;
}
