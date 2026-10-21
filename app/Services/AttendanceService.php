<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Time In / Time Out are driven by authentication, not buttons:
 *
 *  - login (or any activity with no open day)   -> Time In
 *  - logout                                     -> Time Out now
 *  - session expired through inactivity         -> Time Out at the last
 *                                                  recorded activity
 *
 * One row per user per day. Time Out marks the day Timed Out, but it isn't
 * final: logging back in the same day resumes it, and the time between
 * Time Out and the new login is recorded as an "Other" break so it never
 * counts as worked. Other breaks stay manual (AttendanceController::punch()).
 */
class AttendanceService
{
    private const TIMEZONE = 'Europe/London';

    /** Break reason recorded for the gap between a Time Out and the next login. */
    public const LOGGED_OUT_BREAK_REASON = 'Other';

    /** Note on that break so it reads as time spent logged out, not a manual break. */
    public const LOGGED_OUT_BREAK_NOTES = 'Has not logged in again yet';

    /** Called on login: start (or resume) today's attendance. */
    public function clockIn(User $user): Attendance
    {
        return $this->recordActivity($user);
    }

    /**
     * Mark the user as active right now, opening today's attendance if
     * they don't have one yet, or resuming it if it was timed out (see
     * resume()). Also closes any earlier day left open (e.g. a session
     * that expired with nobody coming back).
     */
    public function recordActivity(User $user): Attendance
    {
        $now = Carbon::now(self::TIMEZONE);

        $this->closeStaleFor($user, $now);

        $attendance = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereDate('date', $now->toDateString())
            ->first();

        if (!$attendance) {
            return Attendance::create([
                'user_id'          => $user->id,
                'date'             => $now->toDateString(),
                'time_in'          => $now,
                'last_activity_at' => $now,
            ]);
        }

        if ($attendance->time_out) {
            return $this->resume($attendance, $now);
        }

        $attendance->last_activity_at = $now;
        $attendance->save();

        return $attendance;
    }

    /**
     * Back after a Time Out (logout or idle expiry) on the same day: the
     * time away becomes an "Other" break, and the day carries on from where
     * it stopped - worked time is time_in -> now minus breaks, so the timer
     * resumes at its previous value.
     */
    private function resume(Attendance $attendance, Carbon $now): Attendance
    {
        DB::transaction(function () use ($attendance, $now) {
            // Locked re-read: two requests landing together right after login
            // must not both record the gap.
            $timedOut = Attendance::whereKey($attendance->id)->lockForUpdate()->toBase()->value('time_out');

            if (!$timedOut) {
                return;
            }

            // Raw column = Europe/London wall clock, as every attendance time is stored.
            $timedOutAt = Carbon::createFromFormat('Y-m-d H:i:s', $timedOut, self::TIMEZONE);

            if ($now->gt($timedOutAt)) {
                $attendance->breaks()->create([
                    'reason'   => self::LOGGED_OUT_BREAK_REASON,
                    'notes'    => self::LOGGED_OUT_BREAK_NOTES,
                    'start_at' => $timedOutAt,
                    'end_at'   => $now,
                ]);
            }

            $attendance->update([
                'time_out'         => null,
                'last_activity_at' => $now,
            ]);
        });

        return $attendance->refresh()->load('breaks');
    }

    /** Called on an explicit logout: Time Out now. */
    public function clockOut(User $user): void
    {
        $now = Carbon::now(self::TIMEZONE);

        Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereNull('time_out')
            ->whereNotNull('time_in')
            ->get()
            ->each(fn (Attendance $a) => $this->close($a, $now));
    }

    /**
     * Called when a session expires: close the user's open day at their
     * last activity - but only if they really have gone idle. Another
     * session (second browser/device) still in use keeps it open.
     */
    public function clockOutIfIdle(User $user): void
    {
        $this->closeStaleFor($user, Carbon::now(self::TIMEZONE));
    }

    /** True while the user is inside a break they haven't ended yet. */
    public function isOnBreak(User $user): bool
    {
        return Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::now(self::TIMEZONE)->toDateString())
            ->whereNull('time_out')
            ->whereHas('breaks', fn ($q) => $q->whereNull('end_at'))
            ->exists();
    }

    /**
     * Close every open day that has gone idle, for all users. Run on a
     * schedule and before attendance reports are built, so a user whose
     * session expired and never came back doesn't keep accruing hours.
     */
    public function closeIdle(): int
    {
        $now = Carbon::now(self::TIMEZONE);
        $closed = 0;

        Attendance::with('breaks')
            ->whereNull('time_out')
            ->whereNotNull('time_in')
            ->get()
            ->each(function (Attendance $a) use ($now, &$closed) {
                if ($this->isStale($a, $now)) {
                    $this->close($a, $this->lastActivity($a));
                    $closed++;
                }
            });

        return $closed;
    }

    private function closeStaleFor(User $user, Carbon $now): void
    {
        Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereNull('time_out')
            ->whereNotNull('time_in')
            ->get()
            ->filter(fn (Attendance $a) => $this->isStale($a, $now))
            ->each(fn (Attendance $a) => $this->close($a, $this->lastActivity($a)));
    }

    /**
     * An open day is stale once it's from an earlier date, or (when an
     * inactivity timeout is configured) nothing has happened on it for
     * longer than that. A day with a break in progress isn't stale - the
     * user said they were stepping away and will end the break themselves.
     */
    private function isStale(Attendance $a, Carbon $now): bool
    {
        if ($a->date->toDateString() < $now->toDateString()) {
            return true;
        }

        if ($a->breaks->contains(fn ($b) => $b->end_at === null)) {
            return false;
        }

        $timeoutSeconds = (int) config('security.session_timeout') * 60;

        if ($timeoutSeconds <= 0) {
            return false;
        }

        return $this->lastActivity($a)->getTimestamp() < $now->getTimestamp() - $timeoutSeconds;
    }

    private function lastActivity(Attendance $a): Carbon
    {
        return $a->last_activity_at ?? $a->updated_at ?? $a->time_in;
    }

    private function close(Attendance $a, Carbon $at): void
    {
        // A day never runs past its own midnight, nor ends before it began.
        $endOfDay = Carbon::parse($a->date->toDateString() . ' 23:59:59', self::TIMEZONE);

        if ($at->gt($endOfDay)) {
            $at = $endOfDay;
        }
        if ($at->lt($a->time_in)) {
            $at = $a->time_in;
        }

        foreach ($a->breaks as $break) {
            if ($break->end_at === null) {
                $break->update(['end_at' => $at->lt($break->start_at) ? $break->start_at : $at]);
            }
        }

        $a->update(['time_out' => $at]);
    }
}
