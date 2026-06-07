<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de contrôle d'accès basé sur les rôles.
 * Utilisation dans les routes : ->middleware('role:admin')
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            return response()->json([
                'message' => 'Accès refusé : rôle insuffisant.',
            ], 403);
        }

        return $next($request);
    }
}
