import { Reservation } from './reservation.model';

export type TicketStatut = 'valide' | 'utilisé' | 'annulé';

export interface Ticket {
  id: number;
  reservation_id: number;
  code: string;
  type: string;
  prix: number;
  statut: TicketStatut;
  reservation?: Reservation;
  created_at?: string;
  updated_at?: string;
}

export interface TicketInput {
  reservation_id: number;
  type?: string;
  prix: number;
  statut?: TicketStatut;
}
