<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Proxy login: an Admin / Super Admin signs in as another user to see
 * the app exactly as they do, then returns to their own account.
 *
 * The admin's id (plus the session values login sets for them) are kept
 * under SESSION_KEY while the proxy session lasts. Everything that keeps
 * per-user bookkeeping (attendance, login logs) uses bookkeepingUser(),
 * so the person actually at the keyboard - the admin - is the one
 * recorded, never the user being viewed.
 */
class ProxyLoginService
{
    public const SESSION_KEY = 'proxy_login';

    /** Session values tied to whoever logged in, restored on leave. */
    private const PRESERVED_KEYS = ['agency_id', 'agency_ids'];

    public function isActive(): bool
    {
        return session()->has(self::SESSION_KEY);
    }

    /** The Admin / Super Admin behind the current proxy session, if any. */
    public function impersonator(): ?User
    {
        $id = session(self::SESSION_KEY . '.impersonator_id');

        return $id ? User::find($id) : null;
    }

    /** Who attendance / login logs should be recorded against. */
    public function bookkeepingUser(): ?User
    {
        return $this->impersonator() ?? Auth::user();
    }

    /**
     * Whether $admin may proxy-login as $target. Returns the reason it
     * can't, or null when allowed.
     */
    public function denialReason(User $admin, User $target): ?string
    {
        if (!$admin->isAdminOrAbove()) {
            return 'You do not have permission to use proxy login.';
        }

        if ($this->isActive()) {
            return 'You are already in a proxy session. Return to your own account first.';
        }

        if ($admin->id === $target->id) {
            return 'You cannot proxy login as yourself.';
        }

        // Never into an account at or above your own level: a Super
        // Admin can't be proxied at all, and an Admin can't proxy
        // another Admin.
        if ($target->isSuperAdmin() || ($admin->isAdmin() && $target->isAdmin())) {
            return 'You cannot proxy login as this user.';
        }

        // Same agency scoping as the Users list (UserController@index).
        if ($admin->isAdmin() && (int) $admin->agency_id !== (int) $target->agency_id) {
            return 'You cannot proxy login as this user.';
        }

        if (!$target->status) {
            return 'This user is deactivated. Activate them before using proxy login.';
        }

        return null;
    }

    public function start(User $admin, User $target): void
    {
        $preserved = [];
        foreach (self::PRESERVED_KEYS as $key) {
            if (session()->has($key)) {
                $preserved[$key] = session($key);
            }
        }

        // Set before Auth::login() so the Login listener sees a proxy
        // switch and doesn't clock the target user in.
        session()->put(self::SESSION_KEY, [
            'impersonator_id' => $admin->id,
            'preserved'       => $preserved,
            'started_at'      => now()->timestamp,
        ]);

        session()->forget(self::PRESERVED_KEYS);

        Auth::login($target);

        session()->put('agency_id', $target->agency_id);

        Log::info('Proxy login started', [
            'impersonator_id' => $admin->id,
            'target_id'       => $target->id,
            'ip'              => request()->ip(),
        ]);
    }

    /** Return to the admin's own account. Returns them, or null if gone. */
    public function stop(): ?User
    {
        $state = session(self::SESSION_KEY);
        $admin = $this->impersonator();
        $target = Auth::user();

        if (!$state || !$admin) {
            session()->forget(self::SESSION_KEY);

            return null;
        }

        // Still flagged during this login, so it isn't counted as a
        // fresh Time In for the admin either.
        Auth::login($admin);

        session()->forget(self::PRESERVED_KEYS);
        foreach ($state['preserved'] ?? [] as $key => $value) {
            session()->put($key, $value);
        }

        session()->forget(self::SESSION_KEY);

        Log::info('Proxy login ended', [
            'impersonator_id' => $admin->id,
            'target_id'       => $target?->id,
        ]);

        return $admin;
    }
}
