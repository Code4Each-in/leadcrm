<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadDocument;
use Illuminate\Support\Facades\Storage;
use App\Services\LeadLogger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * The Contract section under Pricing (AU Savers). Everyone who can
 * see the lead can view the documents - view only, no download - and
 * Admin / Super Admin / MIS / the lead's Account Manager upload them
 * (see LeadPolicy::uploadContract()). Files are kept on the private
 * disk and only ever served through show(), never by a public URL.
 */
class LeadContractController extends Controller
{
    use AuthorizesRequests;

    /** Same 10 MB per-file limit as Notes & Documents. */
    private const MAX_FILE_KB = 10240;

    private const MAX_FILES = 10;

    // Served straight from the public disk, so only document / image
    // types - never anything a browser would run.
    private const ALLOWED_TYPES = 'pdf,doc,docx,xls,xlsx,csv,txt,jpg,jpeg,png';

    /**
     * Shows one contract document in the browser (inline - never as
     * an attachment), to anyone who can see its lead.
     */
    public function show(LeadDocument $document)
    {
        $lead = $document->lead;

        abort_unless($lead, 404);

        $this->authorize('viewContracts', $lead);

        // Uploaded before contracts moved to the private disk.
        $disk = Storage::disk(LeadDocument::DISK)->exists($document->file_path) ? LeadDocument::DISK : 'public';

        abort_unless(Storage::disk($disk)->exists($document->file_path), 404);

        return Storage::disk($disk)->response(
            $document->file_path,
            $document->original_name,
            [
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store',
            ],
            'inline'
        );
    }

    /**
     * Uploads one or more contract documents in a single request.
     */
    public function store(Request $request, Lead $lead)
    {
        $this->authorize('uploadContract', $lead);

        $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:' . self::MAX_FILES],
            'files.*' => ['file', 'max:' . self::MAX_FILE_KB, 'mimes:' . self::ALLOWED_TYPES],
        ], [
            'files.required' => 'Please choose at least one contract document.',
            'files.max' => 'You can upload up to ' . self::MAX_FILES . ' documents at a time.',
            'files.*.max' => 'Each document must be 10 MB or smaller.',
            'files.*.mimes' => 'Contract documents must be PDF, Word, Excel, CSV, text or image files.',
        ]);

        $documents = DB::transaction(function () use ($request, $lead) {
            $documents = [];

            foreach ($request->file('files') as $file) {
                $documents[] = LeadDocument::create([
                    'lead_id' => $lead->id,
                    'uploaded_by' => Auth::id(),
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $file->store('lead-contracts/' . $lead->id, LeadDocument::DISK),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }

            LeadLogger::contractsUploaded($lead, $documents);

            return $documents;
        });

        $count = count($documents);

        return response()->json([
            'success' => true,
            'message' => $count === 1
                ? "Contract uploaded to Lead #{$lead->display_id}."
                : "{$count} contracts uploaded to Lead #{$lead->display_id}.",
        ]);
    }
}
