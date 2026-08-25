<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'company',
        'city',
        'source',
        'status',
        'agency_id',
        'notes',
        'documents',
        'created_by',
        'start_date',
        'assigned_to',
        'assigned_qa_id',
        'assigned_manager_id',
        'previous_ae_id',
        'stage',
        'end_date',
    ];

    // AE (many-to-many - ONLY for AE history if you want)
    public function users()
    {
        return $this->belongsToMany(User::class, 'lead_user');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function documents()
    {
        return $this->hasMany(LeadDocument::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function leadNotes()
    {
        return $this->hasMany(LeadNote::class);
    }

    public function leadDocuments()
    {
        return $this->hasMany(LeadDocument::class);
    }

    public function qaUser()
    {
        return $this->belongsTo(User::class, 'assigned_qa_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'assigned_manager_id');
    }
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function involvedUsers()
    {
        $users = collect();

        // pivot users (already loaded relationship recommended)
        $users = $users->merge($this->users);

        // direct relations (no extra find calls if eager loaded)
        if ($this->relationLoaded('creator') || $this->created_by) {
            $users->push($this->creator);
        }

        if ($this->relationLoaded('qaUser') || $this->assigned_qa_id) {
            $users->push($this->qaUser);
        }

        if ($this->relationLoaded('manager') || $this->assigned_manager_id) {
            $users->push($this->manager);
        }

        if ($this->assigned_to) {
            $users->push(User::find($this->assigned_to));
        }

        return $users->filter()->unique('id');
    }
}
