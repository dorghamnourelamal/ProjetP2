<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

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
