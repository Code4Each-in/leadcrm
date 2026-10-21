@extends('layout')

@section('title', 'Dashboard')
@section('subtitle', 'Dashboard')

{{-- One page for every role: the header, unread lead notifications,
     reminders and today's reminder popup are shared; the middle is the role's own partial
     ($rolePartial with $roleData - see DashboardController::DASHBOARDS). --}}

@push('styles')
    @include('dashboard.partials.styles')
@endpush

@section('content')
@php
    $authUser = auth()->user();
    // users.agency_id is the only reliable agency for a user; anyone
    // without one (e.g. Super Admin) keeps the company name.
    $agencyName = $authUser->agency?->agency_name ?: 'AGILE ONE';
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
@endphp

<div id="dashboard">

    {{-- Welcome banner: who and where, today's date, and what is waiting
         (unread notifications / reminders today, each linking to its
         section on this page). --}}
    @php
        $firstName = \Illuminate\Support\Str::before($authUser->name, ' ') ?: $authUser->name;
        $notificationCount = $dashboardNotifications->count();
        $reminderCount = $todaysReminders->count();
    @endphp
    <div class="welcome-banner">
        <span class="welcome-shape shape-one" aria-hidden="true"></span>
        <span class="welcome-shape shape-two" aria-hidden="true"></span>

        <div class="welcome-main">
            <span class="welcome-agency"><i class="mdi mdi-office-building"></i>{{ $agencyName }}</span>
            <h3 class="welcome-title">{{ $greeting }}, {{ $firstName }}</h3>
            <p class="welcome-text">
                <i class="mdi mdi-calendar-blank"></i>{{ now()->format('l, d F Y') }}
                <span class="welcome-sep">&middot;</span>
                <span class="welcome-tagline">Here's what's waiting for you today.</span>
            </p>

            <div class="welcome-chips">
                <a href="#new-notifications" class="welcome-chip {{ $notificationCount ? '' : 'is-empty' }}">
                    <i class="mdi mdi-bell"></i>
                    <span class="chip-count">{{ $notificationCount ?: 'No' }}</span>
                    <span class="chip-label">new {{ \Illuminate\Support\Str::plural('notification', $notificationCount) }}</span>
                </a>
                <a href="#my-reminders" class="welcome-chip {{ $reminderCount ? '' : 'is-empty' }}">
                    <i class="mdi mdi-alarm"></i>
                    <span class="chip-count">{{ $reminderCount ?: 'No' }}</span>
                    <span class="chip-label">{{ \Illuminate\Support\Str::plural('reminder', $reminderCount) }} today</span>
                </a>
            </div>
        </div>

        @php
            $canAddLead = $authUser->isMis() || $authUser->isAe();
        @endphp
        <div class="welcome-side {{ $canAddLead ? 'has-action' : '' }}">
            @if($authUser->role)
                <span class="welcome-role" title="{{ $authUser->role->name }}"><i class="mdi mdi-account-circle"></i><span>{{ $authUser->role->name }}</span></span>
            @endif
            @if($canAddLead)
                <a href="{{ route('leads.create') }}" class="welcome-btn"><i class="mdi mdi-plus"></i> Add Lead</a>
            @endif
        </div>
    </div>

    {{-- Today's reminders: floats in the corner on desktop, sits here,
         under the banner, on phones. --}}
    @include('dashboard.partials.reminder-popup', ['todaysReminders' => $todaysReminders])

    {{-- Unread lead notifications - each row opens the lead (and marks it
         read) via notifications.open. Row look is shared with the header
         bell: includes/notifications/. Collapsed by default: the header
         row (or the banner's notification chip) expands it. --}}
    @if($dashboardNotifications->isNotEmpty())
        <div class="row panel-row" id="new-notifications">
            <div class="col-12">
                <div class="notif-panel is-collapsed" id="dashNotifPanel">
                    <div class="notif-head">
                        <button type="button" class="notif-toggle" id="dashNotifToggle"
                                aria-expanded="false" aria-controls="dashNotifList">
                            <span class="notif-head-title">
                                <i class="mdi mdi-bell-ring-outline text-primary"></i>
                                New Notifications
                                <span class="notif-count">{{ $dashboardNotifications->count() }}</span>
                            </span>
                            <span class="notif-toggle-hint">
                                <span class="notif-toggle-text">Show</span>
                                <i class="mdi mdi-chevron-down notif-toggle-chevron"></i>
                            </span>
                        </button>
                        <form method="POST" action="{{ route('notifications.readAll') }}" class="m-0">
                            @csrf
                            <button type="submit" class="notif-mark-all">Dismiss all</button>
                        </form>
                    </div>
                    {{-- Scrolls inside the panel so a long list doesn't push the
                         rest of the dashboard down. --}}
                    <div class="notif-scroll" id="dashNotifList">
                        @foreach($dashboardNotifications as $notification)
                            @include('includes.notifications.item', ['notification' => $notification, 'showCta' => true])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <script>
        (function () {
            var panel = document.getElementById('dashNotifPanel');
            var toggle = document.getElementById('dashNotifToggle');
            var list = document.getElementById('dashNotifList');
            var text = toggle.querySelector('.notif-toggle-text');

            function setOpen(open) {
                panel.classList.toggle('is-collapsed', !open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                text.textContent = open ? 'Hide' : 'Show';
            }

            toggle.addEventListener('click', function () {
                setOpen(panel.classList.contains('is-collapsed'));
            });

            // The banner's "N new notifications" chip: open the list and glide
            // to the top of the section (scroll-margin-top keeps it clear of
            // the fixed header) instead of jumping past it.
            var section = document.getElementById('new-notifications');
            document.querySelectorAll('a[href="#new-notifications"]').forEach(function (chip) {
                chip.addEventListener('click', function (e) {
                    e.preventDefault();
                    setOpen(true);
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });
        })();
        </script>
    @endif

    @include($rolePartial, $roleData)

    @include('dashboard.partials.reminders', ['reminders' => $reminders])

</div>
@endsection
