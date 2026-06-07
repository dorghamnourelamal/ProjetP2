<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Journal des connexions et actions des utilisateurs (audit / sécurité / analyse d'usage).
 * Stocké en MongoDB sous forme de documents JSON, conformément au cahier des charges SI40.
 *
 * Exemple de document :
 * {
 *   "user_id": 3,
 *   "user_email": "alice@example.com",
 *   "action": "create",        // login, logout, create, update, delete, register...
 *   "entity": "Event",
 *   "entity_id": 12,
 *   "description": "Création de l'événement 'Concert Jazz'",
 *   "ip_address": "127.0.0.1",
 *   "user_agent": "Mozilla/5.0 ...",
 *   "created_at": ISODate(...)
 * }
 */
class ActivityLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'activity_logs';

    protected $fillable = [
        'user_id',
        'user_email',
        'action',
        'entity',
        'entity_id',
        'description',
        'ip_address',
        'user_agent',
    ];
}
