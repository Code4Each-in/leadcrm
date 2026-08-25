<?php

namespace App\Http\Controllers;

use App\Models\LeadNote;
use Illuminate\Http\Request;
use App\Models\LeadDocument;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LeadActivityNotification;
use App\Models\Lead;

class LeadNoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'
        ]);

        $content = trim(strip_tags($request->content ?? ''));
        $hasNote = $content !== '';
        $hasFiles = $request->hasFile('files');

        if (!$hasNote && !$hasFiles) {


        return response()->json([
            'error' => 'Please add a note or upload at least one file.'
        ]);
        }
        $note = null;

        // Save note only if valid
        if ($hasNote) {
            $note = LeadNote::create([
                'lead_id' => $request->lead_id,
                'user_id' => auth()->id(),
                'content' => $request->content,
            ]);
        }

        // Save files
        if ($hasFiles) {
            foreach ($request->file('files') as $file) {

                $destinationPath = public_path('assets/lead_documents');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $fileName);

                $filePath = 'assets/lead_documents/' . $fileName;

                LeadDocument::create([
                    'lead_id' => $request->lead_id,
                    'note_id' => $note?->id,
                    'uploaded_by' => auth()->id(),
                    'file' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => filesize(public_path($filePath)) ?? 0,
                ]);
            }
        }

        $lead = Lead::find($request->lead_id);

        if ($hasNote && $hasFiles) {
            $type = 'note_with_attachment';
            $message = 'A new note with attachments has been added to the lead.';
        } elseif ($hasNote) {
            $type = 'note_added';
            $message = 'A new note has been added to the lead.';
        } else {
            $type = 'document_added';
            $message = 'A new document has been added to the lead.';
        }

        if ($lead && ($hasNote || $hasFiles)) {
            Notification::send(
                $lead->involvedUsers(),
                new LeadActivityNotification($lead, $type, $message)
            );
        }

        return back();
    }

    public function update(Request $request, $id)
    {
        $note = LeadNote::findOrFail($id);

        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $note->update([
            'content' => $request->content,
            'is_edited' => true
        ]);

        return back();
    }

    public function destroy($id)
    {
        $doc = LeadDocument::findOrFail($id);

        if (strtolower(auth()->user()->role->name) !== 'super admin') {
            abort(403, 'Only Super Admin can delete');
        }

        if ($doc->file && file_exists(public_path($doc->file))) {
            unlink(public_path($doc->file));
        }

        $doc->delete();

        return back();
    }
}
