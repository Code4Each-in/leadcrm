<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign in · AGILE ONE</title>

    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vertical-layout-light/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/agile-favicon.png') }}">

    {{--
        Sign-in, forgot password and the OTP step share this page; one
        section is visible at a time. Look follows the app (indigo #4b49ac,
        soft cards, the dashboard banner gradient). General errors (wrong
        credentials, invalid / expired code, ...) are SweetAlert toasts like
        the rest of the app; field validation stays under its field.
    --}}
    <style>
        :root {
            --auth-primary: #4b49ac;
            --auth-primary-dark: #3f3d9e;
            --auth-ink: #1a1f2b;
            --auth-muted: #7a8294;
            --auth-border: #dfe2ec;
            --auth-soft: #f6f7fb;
            --auth-danger: #e5484d;
            --auth-success: #1a7a4c;
            --auth-warning: #b77900;
        }

        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            background:
                radial-gradient(1200px 500px at 100% 0%, rgba(121, 120, 233, .12), transparent 60%),
                radial-gradient(900px 400px at 0% 100%, rgba(75, 73, 172, .10), transparent 60%),
                #f4f5fb;
            color: var(--auth-ink);
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border: 1px solid #e9ebf0;
            border-radius: 22px;
            box-shadow: 0 30px 60px -30px rgba(38, 33, 92, .35);
            overflow: hidden;
        }

        /* ---------- Form panel ---------- */
        .auth-main {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 48px 40px;
        }

        .auth-logo {
            margin-bottom: 28px;
        }

        .auth-logo img {
            height: 38px;
            width: auto;
        }

        .auth-section[hidden] {
            display: none !important;
        }

        .auth-title {
            margin: 0 0 6px;
            font-size: 24px;
            font-weight: 700;
            color: var(--auth-ink);
        }

        .auth-subtitle {
            margin: 0 0 26px;
            font-size: 14px;
            line-height: 1.55;
            color: var(--auth-muted);
        }

        .auth-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 22px;
            padding: 0;
            border: 0;
            background: none;
            font-size: 13px;
            font-weight: 600;
            color: var(--auth-primary);
            text-decoration: none !important;
            cursor: pointer;
        }

        .auth-back:hover {
            color: var(--auth-primary-dark);
        }

        /* Fields */
        .auth-field {
            margin-bottom: 18px;
        }

        .auth-label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 600;
            color: #3b4152;
        }

        .auth-input-wrap {
            position: relative;
        }

        .auth-input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            color: #9aa1b2;
            pointer-events: none;
            transition: color .2s;
        }

        .auth-input {
            width: 100%;
            height: 48px;
            padding: 0 16px 0 42px;
            border: 1px solid var(--auth-border);
            border-radius: 11px;
            background: #fff;
            font-size: 14px;
            color: var(--auth-ink);
            transition: border-color .2s, box-shadow .2s;
        }

        .auth-input::placeholder {
            color: #aeb4c2;
        }

        .auth-input:focus {
            outline: none;
            border-color: var(--auth-primary);
            box-shadow: 0 0 0 4px rgba(75, 73, 172, .12);
        }

        .auth-input-wrap:focus-within .auth-input-icon {
            color: var(--auth-primary);
        }

        .auth-input.has-toggle {
            padding-right: 48px;
        }

        .auth-input.is-invalid {
            border-color: var(--auth-danger);
            background-image: none;
        }

        .auth-input.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(229, 72, 77, .12);
        }

        .auth-toggle {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 9px;
            background: transparent;
            color: #8a90a0;
            cursor: pointer;
        }

        .auth-toggle:hover {
            background: var(--auth-soft);
            color: var(--auth-primary);
        }

        .auth-toggle svg {
            width: 19px;
            height: 19px;
        }

        .field-error {
            display: none;
            align-items: flex-start;
            gap: 6px;
            margin-top: 7px;
            font-size: 12.5px;
            line-height: 1.4;
            color: var(--auth-danger);
        }

        .field-error.show {
            display: flex;
        }

        .field-error::before {
            content: '!';
            width: 15px;
            height: 15px;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 1px;
            border-radius: 50%;
            background: var(--auth-danger);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
        }

        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 2px 0 22px;
        }

        .auth-check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-size: 13px;
            color: #4b5163;
            cursor: pointer;
            user-select: none;
        }

        .auth-check input {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: var(--auth-primary);
        }

        .auth-link {
            padding: 0;
            border: 0;
            background: none;
            font-size: 13px;
            font-weight: 600;
            color: var(--auth-primary);
            text-decoration: none !important;
            cursor: pointer;
        }

        .auth-link:hover {
            color: var(--auth-primary-dark);
        }

        .auth-link:disabled {
            color: #aeb4c2;
            cursor: not-allowed;
        }

        .auth-btn {
            width: 100%;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 0;
            border-radius: 11px;
            background: var(--auth-primary);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .3px;
            box-shadow: 0 10px 22px -12px rgba(75, 73, 172, .8);
            transition: background .2s, transform .1s, box-shadow .2s;
        }

        .auth-btn:hover:not(:disabled) {
            background: var(--auth-primary-dark);
        }

        .auth-btn:active:not(:disabled) {
            transform: translateY(1px);
        }

        .auth-btn:disabled {
            opacity: .65;
            cursor: not-allowed;
            box-shadow: none;
        }

        .auth-btn .spinner-border {
            width: 16px;
            height: 16px;
            border-width: 2px;
        }

        .auth-foot {
            margin-top: 28px;
            font-size: 12px;
            color: #a3a9b8;
            text-align: center;
        }

        /* ---------- OTP ---------- */
        /* OTP: icon, title and subtitle centred. */
        .otp-head {
            text-align: center;
        }

        .otp-badge {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            border-radius: 15px;
            background: #eeedfe;
            color: var(--auth-primary);
            font-size: 22px;
        }

        .otp-email {
            font-weight: 600;
            color: var(--auth-ink);
        }

        .otp-inputs {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-bottom: 6px;
        }

        .otp-input {
            width: 100%;
            height: 54px;
            padding: 0;
            border: 1px solid var(--auth-border);
            border-radius: 11px;
            background: #fff;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            color: var(--auth-ink);
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        .otp-input:focus {
            outline: none;
            border-color: var(--auth-primary);
            box-shadow: 0 0 0 4px rgba(75, 73, 172, .12);
        }

        .otp-input.filled {
            border-color: #c9c8f0;
            background: #fafaff;
        }

        .otp-input:disabled {
            background: var(--auth-soft);
            color: #aeb4c2;
            cursor: not-allowed;
        }

        .otp-inputs.is-invalid .otp-input {
            border-color: var(--auth-danger);
        }

        .otp-inputs.shake {
            animation: otpShake .4s;
        }

        @keyframes otpShake {
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .otp-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 14px 0 20px;
            padding: 10px 14px;
            border-radius: 11px;
            background: var(--auth-soft);
            font-size: 13px;
            color: #4b5163;
        }

        .otp-status strong {
            font-variant-numeric: tabular-nums;
            color: var(--auth-primary);
        }

        .otp-status.is-warning {
            background: #fff6e5;
            color: #8a5a00;
        }

        .otp-status.is-warning strong {
            color: #8a5a00;
        }

        .otp-status.is-danger {
            background: #fdeced;
            color: #b3261e;
        }

        .otp-status.is-danger strong {
            color: #b3261e;
        }

        .otp-attempts {
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .otp-resend {
            margin-top: 18px;
            font-size: 13px;
            color: var(--auth-muted);
            text-align: center;
        }

        /* Toast - same SweetAlert2 toast as the app, a touch rounder. */
        .swal2-popup.swal2-toast {
            border-radius: 12px !important;
            box-shadow: 0 12px 30px -10px rgba(26, 31, 43, .3) !important;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 575.98px) {
            .auth-page {
                padding: 20px 14px;
            }

            .auth-card {
                border-radius: 18px;
            }

            .auth-main {
                padding: 30px 22px 26px;
            }

            .auth-logo {
                margin-bottom: 22px;
            }

            .auth-title {
                font-size: 21px;
            }

            .otp-inputs {
                gap: 7px;
            }

            .otp-input {
                height: 48px;
                font-size: 19px;
            }

            .auth-row {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>
@php
    $showOtp = $showOtp ?? false;

    // Everything the script needs, in one place.
    $authState = [
        'routes' => [
            'login' => route('login'),
            'verify' => route('otp.verify.submit'),
            'resend' => route('otp.resend'),
        ],
        'flash' => [
            'success' => session('success'),
            'error' => session('auth_error'),
        ],
        'otp' => $showOtp ? [
            'email' => $otpEmail ?? '',
            'dead' => (bool) ($otpDead ?? false),
            'locked' => (bool) ($otpLocked ?? false),
            'lockRemaining' => (int) ($otpLockRemaining ?? 0),
            'expiresIn' => (int) ($otpExpiresIn ?? 0),
            'resendIn' => (int) ($otpResendIn ?? 0),
            'attemptsRemaining' => (int) ($otpAttemptsRemaining ?? config('security.otp_max_attempts')),
        ] : null,
    ];

    $eyeClosed = '<path d="M2 2l20 20"></path><path d="M6.71 6.71C3.66 8.83 2 12 2 12s3.5 7 10 7c1.61 0 3.06-.38 4.35-1"></path><path d="M10.73 5.08C11.14 5.03 11.56 5 12 5c6.5 0 10 7 10 7s-1.66 3.17-4.71 5.29"></path><path d="M14 14.12A3 3 0 0 1 9.88 10"></path>';
@endphp

<div class="auth-page">
    <div class="auth-card">

        <main class="auth-main">
            <div class="auth-logo">
                <img src="{{ asset('assets/images/agile-logo.svg') }}" alt="AGILE ONE">
            </div>

            {{-- ============ SIGN IN ============ --}}
            <section class="auth-section" id="signinSection" @if($showOtp) hidden @endif>
                <h1 class="auth-title">Sign in</h1>
                <p class="auth-subtitle">Enter your work email and password to continue.</p>

                <form id="signinForm" action="{{ route('login.submit') }}" method="POST" novalidate>
                    @csrf

                    <div class="auth-field">
                        <label class="auth-label" for="loginEmail">Email address</label>
                        <div class="auth-input-wrap">
                            <i class="ti-email auth-input-icon"></i>
                            <input type="email" name="email" id="loginEmail" value="{{ old('email') }}"
                                   class="auth-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                   placeholder="you@company.com" autocomplete="email" autofocus>
                        </div>
                        <div class="field-error {{ $errors->has('email') ? 'show' : '' }}" id="loginEmailError">{{ $errors->first('email') }}</div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="password">Password</label>
                        <div class="auth-input-wrap">
                            <i class="ti-lock auth-input-icon"></i>
                            <input type="password" name="password" id="password"
                                   class="auth-input has-toggle {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   placeholder="Enter your password" autocomplete="current-password">
                            <button type="button" class="auth-toggle" id="togglePassword" aria-label="Show password">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">{!! $eyeClosed !!}</svg>
                            </button>
                        </div>
                        <div class="field-error {{ $errors->has('password') ? 'show' : '' }}" id="passwordError">{{ $errors->first('password') }}</div>
                    </div>

                    <div class="auth-row">
                        <label class="auth-check">
                            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                            Keep me signed in
                        </label>
                        <button type="button" class="auth-link" data-show="forgotSection">Forgot password?</button>
                    </div>

                    <button type="submit" class="auth-btn" id="signinButton">
                        <span>Sign in</span>
                    </button>
                </form>
            </section>

            {{-- ============ FORGOT PASSWORD ============ --}}
            <section class="auth-section" id="forgotSection" hidden>
                <button type="button" class="auth-back" data-show="signinSection">
                    <i class="ti-arrow-left"></i> Back to sign in
                </button>

                <h1 class="auth-title">Reset your password</h1>
                <p class="auth-subtitle">Enter your email and we'll send you a link to set a new password.</p>

                <form id="forgotForm" action="{{ route('password.email') }}" method="POST" novalidate>
                    @csrf

                    <div class="auth-field">
                        <label class="auth-label" for="forgotEmail">Email address</label>
                        <div class="auth-input-wrap">
                            <i class="ti-email auth-input-icon"></i>
                            <input type="email" name="email" id="forgotEmail" class="auth-input"
                                   placeholder="you@company.com" autocomplete="email">
                        </div>
                        <div class="field-error" id="forgotEmailError"></div>
                    </div>

                    <button type="submit" class="auth-btn" id="forgotButton">
                        <span>Send reset link</span>
                    </button>
                </form>
            </section>

            {{-- ============ OTP ============ --}}
            <section class="auth-section" id="otpSection" @unless($showOtp) hidden @endunless>
                <a href="{{ route('login') }}" class="auth-back">
                    <i class="ti-arrow-left"></i> Back to sign in
                </a>

                <div class="otp-head">
                    <div class="otp-badge"><i class="ti-shield"></i></div>
                    <h1 class="auth-title">Verify it's you</h1>
                    <p class="auth-subtitle">
                        Enter the 6-digit code we sent to<br>
                        <span class="otp-email" id="otpEmailDisplay"></span>
                    </p>
                </div>

                <div class="otp-inputs" id="otpInputs" role="group" aria-label="Verification code">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text" class="otp-input" inputmode="numeric" pattern="[0-9]*" maxlength="1"
                               aria-label="Digit {{ $i + 1 }}" @if($i === 0) autocomplete="one-time-code" @endif>
                    @endfor
                </div>
                <div class="field-error" id="otpError"></div>

                <div class="otp-status" id="otpStatus">
                    <span id="otpStatusText">Code expires in <strong id="otpTimer">--:--</strong></span>
                    <span class="otp-attempts" id="otpAttempts" hidden></span>
                </div>

                <button type="button" class="auth-btn" id="verifyButton">
                    <span>Verify &amp; sign in</span>
                </button>

                <div class="otp-resend">
                    Didn't get the code?
                    <button type="button" class="auth-link" id="resendButton" disabled>Resend code</button>
                </div>
            </section>

            <p class="auth-foot">&copy; {{ date('Y') }} AGILE ONE. All rights reserved.</p>
        </main>
    </div>
</div>

<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function () {
    'use strict';

    const STATE = @json($authState);
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const EYE_OPEN = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    const EYE_CLOSED = @json($eyeClosed);

    /* ---------------- Helpers ---------------- */

    // Same toast as the app (layout.blade.php), so errors look familiar.
    function toast(icon, title) {
        if (!window.Swal) { return; }
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            showConfirmButton: false,
            timer: icon === 'error' ? 4500 : 3000,
            timerProgressBar: true
        });
    }

    function $(id) { return document.getElementById(id); }

    function fmt(seconds) {
        seconds = Math.max(0, Math.ceil(seconds));
        return String(Math.floor(seconds / 60)).padStart(2, '0') + ':' + String(seconds % 60).padStart(2, '0');
    }

    function isEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }

    function setFieldError(input, el, message) {
        if (input) { input.classList.toggle('is-invalid', !!message); }
        el.textContent = message || '';
        el.classList.toggle('show', !!message);
    }

    function setBusy(button, busy, label) {
        if (!button.dataset.label) { button.dataset.label = button.innerHTML; }
        button.disabled = busy;
        button.innerHTML = busy
            ? '<span class="spinner-border" role="status" aria-hidden="true"></span><span>' + label + '</span>'
            : button.dataset.label;
    }

    // JSON POST that never throws on non-2xx; 419 (stale page / CSRF) is
    // reported as a session problem rather than a generic failure.
    function postJson(url, body) {
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(body || {})
        }).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (data) {
                if (res.status === 419) { data.code = 'session'; }
                return { status: res.status, data: data };
            });
        });
    }

    function backToLogin(message) {
        toast('error', message || 'Your session has expired. Please sign in again.');
        setTimeout(function () { window.location.href = STATE.routes.login; }, 1800);
    }

    function validateEmail(input, errorEl, focus) {
        const v = input.value.trim();
        const msg = v === '' ? 'Email address is required.'
            : (!isEmail(v) ? 'Please enter a valid email address.' : '');
        setFieldError(input, errorEl, msg);
        if (msg && focus) { input.focus(); }
        return !msg;
    }

    // Going back (or switching section) starts clean: no field errors,
    // no lingering toast.
    function clearAllErrors() {
        document.querySelectorAll('.field-error').forEach(function (el) { setFieldError(null, el, ''); });
        document.querySelectorAll('.auth-input').forEach(function (el) { el.classList.remove('is-invalid'); });
        if (window.Swal) { Swal.close(); }
    }

    /* ---------------- Flash messages from the server ---------------- */

    if (STATE.flash.success) { toast('success', STATE.flash.success); }
    if (STATE.flash.error) { toast('error', STATE.flash.error); }

    /* ---------------- Section switching (sign in <-> forgot) ---------------- */

    document.querySelectorAll('[data-show]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            ['signinSection', 'forgotSection'].forEach(function (id) {
                $(id).hidden = id !== btn.dataset.show;
            });
            clearAllErrors();

            const target = $(btn.dataset.show).querySelector('.auth-input');
            if (target) {
                if (btn.dataset.show === 'forgotSection' && !target.value) { target.value = $('loginEmail').value.trim(); }
                target.focus();
            }
        });
    });

    /* ---------------- Sign in ---------------- */

    const signinForm = $('signinForm');
    const loginEmail = $('loginEmail');
    const password = $('password');

    function validatePassword(focus) {
        const v = password.value;
        const msg = v === '' ? 'Password is required.'
            : (/\s/.test(v) ? 'Password must not contain spaces.'
            : (v.length < 8 ? 'Password must be at least 8 characters.' : ''));
        setFieldError(password, $('passwordError'), msg);
        if (msg && focus) { password.focus(); }
        return !msg;
    }

    // While typing: an emptied field drops its error; an invalid one
    // re-checks as the user fixes it.
    function liveCheck(input, errorEl, check) {
        input.addEventListener('input', function () {
            if (this.value.trim() === '') { setFieldError(this, errorEl, ''); return; }
            if (this.classList.contains('is-invalid')) { check(); }
        });
    }

    loginEmail.addEventListener('blur', function () { if (this.value.trim()) { validateEmail(this, $('loginEmailError')); } });
    liveCheck(loginEmail, $('loginEmailError'), function () { validateEmail(loginEmail, $('loginEmailError')); });
    liveCheck(password, $('passwordError'), function () { validatePassword(); });

    $('togglePassword').addEventListener('click', function () {
        const show = password.type === 'password';
        password.type = show ? 'text' : 'password';
        this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        this.querySelector('svg').innerHTML = show ? EYE_OPEN : EYE_CLOSED;
    });

    signinForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const emailOk = validateEmail(loginEmail, $('loginEmailError'), true);
        const passOk = validatePassword(emailOk);
        if (!emailOk || !passOk) { return; }

        setBusy($('signinButton'), true, 'Signing in...');
        signinForm.submit();
    });

    /* ---------------- Forgot password ---------------- */

    const forgotForm = $('forgotForm');
    const forgotEmail = $('forgotEmail');

    liveCheck(forgotEmail, $('forgotEmailError'), function () { validateEmail(forgotEmail, $('forgotEmailError')); });

    forgotForm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!validateEmail(forgotEmail, $('forgotEmailError'), true)) { return; }
        setBusy($('forgotButton'), true, 'Sending...');
        forgotForm.submit();
    });

    /* ---------------- OTP ---------------- */

    if (!STATE.otp) { return; }

    const otp = STATE.otp;
    const inputs = Array.from(document.querySelectorAll('.otp-input'));
    const inputsWrap = $('otpInputs');
    const verifyBtn = $('verifyButton');
    const resendBtn = $('resendButton');
    const statusBox = $('otpStatus');
    const statusText = $('otpStatusText');
    const attemptsEl = $('otpAttempts');

    let expiryTimer = null;
    let resendTimer = null;
    let lockTimer = null;
    let verifying = false;
    let mode = 'active'; // active | expired | locked

    $('otpEmailDisplay').textContent = otp.email;

    function code() { return inputs.map(function (i) { return i.value; }).join(''); }

    function setInputsEnabled(enabled) {
        inputs.forEach(function (i) { i.disabled = !enabled; });
        verifyBtn.disabled = !enabled;
    }

    function clearCode(focus) {
        inputs.forEach(function (i) { i.value = ''; i.classList.remove('filled'); });
        inputsWrap.classList.remove('is-invalid');
        setFieldError(null, $('otpError'), '');
        if (focus && !inputs[0].disabled) { inputs[0].focus(); }
    }

    function setStatus(tone, html) {
        statusBox.classList.remove('is-warning', 'is-danger');
        if (tone) { statusBox.classList.add(tone); }
        statusText.innerHTML = html;
    }

    function setAttempts(n) {
        attemptsEl.hidden = !(n > 0 && n < {{ (int) config('security.otp_max_attempts') }});
        attemptsEl.textContent = n === 1 ? '1 attempt left' : n + ' attempts left';
    }

    function stopTimers() {
        clearInterval(expiryTimer);
        clearInterval(lockTimer);
    }

    function startExpiry(seconds) {
        stopTimers();
        mode = 'active';
        setInputsEnabled(true);
        const endsAt = Date.now() + seconds * 1000;

        function tick() {
            const left = (endsAt - Date.now()) / 1000;
            if (left <= 0) { setExpired(true); return; }
            setStatus(left <= 60 ? 'is-warning' : null, 'Code expires in <strong>' + fmt(left) + '</strong>');
        }
        tick();
        expiryTimer = setInterval(tick, 1000);
    }

    function startResendCooldown(seconds) {
        clearInterval(resendTimer);
        const endsAt = Date.now() + seconds * 1000;

        function tick() {
            const left = (endsAt - Date.now()) / 1000;
            if (mode === 'locked') { return; }
            if (left <= 0) {
                clearInterval(resendTimer);
                resendBtn.disabled = false;
                resendBtn.textContent = 'Resend code';
                return;
            }
            resendBtn.disabled = true;
            resendBtn.textContent = 'Resend in ' + fmt(left);
        }
        tick();
        resendTimer = setInterval(tick, 1000);
    }

    function setExpired(notify) {
        stopTimers();
        clearInterval(resendTimer);
        mode = 'expired';
        setInputsEnabled(false);
        attemptsEl.hidden = true;
        setStatus('is-warning', 'This code has expired. Request a new one below.');
        resendBtn.disabled = false;
        resendBtn.textContent = 'Resend code';
        if (notify) { toast('warning', 'Your code has expired. Please request a new one.'); }
    }

    // Too many wrong codes / resends: everything waits, then the page
    // reloads so the server decides what's next (new code or sign in).
    function setLocked(seconds) {
        stopTimers();
        clearInterval(resendTimer);
        mode = 'locked';
        setInputsEnabled(false);
        resendBtn.disabled = true;
        resendBtn.textContent = 'Resend code';
        attemptsEl.hidden = true;
        const endsAt = Date.now() + seconds * 1000;

        function tick() {
            const left = (endsAt - Date.now()) / 1000;
            if (left <= 0) {
                clearInterval(lockTimer);
                window.location.reload();
                return;
            }
            setStatus('is-danger', 'Too many attempts. Try again in <strong>' + fmt(left) + '</strong>');
        }
        tick();
        lockTimer = setInterval(tick, 1000);
    }

    /* Typing, backspace, arrows and paste across the six boxes. */
    inputs.forEach(function (input, index) {
        input.addEventListener('input', function () {
            const digits = this.value.replace(/\D/g, '');
            if (digits.length > 1) { fill(digits, index); return; }
            this.value = digits;
            this.classList.toggle('filled', !!digits);
            inputsWrap.classList.remove('is-invalid');
            setFieldError(null, $('otpError'), '');
            if (digits && index < inputs.length - 1) { inputs[index + 1].focus(); }
            if (code().length === 6) { verify(); }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                inputs[index - 1].value = '';
                inputs[index - 1].classList.remove('filled');
                inputs[index - 1].focus();
                e.preventDefault();
            } else if (e.key === 'ArrowLeft' && index > 0) {
                inputs[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                inputs[index + 1].focus();
            } else if (e.key === 'Enter') {
                verify();
            }
        });

        input.addEventListener('focus', function () { this.select(); });

        input.addEventListener('paste', function (e) {
            e.preventDefault();
            fill((e.clipboardData || window.clipboardData).getData('text'), 0);
        });
    });

    function fill(text, from) {
        const digits = String(text).replace(/\D/g, '').slice(0, 6 - from).split('');
        digits.forEach(function (d, i) {
            inputs[from + i].value = d;
            inputs[from + i].classList.add('filled');
        });
        const next = Math.min(from + digits.length, inputs.length - 1);
        inputs[next].focus();
        if (code().length === 6) { verify(); }
    }

    function verify() {
        if (verifying || mode !== 'active') { return; }

        const value = code();
        if (value.length !== 6) {
            inputsWrap.classList.add('is-invalid');
            setFieldError(null, $('otpError'), 'Please enter the complete 6-digit code.');
            const firstEmpty = inputs.find(function (i) { return !i.value; });
            if (firstEmpty) { firstEmpty.focus(); }
            return;
        }

        verifying = true;
        setBusy(verifyBtn, true, 'Verifying...');

        postJson(STATE.routes.verify, { otp: value }).then(function (r) {
            const d = r.data;

            if (r.status === 200 && d.success) {
                verifyBtn.innerHTML = '<span class="spinner-border" role="status" aria-hidden="true"></span><span>Signing you in...</span>';
                window.location.href = d.redirect;
                return;
            }

            verifying = false;
            setBusy(verifyBtn, false);

            if (d.code === 'session') { backToLogin(d.message); return; }

            if (d.code === 'locked' || (r.status === 429 && d.locked)) {
                toast('error', d.message || 'Too many incorrect attempts. Please wait and try again.');
                clearCode(false);
                setLocked(d.remaining || 120);
                return;
            }

            if (d.code === 'expired') {
                toast('error', d.message);
                clearCode(false);
                setExpired(false);
                return;
            }

            // Field validation from the server (e.g. not 6 digits).
            if (d.errors && d.errors.otp) {
                inputsWrap.classList.add('is-invalid');
                setFieldError(null, $('otpError'), d.errors.otp[0]);
                return;
            }

            // Wrong code.
            toast('error', d.message || 'Incorrect code. Please try again.');
            clearCode(true);
            inputsWrap.classList.add('is-invalid', 'shake');
            setTimeout(function () { inputsWrap.classList.remove('shake'); }, 450);
            if (typeof d.attempts_remaining === 'number') { setAttempts(d.attempts_remaining); }
        }).catch(function () {
            verifying = false;
            setBusy(verifyBtn, false);
            toast('error', 'Something went wrong. Please check your connection and try again.');
        });
    }

    verifyBtn.addEventListener('click', verify);

    resendBtn.addEventListener('click', function () {
        if (resendBtn.disabled) { return; }
        resendBtn.disabled = true;
        resendBtn.textContent = 'Sending...';

        postJson(STATE.routes.resend).then(function (r) {
            const d = r.data;

            if (r.status === 200 && d.success) {
                toast('success', 'A new code has been sent to your email.');
                clearCode(true);
                setAttempts({{ (int) config('security.otp_max_attempts') }});
                startExpiry(d.expires_in);
                startResendCooldown(d.resend_in);
                return;
            }

            if (d.code === 'session') { backToLogin(d.message); return; }

            if (r.status === 429 && d.locked) {
                toast('error', d.message || 'Too many requests. Please wait before requesting a new code.');
                setLocked(d.remaining || 120);
                return;
            }

            if (r.status === 429 && d.cooldown) {
                toast('info', d.message);
                startResendCooldown(d.remaining);
                return;
            }

            toast('error', d.message || 'Unable to send a new code. Please try again.');
            resendBtn.disabled = false;
            resendBtn.textContent = 'Resend code';
        }).catch(function () {
            resendBtn.disabled = false;
            resendBtn.textContent = 'Resend code';
            toast('error', 'Something went wrong. Please check your connection and try again.');
        });
    });

    /* Initial state from the server. */
    setAttempts(otp.attemptsRemaining);

    if (otp.locked) {
        setLocked(otp.lockRemaining);
    } else if (otp.dead) {
        setExpired(false);
    } else {
        startExpiry(otp.expiresIn);
        startResendCooldown(otp.resendIn);
        inputs[0].focus();
    }
})();
</script>
</body>

</html>
