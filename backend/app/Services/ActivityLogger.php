<?php

namespace App\Services;

use App\Models\Mongo\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

/**
 * Service centralisant l'écriture des journaux d'activité dans MongoDB.
 * Utilisé par les contrôleurs (auth, CRUD...) pour tracer connexions et actions.
 */
class ActivityLogger
{
    public function log(?User $user, string $action, string $description, ?Request $request = null, ?string $entity = null, int|string|null $entityId = null): void
    {
        try {
            ActivityLog::create([
                'user_id' => $user?->id,
                'user_email' => $user?->email,
                'action' => $action,
                'entity' => $entity,
                'entity_id' => $entityId,
                'description' => $description,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
            ]);
        } catch (Throwable $e) {
            // On ne bloque jamais une requête métier si MongoDB est indisponible.
            report($e);
        }
    }
}
