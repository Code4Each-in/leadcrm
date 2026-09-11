<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserLog;
use App\Support\DeviceDetector;
use Illuminate\Http\Request;

class UserLogService
{
    public function login(Request $request, User $user): UserLog
    {
        // Close the previous unfinished login session, if any.
        UserLog::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first()
            ?->update([
                'logout_at' => now(),
            ]);

        // Create a new login record.
        return UserLog::create([
            'user_id'    => $user->id,
            'login_at'   => now(),
            'logout_at'  => null,
            'ip_address' => $request->ip(),
            'device'     => DeviceDetector::type($request),
        ]);
    }

    public function logout(User $user): void
    {
        // Close the latest active login session.
        $log = UserLog::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($log) {
            $log->update([
                'logout_at' => now(),
            ]);
        }
    }
}
