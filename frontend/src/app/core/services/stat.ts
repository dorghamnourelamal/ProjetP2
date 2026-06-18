import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface ActivityLogEntry {
  _id: string;
  user_id: number | null;
  user_email: string | null;
  action: string;
  entity: string | null;
  entity_id: number | null;
  description: string;
  created_at: string;
}

export interface StatsOverview {
  totals: {
    events: number;
    reservations: number;
    tickets: number;
    places_reservees: number;
    chiffre_affaires: number;
  };

  top_events_by_reservations: Array<{
    event_id: number;
    total_places: number;
    total_reservations: number;
    event?: {
      id: number;
      titre: string;
    };
  }>;

  tickets_by_status: Array<{
    statut: string;
    total: number;
  }>;

  places_by_salle: Array<{
    salle_id: number;
    salle_nom: string;
    capacite: number;
    places_reservees: number;
    taux_occupation: number;
  }>;

  metrics_by_type: Array<{
    _id: string;
    total: number;
    count: number;
  }>;

  recent_activity: ActivityLogEntry[];
}

@Injectable({ providedIn: 'root' })
export class StatService {
  private readonly apiUrl = `${environment.apiUrl}/stats`;

  constructor(private http: HttpClient) {}

  overview(): Observable<StatsOverview> {
    return this.http.get<StatsOverview>(`${this.apiUrl}/overview`);
  }

  activity(filters: { action?: string; user_id?: number } = {}): Observable<{ data: ActivityLogEntry[] }> {
    const params: Record<string, string> = {};

    if (filters.action) {
      params['action'] = filters.action;
    }

    if (filters.user_id) {
      params['user_id'] = String(filters.user_id);
    }

    return this.http.get<{ data: ActivityLogEntry[] }>(`${this.apiUrl}/activity`, { params });
  }
}
