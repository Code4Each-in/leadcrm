<?php

return [


    'otp_expiry' => (int) env('LOGIN_OTP_EXPIRY', 10),


    'otp_resend_seconds' => (int) env('LOGIN_OTP_RESEND_SECONDS', 120),


    'otp_max_resends' => (int) env('OTP_MAX_RESENDS', 3),


    'otp_resend_lock_seconds' => (int) env(
        'OTP_RESEND_LOCK_SECONDS',
        120
    ),

    'otp_max_attempts' => (int) env(
        'OTP_MAX_ATTEMPTS',
        3
    ),

    'session_timeout' => (int) env(
        'SESSION_INACTIVITY_TIMEOUT',
        60
    ),

];
