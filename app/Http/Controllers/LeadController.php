<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Agency;
use App\Models\LeadReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\LeadStatusNotification;

class LeadController extends Controller
{

    public function index(Request $request)
    {
        $request->merge([
            'start' => $request->start ?? 0,
            'length' => $request->length ?? 10,
        ]);

        $authUser = Auth::user();
        $roleName = strtolower(trim($authUser->role->name ?? ''));

        $query = Lead::with(['assignedUser'])->latest();

        // Role-based filtering
        if ($roleName === 'account executive') {

            $query->where('assigned_to', $authUser->id);

        } elseif ($roleName === 'qa user') {

            $query->where('assigned_qa_id', $authUser->id);

        } elseif ($roleName === 'account manager') {

            $query->where('assigned_manager_id', $authUser->id);
        }

        // AJAX / DataTables
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

        $users = User::all();
        $totalLeads = $query->count();
        $leads = $query->get();

        return view('leads.index', compact(
            'users',
            'authUser',
            'totalLeads',
            'leads'
        ));
    }
    public function store(Request $request)
    {
        $authUser = Auth::user();
        $roleName = strtolower($authUser->role->name);

        if (in_array($roleName, ['mis user', 'admin'])) {
            $request->merge([
                'agency_id' => $authUser->agency_id
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'email'              => 'required|email|max:255',
            'company'            => 'required|string|max:255',
            'city'               => 'required|string|max:100',
            'source'             => 'required|string|max:100',
            'agency_id'          => 'nullable|exists:agencies,id',
            'assigned_user_id'   => 'nullable',
            'assigned_user_id.*' => 'exists:users,id',
            'notes'              => 'required|string',
            'documents'          => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = null;
        if ($request->hasFile('documents')) {
            $file = $request->file('documents')->store('leads', 'public');
        }

        $lead = Lead::create([
            'name'        => $request->name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'company'     => $request->company,
            'city'        => $request->city,
            'source'      => $request->source,
            'status'      => 'Not Started',
            'agency_id'   => $request->agency_id,
            'notes'       => $request->notes,
            'documents'   => $file,
            'created_by'  => $authUser->id,
            'assigned_to' => is_array($request->assigned_user_id)
                                ? $request->assigned_user_id[0]
                                : $request->assigned_user_id,
        ]);

        // Handle multiple assigned users safely
        $assignedUsers = is_array($request->assigned_user_id)
            ? $request->assigned_user_id
            : [$request->assigned_user_id];

        $assignedUsers = array_filter($assignedUsers);

        if (!empty($assignedUsers)) {

            // attach to pivot table
            $lead->users()->attach($assignedUsers);

            //  notify assigned users
            foreach ($assignedUsers as $userId) {
                $user = User::find($userId);

                if ($user) {
                    $user->notify(new LeadStatusNotification($lead, 'to_ae'));
                }
            }
        }

        return response()->json(['success' => 'Lead created successfully']);
    }
    public function update(Request $request, $id)
    {
        $authUser = Auth::user();
        $lead     = Lead::findOrFail($id);

        $roleName = strtolower($authUser->role->name);

        if (in_array($roleName, ['mis user', 'admin'])) {
            $request->merge([
                'agency_id' => $authUser->agency_id
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'phone'  => 'required|string|max:20',
            'email'  => 'required|email|max:255',
            'company'=> 'required|string|max:255',
            'city'   => 'required|string|max:100',
            'source' => 'required|string|max:100',
            'status' => 'required|in:Not Started,In Progress,Hold,Lost,Complete',
            'agency_id' => 'nullable|exists:agencies,id',
            'assigned_user_id'   => 'nullable|min:1',
            'assigned_user_id.*' => 'exists:users,id',
            'notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'name','phone','email','company',
            'city','source','status','agency_id','notes',
        ]);


        if ($request->status === 'In Progress' && !$lead->start_date) {
            $data['start_date'] = Carbon::now();
        }

        if ($request->status === 'Complete' && !$lead->end_date) {
            $data['end_date'] = Carbon::now();
        }

        if ($request->status !== 'Complete') {
            $data['end_date'] = null;
        }

        if ($request->hasFile('documents')) {
            $data['documents'] = $request->file('documents')->store('leads', 'public');
        }

        $lead->update($data);

        $lead->users()->sync($request->assigned_user_id);
        return response()->json(['success' => 'Lead updated successfully']);
    }
    public function destroy($id)
    {
        Lead::findOrFail($id)->delete();;

        return response()->json([
            'success' => 'Lead deleted successfully'
        ]);
    }
    public function downloadTemplate()
    {
        $filename = 'leads_template.xlsx';

        $data = [
            ['name','phone','email','company','city','source','notes'],
            ['John Doe','1234567890','john@example.com','Example Inc','New York','Referral','Test note']
        ];

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }
        }, $filename);
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Not Started,In Progress,Hold,Lost,Complete',
        ]);

