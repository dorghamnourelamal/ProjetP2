import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Event, EventInput } from '../models/event.model';

/**
 * Service métier "Événements" : externalise tous les appels HTTP vers l'API Laravel
 * et expose des Observables RxJS consommés par les composants (architecture en couches).
 */
@Injectable({ providedIn: 'root' })
export class EventService {
  private readonly apiUrl = `${environment.apiUrl}/events`;

  constructor(private http: HttpClient) {}

  list(): Observable<Event[]> {
    return this.http.get<Event[]>(this.apiUrl);
  }

  get(id: number): Observable<Event> {
    return this.http.get<Event>(`${this.apiUrl}/${id}`);
  }

  create(payload: EventInput): Observable<Event> {
    return this.http.post<Event>(this.apiUrl, payload);
  }

  update(id: number, payload: EventInput): Observable<Event> {
    return this.http.put<Event>(`${this.apiUrl}/${id}`, payload);
  }

  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
  }
}
