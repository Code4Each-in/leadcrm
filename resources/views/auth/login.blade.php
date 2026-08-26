<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <title>Lead Bridge - Login</title>

    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}" >

    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}" >

    <link rel="stylesheet" href="{{ asset('assets/css/vertical-layout-light/style.css') }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(
                    circle at top left,
                    rgba(255,255,255,0.18),
                    transparent 35%
                ),
                linear-gradient(
                    135deg,
                    #4e73df 0%,
                    #224abe 100%
                );
            font-family: "Segoe UI", Arial, sans-serif;
        }


        /* =====================================================
           MAIN WRAPPER
        ===================================================== */

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }


        /* =====================================================
           LOGIN CARD
        ===================================================== */

        .login-card {
            width: 100%;
            max-width: 460px;
            background: rgba(255,255,255,0.98);
            border: none;
            border-radius: 24px;
            box-shadow:
                0 25px 70px rgba(0,0,0,0.20);
            overflow: hidden;
        }


        .login-body {
            padding: 45px;
        }


        /* =====================================================
           LOGO
        ===================================================== */

        .logo-wrapper {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-wrapper img {
            height: 58px;
            max-width: 220px;
        }


        /* =====================================================
           TITLES
        ===================================================== */

        .login-title {
            font-size: 26px;
            font-weight: 700;
            color: #202124;
            text-align: center;
            margin-bottom: 8px;
        }

        .login-subtitle {
            text-align: center;
            color: #7a7f87;
            font-size: 14px;
            margin-bottom: 30px;
        }


        /* =====================================================
           AUTH SCREENS
        ===================================================== */

        .auth-screen {
            display: none;
        }

        .auth-screen.active {
            display: block;
            animation: fadeIn 0.25s ease;
        }

        @keyframes fadeIn {

            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }


        /* =====================================================
           INPUT
        ===================================================== */

        .input-group-modern {
            position: relative;
            margin-bottom: 18px;
        }

        .input-group-modern label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 8px;
        }

        .modern-input {
            width: 100%;
            height: 52px;
            padding: 0 16px;
            border: 1px solid #e0e3e8;
            border-radius: 12px;
            outline: none;
            font-size: 15px;
            background: #fff;
            transition: all 0.2s ease;
        }

        .modern-input:focus {
            border-color: #4e73df;
            box-shadow:
                0 0 0 4px rgba(78,115,223,0.10);
        }


        /* =====================================================
           PASSWORD
        ===================================================== */

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .modern-input {
            padding-right: 50px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #7c818a;
            cursor: pointer;
            font-size: 17px;
            padding: 5px;
        }


        /* =====================================================
           REMEMBER / FORGOT
        ===================================================== */

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 5px 0 22px;
            gap: 10px;
        }

        .remember-label {
            font-size: 13px;
            color: #6d727a;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .remember-label input {
            accent-color: #4e73df;
        }

        .forgot-link {
            font-size: 13px;
            color: #4e73df;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }


        /* =====================================================
           BUTTONS
        ===================================================== */

        .modern-btn {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .primary-btn {
            color: #fff;
            background:
                linear-gradient(
                    135deg,
                    #4e73df,
                    #224abe
                );
            box-shadow:
                0 8px 20px rgba(34,74,190,0.20);
        }

        .primary-btn:hover {
            transform: translateY(-1px);
            box-shadow:
                0 10px 25px rgba(34,74,190,0.25);
        }

        .primary-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        .secondary-btn {
            background: #f5f7fb;
            color: #4e73df;
            border: 1px solid #e3e7f0;
        }

        .secondary-btn:hover {
            background: #eef2ff;
        }


        /* =====================================================
           DIVIDER
        ===================================================== */

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 25px 0;
            color: #a1a5ac;
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: #e6e8ed;
        }


        /* =====================================================
           BACK BUTTON
        ===================================================== */

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #6d727a;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
            margin-top: 18px;
        }

        .back-link:hover {
            color: #4e73df;
        }


        /* =====================================================
           OTP SCREEN
        ===================================================== */

        .otp-email-box {
            background: #f6f8ff;
            border: 1px solid #e7ebff;
            border-radius: 12px;
            padding: 13px 15px;
            text-align: center;
            margin-bottom: 25px;
            color: #4e73df;
            font-size: 13px;
            font-weight: 600;
            word-break: break-word;
        }


        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 9px;
            margin: 25px 0;
        }

        .otp-box {
            width: 52px;
            height: 58px;
            border: 1px solid #dfe3ea;
            border-radius: 12px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #202124;
            outline: none;
            transition: all 0.2s ease;
        }

        .otp-box:focus {
            border-color: #4e73df;
            box-shadow:
                0 0 0 4px rgba(78,115,223,0.10);
        }


        /* =====================================================
           OTP TIMER
        ===================================================== */

        .otp-timer {
            text-align: center;
            font-size: 13px;
            color: #777d86;
            margin-bottom: 18px;
        }

        .otp-timer strong {
            color: #4e73df;
        }


        .resend-wrapper {
            text-align: center;
            margin-top: 18px;
        }

        .resend-btn {
            border: none;
            background: transparent;
            color: #4e73df;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .resend-btn:disabled {
            color: #aeb3bd;
            cursor: not-allowed;
        }


        /* =====================================================
           FORGOT PASSWORD MODAL
        ===================================================== */

        .forgot-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(20,25,40,0.55);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .forgot-modal.active {
            display: flex;
        }

        .forgot-modal-card {
            width: 100%;
            max-width: 430px;
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            box-shadow:
                0 25px 70px rgba(0,0,0,0.25);
            animation: modalIn 0.25s ease;
        }

        @keyframes modalIn {

            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }

        }

        .modal-close {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: none;
            background: #f3f5f8;
            color: #555;
            float: right;
            cursor: pointer;
        }

        .modal-title {
            font-size: 21px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .modal-subtitle {
            font-size: 13px;
            color: #777d86;
            margin-bottom: 22px;
            line-height: 1.6;
        }
        .login-footer {
            text-align: center;
            color: #a0a5ad;
            font-size: 11px;
            margin-top: 25px;
        }
        @media (max-width: 480px) {

            .login-wrapper {
                padding: 15px;
                align-items: center;
            }

            .login-card {
                border-radius: 20px;
            }

            .login-body {
                padding: 30px 22px;
            }

            .logo-wrapper img {
                height: 50px;
            }

            .login-title {
                font-size: 23px;
            }

            .otp-inputs {
                gap: 6px;
            }

            .otp-box {
                width: 45px;
                height: 54px;
                font-size: 22px;
            }

            .login-options {
                align-items: flex-start;
            }

        }

        @media (max-width: 360px) {

            .login-body {
                padding: 25px 16px;
            }

            .otp-box {
                width: 41px;
                height: 50px;
            }

            .otp-inputs {
                gap: 5px;
            }

        }

        .field-error {
            display: none;
            margin-top: 7px;
            font-size: 12px;
            color: #dc3545;
            font-weight: 500;
            animation: validationIn 0.2s ease;
        }

        .field-error.show {
            display: block;
        }

        .modern-input.input-error {
            border-color: #dc3545;
            background: #fff8f8;
        }

        .modern-input.input-error:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.08);
        }

        .modern-input.input-success {
            border-color: #198754;
            background: #f8fffb;
        }

        .modern-input.input-success:focus {
            border-color: #198754;
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.08);
        }

        .validation-success {
            display: none;
            margin-top: 7px;
            font-size: 12px;
            color: #198754;
            font-weight: 500;
        }

        .validation-success.show {
            display: block;
        }

        @keyframes validationIn {
            from {
                opacity: 0;
                transform: translateY(-3px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

</head>


<body>


<div class="login-wrapper">

    <div class="login-card">

        <div class="login-body">
            <div class="logo-wrapper">

                <img
                    src="{{ asset('assets/images/logo-dummy.svg') }}"
                    alt="Lead Bridge" >

            </div>

            <div id="passwordScreen" class="auth-screen active">

                <div class="login-title">
                    Welcome Back 👋
                </div>

                <div class="login-subtitle">
                    Sign in to continue
                </div>


                <form id="passwordLoginForm" action="{{ route('login.submit') }}" method="POST"  novalidate >

                    @csrf


                    <!-- EMAIL -->

                    <div class="input-group-modern">

                        <label for="loginEmail">
                            Email Address
                        </label>

                        <input type="email" name="email" id="loginEmail" value="{{ old('email') }}" class="modern-input" placeholder="Enter your email" autocomplete="email" >

                        <div class="field-error" id="loginEmailError"></div>

                    </div>


                    <!-- PASSWORD -->

                    <div class="input-group-modern">

                        <label for="password">
                            Password
                        </label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="modern-input"
                                placeholder="Enter your password"
                                autocomplete="current-password"

                            >

                            <button type="button" class="password-toggle" onclick="togglePassword()" >
                                <i id="passwordIcon" class="ti-eye"></i>
                            </button>

                        </div>

                        <div class="field-error" id="passwordError"></div>

                    </div>


                    <!-- OPTIONS -->

                    <div class="login-options">

                        <label class="remember-label" for="remember" >
                            <input type="checkbox"name="remember" value="1" id="remember">
                            Remember me
                        </label>


                        <a href="javascript:void(0)"class="forgot-link"onclick="openForgotModal()">
                            Forgot Password?
                        </a>

                    </div>

                    <!-- SERVER ERROR -->

                    @if($errors->any())

                        <script>

                            document.addEventListener(
                                'DOMContentLoaded',
                                function () {

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Login Failed',
                                        text: @json($errors->first()),
                                        confirmButtonText: 'Try Again',
                                        confirmButtonColor: '#4e73df',
                                        borderRadius: '16px'
                                    });

                                }
                            );

                        </script>

                    @endif

                    <!-- LOGIN BUTTON -->

                    <button type="submit"  class="modern-btn primary-btn" id="loginButton" >

                        <span id="loginButtonText"> Sign In </span>

                    </button>

                </form>


                <div class="divider">
                    <span>OR</span>
                </div>


                <!-- OTP BUTTON -->

                <button
                    type="button"
                    class="modern-btn secondary-btn"
                    onclick="showOtpEmailScreen()"
                >

                    <i class="ti-email"></i>

                    Login with Email OTP

                </button>


            </div>



            <!-- =====================================================
                 OTP EMAIL SCREEN
            ====================================================== -->

            <div
                id="otpEmailScreen"
                class="auth-screen"
            >

                <div class="login-title">
                    Login with OTP
                </div>

                <div class="login-subtitle">
                    Enter your registered email and we'll send
                    you a verification code.
                </div>


                <form id="otpEmailForm" novalidate>

                    @csrf


                    <div class="input-group-modern">

                        <label for="otpEmail">
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="otpEmail"
                            class="modern-input"
                            placeholder="Enter your registered email"
                            autocomplete="email"

                        >

                        <div class="field-error" id="otpEmailError"></div>

                    </div>


                    <button
                        type="submit"
                        class="modern-btn primary-btn"
                        id="sendOtpButton"
                    >

                        <span id="sendOtpText">
                            Send OTP
                        </span>

                    </button>


                </form>


                <div
                    class="back-link"
                    onclick="showPasswordScreen()"
                >

                    ← Back to password login

                </div>

            </div>



            <!-- =====================================================
                 OTP VERIFICATION SCREEN
            ====================================================== -->

            <div
                id="otpVerifyScreen"
                class="auth-screen"
            >

                <div class="login-title">
                    Verify Your Email
                </div>

                <div class="login-subtitle">

                    Enter the 6-digit code we sent to your email.

                </div>


                <div
                    class="otp-email-box"
                    id="otpEmailDisplay"
                >
                    your email
                </div>


                <!-- OTP INPUTS -->

                <div class="otp-inputs">

                    <input
                        type="text"
                        maxlength="1"
                        class="otp-box"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                    >

                    <input
                        type="text"
                        maxlength="1"
                        class="otp-box"
                        inputmode="numeric"
                    >

                    <input
                        type="text"
                        maxlength="1"
                        class="otp-box"
                        inputmode="numeric"
                    >

                    <input
                        type="text"
                        maxlength="1"
                        class="otp-box"
                        inputmode="numeric"
                    >

                    <input
                        type="text"
                        maxlength="1"
                        class="otp-box"
                        inputmode="numeric"
                    >

                    <input
                        type="text"
                        maxlength="1"
                        class="otp-box"
                        inputmode="numeric"
                    >

                </div>

                <div class="field-error" id="otpCodeError" style="text-align:center;"></div>


                <!-- TIMER -->

                <div class="otp-timer">

                    Code expires in
                    <strong id="otpTimer">
                        10:00
                    </strong>

                </div>


                <!-- VERIFY -->

                <button
                    type="button"
                    class="modern-btn primary-btn"
                    id="verifyOtpButton"
                    onclick="verifyOtp()"
                >

                    <span id="verifyOtpText">
                        Verify & Login
                    </span>

                </button>


                <!-- RESEND -->

                <div class="resend-wrapper">

                    <button
                        type="button"
                        class="resend-btn"
                        id="resendButton"
                        onclick="resendOtp()"
                        disabled
                    >
                        Resend OTP
                    </button>

                </div>


                <!-- CHANGE EMAIL -->

                <div
                    class="back-link"
                    onclick="showOtpEmailScreen()"
                >

                    ← Change email

                </div>

            </div>


            <!-- FOOTER -->

            <div class="login-footer">

                © {{ date('Y') }}
                All rights reserved.

            </div>


        </div>

    </div>

</div>

<div id="forgotModal" class="forgot-modal">

    <div class="forgot-modal-card">


        <button
            type="button"
            class="modal-close"
            onclick="closeForgotModal()"
        >
            ×
        </button>


        <div class="modal-title">
            Forgot Password?
        </div>

        <div class="modal-subtitle">

            Enter your registered email address and we'll
            send you a password reset link.

        </div>


        <form
            id="forgotPasswordForm"
            method="POST"
            action="{{ route('password.email') }}" novalidate
        >

            @csrf


            <div class="input-group-modern">

                <label for="forgotEmail">
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    id="forgotEmail"
                    class="modern-input"
                    placeholder="Enter your registered email"

                >

                <div class="field-error" id="forgotEmailError"></div>

            </div>


            <button
                type="submit"
                class="modern-btn primary-btn"
                id="forgotButton"
            >

                <span id="forgotButtonText">
                    Send Reset Link
                </span>

            </button>


        </form>


    </div>

</div>



<script>

    function isValidEmail(email)
    {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }


    function showFieldError(input, errorElement, message)
    {
        if (!input || !errorElement) {
            return;
        }

        input.classList.remove('input-success');
        input.classList.add('input-error');

        errorElement.innerText = message;
        errorElement.classList.add('show');
    }


    function showFieldSuccess(input, errorElement)
    {
        if (!input || !errorElement) {
            return;
        }

        input.classList.remove('input-error');
        input.classList.add('input-success');

        errorElement.innerText = '';
        errorElement.classList.remove('show');
    }


    function clearFieldValidation(input, errorElement)
    {
        if (!input) {
            return;
        }

        input.classList.remove('input-error', 'input-success');

        if (errorElement) {
            errorElement.innerText = '';
            errorElement.classList.remove('show');
        }
    }


    // Wires up "clear on empty / error on invalid / success on valid" for a text input
    function wireEmailField(input, errorElement)
    {
        if (!input) {
            return;
        }

        input.addEventListener('input', function () {

            const value = this.value.trim();

            if (!value) {
                clearFieldValidation(this, errorElement);
                return;
            }

            if (!isValidEmail(value)) {
                showFieldError(this, errorElement, 'Please enter a valid email address.');
                return;
            }

            showFieldSuccess(this, errorElement);
        });
    }


    function showScreen(screenId)
    {
        document
            .querySelectorAll('.auth-screen')
            .forEach(function(screen) {

                screen.classList.remove('active');

            });

        document
            .getElementById(screenId)
            .classList.add('active');
    }


    function showPasswordScreen()
    {
        showScreen('passwordScreen');
    }


    function showOtpEmailScreen()
    {
        showScreen('otpEmailScreen');

        setTimeout(function() {

            document
                .getElementById('otpEmail')
                .focus();

        }, 100);
    }


    function showOtpVerifyScreen()
    {
        showScreen('otpVerifyScreen');

        setTimeout(function() {

            document
                .querySelector('.otp-box')
                .focus();

        }, 100);
    }

    function togglePassword()
    {
        const password =
            document.getElementById('password');

        const icon =
            document.getElementById('passwordIcon');


        if (password.type === 'password') {

            password.type = 'text';

            icon.className = 'ti-eye-off';

        } else {

            password.type = 'password';

            icon.className = 'ti-eye';

        }
    }


    let otpEmail = '';

    let otpTimerInterval = null;

    let resendTimerInterval = null;


    /* PASSWORD LOGIN FORM — validation + submit */

    const loginEmail       = document.getElementById('loginEmail');
    const loginEmailError  = document.getElementById('loginEmailError');
    const passwordInput    = document.getElementById('password');
    const passwordError    = document.getElementById('passwordError');

    wireEmailField(loginEmail, loginEmailError);

    if (passwordInput) {

        passwordInput.addEventListener('input', function () {

            const value = this.value;

            if (!value) {
                clearFieldValidation(this, passwordError);
                return;
            }

            if (value.length < 6) {
                showFieldError(this, passwordError, 'Password must be at least 6 characters.');
                return;
            }

            showFieldSuccess(this, passwordError);
        });
    }


    const passwordLoginForm = document.getElementById('passwordLoginForm');

    if (passwordLoginForm) {

        passwordLoginForm.addEventListener('submit', function (event) {

            event.preventDefault();

            const email    = loginEmail ? loginEmail.value.trim() : '';
            const password = passwordInput ? passwordInput.value : '';

            let valid = true;

            if (!email) {

                showFieldError(loginEmail, loginEmailError, 'Email address is required.');
                valid = false;

            } else if (!isValidEmail(email)) {

                showFieldError(loginEmail, loginEmailError, 'Please enter a valid email address.');
                valid = false;

            } else {

                showFieldSuccess(loginEmail, loginEmailError);
            }


            if (!password) {

                showFieldError(passwordInput, passwordError, 'Password is required.');
                valid = false;

            } else if (password.length < 6) {

                showFieldError(passwordInput, passwordError, 'Password must be at least 6 characters.');
                valid = false;

            } else {

                showFieldSuccess(passwordInput, passwordError);
            }


            if (!valid) {
                return;
            }


            const button = document.getElementById('loginButton');
            const text   = document.getElementById('loginButtonText');

            button.disabled = true;

            text.innerHTML =
                '<span class="spinner-border spinner-border-sm"></span> Signing in...';

            this.submit();
        });
    }


    /*
       OTP EMAIL FORM — validation + send */

    const otpEmailInput = document.getElementById('otpEmail');
    const otpEmailError = document.getElementById('otpEmailError');

    wireEmailField(otpEmailInput, otpEmailError);

    document
        .getElementById('otpEmailForm')
        .addEventListener('submit', function(event) {

            event.preventDefault();


            const email = otpEmailInput.value.trim();


            if (!email) {

                showFieldError(otpEmailInput, otpEmailError, 'Email address is required.');
                return;
            }

            if (!isValidEmail(email)) {

                showFieldError(otpEmailInput, otpEmailError, 'Please enter a valid email address.');
                return;
            }

            showFieldSuccess(otpEmailInput, otpEmailError);


            otpEmail = email;


            const button =
                document.getElementById('sendOtpButton');

            const text =
                document.getElementById('sendOtpText');


            button.disabled = true;

            text.innerHTML =
                '<span class="spinner-border spinner-border-sm"></span> Sending OTP...';


            fetch('{{ route('otp.send') }}', {

                method: 'POST',

                headers: {

                    'Content-Type':
                        'application/json',

                    'Accept':
                        'application/json',

                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}'

                },

                body: JSON.stringify({

                    email: email

                })

            })

            .then(async function(response) {

                const data =
                    await response.json();

                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        'Unable to send OTP.'
                    );

                }

                return data;

            })

            .then(function(data) {

                otpEmail =
                    data.email;

                document
                    .getElementById('otpEmailDisplay')
                    .innerText =
                    maskEmail(data.email);


                clearOtpInputs();


                showOtpVerifyScreen();


                startOtpTimer(
                    data.expires_in
                );


                startResendTimer(
                    data.resend_in
                );


                Swal.fire({

                    icon: 'success',

                    title: 'OTP Sent',

                    text:
                        'A verification code has been sent to your registered email.',

                    confirmButtonText: 'Continue',

                    confirmButtonColor: '#4e73df',

                    borderRadius: '16px',

                    timer: 2500,

                    timerProgressBar: true

                });

            })

            .catch(function(error) {

                showFieldError(otpEmailInput, otpEmailError, error.message);

                Swal.fire({

                    icon: 'error',

                    title: 'Unable to Send OTP',

                    text: error.message,

                    confirmButtonColor: '#4e73df',

                    borderRadius: '16px'

                });

            })

            .finally(function() {

                button.disabled = false;

                text.innerText = 'Send OTP';

            });

        });

    async function verifyOtp()
    {

        const inputs =
            document.querySelectorAll('.otp-box');

        const otpCodeError =
            document.getElementById('otpCodeError');


        let otp = '';

        inputs.forEach(function(input) {

            otp += input.value;

        });


        if (otp.length !== 6) {

            if (otpCodeError) {
                otpCodeError.innerText = 'Please enter the complete 6-digit verification code.';
                otpCodeError.classList.add('show');
            }

            Swal.fire({

                icon: 'warning',

                title: 'Enter OTP',

                text:
                    'Please enter the complete 6-digit verification code.',

                confirmButtonColor: '#4e73df',

                borderRadius: '16px'

            });

            return;

        }

        if (otpCodeError) {
            otpCodeError.innerText = '';
            otpCodeError.classList.remove('show');
        }


        const button =
            document.getElementById('verifyOtpButton');

        const text =
            document.getElementById('verifyOtpText');


        button.disabled = true;

        text.innerHTML =
            '<span class="spinner-border spinner-border-sm"></span> Verifying...';


        try {

            const response =
                await fetch(
                    '{{ route('otp.verify.submit') }}',
                    {

                        method: 'POST',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                '{{ csrf_token() }}'

                        },

                        body: JSON.stringify({

                            otp: otp

                        })

                    }
                );


            const data =
                await response.json();


            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Invalid OTP.'
                );

            }


            if (data.success && data.redirect) {

                Swal.fire({

                    icon: 'success',

                    title: 'Login Successful',

                    text: 'Welcome back!',

                    showConfirmButton: false,

                    timer: 1000,

                    borderRadius: '16px'

                }).then(function() {

                    window.location.href =
                        data.redirect;

                });

            }

        } catch(error) {

            clearOtpInputs();

            if (otpCodeError) {
                otpCodeError.innerText = error.message || 'The verification code is incorrect.';
                otpCodeError.classList.add('show');
            }


            Swal.fire({

                icon: 'error',

                title: 'Invalid OTP',

                text:
                    error.message ||
                    'The verification code is incorrect. Please try again.',

                confirmButtonText: 'Try Again',

                confirmButtonColor: '#4e73df',

                borderRadius: '16px'

            }).then(function() {

                document
                    .querySelector('.otp-box')
                    .focus();

            });

        } finally {

            button.disabled = false;

            text.innerText =
                'Verify & Login';

        }

    }

    document
        .querySelectorAll('.otp-box')
        .forEach(function(input, index, inputs) {


            input.addEventListener(
                'input',
                function(event) {

                    this.value =
                        this.value
                            .replace(/\D/g, '')
                            .slice(0, 1);


                    if (
                        this.value &&
                        index < inputs.length - 1
                    ) {

                        inputs[index + 1].focus();

                    }

                }
            );


            input.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Backspace' &&
                        !this.value &&
                        index > 0
                    ) {

                        inputs[index - 1].focus();

                    }

                }
            );


            input.addEventListener(
                'paste',
                function(event) {

                    event.preventDefault();


                    const pasted =
                        (
                            event.clipboardData ||
                            window.clipboardData
                        )
                        .getData('text')
                        .replace(/\D/g, '')
                        .slice(0, 6);


                    if (!pasted) {
                        return;
                    }


                    pasted
                        .split('')
                        .forEach(function(number, i) {

                            if (inputs[i]) {

                                inputs[i].value =
                                    number;

                            }

                        });


                    const focusIndex =
                        Math.min(
                            pasted.length,
                            inputs.length - 1
                        );


                    inputs[focusIndex].focus();

                }
            );


        });

    function clearOtpInputs()
    {

        document
            .querySelectorAll('.otp-box')
            .forEach(function(input) {

                input.value = '';

            });

    }


    function maskEmail(email)
    {

        const parts =
            email.split('@');


        if (parts.length !== 2) {

            return email;

        }


        const name =
            parts[0];

        const domain =
            parts[1];


        if (name.length <= 2) {

            return (
                name.charAt(0) +
                '*@' +
                domain
            );

        }


        return (
            name.charAt(0) +
            '*'.repeat(
                Math.min(
                    name.length - 2,
                    6
                )
            ) +
            name.charAt(name.length - 1) +
            '@' +
            domain
        );

    }

    function startOtpTimer(seconds)
    {

        clearInterval(
            otpTimerInterval
        );


        let remaining =
            Number(seconds);


        updateOtpTimer(
            remaining
        );


        otpTimerInterval =
            setInterval(function() {

                remaining--;

                updateOtpTimer(
                    remaining
                );


                if (remaining <= 0) {

                    clearInterval(
                        otpTimerInterval
                    );

                    document
                        .getElementById('otpTimer')
                        .innerText =
                        'Expired';

                }

            }, 1000);

    }


    function updateOtpTimer(seconds)
    {

        const minutes =
            Math.floor(seconds / 60);

        const remainingSeconds =
            seconds % 60;


        document
            .getElementById('otpTimer')
            .innerText =
            String(minutes).padStart(2, '0') +
            ':' +
            String(remainingSeconds).padStart(2, '0');

    }


    function startResendTimer(seconds)
    {

        clearInterval(
            resendTimerInterval
        );


        const button =
            document.getElementById(
                'resendButton'
            );


        button.disabled = true;


        let remaining =
            Number(seconds);


        button.innerText =
            'Resend OTP in ' +
            remaining +
            's';


        resendTimerInterval =
            setInterval(function() {

                remaining--;


                if (remaining <= 0) {

                    clearInterval(
                        resendTimerInterval
                    );

                    button.disabled = false;

                    button.innerText =
                        'Resend OTP';

                    return;

                }


                button.innerText =
                    'Resend OTP in ' +
                    remaining +
                    's';

            }, 1000);

    }

    async function resendOtp()
    {

        const button =
            document.getElementById(
                'resendButton'
            );


        button.disabled = true;

        button.innerText =
            'Sending...';


        try {

            const response =
                await fetch(
                    '{{ route('otp.resend') }}',
                    {

                        method: 'POST',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                '{{ csrf_token() }}'

                        }

                    }
                );


            const data =
                await response.json();


            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Unable to resend OTP.'
                );

            }


            startOtpTimer(
                data.expires_in
            );


            startResendTimer(
                data.resend_in
            );


            clearOtpInputs();


            Swal.fire({

                icon: 'success',

                title: 'OTP Resent',

                text:
                    'A new verification code has been sent.',

                confirmButtonColor: '#4e73df',

                timer: 2200,

                timerProgressBar: true,

                showConfirmButton: false

            });

        } catch(error) {

            Swal.fire({

                icon: 'error',

                title: 'Unable to Resend',

                text: error.message,

                confirmButtonColor: '#4e73df',

                borderRadius: '16px'

            });


            button.disabled = false;

            button.innerText =
                'Resend OTP';

        }

    }

    function openForgotModal()
    {

        document
            .getElementById('forgotModal')
            .classList.add('active');


        setTimeout(function() {

            document
                .querySelector(
                    '#forgotPasswordForm input[name="email"]'
                )
                .focus();

        }, 100);

    }


    function closeForgotModal()
    {

        document
            .getElementById('forgotModal')
            .classList.remove('active');

    }


    document
        .getElementById('forgotModal')
        .addEventListener(
            'click',
            function(event) {

                if (
                    event.target === this
                ) {

                    closeForgotModal();

                }

            }
        );


    /* FORGOT PASSWORD FORM — validation + submit */

    const forgotEmailInput = document.getElementById('forgotEmail');
    const forgotEmailError = document.getElementById('forgotEmailError');

    wireEmailField(forgotEmailInput, forgotEmailError);

    document
        .getElementById('forgotPasswordForm')
        .addEventListener(
            'submit',
            function(event) {

                const email = forgotEmailInput.value.trim();

                if (!email) {

                    event.preventDefault();
                    showFieldError(forgotEmailInput, forgotEmailError, 'Email address is required.');
                    return;
                }

                if (!isValidEmail(email)) {

                    event.preventDefault();
                    showFieldError(forgotEmailInput, forgotEmailError, 'Please enter a valid email address.');
                    return;
                }


                const button =
                    document.getElementById(
                        'forgotButton'
                    );

                const text =
                    document.getElementById(
                        'forgotButtonText'
                    );


                button.disabled = true;

                text.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> Sending...';

            }
        );

</script>


<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>

<script src="{{ asset('assets/js/off-canvas.js') }}"></script>

<script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>

<script src="{{ asset('assets/js/template.js') }}"></script>

<script src="{{ asset('assets/js/settings.js') }}"></script>

<script src="{{ asset('assets/js/todolist.js') }}"></script>

</body>

</html>
