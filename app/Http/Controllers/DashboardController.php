<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Agency;
use App\Models\LeadReminder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{

    public function index()
    {
        return view('dashboard.index2');
    }

    public function dismissReminder(LeadReminder $reminder)
    {
        $reminder->update(['is_triggered' => 1]);
        return response()->json(['success' => true]);
    }
}
