<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        $allowedRoles = [
            'Super Admin',
            'Admin',
            'MIS User',
            'Account Executive',
            'QA User',
            'Account Manager',
        ];

        $roleName = $user->role?->name;

        if (!$roleName || !in_array(
            strtolower($roleName),
            array_map('strtolower', $allowedRoles),
            true
        )) {
            abort(403, 'You do not have permission to access chat.');
        }

        return $next($request);
    }
}
