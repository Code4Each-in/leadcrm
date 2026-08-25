<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (
            $user &&
            $user->role &&
            strtolower(trim($user->role->name)) === 'super admin'
        ) {
            return $next($request);
        }

    return response()->view('unauthorized', [], 403);
    }
}
