<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

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
