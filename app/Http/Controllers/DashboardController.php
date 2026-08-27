<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Agency;
use App\Models\LeadReminder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $authUser = Auth::user();
    //     $roleName = strtolower($authUser->role->name);
    //     $totalUploaded = 0;
    //     $todayUploads = 0;
    //     $weeklyUploads = 0;
    //     $monthlyUploads = 0;
    //     $recentUploads = collect();
    //     $pendingReviews = 0;
    //     $qaRevertedLeads = 0;
    //     $completedToday = 0;
    //     $qaPendingLeads = collect();
    //     // Agency selection
    //     $agencyId = session('agency_ids', [$authUser->agency_id])[0] ?? $authUser->agency_id;

    //     $agency = Agency::find($agencyId);
    //     $agencyName = optional($agency)->agency_name ?? 'AGILE ONE';

    //     if ($roleName === 'super admin') {

    //         if ($agencyId) {
    //             $totalAgencyUsers = User::where('agency_id', $agencyId)->count();
    //             $totalLeads = Lead::where('agency_id', $agencyId)->count();

    //             $pendingLeads = Lead::where('agency_id', $agencyId)
    //                 ->whereIn('status', ['Not Started', 'In Progress', 'Hold'])
    //                 ->count();

    //             $completedLeads = Lead::where('agency_id', $agencyId)
    //                 ->where('status', 'Complete')
    //                 ->count();
    //         } else {
    //             $totalAgencyUsers = User::count();
    //             $totalLeads = Lead::count();

    //             $pendingLeads = Lead::whereIn('status', ['Not Started', 'In Progress', 'Hold'])->count();

    //             $completedLeads = Lead::where('status', 'Complete')->count();
    //         }

    //     } elseif (in_array($roleName, ['mis user', 'admin'])) {

    //         $totalAgencyUsers = User::where('agency_id', $authUser->agency_id)->count();
    //         $totalLeads = Lead::where('agency_id', $authUser->agency_id)->count();

    //         $pendingLeads = Lead::where('agency_id', $authUser->agency_id)
    //             ->whereIn('status', ['Not Started', 'In Progress', 'Hold'])
    //             ->count();

    //         $completedLeads = Lead::where('agency_id', $authUser->agency_id)
    //             ->where('status', 'Complete')
    //             ->count();
    //               $agencyId = $authUser->agency_id;

    //         $totalUploaded = Lead::where('agency_id', $agencyId)->count();

    //         $todayUploads = Lead::where('agency_id', $agencyId)
    //             ->whereDate('created_at', today())
    //             ->count();

    //         $weeklyUploads = Lead::where('agency_id', $agencyId)
    //             ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
    //             ->count();

    //         $monthlyUploads = Lead::where('agency_id', $agencyId)
    //             ->whereMonth('created_at', now()->month)
    //             ->count();

    //         $recentUploads = Lead::where('agency_id', $agencyId)
    //             ->latest()
    //             ->take(5)
    //             ->get();

    //     }
    //     elseif ($roleName === 'qa user') {

    //         $qaId = $authUser->id;

    //         // Pending Reviews (assigned to QA + status pending)
    //         $pendingReviews = Lead::where('assigned_qa_id', $qaId)
    //             ->where('status', 'In Progress') // adjust if needed
    //             ->count();

    //         // Reverted Leads (QA reverted back to AE)
    //         $qaRevertedLeads = Lead::where('assigned_qa_id', $qaId)
    //             ->whereNotNull('previous_ae_id')
    //             ->count();

    //         // Completed Today (QA finished today)
    //         $completedToday = Lead::where('assigned_qa_id', $qaId)
    //             ->where('status', 'Complete')
    //             ->whereDate('updated_at', today())
    //             ->count();

    //         $qaPendingLeads = Lead::where('assigned_qa_id', $qaId)
    //             ->where('status', 'In Progress')
    //             ->latest()
    //             ->take(10)
    //             ->get();
    //     }else {
    //         // Account Executive
    //         $totalAgencyUsers = 1;

    //         $myLeads = Lead::whereHas('users', function ($q) use ($authUser) {
    //             $q->where('users.id', $authUser->id);
    //         });

    //         $totalLeads = $myLeads->count();

    //         $pendingLeads = (clone $myLeads)
    //             ->whereIn('status', ['Not Started', 'In Progress', 'Hold'])
    //             ->count();

    //         $completedLeads = (clone $myLeads)
    //             ->where('status', 'Complete')
    //             ->count();

    //         $revertedLeads = Lead::where('previous_ae_id', $authUser->id)
    //             ->whereHas('users', function ($q) use ($authUser) {
    //                 $q->where('users.id', $authUser->id);
    //             })
    //             ->count();

    //     }
    //     $todayRemindersQuery = LeadReminder::whereDate('date', now())
    //         ->where('is_triggered', 0);

    //     // Role-based filtering
    //     if ($roleName === 'super admin') {

    //         if ($agencyId) {
    //             $todayRemindersQuery->where('agency_id', $agencyId);
    //         }

    //     } elseif (in_array($roleName, ['admin', 'mis user'])) {

    //         $todayRemindersQuery->where(function ($q) use ($authUser) {
    //             $q->where('agency_id', $authUser->agency_id)
    //             ->orWhere('user_id', $authUser->id);
    //         });

    //     } else {

    //         $todayRemindersQuery->where('user_id', $authUser->id);
    //     }

    //     $todayReminders = $todayRemindersQuery
    //         ->with(['lead'])
    //         ->orderBy('time')
    //         ->get();
    //         $leadsByStatusQuery = Lead::query();

    //     if ($roleName === 'super admin') {

    //         if ($agencyId) {
    //             $leadsByStatusQuery->where('agency_id', $agencyId);
    //         }

    //     } elseif ($roleName === 'admin') {

    //         $leadsByStatusQuery->where('agency_id', $authUser->agency_id);
    //     }

    //     // GROUP BY STATUS
    //     $leadsByStatus = $leadsByStatusQuery
    //         ->selectRaw('status, COUNT(*) as total')
    //         ->groupBy('status')
    //         ->pluck('total', 'status');
    //     $recentLeads = collect();
    //     $recentAgencies = collect();

    //     if ($roleName === 'super admin') {

    //         $recentLeads = Lead::with('agency')
    //             ->whereDate('created_at', '>=', now()->subDays(5))
    //             ->latest()
    //             ->take(10)
    //             ->get();

    //         $recentAgencies = Agency::whereDate('created_at', '>=', now()->subDays(5))
    //             ->latest()
    //             ->take(10)
    //             ->get();
    //     }
    //     return view('dashboard.index', compact(
    //         'totalAgencyUsers',
    //         'totalLeads',
    //         'pendingLeads',
    //         'completedLeads',
    //         'agencyName',
    //         'todayReminders',
    //         'leadsByStatus',
    //         'recentLeads',
    //         'recentAgencies',
    //         'totalUploaded',
    //         'todayUploads',
    //         'weeklyUploads',
    //         'monthlyUploads',
    //         'pendingReviews',
    //         'qaRevertedLeads',
    //         'completedToday',
    //         'qaPendingLeads',
    //         'recentUploads'
    //     ));
    // }
    public function index()
    {
        $authUser = Auth::user();
        $roleName = strtolower($authUser->role->name);

        // Common stats
        $totalAgencyUsers = 0;
        $totalLeads = 0;
        $pendingLeads = 0;
        $completedLeads = 0;

        // MIS
        $totalUploaded = 0;
        $todayUploads = 0;
        $weeklyUploads = 0;
        $monthlyUploads = 0;
        $recentUploads = collect();

        // QA
        $pendingReviews = 0;
        $qaRevertedLeads = 0;
        $completedToday = 0;
        $qaPendingLeads = collect();

        // AE / Manager
        $assignedLeads = 0;
        $lostLeads = 0;
        $revertedLeads = 0;

        // Super Admin
        $recentLeads = collect();
        $recentAgencies = collect();

        $agencyId = session('agency_ids', [$authUser->agency_id])[0] ?? $authUser->agency_id;
        $agency = Agency::find($agencyId);
        $agencyName = optional($agency)->agency_name ?? 'AGILE ONE';

        // super admin
        if ($roleName === 'super admin') {

            $leadQuery = Lead::query();
            $userQuery = User::query();

            if ($agencyId) {
                $leadQuery->where('agency_id', $agencyId);
                $userQuery->where('agency_id', $agencyId);
            }

            $totalAgencyUsers = $userQuery->count();
            $totalLeads = $leadQuery->count();

            $pendingLeads = (clone $leadQuery)
                ->whereIn('status', ['Not Started', 'In Progress', 'Hold'])
                ->count();

            $completedLeads = (clone $leadQuery)
                ->where('status', 'Complete')
                ->count();

            // Recent (Last 5 days)
            $recentLeads = (clone $leadQuery)
                ->with('agency')
                ->whereDate('created_at', '>=', now()->subDays(5))
                ->latest()
                ->take(10)
                ->get();

            $recentAgencies = Agency::whereDate('created_at', '>=', now()->subDays(5))
                ->latest()
                ->take(10)
                ->get();
        }

        //  admin and mis
        elseif (in_array($roleName, ['admin', 'mis user'])) {

            $agencyId = $authUser->agency_id;

            $leadQuery = Lead::where('agency_id', $agencyId);
            $userQuery = User::where('agency_id', $agencyId);

            $totalAgencyUsers = $userQuery->count();
            $totalLeads = $leadQuery->count();

            $pendingLeads = (clone $leadQuery)
                ->whereIn('status', ['Not Started', 'In Progress', 'Hold'])
                ->count();

            $completedLeads = (clone $leadQuery)
                ->where('status', 'Complete')
                ->count();

            // Upload Stats
            $totalUploaded = $leadQuery->count();

            $todayUploads = (clone $leadQuery)
                ->whereDate('created_at', today())
                ->count();

            $weeklyUploads = (clone $leadQuery)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();

            $monthlyUploads = (clone $leadQuery)
                ->whereMonth('created_at', now()->month)
                ->count();

            $recentUploads = (clone $leadQuery)
                ->latest()
                ->take(5)
                ->get();
        }

        // for qa user
        elseif ($roleName === 'qa user') {

            $qaId = $authUser->id;

            $qaQuery = Lead::where('assigned_qa_id', $qaId);

            $pendingReviews = (clone $qaQuery)
                ->where('status', 'In Progress')
                ->count();

            $qaRevertedLeads = (clone $qaQuery)
                ->whereNotNull('previous_ae_id')
                ->count();

            $completedToday = (clone $qaQuery)
                ->where('status', 'Complete')
                ->whereDate('updated_at', today())
                ->count();

            $qaPendingLeads = (clone $qaQuery)
                ->where('status', 'In Progress')
                ->latest()
                ->take(10)
                ->get();
        }elseif ($roleName === 'account manager') {

        $managerId = $authUser->id;

        $managerLeads = Lead::where('assigned_manager_id', $managerId);

        $completedLeads = (clone $managerLeads)
            ->where('status', 'Complete')
            ->count();

        $lostLeads = (clone $managerLeads)
            ->where('status', 'Lost')
            ->count();
        }
        // for ae user
        else {

            $agencyId = $authUser->agency_id;

            $leadQuery = Lead::where('agency_id', $agencyId);
            $userQuery = User::where('agency_id', $agencyId);

            $totalAgencyUsers = $userQuery->count();
            $totalLeads = $leadQuery->count();

            $myLeads = Lead::where('assigned_to', $authUser->id);

            $assignedLeads = $myLeads->count();

            $pendingLeads = (clone $myLeads)
                ->whereIn('status', ['Not Started', 'In Progress', 'Hold'])
                ->count();

            $completedLeads = (clone $leadQuery)
                ->where('status', 'Complete')
                ->count();

            $revertedLeads = Lead::where('previous_ae_id', $authUser->id)->count();
        }

        // reminder
        $todayRemindersQuery = LeadReminder::whereDate('date', now())
            ->where('is_triggered', 0);

        if ($roleName === 'super admin') {
            if ($agencyId) {
                $todayRemindersQuery->where('agency_id', $agencyId);
            }
        } elseif (in_array($roleName, ['admin', 'mis user'])) {
            $todayRemindersQuery->where(function ($q) use ($authUser) {
                $q->where('agency_id', $authUser->agency_id)
                ->orWhere('user_id', $authUser->id);
            });
        } else {
            $todayRemindersQuery->where('user_id', $authUser->id);
        }

        $todayReminders = $todayRemindersQuery
            ->with('lead')
            ->orderBy('time')
            ->get();

        // leads by status
        $leadsByStatusQuery = Lead::query();

        if ($roleName === 'super admin') {
            if ($agencyId) {
                $leadsByStatusQuery->where('agency_id', $agencyId);
            }
        } elseif ($roleName === 'admin') {
            $leadsByStatusQuery->where('agency_id', $authUser->agency_id);
        }

        $leadsByStatus = $leadsByStatusQuery
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');


        return view('dashboard.index', compact(
            'totalAgencyUsers',
            'totalLeads',
            'pendingLeads',
            'completedLeads',
            'agencyName',
            'todayReminders',
            'leadsByStatus',
            'assignedLeads',
            'recentLeads',
            'recentAgencies',
            'totalUploaded',
            'todayUploads',
            'weeklyUploads',
            'monthlyUploads',
            'pendingReviews',
            'lostLeads',
            'qaRevertedLeads',
            'completedToday',
            'qaPendingLeads',
            'recentUploads'
        ));
    }
    public function dismissReminder(LeadReminder $reminder)
    {
        $reminder->update(['is_triggered' => 1]);

        return response()->json(['success' => true]);
    }
}
