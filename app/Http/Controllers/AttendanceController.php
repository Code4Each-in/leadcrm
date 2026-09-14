<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    private const TIMEZONE = 'Asia/Kolkata';


    public function index()
    {
        return view('attendance.index');
    }

    public function status()
    {
        return response()->json($this->payload($this->todayRecord()))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function punch(Request $request)
    {
        $now        = Carbon::now(self::TIMEZONE);
        $attendance = $this->todayRecord();
        $status     = $this->statusFor($attendance);

        $action  = $request->input('action');
        $allowed = array_column($this->availableActionsFor($status), 'action');

        if (!$action || !in_array($action, $allowed, true)) {
            return response()->json([
                'success' => false,
                'message' => 'That action is not available right now.',
            ], 422);
        }

        switch ($action) {
            case 'time_in':
                $attendance = Attendance::create([
                    'user_id' => auth()->id(),
                    'date'    => $now->toDateString(),
                    'time_in' => $now,
                ]);
                break;

            case 'start_break':
                // Every break must have a reason. No reason, no break — enforced
                // here regardless of what the client sends.
                $validated = $request->validate([
                    'reason' => ['required', 'string', 'max:255'],
                ]);

                $attendance->breaks()->create([
                    'reason'   => trim($validated['reason']),
                    'start_at' => $now,
                ]);
                break;

            case 'end_break':
                $openBreak = $attendance->breaks()
                    ->whereNull('end_at')
                    ->latest('start_at')
                    ->first();

                if ($openBreak) {
                    $openBreak->update(['end_at' => $now]);
                }
                break;

            case 'time_out':
                $attendance->update(['time_out' => $now]);
                break;
        }

        $attendance->refresh();
        $attendance->load('breaks');

        return response()->json(array_merge(
            ['success' => true, 'message' => $this->messageFor($action)],
            $this->payload($attendance)
        ));
    }

    public function report(Request $request)
    {
        $now = Carbon::now(self::TIMEZONE);

        $query = Attendance::query()->with('breaks');

        // Admins can pass ?user_id= to view someone else's attendance.
        if ($request->filled('user_id') && $request->user()->isAdmin()) {
            $query->where('user_id', $request->user_id);
        } else {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $rows = $query->orderByDesc('date')->get()->map(function (Attendance $a) use ($now) {
            return [
                'date'           => $a->date->format('d M Y'),
                'time_in'        => optional($this->ist($a, 'time_in'))->format('h:i A') ?? '-',
                'time_out'       => optional($this->ist($a, 'time_out'))->format('h:i A') ?? '-',
                'break_duration' => $this->formatDuration($this->breakSeconds($a, $now)),
                'break_count'    => $a->breaks->count(),
                'breaks'         => $this->breakDetails($a, $now),
                'total_hours'    => $this->formatDuration($this->workedSeconds($a, $now)),
            ];
        });

        return response()->json(['data' => $rows]);
    }


    /**
     * Per-break rows (start, end, duration, reason) for the "view breaks" modal.
     */
    private function breakDetails(?Attendance $a, Carbon $now): array
    {
        if (!$a) {
            return [];
        }

        return $a->breaks->map(function (AttendanceBreak $b) use ($now) {
            $start = $this->istBreak($b, 'start_at');
            $end   = $this->istBreak($b, 'end_at');

            return [
                'start'    => optional($start)->format('h:i A') ?? '-',
                'end'      => $end ? $end->format('h:i A') : 'Ongoing',
                'duration' => $this->formatDuration($this->seconds($start, $end ?: $now)),
                'reason'   => $b->reason ?: '-',
            ];
        })->values()->all();
    }


    private function todayRecord(): ?Attendance
    {
        return Attendance::with('breaks')
            ->where('user_id', auth()->id())
            ->where('date', Carbon::now(self::TIMEZONE)->toDateString())
            ->first();
    }


    private function ist(?Attendance $a, string $column): ?Carbon
    {
        if (!$a) {
            return null;
        }

        $raw = $a->getRawOriginal($column);

        if (!$raw) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $raw, self::TIMEZONE);
    }

    private function istBreak(?AttendanceBreak $b, string $column): ?Carbon
    {
        if (!$b) {
            return null;
        }

        $raw = $b->getRawOriginal($column);

        if (!$raw) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $raw, self::TIMEZONE);
    }


    private function seconds(?Carbon $start, ?Carbon $end): int
    {
        if (!$start || !$end) {
            return 0;
        }

        return max(0, $end->getTimestamp() - $start->getTimestamp());
    }


    /**
     * The break the user is currently inside of, if any (end_at not yet set).
     */
    private function openBreak(?Attendance $a): ?AttendanceBreak
    {
        if (!$a) {
            return null;
        }

        foreach ($a->breaks as $break) {
            if (!$break->getRawOriginal('end_at')) {
                return $break;
            }
        }

        return null;
    }


    private function breakSeconds(?Attendance $a, Carbon $now): int
    {
        if (!$a) {
            return 0;
        }

        $total = 0;

        foreach ($a->breaks as $break) {
            $start = $this->istBreak($break, 'start_at');
            $end   = $this->istBreak($break, 'end_at');
            $total += $this->seconds($start, $end ?: $now);
        }

        return $total;
    }


    private function workedSeconds(?Attendance $a, Carbon $now): int
    {
        if (!$a) {
            return 0;
        }

        $timeIn  = $this->ist($a, 'time_in');
        $timeOut = $this->ist($a, 'time_out');

        if (!$timeIn) {
            return 0;
        }

        $openBreak = $this->openBreak($a);

        $clockEnd = match (true) {
            (bool) $timeOut => $timeOut,
            (bool) $openBreak => $this->istBreak($openBreak, 'start_at'),
            default => $now,
        };

        $gross = $this->seconds($timeIn, $clockEnd);

        $completedBreaks = 0;
        foreach ($a->breaks as $break) {
            if ($break->getRawOriginal('end_at')) {
                $completedBreaks += $this->seconds(
                    $this->istBreak($break, 'start_at'),
                    $this->istBreak($break, 'end_at')
                );
            }
        }

        return max(0, $gross - $completedBreaks);
    }

    /**
     * Current status, derived directly from the raw columns / breaks.
     */
    private function statusFor(?Attendance $a): string
    {
        if (!$a || !$a->time_in) {
            return 'not_started';
        }
        if ($a->time_out) {
            return 'completed';
        }
        if ($this->openBreak($a)) {
            return 'on_break';
        }

        return 'working';
    }


    /**
     * Which punch actions are valid from the current status. While "working"
     * the user can freely choose to start another break or clock out — breaks
     * are no longer capped at two.
     */
    private function availableActionsFor(string $status): array
    {
        return match ($status) {
            'not_started' => [
                ['action' => 'time_in', 'label' => 'Time In'],
            ],
            'working' => [
                ['action' => 'start_break', 'label' => 'Start Break'],
                ['action' => 'time_out', 'label' => 'Time Out'],
            ],
            'on_break' => [
                ['action' => 'end_break', 'label' => 'End Break'],
            ],
            default => [],
        };
    }

    private function messageFor(string $action): string
    {
        return match ($action) {
            'time_in'     => 'Checked in. Have a productive day!',
            'start_break' => 'Break started. Enjoy!',
            'end_break'   => 'Break ended. Welcome back!',
            'time_out'    => 'Checked out. See you tomorrow!',
            default       => 'Updated.',
        };
    }

    /**
     * "1h 05m" style formatting. Plain function, not a model static.
     */
    private function formatDuration(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);

        return sprintf('%dh %02dm', $h, $m);
    }


    private function payload(?Attendance $attendance): array
    {
        $now        = Carbon::now(self::TIMEZONE);
        $status     = $this->statusFor($attendance);
        $actions    = $this->availableActionsFor($status);
        $worked     = $this->workedSeconds($attendance, $now);
        $break      = $this->breakSeconds($attendance, $now);
        $openBreak  = $this->openBreak($attendance);

        return [
            'status'               => $status,
            'available_actions'    => $actions,
            'time_in'              => optional($this->ist($attendance, 'time_in'))->format('h:i A'),
            'time_out'             => optional($this->ist($attendance, 'time_out'))->format('h:i A'),
            'is_on_break'          => $status === 'on_break',
            'current_break_reason' => $openBreak?->reason,
            'break_seconds'        => $break,
            'worked_seconds'       => $worked,
            'worked_formatted'     => $this->formatDuration($worked),
        ];
    }

    public function adminIndex()
    {
        $employees = User::orderBy('name')->get(['id', 'name']);
        return view('attendance.admin', compact('employees'));
    }


    public function adminReport(Request $request)
    {
        $now = Carbon::now(self::TIMEZONE);

        $query = Attendance::query()->with(['user:id,name', 'breaks']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $rows = $query->orderByDesc('date')->get()->map(function (Attendance $a) use ($now) {
            return [
                'employee'       => $a->user->name ?? '-',
                'date'           => $a->date->format('d M Y'),
                'time_in'        => optional($this->ist($a, 'time_in'))->format('h:i A') ?? '-',
                'time_out'       => optional($this->ist($a, 'time_out'))->format('h:i A') ?? '-',
                'break_duration' => $this->formatDuration($this->breakSeconds($a, $now)),
                'break_count'    => $a->breaks->count(),
                'breaks'         => $this->breakDetails($a, $now),
                'total_hours'    => $this->formatDuration($this->workedSeconds($a, $now)),
            ];
        });

        return response()->json(['data' => $rows]);
    }
}