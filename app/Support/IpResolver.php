<?php

namespace App\Support;

use Illuminate\Http\Request;

class IpResolver
{
    /**
     * Checked in priority order before falling back to Laravel's own
     * $request->ip() (which already respects your trusted-proxy config).
     */
    protected static array $headers = [
        'CF-Connecting-IP',  // Cloudflare
        'True-Client-IP',    // Cloudflare Enterprise / Akamai
        'X-Real-IP',
        'X-Forwarded-For',
    ];

    public static function resolve(Request $request): ?string
    {
        $candidates = [];

        foreach (static::$headers as $header) {
            $value = $request->headers->get($header);

            if (! $value) {
                continue;
            }

            // X-Forwarded-For can be a comma-separated chain; client IP is first.
            foreach (explode(',', $value) as $part) {
                $ip = trim($part);

                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    $candidates[] = $ip;
                }
            }
        }

        if ($request->ip() && filter_var($request->ip(), FILTER_VALIDATE_IP)) {
            $candidates[] = $request->ip();
        }

        if (empty($candidates)) {
            return null;
        }

        // Prefer a public IPv4 wherever it appears in the candidate list.
        foreach ($candidates as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && static::isPublic($ip)) {
                return $ip;
            }
        }

        // No public IPv4 — accept any IPv4 (e.g. local/dev environments).
        foreach ($candidates as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $ip;
            }
        }

        // Only IPv6 was ever supplied — keep it, it can't be turned into IPv4.
        return $candidates[0];
    }

    protected static function isPublic(string $ip): bool
    {
        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
