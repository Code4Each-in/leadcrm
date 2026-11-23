<?php

namespace App\Http\Middleware;

use App\Services\AttendanceService;
use App\Services\LoginLogService;
use App\Services\ProxyLoginService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    /** How often (seconds) a request also refreshes the attendance row. */
    private const ATTENDANCE_TOUCH_INTERVAL = 60;

    public function __construct(
        protected AttendanceService $attendance,
        protected ProxyLoginService $proxy,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            // During a proxy login the admin is the one working - their
            // attendance / login log is what activity and timeouts touch,
            // never the user being viewed.
            $bookkeepingUser = $this->proxy->bookkeepingUser();

            $timeout = config('security.session_timeout') * 60;

            $lastActivity = $request->session()->get('last_activity');

            // Only real inactivity ends the session, and only when a timeout
            // is configured (security.session_timeout, 0 = off). The layout's
            // activity heartbeat keeps last_activity fresh while someone is
            // working on a single page, and a break in progress never times out.
            if ($timeout > 0
                && $lastActivity
                && (now()->timestamp - $lastActivity) > $timeout
                && !$this->attendance->isOnBreak($bookkeepingUser)) {

                $user = $bookkeepingUser;

                // Close the open Login Logs row, as an explicit logout
                // does (AuthController::logout()) - otherwise a timed-out
                // session shows as never having ended.
                app(LoginLogService::class)->logout($user);

                // Time Out at the last activity rather than now - unless
                // another session of theirs is still in use.
                $this->attendance->clockOutIfIdle($user);

                Auth::logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                // Straight back to the login page - no "session expired"
                // notice.
                if ($request->expectsJson()) {
                    return response()->json([
                        'message'  => 'Unauthenticated.',
                        'redirect' => route('login'),
                    ], 401);
                }

                return redirect()->route('login');
            }

            $request->session()->put(
                'last_activity',
                now()->timestamp
            );

            // Keep attendance in step with activity: opens the day if
            // there isn't one yet (sessions that predate auto Time In,
            // crossing midnight) and records when the user was last seen.
            $touchedAt = $request->session()->get('attendance_touched_at');

            if (!$touchedAt || (now()->timestamp - $touchedAt) >= self::ATTENDANCE_TOUCH_INTERVAL) {
                $this->attendance->recordActivity($bookkeepingUser);
                $request->session()->put('attendance_touched_at', now()->timestamp);
            }
        }

        return $next($request);
    }
}
