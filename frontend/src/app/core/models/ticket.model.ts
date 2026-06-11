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
  qr_code_url?: string | null;
  verification_url?: string | null;
  created_at?: string;
  updated_at?: string;
}

export interface TicketInput {
  reservation_id: number;
  type?: string;
  prix: number;
  statut?: TicketStatut;
}

export interface TicketVerification {
  valid: boolean;
  message: string;
  code?: string;
  statut?: TicketStatut;
  type?: string;
  prix?: number;
  event?: {
    titre?: string | null;
    date_event?: string | null;
    heure?: string | null;
    heure_fin?: string | null;
    salle?: string | null;
  };
  reservation?: {
    id?: number | null;
    nom_client?: string | null;
    email_client?: string | null;
    nombre_places?: number | null;
  };
}
