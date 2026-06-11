import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Ticket, TicketInput, TicketVerification } from '../models/ticket.model';

@Injectable({ providedIn: 'root' })
export class TicketService {
  private readonly apiUrl = `${environment.apiUrl}/tickets`;

  constructor(private http: HttpClient) {}

  list(): Observable<Ticket[]> {
    return this.http.get<Ticket[]>(this.apiUrl);
  }

  get(id: number): Observable<Ticket> {
    return this.http.get<Ticket>(`${this.apiUrl}/${id}`);
  }

  create(payload: TicketInput): Observable<Ticket> {
    return this.http.post<Ticket>(this.apiUrl, payload);
  }

  update(id: number, payload: Partial<TicketInput>): Observable<Ticket> {
    return this.http.put<Ticket>(`${this.apiUrl}/${id}`, payload);
  }

  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
  }

  verifyByCode(code: string): Observable<TicketVerification> {
    return this.http.get<TicketVerification>(`${this.apiUrl}/verify/${code}`);
  }

  useByCode(code: string): Observable<TicketVerification> {
    return this.http.patch<TicketVerification>(`${this.apiUrl}/verify/${code}/use`, {});
  }
}
