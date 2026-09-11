<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadLog;
use App\Models\LeadReminder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Single write path for every Lead-related audit log entry.
 *
 * Used by LeadObserver (automatic Lead create/update/delete/restore
 * logging) and by controllers (for actions that don't map to a plain
 * Eloquent lifecycle event: viewing a lead, and note/document/reminder
 * actions on their own small controllers).
 */
class LeadLogger
{
    /**
     * Core writer. Every other method on this class ends up calling
     * this one, so this is the only place that actually inserts a
     * lead_logs row.
     */
    public static function log(
        Lead $lead,
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        ?array $changes = null
    ): LeadLog {
        $user = Auth::user();

        return LeadLog::create([
            'lead_id' => $lead->id,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->id,
            'changes' => $changes,
            'created_at' => now(),
        ]);
    }

    // ------------------------------------------------------------
    // Lead
    // ------------------------------------------------------------

    public static function leadCreated(Lead $lead): void
    {
        $name = static::actorName();

        static::log(
            $lead,
            'lead_created',
            'lead',
            "{$name} created Lead #{$lead->id}."
        );
    }

    /**
     * $changes: ['field' => ['old' => ..., 'new' => ...], ...] - already
     * excludes noise columns (see LeadObserver::updated()).
     */
    public static function leadUpdated(Lead $lead, array $changes): void
    {
        if (empty($changes)) {
            return;
        }

        $name = static::actorName();

        if (array_key_exists('status', $changes)) {
            $old = ucfirst($changes['status']['old'] ?? '-');
            $new = ucfirst($changes['status']['new'] ?? '-');

            static::log(
                $lead,
                'lead_status_changed',
                'lead',
                "{$name} changed Lead #{$lead->id} status from {$old} to {$new}.",
                null,
                $changes
            );

            return;
        }

        $fields = collect(array_keys($changes))
            ->map(fn ($field) => ucwords(str_replace('_', ' ', $field)))
            ->implode(', ');

        static::log(
            $lead,
            'lead_updated',
            'lead',
            "{$name} updated Lead #{$lead->id} ({$fields}).",
            null,
            $changes
        );
    }

    public static function leadDeleted(Lead $lead): void
    {
        $name = static::actorName();

        static::log(
            $lead,
            'lead_deleted',
            'lead',
            "{$name} deleted Lead #{$lead->id}."
        );
    }

    public static function leadRestored(Lead $lead): void
    {
        $name = static::actorName();

        static::log(
            $lead,
            'lead_restored',
            'lead',
            "{$name} restored Lead #{$lead->id}."
        );
    }

    /**
     * At most one "viewed" entry per user per lead per day, so opening
     * a lead repeatedly through the day doesn't flood the log.
     */
    public static function leadViewed(Lead $lead): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $alreadyLoggedToday = LeadLog::where('lead_id', $lead->id)
            ->where('user_id', $user->id)
            ->where('action', 'lead_viewed')
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($alreadyLoggedToday) {
            return;
        }

        static::log(
            $lead,
            'lead_viewed',
            'lead',
            "{$user->name} viewed Lead #{$lead->id}."
        );
    }

    // ------------------------------------------------------------
    // Notes / Documents (both live on LeadActivity - a row is a
    // "note" if it has content, a "document" if it has a file, and
    // can be both at once)
    // ------------------------------------------------------------

    public static function activityCreated(Lead $lead, LeadActivity $activity): void
    {
        $name = static::actorName();

        $hasNote = filled($activity->content);
        $hasFile = filled($activity->file_path);

        if ($hasNote && $hasFile) {
            $module = 'note';
            $action = 'note_created';
            $description = "{$name} added a note with attachment {$activity->original_name} to Lead #{$lead->id}.";
        } elseif ($hasFile) {
            $module = 'document';
            $action = 'document_uploaded';
            $description = "{$name} uploaded {$activity->original_name} to Lead #{$lead->id}.";
        } else {
            $module = 'note';
            $action = 'note_created';
            $description = "{$name} added a note to Lead #{$lead->id}.";
        }

        static::log($lead, $action, $module, $description, $activity);
    }

    public static function activityUpdated(LeadActivity $activity): void
    {
        $name = static::actorName();
        $lead = $activity->lead;

        static::log(
            $lead,
            'note_updated',
            'note',
            "{$name} updated a note on Lead #{$lead->id}.",
            $activity
        );
    }

    public static function activityDeleted(LeadActivity $activity): void
    {
        $name = static::actorName();
        $lead = $activity->lead;

        $hasNote = filled($activity->content);
        $hasFile = filled($activity->file_path);

        if ($hasNote && $hasFile) {
            $module = 'note';
            $action = 'note_deleted';
            $description = "{$name} deleted a note and attachment {$activity->original_name} from Lead #{$lead->id}.";
        } elseif ($hasFile) {
            $module = 'document';
            $action = 'document_deleted';
            $description = "{$name} deleted the document {$activity->original_name} from Lead #{$lead->id}.";
        } else {
            $module = 'note';
            $action = 'note_deleted';
            $description = "{$name} deleted a note from Lead #{$lead->id}.";
        }

        static::log($lead, $action, $module, $description, $activity, [
            'content' => $activity->content,
            'original_name' => $activity->original_name,
        ]);
    }

    // ------------------------------------------------------------
    // Reminders
    // ------------------------------------------------------------

    public static function reminderCreated(Lead $lead, LeadReminder $reminder): void
    {
        $name = static::actorName();
        $when = static::formatReminderWhen($reminder);

        static::log(
            $lead,
            'reminder_created',
            'reminder',
            "{$name} added a reminder for Lead #{$lead->id} ({$when}).",
            $reminder
        );
    }

    public static function reminderDeleted(LeadReminder $reminder): void
    {
        $name = static::actorName();
        $lead = $reminder->lead;
        $when = static::formatReminderWhen($reminder);

        static::log(
            $lead,
            'reminder_deleted',
            'reminder',
            "{$name} deleted a reminder ({$when}) from Lead #{$lead->id}.",
            $reminder,
            ['note' => $reminder->note]
        );
    }

    private static function formatReminderWhen(LeadReminder $reminder): string
    {
        $date = optional($reminder->reminder_date)->format('d M Y') ?? '-';
        $time = $reminder->reminder_time ? substr($reminder->reminder_time, 0, 5) : '';

        return trim("{$date} {$time}");
    }

    private static function actorName(): string
    {
        return Auth::user()?->name ?? 'Someone';
    }
}
