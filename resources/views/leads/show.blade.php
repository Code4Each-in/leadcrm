@extends('layout')
@section('title', 'Leads')
@section('subtitle', 'Leads')
@section('content')
<style>
    .custom-card {
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 0.5px solid #e5e7eb;
        background: #fff;
    }

    .custom-header {
        background: #0d2c6c;
        color: #fff;
        font-weight: 500;
        font-size: 15px;
        border-radius: 14px 14px 0 0;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: .01em;
    }

    .icon-head {
        opacity: .75;
        font-size: 16px;
    }


    .detail-row {
        display: flex;
        align-items: flex-start;
        padding: 11px 0;
        border-bottom: 0.5px solid #f0f2f5;
        font-size: 13.5px;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-row i {
        width: 28px;
        color: #0d2c6c;
        font-size: 16px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .label {
        font-weight: 500;
        font-size: 12.5px;
        color: #6b7280;
        min-width: 115px;
        margin-right: 0;
        padding-top: 1px;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        padding: 11px 0;
        border-bottom: 0.5px solid #f0f2f5;
        font-size: 13.5px;
    }

    .detail-item i {
        width: 28px;
        color: #0d2c6c;
        font-size: 16px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .btn-outline-primary.btn-sm {
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 6px;
        border-width: 0.5px;
    }

    .status-container {
        position: relative;
        display: inline-block;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 13px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 500;
        cursor: pointer;
        user-select: none;
        transition: opacity .15s;
        color: #0d2c6c;
    }

    .status-badge:hover { opacity: .82; }

    .status-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        background: #fff;
        border-radius: 10px;
        border: 0.5px solid #d1d5db;
        box-shadow: 0 6px 20px rgba(0,0,0,.10);
        min-width: 156px;
        overflow: hidden;
        z-index: 200;
    }

    .status-option {
        padding: 9px 14px;
        cursor: pointer;
        font-size: 13px;
        color: #111827;
        transition: background .1s;
    }

    .status-option:hover { background: #f9fafb; }

    .status-not      { background: #f1f5f9; color: #475569; }
    .status-progress { background: #fef3c7; color: #92400e; }
    .status-hold     { background: #e0f2fe; color: #075985; }
    .status-lost     { background: #fee2e2; color: #991b1b; }
    .status-complete { background: #dcfce7; color: #166534; }

    .rp-section {
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #9ca3af;
        padding: 14px 0 8px;
        border-bottom: 0.5px solid #f0f2f5;
        margin-bottom: 2px;
    }

    /* User row */
    .user-row {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 0.5px solid #f0f2f5;
        gap: 12px;
    }

    .user-row:last-child {
        border-bottom: none;
    }

    /* Initials avatar */
    .rp-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 600;
        flex-shrink: 0;
        letter-spacing: .02em;
        /* fallback if image fails */
        background: #e0e7ff;
        color: #3730a3;
        overflow: hidden;
        position: relative;
    }

    .rp-avatar img {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        position: absolute;
        inset: 0;
    }

    /* avatar colour variants */
    .av-blue   { background: #dbeafe; color: #1d4ed8; }
    .av-green  { background: #dcfce7; color: #15803d; }
    .av-amber  { background: #fef3c7; color: #b45309; }

    /* User info */
    .rp-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .rp-name {
        font-size: 13.5px;
        font-weight: 500;
        color: #111827;
        line-height: 1;
    }

    /* Role badge */
    .rp-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 500;
        padding: 3px 9px;
        border-radius: 20px;
        width: fit-content;
        line-height: 1;
    }

    .rp-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .rpb-blue {
        background: #eff6ff;
        color: #1d4ed8;
    }
    .rpb-blue::before  { background: #3b82f6; }

    .rpb-green {
        background: #f0fdf4;
        color: #15803d;
    }
    .rpb-green::before { background: #22c55e; }

    .rpb-amber {
        background: #fffbeb;
        color: #b45309;
    }

    .rpb-amber::before { background: #f59e0b; }
    .rpb-purple {
        background: #e0e7ff;
        color: #6f42c1;
    }
    .rpb-purple::before { background: #6f42c1; }
</style>
<style>

    .modal-dialog {
        max-width: 600px;
        margin: 1.75rem auto;
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    @media (max-width: 768px) {

        .modal-dialog {
            max-width: 92%;
            margin: 1rem auto;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header,
        .modal-body,
        .modal-footer {
            padding: 12px 14px;
        }
    }

    @media (max-width: 576px) {

        /* Full width feel */
        .modal-dialog {
            max-width: 100%;
            margin: 0;
            height: 100%;
        }

        .modal-content {
            height: 100%;
            border-radius: 0;
            display: flex;
            flex-direction: column;
        }

        .modal-body {
            flex: 1;
            max-height: none;
            overflow-y: auto;
        }

        /* Better spacing */
        .modal-header,
        .modal-body,
        .modal-footer {
            padding: 12px;
        }

        /* Stack buttons */
        .modal-footer {
            flex-direction: column;
        }

        .modal-footer .btn {
            width: 100%;
            margin-bottom: 8px;
        }

        .modal-footer .btn:last-child {
            margin-bottom: 0;
        }

        /* Title smaller */
        .modal-title,
        .modal-header h5 {
            font-size: 16px;
        }
    }


    /* Desktop default */
    .modal-dialog {
        max-width: 700px !important;
        margin: 1.75rem auto !important;
    }

    /* Body scroll fix */
    .modal-body {
        max-height: 70vh !important;
        overflow-y: auto !important;
    }


    @media (max-width: 700px) {

        .modal {
            padding: 0 !important;
        }

        .modal-dialog {
            width: 100% !important;
            max-width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            display: flex !important;
            justify-content: center;
            align-items: stretch !important;
            margin: 10px !important;
        }

        .modal-content {
            width: 100% !important;
            height: 70% !important;
            border-radius: 0 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .modal-header {
            flex-shrink: 0;
        }

        .modal-body {
            flex: 1 !important;
            max-height: none !important;
            overflow-y: auto !important;
        }

        .modal-footer {
            flex-shrink: 0;
            display: flex !important;
            flex-direction: column !important;
        }

        .modal-footer .btn {
            width: 100% !important;
            margin-bottom: 8px !important;
        }

        .modal-footer .btn:last-child {
            margin-bottom: 0 !important;
        }
    }
    /* Prevent table overflow issues */
    .dataTables_wrapper {
        width: 100% !important;
    }

    /* Make action buttons wrap on mobile */
    @media (max-width: 768px) {
        table.dataTable td {
            white-space: normal !important;
        }

        .btn {
            margin-bottom: 5px;
        }
    }
</style>
<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-body">
                    <!-- RIGHT SIDE BUTTONS -->
                    <div class="d-flex justify-content-between align-items-center mb-3">

                        @php
                            $role = strtolower(auth()->user()->role->name ?? '');
                            $isAdmin = in_array($role, ['super admin', 'admin']);
                        @endphp

                        <!-- LEFT: Role-based buttons -->
                        <div class="d-flex gap-2">

                            {{-- AE --}}
                            @if($role == 'account executive' && ($lead->stage == 'ae' || $lead->stage == 'returned'))
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#qaModal">
                                    Move to QA
                                </button>
                            @endif

                            {{-- QA --}}
                            @if($role == 'qa user' && $lead->stage == 'qa')
                                <button class="btn btn-info btn-sm mr-2" data-toggle="modal" data-target="#managerModal">
                                    Move to Manager
                                </button>

                                <form method="POST" action="{{ route('lead.return-ae', $lead->id) }}" class="d-inline return-ae-form">
                                    @csrf
                                    <button class="btn btn-secondary btn-sm">
                                        Return to AE
                                    </button>
                                </form>
                            @endif

                            {{-- MANAGER --}}
                            @if($role == 'account manager' && $lead->stage == 'manager')
                                <form method="POST" action="{{ route('lead.complete', $lead->id) }}" class="d-inline" id="completeForm">
                                    @csrf
                                    <button class="btn btn-success btn-sm mr-2" type="submit">Complete</button>
                                </form>

                                <form method="POST" action="{{ route('lead.lost', $lead->id) }}" class="d-inline" id="lostForm">
                                    @csrf
                                    <button class="btn btn-danger btn-sm" type="submit">Lost</button>
                                </form>
                            @endif

                        </div>

                        <!-- RIGHT: Reminder buttons -->
                        <div class="d-flex ">
                            <button class="btn btn-warning btn-sm mr-2"
                                    data-toggle="modal"
                                    data-target="#addReminderModal">
                                <i class="mdi mdi-bell-plus"></i> Add Reminder
                            </button>

                            <button class="btn btn-info btn-sm"
                                    data-toggle="modal"
                                    data-target="#viewReminderModal">
                                <i class="mdi mdi-bell"></i> Reminders
                            </button>
                        </div>

                    </div>
                <div class="container-fluid mt-3">
                    <div class="row">

                        <!-- LEFT: Details -->
                        <div class="col-md-8 mb-4">
                            <div class="card custom-card h-100">

                                <div class="card-header custom-header d-flex justify-content-between align-items-center">

                                    <div>
                                        <i class="mdi mdi-chart-bar menu-icon icon-head me-2"></i>
                                        {{ $lead->name ?? 'Lead Name' }}
                                    </div>
                                </div>

                                <div class="card-body">

                                    <div class="detail-row">
                                        <i class="mdi mdi-email"></i>
                                        <span class="label">Email:</span>
                                        <span>{{ $lead->email ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-phone"></i>
                                        <span class="label">Phone:</span>
                                        <span>{{ $lead->phone ?? '-' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <i class="mdi mdi-account"></i>
                                        <span class="label">Agency:</span>
                                        <span>{{ $lead->agency->agency_name ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-account-multiple"></i>
                                        <span class="label">Assigned To:</span>
                                        <span>{{ $lead->users->pluck('name')->join(', ') ?: '---' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-office-building"></i>
                                        <span class="label">Company:</span>
                                        <span>{{ $lead->company ?? '---' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-source-branch"></i>
                                        <span class="label">Source:</span>
                                        <span>{{ $lead->source ?? '---' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-calendar-check"></i>
                                        <span class="label">Start date:</span>
                                        <span>{{ $lead->start_date ?? '---' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-calendar-clock"></i>
                                        <span class="label">End date:</span>
                                        <span>{{ $lead->end_date ?? '---' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-file-excel"></i>
                                        <span class="label">Notes:</span>
                                        <span style="color:#6b7280; font-size:13px;">{{ $lead->notes ?? '---' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-information-outline"></i>
                                        <span class="label">Status:</span>

                                        <div class="status-container" data-lead-id="{{ $lead->id }}">
                                            <span class="status-badge">
                                                {{ $lead->status ?? 'Not Started' }}
                                            </span>

                                            <div class="status-dropdown d-none">
                                                @php
                                                    $statuses = ['Not Started', 'In Progress', 'Hold', 'Lost', 'Complete'];
                                                @endphp
                                                @foreach($statuses as $status)
                                                    <div class="status-option" data-value="{{ $status }}">
                                                        {{ $status }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!--  Users — Lead Overview -->
                    <div class="col-md-4 mb-4">
                        <div class="card custom-card h-100">

                            <div class="card-header custom-header">
                                <i class="fa-solid fa-users me-2 icon-head"></i>
                                Lead Overview
                            </div>

                            <div class="card-body px-3 py-2">

                                <!-- created by-->
                                @if($lead->creator)
                                    @php
                                        $words = explode(' ', trim($lead->creator->name));
                                        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                    @endphp

                                    <div class="rp-section">Created by</div>

                                    <div class="user-row">
                                        <div class="rp-avatar av-blue">
                                            {{ $initials }}

                                            @if($lead->creator->profile)
                                                <img src="{{ asset($lead->creator->profile) }}" alt="{{ $lead->creator->name }}">
                                            @endif
                                        </div>

                                        <div class="rp-info">
                                            <span class="rp-name">
                                                {{ $lead->creator->name }}
                                                <small>({{ $lead->creator->role->name }})</small>
                                            </span>
                                            <span class="rp-badge rpb-blue">Created Lead</span>
                                        </div>
                                    </div>
                                @endif


                                <!-- acc ex user -->
                                @if($lead->users->count())
                                    <div class="rp-section">Account Executive</div>

                                    @foreach($lead->users as $user)
                                        @php
                                            $words = explode(' ', trim($user->name));
                                            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                        @endphp

                                        <div class="user-row">
                                            <div class="rp-avatar av-green">
                                                {{ $initials }}

                                                @if($user->profile)
                                                    <img src="{{ asset($user->profile) }}" alt="{{ $user->name }}">
                                                @endif
                                            </div>

                                            <div class="rp-info">
                                                <span class="rp-name">
                                                    {{ $user->name }}
                                                    <small>({{ $user->role->name }})</small>
                                                </span>
                                                <span class="rp-badge rpb-green">AE Assigned</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif


                                <!-- qa user -->
                                @if($lead->qaUser)
                                    <div class="rp-section">Quality Assurance</div>

                                    @php
                                        $qa = $lead->qaUser;
                                        $words = explode(' ', trim($qa->name));
                                        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                    @endphp

                                    <div class="user-row">
                                        <div class="rp-avatar av-amber">
                                            {{ $initials }}

                                            @if($qa->profile)
                                                <img src="{{ asset($qa->profile) }}" >

                                            @endif
                                        </div>

                                        <div class="rp-info">
                                            <span class="rp-name">
                                                {{ $qa->name }}
                                                <small>({{ $qa->role->name }})</small>
                                            </span>
                                            <span class="rp-badge rpb-amber">QA Assigned</span>
                                        </div>
                                    </div>
                                @endif


                                <!-- manager-->
                                @if($lead->manager)
                                    <div class="rp-section">Account Manager</div>

                                    @php
                                        $words = explode(' ', trim($lead->manager->name));
                                        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                    @endphp

                                    <div class="user-row">
                                        <div class="rp-avatar av-purple">
                                            {{ $initials }}

                                            @if($lead->manager->profile)
                                                <img src="{{ asset($lead->manager->profile) }}" alt="{{ $lead->manager->name }}">
                                            @endif
                                        </div>

                                        <div class="rp-info">
                                            <span class="rp-name">
                                                {{ $lead->manager->name }}
                                                <small>({{ $lead->manager->role->name }})</small>
                                            </span>
                                            <span  class="rp-badge rpb-purple">Manager Assigned</span>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                    </div>
                        <div class="col-md-12">

                            <!-- HEADER -->
                            <div class="card-header custom-header">
                                <i class="mdi mdi-chart-bar menu-icon icon-head me-2"></i>
                                Notes and Documents
                            </div>

                            <div class="card-body">

                                <h5 class="mb-3">Activity</h5>

                                <!-- TIMELINE -->
                                @foreach($activities as $activity)

                                    <div class="mb-3 p-3 border rounded d-flex justify-content-between">

                                        <!-- LEFT CONTENT -->
                                        <div>

                                            {{-- NOTE --}}
                                                @if($activity['type'] === 'note')

                                                <strong>{{ $activity['data']->user->name }}</strong>

                                                <small class="text-muted">
                                                    {{ $activity['data']->created_at->format('M d, Y h:i A') }}
                                                </small>

                                                <p class="mb-2"
                                                id="view-{{ $activity['data']->id }}"
                                                data-content="@js($activity['data']->content)">
                                                    {!! $activity['data']->content !!}
                                                </p>

                                                {{-- DOCUMENTS UNDER NOTE --}}
                                                    @if($activity['data']->documents->count())

                                                        <div class="mt-2">
                                                            @foreach($activity['data']->documents as $doc)

                                                                <i class="mdi mdi-file-document me-1 text-primary"></i>

                                                                <a href="{{ asset($doc->file) }}" target="_blank">
                                                                    {{ $doc->file_name }}
                                                                </a>

                                                                <small class="text-muted">
                                                                    ({{ number_format($doc->file_size / 1024, 1) }} KB)
                                                                </small>

                                                                <br>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                @endif

                                            {{-- DOCUMENT --}}
                                            @if($activity['type'] === 'document')

                                                <i class="mdi mdi-file-document me-1 text-primary"></i>
                                                <a href="{{ asset($activity['data']->file) }}" target="_blank">
                                                    {{ $activity['data']->file_name }}
                                                </a>

                                                <small class="text-muted">
                                                    ({{ number_format($activity['data']->file_size / 1024, 1) }} KB)
                                                </small>

                                            @endif

                                        </div>

                                        <!-- ACTIONS -->
                                        <div>

                                            {{-- EDIT NOTE --}}
                                            @if($activity['type'] === 'note')
                                                @if($activity['data']->user_id == auth()->id())

                                                <button type="button"
                                                        class="btn btn-link text-primary p-0"
                                                        onclick="editNote({{ $activity['data']->id }}, @js($activity['data']->content))">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>

                                                @endif
                                            @endif

                                            {{-- DELETE DOCUMENT --}}
                                                @if($activity['type'] === 'document')
                                                    @if(strtolower(auth()->user()->role->name) === 'super admin')

                                                        <form id="delete-form-{{ $activity['data']->id }}"
                                                            method="POST"
                                                            action="{{ route('documents.destroy', $activity['data']->id) }}"
                                                            style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                        <button type="button"
                                                                class="btn btn-link text-danger p-0"
                                                                onclick="confirmDelete({{ $activity['data']->id }})"
                                                                title="Delete Document">

                                                            <i class="mdi mdi-delete"></i>

                                                        </button>
                                                    @endif

                                                @endif

                                        </div>

                                    </div>

                                @endforeach

                                <hr>
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!-- COMMENT FORM -->
                                <form id="comment-form" method="POST"
                                    action="{{ route('notes.store') }}"
                                    enctype="multipart/form-data">

                                    @csrf
                                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">

                                    <div id="create-editor" style="height:150px; background:#fff;"></div>
                                    <input type="hidden" name="content" id="create-content">
                                    <input type="hidden" id="edit-note-id" value="">
                                    <!-- FILE UPLOAD (COMMENT FORM STYLE LIKE PROFILE) -->
                                    <div class="form-group">
                                        <div class="input-group">
                                            <!-- hidden real file input -->
                                            <input type="file" id="commentFiles" name="files[]" multiple style="display: none;">
                                            @error('files')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                            <!-- readonly text box showing selected file names -->
                                            <input type="text"
                                                class="form-control file-upload-info"
                                                id="commentFileName"
                                                placeholder="Choose files"
                                                readonly>

                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-primary" type="button"
                                                    onclick="document.getElementById('commentFiles').click();">
                                                    Upload
                                                </button>
                                            </span>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary">
                                        Sent
                                    </button>

                                </form>

                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- add reminder modal -->
<div class="modal fade" id="addReminderModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="reminderForm" action="{{ route('reminders.store') }}" method="POST" class="modal-content" novalidate>
            @csrf
       <input type="hidden" name="lead_id" value="{{ $lead->id }}">
            <div class="modal-header">
                <h5 class="modal-title">Add Reminder</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date"  min="{{ date('Y-m-d') }}" class="form-control">
                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label>Time</label>
                <input type="time" name="time" class="form-control">
                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label>Note (Optional)</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
                <div class="invalid-feedback"></div>
            </div>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="reminderBtn">Save Reminder</button>

            </div>

        </form>
    </div>
</div>
<!-- view reminder modal -->
 <div class="modal fade" id="viewReminderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Your Reminders</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                @if(isset($reminders) && count($reminders) > 0)

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($reminders as $reminder)
                                <tr>
                                    <td>{{ $reminder->date_time->format('M d, Y h:i A')  }}</td>
                                    <td>{{ $reminder->notes ?? 'N/A' }}</td>

                                    <td>
                                        <a href="{{ route('reminders.delete', $reminder->id) }}"
                                            class="btn btn-sm btn-danger btn-delete "
                                            data-id="{{ $reminder->id }}"
                                           >
                                            <i class="mdi mdi-delete"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                @else
                    <p class="text-muted">No reminders found.</p>
                @endif

            </div>

        </div>
    </div>
</div>
<!-- qa selection -->
 <div class="modal fade" id="qaModal">
    <div class="modal-dialog">
        <form id="qaForm" method="POST" action="{{ route('lead.move-to-qa', $lead->id) }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Select QA User</h5>
                </div>

                <div class="modal-body">
                    <select name="qa_user_id" class="form-control">
                        <option value="">Select QA</option>
                        @foreach($qaUsers as $qa)
                            <option value="{{ $qa->id }}">{{ $qa->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm">Assign</button>
                </div>
            </div>

        </form>
    </div>
</div>
<!-- manager selection -->
<div class="modal fade" id="managerModal">
    <div class="modal-dialog">
        <form id="managerForm" method="POST" action="{{ route('lead.move-to-manager', $lead->id) }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Select Manager</h5>
                </div>

                <div class="modal-body">
                    <select name="manager_user_id" class="form-control">
                        <option value="">Select Manager</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success btn-sm">Assign</button>
                </div>
            </div>

        </form>
    </div>
</div>
<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
let editors = {};

document.addEventListener("DOMContentLoaded", function () {

    // CREATE editor (ONLY ONCE)
    editors['create'] = new Quill('#create-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ font: [] }, { size: [] }],                 // font & size
                ['bold', 'italic', 'underline', 'strike'],    // formatting
                [{ color: [] }, { background: [] }],          // text color
                [{ script: 'sub' }, { script: 'super' }],     // sub/superscript
                [{ header: 1 }, { header: 2 }, 'blockquote', 'code-block'],

                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ indent: '-1' }, { indent: '+1' }],
                [{ align: [] }],

                ['link', 'image', 'video'],                   // media
                ['clean']                                     // remove formatting
            ]
        }
    });

    editors['create'].on('text-change', function () {
        document.getElementById('create-content').value =
            editors['create'].root.innerHTML;
    });

});
// EDIT editors
    function initEditor(id) {

        const container = document.getElementById('editor-' + id);

        if (editors[id]) return;

        editors[id] = new Quill(container, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });
    }
    function clearErrors($form) {
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();
    }

    function showErrors($form, errors) {
        $.each(errors, function (field, messages) {
            const $input = $form.find(`[name="${field}[]"], [name="${field}"]`).first();
            $input.addClass('is-invalid');
            $input.closest('.form-group')
                .append(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
        });
    }
    $(document).on('submit', '#reminderForm', function(e) {

        e.preventDefault();

        const $form = $(this);
        const $btn  = $('#reminderBtn');
        console.log("btn clicked");
        clearErrors($form);

        $btn.prop('disabled', true).text('Saving…');

        $.ajax({
            url        : '{{ route("reminders.store") }}',
            method     : 'POST',
            data       : new FormData($form[0]),
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    $('#addReminderModal').modal('hide');
                    Swal.fire({
                        icon : 'success',
                        title: 'Created!',
                        text : res.success,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => location.reload());
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showErrors($form, xhr.responseJSON.errors);
                } else {
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).text('Save Reminder');
            },
        });
    });
    $(document).on('submit', '#qaForm', function(e) {
        e.preventDefault();

        const $form = $(this);
        clearErrors($form);

        let qaUser = $form.find('[name="qa_user_id"]').val();

        // ✅ JS validation (inline, not popup)
        if (!qaUser) {
            showErrors($form, {
                qa_user_id: ['Please select a QA user']
            });
            return;
        }

        // ✅ Confirmation AFTER validation
        Swal.fire({
            title: 'Are you sure?',
            text: "Move this lead to QA?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, move it!'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: new FormData($form[0]),
                processData: false,
                contentType: false,

                success: function(res) {
                    $('#qaModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.success,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },

                error: function(xhr) {
                    if (xhr.status === 422) {
                        showErrors($form, xhr.responseJSON.errors);
                    } else {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                }
            });
        });
    });
    $(document).on('submit', '#managerForm', function(e) {
        e.preventDefault();

        const $form = $(this);
        clearErrors($form);

        let managerUser = $form.find('[name="manager_user_id"]').val();

        if (!managerUser) {
            showErrors($form, {
                manager_user_id: ['Please select a Manager']
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "Move this lead to Manager?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, move it!'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: new FormData($form[0]),
                processData: false,
                contentType: false,

                success: function(res) {
                    $('#managerModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.success,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },

                error: function(xhr) {
                    if (xhr.status === 422) {
                        showErrors($form, xhr.responseJSON.errors);
                    } else {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                }
            });
        });
    });
    $(document).on('submit', '.return-ae-form', function(e) {
        e.preventDefault();

        const $form = $(this);

        Swal.fire({
            title: 'Are you sure?',
            text: "Return this lead to AE?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, return it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),

                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.success || 'Lead returned successfully',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },

                error: function() {
                    Swal.fire('Error', 'Something went wrong', 'error');
                }
            });
        });
    });
    $(document).on('submit', '#completeForm', function(e) {
        e.preventDefault();

        let form = this;

        Swal.fire({
            title: 'Are you sure?',
            text: "Mark this lead as Completed?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Complete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
    $(document).on('submit', '#lostForm', function(e) {
        e.preventDefault();

        let form = this;

        Swal.fire({
            title: 'Are you sure?',
            text: "Mark this lead as Lost?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Mark Lost'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
    document.getElementById('commentFiles').addEventListener('change', function () {
        let files = Array.from(this.files).map(f => f.name).join(', ');
        document.getElementById('commentFileName').value = files;
    });
    function editNote(id, content) {

        const quill = editors['create'];

        quill.setContents([]);
        quill.clipboard.dangerouslyPasteHTML(content);

        document.getElementById('edit-note-id').value = id;

        document.getElementById('create-content').value = content;

        document.querySelector('#comment-form button').innerText = 'Update';

        document.getElementById('create-editor').scrollIntoView({
            behavior: 'smooth'
        });
    }
    function cancelEdit(id) {

        const view = document.getElementById('view-' + id);
        const form = document.getElementById('edit-form-' + id);

        view.classList.remove('d-none');
        form.classList.add('d-none');
    }
    function confirmDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This document will be deleted permanently!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    document.getElementById('comment-form').addEventListener('submit', function (e) {

        const editId = document.getElementById('edit-note-id').value;

        let content = editors['create'].root.innerHTML;

        const cleanContent = content.replace(/<(.|\n)*?>/g, '').trim();

        document.getElementById('create-content').value = content;

        const files = document.getElementById('commentFiles').files.length;

        if (!cleanContent && files === 0) {
            e.preventDefault();

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please add a note or upload at least one file.',
                confirmButtonColor: '#3085d6'
            });

            return;
        }
        if (editId) {
            this.action = `/notes/${editId}`;
            this.method = 'POST';

            let old = this.querySelector('input[name="_method"]');
            if (old) old.remove();

            let methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            this.appendChild(methodInput);
        }
    });
    function resetEditor() {
        editors['create'].setContents([]);
        document.getElementById('edit-note-id').value = '';
        document.querySelector('#comment-form button').innerText = 'Comment';
    }

    document.querySelectorAll('.status-container').forEach(container => {
        const badge    = container.querySelector('.status-badge');
        const dropdown = container.querySelector('.status-dropdown');
        const leadId   = container.dataset.leadId;

        function setColor(status) {
            badge.classList.remove(
                'status-not','status-progress','status-hold','status-lost','status-complete'
            );
            switch(status){
                case 'Not Started': badge.classList.add('status-not');      break;
                case 'In Progress': badge.classList.add('status-progress'); break;
                case 'Hold':        badge.classList.add('status-hold');     break;
                case 'Lost':        badge.classList.add('status-lost');     break;
                case 'Complete':    badge.classList.add('status-complete'); break;
            }
        }

        setColor(badge.innerText.replace(' ▼','').trim());

        badge.addEventListener('click', () => {
            dropdown.classList.toggle('d-none');
        });

        dropdown.querySelectorAll('.status-option').forEach(option => {
            option.addEventListener('click', () => {
                const status = option.dataset.value;
                badge.innerText = status + ' ▼';
                setColor(status);
                dropdown.classList.add('d-none');

                fetch(`/leads/${leadId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status })
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: data.success,
                        timer: 1500,
                        showConfirmButton: false
                    });
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Update failed' });
                });
            });
        });
    });
    $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This Reminder will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function (res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: res.success,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(function () {
                                    location.reload();
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong. Please try again.'
                            });
                        }
                    });
                }
            });
    });
    document.addEventListener('click', function(e){
        document.querySelectorAll('.status-dropdown').forEach(drop => {
            if (!drop.closest('.status-container').contains(e.target)) {
                drop.classList.add('d-none');
            }
        });
    });
</script>
@endsection
