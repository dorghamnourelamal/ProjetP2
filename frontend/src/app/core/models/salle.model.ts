export interface Salle {
  id: number;
  nom: string;
  capacite: number;
  adresse?: string | null;
  events?: EventSummary[];
  created_at?: string;
  updated_at?: string;
}

/** Vue allégée d'un événement, telle que renvoyée imbriquée dans une salle. */
export interface EventSummary {
  id: number;
  titre: string;
  date_event: string;
}
