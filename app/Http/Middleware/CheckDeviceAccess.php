<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDeviceAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Login page itself is allowed to load on every device.
        if (!$request->routeIs('login.submit')) {
            return $next($request);
        }

        return $next($request);
    }
}
