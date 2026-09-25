<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Marks one of the current user's notifications as read and
     * sends them to whatever it points at. Scoped to the logged-in
     * user's own notifications, so another user's notification id
     * simply 404s.
     */
    public function open(string $notification)
    {
        $notification = Auth::user()->notifications()->findOrFail($notification);

        $notification->markAsRead();

        $data = $notification->data;

        // Every lead notification (assigned, forwarded, sent back,
        // process started, closed) points at a lead.
        if (isset($data['lead_display_id']) || isset($data['lead_id'])) {

            // Resolved the same way route binding does (business
            // lead_id first, internal id as fallback) so a lead that
            // has since been deleted is handled gracefully instead
            // of 404ing on a dead link.
            $leadKey = $data['lead_display_id'] ?? $data['lead_id'] ?? null;
            $lead = $leadKey !== null ? (new Lead)->resolveRouteBinding($leadKey) : null;

            if ($lead && Auth::user()->can('view', $lead)) {
                return redirect()->route('leads.show', $lead);
            }

            return redirect()
                ->route('dashboard')
                ->with('error', 'That lead is no longer available to you.');
        }

        return redirect()->route('dashboard');
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }
}
