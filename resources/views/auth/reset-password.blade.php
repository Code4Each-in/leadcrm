<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>Reset Password - AGILE ONE</title>

    <link rel="stylesheet"
          href="{{ asset('assets/vendors/feather/feather.css') }}">

    <link rel="stylesheet"
          href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">

    <link rel="stylesheet"
          href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">

    <link rel="stylesheet"
          href="{{ asset('assets/css/vertical-layout-light/style.css') }}">

    <link rel="shortcut icon"
          href="{{ asset('assets/images/favicon.png') }}">

    <style>

        body {
            background: #f5f7ff;
        }

        .auth-form-light {
            background: #fff;
            border-radius: 0;
        }

        .brand-logo img {
            max-width: 170px;
            max-height: 60px;
            object-fit: contain;
        }

        .modern-input {
            height: 52px;
            border: 1px solid #d8dce5;
            border-radius: 2px;
            font-size: 14px;
            padding: 0 18px;
        }

        .modern-input:focus {
            border-color: #4b49ac;
            box-shadow: none;
        }

        .auth-form-btn {
            height: 52px;
            border-radius: 1.1875rem !important;
            background: #4b49ac !important;
            border-color: #4b49ac !important;
        }

        .field-error {
            display: none;
            color: #dc3545;
            font-size: 12px;
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

    </style>

</head>

<body>

<div class="container-scroller">

    <div class="container-fluid page-body-wrapper full-page-wrapper">

        <div class="content-wrapper d-flex align-items-center auth px-0">

            <div class="row w-100 mx-0">

                <div class="col-lg-4 col-md-6 mx-auto">

                    <div class="auth-form-light text-left py-5 px-4 px-sm-5">

                        <div class="brand-logo">

                            <img
                                src="{{ asset('assets/images/logo-dummy.svg') }}"
                                alt="AGILE ONE"
                            >

                        </div>

                        <h4>Reset your password</h4>

                        <h6 class="font-weight-light">
                            Enter your new password below.
                        </h6>

                        <form
                            id="resetPasswordForm"
                            method="POST"
                            action="{{ route('password.update') }}"
                            class="pt-3"
                            novalidate
                        >

                            @csrf

                            <input
                                type="hidden"
                                name="token"
                                value="{{ $token }}"
                            >

                            <div class="form-group">

                                <input
                                    type="email"
                                    name="email"
                                    id="resetEmail"
                                    value="{{ old('email', $email) }}"
                                    class="form-control form-control-lg modern-input"
                                    placeholder="Email Address"
                                    autocomplete="email"
                                >

                                <div
                                    id="resetEmailError"
                                    class="field-error"
                                ></div>

                                @error('email')
                                    <div class="field-error show">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>
                            <div class="form-group">
                                <div class="password-input-wrapper">
                                    <input
                                        type="password"
                                        name="password"
                                        id="newPassword"
                                        class="form-control form-control-lg modern-input password-input"
                                        placeholder="New Password"
                                        autocomplete="new-password"
                                    >

                                    <button
                                        type="button"
                                        id="toggleNewPassword"
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

                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>

                                        </svg>

                                    </button>
                                </div>
                                <div
                                    id="newPasswordError"
                                    class="field-error"
                                ></div>

                            </div>


                            <div class="form-group">
                                <div class="password-input-wrapper">

                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        id="confirmPassword"
                                        class="form-control form-control-lg modern-input password-input"
                                        placeholder="Confirm New Password"
                                        autocomplete="new-password"
                                    >

                                    <button
                                        type="button"
                                        id="toggleConfirmPassword"
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

                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>

                                        </svg>

                                    </button>
                                </div>
                                <div
                                    id="confirmPasswordError"
                                    class="field-error"
                                ></div>

                            </div>


                            <div class="mt-3">

                                <button
                                    type="submit"
                                    id="resetPasswordButton"
                                    class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                >

                                    <span id="resetPasswordText">
                                        RESET PASSWORD
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

<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form =
        document.getElementById('resetPasswordForm');

    const email =
        document.getElementById('resetEmail');

    const password =
        document.getElementById('newPassword');

    const confirmPassword =
        document.getElementById('confirmPassword');

    const emailError =
        document.getElementById('resetEmailError');

    const passwordError =
        document.getElementById('newPasswordError');

    const confirmError =
        document.getElementById('confirmPasswordError');

    function validEmail(value) {

        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

    }

    function error(input, element, message) {

        input.classList.remove('input-success');

        input.classList.add('input-error');

        element.textContent = message;

        element.classList.add('show');

    }

    function success(input, element) {

        input.classList.remove('input-error');

        input.classList.add('input-success');

        element.textContent = '';

        element.classList.remove('show');

    }

    email.addEventListener('input', function () {

        const value = this.value.trim();

        if (!validEmail(value)) {

            error(
                this,
                emailError,
                'Please enter a valid email address.'
            );

        } else {

            success(this, emailError);

        }

    });

    password.addEventListener('input', function () {

        if (this.value.length < 8) {

            error(
                this,
                passwordError,
                'Password must be at least 8 characters.'
            );

        } else {

            success(this, passwordError);

        }

        if (
            confirmPassword.value &&
            confirmPassword.value !== this.value
        ) {

            error(
                confirmPassword,
                confirmError,
                'Passwords do not match.'
            );

        } else if (confirmPassword.value) {

            success(
                confirmPassword,
                confirmError
            );

        }

    });

    confirmPassword.addEventListener('input', function () {

        if (this.value !== password.value) {

            error(
                this,
                confirmError,
                'Passwords do not match.'
            );

        } else {

            success(
                this,
                confirmError
            );

        }

    });

    form.addEventListener('submit', function (event) {

        event.preventDefault();

        let valid = true;

        if (!validEmail(email.value.trim())) {

            error(
                email,
                emailError,
                'Please enter a valid email address.'
            );

            valid = false;

        }

        if (password.value.length < 8) {

            error(
                password,
                passwordError,
                'Password must be at least 8 characters.'
            );

            valid = false;

        }

        if (
            confirmPassword.value !==
            password.value
        ) {

            error(
                confirmPassword,
                confirmError,
                'Passwords do not match.'
            );

            valid = false;

        }

        if (!valid) {
            return;
        }

        const button =
            document.getElementById('resetPasswordButton');

        const buttonText =
            document.getElementById('resetPasswordText');

        button.disabled = true;

        buttonText.innerHTML =
            '<span class="spinner-border spinner-border-sm mr-2"></span> RESETTING...';

        form.submit();

    });
    const toggleNewPassword =
        document.getElementById('toggleNewPassword');

    const toggleConfirmPassword =
        document.getElementById('toggleConfirmPassword');

    function setupPasswordToggle(button, input) {

        if (!button || !input) {
            return;
        }

        button.addEventListener('click', function () {

            const icon =
                this.querySelector('.password-eye-icon');

            if (input.type === 'password') {

                input.type = 'text';

                this.setAttribute(
                    'aria-label',
                    'Hide password'
                );

                icon.innerHTML = `
                    <path d="M2 2l20 20"></path>
                    <path d="M6.71 6.71C3.66 8.83 2 12 2 12s3.5 7 10 7c1.61 0 3.06-.38 4.35-1"></path>
                    <path d="M10.73 5.08C11.14 5.03 11.56 5 12 5c6.5 0 10 7 10 7s-1.66 3.17-4.71 5.29"></path>
                    <path d="M14 14.12A3 3 0 0 1 9.88 10"></path>
                `;

            } else {

                input.type = 'password';

                this.setAttribute(
                    'aria-label',
                    'Show password'
                );

                icon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            }

        });
    }

    setupPasswordToggle(
        toggleNewPassword,
        password
    );

    setupPasswordToggle(
        toggleConfirmPassword,
        confirmPassword
    );

});
</script>

</body>
</html>
