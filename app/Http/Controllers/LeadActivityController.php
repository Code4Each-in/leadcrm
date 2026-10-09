<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadAssignment;
use App\Services\LeadLogger;
use App\Services\LeadWorkflowService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LeadActivityController extends Controller
{
    use AuthorizesRequests;

    public function index(Lead $lead)
    {
        $this->authorize('view', $lead);

        $activities = $lead->activities()->with('creator:id,name');

        // Pricing approve / decline notes (with the decline reason)
        // are for those who can see the lead's pricing only.
        if (Auth::user()->cannot('viewPricing', $lead)) {
            $activities->where(function ($q) {
                $q->whereNull('workflow_action')
                    ->orWhereNotIn('workflow_action', LeadAssignment::PRICING_ACTIONS);
            });
        }

        return response()->json($activities->get());
    }

    public function store(Request $request, Lead $lead)
    {
        $this->authorize('view', $lead);

        // Every entry is a document of a given type - Other included.
        // Typed notes are no longer added here (any text posted is
        // ignored); workflow notes are written by LeadWorkflowService.
        $request->validate([
            'document_type' => ['required', Rule::in(array_keys(LeadActivity::DOCUMENT_TYPES))],
            'file'          => ['required', 'file', 'max:10240'],
        ], [
            'document_type.required' => 'Please select a document type.',
            'document_type.in'       => 'Please select a valid document type.',
            'file.required'          => 'Please choose a document to upload.',
        ]);

        $file = $request->file('file');

        $data = [
            'lead_id'       => $lead->id,
            'created_by'    => Auth::id(),
            'document_type' => $request->input('document_type'),
            'content'       => null,
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $file->store('lead-documents/' . $lead->id, 'public'),
            'file_type'     => $file->getClientMimeType(),
            'file_size'     => $file->getSize(),
        ];

        $activity = LeadActivity::create($data);

        LeadLogger::activityCreated($lead, $activity);

        // The AE answering a "Sent back to AE" - tells the MIS user
        // who sent it back.
        app(LeadWorkflowService::class)->aeAddedActivity($lead, Auth::user());

        return response()->json([
            'success'  => true,
            'activity' => $activity->load('creator:id,name'),
        ]);
    }

    public function update(Request $request, LeadActivity $activity)
    {
        if (!$this->canManage($activity)) {
            return response()->json(['success' => false, 'message' => 'Not allowed.'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $activity->update(['content' => $request->content]);

        LeadLogger::activityUpdated($activity);

        return response()->json([
            'success'  => true,
            'activity' => $activity->load('creator:id,name'),
        ]);
    }

    public function destroy(LeadActivity $activity)
    {
        if (!$this->canManage($activity)) {
            return response()->json(['success' => false], 403);
        }

        LeadLogger::activityDeleted($activity);

        if ($activity->file_path) {
            Storage::disk('public')->delete($activity->file_path);
        }

        $activity->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Owner of the note/document, or Admin / Super Admin - and only
     * while they can still see the lead it belongs to.
     */
    private function canManage(LeadActivity $activity): bool
    {
        // Workflow notes (pricing decisions, hold / lost / close) are
        // a permanent record.
        if ($activity->isWorkflowNote()) {
            return false;
        }

        if (!$activity->lead || Auth::user()->cannot('view', $activity->lead)) {
            return false;
        }

        if ((int) $activity->created_by === Auth::id()) {
            return true;
        }

        return Auth::user()->isAdminOrAbove();
    }
}
