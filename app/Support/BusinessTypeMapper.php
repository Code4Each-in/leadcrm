<?php

namespace App\Support;

/**
 * Normalizes the raw "type" values Companies House returns into the
 * labels the CRM should display/store. Anything not in the map is
 * passed through unchanged, so unknown Companies House types don't
 * disappear or get overwritten with something wrong.
 */
class BusinessTypeMapper
{
    private const MAP = [
        'ltd' => 'Limited',
    ];

    public static function map(?string $type): ?string
    {
        if ($type === null || trim($type) === '') {
            return $type;
        }

        $key = strtolower(trim($type));

        return self::MAP[$key] ?? $type;
    }
}
