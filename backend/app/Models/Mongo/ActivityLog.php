<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

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
