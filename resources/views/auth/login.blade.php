<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AGILE ONE</title>

    <!-- Feather -->
    <link rel="stylesheet"
        href="{{ asset('assets/vendors/feather/feather.css') }}">

    <!-- Themify -->
    <link rel="stylesheet"
        href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">

    <!-- Vendor -->
    <link rel="stylesheet"
        href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">

    <!-- SkyDash -->
    <link rel="stylesheet"
        href="{{ asset('assets/css/vertical-layout-light/style.css') }}">

    <link rel="shortcut icon"
        href="{{ asset('assets/images/agile-favicon.png') }}">


    <style>
        body {
            background: #f5f7ff;
        }

        .auth-form-light {
            background: #ffffff;
            border-radius: 0;
        }

        .brand-logo img {
            max-width: 170px;
            max-height: 60px;
            object-fit: contain;
        }

        .form-control {
            height: 52px;
            border: 1px solid #d8dce5;
            border-radius: 2px;
            font-size: 14px;
            padding: 0 18px;
            color: #333;
            transition: all .2s ease;
        }

        .form-control:focus {
            border-color: #4b49ac;
            box-shadow: none;
        }

        .form-control::placeholder {
            color: #b8b8b8;
        }
        .auth-form-btn {
            height: 52px;
            border-radius: 1.1875rem !important;
            ;
            background: #4b49ac !important;
            border-color: #4b49ac !important;
            font-size: 14px;
            letter-spacing: .3px;
        }

        .auth-form-btn:hover {
            background: #3f3e94 !important;
            border-color: #3f3e94 !important;
        }

        .auth-form-btn:disabled {
            opacity: .7;
        }

        .otp-login-btn {
            height: 52px;
            border: 1px solid #4b49ac;
            color: #4b49ac;
            background: #fff;
            border-radius: 2px;
            font-weight: 500;
            transition: all .2s ease;
        }

        .otp-login-btn:hover {
            background: #4b49ac;
            color: #fff;
        }

        .login-divider {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 22px 0;
            color: #aaa;
            font-size: 12px;
        }
        .auth-link{
            text-decoration: none !important;
        }
        .login-divider::before,
        .login-divider::after {
            content: "";
            height: 1px;
            background: #e5e5e5;
            flex: 1;
        }


        .field-error {
            display: none;
            color: #dc3545;
            font-size: 14px;
            margin-top: 6px;
        }

        .field-error.show {
            display: block;
        }

        .modern-input.input-error {
            border-color: #dc3545 !important;
        }

        .modern-input.input-success {
            border-color: #28a745 !important;
        }
        #otpSection {
            display: none;
        }

        .otp-description {
            color: #777;
            font-size: 13px;
            line-height: 1.6;
        }

        .otp-email {
            color: #4b49ac;
            font-weight: 600;
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 25px 0;
        }

        .otp-input {
            width: 45px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            border: 1px solid #d8dce5;
            border-radius: 3px;
            outline: none;
        }

        .otp-input:focus {
            border-color: #4b49ac;
            box-shadow: 0 0 0 2px rgba(75, 73, 172, .08);
        }

        .otp-timer {
            font-size: 13px;
            color: #777;
            margin-bottom: 18px;
        }

        .otp-timer strong {
            color: #4b49ac;
        }

        .resend-btn {
            background: none;
            border: none;
            color: #4b49ac;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            padding: 0;
        }

        .resend-btn:disabled {
            color: #b8b8b8;
            cursor: not-allowed;
        }

        .back-login {
            color: #4b49ac;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none !important;
        }

        .back-login:hover {
            text-decoration: underline;
        }
       .password-input-wrapper {
            position: relative;
            width: 100%;
        }

        .password-input-wrapper .password-input,
        .password-input-wrapper input {
            padding-right: 55px !important;
        }

        .password-toggle {
            position: absolute !important;

            right: 12px !important;
            top: 50% !important;

            transform: translateY(-50%) !important;

            width: 36px !important;
            height: 36px !important;

            padding: 0 !important;
            margin: 0 !important;

            display: flex !important;
            align-items: center !important;
            justify-content: center !important;

            border: none !important;
            background: transparent !important;

            color: #6c757d !important;

            cursor: pointer !important;

            z-index: 10 !important;
        }

        .password-toggle:hover {
            color: #4b49ac !important;
        }

        .password-toggle:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        .password-eye-icon {
            width: 20px !important;
            height: 20px !important;

            display: block !important;

            pointer-events: none;
        }
        @media (max-width: 576px) {

            .auth-form-light {
                padding: 35px 25px !important;
            }

            .brand-logo img {
                max-width: 145px;
            }

            .otp-inputs {
                gap: 5px;
            }

            .otp-input {
                width: 40px;
                height: 46px;
            }

        }
    </style>

</head>


