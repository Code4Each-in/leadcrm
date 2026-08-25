<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && strtolower($user->role->name) === 'mis user') {
            return $next($request);
        }

        return response()->view('unauthorized', [], 403);
    }
}
