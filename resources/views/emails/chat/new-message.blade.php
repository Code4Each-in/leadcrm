<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Chat Message</title>
</head>

<body style="margin:0; padding:0; background:#f5f6fa; font-family:Arial, sans-serif;">

<div style="max-width:600px; margin:40px auto; background:#ffffff; padding:30px; border-radius:8px;">

    <h2 style="margin-top:0;">
        New Chat Message
    </h2>

    <p>
        You have received a new message on Lead Bridge.
    </p>

    <p>
        <strong>From:</strong>
        {{ $chatMessage->sender->name ?? 'User' }}
    </p>

    @if($chatMessage->body)
        <div style="
            margin:20px 0;
            padding:15px;
            background:#f5f6fa;
            border-radius:6px;
        ">
            {{ $chatMessage->body }}
        </div>
    @endif

    <p>
        Please log in to Lead Bridge to view and reply to the conversation.
    </p>

    <p style="margin-top:30px;">
        <a
            href="{{ url('/chatify') }}"
            style="
                display:inline-block;
                padding:10px 20px;
                background:#007bff;
                color:#ffffff;
                text-decoration:none;
                border-radius:5px;
            "
        >
            Open Chat
        </a>
    </p>

    <p style="margin-top:30px; color:#777; font-size:12px;">
        This is an automated notification from Lead Bridge.
    </p>

</div>

</body>
</html>
