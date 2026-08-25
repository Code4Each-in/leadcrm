<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user) {
            // super admin has full access
            if ($user->role_id === 1) {
                return $next($request);
            }

            // Block inactive users
            if (!$user->status) {
                auth()->logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account has been deactivated.'
                ]);
            }
        }

        return $next($request);
    }
}
