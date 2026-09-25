<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Agency;
use App\Models\LeadReminder;
use App\Notifications\LeadAssignedNotification;
use App\Notifications\LeadWorkflowNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        // Unread lead notifications (assigned / forwarded / sent back /
        // process started / closed), shown as a call-to-action panel at
        // the top of the dashboard for whichever role received them.
        // Read ones drop off once opened (see NotificationController::open()).
        $dashboardNotifications = $user->unreadNotifications()
            ->whereIn('type', [LeadAssignedNotification::class, LeadWorkflowNotification::class])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index2', compact('dashboardNotifications'));
    }

    public function dismissReminder(LeadReminder $reminder)
    {
        $reminder->update(['is_triggered' => 1]);
        return response()->json(['success' => true]);
    }
}
