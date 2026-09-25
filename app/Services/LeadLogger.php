<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadLog;
use App\Models\LeadPricing;
use App\Models\LeadReminder;
use App\Models\User;
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
            "{$name} created Lead #{$lead->display_id}."
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

        // Workflow transitions (assign, start process, move to an
        // Account Manager, send back, close) are logged explicitly
        // by the methods below with who did what to whom - don't add
        // a second, less useful generic entry for them.
        if (array_intersect(['assigned_to', 'process_started_at', 'closed_at', 'hold_at', 'lost_at'], array_keys($changes))) {
            return;
        }

        $name = static::actorName();

        if (array_key_exists('status', $changes)) {
            $old = $changes['status']['old'] ? Lead::statusLabel($changes['status']['old']) : '-';
            $new = $changes['status']['new'] ? Lead::statusLabel($changes['status']['new']) : '-';

            static::log(
                $lead,
                'lead_status_changed',
                'lead',
                "{$name} changed Lead #{$lead->display_id} status from {$old} to {$new}.",
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
            "{$name} updated Lead #{$lead->display_id} ({$fields}).",
            null,
            $changes
        );
    }

    /**
     * $previousAe is null for a first assignment.
     */
    public static function leadAssigned(Lead $lead, User $ae, ?User $previousAe = null): void
    {
        $name = static::actorName();

        $description = $previousAe
            ? "{$name} reassigned Lead #{$lead->display_id} from {$previousAe->name} to {$ae->name}."
            : "{$name} assigned Lead #{$lead->display_id} to {$ae->name}.";

        static::log(
            $lead,
            'lead_assigned',
            'lead',
            $description,
            null,
            [
                'assigned_to' => [
                    'old' => $previousAe?->name,
                    'new' => $ae->name,
                ],
            ]
        );
    }

    public static function leadProcessStarted(Lead $lead): void
    {
        $name = static::actorName();

        static::log(
            $lead,
            'lead_process_started',
            'lead',
            "{$name} started processing Lead #{$lead->display_id}.",
            null,
            ['status' => ['old' => Lead::STATUS_ASSIGNED, 'new' => Lead::STATUS_IN_PROGRESS]]
        );
    }

    public static function leadMovedToAccountManager(Lead $lead, User $ae, User $accountManager): void
    {
        static::log(
            $lead,
            'lead_moved_to_am',
            'lead',
            "{$ae->name} moved Lead #{$lead->display_id} to Account Manager {$accountManager->name}.",
            null,
            [
                'assigned_to' => ['old' => $ae->name, 'new' => $accountManager->name],
                'status' => ['old' => Lead::STATUS_IN_PROGRESS, 'new' => Lead::STATUS_WITH_ACCOUNT_MANAGER],
            ]
        );
    }

    public static function leadSentBack(Lead $lead, User $accountManager, User $ae, ?string $note = null): void
    {
        static::log(
            $lead,
            'lead_sent_back',
            'lead',
            "{$accountManager->name} sent Lead #{$lead->display_id} back to {$ae->name}." . static::noteSuffix($note),
            null,
            [
                'assigned_to' => ['old' => $accountManager->name, 'new' => $ae->name],
                'status' => ['old' => Lead::STATUS_WITH_ACCOUNT_MANAGER, 'new' => Lead::STATUS_SENT_BACK],
            ]
        );
    }

    /**
     * The Account Manager's Hold / Lost / Close.
     * $status is the new stored status; $fromStatus what it was.
     */
    public static function leadStatusSetByAccountManager(Lead $lead, User $accountManager, string $status, string $fromStatus, ?string $note = null): void
    {
        [$action, $verb] = match ($status) {
            Lead::STATUS_HOLD => ['lead_on_hold', 'put'],
            Lead::STATUS_LOST => ['lead_lost', 'marked'],
            default => ['lead_closed', 'closed'],
        };

        $sentence = match ($status) {
            Lead::STATUS_HOLD => "{$accountManager->name} put Lead #{$lead->display_id} on hold.",
            Lead::STATUS_LOST => "{$accountManager->name} marked Lead #{$lead->display_id} as lost.",
            default => "{$accountManager->name} closed Lead #{$lead->display_id}.",
        };

        static::log(
            $lead,
            $action,
            'lead',
            $sentence . static::noteSuffix($note),
            null,
            ['status' => ['old' => $fromStatus, 'new' => $status]]
        );
    }

    private static function noteSuffix(?string $note): string
    {
        return filled($note) ? ' Note: ' . trim($note) : '';
    }

    public static function leadDeleted(Lead $lead): void
    {
        $name = static::actorName();

        static::log(
            $lead,
            'lead_deleted',
            'lead',
            "{$name} deleted Lead #{$lead->display_id}."
        );
    }

    public static function leadRestored(Lead $lead): void
    {
        $name = static::actorName();

        static::log(
            $lead,
            'lead_restored',
            'lead',
            "{$name} restored Lead #{$lead->display_id}."
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
            "{$user->name} viewed Lead #{$lead->display_id}."
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
            $description = "{$name} added a note with attachment {$activity->original_name} to Lead #{$lead->display_id}.";
        } elseif ($hasFile) {
            $module = 'document';
            $action = 'document_uploaded';
            $description = "{$name} uploaded {$activity->original_name} to Lead #{$lead->display_id}.";
        } else {
            $module = 'note';
            $action = 'note_created';
            $description = "{$name} added a note to Lead #{$lead->display_id}.";
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
            "{$name} updated a note on Lead #{$lead->display_id}.",
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
            $description = "{$name} deleted a note and attachment {$activity->original_name} from Lead #{$lead->display_id}.";
        } elseif ($hasFile) {
            $module = 'document';
            $action = 'document_deleted';
            $description = "{$name} deleted the document {$activity->original_name} from Lead #{$lead->display_id}.";
        } else {
            $module = 'note';
            $action = 'note_deleted';
            $description = "{$name} deleted a note from Lead #{$lead->display_id}.";
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
            "{$name} added a reminder for Lead #{$lead->display_id} ({$when}).",
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
            "{$name} deleted a reminder ({$when}) from Lead #{$lead->display_id}.",
            $reminder,
            ['note' => $reminder->note]
        );
    }

    // ------------------------------------------------------------
    // Pricing
    // ------------------------------------------------------------

    public static function pricingCreated(Lead $lead, LeadPricing $pricing): void
    {
        $name = static::actorName();

        static::log(
            $lead,
            'pricing_created',
            'pricing',
            "{$name} added {$pricing->status} pricing for Lead #{$lead->display_id} ({$pricing->supplier->name}).",
            $pricing
        );
    }

    public static function pricingUpdated(LeadPricing $pricing, array $changes): void
    {
        if (empty($changes)) {
            return;
        }

        $name = static::actorName();
        $lead = $pricing->lead;

        if (array_key_exists('status', $changes) && $changes['status']['new'] === 'published') {
            static::log(
                $lead,
                'pricing_published',
                'pricing',
                "{$name} published pricing for Lead #{$lead->display_id} ({$pricing->supplier->name}).",
                $pricing,
                $changes
            );

            return;
        }

        static::log(
            $lead,
            'pricing_updated',
            'pricing',
            "{$name} updated pricing for Lead #{$lead->display_id} ({$pricing->supplier->name}).",
            $pricing,
            $changes
        );
    }

    public static function pricingDeleted(LeadPricing $pricing): void
    {
        $name = static::actorName();
        $lead = $pricing->lead;

        static::log(
            $lead,
            'pricing_deleted',
            'pricing',
            "{$name} deleted {$pricing->status} pricing from Lead #{$lead->display_id} ({$pricing->supplier->name}).",
            $pricing,
            ['annual_spend' => (string) $pricing->annual_spend]
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
