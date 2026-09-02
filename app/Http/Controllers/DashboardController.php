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

    public function index()
    {
        $authUser = Auth::user();
        $roleName = strtolower($authUser->role->name);

        $agency = Agency::where('agency_name', 'AGILE ONE')->firstOrFail();
        $agencyId = $agency->id;
        $agencyName = $agency->agency_name;

        // Super Admin
        $totalLeads = 0;
        $leadsByStatus = collect();
        $teamPerformance = collect();
        $pendingQA = 0;
        $pendingClosure = 0;

        // MIS
        $totalUploaded = 0;
        $todayUploads = 0;
        $weeklyUploads = 0;
        $monthlyUploads = 0;
        $totalAssigned = 0;
        $todayAssigned = 0;

        // Account Executive
        $assignedLeads = 0;
        $pendingLeads = 0;
        $revertedLeads = 0;
        $todayFollowUps = collect();

        // QA
        $pendingReviews = 0;
        $qaRevertedLeads = 0;
        $completedToday = 0;

        // Account Manager
        $closureLeads = 0;
        $closedToday = 0;
        $wonLeads = 0;
        $lostLeads = 0;

        if (in_array($roleName, ['super admin', 'admin'])) {

            $leadQuery = Lead::where('agency_id', $agencyId);

            $totalLeads = (clone $leadQuery)->count();

            $leadsByStatus = (clone $leadQuery)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $pendingQA = (clone $leadQuery)
                ->where('status', 'In Progress')
                ->whereNotNull('assigned_qa_id')
                ->count();

            $pendingClosure = (clone $leadQuery)
                ->where('status', 'Complete')
                ->count();

            $teamPerformance = User::where('agency_id', $agencyId)
                ->whereHas('role', function ($query) {
                    $query->whereRaw('LOWER(name) = ?', ['account executive']);
                })
                ->get()
                ->map(function ($user) use ($agencyId) {

                    $user->total_leads = Lead::where('agency_id', $agencyId)
                        ->where('assigned_to', $user->id)
                        ->count();

                    return $user;
                });
        }
        elseif ($roleName === 'mis user') {

            $leadQuery = Lead::where('agency_id', $agencyId);
            $totalUploaded = (clone $leadQuery)->count();


            $todayUploads = (clone $leadQuery)
                ->whereDate('created_at', today())
                ->count();


            $weeklyUploads = (clone $leadQuery)
                ->whereBetween(
                    'created_at',
                    [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]
                )
                ->count();


            $monthlyUploads = (clone $leadQuery)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            $totalAssigned = (clone $leadQuery)
                ->whereNotNull('assigned_to')
                ->count();


            $todayAssigned = (clone $leadQuery)
                ->whereNotNull('assigned_to')
                ->whereDate('updated_at', today())
                ->count();
        }

        elseif (
            in_array($roleName, [
                'account executive',
                'ae user',
                'ae'
            ])
        ) {

            $myLeads = Lead::where('agency_id', $agencyId)
                ->where('assigned_to', $authUser->id);
            $assignedLeads = (clone $myLeads)->count();

            $pendingLeads = (clone $myLeads)
                ->whereIn('status', [
                    'Not Started',
                    'In Progress',
                    'Hold'
                ])
                ->count();

            $revertedLeads = Lead::where('agency_id', $agencyId)
                ->where('previous_ae_id', $authUser->id)
                ->count();
            $todayFollowUps = LeadReminder::where('agency_id', $agencyId)
                ->where('user_id', $authUser->id)
                ->whereDate('date', today())
                ->where('is_triggered', 0)
                ->with('lead')
                ->orderBy('time')
                ->get();
        }
        elseif ($roleName === 'qa user') {

            $qaLeads = Lead::where('agency_id', $agencyId)
                ->where('assigned_qa_id', $authUser->id);
            $pendingReviews = (clone $qaLeads)
                ->where('status', 'In Progress')
                ->count();

            $qaRevertedLeads = (clone $qaLeads)
                ->whereNotNull('previous_ae_id')
                ->count();
            $completedToday = (clone $qaLeads)
                ->where('status', 'Complete')
                ->whereDate('updated_at', today())
                ->count();
        }
        elseif ($roleName === 'account manager') {

            $managerLeads = Lead::where('agency_id', $agencyId)
                ->where('assigned_manager_id', $authUser->id);

            // Leads waiting for Account Manager closure
            $closureLeads = (clone $managerLeads)
                ->where('status', 'Complete')
                ->count();

            // Leads closed today
            // Assuming Won/Lost means the lead has been closed
            $closedToday = (clone $managerLeads)
                ->whereIn('status', ['Won', 'Lost'])
                ->whereDate('updated_at', today())
                ->count();

            // Won leads
            $wonLeads = (clone $managerLeads)
                ->where('status', 'Won')
                ->count();

            // Lost leads
            $lostLeads = (clone $managerLeads)
                ->where('status', 'Lost')
                ->count();
        }

        return view('dashboard.index2', compact(
            'totalLeads',
            'leadsByStatus',
            'teamPerformance',
            'pendingQA',
            'pendingClosure',
            'totalUploaded',
            'todayUploads',
            'weeklyUploads',
            'monthlyUploads',
            'totalAssigned',
            'todayAssigned',
            'assignedLeads',
            'pendingLeads',
            'revertedLeads',
            'todayFollowUps',
            'pendingReviews',
            'qaRevertedLeads',
            'completedToday',
            'closureLeads',
            'closedToday',
            'wonLeads',
            'lostLeads',
            'agencyName'
        ));
    }
    public function dismissReminder(LeadReminder $reminder)
    {
        $reminder->update(['is_triggered' => 1]);

        return response()->json(['success' => true]);
    }
}
