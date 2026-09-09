<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeadActivityController extends Controller
{
    public function index(Lead $lead)
    {
        return response()->json(
            $lead->activities()->with('creator:id,name')->get()
        );
    }

    public function store(Request $request, Lead $lead)
    {
        $request->validate([
            'content' => 'nullable|string',
            'file'    => 'nullable|file|max:10240',
        ]);

        if (!$request->filled('content') && !$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Please write a note, choose a file, or both.',
            ], 422);
        }

        $data = [
            'lead_id'    => $lead->id,
            'created_by' => Auth::id(),
            'content'    => $request->input('content'),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('lead-documents/' . $lead->id, 'public');

            $data['original_name'] = $file->getClientOriginalName();
            $data['file_path']     = $path;
            $data['file_type']     = $file->getClientMimeType();
            $data['file_size']     = $file->getSize();
        }

        $activity = LeadActivity::create($data);

        return response()->json([
            'success'  => true,
            'activity' => $activity->load('creator:id,name'),
        ]);
    }

    public function update(Request $request, LeadActivity $activity)
    {
        if ($activity->created_by !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Not allowed.'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $activity->update(['content' => $request->content]);

        return response()->json([
            'success'  => true,
            'activity' => $activity->load('creator:id,name'),
        ]);
    }

    public function destroy(LeadActivity $activity)
    {
        if ($activity->created_by !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        if ($activity->file_path) {
            Storage::disk('public')->delete($activity->file_path);
        }

        $activity->delete();

        return response()->json(['success' => true]);
    }
}
