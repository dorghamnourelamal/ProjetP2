import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Salle } from '../models/salle.model';

export interface SalleInput {
  nom: string;
  capacite: number;
  adresse?: string | null;
}

@Injectable({ providedIn: 'root' })
export class SalleService {
  private readonly apiUrl = `${environment.apiUrl}/salles`;

  constructor(private http: HttpClient) {}

  list(): Observable<Salle[]> {
    return this.http.get<Salle[]>(this.apiUrl);
  }

  get(id: number): Observable<Salle> {
    return this.http.get<Salle>(`${this.apiUrl}/${id}`);
  }

  create(payload: SalleInput): Observable<Salle> {
    return this.http.post<Salle>(this.apiUrl, payload);
  }

  update(id: number, payload: SalleInput): Observable<Salle> {
    return this.http.put<Salle>(`${this.apiUrl}/${id}`, payload);
  }

  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
  }
}
