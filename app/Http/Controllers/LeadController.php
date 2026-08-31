<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\LeadReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\LeadStatusNotification;

class LeadController extends Controller
{
    /**
     * Get the single static agency ID.
     *
     * AGILE ONE is the only agency in the system.
     */
    private function agencyId()
    {
        return \App\Models\Agency::where('agency_name', 'AGILE ONE')->value('id');
    }

    /**
     * Leads listing
     */
    public function index(Request $request)
    {
        $request->merge([
            'start' => $request->start ?? 0,
            'length' => $request->length ?? 10,
        ]);

        $authUser = Auth::user();
        $roleName = strtolower($authUser->role->name);

        $agencyId = $this->agencyId();

        $query = Lead::with(['assignedUser', 'agency'])
            ->where('agency_id', $agencyId)
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Role-based filtering
        |--------------------------------------------------------------------------
        */

        if ($roleName === 'account executive') {

            $query->where('assigned_to', $authUser->id);

        } elseif ($roleName === 'qa user') {

            $query->where('assigned_qa_id', $authUser->id);

        } elseif ($roleName === 'account manager') {

            $query->where('assigned_manager_id', $authUser->id);

        }

        /*
        |--------------------------------------------------------------------------
        | DataTables AJAX
        |--------------------------------------------------------------------------
        */

        if ($request->ajax()) {

            $baseQuery = clone $query;

            if (!empty($request->search['value'])) {

                $search = $request->search['value'];

                $query->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('source', 'like', "%{$search}%");
                });
            }

            $total = $baseQuery->count();

            $filtered = $query->count();

            $leads = $query
                ->skip($request->start)
                ->take($request->length)
                ->get();

