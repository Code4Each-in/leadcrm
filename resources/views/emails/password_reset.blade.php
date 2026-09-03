<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Your Password</title>
</head>

<body style="
    margin: 0;
    padding: 0;
    background-color: #f4f6f8;
    font-family: Arial, Helvetica, sans-serif;
">

<table width="100%"
       cellpadding="0"
       cellspacing="0"
       border="0"
       style="background-color: #f4f6f8;">

    <tr>
        <td align="center" style="padding: 40px 15px;">

            <!-- Main Container -->
            <table width="600"
                   cellpadding="0"
                   cellspacing="0"
                   border="0"
                   style="
                        max-width: 600px;
                        width: 100%;
                        background-color: #ffffff;
                        border-radius: 10px;
                        overflow: hidden;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                   ">

                <!-- Header -->
                <tr>
                    <td align="center"
                        style="
                            background-color: #4b49ac !important;
                            padding: 30px 20px;
                        ">

                        <!-- <div style="text-align:center;">
                            <img
                                        src="{{ asset('assets/images/agile-logo.svg') }}"
                                style="height:60px;"
                                alt="AGILE ONE"
                            >
                        </div> -->

                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding: 40px 35px;">

                        <p style="
                            margin: 0 0 20px;
                            color: #333333;
                            font-size: 16px;
                            line-height: 1.6;
                        ">
                            Hello {{ $user->name ?? 'User' }},
                        </p>

                        <p style="
                            margin: 0 0 20px;
                            color: #555555;
                            font-size: 15px;
                            line-height: 1.6;
                        ">
                            We received a request to reset the password
                            associated with your account.
                        </p>

                        <p style="
                            margin: 0 0 30px;
                            color: #555555;
                            font-size: 15px;
                            line-height: 1.6;
                        ">
                            Click the button below to create a new password.
                        </p>

                        <!-- Button -->
                        <table width="100%"
                               cellpadding="0"
                               cellspacing="0"
                               border="0">

                            <tr>
                                <td align="center">

                                    <a href="{{ $resetUrl }}"
                                       style="
                                            display: inline-block;
                                            padding: 14px 30px;
                                            background-color: #4b49ac !important;
                                            color: #ffffff;
                                            text-decoration: none;
                                            border-radius: 6px;
                                            font-size: 16px;
                                            font-weight: 600;
                                       ">
                                        Reset Password
                                    </a>

                                </td>
                            </tr>

                        </table>

                        <p style="
                            margin: 30px 0 15px;
                            color: #555555;
                            font-size: 14px;
                            line-height: 1.6;
                        ">
                            This password reset link will expire after
                            {{ config('auth.passwords.users.expire', 60) }}
                            seconds.
                        </p>

                        <p style="
                            margin: 0 0 15px;
                            color: #555555;
                            font-size: 14px;
                            line-height: 1.6;
                        ">
                            If you did not request a password reset,
                            you can safely ignore this email.
                        </p>

                    </td>
                </tr>


            </table>

        </td>
    </tr>

</table>

</body>
</html>
