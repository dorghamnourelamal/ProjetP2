import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth';
import { UserRole } from '../models/user.model';

/**
 * Fabrique de garde basée sur les rôles : ->canActivate: [roleGuard(['admin'])]
 * Vérifie l'authentification ET l'appartenance au(x) rôle(s) requis pour la route.
 * Redirige vers /login si non connecté, ou vers /forbidden si rôle insuffisant.
 */
export const roleGuard = (allowedRoles: UserRole[]): CanActivateFn => {
  return (_route, state) => {
    const authService = inject(AuthService);
    const router = inject(Router);

    if (!authService.isAuthenticated()) {
      return router.createUrlTree(['/login'], { queryParams: { returnUrl: state.url } });
    }

    const role = authService.currentUser()?.role;

    if (role && allowedRoles.includes(role)) {
      return true;
    }

    return router.createUrlTree(['/forbidden']);
  };
};
