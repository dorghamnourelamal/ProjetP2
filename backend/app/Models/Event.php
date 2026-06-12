<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'date_event',
        'heure',
        'heure_fin',
        'places_disponibles',
        'prix',
        'salle_id',
        'statut',
    ];

    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
