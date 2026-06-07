<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Métadonnées des fichiers liés au métier (images d'événements, justificatifs, etc.).
 * Le fichier binaire est stocké via le disque Laravel (storage/app/public/...),
 * seules les métadonnées (document JSON) sont conservées dans MongoDB :
 * séparation entre données relationnelles (MySQL) et non structurées (MongoDB).
 *
 * Exemple de document :
 * {
 *   "original_name": "affiche.png",
 *   "path": "uploads/events/affiche_1718000000.png",
 *   "mime_type": "image/png",
 *   "size": 204800,
 *   "related_type": "Event",
 *   "related_id": 5,
 *   "uploaded_by": 1,
 *   "created_at": ISODate(...)
 * }
 */
class FileMeta extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'file_metadata';

    protected $fillable = [
        'original_name',
        'path',
        'mime_type',
        'size',
        'related_type',
        'related_id',
        'uploaded_by',
    ];
}
