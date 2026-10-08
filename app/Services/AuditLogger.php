<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public static function log(Model $model, string $event, string $description, array $properties = []): void
    {
        $actor = 'POS Cashier / Terminal';

        if (function_exists('auth') && auth()->check()) {
            $actor = auth()->user()->name ?? 'Authenticated User';
        }

        /** @phpstan-ignore-next-line */
        $model->auditLogs()->create([
            'event' => $event,
            'description' => $description,
            'properties' => $properties,
            'actor' => $actor,
            'created_at' => now(),
        ]);
    }
}

