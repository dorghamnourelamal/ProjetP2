import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth';

/**
 * Garde de routage (CanActivate) : protège l'accès aux routes nécessitant une session active.
 * Redirige vers /login en conservant l'URL demandée (returnUrl) si l'utilisateur n'est pas connecté.
 */
export const authGuard: CanActivateFn = (_route, state) => {
  const authService = inject(AuthService);
  const router = inject(Router);

  if (authService.isAuthenticated()) {
    return true;
  }

  return router.createUrlTree(['/login'], { queryParams: { returnUrl: state.url } });
};
