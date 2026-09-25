<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LeadActivity extends Model
{
    protected $fillable = [
        'lead_id', 'created_by', 'content', 'workflow_action',
        'original_name', 'file_path', 'file_type', 'file_size',
    ];

    /**
     * Written by the lead workflow (send back / close) rather than
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
