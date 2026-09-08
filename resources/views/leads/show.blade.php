@extends('layout')

@section('title', 'Lead Details')
@section('subtitle', 'View Lead Information')

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
        min-width: 190px;
        margin-right: 0;
        padding-top: 1px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 13px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 500;
    }

    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-complete {
        background: #dcfce7;
        color: #166534;
    }
    .status-complete::before { background: #22c55e; }

    .status-progress {
        background: #fef3c7;
        color: #92400e;
    }
    .status-progress::before { background: #f59e0b; }

    .back-btn {
        border-radius: 8px;
        font-size: 13px;
        padding: 6px 16px;
        border: 0.5px solid #d1d5db;
    }
    .reminder-item {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 12px;
    background: #fff;
}

.reminder-item:hover {
    background: #f9fafb;
}

#remindersModal .modal-header,
#addReminderModal .modal-header {
    background: #0d2c6c;
    color: #fff;
}

#remindersModal .btn-close,
#addReminderModal .btn-close {
    filter: brightness(0) invert(1);
}
.reminder-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.reminder-modal-overlay.show {
    display: flex;
}

.reminder-modal-box {
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,.2);
}

.reminder-modal-box.large {
    max-width: 800px;
}

.reminder-modal-header {
    background: #0d2c6c;
    color: #fff;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 12px 12px 0 0;
}

.reminder-modal-header h5 {
    margin: 0;
    font-size: 16px;
}

.reminder-modal-body {
    padding: 20px;
}

