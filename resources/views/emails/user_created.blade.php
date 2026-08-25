@extends('emails.layout_email')

@section('content')

{{-- Dark header with agency logo --}}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td align="center" style="background:#0d2c6c;padding:32px 28px;">

        {{-- Agency logo box --}}
        <div style="width:68px;height:68px;background:#ffffff;border-radius:10px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;overflow:hidden;">
            @if($agency && $agency->logo)
                <img src="{{ asset($agency->logo) }}"
                     alt="{{ $agency->agency_name }}"
                     width="68" height="68"
                     style="object-fit:contain;padding:6px;">
            @else
                <span style="font-size:22px;font-weight:700;color:#0d2c6c;">
                    {{ strtoupper(substr($agency->agency_name ?? 'A', 0, 2)) }}
                </span>
            @endif
        </div>

        <p style="color:#ffffff;font-size:18px;font-weight:600;margin:0 0 6px;">
            Welcome to {{ $agency->agency_name ?? config('app.name') }}
        </p>
        <p style="color:rgba(255,255,255,0.6);font-size:13px;margin:0;">
            Your account has been created successfully
        </p>
    </td>
</tr>
</table>

{{-- Body --}}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td style="padding:28px;">

        {{-- User row --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid #f3f4f6;">
        <tr>
            <td width="42">
                <div style="width:42px;height:42px;border-radius:50%;background:#e8edf7;text-align:center;line-height:42px;">
                    <span style="font-size:15px;font-weight:700;color:#0d2c6c;">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </span>
                </div>
            </td>
            <td style="padding-left:12px;">
                <p style="font-size:15px;font-weight:600;color:#111827;margin:0;">{{ $user->name }}</p>
                <p style="font-size:13px;color:#6b7280;margin:0;">{{ $user->email }}</p>
            </td>
            <td align="right">
                <span style="font-size:11px;font-weight:600;background:#e8edf7;color:#0d2c6c;padding:4px 10px;border-radius:20px;">
                    {{ $user->role->name ?? 'User' }}
                </span>
            </td>
        </tr>
        </table>

        {{-- Credentials label --}}
        <p style="font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin:0 0 10px;">
            Login credentials
        </p>

        {{-- Credentials box --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #f3f4f6;border-radius:8px;overflow:hidden;margin-bottom:20px;">
        <tr>
            <td style="padding:11px 14px;background:#fafafa;border-bottom:1px solid #f3f4f6;">
                <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-size:12px;color:#9ca3af;">Email</td>
                    <td align="right" style="font-size:13px;color:#111827;font-weight:500;">{{ $user->email }}</td>
                </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 14px;background:#fafafa;">
                <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-size:12px;color:#9ca3af;">Password</td>
                    <td align="right">
                        <span style="font-size:13px;color:#111827;font-weight:500;font-family:monospace;background:#f3f4f6;padding:3px 8px;border-radius:4px;">
                            {{ $password }}
                        </span>
                    </td>
                </tr>
                </table>
            </td>
        </tr>
          <tr>
            <td style="padding:11px 14px;background:#fafafa;">
                <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-size:12px;color:#9ca3af;">Your Role</td>
                    <td align="right">
                        <span style="font-size:13px;color:#111827;font-weight:500;font-family:monospace;background:#f3f4f6;padding:3px 8px;border-radius:4px;">
                            {{ $user->role->name }}
                        </span>
                    </td>
                </tr>
                </table>
            </td>
        </tr>
        </table>

        {{-- Warning --}}
        <!-- <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;margin-bottom:24px;">
        <tr>
            <td style="padding:12px 14px;font-size:13px;color:#92400e;line-height:1.5;">
                ⚠ Change your password immediately after first login to keep your account secure.
            </td>
        </tr>
        </table> -->

        {{-- CTA button --}}
        <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <a href="{{ $loginUrl }}"
                   style="display:inline-block;background:#0d2c6c;color:#ffffff;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:600;">
                    Login to your account &rarr;
                </a>
            </td>
        </tr>
        </table>

    </td>
</tr>
</table>

@endsection
