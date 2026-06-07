import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface FileMeta {
  _id: string;
  original_name: string;
  path: string;
  mime_type: string;
  size: number;
  related_type: string | null;
  related_id: number | null;
  uploaded_by: number | null;
  created_at: string;
}

/**
 * Service de gestion des fichiers (images d'événements, justificatifs...).
 * Le binaire est uploadé vers Laravel ; les métadonnées sont stockées en MongoDB.
 */
@Injectable({ providedIn: 'root' })
export class FileService {
  private readonly apiUrl = `${environment.apiUrl}/files`;

  constructor(private http: HttpClient) {}

  list(relatedType?: string, relatedId?: number): Observable<FileMeta[]> {
    const params: Record<string, string> = {};
    if (relatedType) params['related_type'] = relatedType;
    if (relatedId) params['related_id'] = String(relatedId);

    return this.http.get<FileMeta[]>(this.apiUrl, { params });
  }

  upload(file: File, relatedType?: string, relatedId?: number): Observable<{ meta: FileMeta; url: string }> {
    const formData = new FormData();
    formData.append('file', file);
    if (relatedType) formData.append('related_type', relatedType);
    if (relatedId) formData.append('related_id', String(relatedId));

    return this.http.post<{ meta: FileMeta; url: string }>(this.apiUrl, formData);
  }

  delete(id: string): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
  }

  /** Construit l'URL publique d'un fichier stocké (storage/app/public/...). */
  url(path: string): string {
    return `${environment.storageUrl}/${path}`;
  }
}
