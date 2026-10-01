<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A contract document uploaded in the Contract section under Pricing
 * (AU Savers). Notes & Documents keep their own files on LeadActivity;
 * this table holds only contracts. Stored on the private disk under
 * lead-contracts/{lead id} and served view-only through
 * LeadContractController::show() - there is no public URL.
 */
class LeadDocument extends Model
{
    public const DISK = 'local';

    protected $fillable = ['lead_id', 'uploaded_by', 'original_name', 'file_path', 'file_type', 'file_size'];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * The view-only link (LeadContractController::show() authorises it).
     */
    public function getUrlAttribute(): string
    {
        return route('lead-contracts.show', $this);
    }

    public function isPdf(): bool
    {
        return str_contains((string) $this->file_type, 'pdf');
    }

    /**
     * "245 KB" / "1.4 MB" - same format as the Notes & Documents feed.
     */
    public function getSizeLabelAttribute(): string
    {
        $kb = (int) $this->file_size / 1024;

        return $kb < 1024 ? round($kb) . ' KB' : number_format($kb / 1024, 1) . ' MB';
    }
}