.reminder-modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
.button-group {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
</style>

<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-end gap-2 mb-3 button-group">
                    <button
                        type="button"
                        class="btn btn-warning"
                        onclick="openAddReminderModal()"
                    >
                        <i class="mdi mdi-bell-plus me-1"></i>
                        Add Reminder
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="openRemindersModal()"
                    >
                        <i class="mdi mdi-bell me-1"></i>
                        Reminders
                    </button>

                    <a href="{{ route('leads.index') }}" class="btn btn-light back-btn">
                        <i class="mdi mdi-arrow-left me-1"></i>
                        Back
                    </a>

                </div>
                <div class="container-fluid mt-3">
                    <div class="row">

                        <!-- LEFT: Application Info -->
                        <div class="col-md-8 mb-4">
                            <div class="card custom-card h-100">

                                <div class="card-header custom-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="mdi mdi-file-document-outline menu-icon icon-head me-2"></i>
                                        {{ $lead->company_business_name ?? 'Lead' }}
                                    </div>
                                </div>

                                <div class="card-body">

                                    <div class="detail-row">
                                        <i class="mdi mdi-package-variant"></i>
                                        <span class="label">Product:</span>
                                        <span>{{ $lead->product->name ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-office-building"></i>
                                        <span class="label">Company / Business Name:</span>
                                        <span>{{ $lead->company_business_name ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-pound"></i>
                                        <span class="label">Company Number:</span>
                                        <span>{{ $lead->company_number ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-domain"></i>
                                        <span class="label">Company Type:</span>
                                        <span>{{ $lead->company_type ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-calendar"></i>
                                        <span class="label">Business Start Date:</span>
                                        <span>{{ $lead->business_start_date?->format('d M Y') ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-account"></i>
                                        <span class="label">Customer Name:</span>
                                        <span>{{ $lead->customer_name ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-account-tie"></i>
                                        <span class="label">Contact Person:</span>
                                        <span>{{ $lead->contact_person ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-email"></i>
                                        <span class="label">Email:</span>
                                        <span>{{ $lead->email ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-phone"></i>
                                        <span class="label">Phone:</span>
                                        <span>{{ $lead->phone_no ? '+44 '.$lead->phone_no : '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-map-marker"></i>
                                        <span class="label">Registered Address:</span>
                                        <span>{{ $lead->business_registered_address ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-map-marker-outline"></i>
                                        <span class="label">Trading Address:</span>
                                        <span>{{ $lead->business_trading_address ?? '-' }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Overview -->
                        <div class="col-md-4 mb-4">
                            <div class="card custom-card h-100">

                                <div class="card-header custom-header">
                                    <i class="mdi mdi-information-outline me-2 icon-head"></i>
                                    Lead Overview
                                </div>

                                <div class="card-body px-3 py-2">

                                    <div class="detail-row">
                                        <i class="mdi mdi-flag"></i>
                                        <span class="label">Status:</span>
                                        <span>
                                            @if($lead->status === 'published')
                                                <span class="status-badge status-complete">Published</span>
                                            @else
                                                <span class="status-badge status-progress">Draft</span>
                                            @endif
                                        </span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-package-variant"></i>
                                        <span class="label">Product:</span>
                                        <span>{{ $lead->product->name ?? '-' }}</span>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Add Reminder Modal -->
<div id="addReminderModal" class="reminder-modal-overlay">

    <div class="reminder-modal-box">

        <div class="reminder-modal-header">

            <h5>
                <i class="mdi mdi-bell-plus me-1"></i>
                Add Reminder
            </h5>

            <button
                type="button"
                class="btn-close"
                onclick="closeAddReminderModal()"
                style="filter:brightness(0) invert(1);"
            ></button>

        </div>

        <form
            method="POST"
            action="{{ route('leads.reminders.store', $lead) }}"
        >

            @csrf

            <div class="reminder-modal-body">

                <div class="mb-3">

                    <label class="form-label">
                        Reminder Date
                    </label>

                    <input
                        type="date"
                        name="reminder_date"
                        class="form-control"
                        min="{{ date('Y-m-d') }}"
                        value="{{ old('reminder_date') }}"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Reminder Time
                    </label>

                    <input
                        type="time"
                        name="reminder_time"
                        class="form-control"
                        value="{{ old('reminder_time') }}"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Reminder Note
                    </label>

                    <textarea
                        name="note"
                        class="form-control"
                        rows="4"
                        placeholder="Enter reminder details..."
                    >{{ old('note') }}</textarea>

                </div>

            </div>

            <div class="reminder-modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    onclick="closeAddReminderModal()"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="mdi mdi-content-save me-1"></i>
                    Save Reminder
                </button>

            </div>

        </form>

    </div>

</div>
<!-- Reminders Modal -->
<div id="remindersModal" class="reminder-modal-overlay">

    <div class="reminder-modal-box large">

        <div class="reminder-modal-header">

            <h5>
                <i class="mdi mdi-bell me-1"></i>
                Lead Reminders
            </h5>

            <button
                type="button"
                class="btn-close"
                onclick="closeRemindersModal()"
                style="filter:brightness(0) invert(1);"
            ></button>

        </div>

        <div class="reminder-modal-body">

            <div id="remindersLoading" class="text-center py-4">

                <div class="spinner-border"></div>

                <div class="mt-2 text-muted">
                    Loading reminders...
                </div>

            </div>

            <div id="remindersList"></div>

        </div>

    </div>

</div>
<script>
    function openAddReminderModal()
{
    document
        .getElementById('addReminderModal')
        .classList.add('show');

    document.body.style.overflow = 'hidden';
}

function closeAddReminderModal()
{
    document
        .getElementById('addReminderModal')
        .classList.remove('show');

    document.body.style.overflow = '';
}

function openRemindersModal()
{
    document
        .getElementById('remindersModal')
        .classList.add('show');

    document.body.style.overflow = 'hidden';

    loadReminders();
}

function closeRemindersModal()
{
    document
        .getElementById('remindersModal')
        .classList.remove('show');

    document.body.style.overflow = '';
}
function loadReminders()
{
    const loading = document.getElementById('remindersLoading');
    const list = document.getElementById('remindersList');

    loading.style.display = 'block';
    list.innerHTML = '';

    fetch('{{ route('leads.reminders', $lead) }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load reminders.');
        }

        return response.json();
    })
    .then(reminders => {

        loading.style.display = 'none';

        if (!reminders.length) {

            list.innerHTML = `
                <div class="text-center py-5 text-muted">

                    <i
                        class="mdi mdi-bell-off-outline"
                        style="font-size:40px;"
                    ></i>

                    <div class="mt-2">
                        No reminders found for this lead.
                    </div>

                </div>
            `;

            return;
        }

        reminders.forEach(reminder => {

            const date = new Date(reminder.reminder_date);

            const formattedDate = date.toLocaleDateString(
                'en-GB',
                {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }
            );

            const time = reminder.reminder_time
                ? reminder.reminder_time.substring(0, 5)
                : '';

            const creatorName =
                reminder.creator?.name ?? 'Unknown';

            const currentUserId = {{ Auth::id() }};

            const canDelete =
                Number(reminder.created_by) === Number(currentUserId);

            list.innerHTML += `
                <div class="reminder-item">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="fw-semibold">
                                <i class="mdi mdi-calendar-clock me-1"></i>
                                ${formattedDate} ${time}
                            </div>

                            <div class="text-muted small mt-1">
                                Created by:
                                <strong>${escapeHtml(creatorName)}</strong>
                            </div>

                            ${
                                reminder.note
                                ? `
                                    <div class="mt-2">
                                        ${escapeHtml(reminder.note)}
                                    </div>
                                  `
                                : ''
                            }

                        </div>

                        ${
                            canDelete
                            ? `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="deleteReminder(${reminder.id})"
                                >
                                    <i class="mdi mdi-delete-outline"></i>
                                </button>
                              `
                            : ''
                        }

                    </div>

                </div>
            `;
        });
    })
    .catch(error => {

        loading.style.display = 'none';

        list.innerHTML = `
            <div class="alert alert-danger">
                Unable to load reminders.
            </div>
        `;

        console.error(error);
    });
}


function deleteReminder(reminderId)
{
    if (!confirm('Are you sure you want to delete this reminder?')) {
        return;
    }

    fetch(`/lead-reminders/${reminderId}`, {

        method: 'DELETE',

        headers: {
            'X-CSRF-TOKEN':
                document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),

            'Accept': 'application/json',

            'X-Requested-With': 'XMLHttpRequest'
        }

    })
    .then(response => {

        if (!response.ok) {
            throw new Error('Unable to delete reminder.');
        }

        return response.json();

    })
    .then(result => {

        if (result.success) {
            loadReminders();
        }

    })
    .catch(error => {

        alert('Unable to delete reminder.');

        console.error(error);

    });
}


function escapeHtml(value)
{
    const div = document.createElement('div');

    div.textContent = value ?? '';

    return div.innerHTML;
}
</script>
@endsection
