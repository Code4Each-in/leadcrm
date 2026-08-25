@extends('layout')
@section('title', 'Dashboard')
@section('subtitle', 'Dashboard')
@section('content')
<style>
.reminders-panel {
    border: 1px solid #AFA9EC;
    border-radius: 12px;
    overflow: hidden;
    background: #ffffff;
}

.reminders-panel__head {
    padding: 12px 16px;
    border-bottom: 1px solid #AFA9EC;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #EEEDFE;
}

.reminders-panel__title {
    font-size: 13px;
    font-weight: 600;
    color: #26215C;
    display: flex;
    align-items: center;
    gap: 8px;
}

.reminders-panel__count {
    background: #E24B4A;
    color: #ffffff;
    font-size: 11px;
    font-weight: 600;
    padding: 1px 7px;
    border-radius: 20px;
    line-height: 1.6;
}

.reminders-panel__dismiss-all {
    font-size: 12px;
    color: #534AB7;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    font-weight: 500;
    transition: color 0.15s;
}
.reminders-panel__dismiss-all:hover { color: #3C3489; }

.reminders-panel__item {
    padding: 12px 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    border-bottom: 1px solid #EEEDFE;
    transition: background 0.12s;
}
.reminders-panel__item:last-child { border-bottom: none; }
.reminders-panel__item:hover { background: #F7F6FE; }

.reminders-panel__avatar-wrap {
    position: relative;
    flex-shrink: 0;
}

.reminders-panel__avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
}

.reminders-panel__status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #534AB7;
    border: 2px solid #ffffff;
    position: absolute;
    bottom: 0;
    right: 0;
}

.reminders-panel__body {
    flex: 1;
    min-width: 0;
}

.reminders-panel__name {
    font-size: 13px;
    font-weight: 600;
    color: #26215C;
    margin-bottom: 2px;
}

.reminders-panel__note {
    font-size: 12px;
    color: #534AB7;
    line-height: 1.5;
    margin-bottom: 6px;
}

.reminders-panel__meta {
    display: flex;
    align-items: center;
    gap: 6px;
}

.reminders-panel__time-chip {
    font-size: 11px;
    color: #3C3489;
    background: #EEEDFE;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 500;
}

.reminders-panel__close {
    border: none;
    background: none;
    cursor: pointer;
    color: #AFA9EC;
    font-size: 16px;
    line-height: 1;
    padding: 0;
    margin-top: 2px;
    flex-shrink: 0;
    transition: color 0.15s;
}
.reminders-panel__close:hover { color: #E24B4A; }
.status-card {
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    border: 1px solid #f1f1f1;
    transition: all 0.25s ease;
}

.status-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.06);
}

.status-label {
    font-size: 13px;
    color: #6c757d;
    font-weight: 500;
}

.status-count {
    font-size: 26px;
    font-weight: 700;
    color: #111;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.icon-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    border-radius: 12px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.1);
}
.icon-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    border-radius: 12px;
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08);
}
@media (max-width: 768px) {
    .table-responsive-mobile {
        overflow-x: auto;
    }
}
@media (max-width: 576px){
    .row.custom-gap {
        row-gap: 12px;
        column-gap: 12px;
    }
}
@media (max-width: 1024px){
    .row > [class*="col-"] {
        padding: 10px;
    }
    .status-card {
        margin: 6px;
    }
}
</style>

<div class="row mb-3">
    <div class="col-md-12">
        <div style="padding-bottom: 12px; border-bottom: 1px solid #e5e7eb;">
            <span style="font-size: 13px; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #6b7280; display: block; margin-bottom: 4px;">Agency</span>
            <h2 style=" margin: 0; color: #111827; font-size: 2.2rem;">
                {{ $agencyName }}
            </h2>
        </div>
    </div>
