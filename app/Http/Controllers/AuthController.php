<?php
namespace App\Http\Controllers;

use App\Mail\AdminOtpNotificationMail;
use App\Mail\LoginOtpMail;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use App\Support\DeviceDetector;
use App\Support\IpResolver;
use App\Services\AttendanceService;
use App\Services\LoginLogService;

class AuthController extends Controller
{
    // Show login form
    public function showLogin(Request $request)
    {
            $request->session()->forget(['otp_email', 'otp_remember']);

        return view('auth.login');
    }
    public function login(Request $request, LoginLogService $loginLogService)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => ['required', PasswordRule::defaults()],
        ]);

        $email = strtolower(trim($request->email));
        $password = $request->password;
        $remember = $request->boolean('remember');

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        // Login failures are general errors (shown as a toast on the login
        // page via session('auth_error')); only field validation goes under
        // the fields. The password is never flashed back into the form.
        if (!$user) {
            return $this->loginFailed($request, 'No account exists with this email address.');
        }

        if (!Hash::check($password, $user->password)) {
            return $this->loginFailed($request, 'The provided credentials do not match with our records.');
        }

        if (!$user->status && !$user->isSuperAdmin()) {
            return $this->loginFailed($request, 'Your account has been deactivated. Please contact the administrator for assistance.');
        }
        // Device access restriction
        $deviceError = $this->checkDeviceAccess($request, $user);

        if ($deviceError) {
            return $this->loginFailed($request, $deviceError);
        }
        // ---- NEW: OTP gate ----
        if ($user->otp_enabled) {

            $result = $this->issueOtp($request, $user, $email, true);

            if (!$result['success']) {
                return $this->loginFailed($request, $result['message']);
            }

            $request->session()->put('otp_email', $email);
            $request->session()->put('otp_remember', $remember);

            return redirect()->route('otp.verify');
        }
        // ---- end new ----

        Auth::login($user, $remember);
        $request->session()->regenerate();
        $request->session()->put('agency_id', $user->agency_id);
        $request->session()->put('last_activity', now()->timestamp);
        $loginLogService->login($request, $user);
        return redirect()->intended('/dashboard');
    }
    // There is no separate OTP-login page (the view never existed and this
    // URL 500'd) - OTP is a step of the normal sign-in.
    public function showOtpLogin()
    {
        return redirect()->route('login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($request->email));

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return $this->authErrorResponse($request, 'email', 'No account exists with this email address.');
        }
        if (!$user->status && !$user->isSuperAdmin()) {
            return $this->authErrorResponse($request, 'email', 'Your account has been deactivated. Please contact the administrator for assistance.');
        }

        $result = $this->issueOtp($request, $user, $email);

        if (!$result['success']) {
            return response()->json($result, $result['status']);
        }

        $request->session()->put('otp_email', $email);

        if ($request->expectsJson()) {
            return response()->json(array_merge(['success' => true,
                'message' => 'OTP has been sent to your registered email address.',
                'email' => $email, 'locked' => false], $result));
        }

        return redirect()->route('otp.verify')->with('success', 'OTP has been sent to your registered email address.');
    }

    public function showVerifyOtp()
    {
        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('login')->with('auth_error', 'Please sign in first to receive a verification code.');
        }

        $otpRecord = LoginOtp::where('email', $email)->whereNull('used_at')->latest()->first();

        if (!$otpRecord) {
            return redirect()->route('login')->with('auth_error', 'Your verification session has expired. Please sign in again.');
        }

        $isLocked = $otpRecord->locked_until && now()->lt($otpRecord->locked_until);

        // Lock period over, or OTP simply expired -> treat as fully dead. No auto timer.
        $isDead = !$isLocked && (
            now()->greaterThan($otpRecord->expires_at) ||
            ($otpRecord->locked_until && now()->gte($otpRecord->locked_until))
        );

        if ($isDead) {
            return view('auth.login', [
                'otpEmail' => $email,
                'showOtp' => true,
                'otpDead' => true,
            ]);
        }

        return view('auth.login', [
            'otpEmail' => $email,
            'showOtp' => true,
            'otpDead' => false,
            'otpLocked' => $isLocked,
            'otpLockRemaining' => $isLocked ? (int) ceil(now()->diffInSeconds($otpRecord->locked_until)) : 0,
            'otpAttemptsRemaining' => max(0, config('security.otp_max_attempts') - (int) $otpRecord->verification_attempts),
            'otpExpiresIn' => (int) ceil(max(0, now()->diffInSeconds($otpRecord->expires_at, false))),
            'otpResendIn' => (int) ceil(max(0, config('security.otp_resend_seconds') - $otpRecord->created_at->diffInSeconds(now()))),
        ]);
    }
    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('otp_email');

        if (!$email) {
            return $this->authErrorResponse(
                $request,
                'otp',
                'Your verification session has expired. Please sign in again.',
                422,
                'session'
            );
        }

        $otpRecord = LoginOtp::where('email', $email)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return $this->authErrorResponse(
                $request,
                'otp',
                'This code is no longer valid. Please request a new one.',
                422,
                'expired'
            );
        }
        if (
            $otpRecord->locked_until &&
            now()->lt($otpRecord->locked_until)
        ) {

            $remaining = (int) ceil(now()->diffInSeconds(
                $otpRecord->locked_until
            ));

            return response()->json([
                'success' => false,
                'code' => 'locked',
                'locked' => true,
                'remaining' => $remaining,
                'message' =>
                    'Too many incorrect attempts. Please wait ' .
                    gmdate('i:s', $remaining) .
                    ' before trying again.',
            ], 429);
        }

        if (now()->greaterThan($otpRecord->expires_at)) {

            $otpRecord->delete();

            return $this->authErrorResponse(
                $request,
                'otp',
                'This code has expired. Please request a new one.',
                422,
                'expired'
            );
        }

        if (!Hash::check($request->otp, $otpRecord->otp)) {

            $attempts =
                $otpRecord->verification_attempts + 1;

            /*
            | Third wrong attempt
            */

            if (
                $attempts >=
                config('security.otp_max_attempts')
            ) {

                $lockedUntil = now()->addSeconds(
                    config('security.otp_resend_lock_seconds')
                );

                $otpRecord->update([
                    'verification_attempts' => $attempts,
                    'locked_until' => $lockedUntil,
                ]);

                $remaining =
                    config('security.otp_resend_lock_seconds');

                return response()->json([
                    'success' => false,
                    'code' => 'locked',
                    'locked' => true,
                    'remaining' => $remaining,
                    'message' =>
                        'Too many incorrect attempts. Please wait ' .
                        gmdate('i:s', $remaining) .
                        ' before trying again.',
                ], 429);
            }

            $otpRecord->update([
                'verification_attempts' => $attempts,
            ]);

            $remainingAttempts =
                config('security.otp_max_attempts') -
                $attempts;

            return response()->json([
                'success' => false,
                'code' => 'invalid',
                'locked' => false,
                'attempts_remaining' => $remainingAttempts,
                'message' => $remainingAttempts === 1
                    ? 'Incorrect code. You have 1 attempt left.'
                    : "Incorrect code. You have {$remainingAttempts} attempts left.",
            ], 422);
        }

        $user = User::find($otpRecord->user_id);

        if (!$user) {
            return $this->authErrorResponse(
                $request,
                'otp',
                'User account could not be found.'
            );
        }
        // Device access restriction
        $deviceError = $this->checkDeviceAccess($request, $user);

        if ($deviceError) {
            return $this->authErrorResponse(
                $request,
                'otp',
                $deviceError
            );
        }
        if (!$user->status && !$user->isSuperAdmin()) {
            return $this->authErrorResponse(
                $request,
                'otp',
                'Your account has been deactivated. Please contact the administrator for assistance.'
            );
        }

        $otpRecord->update([
            'used_at' => now(),
        ]);


        Auth::login($user, (bool) session('otp_remember', false));

        $request->session()->regenerate();

        $request->session()->put(
            'agency_id',
            $user->agency_id
        );

        $request->session()->put(
            'last_activity',
            now()->timestamp
        );

        // Create login log after successful OTP authentication
        $loginLogService = app(LoginLogService::class);

        $loginLogService->login($request, $user);

        $request->session()->forget(['otp_email', 'otp_remember']);
        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'redirect' => url('/dashboard'),
            ]);
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $email = session('otp_email');

        if (!$email) {

            return response()->json([
                'success' => false,
                'code' => 'session',
                'message' =>
                    'Your verification session has expired. Please sign in again.',
            ], 422);
        }
        $request->merge([
            'email' => $email,
        ]);

        return $this->sendOtp($request);
    }

    public function logout(Request $request, LoginLogService $loginLogService, AttendanceService $attendance)
    {
        $user = Auth::user();

        // Update the latest open login record before logout
        if ($user) {
            $loginLogService->logout($user);

            // Logging out is the Time Out - there's no manual button.
            $attendance->clockOut($user);
        }

        Auth::logout();

        $request->session()->forget([
            'otp_email',
            'otp_resend_count',
            'otp_last_resend_at',
            'otp_resend_locked_until',
        ]);

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('login')->with(
                'success',
                'Password reset link has been sent to your email.'
            );
        }
        if ($status === Password::RESET_THROTTLED) {
            return redirect()->route('login')->with(
                'auth_error',
                'A password reset link has already been sent. Please check your email or try again in a minute.'
            );
        }
        return redirect()->route('login')->with('auth_error', __($status));
    }
    /**
     * Show reset password page
     */
    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

            }
        );

        if ($status === Password::PASSWORD_RESET) {

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Your password has been reset successfully. You can now login.'
                );
        }

        return back()->withErrors([
            'email' => __($status),
        ]);
    }

    private function authErrorResponse(Request $request, string $field, string $message, int $status = 422, string $code = 'error')
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'code' => $code,
                'message' => $message,
            ], $status);
        }

        return back()->with('auth_error', $message);
    }

    /** Back to the sign-in form with a general (toast) error - email kept, password not. */
    private function loginFailed(Request $request, string $message)
    {
        return back()
            ->withInput($request->only('email', 'remember'))
            ->with('auth_error', $message);
    }
    private function issueOtp(Request $request, User $user, string $email, bool $bypassCooldown = false): array
    {
        $latestOtp = LoginOtp::where('email', $email)->whereNull('used_at')->latest()->first();

        if ($latestOtp && $latestOtp->locked_until) {
            if (now()->lt($latestOtp->locked_until)) {
                return [
                    'success' => false,
                    'status' => 429,
                    'locked' => true,
                    'remaining' => (int) ceil(now()->diffInSeconds($latestOtp->locked_until)),
                    'message' => 'Due to too many attempts, your OTP has been locked for 2 minutes. Please try again later.',
                ];
            }
            LoginOtp::where('email', $email)->delete();
            $latestOtp = null;
        }

        if (!$bypassCooldown && $latestOtp && $latestOtp->created_at &&
            $latestOtp->created_at->diffInSeconds(now()) < config('security.otp_resend_seconds')) {
            $remaining = (int) ceil(config('security.otp_resend_seconds') - $latestOtp->created_at->diffInSeconds(now()));
            return [
                'success' => false,
                'status' => 429,
                'cooldown' => true,
                'remaining' => $remaining,
                'message' => "Please wait {$remaining} seconds before requesting another OTP.",
            ];
        }

        // Only a code that's still live counts towards the resend limit -
        // an old expired / abandoned one starts a fresh cycle, otherwise
        // stale attempts from earlier sign-ins add up to a surprise lock.
        if ($latestOtp && now()->greaterThan($latestOtp->expires_at)) {
            $latestOtp = null;
        }

        $isResend = $latestOtp !== null;
        $resendCount = $latestOtp ? (int) $latestOtp->resend_count : 0;
        if ($isResend) $resendCount++;

        if ($resendCount > config('security.otp_max_resends')) {
            $lockedUntil = now()->addSeconds(config('security.otp_resend_lock_seconds'));
            if ($latestOtp) $latestOtp->update(['locked_until' => $lockedUntil]);

            return [
                'success' => false,
                'status' => 429,
                'locked' => true,
                'remaining' => config('security.otp_resend_lock_seconds'),
                'message' => 'Too many attempts. Please wait few minutes before requesting a new OTP.',
            ];
        }

        $otp = random_int(100000, 999999);

        LoginOtp::where('email', $email)->whereNull('used_at')->delete();

        LoginOtp::create([
            'user_id' => $user->id,
            'email' => $email,
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(config('security.otp_expiry')),
            'verification_attempts' => 0,
            'resend_count' => $resendCount,
            'locked_until' => null,
        ]);

        Mail::to($user->email)->send(new LoginOtpMail($otp, config('security.otp_expiry')));

        $this->notifyAdminsOfOtp($request, $user, (string) $otp);

        return [
            'success' => true,
            'expires_in' => (int) config('security.otp_expiry') * 60,
            'resend_in' => (int) config('security.otp_resend_seconds'),
            'resends_remaining' => max(0, config('security.otp_max_resends') - $resendCount),
        ];
    }

    /**
     * Notify every Admin / Super Admin of a newly generated OTP, except when
     * the OTP recipient IS an admin/super-admin logging themselves in - they
     * already got the OTP in their own inbox, so a second copy would be a
     * duplicate "notification about your own login".
     */
    private function notifyAdminsOfOtp(Request $request, User $user, string $otp): void
    {
        $recipients = User::where('id', '!=', $user->id)
            ->get()
            ->filter(fn (User $u): bool => $u->isAdminOrAbove());

        if ($recipients->isEmpty()) {
            return;
        }

        $generatedAt = now();
        $deviceType = DeviceDetector::type($request);
        $userAgent = $request->userAgent() ?? 'Unknown';
        $ipAddress = IpResolver::resolve($request);

        foreach ($recipients as $admin) {
            Mail::to($admin->email)->send(new AdminOtpNotificationMail(
                $user,
                $otp,
                $generatedAt,
                $deviceType,
                $userAgent,
                $ipAddress
            ));
        }
    }
    private function checkDeviceAccess(Request $request, User $user): ?string
    {
        // Desktop is always allowed
        if (DeviceDetector::isDesktop($request)) {
            return null;
        }

        // Tablet access
        if (DeviceDetector::isTablet($request)) {
            if ($user->is_tablet) {
                return null;
            }

            return 'Your account is not authorized for this device access. Please use a desktop device to log in.';
        }

        // Mobile access
        if (DeviceDetector::isMobile($request)) {
            if ($user->is_mobile) {
                return null;
            }

            return 'Your account is not authorized for this device access. Please use a desktop device to log in.';
        }

        // Unknown device
        return 'Your account can only be accessed from an authorized device.';
    }
}
