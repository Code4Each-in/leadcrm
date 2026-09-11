<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    public function lookup(?string $ip): array
    {
        $empty = ['city' => null, 'region' => null, 'country' => null, 'country_code' => null];

        if (! $ip || ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $empty;
        }

        return Cache::remember("geoip:{$ip}", now()->addDays(7), function () use ($ip, $empty) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,city,regionName,country,countryCode',
                ]);

                if (! $response->ok() || $response->json('status') !== 'success') {
                    return $empty;
                }

                return [
                    'city'         => $response->json('city'),
                    'region'       => $response->json('regionName'),
                    'country'      => $response->json('country'),
                    'country_code' => $response->json('countryCode'),
                ];
            } catch (\Throwable $e) {
                Log::warning('GeoLocation lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
                return $empty;
            }
        });
    }
}
