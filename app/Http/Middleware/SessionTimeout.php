<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            $timeout = config('security.session_timeout') * 60;

            $lastActivity = $request->session()->get('last_activity');

            if ($lastActivity && (now()->timestamp - $lastActivity) > $timeout) {

                Auth::logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'email' => 'Your session has expired due to inactivity. Please login again.',
                    ]);
            }

            $request->session()->put(
                'last_activity',
                now()->timestamp
            );
        }

        return $next($request);
    }
}
