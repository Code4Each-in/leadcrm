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
        $credentials = $request->only('email', 'password');

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Invalid email or password.',
                ]);
        }



            $user = Auth::user();

            // Check if user is inactive (except super admin)
            if (!$user->status && $user->role_id !== 1) {
                Auth::logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Your account has been deactivated.',
                ]);
            }

            //Regenerate session after validation
            $request->session()->regenerate();

            // Store agency_id in session
            $request->session()->put('agency_id', $user->agency_id);
            //store activity timimg
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
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'No account exists with this email address.',
                ]);
        }

        // Check inactive user
        if (!$user->status && $user->role_id !== 1) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Your account has been deactivated.',
                ]);
        }

        // Check resend limit
        $recentOtp = LoginOtp::where('email', $email)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if ($recentOtp && $recentOtp->created_at->diffInSeconds(now()) <
            config('security.otp_resend_seconds')) {

            $remaining = config('security.otp_resend_seconds')
                - $recentOtp->created_at->diffInSeconds(now());

            return back()
                ->withInput()
                ->withErrors([
                    'email' => "Please wait {$remaining} seconds before requesting another OTP.",
                ]);
        }

        // Delete old unused OTPs
        LoginOtp::where('email', $email)
            ->whereNull('used_at')
            ->delete();

        // Generate 6 digit OTP
        $otp = random_int(100000, 999999);

        LoginOtp::create([
            'user_id' => $user->id,
            'email' => $email,
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(
                config('security.otp_expiry')
            ),
        ]);

        Mail::to($user->email)->send(
            new LoginOtpMail(
                $otp,
                config('security.otp_expiry')
            )
        );

        // Store email temporarily in session
        $request->session()->put('otp_email', $email);
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your registered email address.',
                'email' => $email,
                'expires_in' => (int) config('security.otp_expiry') * 60,
                'resend_in' => (int) config('security.otp_resend_seconds'),
            ]);
        }

        return redirect()
            ->route('otp.verify')
            ->with('success', 'OTP has been sent to your registered email address.');
        // return redirect()
        //     ->route('otp.verify')
        //     ->with('success', 'OTP has been sent to your registered email address.');
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
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your OTP session has expired. Please request a new OTP.',
                ]);
        }

        $otpRecord = LoginOtp::where('email', $email)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            // return back()->withErrors([
            //     'otp' => 'Invalid OTP. Please request a new OTP.',
            // ]);
            if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid OTP. Please check the code and try again.',
                    ], 422);
                }

                return back()->withErrors([
                    'otp' => 'Invalid OTP. Please request a new OTP.',
                ]);
        }

        if (now()->greaterThan($otpRecord->expires_at)) {

            $otpRecord->delete();

            // return back()->withErrors([
            //     'otp' => 'This OTP has expired. Please request a new OTP.',
            // ]);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This OTP has expired. Please request a new OTP.',
                ], 422);
            }

            return back()->withErrors([
                'otp' => 'This OTP has expired. Please request a new OTP.',
            ]);
        }

        if (!Hash::check($request->otp, $otpRecord->otp)) {
            return back()->withErrors([
                'otp' => 'Invalid OTP.',
            ]);
        }

        $user = User::find($otpRecord->user_id);

        if (!$user) {
            return back()->withErrors([
                'otp' => 'User account could not be found.',
            ]);
        }

        if (!$user->status && $user->role_id !== 1) {
            return back()->withErrors([
                'otp' => 'Your account has been deactivated.',
            ]);
        }

        // Mark OTP as used
        $otpRecord->update([
            'used_at' => now(),
        ]);

        // Login user
        Auth::login($user);

        $request->session()->regenerate();

        $request->session()->put('agency_id', $user->agency_id);

        $request->session()->put(
            'last_activity',
            now()->timestamp
        );

        // Remove temporary OTP session
        $request->session()->forget('otp_email');

        // return redirect()->intended('/dashboard');
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
            return redirect()->route('login');
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
}
