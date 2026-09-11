<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    private const TIMEZONE = 'Asia/Kolkata';

    protected $fillable = [
        'user_id', 'date', 'time_in', 'time_out',
        'break1_start', 'break1_end', 'break2_start', 'break2_end',
    ];

    protected $casts = [ 
        'date'         => 'date',
        'time_in'      => 'datetime',
        'time_out'     => 'datetime',
        'break1_start' => 'datetime',
        'break1_end'   => 'datetime',
        'break2_start' => 'datetime',
        'break2_end'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

  
    public function getNextActionAttribute(): string
    {
        if (!$this->time_in) return 'time_in';
        if (!$this->break1_start) return 'break1_start';
        if (!$this->break1_end) return 'break1_end';
        if (!$this->break2_start) return 'break2_start';
        if (!$this->break2_end) return 'break2_end';
        if (!$this->time_out) return 'time_out';
        return 'completed';
    }

 
    public function getStatusAttribute(): string
    {
        if (!$this->time_in) return 'not_started';
        if ($this->time_out) return 'completed';
        if ($this->break1_start && !$this->break1_end) return 'on_break';
        if ($this->break2_start && !$this->break2_end) return 'on_break';
        return 'working';
    }

    /** Total break time in seconds (handles a break that was never closed). */
    public function getBreakSecondsAttribute(): int
    {
        $seconds = 0;

        if ($this->break1_start) {
            $start = $this->wallClock($this->break1_start);
            $end   = $this->wallClock($this->break1_end) ?? Carbon::now(self::TIMEZONE);
            $seconds += $end->diffInSeconds($start);
        }
        if ($this->break2_start) {
            $start = $this->wallClock($this->break2_start);
            $end   = $this->wallClock($this->break2_end) ?? Carbon::now(self::TIMEZONE);
            $seconds += $end->diffInSeconds($start);
        }

        return $seconds;
    }

    /** Net worked time in seconds = (time_out - time_in) - breaks. */
    public function getWorkedSecondsAttribute(): int
    {
        if (!$this->time_in) return 0;

        $start = $this->wallClock($this->time_in);
        $end   = $this->wallClock($this->time_out) ?? Carbon::now(self::TIMEZONE);

        $total = $end->diffInSeconds($start);

        return max(0, $total - $this->break_seconds);
    }

    public function getLiveWorkedSecondsAttribute(): int
    {
        if (!$this->time_in || $this->time_out) {
            return $this->worked_seconds;
        }

        $start = $this->wallClock($this->time_in);

        return Carbon::now(self::TIMEZONE)
            ->diffInSeconds($start) - $this->break_seconds;
    }

 
    private function wallClock(?Carbon $moment): ?Carbon
    {
        return $moment ? Carbon::parse($moment->format('Y-m-d H:i:s'), self::TIMEZONE) : null;
    }

    public static function formatDuration(int $seconds): string
    {
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);

        return sprintf('%dh %02dm', $h, $m);
    }

    public function breaks()
    {
        return $this->hasMany(\App\Models\AttendanceBreak::class);
    }
}

