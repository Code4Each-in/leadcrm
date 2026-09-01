@extends('layout')

@section('title', 'Dashboard')
@section('subtitle', 'Dashboard')

@section('content')

@php
$role = strtolower(auth()->user()->role->name);
@endphp

<style>
    .dashboard-card {
        background: #fff;
        border: 1px solid #eeeef5;
        border-radius: 14px;
        transition: all 0.2s ease;
        height: 100%;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .dashboard-card .card-body {
        padding: 22px;
    }

    .dashboard-label {
        font-size: 13px;
        color: #77778a;
        margin-bottom: 7px;
        font-weight: 500;
    }

    .dashboard-value {
        font-size: 28px;
        line-height: 1.2;
        font-weight: 700;
        color: #26215c;
        margin: 0;
    }

    .dashboard-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #26215c;
        margin-bottom: 16px;
    }

    .section-subtitle {
        font-size: 12px;
        color: #8a8a9a;
        margin-top: -10px;
        margin-bottom: 18px;
    }

    .status-card {
        background: #fff;
        border: 1px solid #eeeef5;
        border-radius: 12px;
        padding: 18px;
        height: 100%;
    }

    .status-label {
        font-size: 13px;
        color: #6f6f80;
        font-weight: 500;
    }

    .status-count {
        font-size: 25px;
        font-weight: 700;
        color: #26215c;
        margin-top: 8px;
    }

    .status-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
    }

    .performance-table th {
        font-size: 12px;
        color: #77778a;
        font-weight: 600;
        border-bottom: 1px solid #eeeef5;
    }

    .performance-table td {
        font-size: 13px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f1f6;
    }

    .empty-state {
        padding: 30px 15px;
        text-align: center;
        color: #8a8a9a;
        font-size: 13px;
    }

    .follow-up-card {
        border: 1px solid #eeeef5;
        border-radius: 12px;
        padding: 16px;
        background: #fff;
    }

    .follow-up-name {
        font-size: 14px;
        font-weight: 600;
        color: #26215c;
    }

    .follow-up-note {
        font-size: 12px;
        color: #77778a;
        margin-top: 4px;
    }

    .follow-up-time {
        font-size: 11px;
        background: #eeedfe;
        color: #534ab7;
        padding: 4px 9px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .summary-box {
        border-radius: 12px;
        padding: 20px;
        background: #fff;
        border: 1px solid #eeeef5;
        height: 100%;
    }

    .summary-box .summary-label {
        font-size: 13px;
        color: #77778a;
        margin-bottom: 6px;
    }

    .summary-box .summary-value {
        font-size: 26px;
        font-weight: 700;
        color: #26215c;
    }

    .badge-soft-success {
        background: #e9f8ef;
        color: #198754;
    }

    .badge-soft-danger {
        background: #fdecec;
        color: #dc3545;
    }

    .badge-soft-warning {
        background: #fff4db;
        color: #b77900;
    }

    .badge-soft-primary {
        background: #eeedfe;
        color: #534ab7;
    }

    .badge-soft-secondary {
        background: #f1f1f5;
        color: #666675;
    }

    .role-header {
        margin-bottom: 24px;
    }

    .role-header h4 {
        color: #26215c;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .role-header p {
        color: #888899;
        font-size: 13px;
        margin-bottom: 0;
    }
        .follow-up-time {
        font-size: 11px;
        white-space: nowrap;
    }

    .icon-data-list li {
        border-bottom: 1px solid #f1f1f6;
        padding: 12px 0;
    }

    .icon-data-list li:last-child {
        border-bottom: none;
    }

    .icon-data-list li:first-child {
        padding-top: 0;
    }

    .empty-state {
        padding: 30px 15px;
        text-align: center;
        color: #8a8a9a;
        font-size: 13px;
    }
</style>

{{-- Agency Name --}}
<div class="agency-header">
    <!-- <span class="agency-label">
        Agency
    </span> -->

    <h2 class="agency-name">
        AGILE ONE
    </h2>
</div>


@if($role === 'super admin')

    {{-- Top Statistics --}}
    <div class="row">

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-tale">
                <div class="card-body">
                    <p class="mb-4">Total Leads</p>
                    <p class="fs-30 mb-2">{{ number_format($totalLeads) }}</p>
                    <p><i class="mdi mdi-account-multiple mr-1"></i>All time</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-dark-blue">
                <div class="card-body">
                    <p class="mb-4">Pending QA</p>
                    <p class="fs-30 mb-2">{{ number_format($pendingQA) }}</p>
                    <p><i class="mdi mdi-clipboard-check-outline mr-1"></i>Awaiting review</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-light-danger">
                <div class="card-body">
                    <p class="mb-4">Pending Closure</p>
                    <p class="fs-30 mb-2">{{ number_format($pendingClosure) }}</p>
                    <p><i class="mdi mdi-clock-alert-outline mr-1"></i>Needs action</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Leads By Status --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="card-title">Leads by Status</p>

                    <div class="charts-data">

                        @forelse($leadsByStatus as $status => $count)

                            @php
                                $statusLower = strtolower($status);

                                if ($statusLower === 'complete' || $statusLower === 'won') {
                                    $statusClass = 'success';
                                } elseif ($statusLower === 'in progress') {
                                    $statusClass = 'primary';
                                } elseif ($statusLower === 'hold') {
                                    $statusClass = 'warning';
                                } elseif ($statusLower === 'lost') {
                                    $statusClass = 'danger';
                                } else {
                                    $statusClass = 'info';
                                }

                                $percent = $totalLeads > 0 ? round(($count / $totalLeads) * 100) : 0;
                            @endphp

                            <div class="mt-3">
                                <p class="mb-0">{{ $status }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="progress progress-md flex-grow-1 mr-4">
                                        <div class="progress-bar bg-{{ $statusClass }}" role="progressbar"
                                             style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mb-0">{{ number_format($count) }}</p>
                                </div>
                            </div>

                        @empty

                            <div class="empty-state">
                                No lead status data available.
                            </div>

                        @endforelse

                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Team Performance --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="card-title mb-0">Team Performance</p>

                    <div class="table-responsive">
                        <table class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th>Team Member</th>
                                    <th>Email</th>
                                    <th>Assigned Leads</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($teamPerformance as $member)

                                    <tr>
                                        <td class="font-weight-bold">{{ $member->name }}</td>
                                        <td class="text-muted">{{ $member->email }}</td>
                                        <td class="font-weight-medium">
                                            <div class="badge badge-primary">
                                                {{ number_format($member->total_leads ?? 0) }}
                                            </div>
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="3">
                                            <div class="empty-state">
                                                No team performance data available.
                                            </div>
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endif

@if($role === 'mis user')

    <div class="row">
        <div class="col-md-12 grid-margin">
            <p class="card-title mb-0">Upload Statistics</p>
        </div>
    </div>

    <div class="row">

        {{-- Total Uploads --}}
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card card-tale">
                <div class="card-body">
                    <p class="mb-4">Total Uploads</p>
                    <p class="fs-30 mb-2">{{ number_format($totalUploaded) }}</p>
                    <p><i class="mdi mdi-upload mr-1"></i>All time</p>
                </div>
            </div>
        </div>

        {{-- Today --}}
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card card-dark-blue">
                <div class="card-body">
                    <p class="mb-4">Uploaded Today</p>
                    <p class="fs-30 mb-2">{{ number_format($todayUploads) }}</p>
                    <p><i class="mdi mdi-calendar-today mr-1"></i>Today</p>
                </div>
            </div>
        </div>

        {{-- Weekly --}}
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card card-light-blue">
                <div class="card-body">
                    <p class="mb-4">This Week</p>
                    <p class="fs-30 mb-2">{{ number_format($weeklyUploads) }}</p>
                    <p><i class="mdi mdi-calendar-week mr-1"></i>7 days</p>
                </div>
            </div>
        </div>

        {{-- Monthly --}}
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card card-light-danger">
                <div class="card-body">
                    <p class="mb-4">This Month</p>
                    <p class="fs-30 mb-2">{{ number_format($monthlyUploads) }}</p>
                    <p><i class="mdi mdi-calendar-month mr-1"></i>30 days</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Assignment Statistics --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="card-title">Assignment Statistics</p>
                    <div class="d-flex flex-wrap">

                        <div class="mr-5 mt-3">
                            <p class="text-muted">Total Assigned Leads</p>
                            <h3 class="text-primary fs-30 font-weight-medium">{{ number_format($totalAssigned) }}</h3>
                        </div>

                        <div class="mr-5 mt-3">
                            <p class="text-muted">Assigned Today</p>
                            <h3 class="text-primary fs-30 font-weight-medium">{{ number_format($todayAssigned) }}</h3>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endif

@if(in_array($role, ['account executive', 'ae user', 'ae']))

    <div class="row">

        {{-- Assigned Leads --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-tale">
                <div class="card-body">
                    <p class="mb-4">Assigned Leads</p>
                    <p class="fs-30 mb-2">{{ number_format($assignedLeads) }}</p>
                    <p><i class="mdi mdi-account-multiple mr-1"></i>All time</p>
                </div>
            </div>
        </div>

        {{-- Pending Leads --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-dark-blue">
                <div class="card-body">
                    <p class="mb-4">Pending Leads</p>
                    <p class="fs-30 mb-2">{{ number_format($pendingLeads) }}</p>
                    <p><i class="mdi mdi-timer-sand mr-1"></i>Awaiting action</p>
                </div>
            </div>
        </div>

        {{-- Reverted Leads --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-light-danger">
                <div class="card-body">
                    <p class="mb-4">Reverted Leads</p>
                    <p class="fs-30 mb-2">{{ number_format($revertedLeads) }}</p>
                    <p><i class="mdi mdi-undo mr-1"></i>Needs review</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Today's Follow-ups --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="card-title mb-0">Today's Follow-ups</p>
                    <p class="font-weight-500 text-muted">Your scheduled follow-ups for today</p>

                    <ul class="icon-data-list">

                        @forelse($todayFollowUps as $followUp)

                            <li>
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-info mb-1">{{ $followUp->lead->name ?? 'Lead' }}</p>

                                        @if(!empty($followUp->notes))
                                            <p class="mb-0">{{ $followUp->notes }}</p>
                                        @endif
                                    </div>

                                    <div class="badge badge-primary follow-up-time">
                                        @if(isset($followUp->time))
                                            {{ \Carbon\Carbon::parse($followUp->time)->format('h:i A') }}
                                        @elseif(isset($followUp->date_time))
                                            {{ \Carbon\Carbon::parse($followUp->date_time)->format('h:i A') }}
                                        @else
                                            Today
                                        @endif
                                    </div>
                                </div>
                            </li>

                        @empty

                            <li>
                                <div class="empty-state">
                                    No follow-ups scheduled for today.
                                </div>
                            </li>

                        @endforelse

                    </ul>

                </div>
            </div>
        </div>
    </div>

@endif


@if($role === 'qa user')

    <div class="row">

        {{-- Pending Reviews --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-tale">
                <div class="card-body">
                    <p class="mb-4">Pending Reviews</p>
                    <p class="fs-30 mb-2">{{ number_format($pendingReviews) }}</p>
                    <p><i class="mdi mdi-clipboard-text-outline mr-1"></i>Awaiting QA</p>
                </div>
            </div>
        </div>

        {{-- Reverted Leads --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-light-danger">
                <div class="card-body">
                    <p class="mb-4">Reverted Leads</p>
                    <p class="fs-30 mb-2">{{ number_format($qaRevertedLeads) }}</p>
                    <p><i class="mdi mdi-undo mr-1"></i>Sent back</p>
                </div>
            </div>
        </div>

        {{-- Completed Today --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-dark-blue">
                <div class="card-body">
                    <p class="mb-4">Completed Today</p>
                    <p class="fs-30 mb-2">{{ number_format($completedToday) }}</p>
                    <p><i class="mdi mdi-check-circle mr-1"></i>Today</p>
                </div>
            </div>
        </div>

    </div>

@endif

@if($role === 'account manager')

    {{-- Closure Statistics --}}
    <div class="row">

        {{-- Leads for Closure --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-dark-blue">
                <div class="card-body">
                    <p class="mb-4">Leads for Closure</p>
                    <p class="fs-30 mb-2">{{ number_format($closureLeads) }}</p>
                    <p><i class="mdi mdi-clipboard-clock-outline mr-1"></i>In progress</p>
                </div>
            </div>
        </div>

        {{-- Closed Today --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-tale">
                <div class="card-body">
                    <p class="mb-4">Closed Today</p>
                    <p class="fs-30 mb-2">{{ number_format($closedToday) }}</p>
                    <p><i class="mdi mdi-check-circle mr-1"></i>Today</p>
                </div>

        {{-- Total Won --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-light-blue">
                <div class="card-body">
                    <p class="mb-4">Won Leads</p>
                    <p class="fs-30 mb-2">{{ number_format($wonLeads) }}</p>
                    <p><i class="mdi mdi-trophy-outline mr-1"></i>All time</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Won / Lost Summary --}}
    <div class="row">
        <div class="col-md-12 grid-margin">
            <p class="card-title mb-0">Won / Lost Summary</p>
        </div>
    </div>

    <div class="row">

        {{-- Won --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card data-icon-card-primary bg-success">
                <div class="card-body">
                    <p class="card-title text-white">Won Leads</p>
                    <div class="row">
                        <div class="col-8 text-white">
                            <h3>{{ number_format($wonLeads) }}</h3>
                            <div class="badge badge-light">Won</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lost --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card data-icon-card-primary bg-danger">
                <div class="card-body">
                    <p class="card-title text-white">Lost Leads</p>
                    <div class="row">
                        <div class="col-8 text-white">
                            <h3>{{ number_format($lostLeads) }}</h3>
                            <div class="badge badge-light">Lost</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Won / Lost Summary --}}
    <div class="row">
        <div class="col-md-12 grid-margin">
            <p class="card-title mb-0">Won / Lost Summary</p>
        </div>
    </div>

    <div class="row">

        {{-- Won --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card data-icon-card-primary bg-success">
                <div class="card-body">
                    <p class="card-title text-white">Won Leads</p>
                    <div class="row">
                        <div class="col-8 text-white">
                            <h3>{{ number_format($wonLeads) }}</h3>
                            <div class="badge badge-light">Won</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lost --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card data-icon-card-primary bg-danger">
                <div class="card-body">
                    <p class="card-title text-white">Lost Leads</p>
                    <div class="row">
                        <div class="col-8 text-white">
                            <h3>{{ number_format($lostLeads) }}</h3>
                            <div class="badge badge-light">Lost</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endif
@endsection
