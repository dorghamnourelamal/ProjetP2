<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'reservation_id',
        'code',
        'type',
        'prix',
        'statut',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
