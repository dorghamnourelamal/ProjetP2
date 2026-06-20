<?php

namespace App\Services;

use App\Models\Mongo\StatEntry;
use Throwable;

class StatRecorder
{
    public function record(string $metric, ?string $entity = null, int|string|null $entityId = null, float|int $value = 1, array $meta = []): void
    {
        try {
            StatEntry::create([
                'metric' => $metric,
                'entity' => $entity,
                'entity_id' => $entityId,
                'value' => $value,
                'meta' => $meta,
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
