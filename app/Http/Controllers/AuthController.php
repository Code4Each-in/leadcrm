<?php
namespace App\Http\Controllers;

use App\Mail\LoginOtpMail;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = strtolower(trim($request->email));
        $password = $request->password;

        $remember = $request->boolean('remember');
        //find user
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'No account exists with this email address.',
                ]);
        }

        if (!Hash::check($password, $user->password)) {

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'password' => 'The password you entered is incorrect.',
                ]);
        }

        if (!$user->status && $user->role_id !== 1) {

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Your account has been deactivated.',
                ]);
        }

        Auth::login($user, $remember);

        $request->session()->regenerate();

        $request->session()->put(
            'agency_id',
            $user->agency_id
        );

        $request->session()->put(
            'last_activity',
            now()->timestamp
        );


        return redirect()->intended('/dashboard');
    }

    public function showOtpLogin()
    {
        return view('auth.otp-login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->email));

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return $this->authErrorResponse(
                $request,
                'email',
                'No account exists with this email address.'
            );
        }

        if (!$user->status && $user->role_id !== 1) {
            return $this->authErrorResponse(
                $request,
                'email',
                'Your account has been deactivated.'
            );
        }
        $latestOtp = LoginOtp::where('email', $email)
            ->latest()
            ->first();
        if ($latestOtp && $latestOtp->locked_until) {

            if (now()->lt($latestOtp->locked_until)) {

                $remaining = now()->diffInSeconds(
                    $latestOtp->locked_until
                );

                return response()->json([
                    'success' => false,
                    'locked' => true,
                    'remaining' => $remaining,
                    'message' =>
                        'Too many attempts. Please wait ' .
                        gmdate('i:s', $remaining) .
                        ' before requesting a new OTP.',
                ], 429);
            }

            LoginOtp::where('email', $email)->delete();

            $latestOtp = null;
        }

        if (
            $latestOtp &&
            $latestOtp->created_at &&
            $latestOtp->created_at->diffInSeconds(now()) <
            config('security.otp_resend_seconds')
        ) {

            $remaining =
                config('security.otp_resend_seconds') -
                $latestOtp->created_at->diffInSeconds(now());

            return response()->json([
                'success' => false,
                'cooldown' => true,
                'remaining' => $remaining,
                'message' =>
                    "Please wait {$remaining} seconds before requesting another OTP.",
            ], 429);
        }

        $isResend = $latestOtp !== null;

        $resendCount = $latestOtp
            ? (int) $latestOtp->resend_count
            : 0;

        if ($isResend) {
            $resendCount++;
        }

       // if reached max count already

        if (
            $resendCount > config('security.otp_max_resends')
        ) {

            $lockedUntil = now()->addSeconds(
                config('security.otp_resend_lock_seconds')
            );

            if ($latestOtp) {
                $latestOtp->update([
                    'locked_until' => $lockedUntil,
                ]);
            }

            return response()->json([
                'success' => false,
                'locked' => true,
                'remaining' => config('security.otp_resend_lock_seconds'),
                'message' =>
                    'Maximum resend attempts reached. Please wait 2 minutes before requesting another OTP.',
            ], 429);
        }


        $otp = random_int(100000, 999999);

        LoginOtp::where('email', $email)
            ->whereNull('used_at')
            ->delete();

        $newOtp = LoginOtp::create([
            'user_id' => $user->id,
            'email' => $email,
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(
                config('security.otp_expiry')
            ),
            'verification_attempts' => 0,
            'resend_count' => $resendCount,
            'locked_until' => null,
        ]);

        Mail::to($user->email)->send(
            new LoginOtpMail(
                $otp,
                config('security.otp_expiry')
            )
        );

        $request->session()->put('otp_email', $email);

        $resendsRemaining =
            max(
                0,
                config('security.otp_max_resends') - $resendCount
            );

        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'message' =>
                    'OTP has been sent to your registered email address.',

                'email' => $email,

                'expires_in' =>
                    (int) config('security.otp_expiry') * 60,

                'resend_in' =>
                    (int) config('security.otp_resend_seconds'),

                'resends_remaining' =>
                    $resendsRemaining,

                'verification_attempts_remaining' =>
                    config('security.otp_max_attempts'),

                'locked' => false,
            ]);
        }

        return redirect()
            ->route('otp.verify')
            ->with(
                'success',
                'OTP has been sent to your registered email address.'
            );
    }

    /**
     * Show OTP verification page
     */
    public function showVerifyOtp()
    {
        if (!session()->has('otp_email')) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Please request an OTP first.',
                ]);
        }

        return view('auth.verify-otp');
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
                'Your OTP session has expired. Please request a new OTP.'
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
                'Invalid OTP. Please request a new OTP.'
            );
        }
        if (
            $otpRecord->locked_until &&
            now()->lt($otpRecord->locked_until)
        ) {

            $remaining = now()->diffInSeconds(
                $otpRecord->locked_until
            );

            return response()->json([
                'success' => false,
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
                'This OTP has expired. Please request a new OTP.'
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
                    'locked' => true,
                    'remaining' => $remaining,
                    'message' =>
                        'Too many incorrect OTP attempts. ' .
                        'Please wait 2 minutes before requesting a new OTP.',
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
                'locked' => false,
                'attempts_remaining' => $remainingAttempts,
                'message' =>
                    "Incorrect OTP. You have {$remainingAttempts} attempt(s) remaining.",
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        $user = User::find($otpRecord->user_id);

        if (!$user) {
            return $this->authErrorResponse(
                $request,
                'otp',
                'User account could not be found.'
            );
        }

        if (!$user->status && $user->role_id !== 1) {
            return $this->authErrorResponse(
                $request,
                'otp',
                'Your account has been deactivated.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | OTP successful
        |--------------------------------------------------------------------------
        */

        $otpRecord->update([
            'used_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        $request->session()->regenerate();

        $request->session()->put(
            'agency_id',
            $user->agency_id
        );

        $request->session()->put(
            'last_activity',
            now()->timestamp
        );

        $request->session()->forget('otp_email');

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
                'message' =>
                    'Your OTP session has expired. Please request a new OTP.',
            ], 422);
        }
        $request->merge([
            'email' => $email,
        ]);

        return $this->sendOtp($request);
    }

    // Handle logout
    public function logout(Request $request)
    {
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

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'success',
                'Password reset link has been sent to your email.'
            );
        }

        return back()->withErrors([
            'email' => __($status),
        ]);
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
            'password' => 'required|min:8|confirmed',
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

    private function authErrorResponse(Request $request, string $field, string $message, int $status = 422)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        }

        return back()->withErrors([
            $field => $message,
        ]);
    }
}
