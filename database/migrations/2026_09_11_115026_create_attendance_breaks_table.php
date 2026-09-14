<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();
            $table->string('reason');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->timestamps();

            $table->index(['attendance_id', 'start_at']);
        });

        // Migrate any existing break1_*/break2_* data into the new table.
        // Old rows had no reason recorded, so we tag them "Break" so nothing
        // is silently dropped from historical reports.
        if (Schema::hasColumn('attendances', 'break1_start')) {
            DB::table('attendances')->orderBy('id')->chunkById(200, function ($rows) {
                $now = now();

                foreach ($rows as $row) {
                    if (!empty($row->break1_start)) {
                        DB::table('attendance_breaks')->insert([
                            'attendance_id' => $row->id,
                            'reason'        => 'Break',
                            'start_at'      => $row->break1_start,
                            'end_at'        => $row->break1_end,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ]);
                    }
                    if (!empty($row->break2_start)) {
                        DB::table('attendance_breaks')->insert([
                            'attendance_id' => $row->id,
                            'reason'        => 'Break',
                            'start_at'      => $row->break2_start,
                            'end_at'        => $row->break2_end,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ]);
                    }
                }
            });

            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn(['break1_start', 'break1_end', 'break2_start', 'break2_end']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dateTime('break1_start')->nullable();
            $table->dateTime('break1_end')->nullable();
            $table->dateTime('break2_start')->nullable();
            $table->dateTime('break2_end')->nullable();
        });

        // Best-effort restore of the two most recent breaks per attendance day.
        $breaks = DB::table('attendance_breaks')->orderBy('attendance_id')->orderBy('start_at')->get();
        $grouped = [];
        foreach ($breaks as $b) {
            $grouped[$b->attendance_id][] = $b;
        }
        foreach ($grouped as $attendanceId => $items) {
            $first = $items[0] ?? null;
            $second = $items[1] ?? null;
            DB::table('attendances')->where('id', $attendanceId)->update([
                'break1_start' => $first->start_at ?? null,
                'break1_end'   => $first->end_at ?? null,
                'break2_start' => $second->start_at ?? null,
                'break2_end'   => $second->end_at ?? null,
            ]);
        }

        Schema::dropIfExists('attendance_breaks');
    }
};
