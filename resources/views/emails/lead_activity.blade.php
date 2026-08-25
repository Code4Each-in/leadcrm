@extends('emails.layout_email')

@section('content')

{{-- Header --}}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td align="center" style="background:#0d2c6c;padding:28px;">

        {{-- Type badge --}}
        <div style="display:inline-block;margin:0 auto 14px;">
            @if($type === 'note_added')
                <span style="background:rgba(255,255,255,0.15);color:#ffffff;font-size:11px;font-weight:600;padding:5px 14px;border-radius:20px;letter-spacing:0.05em;text-transform:uppercase;">Note</span>
            @elseif($type === 'note_with_attachment')
                <span style="background:rgba(255,255,255,0.15);color:#ffffff;font-size:11px;font-weight:600;padding:5px 14px;border-radius:20px;letter-spacing:0.05em;text-transform:uppercase;">Note + Attachment</span>
            @elseif($type === 'document_added')
                <span style="background:rgba(255,255,255,0.15);color:#ffffff;font-size:11px;font-weight:600;padding:5px 14px;border-radius:20px;letter-spacing:0.05em;text-transform:uppercase;">Document</span>
            @else
                <span style="background:rgba(255,255,255,0.15);color:#ffffff;font-size:11px;font-weight:600;padding:5px 14px;border-radius:20px;letter-spacing:0.05em;text-transform:uppercase;">Activity</span>
            @endif
        </div>

        <p style="color:#ffffff;font-size:17px;font-weight:600;margin:0 0 4px;">
            @if($type === 'note_added') New Note Added
            @elseif($type === 'note_with_attachment') New Note with Attachment
            @elseif($type === 'document_added') New Document Uploaded
            @else Lead Activity
            @endif
        </p>
        <p style="color:rgba(255,255,255,0.6);font-size:13px;margin:0;">
            Lead #{{ $lead->id }} &mdash; {{ $lead->name ?? 'N/A' }}
        </p>
    </td>
</tr>
</table>

{{-- Body --}}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td style="padding:28px;">

        <p style="font-size:14px;color:#374151;margin:0 0 20px;">
            Hi <strong>{{ $user->name }}</strong>,
        </p>

        {{-- Activity info card --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #f3f4f6;border-radius:8px;overflow:hidden;margin-bottom:20px;">
        <tr>
            <td style="padding:11px 14px;background:#fafafa;border-bottom:1px solid #f3f4f6;">
                <table width="100%" cellpadding="0" cellspacing="0"><tr>
                    <td style="font-size:12px;color:#9ca3af;">Lead</td>
                    <td align="right" style="font-size:13px;color:#111827;font-weight:600;">#{{ $lead->id }} &mdash; {{ $lead->name ?? 'N/A' }}</td>
                </tr></table>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 14px;background:#fafafa;border-bottom:1px solid #f3f4f6;">
                <table width="100%" cellpadding="0" cellspacing="0"><tr>
                    <td style="font-size:12px;color:#9ca3af;">Activity</td>
                    <td align="right">
                        <span style="font-size:11px;font-weight:600;background:#e8edf7;color:#0d2c6c;padding:3px 10px;border-radius:20px;">
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </span>
                    </td>
                </tr></table>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 14px;background:#fafafa;">
                <table width="100%" cellpadding="0" cellspacing="0"><tr>
                    <td style="font-size:12px;color:#9ca3af;">Date</td>
                    <td align="right" style="font-size:13px;color:#111827;font-weight:500;">{{ now()->format('d M Y, h:i A') }}</td>
                </tr></table>
            </td>
        </tr>
        </table>

        {{-- Note / body content --}}
        @if(!empty($body))
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td>
                <p style="font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin:0 0 8px;">
                    @if($type === 'note_added' || $type === 'note_with_attachment')
                        Note content
                    @else
                        Details
                    @endif
                </p>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-left:3px solid #0d2c6c;background:#f9fafb;border-radius:0 6px 6px 0;">
                <tr>
                    <td style="padding:12px 14px;font-size:14px;color:#374151;line-height:1.6;">
                        {{ $body }}
                    </td>
                </tr>
                </table>
            </td>
        </tr>
        </table>
        @endif

        {{-- CTA --}}
        <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <a href="{{ url('/leads/' . $lead->id) }}"
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
