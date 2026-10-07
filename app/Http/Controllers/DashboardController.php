<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LeadAssignedNotification;
use App\Notifications\LeadPublishedNotification;
use App\Notifications\LeadWorkflowNotification;
use App\Services\Dashboard\AccountExecutiveDashboard;
use App\Services\Dashboard\AccountManagerDashboard;
use App\Services\Dashboard\AdminDashboard;
use App\Services\Dashboard\BasicDashboard;
use App\Services\Dashboard\Dashboard;
use App\Services\Dashboard\MisDashboard;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * config('roles.*') key => the dashboard that role sees. Admin and
     * Super Admin share one today; giving either its own is a new
     * Dashboard subclass here. Any role not listed gets BasicDashboard.
     */
    private const DASHBOARDS = [
        'super_admin' => AdminDashboard::class,
        'admin' => AdminDashboard::class,
        'mis' => MisDashboard::class,
        'ae' => AccountExecutiveDashboard::class,
        'manager' => AccountManagerDashboard::class,
        'qa' => BasicDashboard::class,
    ];

    public function index()
    {
        $user = Auth::user();

        $dashboard = $this->dashboardFor($user);

        // Unread lead notifications (published / assigned / pricing approved or
        // declined / hold / lost / closed), shown as a call-to-action panel at
        // the top of the dashboard for whichever role received them.
        // Read ones drop off once opened (see NotificationController::open()).
        $dashboardNotifications = $user->unreadNotifications()
            ->whereIn('type', [LeadAssignedNotification::class, LeadWorkflowNotification::class, LeadPublishedNotification::class])
            ->latest()
            // The panel scrolls, so it can hold more than fits on screen.
            ->take(30)
            ->get();

        return view('dashboard.index', [
            'dashboardNotifications' => $dashboardNotifications,
            'reminders' => $dashboard->upcomingReminders($user),
            'todaysReminders' => $dashboard->todaysReminders($user),
            'rolePartial' => $dashboard->view(),
            'roleData' => $dashboard->data($user),
        ]);
    }

    private function dashboardFor(User $user): Dashboard
    {
        foreach (self::DASHBOARDS as $roleKey => $dashboard) {
            if ((int) $user->role_id === config("roles.{$roleKey}")) {
                return app($dashboard);
            }
        }

        return app(BasicDashboard::class);
    }
}
