<?php

return [

    'otp_expiry' => (int) env('LOGIN_OTP_EXPIRY', 10),

    'otp_resend_seconds' => (int) env('LOGIN_OTP_RESEND_SECONDS', 60),

    'session_timeout' => (int) env('SESSION_INACTIVITY_TIMEOUT', 30),

];