            $data = $leads->map(function ($lead) {

                return [
                    'name' => $lead->name,
                    'company' => $lead->company,
                    'assigned_user' => $lead->assignedUser
                        ? $lead->assignedUser->name
                        : 'N/A',
                    'status' => $lead->status,
                    'source' => $lead->source,
                    'id' => $lead->id,
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => $data,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Regular page load
        |--------------------------------------------------------------------------
        */

        $users = User::where('agency_id', $agencyId)->get();

        $totalLeads = $query->count();

        $leads = $query->get();

        return view('leads.index', compact(
            'users',
            'authUser',
            'totalLeads',
            'leads'
        ));
    }

    /**
     * Create Lead
     */
    public function store(Request $request)
    {
        $authUser = Auth::user();

        $agencyId = $this->agencyId();

        if (!$agencyId) {

            return response()->json([
                'error' => 'AGILE ONE agency has not been created.'
            ], 500);
        }

        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:255',

            'phone' => 'required|string|max:20',

            'email' => 'required|email|max:255',

            'company' => 'required|string|max:255',

            'city' => 'required|string|max:100',

            'source' => 'required|string|max:100',

            'assigned_user_id' => 'nullable',

            'assigned_user_id.*' => 'exists:users,id',

            'notes' => 'required|string',

            'documents' =>
                'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Document
        |--------------------------------------------------------------------------
        */

        $file = null;

        if ($request->hasFile('documents')) {

            $file = $request
                ->file('documents')
                ->store('leads', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Assigned User
        |--------------------------------------------------------------------------
        */

        $assignedUserId = is_array($request->assigned_user_id)
            ? ($request->assigned_user_id[0] ?? null)
            : $request->assigned_user_id;

        /*
        |--------------------------------------------------------------------------
        | Create Lead
        |--------------------------------------------------------------------------
        */

        $lead = Lead::create([

            'name' => $request->name,

            'phone' => $request->phone,

            'email' => $request->email,

            'company' => $request->company,

            'city' => $request->city,

            'source' => $request->source,

            'status' => 'Not Started',

            // ALWAYS AGILE ONE
            'agency_id' => $agencyId,

            'notes' => $request->notes,

            'documents' => $file,

            'created_by' => $authUser->id,

            'assigned_to' => $assignedUserId,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Multiple Assigned Users
        |--------------------------------------------------------------------------
        */

        $assignedUsers = is_array($request->assigned_user_id)
            ? $request->assigned_user_id
            : [$request->assigned_user_id];

        $assignedUsers = array_filter($assignedUsers);

        /*
        |--------------------------------------------------------------------------
        | Make sure assigned users belong to AGILE ONE
        |--------------------------------------------------------------------------
        */

        $assignedUsers = User::where('agency_id', $agencyId)
            ->whereIn('id', $assignedUsers)
            ->pluck('id')
            ->toArray();

        if (!empty($assignedUsers)) {

            $lead->users()->attach($assignedUsers);

            foreach ($assignedUsers as $userId) {

                $user = User::find($userId);

                if ($user) {

                    $user->notify(
                        new LeadStatusNotification($lead, 'to_ae')
                    );
                }
            }
        }

        return response()->json([
            'success' => 'Lead created successfully'
        ]);
    }

    /**
     * Update Lead
     */
    public function update(Request $request, $id)
    {
        $authUser = Auth::user();

        $agencyId = $this->agencyId();

        if (!$agencyId) {

            return response()->json([
                'error' => 'AGILE ONE agency has not been created.'
            ], 500);
        }

        $lead = Lead::where('agency_id', $agencyId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:255',

            'phone' => 'required|string|max:20',

            'email' => 'required|email|max:255',

            'company' => 'required|string|max:255',

            'city' => 'required|string|max:100',

            'source' => 'required|string|max:100',

            'status' =>
                'required|in:Not Started,In Progress,Hold,Lost,Complete',

            'assigned_user_id' => 'nullable',

            'assigned_user_id.*' => 'exists:users,id',

            'notes' => 'required|string',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'name',
            'phone',
            'email',
            'company',
            'city',
            'source',
            'status',
            'notes',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ALWAYS KEEP AGILE ONE
        |--------------------------------------------------------------------------
        */

        $data['agency_id'] = $agencyId;

        /*
        |--------------------------------------------------------------------------
        | Dates
        |--------------------------------------------------------------------------
        */

        if ($request->status === 'In Progress' && !$lead->start_date) {

            $data['start_date'] = Carbon::now();
        }

        if ($request->status === 'Complete' && !$lead->end_date) {

            $data['end_date'] = Carbon::now();
        }

        if ($request->status !== 'Complete') {

            $data['end_date'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Document
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('documents')) {

            $data['documents'] =
                $request->file('documents')->store('leads', 'public');
        }

        $lead->update($data);

        /*
        |--------------------------------------------------------------------------
        | Assigned Users
        |--------------------------------------------------------------------------
        */

        $assignedUsers = is_array($request->assigned_user_id)
            ? $request->assigned_user_id
            : [$request->assigned_user_id];

        $assignedUsers = array_filter($assignedUsers);

        // Only users from AGILE ONE
        $assignedUsers = User::where('agency_id', $agencyId)
            ->whereIn('id', $assignedUsers)
            ->pluck('id')
            ->toArray();

        $lead->users()->sync($assignedUsers);

        /*
        |--------------------------------------------------------------------------
        | Keep assigned_to in sync
        |--------------------------------------------------------------------------
        */

        $lead->assigned_to = $assignedUsers[0] ?? null;
        $lead->save();

        return response()->json([
            'success' => 'Lead updated successfully.'
        ]);
    }

    /**
     * Delete Lead
     */
    public function destroy($id)
    {
        $agencyId = $this->agencyId();

        Lead::where('agency_id', $agencyId)
            ->findOrFail($id)
            ->delete();

        return response()->json([
            'success' => 'Lead deleted successfully'
        ]);
    }

    /**
     * Download Excel Template
     */
    public function downloadTemplate()
    {
        $filename = 'leads_template.xlsx';

        $data = [
            [
                'name',
                'phone',
                'email',
                'company',
                'city',
                'source',
                'notes'
            ],
            [
                'John Doe',
                '1234567890',
                'john@example.com',
                'Example Inc',
                'New York',
                'Referral',
                'Test note'
            ]
        ];

        return Excel::download(
            new class($data)
                implements \Maatwebsite\Excel\Concerns\FromArray {

                protected $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function array(): array
                {
                    return $this->data;
                }
            },
            $filename
        );
    }

    /**
     * Update Lead Status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' =>
                'required|in:Not Started,In Progress,Hold,Lost,Complete',
        ]);

        $agencyId = $this->agencyId();

        $lead = Lead::where('agency_id', $agencyId)
            ->findOrFail($id);

        if ($request->status === 'In Progress' && !$lead->start_date) {

            $lead->start_date = now();
        }

        if ($request->status === 'Complete' && !$lead->end_date) {

            $lead->end_date = now();
        }

        if ($request->status !== 'Complete') {

            $lead->end_date = null;
        }

        $lead->status = $request->status;

        $lead->save();

        return response()->json([
            'success' => 'Status updated successfully'
        ]);
    }

    /**
     * Show Lead
     */
    public function showLead($id)
    {
        $agencyId = $this->agencyId();

        $lead = Lead::with([
            'agency',
            'users',
            'leadNotes.user',
            'leadNotes.documents',
            'leadDocuments'
        ])
        ->where('agency_id', $agencyId)
        ->findOrFail($id);

        $activities = collect();

        foreach ($lead->leadNotes as $note) {

            $activities->push([
                'type' => 'note',
                'data' => $note,
                'created_at' => $note->created_at
            ]);
        }

        foreach ($lead->leadDocuments->whereNull('note_id') as $doc) {

            $activities->push([
                'type' => 'document',
                'data' => $doc,
                'created_at' => $doc->created_at
            ]);
        }

        $activities = $activities
            ->sortBy('created_at')
            ->values();

        $authUser = auth()->user();

        $reminders = LeadReminder::where('lead_id', $id)
            ->where('agency_id', $agencyId)
            ->latest()
            ->get();

        $qaUsers = User::whereHas('role', function ($q) {

            $q->where('name', 'QA User');

        })
        ->where('agency_id', $agencyId)
        ->get();

        $managers = User::whereHas('role', function ($q) {

            $q->where('name', 'Account Manager');

        })
        ->where('agency_id', $agencyId)
        ->get();

        return view('leads.show', compact(
            'lead',
            'activities',
            'reminders',
            'qaUsers',
            'managers'
        ));
    }

    /**
     * Store Reminder
     */
    public function storeReminder(Request $request)
    {
        $authUser = auth()->user();

        $agencyId = $this->agencyId();

        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'notes' => 'nullable|string'
        ]);

        LeadReminder::create([

            'user_id' => $authUser->id,

            'lead_id' => $request->lead_id,

            // ALWAYS AGILE ONE
            'agency_id' => $agencyId,

            'date' => $request->date,

            'time' => $request->time,

            'notes' => $request->notes,

            'is_triggered' => 0
        ]);

        return response()->json([
            'success' => 'Reminder added successfully'
        ]);
    }

    /**
     * Delete Reminder
     */
    public function destroyReminder($id)
    {
        $reminder = LeadReminder::findOrFail($id);

        if ($reminder->user_id != auth()->id()) {

            return back()->with(
                'error',
                'You cannot delete this reminder. Only creator can delete it.'
            );
        }

        $reminder->delete();

        return response()->json([
            'success' => 'Reminder deleted successfully'
        ]);
    }

    /**
     * Move Lead to QA
     */
    public function moveToQA(Request $request, $id)
    {
        $request->validate([
            'qa_user_id' => 'required|exists:users,id'
        ]);

        $agencyId = $this->agencyId();

        $lead = Lead::where('agency_id', $agencyId)
            ->findOrFail($id);

        $qaUser = User::where('agency_id', $agencyId)
            ->findOrFail($request->qa_user_id);

        $lead->update([

            'assigned_qa_id' => $qaUser->id,

            'previous_ae_id' => auth()->id(),

            'stage' => 'qa',
        ]);

        $qaUser->notify(
            new LeadStatusNotification($lead, 'to_qa')
        );

        return response()->json([
            'success' => 'Lead moved to QA successfully'
        ]);
    }

    /**
     * Move Lead to Account Manager
     */
    public function moveToManager(Request $request, $id)
    {
        $request->validate([
            'manager_user_id' => 'required|exists:users,id'
        ]);

        $agencyId = $this->agencyId();

        $lead = Lead::where('agency_id', $agencyId)
            ->findOrFail($id);

        $manager = User::where('agency_id', $agencyId)
            ->findOrFail($request->manager_user_id);

        $lead->update([

            'assigned_manager_id' => $manager->id,

            'stage' => 'manager',
        ]);

        $manager->notify(
            new LeadStatusNotification($lead, 'to_manager')
        );

        return response()->json([
            'success' => 'Lead moved to Manager successfully'
        ]);
    }

    /**
     * Return Lead to AE
     */
    public function returnToAE($id)
    {
        $agencyId = $this->agencyId();

        $lead = Lead::where('agency_id', $agencyId)
            ->findOrFail($id);

        if (!$lead->previous_ae_id) {

            return back()->with(
                'error',
                'No previous AE found for this lead'
            );
        }

        $lead->update([

            'assigned_to' => $lead->previous_ae_id,

            'stage' => 'ae',
        ]);

        $lead->users()->sync([
            $lead->previous_ae_id
        ]);

        $ae = User::where('agency_id', $agencyId)
            ->find($lead->previous_ae_id);

        if ($ae) {

            $ae->notify(
                new LeadStatusNotification($lead, 'return_ae')
            );
        }

        return back()->with(
            'success',
            'Lead returned to Account Executive'
        );
    }

    /**
     * Mark Lead Complete
     */
    public function markComplete($id)
    {
        $agencyId = $this->agencyId();

        $lead = Lead::where('agency_id', $agencyId)
            ->findOrFail($id);

        $lead->update([

            'stage' => 'completed',

            'status' => 'Complete',

            'assigned_to' => null,
        ]);

        foreach ($lead->involvedUsers() as $user) {

            $user->notify(
                new LeadStatusNotification($lead, 'completed')
            );
        }

        return back()->with(
            'success',
            'Lead marked as Completed'
        );
    }

    /**
     * Mark Lead Lost
     */
    public function markLost($id)
    {
        $agencyId = $this->agencyId();

        $lead = Lead::where('agency_id', $agencyId)
            ->findOrFail($id);

        $lead->update([

            'stage' => 'lost',

            'status' => 'Lost',

            'assigned_to' => null,
        ]);

        foreach ($lead->involvedUsers() as $user) {

            $user->notify(
                new LeadStatusNotification($lead, 'lost')
            );
        }

        return back()->with(
            'success',
            'Lead marked as Lost'
        );
    }
}
