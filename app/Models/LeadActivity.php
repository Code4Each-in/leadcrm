<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LeadActivity extends Model
{
    protected $fillable = [
        'lead_id', 'created_by', 'content', 'workflow_action', 'document_type',
        'original_name', 'file_path', 'file_type', 'file_size',
    ];

    /**
     * Document Type picked in the Notes & Documents composer. "Other"
     * is a typed note (optionally with a document); every other type
     * is an uploaded document.
     */
    public const DOCUMENT_TYPE_OTHER = 'other';

    public const DOCUMENT_TYPES = [
        'loa' => 'Letter of Authority (LOA)',
        'energy_bill' => 'Energy Bill',
        'supplier_quote' => 'Supplier Quote',
        'signed_contract' => 'Signed Contract',
        'meter_reading' => 'Meter Reading / Photo',
        'company_documents' => 'Company Documents',
        'id_proof' => 'Proof of ID',
        'bank_details' => 'Bank Details / Direct Debit',
        'termination_letter' => 'Termination Letter',
        self::DOCUMENT_TYPE_OTHER => 'Other',
    ];

    protected $appends = ['document_type_label'];

    public function getDocumentTypeLabelAttribute(): ?string
    {
        return $this->document_type ? (self::DOCUMENT_TYPES[$this->document_type] ?? null) : null;
    }

    /**
     * Written by the lead workflow (pricing approve / decline, hold /
     * lost / close) rather than
     * typed in by a user - permanent, never editable or deletable.
     */
    public function isWorkflowNote(): bool
    {
        return $this->workflow_action !== null;
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }
}
