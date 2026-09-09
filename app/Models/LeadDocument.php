<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LeadDocument extends Model
{
    protected $fillable = ['lead_id', 'uploaded_by', 'original_name', 'file_path', 'file_type', 'file_size'];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
