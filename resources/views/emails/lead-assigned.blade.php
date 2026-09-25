@extends('emails.layout_email')

@section('content')

{{-- Header --}}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td align="center" style="background:#0d2c6c;padding:28px;">
        <div style="display:inline-block;margin:0 auto 14px;">
            <span style="background:rgba(255,255,255,0.15);color:#ffffff;font-size:11px;font-weight:600;padding:5px 14px;border-radius:20px;letter-spacing:0.05em;text-transform:uppercase;">{{ $badge ?? 'Lead Assigned' }}</span>
        </div>

        <p style="color:#ffffff;font-size:17px;font-weight:600;margin:0 0 4px;">{{ $title }}</p>
        <p style="color:rgba(255,255,255,0.75);font-size:13px;margin:0;">{{ $messageText }}</p>
    </td>
</tr>
</table>

{{-- Body --}}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td style="padding:28px;">

        <p style="font-size:14px;color:#374151;margin:0 0 20px;">Hi {{ $assignee->name }},</p>

        <p style="font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin:0 0 10px;">Lead details</p>

        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #f3f4f6;border-radius:8px;overflow:hidden;margin-bottom:24px;">
            <tr>
                <td style="padding:11px 14px;background:#fafafa;border-bottom:1px solid #f3f4f6;">
                    <table width="100%" cellpadding="0" cellspacing="0"><tr>
                        <td style="font-size:12px;color:#9ca3af;">Lead ID</td>
                        <td align="right" style="font-size:13px;color:#111827;font-weight:600;">#{{ $lead->display_id }}</td>
                    </tr></table>
                </td>
            </tr>
            <tr>
                <td style="padding:11px 14px;background:#fafafa;border-bottom:1px solid #f3f4f6;">
                    <table width="100%" cellpadding="0" cellspacing="0"><tr>
                        <td style="font-size:12px;color:#9ca3af;">Business / Customer</td>
                        <td align="right" style="font-size:13px;color:#111827;font-weight:500;">{{ $lead->company_business_name ?? $lead->customer_name ?? '-' }}</td>
                    </tr></table>
                </td>
            </tr>
            <tr>
                <td style="padding:11px 14px;background:#fafafa;border-bottom:1px solid #f3f4f6;">
                    <table width="100%" cellpadding="0" cellspacing="0"><tr>
                        <td style="font-size:12px;color:#9ca3af;">Product</td>
                        <td align="right" style="font-size:13px;color:#111827;font-weight:500;">{{ $lead->product?->name ?? '-' }}</td>
                    </tr></table>
                </td>
            </tr>
            <tr>
                <td style="padding:11px 14px;background:#fafafa;">
                    <table width="100%" cellpadding="0" cellspacing="0"><tr>
                        <td style="font-size:12px;color:#9ca3af;">Status</td>
                        <td align="right">
                            <span style="font-size:11px;font-weight:600;background:#e8edf7;color:#0d2c6c;padding:3px 10px;border-radius:20px;">{{ $lead->status_label }}</span>
                        </td>
                    </tr></table>
                </td>
            </tr>
        </table>

        @if(!empty($note))
        <p style="font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin:0 0 10px;">Note</p>
        <div style="border:1px solid #f3f4f6;border-left:3px solid #0d2c6c;border-radius:8px;background:#fafafa;padding:12px 14px;margin-bottom:24px;font-size:13px;color:#374151;line-height:1.5;">{!! nl2br(e($note)) !!}</div>
        @endif

        <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <a href="{{ $url }}"
                   style="display:inline-block;background:#0d2c6c;color:#ffffff;text-decoration:none;padding:12px 32px;border-radius:8px;font-size:14px;font-weight:600;">
                    View Lead &rarr;
                </a>
            </td>
        </tr>
        </table>

    </td>
</tr>
</table>

@endsection
