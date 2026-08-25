<?php

namespace App\Http\Controllers;

use App\Models\LeadDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LeadActivityNotification;
use App\Models\Lead;


class LeadDocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'file|max:5120'
        ]);

        if ($request->hasFile('files')) {
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
                    'uploaded_by' => auth()->id(),
                    'file' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
        $lead = Lead::find($request->lead_id);

            $hasNote  = !empty($request->content);
            $hasFiles = $request->hasFile('files');

            if ($hasNote && $hasFiles) {
                $type    = 'note_with_attachment';
                $message = 'A new note with attachments has been added to the lead.';
            } elseif ($hasNote) {
                $type    = 'note_added';
                $message = 'A new note has been added to the lead.';
            } else {
                $type    = 'document_added';
                $message = 'A new document has been added to the lead.';
            }

            Notification::send(
                $lead->involvedUsers(),
                new LeadActivityNotification($lead, $type, $message)
            );

        return back();
    }

    public function destroy($id)
    {
        $doc = LeadDocument::findOrFail($id);

        // Only allow super admin
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
