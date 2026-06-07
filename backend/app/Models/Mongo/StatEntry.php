<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Indicateurs d'utilisation/statistiques collectés au fil de l'eau
 * (consultations d'événements, réservations, ventes de billets...).
 * Permet de générer des rapports/visualisations (ex: export vers Power BI).
 *
 * Exemple de document :
 * {
 *   "metric": "event_view",       // event_view, reservation_created, ticket_sold...
 *   "entity": "Event",
 *   "entity_id": 5,
 *   "value": 1,
 *   "meta": { "salle_id": 2 },
 *   "created_at": ISODate(...)
 * }
 */
class StatEntry extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'stat_entries';

    protected $fillable = [
        'metric',
        'entity',
        'entity_id',
        'value',
        'meta',
    ];
}
