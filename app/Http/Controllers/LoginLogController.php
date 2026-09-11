<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();

        $roleName = strtolower(
            $authUser->role->name ?? ''
        );

        if (!in_array($roleName, ['super admin', 'admin'], true)) {
            abort(403, 'You are not authorized to view login logs.');
        }

        $query = UserLog::with([
            'user',
            'user.role'
        ])
        ->whereHas('user.role', function ($q) {
            $q->whereNotIn(
                DB::raw('LOWER(name)'),
                ['super admin', 'admin']
            );
        })
        ->latest('login_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('login')) {
            $query->whereDate(
                'login_at',
                $request->login
            );
        }

        if ($request->filled('logout')) {
            $query->whereDate(
                'logout_at',
                $request->logout
            );
        }

        $logs = $query
            ->paginate(20)
            ->withQueryString();

        $users = User::with('role')
            ->whereHas('role', function ($q) {
                $q->whereNotIn(
                    DB::raw('LOWER(name)'),
                    ['super admin', 'admin']
                );
            })
            ->orderBy('name')
            ->get();

        return view(
            'login-logs.index',
            compact('logs', 'users')
        );
    }
}
