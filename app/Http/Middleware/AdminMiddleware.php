<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->isAdminOrAbove()) {
            return $next($request);
        }

        // A plain page render (the old 'unauthorized' view) doesn't
        // help someone who typed the URL directly - send them
        // somewhere useful instead, with the same session('error')
        // flash -> Swal notification layout.blade.php already shows
        // for access-denied cases elsewhere in the app (e.g.
        // AgencyController), rather than a one-off alert design.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'You do not have permission to access this page.',
            ], 403);
        }

        return redirect()
            ->route('dashboard')
            ->with('error', 'You do not have permission to access this page.');
    }
}
