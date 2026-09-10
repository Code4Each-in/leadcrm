<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
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

        if (!$attendance) {
            $attendance = Attendance::create([
                'user_id' => auth()->id(),
                'date'    => $now->toDateString(),
                'time_in' => $now,
            ]);
            return response()->json(array_merge(
                ['success' => true, 'message' => $this->messageFor('time_in')],
                $this->payload($attendance)
            ));
        }

        $action = $this->nextActionFor($attendance);

        if ($action === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Attendance for today is already completed.',
            ], 422);
        }

        // Explicit column update per action. Easy to read, nothing hidden
        // in the model, nothing assigned via a dynamic {$action} property.
        match ($action) {
            'break1_start' => $attendance->update(['break1_start' => $now]),
            'break1_end'   => $attendance->update(['break1_end'   => $now]),
            'break2_start' => $attendance->update(['break2_start' => $now]),
            'break2_end'   => $attendance->update(['break2_end'   => $now]),
            'time_out'     => $attendance->update(['time_out'     => $now]),
            default        => null,
        };

        $attendance->refresh();

        return response()->json(array_merge(
            ['success' => true, 'message' => $this->messageFor($action)],
            $this->payload($attendance)
        ));
    }

    public function report(Request $request)
    {
        $now = Carbon::now(self::TIMEZONE);

        $query = Attendance::query();

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
                'total_hours'    => $this->formatDuration($this->workedSeconds($a, $now)),
            ];
        });

        return response()->json(['data' => $rows]);
    }


    private function todayRecord(): ?Attendance
    {
        return Attendance::where('user_id', auth()->id())
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

   
    private function seconds(?Carbon $start, ?Carbon $end): int
    {
        if (!$start || !$end) {
            return 0;
        }

        return max(0, $end->getTimestamp() - $start->getTimestamp());
    }

 
    private function breakSeconds(?Attendance $a, Carbon $now): int
    {
        if (!$a) {
            return 0;
        }

        $break1Start = $this->ist($a, 'break1_start');
        $break1End   = $this->ist($a, 'break1_end');
        $break2Start = $this->ist($a, 'break2_start');
        $break2End   = $this->ist($a, 'break2_end');

        return $this->seconds($break1Start, $break1End ?: $now)
             + $this->seconds($break2Start, $break2End ?: $now);
    }


    private function workedSeconds(?Attendance $a, Carbon $now): int
    {
        if (!$a) {
            return 0;
        }

        $timeIn      = $this->ist($a, 'time_in');
        $timeOut     = $this->ist($a, 'time_out');
        $break1Start = $this->ist($a, 'break1_start');
        $break1End   = $this->ist($a, 'break1_end');
        $break2Start = $this->ist($a, 'break2_start');
        $break2End   = $this->ist($a, 'break2_end');

        if (!$timeIn) {
            return 0;
        }

        $onBreak1 = $break1Start && !$break1End;
        $onBreak2 = $break2Start && !$break2End;

        $clockEnd = match (true) {
            (bool) $timeOut => $timeOut,
            $onBreak1        => $break1Start,
            $onBreak2        => $break2Start,
            default          => $now,
        };

        $gross = $this->seconds($timeIn, $clockEnd);

        $completedBreaks = $this->seconds($break1Start, $break1End)
                          + $this->seconds($break2Start, $break2End);

        return max(0, $gross - $completedBreaks);
    }

    /**
     * Current status, derived directly from the raw columns.
     */
    private function statusFor(?Attendance $a): string
    {
        if (!$a || !$a->time_in) {
            return 'not_started';
        }
        if ($a->time_out) {
            return 'completed';
        }
        if (($a->break1_start && !$a->break1_end) || ($a->break2_start && !$a->break2_end)) {
            return 'on_break';
        }

        return 'working';
    }


    private function nextActionFor(?Attendance $a): string
    {
        if (!$a || !$a->time_in) {
            return 'time_in';
        }
        if ($a->break1_start && !$a->break1_end) {
            return 'break1_end';
        }
        if ($a->break2_start && !$a->break2_end) {
            return 'break2_end';
        }
        if ($a->time_out) {
            return 'completed';
        }
        if (!$a->break1_start) {
            return 'break1_start';
        }
        if (!$a->break2_start) {
            return 'break2_start';
        }

        return 'time_out';
    }

    private function buttonLabel(string $nextAction): string
    {
        return match ($nextAction) {
            'time_in' => 'Time In',
            'break1_start', 'break2_start' => 'Start Break',
            'break1_end', 'break2_end' => 'End Break',
            'time_out' => 'Time Out',
            default => 'Completed',
        };
    }

    private function messageFor(string $action): string
    {
        return match ($action) {
            'time_in' => 'Checked in. Have a productive day!',
            'break1_start', 'break2_start' => 'Break started. Enjoy!',
            'break1_end', 'break2_end' => 'Break ended. Welcome back!',
            'time_out' => 'Checked out. See you tomorrow!',
            default => 'Updated.',
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
        $nextAction = $this->nextActionFor($attendance);
        $worked     = $this->workedSeconds($attendance, $now);
        $break      = $this->breakSeconds($attendance, $now);

        return [
            'status'           => $status,
            'next_action'      => $nextAction,
            'button_label'     => $this->buttonLabel($nextAction),
            'time_in'          => optional($this->ist($attendance, 'time_in'))->format('h:i A'),
            'time_out'         => optional($this->ist($attendance, 'time_out'))->format('h:i A'),
            'is_on_break'      => $status === 'on_break',
            'break_seconds'    => $break,
            'worked_seconds'   => $worked,
            'worked_formatted' => $this->formatDuration($worked),
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
 
        $query = Attendance::query()->with('user:id,name');
 
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
                'total_hours'    => $this->formatDuration($this->workedSeconds($a, $now)),
            ];
        });
 
        return response()->json(['data' => $rows]);
    }
}