<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\LoginLog;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        $request->merge([
            'start'  => $request->start ?? 0,
            'length' => $request->length ?? 10,
        ]);

        // login_logs joined to users/roles (left join, so a log
        // whose user was since deleted still shows up) purely so
        // the User/Role columns below can be sorted at the database
        // level - explicit `login_logs.*` avoids the ambiguous
        // column errors that a bare `select *` would hit once
        // users.id/roles.id are also in scope.
        $query = LoginLog::with(['user' => function ($q) {
                $q->withoutGlobalScopes()->with('role');
            }])
            ->withoutGlobalScopes()
            ->select('login_logs.*')
            ->leftJoin('users', 'users.id', '=', 'login_logs.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->latest('login_logs.login_at');

        if ($request->filled('user_id')) {
            $query->where('login_logs.user_id', $request->user_id);
        }

        if ($request->filled('role_id')) {
            $query->where('users.role_id', $request->role_id);
        }

        if ($request->filled('login')) {
            $query->whereDate('login_logs.login_at', $request->login);
        }

        if ($request->filled('logout')) {
            $query->whereDate('login_logs.logout_at', $request->logout);
        }

        if ($request->ajax() || $request->has('draw')) {

            // Base query clone (IMPORTANT — same as UserController)
            $baseQuery = clone $query;

            $total    = $baseQuery->count();
            $filtered = $total; // no free-text search box on this table yet

            // Column sorting - maps the DataTables column index (sent
            // as order[0][column]/order[0][dir]) to an actual column,
            // same pattern used in UserController@index/
            // RoleController@index. User/Role sort on the joined
            // users.name/roles.name added above; Location is a JSON
            // column and isn't sortable (see columns() in the view).
            $columns = [
                0 => 'users.name',
                1 => 'roles.name',
                2 => 'login_logs.login_at',
                3 => 'login_logs.logout_at',
                4 => 'login_logs.device',
                5 => 'login_logs.ip_address',
            ];

            if ($request->has('order')) {

                $orderColumnIndex = $request->order[0]['column'] ?? 2;
                $orderDirection = $request->order[0]['dir'] ?? 'desc';

                if (isset($columns[$orderColumnIndex])) {
                    $query->reorder($columns[$orderColumnIndex], $orderDirection);
                }
            }

            $logs = $query->skip($request->start ?? 0)
                ->take($request->length ?? 10)
                ->get()
                ->map(function (LoginLog $log) {
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
