<?php

namespace App\Support;

use Illuminate\Notifications\DatabaseNotification;

/**
 * Turns a stored lead notification into what the bell and the
 * dashboard panel display - one place, so both always look the same.
 * Older notifications (stored before "event"/"lead_name" existed)
 * fall back gracefully to the "assigned" look.
 */
class NotificationPresenter
{
    /** event => [icon, tone] - tone maps to a colour class in the CSS. */
    private const EVENTS = [
        'assigned' => ['mdi-account-check-outline', 'blue'],
        'forwarded' => ['mdi-account-arrow-right-outline', 'purple'],
        'sent_back' => ['mdi-undo-variant', 'orange'],
        'process_started' => ['mdi-play-circle-outline', 'teal'],
        'closed' => ['mdi-check-decagram-outline', 'green'],
        'reassigned' => ['mdi-swap-horizontal', 'orange'],
        'hold' => ['mdi-pause-circle-outline', 'grey'],
        'lost' => ['mdi-close-circle-outline', 'red'],
        'pricing_approved' => ['mdi-check-decagram-outline', 'green'],
        'pricing_declined' => ['mdi-cash-remove', 'red'],
        'pricing_resubmitted' => ['mdi-cash-refund', 'purple'],
        'pricing_published' => ['mdi-cash-plus', 'purple'],
        'published' => ['mdi-folder-open-outline', 'teal'],
        'sent_back_to_ae' => ['mdi-undo-variant', 'orange'],
        'ae_responded' => ['mdi-note-text-outline', 'purple'],
        'revision_requested' => ['mdi-cash-refund', 'red'],
        'stage_changed' => ['mdi-stairs', 'purple'],
    ];

    public static function present(DatabaseNotification $notification): array
    {
        $data = $notification->data;
        $event = $data['event'] ?? 'assigned';
        [$icon, $tone] = self::EVENTS[$event] ?? ['mdi-bell-outline', 'blue'];

        $leadId = $data['lead_display_id'] ?? $data['lead_id'] ?? null;

        return [
            'id' => $notification->id,
            'url' => route('notifications.open', $notification->id),
            'unread' => $notification->read_at === null,
            'icon' => $icon,
            'tone' => $tone,
            'title' => $data['title'] ?? 'Notification',
            'message' => $data['message'] ?? '',
            'lead_label' => $leadId !== null ? "Lead #{$leadId}" : null,
            'lead_name' => $data['lead_name'] ?? null,
            'ago' => $notification->created_at->diffForHumans(),
            // "25 Sep 2026, 10:30 AM"
            'when' => $notification->created_at->format('d M Y, h:i A'),
        ];
    }
}
