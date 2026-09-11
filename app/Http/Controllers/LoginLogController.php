<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        $request->merge([
            'start'  => $request->start ?? 0,
            'length' => $request->length ?? 10,
        ]);

        $query = UserLog::with(['user' => function ($q) {
                $q->withoutGlobalScopes()->with('role');
            }])
            ->withoutGlobalScopes()
            ->latest('login_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('role_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role_id', $request->role_id);
            });
        }

        if ($request->filled('login')) {
            $query->whereDate('login_at', $request->login);
        }

        if ($request->filled('logout')) {
            $query->whereDate('logout_at', $request->logout);
        }

        if ($request->ajax() || $request->has('draw')) {

            // Base query clone (IMPORTANT — same as UserController)
            $baseQuery = clone $query;

            $total    = $baseQuery->count();
            $filtered = $total; // no free-text search box on this table yet

            $logs = $query->skip($request->start ?? 0)
                ->take($request->length ?? 10)
                ->get()
                ->map(function (UserLog $log) {
                    return [
                        'id'         => $log->id,
                        'user'       => $log->user ? [
                            'name'  => $log->user->name,
                            'email' => $log->user->email,
                        ] : null,
                        'role'       => $log->user && $log->user->role ? [
                            'name' => $log->user->role->name,
                        ] : null,
                        'login_at'   => $log->login_at,
                        'logout_at'  => $log->logout_at,
                        'device'     => $log->device,
                        'ip_address' => $log->ip_address,
                        'location'   => [
                            'line' => collect([
                                    $log->location['city'] ?? null,
                                    $log->location['region'] ?? null,
                                    $log->location['country'] ?? null,
                                ])
                                ->filter()
                                ->implode(', '),
                            'country_code' => $log->location['country_code'] ?? null,
                        ],
                    ];
                });

            return response()->json([
                'draw'            => intval($request->draw),
                'recordsTotal'    => $total,
                'recordsFiltered' => $filtered,
                'data'            => $logs,
            ]);
        }

        return view('login-logs.index', [
            'users' => User::withoutGlobalScopes()->orderBy('name')->get(['id', 'name']),
            'roles' => Role::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
