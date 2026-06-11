import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface FileMeta {
  _id?: string;
  id?: string;
  original_name: string;
  path: string;
  mime_type: string;
  size: number;
  related_type: string | null;
  related_id: number | string | null;
  uploaded_by: number | null;
  created_at: string;
  updated_at?: string;
}

@Injectable({ providedIn: 'root' })
export class FileService {
  private readonly apiUrl = `${environment.apiUrl}/files`;

  constructor(private http: HttpClient) {}

  list(relatedType?: string, relatedId?: number): Observable<FileMeta[]> {
    const params: Record<string, string> = {};

    if (relatedType) {
      params['related_type'] = relatedType;
    }

    if (relatedId) {
      params['related_id'] = String(relatedId);
    }

    return this.http.get<FileMeta[]>(this.apiUrl, { params });
  }

  upload(file: File, relatedType?: string, relatedId?: number): Observable<{ meta: FileMeta; url: string }> {
    const formData = new FormData();

    formData.append('file', file);

    if (relatedType) {
      formData.append('related_type', relatedType);
    }

    if (relatedId) {
      formData.append('related_id', String(relatedId));
    }

    return this.http.post<{ meta: FileMeta; url: string }>(this.apiUrl, formData);
  }

  delete(id: string): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/${id}`);
  }

  contentUrl(file: FileMeta): string {
    const fileId = file._id ?? file.id;

    if (!fileId) {
      return '';
    }

    const version = encodeURIComponent(file.updated_at ?? file.created_at ?? String(Date.now()));

    return `${this.apiUrl}/${fileId}/content?v=${version}`;
  }
}
