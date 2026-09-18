<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AGILE ONE Login OTP Notification</title>
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

    <h2 style="text-align:center; margin-top:0;">
        Login OTP Generated
    </h2>

    <p>
        A login OTP was just generated for the following user account. This is an informational
        notification only - no action is required unless you did not expect this login attempt.
    </p>

    <table style="width:100%; border-collapse:collapse; margin-top:20px;">
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eee; color:#888; width:160px;">User name</td>
            <td style="padding:10px 0; border-bottom:1px solid #eee;"><strong>{{ $loginUser->name }}</strong></td>
        </tr>
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eee; color:#888;">User email</td>
            <td style="padding:10px 0; border-bottom:1px solid #eee;">{{ $loginUser->email }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eee; color:#888;">Date &amp; time (London)</td>
            <td style="padding:10px 0; border-bottom:1px solid #eee;">{{ $generatedAt->format('d M Y, h:i:s A') }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eee; color:#888;">Device</td>
            <td style="padding:10px 0; border-bottom:1px solid #eee;">{{ ucfirst($deviceType) }} &mdash; {{ $userAgent }}</td>
        </tr>
        @if($ipAddress)
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #eee; color:#888;">IP address</td>
            <td style="padding:10px 0; border-bottom:1px solid #eee;">{{ $ipAddress }}</td>
        </tr>
        @endif
    </table>

    <p style="margin-top:20px;">OTP code:</p>

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

    <hr style="margin-top:25px;">

    <p style="font-size:12px; color:#888; text-align:center;">
        © {{ date('Y') }} AGILE ONE
    </p>

</div>

</body>
</html>