<body>

    <div class="container-scroller">

        <div class="container-fluid page-body-wrapper full-page-wrapper">

            <div class="content-wrapper d-flex align-items-center auth px-0">

                <div class="row w-100 mx-0">

                    <div class="col-lg-4 col-md-6 mx-auto">

                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">


                            <!-- LOGO -->

                            <div class="brand-logo">

                                <img
                                    src="{{ asset('assets/images/logo-dummy.svg') }}"
                                    alt="AGILE ONE">

                            </div>

                            @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show modern-alert">
                                <i class="ti-check mr-2"></i>
                                {{ session('success') }}

                                <button
                                    type="button"
                                    class="close"
                                    data-dismiss="alert"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            <div id="passwordLoginSection">

                                <h4>Hello! let's get started</h4>

                                <h6 class="font-weight-light">
                                    Sign in to continue.
                                </h6>


                                <form id="passwordLoginForm" action="{{ route('login.submit') }}" method="POST" class="pt-3" novalidate>

                                    @csrf

                                    <!-- EMAIL -->

                                    <div class="form-group">

                                        <input type="email" name="email" id="loginEmail" value="{{ old('email') }}" class="form-control form-control-lg modern-input" placeholder="Username"
                                            autocomplete="email">

                                        <div id="loginEmailError" class="field-error {{ $errors->has('email') ? 'show' : '' }}">
                                            @if ($errors->has('email'))
                                            {{ $errors->first('email') }}
                                            @endif
                                        </div>

                                    </div>


                                    <!-- PASSWORD -->

                                    <div class="form-group ">


                                        <div class="password-input-wrapper">
                                            <input
                                                type="password"
                                                name="password"
                                                id="password"
                                                class="form-control form-control-lg modern-input"
                                                placeholder="Password"
                                                value="{{ old('password') }}"
                                                autocomplete="current-password">

                                            <button
                                                type="button"
                                                id="togglePassword"
                                                class="password-toggle"
                                                aria-label="Show password">

                                                <svg
                                                    class="password-eye-icon"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round">

                                                    <!-- Closed eye initially -->
                                                    <path d="M2 2l20 20"></path>
                                                    <path d="M6.71 6.71C3.66 8.83 2 12 2 12s3.5 7 10 7c1.61 0 3.06-.38 4.35-1"></path>
                                                    <path d="M10.73 5.08C11.14 5.03 11.56 5 12 5c6.5 0 10 7 10 7s-1.66 3.17-4.71 5.29"></path>
                                                    <path d="M14 14.12A3 3 0 0 1 9.88 10"></path>

                                                </svg>
                                            </button>



                                        </div>
                                        <div id="passwordError" class="field-error {{ $errors->has('password') ? 'show' : '' }}">
                                            @if ($errors->has('password'))
                                            {{ $errors->first('password') }}
                                            @endif
                                        </div>

                                    </div>


                                    <!-- SIGN IN -->

                                    <div class="mt-3">

                                        <button
                                            type="submit"
                                            id="loginButton"
                                            class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">

                                            <span id="loginButtonText">
                                                SIGN IN
                                            </span>

                                        </button>

                                    </div>


                                    <!-- REMEMBER + FORGOT -->

                                    <div class="my-2 d-flex justify-content-between align-items-center">

                                        <div class="form-check">
                                            <label class="form-check-label text-muted">

                                                <input
                                                    type="checkbox"
                                                    name="remember"
                                                    value="1"
                                                    class="form-check-input"
                                                    id="remember"
                                                    {{ old('remember') ? 'checked' : '' }}>

                                                Keep me signed in

                                            </label>
                                        </div>


                                        <a
                                            href="javascript:void(0)"
                                            id="forgotPasswordLink"
                                            class="auth-link text-black">
                                            Forgot password?
                                        </a>

                                    </div>
                                </form>

                            </div>


                            <div id="otpEmailSection" style="display:none;">

                                <a
                                    href="javascript:void(0)"
                                    class="back-login"
                                    id="backToPassword">
                                    <i class="ti-arrow-left mr-1"></i>
                                    Back to password login
                                </a>

                                <div class="mt-4">

                                    <h4>Sign in with OTP</h4>

                                    <h6 class="font-weight-light">
                                        Enter your registered email address.
                                    </h6>

                                </div>


                                <form id="otpEmailForm" novalidate>

                                    @csrf

                                    <div class="form-group">

                                        <input
                                            type="email"
                                            id="otpEmail"
                                            class="form-control form-control-lg modern-input"
                                            placeholder="Email Address"
                                            autocomplete="email">

                                        <div
                                            id="otpEmailError"
                                            class="field-error"></div>

                                    </div>

                                    <div class="mt-3">

                                        <button
                                            type="submit"
                                            id="sendOtpButton"
                                            class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">

                                            <span id="sendOtpText">
                                                SEND OTP
                                            </span>

                                        </button>

                                    </div>

                                </form>

                            </div>

                            <div id="otpSection">

                                <a href="javascript:void(0)" class="back-login" id="backToOtpEmail">
                                    <i class="ti-arrow-left mr-1"></i>
                                    Back to Login
                                </a>


                                <div class="mt-4 text-center">

                                    <h4>Verify your email</h4>

                                    <p class="otp-description">

                                        We've sent a 6-digit verification code to

                                        <br>

                                        <span
                                            id="otpEmailDisplay"
                                            class="otp-email"></span>

                                    </p>

                                </div>


                                <!-- OTP INPUTS -->

                                <div class="otp-inputs">

                                    <input type="text" maxlength="1" class="otp-input" inputmode="numeric" autocomplete="one-time-code">

                                    <input
                                        type="text"
                                        maxlength="1"
                                        class="otp-input"
                                        inputmode="numeric">

                                    <input
                                        type="text"
                                        maxlength="1"
                                        class="otp-input"
                                        inputmode="numeric">

                                    <input
                                        type="text"
                                        maxlength="1"
                                        class="otp-input"
                                        inputmode="numeric">

                                    <input
                                        type="text"
                                        maxlength="1"
                                        class="otp-input"
                                        inputmode="numeric">

                                    <input
                                        type="text"
                                        maxlength="1"
                                        class="otp-input"
                                        inputmode="numeric">

                                </div>

                                <!-- VERIFY ERROR -->

                                <div
                                    id="otpVerifyError"
                                    class="field-error text-center"
                                    style="margin-bottom: 12px;"></div>


                                <!-- TIMER -->

                                <div class="text-center otp-timer">

                                    Code expires in

                                    <strong id="otpTimer">
                                        00:00
                                    </strong>

                                </div>


                                <!-- VERIFY -->

                                <div>

                                    <button
                                        type="button"
                                        id="verifyOtpButton"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                        onclick="verifyOtp()">
                                        <span id="verifyOtpText">
                                            VERIFY OTP
                                        </span>
                                    </button>

                                </div>


                                <!-- RESEND -->

                                <div class="text-center mt-3">

                                    <span class="text-muted small">
                                        Didn't receive the code?
                                    </span>

                                    <button
                                        type="button"
                                        class="resend-btn"
                                        id="resendButton"
                                        onclick="resendOtp()"
                                        disabled>
                                        Resend OTP
                                    </button>

                                    <div id="resendLockTimer" class="text-center mt-2 text-danger" style="display:none;"></div>
                                </div>

                            </div>


                            <!-- =================================================
                             FORGOT PASSWORD
                        ================================================== -->

                            <div
                                id="forgotPasswordSection"
                                style="display:none;">

                                <a
                                    href="javascript:void(0)"
                                    class="back-login"
                                    id="backFromForgotPassword">
                                    <i class="ti-arrow-left mr-1"></i>
                                    Back to login
                                </a>


                                <div class="mt-4">

                                    <h4>Forgot password?</h4>

                                    <h6 class="font-weight-light">
                                        Enter your email and we'll send you a
                                        password reset link.
                                    </h6>

                                </div>


                                <form
                                    id="forgotPasswordForm"
                                    action="{{ route('password.email') }}"
                                    method="POST"
                                    class="pt-3"
                                    novalidate>

                                    @csrf


                                    <div class="form-group">

                                        <input
                                            type="email"
                                            id="forgotEmail"
                                            name="email"
                                            class="form-control form-control-lg modern-input"
                                            placeholder="Email Address"
                                            autocomplete="email">

                                        <div
                                            id="forgotEmailError"
                                            class="field-error"></div>

                                    </div>


                                    <div class="mt-3">

                                        <button
                                            type="submit"
                                            id="forgotPasswordButton"
                                            class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">

                                            <span id="forgotPasswordText">
                                                SEND RESET LINK
                                            </span>

                                        </button>

                                    </div>

                                </form>

                            </div>


                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>

    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>

    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>

    <script src="{{ asset('assets/js/template.js') }}"></script>

    <script src="{{ asset('assets/js/settings.js') }}"></script>

    <script src="{{ asset('assets/js/todolist.js') }}"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            function showError(input, errorElement, message) {
                input.classList.remove('input-success');
                input.classList.add('input-error');
                errorElement.textContent = message;
                errorElement.classList.add('show');
            }

            function showSuccess(input, errorElement) {
                input.classList.remove('input-error');
                input.classList.add('input-success');
                errorElement.textContent = '';
                errorElement.classList.remove('show');
            }

            function clearValidation(input, errorElement) {
                input.classList.remove('input-error', 'input-success');
                errorElement.textContent = '';
                errorElement.classList.remove('show');
            }
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (togglePassword && passwordInput) {

                togglePassword.addEventListener('click', function () {

                    const icon = this.querySelector('.password-eye-icon');

                    if (passwordInput.type === 'password') {

                        // Show password
                        passwordInput.type = 'text';

                        this.setAttribute('aria-label', 'Hide password');

                        // Open eye
                        icon.innerHTML = `
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        `;

                    } else {

                        // Hide password
                        passwordInput.type = 'password';

                        this.setAttribute('aria-label', 'Show password');

                        // Closed eye
                        icon.innerHTML = `
                            <path d="M2 2l20 20"></path>
                            <path d="M6.71 6.71C3.66 8.83 2 12 2 12s3.5 7 10 7c1.61 0 3.06-.38 4.35-1"></path>
                            <path d="M10.73 5.08C11.14 5.03 11.56 5 12 5c6.5 0 10 7 10 7s-1.66 3.17-4.71 5.29"></path>
                            <path d="M14 14.12A3 3 0 0 1 9.88 10"></path>
                        `;
                    }
                });
            }


            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content');


            /*
            | SECTION SWITCHING
            */

            const passwordLoginSection = document.getElementById('passwordLoginSection');
            const otpSection = document.getElementById('otpSection');
            const forgotPasswordSection = document.getElementById('forgotPasswordSection');



            document.getElementById('forgotPasswordLink').addEventListener('click', function() {
                showSection(forgotPasswordSection);
            });

            document.getElementById('backFromForgotPassword').addEventListener('click', function() {
                showSection(passwordLoginSection);
            });

            document.getElementById('backToOtpEmail').addEventListener('click', function() {
                clearInterval(otpTimerInterval);
                showSection(passwordLoginSection);
            });

            function startResendTimer(seconds) {

                const button =
                    document.getElementById('resendButton');

                if (!button) return;


                if (seconds <= 0) {

                    button.disabled = false;

                    button.textContent = 'Resend OTP';

                    return;
                }


                button.disabled = true;


                let remaining = seconds;


                const timer =
                    setInterval(() => {

                        const minutes =
                            Math.floor(remaining / 60);

                        const secs =
                            remaining % 60;


                        button.textContent =
                            `Resend OTP in ${
                        String(minutes).padStart(2, '0')
                    }:${
                        String(secs).padStart(2, '0')
                    }`;


                        if (remaining <= 0) {

                            clearInterval(timer);

                            button.disabled = false;

                            button.textContent =
                                'Resend OTP';

                            return;
                        }


                        remaining--;

                    }, 1000);
            }

            function startLockTimer(seconds) {

                const button =
                    document.getElementById('resendButton');

                const lockTimer =
                    document.getElementById('resendLockTimer');

                if (!button || !lockTimer) return;


                button.disabled = true;

                let remaining = seconds;


                function update() {

                    const minutes =
                        Math.floor(remaining / 60);

                    const secs =
                        remaining % 60;


                    const formatted =
                        String(minutes).padStart(2, '0') +
                        ':' +
                        String(secs).padStart(2, '0');


                    lockTimer.style.display = 'block';

                    lockTimer.textContent =
                        `Maximum resend attempts reached. Try again in ${formatted}`;



                    if (remaining <= 0) {

                        clearInterval(timer);

                        lockTimer.style.display = 'none';

                        button.disabled = false;

                        button.textContent = 'Resend OTP';

                        /*
                        | Start fresh cycle
                        */

                        if (typeof startResendTimer === 'function') {
                            startResendTimer(0);
                        }

                        return;
                    }


                    remaining--;
                }


                update();

                const timer =
                    setInterval(update, 1000);
            }
            /*
            | PASSWORD LOGIN
            */

            const loginForm = document.getElementById('passwordLoginForm');
            const loginEmail = document.getElementById('loginEmail');
            const loginEmailError = document.getElementById('loginEmailError');
            const password = document.getElementById('password');
            const passwordError = document.getElementById('passwordError');

            if (loginEmail) {
                loginEmail.addEventListener('input', function() {
                    const value = this.value.trim();
                    if (value === '') {
                        clearValidation(this, loginEmailError);
                        return;
                    }
                    if (!isValidEmail(value)) {
                        showError(this, loginEmailError, 'Please enter a valid email address.');
                        return;
                    }
                    showSuccess(this, loginEmailError);
                });

                loginEmail.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (value === '') {
                        showError(this, loginEmailError, 'Email address is required.');
                        return;
                    }
                    if (!isValidEmail(value)) {
                        showError(this, loginEmailError, 'Please enter a valid email address.');
                        return;
                    }
                    showSuccess(this, loginEmailError);
                });
            }

            if (password) {
                password.addEventListener('input', function() {
                    const value = this.value;
                    if (value === '') {
                        clearValidation(this, passwordError);
                        return;
                    }
                    if (value.length < 6) {
                        showError(this, passwordError, 'Password must be at least 6 characters.');
                        return;
                    }
                    showSuccess(this, passwordError);
                });

                password.addEventListener('blur', function() {
                    const value = this.value;
                    if (value === '') {
                        showError(this, passwordError, 'Password is required.');
                        return;
                    }
                    if (value.length < 6) {
                        showError(this, passwordError, 'Password must be at least 6 characters.');
                        return;
                    }
                    showSuccess(this, passwordError);
                });
            }

            if (loginForm) {
                loginForm.addEventListener('submit', function(event) {
                    event.preventDefault();

                    let valid = true;

                    const emailValue = loginEmail.value.trim();

                    if (emailValue === '') {
                        showError(loginEmail, loginEmailError, 'Email address is required.');
                        loginEmail.focus();
                        valid = false;
                    } else if (!isValidEmail(emailValue)) {
                        showError(loginEmail, loginEmailError, 'Please enter a valid email address.');
                        loginEmail.focus();
                        valid = false;
                    } else {
                        showSuccess(loginEmail, loginEmailError);
                    }

                    const passwordValue = password.value;

                    if (passwordValue === '') {
                        showError(password, passwordError, 'Password is required.');
                        if (valid) password.focus();
                        valid = false;
                    } else if (passwordValue.length < 6) {
                        showError(password, passwordError, 'Password must be at least 6 characters.');
                        if (valid) password.focus();
                        valid = false;
                    } else {
                        showSuccess(password, passwordError);
                    }

                    if (!valid) return;

                    const button = document.getElementById('loginButton');
                    const buttonText = document.getElementById('loginButtonText');

                    if (button) button.disabled = true;
                    if (buttonText) {
                        buttonText.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> SIGNING IN...';
                    }

                    loginForm.submit();
                });
            }


            /*
            | OTP EMAIL VALIDATION
            */

            const otpForm = document.getElementById('otpEmailForm');
            const otpEmail = document.getElementById('otpEmail');
            const otpEmailError = document.getElementById('otpEmailError');

            if (otpEmail) {
                otpEmail.addEventListener('input', function() {
                    const value = this.value.trim();
                    if (value === '') {
                        clearValidation(this, otpEmailError);
                        return;
                    }
                    if (!isValidEmail(value)) {
                        showError(this, otpEmailError, 'Please enter a valid email address.');
                        return;
                    }
                    showSuccess(this, otpEmailError);
                });

                otpEmail.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (value === '') {
                        showError(this, otpEmailError, 'Email address is required.');
                        return;
                    }
                    if (!isValidEmail(value)) {
                        showError(this, otpEmailError, 'Please enter a valid email address.');
                        return;
                    }
                    showSuccess(this, otpEmailError);
                });
            }


            /*
            | OTP TIMER / HELPERS
            */

            let otpTimerInterval = null;

            function startOtpTimer(expiresInSeconds, resendInSeconds) {
                clearInterval(otpTimerInterval);

                const timerEl = document.getElementById('otpTimer');
                const resendBtn = document.getElementById('resendButton');

                let remaining = expiresInSeconds;
                let resendRemaining = resendInSeconds;

                resendBtn.disabled = true;

                function tick() {
                    const m = String(Math.floor(Math.max(remaining, 0) / 60)).padStart(2, '0');
                    const s = String(Math.max(remaining, 0) % 60).padStart(2, '0');
                    timerEl.textContent = m + ':' + s;

                    if (resendRemaining <= 0) {

                        /*
                        | Only enable if we are not currently locked
                        */

                        if (!window.otpLocked) {
                            resendBtn.disabled = false;
                            resendBtn.textContent = 'Resend OTP';
                        }

                    } else {

                        resendBtn.disabled = true;

                        resendBtn.textContent =
                            `Resend OTP in ${
                        String(Math.floor(resendRemaining / 60)).padStart(2, '0')
                    }:${
                        String(resendRemaining % 60).padStart(2, '0')
                    }`;

                        resendRemaining--;
                    }

                    if (remaining <= 0) {
                        clearInterval(otpTimerInterval);
                        return;
                    }

                    remaining--;
                }

                tick();
                otpTimerInterval = setInterval(tick, 1000);
            }

            function maskEmail(email) {
                if (!email) return '';
                const parts = email.split('@');
                if (parts.length !== 2) return email;
                const name = parts[0];
                if (name.length <= 2) return name.charAt(0) + '***@' + parts[1];
                return name.substring(0, 2) + '***@' + parts[1];
            }

            function clearOtpInputs() {
                document.querySelectorAll('.otp-input').forEach(function(input) {
                    input.value = '';
                });

                const first = document.querySelector('.otp-input');
                if (first) first.focus();

                const otpVerifyError = document.getElementById('otpVerifyError');
                if (otpVerifyError) {
                    otpVerifyError.textContent = '';
                    otpVerifyError.classList.remove('show');
                }
            }


            /*
            | OTP INPUT BEHAVIOUR
            */

            const otpInputs = document.querySelectorAll('.otp-input');

            otpInputs.forEach(function(input, index) {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '');
                    if (this.value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Backspace' && !this.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', function(event) {
                    event.preventDefault();

                    const pasted = (event.clipboardData || window.clipboardData)
                        .getData('text')
                        .replace(/\D/g, '')
                        .substring(0, 6);

                    pasted.split('').forEach(function(digit, i) {
                        if (otpInputs[i]) otpInputs[i].value = digit;
                    });

                    if (otpInputs[pasted.length - 1]) otpInputs[pasted.length - 1].focus();
                });
            });


            /*
            | SEND OTP (AJAX)
            */

            if (otpForm) {
                otpForm.addEventListener('submit', function(event) {
                    event.preventDefault();

                    const email = otpEmail.value.trim();

                    if (email === '') {
                        showError(otpEmail, otpEmailError, 'Email address is required.');
                        otpEmail.focus();
                        return;
                    }

                    if (!isValidEmail(email)) {
                        showError(otpEmail, otpEmailError, 'Please enter a valid email address.');
                        otpEmail.focus();
                        return;
                    }

                    showSuccess(otpEmail, otpEmailError);

                    const btn = document.getElementById('sendOtpButton');
                    const btnText = document.getElementById('sendOtpText');

                    btn.disabled = true;
                    btnText.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> SENDING...';

                    fetch('{{ route("otp.send") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                email: email
                            })
                        })
                        .then(function(res) {
                            return res.json().then(function(data) {
                                return {
                                    status: res.status,
                                    data: data
                                };
                            });
                        })
                        .then(function(result) {
                            btn.disabled = false;
                            btnText.textContent = 'SEND OTP';

                            if (result.status === 200 && result.data.success) {
                                document.getElementById('otpEmailDisplay').textContent = result.data.email;
                                clearOtpInputs();
                                showSection(otpSection);
                                startOtpTimer(result.data.expires_in, result.data.resend_in);
                            } else {
                                const msg = (result.data.errors && result.data.errors.email && result.data.errors.email[0]) ||
                                    result.data.message ||
                                    'Unable to send OTP. Please try again.';
                                showError(otpEmail, otpEmailError, msg);
                            }
                        })
                        .catch(function() {
                            btn.disabled = false;
                            btnText.textContent = 'SEND OTP';
                            showError(otpEmail, otpEmailError, 'Something went wrong. Please try again.');
                        });
                });
            }


            /*
            | VERIFY OTP
            */

            window.verifyOtp = function() {

                const otp = Array.from(
                        document.querySelectorAll('.otp-input')
                    )
                    .map(function(input) {
                        return input.value;
                    })
                    .join('');

                const otpVerifyError =
                    document.getElementById('otpVerifyError');

                const btn =
                    document.getElementById('verifyOtpButton');

                const btnText =
                    document.getElementById('verifyOtpText');

                if (otp.length !== 6) {

                    if (otpVerifyError) {

                        otpVerifyError.textContent =
                            'Please enter the complete 6-digit code.';

                        otpVerifyError.classList.add('show');
                    }

                    return;
                }

                btn.disabled = true;

                btnText.innerHTML =
                    '<span class="spinner-border spinner-border-sm mr-2"></span> VERIFYING...';

                fetch('{{ route("otp.verify.submit") }}', {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },

                        body: JSON.stringify({
                            otp: otp
                        })

                    })
                    .then(function(res) {

                        return res.json().then(function(data) {

                            return {
                                status: res.status,
                                data: data
                            };

                        });

                    })
                    .then(function(result) {

                        const data = result.data;

                        /*
                        |--------------------------------------------------------------------------
                        | SUCCESS
                        |--------------------------------------------------------------------------
                        */

                        if (
                            result.status === 200 &&
                            data.success
                        ) {

                            window.location.href =
                                data.redirect;

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | 2 MINUTE LOCK
                        |--------------------------------------------------------------------------
                        */

                        if (
                            result.status === 429 &&
                            data.locked
                        ) {

                            if (otpVerifyError) {

                                otpVerifyError.textContent =
                                    data.message ||

                                otpVerifyError.classList.add('show');
                            }

                            /*
                            | Disable verification
                            */

                            btn.disabled = true;

                            /*
                            | Disable OTP inputs
                            */

                            document
                                .querySelectorAll('.otp-input')
                                .forEach(function(input) {

                                    input.disabled = true;

                                });

                            /*
                            | Disable resend
                            */

                            const resendBtn =
                                document.getElementById('resendButton');

                            if (resendBtn) {
                                resendBtn.disabled = true;
                            }

                            /*
                            | Start common 2 minute lock
                            */

                            startOtpLockTimer(
                                data.remaining
                            );

                            return;
                        }
                        //wrong otp

                        btn.disabled = false;

                        btnText.textContent = 'VERIFY OTP';

                        /*
                        | Clear OTP fields
                        */
                        clearOtpInputs();

                        if (otpVerifyError) {
                            otpVerifyError.textContent =
                                data.message ||
                                'Invalid OTP. Please try again.';

                            otpVerifyError.classList.add('show');
                        }

                    })
                    .catch(function() {

                        btn.disabled = false;

                        btnText.textContent =
                            'VERIFY OTP';

                        if (otpVerifyError) {

                            otpVerifyError.textContent =
                                'Something went wrong. Please try again.';

                            otpVerifyError.classList.add('show');
                        }

                    });
            };


            /*
            | RESEND OTP
            */
            function startOtpLockTimer(seconds) {

                const verifyBtn =
                    document.getElementById('verifyOtpButton');

                const verifyText =
                    document.getElementById('verifyOtpText');

                const resendBtn =
                    document.getElementById('resendButton');

                const lockTimer =
                    document.getElementById('resendLockTimer');

                const otpInputs =
                    document.querySelectorAll('.otp-input');

                let remaining =
                    parseInt(seconds, 10);

                /*
                | Clear previous lock timer
                */

                if (window.otpLockInterval) {
                    clearInterval(window.otpLockInterval);
                }

                if (verifyBtn) {
                    verifyBtn.disabled = true;
                }

                if (resendBtn) {
                    resendBtn.disabled = true;
                }

                otpInputs.forEach(function(input) {
                    input.disabled = true;
                });

                if (lockTimer) {
                    lockTimer.style.display = 'block';
                }

                function update() {

                    const minutes =
                        Math.floor(Math.max(remaining, 0) / 60);

                    const secondsPart =
                        Math.max(remaining, 0) % 60;

                    const formatted =
                        String(minutes).padStart(2, '0') +
                        ':' +
                        String(secondsPart).padStart(2, '0');

                    if (lockTimer) {

                        lockTimer.textContent =
                            'Too many attempts. You can try again in ' +
                            formatted + '.';
                    }

                    if (verifyText) {

                        verifyText.textContent =
                            'Locked ' + formatted;
                    }

                    if (remaining <= 0) {

                        clearInterval(window.otpLockInterval);

                        /*
                        | Enable OTP fields
                        */

                        otpInputs.forEach(function(input) {
                            input.disabled = false;
                        });

                        /*
                        | Enable verify
                        */

                        if (verifyBtn) {

                            verifyBtn.disabled = false;

                            verifyText.textContent =
                                'VERIFY OTP';
                        }

                        /*
                        | Allow resend
                        */

                        if (resendBtn) {

                            resendBtn.disabled = false;

                            resendBtn.textContent =
                                'Resend OTP';
                        }

                        if (lockTimer) {

                            lockTimer.textContent = '';

                            lockTimer.style.display =
                                'none';
                        }

                        return;
                    }

                    remaining--;
                }

                update();

                window.otpLockInterval =
                    setInterval(update, 1000);
            }
            window.resendOtp = function() {

                const resendBtn =
                    document.getElementById('resendButton');

                const resendStatus =
                    document.getElementById('resendStatus');

                const resendLockTimer =
                    document.getElementById('resendLockTimer');

                const otpVerifyError =
                    document.getElementById('otpVerifyError');

                if (!resendBtn) {
                    return;
                }

                resendBtn.disabled = true;

                resendBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm mr-2"></span> Sending...';


                fetch('{{ route("otp.resend") }}', {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }

                    })

                    .then(function(res) {

                        return res.json().then(function(data) {

                            return {
                                status: res.status,
                                data: data
                            };

                        });

                    })

                    .then(function(result) {

                        const data = result.data;


                        /*
                        |--------------------------------------------------------------------------
                        | Success
                        |--------------------------------------------------------------------------
                        */

                        if (
                            result.status === 200 &&
                            data.success
                        ) {

                            clearOtpInputs();


                            /*
                            | Update remaining count
                            */

                            if (resendStatus) {

                                resendStatus.textContent =
                                    'Resends remaining: ' +
                                    data.resends_remaining;
                            }


                            /*
                            | Start OTP expiry timer
                            */

                            startOtpTimer(
                                data.expires_in,
                                data.resend_in
                            );


                            /*
                            | Third resend reached
                            */

                            if (data.locked) {
                                startResendLockTimer(data.remaining);
                                return;
                            }

                            /*
                            | Normal resend
                            */

                            if (otpVerifyError) {

                                otpVerifyError.textContent = '';

                                otpVerifyError.classList.remove('show');
                            }


                            Swal.fire({
                                icon: 'success',
                                title: 'OTP Sent',
                                text: 'A new OTP has been sent to your registered email.',
                                timer: 1800,
                                showConfirmButton: false
                            });

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | 2 minute lock
                        |--------------------------------------------------------------------------
                        */

                        if (
                            result.status === 429 &&
                            data.locked
                        ) {

                            startResendLockTimer(
                                data.remaining
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Normal 60 second cooldown
                        |--------------------------------------------------------------------------
                        */

                        if (
                            result.status === 429 &&
                            data.cooldown
                        ) {

                            startResendTimer(
                                data.remaining
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Other error
                        |--------------------------------------------------------------------------
                        */

                        if (otpVerifyError) {

                            otpVerifyError.textContent =
                                data.message ||
                                'Unable to resend OTP.';

                            otpVerifyError.classList.add('show');
                        }

                        resendBtn.disabled = false;

                        resendBtn.textContent = 'Resend OTP';

                    })

                    .catch(function() {

                        resendBtn.disabled = false;

                        resendBtn.textContent =
                            'Resend OTP';

                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            text: 'Unable to resend OTP. Please try again.'
                        });

                    });
            };

            function startResendLockTimer(seconds) {

                const resendBtn =
                    document.getElementById('resendButton');

                const lockTimer =
                    document.getElementById('resendLockTimer');

                if (!resendBtn) {
                    return;
                }

                resendBtn.disabled = true;

                let remaining = parseInt(seconds, 10);


                if (lockTimer) {
                    lockTimer.style.display = 'block';
                }


                const interval = setInterval(function() {

                    const minutes =
                        Math.floor(remaining / 60);

                    const secs =
                        remaining % 60;

                    const formatted =
                        String(minutes).padStart(2, '0') +
                        ':' +
                        String(secs).padStart(2, '0');



                    if (lockTimer) {

                        lockTimer.textContent =
                            'Maximum resend attempts reached. ' +
                            'You can request a new OTP in ' +
                            formatted + '.';
                    }


                    if (remaining <= 0) {

                        clearInterval(interval);


                        resendBtn.disabled = false;

                        resendBtn.textContent =
                            'Resend OTP';


                        if (lockTimer) {

                            lockTimer.textContent = '';

                            lockTimer.style.display =
                                'none';
                        }


                        /*
                        | Reset displayed counter
                        */

                        const resendStatus =
                            document.getElementById(
                                'resendStatus'
                            );

                        if (resendStatus) {

                            resendStatus.textContent =
                                'Resends remaining: 3';
                        }

                        return;
                    }


                    remaining--;

                }, 1000);
            }
            /*
            | FORGOT PASSWORD
            */

            const forgotForm = document.getElementById('forgotPasswordForm');
            const forgotEmail = document.getElementById('forgotEmail');
            const forgotEmailError = document.getElementById('forgotEmailError');

            if (forgotEmail) {
                forgotEmail.addEventListener('input', function() {
                    const value = this.value.trim();
                    if (value === '') {
                        clearValidation(this, forgotEmailError);
                        return;
                    }
                    if (!isValidEmail(value)) {
                        showError(this, forgotEmailError, 'Please enter a valid email address.');
                        return;
                    }
                    showSuccess(this, forgotEmailError);
                });

                forgotEmail.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (value === '') {
                        showError(this, forgotEmailError, 'Email address is required.');
                        return;
                    }
                    if (!isValidEmail(value)) {
                        showError(this, forgotEmailError, 'Please enter a valid email address.');
                        return;
                    }
                    showSuccess(this, forgotEmailError);
                });
            }

            if (forgotForm) {
                forgotForm.addEventListener('submit', function(event) {
                    event.preventDefault();

                    const email = forgotEmail.value.trim();

                    if (email === '') {
                        showError(forgotEmail, forgotEmailError, 'Email address is required.');
                        forgotEmail.focus();
                        return;
                    }

                    if (!isValidEmail(email)) {
                        showError(forgotEmail, forgotEmailError, 'Please enter a valid email address.');
                        forgotEmail.focus();
                        return;
                    }

                    showSuccess(forgotEmail, forgotEmailError);

                    const button = document.getElementById('forgotPasswordButton');
                    const buttonText = document.getElementById('forgotPasswordText');

                    if (button) button.disabled = true;
                    if (buttonText) {
                        buttonText.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> SENDING...';
                    }

                    forgotForm.submit();
                });
            }
            function clearField(input, errorElement) {
                if (input) {
                    input.value = '';
                    input.classList.remove('input-error', 'input-success');
                }

                if (errorElement) {
                    errorElement.textContent = '';
                    errorElement.classList.remove('show');
                }
            }

            function clearAllValidationErrors() {
                document.querySelectorAll('.field-error').forEach(function(error) {
                    error.textContent = '';
                    error.classList.remove('show');
                });

                document.querySelectorAll('.modern-input').forEach(function(input) {
                    input.classList.remove('input-error', 'input-success');
                });
            }

            function clearLoginForm() {
                clearField(loginEmail, loginEmailError);
                clearField(password, passwordError);
            }

            function clearOtpEmailErrorsAndFields() {
                clearField(otpEmail, otpEmailError);
            }

            function clearForgotPasswordErrorsAndFields() {
                clearField(forgotEmail, forgotEmailError);
            }

            function clearOtpVerification() {

                document.querySelectorAll('.otp-input').forEach(function(input) {
                    input.value = '';
                    input.classList.remove('input-error', 'input-success');
                    input.disabled = false;
                });

                const otpVerifyError = document.getElementById('otpVerifyError');

                if (otpVerifyError) {
                    otpVerifyError.textContent = '';
                    otpVerifyError.classList.remove('show');
                }
            }

            function clearForgotPasswordForm() {
                clearForgotPasswordErrorsAndFields();
            }

            function clearOtpEmailForm() {
                clearOtpEmailErrorsAndFields();
            }

            function showSection(section) {

                // Clear ALL previous form data and errors
                clearAllValidationErrors();

                clearLoginForm();
                clearOtpEmailForm();
                clearOtpVerification();
                clearForgotPasswordForm();

                // Hide all sections
                [
                    passwordLoginSection,
                    otpEmailSection,
                    otpSection,
                    forgotPasswordSection
                ].forEach(function(s) {
                    if (s) {
                        s.style.display = 'none';
                    }
                });

                // Show requested section
                if (section) {
                    section.style.display = 'block';
                }
            }
            @if(isset($showOtp) && $showOtp)
                document.getElementById('otpEmailDisplay').textContent = @json($otpEmail);
                showSection(otpSection);

                @if($otpLocked)
                    startOtpLockTimer({{ $otpLockRemaining }});
                @else
                    startOtpTimer({{ $otpExpiresIn }}, {{ $otpResendIn }});
                @endif
            @endif
        });

        document.addEventListener('DOMContentLoaded', function() {

            const successAlert = document.querySelector('.modern-alert');

            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.transition = 'opacity 0.5s ease';
                    successAlert.style.opacity = '0';

                    setTimeout(function() {
                        successAlert.remove();
                    }, 500);

                }, 30000); // 30 seconds
            }

        });

    </script>

</body>

</html>
