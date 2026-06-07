<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    protected $fillable = [
        'nom',
        'capacite',
        'adresse'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
