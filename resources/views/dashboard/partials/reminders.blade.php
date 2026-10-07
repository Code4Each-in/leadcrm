{{-- The user's reminders for today and the next 7 days (every role -
     Dashboard::upcomingReminders()). Each opens its lead, where
     reminders are managed. Expects $reminders. --}}
<div class="row panel-row" id="my-reminders">
    <div class="col-12">
        <div class="dash-panel">
            @include('dashboard.partials.panel-head', ['title' => 'My Reminders', 'subtitle' => 'Today and the next 7 days'])
            <div class="panel-body">
                @forelse($reminders as $reminder)
                    @php($day = $reminder->reminder_date)
                    <a href="{{ route('leads.show', $reminder->lead) }}" class="dash-row">
                        <span class="date-chip {{ $day->isToday() ? 'is-today' : '' }}">
                            <span class="day">{{ $day->format('d') }}</span>
                            <span class="month">{{ $day->isToday() ? 'Today' : $day->format('M') }}</span>
                        </span>
                        <span class="row-main">
                            <span class="row-title">{{ $reminder->note ?: 'Follow up' }}</span>
                            <span class="row-meta">
                                <span class="lead-id">#{{ $reminder->lead->display_id }}</span>
                                &middot; {{ $reminder->lead->company_business_name ?: $reminder->lead->customer_name }}
                            </span>
                        </span>
                        <span class="row-side">
                            <span class="row-time"><i class="mdi mdi-clock-outline"></i> {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('h:i A') }}</span>
                        </span>
                    </a>
                @empty
                    <div class="empty-state">
                        <i class="mdi mdi-calendar-check"></i>
                        No reminders for the next 7 days.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
