<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AGILE ONE Login OTP</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f5f7fb; padding:30px;">

<div style="
    max-width:600px;
    margin:auto;
    background:#ffffff;
    padding:35px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
">

    <!-- <div style="text-align:center;">
        <img
                    src="{{ asset('assets/images/logo-dummy.svg') }}"
            style="height:60px;"
            alt="AGILE ONE"
        >
    </div> -->

    <h2 style="text-align:center; margin-top:30px;">
        Login Verification
    </h2>

    <p>
        You requested to login to your AGILE ONE account using OTP.
    </p>

    <p>
        Your verification code is:
    </p>

    <div style="
        text-align:center;
        font-size:32px;
        font-weight:bold;
        letter-spacing:8px;
        padding:20px;
        background:#f1f4ff;
        border-radius:10px;
        color:#4e73df;
    ">
        {{ $otp }}
    </div>

    <p style="margin-top:25px;">
        This OTP will expire in <strong>{{ $expiresIn }} minutes</strong>.
    </p>

    <p>
        If you did not request this login, you can safely ignore this email.
    </p>

    <hr>

    <p style="font-size:12px; color:#888; text-align:center;">
        © {{ date('Y') }} AGILE ONE
    </p>

</div>

</body>
</html>
