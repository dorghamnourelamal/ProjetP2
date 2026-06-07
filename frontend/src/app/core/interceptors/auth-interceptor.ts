import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError, throwError } from 'rxjs';
import { AuthService } from '../services/auth';

/**
 * Intercepteur HTTP fonctionnel :
 *  - ajoute le token Sanctum (Bearer) sur chaque requête vers l'API,
 *  - efface la session locale en cas de réponse 401 (token expiré/invalide).
 */
export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const authService = inject(AuthService);
  const token = authService.getToken();

  const authReq = token
    ? req.clone({ setHeaders: { Authorization: `Bearer ${token}` } })
    : req;

  return next(authReq).pipe(
    catchError((error) => {
      if (error?.status === 401) {
        authService.clearSession();
      }
      return throwError(() => error);
    }),
  );
};
