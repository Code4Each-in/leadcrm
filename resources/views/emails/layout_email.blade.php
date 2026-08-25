<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? config('app.name') }}</title>
</head>

<body style="margin:0;padding:0;background:#f4f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="center" style="padding:30px 16px;">

    <!-- Outer card -->
    <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">

        <!-- BODY CONTENT -->
        <tr>
            <td>
                @yield('content')
            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
            <td style="background:#fafafa;border-top:1px solid #f3f4f6;padding:16px 28px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-size:11px;color:#9ca3af;">{{ config('app.name') }} Team</td>
                    <!-- <td align="right" style="font-size:11px;color:#d1d5db;">If you didn't expect this email, ignore it.</td> -->
                </tr>
                </table>
            </td>
        </tr>

    </table>

</td>
</tr>
</table>

</body>
</html>