        $lead = Lead::findOrFail($id);

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

        return response()->json(['success' => 'Status updated successfully']);
    }
    public function showLead($id)
    {
        $lead = Lead::with([
            'agency',
            'users',
            'leadNotes.user',
            'leadNotes.documents',
            'leadDocuments'
        ])->findOrFail($id);

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

        $activities = $activities->sortBy('created_at')->values();

        $authUser = auth()->user();

        $reminders = LeadReminder::where('lead_id', $id)
            ->where('agency_id', $authUser->agency_id)
            ->latest()
            ->get();


        $qaUsers = User::whereHas('role', function ($q) {
                $q->where('name', 'QA User');
            })
            ->where('agency_id', $authUser->agency_id)
            ->get();

        $managers = User::whereHas('role', function ($q) {
                $q->where('name', 'Account Manager');
            })
            ->where('agency_id', $authUser->agency_id)
            ->get();

        return view('leads.show', compact(
            'lead',
            'activities',
            'reminders',
            'qaUsers',
            'managers'
        ));
    }
    public function storeReminder(Request $request)
    {
        $authUser = auth()->user();
        $roleName = strtolower($authUser->role->name ?? '');

        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'date'    => 'required|date|after_or_equal:today',
            'time'    => 'required',
            'notes'   => 'nullable|string'
        ]);

        $agencyId = match ($roleName) {
            'admin', 'mis user' => $authUser->agency_id,
            'super admin'       => null,
            default             => $authUser->agency_id,
        };

        LeadReminder::create([
            'user_id'   => $authUser->id,
            'lead_id'   => $request->lead_id,
            'agency_id' => $agencyId,
            'date'      => $request->date,
            'time'      => $request->time,
            'notes'     => $request->notes,
            'is_triggered' => 0
        ]);

        return response()->json([
            'success' => 'Reminder added successfully'
        ]);
    }
    public function destroyReminder($id)
    {
        $reminder = LeadReminder::findOrFail($id);

        if ($reminder->user_id != auth()->id()) {
            return back()->with('error', 'You cannot delete this reminder. Only creator can delete it.');
        }

        $reminder->delete();
        return response()->json(['success' => 'Reminder deleted successfully']);
    }
    public function moveToQA(Request $request, $id)
    {
        $request->validate([
            'qa_user_id' => 'required|exists:users,id'
        ]);

        $lead = Lead::findOrFail($id);

        $lead->update([
            'assigned_qa_id' => $request->qa_user_id,
            'previous_ae_id' => auth()->id(),
            'stage' => 'qa',
        ]);

        $qaUser = User::find($request->qa_user_id);
        $qaUser->notify(new LeadStatusNotification($lead, 'to_qa'));

        return response()->json([
            'success' => 'Lead moved to QA successfully'
        ]);
    }
    public function moveToManager(Request $request, $id)
    {
        $request->validate([
            'manager_user_id' => 'required|exists:users,id'
        ]);

        $lead = Lead::findOrFail($id);

        $lead->update([
            'assigned_manager_id' => $request->manager_user_id,
            'stage' => 'manager',
        ]);

        $manager = User::find($request->manager_user_id);
        $manager->notify(new LeadStatusNotification($lead, 'to_manager'));

        return response()->json([
            'success' => 'Lead moved to Manager successfully'
        ]);
    }
    public function returnToAE($id)
    {
        $lead = Lead::findOrFail($id);

        if (!$lead->previous_ae_id) {
            return back()->with('error', 'No previous AE found for this lead');
        }

        $lead->update([
            'assigned_to' => $lead->previous_ae_id,
            'stage' => 'ae',
        ]);

        $lead->users()->sync([$lead->previous_ae_id]);

        $ae = User::find($lead->previous_ae_id);
        if ($ae) {
            $ae->notify(new LeadStatusNotification($lead, 'return_ae'));
        }

        return back()->with('success', 'Lead returned to Account Executive');
    }
    public function markComplete($id)
    {
        $lead = Lead::findOrFail($id);

        $lead->update([
            'stage' => 'completed',
            'status' => 'Complete',
            'assigned_to' => null,
        ]);

        // notify ALL involved users
        foreach ($lead->involvedUsers() as $user) {
            $user->notify(new LeadStatusNotification($lead, 'completed'));
        }

        return back()->with('success', 'Lead marked as Completed');
    }
    public function markLost($id)
    {
        $lead = Lead::findOrFail($id);

        $lead->update([
            'stage' => 'lost',
            'status' => 'Lost',
            'assigned_to' => null,
        ]);

        // notify ALL involved users
        foreach ($lead->involvedUsers() as $user) {
            $user->notify(new LeadStatusNotification($lead, 'lost'));
        }

        return back()->with('success', 'Lead marked as Lost');
    }

}