</div>
    @if($todayReminders->count())
    <div class="reminders-panel">
        <div class="reminders-panel__head">
            <span class="reminders-panel__title">
                Today’s Reminder for Leads

            </span>
            <button class="reminders-panel__dismiss-all" onclick="dismissAllReminders()">Dismiss all</button>
        </div>
        <div id="reminders-list">
            @foreach($todayReminders as $reminder)
            <div class="reminders-panel__item" id="reminder-{{ $reminder->id }}">
                <div class="reminders-panel__avatar-wrap">
                    <img class="reminders-panel__avatar"
                        src="{{ auth()->user()->profile
                            ? asset(auth()->user()->profile)
                            : asset('assets/images/default-profile.png') }}"
                        alt="profile">
                    <span class="reminders-panel__status-dot"></span>
                </div>
                <div class="reminders-panel__body">
                    <div class="reminders-panel__name">{{ $reminder->lead->name ?? 'Lead' }}</div>
                    <div class="reminders-panel__note">{{ $reminder->notes }}</div>
                    <div class="reminders-panel__meta">
                        <span class="reminders-panel__time-chip">
                            {{ $reminder->date_time->format('M d, Y · h:i A') }}
                        </span>
                    </div>
                </div>
                <button class="reminders-panel__close"
                        onclick="removeReminder({{ $reminder->id }})"
                        title="Dismiss">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    <div class="row mt-4">

        @php
            $role = strtolower(auth()->user()->role->name);
        @endphp

            <!-- Heading -->
            <div class="col-12 mb-3">
                <h5 class="fw-semibold text-muted">
                    Records of Your Agency
                </h5>
            </div>

            <!-- Total Users -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Members</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($totalAgencyUsers) }}</h3>
                        </div>
                        <div class="icon-circle bg-primary">
                            <i class="mdi mdi-account-group text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Leads -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Leads</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($totalLeads) }}</h3>
                        </div>
                        <div class="icon-circle bg-info">
                            <i class="mdi mdi-database text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Leads -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Pending Leads</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($pendingLeads) }}</h3>
                        </div>
                        <div class="icon-circle bg-warning">
                            <i class="mdi mdi-timer-sand text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Leads -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Completed Leads</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($completedLeads) }}</h3>
                        </div>
                        <div class="icon-circle bg-success">
                            <i class="mdi mdi-check-circle text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

    </div>
    @if($role === 'account executive')

    <div class="row mt-4">

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Assigned Leads</p>
                        <h3 class="fw-bold">{{ $assignedLeads }}</h3>
                    </div>
                    <div class="icon-circle bg-primary">
                        <i class="mdi mdi-account-multiple text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Pending Leads</p>
                        <h3 class="fw-bold">{{ $pendingLeads }}</h3>
                    </div>
                    <div class="icon-circle bg-warning">
                        <i class="mdi mdi-timer-sand text-white"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @endif
    @if(in_array(strtolower(auth()->user()->role->name), ['super admin', 'admin']))

        {{-- Leads by Status --}}
        <div class="row mt-4">
            <div class="col-12">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0 fw-semibold" style="letter-spacing:.08em;">
                        LEADS BY STATUS
                    </h6>
                </div>

                <div class="row custom-gap g-3">

                    @foreach($leadsByStatus as $status => $count)
                    <div class="col-6 col-md-4 col-lg-3">

                        <div class="status-card h-100">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="status-label">{{ $status }}</span>

                                <span class="status-dot
                                    @if($status == 'Complete') bg-success
                                    @elseif($status == 'In Progress') bg-primary
                                    @elseif($status == 'Hold') bg-warning
                                    @else bg-secondary
                                    @endif
                                "></span>
                            </div>

                            <div class="status-count">{{ $count }}</div>

                        </div>

                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    @endif
    @if(in_array(strtolower(auth()->user()->role->name), ['mis user', 'admin']))

        <div class="mt-4">

            <h5 class="mb-3 fw-semibold">Upload Statistics</h5>

            <div class="row">

                <!-- Total Upload -->
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Uploads</p>
                                <h3 class="fw-bold">{{ $totalUploaded }}</h3>
                            </div>
                            <div class="icon-circle bg-primary">
                                <i class="mdi mdi-upload text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today -->
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Today</p>
                                <h3 class="fw-bold">{{ $todayUploads }}</h3>
                            </div>
                            <div class="icon-circle bg-success">
                                <i class="mdi mdi-calendar-today text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weekly -->
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">This Week</p>
                                <h3 class="fw-bold">{{ $weeklyUploads }}</h3>
                            </div>
                            <div class="icon-circle bg-warning">
                                <i class="mdi mdi-calendar-week text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly -->
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">This Month</p>
                                <h3 class="fw-bold">{{ $monthlyUploads }}</h3>
                            </div>
                            <div class="icon-circle bg-info">
                                <i class="mdi mdi-calendar-month text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Recent Uploads Table -->
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body">

                    <h6 class="mb-3 fw-semibold">Recent Uploads</h6>

                    <div class="table-responsive">
                        <table class="table align-middle table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUploads as $lead)
                                    <tr>
                                        <td>
                                            <strong>{{ $lead->name }}</strong>
                                        </td>
                                        <td>{{ $lead->company ?? '-' }}</td>
                                        <td>
                                            <span class="badge px-3 py-2
                                                @if($lead->status == 'Complete') badge badge-success
                                                @elseif($lead->status == 'In Progress') badge badge-warning
                                                @elseif($lead->status == 'Hold') badge badge-danger text-dark
                                                @else bg-secondary
                                                @endif
                                            ">
                                                {{ $lead->status }}
                                            </span>
                                        </td>
                                        <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            No uploads found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

    @endif

    @if(strtolower(auth()->user()->role->name) === 'super admin')

        {{-- Recent Tables --}}
        <div class="row g-4 mt-3">

            {{-- Recent Leads --}}
            <div class="col-md-6">
                <div class="card border rounded-3">
                    <div class="card-body">
                        <p class="fw-medium mb-3">
                            Recent Leads
                            <span class="text-muted fw-normal small">(last 5 days)</span>
                        </p>
                        <div class="table-responsive">
                            <table id="leadsTable" class="table table-striped align-middle mb-0" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Agency</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentLeads as $lead)
                                    <tr>
                                        <td>{{ $lead->name }}</td>
                                        <td>{{ $lead->agency->agency_name ?? '—' }}</td>
                                        <td>
                                            @php
                                                $badgeMap = [
                                                    'Not Started'        => 'primary',
                                                    'In Progress'  => 'success',
                                                    'Hold'  => 'teal',
                                                    'Lost'        => 'danger',
                                                    'Complete'       => 'success',
                                                ];
                                                $color = $badgeMap[strtolower($lead->status)] ?? 'secondary';
                                            @endphp
                                            <span class="badge rounded-pill bg-{{ $color }}-subtle text-{{ $color }}-emphasis">
                                                {{ $lead->status }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">{{ $lead->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">No recent leads</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Agencies --}}
            <div class="col-md-6">
                <div class="card border rounded-3">
                    <div class="card-body">
                        <p class="fw-medium mb-3">
                            Recent Agencies
                            <span class="text-muted fw-normal small">(last 5 days)</span>
                        </p>
                        <div class="table-responsive">
                            <table id="agenciesTable" class="table table-striped align-middle mb-0" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Agency Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAgencies as $agency)
                                    <tr>
                                        <td>{{ $agency->agency_name }}</td>
                                        <td class="text-muted">{{ $agency->primary_email ?? '—' }}</td>
                                        <td class="text-muted small">{{ $agency->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No recent agencies</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @endif

    @if(strtolower(auth()->user()->role->name) === 'qa user')

        <div class="row mt-4">

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Pending Reviews</p>
                            <h3 class="fw-bold">{{ $pendingReviews }}</h3>
                        </div>
                        <div class="icon-circle bg-warning">
                            <i class="mdi mdi-clipboard-text text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Reverted Leads</p>
                            <h3 class="fw-bold">{{ $qaRevertedLeads }}</h3>
                        </div>
                        <div class="icon-circle bg-danger">
                            <i class="mdi mdi-backup-restore text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Completed Today</p>
                            <h3 class="fw-bold">{{ $completedToday }}</h3>
                        </div>
                        <div class="icon-circle bg-success">
                            <i class="mdi mdi-check-circle text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body">

                <h6 class="mb-3 fw-semibold">Pending QA Leads</h6>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Lead Name</th>
                                <th>Company</th>
                                <th>AE</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($qaPendingLeads as $lead)
                                <tr>
                                    <td><strong>{{ $lead->name }}</strong></td>

                                    <td>{{ $lead->company ?? '-' }}</td>

                                    <td>
                                        {{ optional($lead->users->first())->name ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="badge bg-warning text-dark px-3 py-2">
                                            {{ $lead->status }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $lead->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No pending QA leads
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    @endif
    @if($role === 'account manager')

        <div class="row mt-4">

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Completed Leads</p>
                            <h3 class="fw-bold">{{ $completedLeads }}</h3>
                        </div>
                        <div class="icon-circle bg-success">
                            <i class="mdi mdi-check-circle text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Lost Leads</p>
                            <h3 class="fw-bold">{{ $lostLeads }}</h3>
                        </div>
                        <div class="icon-circle bg-danger">
                            <i class="mdi mdi-close-circle text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @endif
@endsection

@section('js_scripts')
<script>

function removeReminder(id) {
    const el = document.getElementById('reminder-' + id);
    if (!el) return;

    el.style.transition = 'opacity 0.25s, transform 0.25s';
    el.style.opacity = '0';
    el.style.transform = 'translateX(20px)';

    fetch(`/reminders/${id}/dismiss`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => console.log(data))
    .catch(err => console.error(err));

    setTimeout(() => {
        el.remove();
        checkAndHidePanel();
    }, 260);
}

function dismissAllReminders() {
    const items = document.querySelectorAll('[id^="reminder-"]');
    items.forEach(el => {
        const id = el.id.replace('reminder-', '');

        el.style.transition = 'opacity 0.25s, transform 0.25s';
        el.style.opacity = '0';
        el.style.transform = 'translateX(20px)';

        fetch(`/reminders/${id}/dismiss`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => console.log(data))
        .catch(err => console.error(err));
    });

    setTimeout(() => {
        const panel = document.querySelector('.reminders-panel');
        if (panel) {
            panel.style.transition = 'opacity 0.2s';
            panel.style.opacity = '0';
            setTimeout(() => panel.remove(), 210);
        }
    }, 260);
}

function checkAndHidePanel() {
    const list = document.getElementById('reminders-list');
    const panel = document.querySelector('.reminders-panel');

    if (list && !list.querySelector('.reminders-panel__item')) {
        panel.style.transition = 'opacity 0.2s';
        panel.style.opacity = '0';
        setTimeout(() => panel.remove(), 210);
    }
}
</script>
@endsection
