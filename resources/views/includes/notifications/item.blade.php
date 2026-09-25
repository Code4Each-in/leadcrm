{{-- One notification row, shared by the header bell and the dashboard
     panel. Expects $notification (a DatabaseNotification); $showCta adds a "View Lead" button (dashboard). Styles live
     in includes/notifications/styles.blade.php. --}}
@php($n = \App\Support\NotificationPresenter::present($notification))

<a href="{{ $n['url'] }}"
   class="notif-item {{ $n['unread'] ? 'is-unread' : 'is-read' }}"
   title="{{ $n['title'] }} - {{ $n['when'] }}">

    <span class="notif-icon tone-{{ $n['tone'] }}"><i class="mdi {{ $n['icon'] }}"></i></span>

    <span class="notif-body">
        <span class="notif-top">
            <span class="notif-title">{{ $n['title'] }}</span>
            @if($n['unread'])
                <span class="notif-dot" aria-label="Unread"></span>
            @endif
        </span>

        <span class="notif-message">{{ $n['message'] }}</span>

        <span class="notif-meta">
            @if($n['lead_label'])
                <span class="notif-lead"><i class="mdi mdi-file-account-outline"></i>{{ $n['lead_label'] }}@if($n['lead_name']) &middot; {{ $n['lead_name'] }}@endif</span>
            @endif
            <span class="notif-time" title="{{ $n['when'] }}"><i class="mdi mdi-clock-outline"></i>{{ $n['when'] }} &middot; {{ $n['ago'] }}</span>
        </span>
    </span>

    @if(!empty($showCta))
        <span class="notif-cta">View Lead</span>
    @endif
</a>
