<?php

namespace App\Http\Middleware;

use App\Services\LoginLogService;
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

                // Close the open Login Logs row, as an explicit logout
                // does (AuthController::logout()) - otherwise a timed-out
                // session shows as never having ended.
                app(LoginLogService::class)->logout(Auth::user());

                Auth::logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return redirect()->route('session.expired');
            }

            $request->session()->put(
                'last_activity',
                now()->timestamp
            );
        }

        return $next($request);
    }
}
