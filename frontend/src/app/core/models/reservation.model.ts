import { Event } from './event.model';
import { Ticket } from './ticket.model';

export interface Reservation {
  id: number;
  nom_client: string;
  email_client: string;
  nombre_places: number;
  event_id: number;
  event?: Event;
  tickets?: Ticket[];
  created_at?: string;
  updated_at?: string;
}

export interface ReservationInput {
  nom_client: string;
  email_client: string;
  nombre_places: number;
  event_id: number;
}
