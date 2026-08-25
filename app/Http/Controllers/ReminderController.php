<?php

namespace App\Http\Controllers;

use App\Models\LeadReminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function store(Request $request)
    {
        $authUser = auth()->user();
        $roleName = $authUser->role->name ?? null;

        $agencyId = null;

        // MIS user → always from user table
        if ($roleName === 'mis user') {
            $agencyId = $authUser->agency_id;
        }

        // Admin → must have agency_id (from user)
        elseif ($roleName === 'admin') {
            $agencyId = $authUser->agency_id;
        }

        // Super admin → ONLY one allowed NULL
        elseif ($roleName === 'super admin') {
            $agencyId = null;
        }

        LeadReminder::create([
            'user_id'   => $authUser->id,
            'lead_id'   => $request->lead_id,
            'agency_id' => $agencyId,
            'date'      => $request->date,
            'time'      => $request->time,
            'notes'     => $request->notes
        ]);

        return back()->with('success', 'Reminder added successfully');

    }
    
}
