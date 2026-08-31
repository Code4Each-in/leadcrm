<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Session Expired</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f7fb;
            font-family: Arial, sans-serif;
            color: #1f2937;
        }

        .session-card {
            width: 100%;
            max-width: 460px;
            margin: 20px;
            padding: 45px 35px;
            background: #ffffff;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }

        .icon {
            width: 75px;
            height: 75px;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff4e5;
            color: #f59e0b;
            font-size: 34px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 26px;
            font-weight: 700;
        }

        p {
            margin: 0 auto 30px;
            max-width: 350px;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
        }

        .login-button {
            display: inline-block;
            width: 100%;
            padding: 14px 20px;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: background 0.2s ease;
        }

        .login-button:hover {
            background: #1d4ed8;
        }

        .small-text {
            margin-top: 20px;
            font-size: 13px;
            color: #9ca3af;
        }
    </style>
</head>

<body>

    <div class="session-card">

        <div class="icon">
            ⏱
        </div>

        <h1>Session Expired</h1>

        <p>
            Your session has expired due to inactivity.
            Please log in again to continue.
        </p>

        <a href="{{ route('login') }}" class="login-button">
            Login Again
        </a>

        <div class="small-text">
            Your account is safe. You can continue by signing in again.
        </div>

    </div>

</body>
</html>
