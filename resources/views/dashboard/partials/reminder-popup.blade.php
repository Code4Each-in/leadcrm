{{-- Today's reminders as one small alert in the corner
     (Dashboard::todaysReminders()) - earlier and later days never pop up.
     Closing it is remembered in this browser for the rest of the day,
     per reminder (keyed on its last update), so a reminder added or
     edited later today pops up again on its own.
     Expects $todaysReminders. --}}
@if($todaysReminders->isNotEmpty())
@php
    $shown = 3;
@endphp
<div class="reminder-alert" id="reminderAlert" role="alert" aria-live="polite">
    <div class="reminder-alert-head">
        <span class="reminder-alert-icon"><i class="mdi mdi-bell-ring"></i></span>
        <span class="reminder-alert-title">
            You have <span class="reminder-alert-count">{{ $todaysReminders->count() }}</span>
            <span class="reminder-alert-noun">{{ \Illuminate\Support\Str::plural('reminder', $todaysReminders->count()) }}</span> today
        </span>
        <button type="button" class="reminder-alert-close" aria-label="Dismiss today's reminders"><i class="mdi mdi-close"></i></button>
    </div>

    <div class="reminder-alert-list">
        @foreach($todaysReminders as $reminder)
            @php
                $time = \Carbon\Carbon::parse($reminder->reminder_date->toDateString() . ' ' . $reminder->reminder_time);
            @endphp
            <a href="{{ route('leads.show', $reminder->lead) }}"
               class="reminder-alert-row {{ $loop->index >= $shown ? 'is-queued' : '' }}"
               data-reminder-key="{{ $reminder->id }}-{{ $reminder->updated_at?->timestamp }}">
                <span class="reminder-alert-time {{ $time->isPast() ? 'is-due' : '' }}" title="{{ $time->isPast() ? 'Due' : 'Later today' }}">{{ $time->format('h:i A') }}</span>
                <span class="reminder-alert-text">
                    <span class="reminder-alert-note">{{ $reminder->note ?: 'Follow up on this lead' }}</span>
                    <span class="reminder-alert-lead">#{{ $reminder->lead->display_id }} &middot; {{ $reminder->lead->company_business_name ?: $reminder->lead->customer_name }}</span>
                </span>
            </a>
        @endforeach
    </div>

    <a href="#my-reminders" class="reminder-alert-more" hidden></a>
</div>

<script>
(function () {
    var box = document.getElementById('reminderAlert');
    if (!box) return;

    var storageKey = 'dashReminderDismissed:' + @json(now()->toDateString());
    var maxShown = {{ $shown }};
    var dismissed = [];
    try { dismissed = JSON.parse(localStorage.getItem(storageKey) || '[]') || []; } catch (e) {}

    // Drop reminders already dismissed today; show what is left.
    var rows = Array.prototype.slice.call(box.querySelectorAll('.reminder-alert-row')).filter(function (row) {
        if (dismissed.indexOf(row.getAttribute('data-reminder-key')) === -1) return true;
        row.remove();
        return false;
    });

    if (!rows.length) {
        box.remove();
        return;
    }

    rows.forEach(function (row, i) { row.classList.toggle('is-queued', i >= maxShown); });

    box.querySelector('.reminder-alert-count').textContent = rows.length;
    box.querySelector('.reminder-alert-noun').textContent = rows.length === 1 ? 'reminder' : 'reminders';

    var more = box.querySelector('.reminder-alert-more');
    if (rows.length > maxShown) {
        more.hidden = false;
        more.textContent = '+' + (rows.length - maxShown) + ' more - view all';
    }

    box.classList.add('is-visible');

    box.querySelector('.reminder-alert-close').addEventListener('click', function () {
        rows.forEach(function (row) { dismissed.push(row.getAttribute('data-reminder-key')); });
        try { localStorage.setItem(storageKey, JSON.stringify(dismissed)); } catch (e) {}

        box.classList.add('is-leaving');
        setTimeout(function () { box.remove(); }, 200);
    });
})();
</script>
@endif
