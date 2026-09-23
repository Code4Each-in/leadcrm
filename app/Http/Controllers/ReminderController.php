<?php

namespace App\Http\Controllers;

use App\Models\LeadReminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function store(Request $request)
    {
        $authUser = auth()->user();

        $agencyId = null;

        // MIS user → always from user table
        if ($authUser->isMis()) {
            $agencyId = $authUser->agency_id;
        }

        // Admin → must have agency_id (from user)
        elseif ($authUser->isAdmin()) {
            $agencyId = $authUser->agency_id;
        }

        // Super admin → ONLY one allowed NULL
        elseif ($authUser->isSuperAdmin()) {
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
