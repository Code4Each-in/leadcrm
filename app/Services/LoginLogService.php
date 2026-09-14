<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginLog;
use App\Support\DeviceDetector;
use App\Support\IpResolver;
use Illuminate\Http\Request;

class LoginLogService
{
    public function __construct(protected GeoLocationService $geoLocation) {}

    public function login(Request $request, User $user): LoginLog
    {
        LoginLog::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first()
            ?->update(['logout_at' => now()]);

        $ip = IpResolver::resolve($request);

        return LoginLog::create([
            'user_id'    => $user->id,
            'login_at'   => now(),
            'logout_at'  => null,
            'ip_address' => $ip,
            'device'     => DeviceDetector::type($request),
            'location'   => $this->geoLocation->lookup($ip),   // array -> auto JSON
        ]);
    }

    public function logout(User $user): void
    {
        LoginLog::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first()
            ?->update(['logout_at' => now()]);
    }
}
