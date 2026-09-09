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
        padding: 10px 0;
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

    /* Modal overlay */
    .reminder-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(0, 0, 0, 0.45);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .reminder-modal-overlay.show {
        display: flex;
    }

    .reminder-modal-box {
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 12px 40px rgba(0,0,0,.18);
    }

    .reminder-modal-box.large {
        max-width: 700px;
    }

    .reminder-modal-header {
        background: #fff;
        color: #111827;
        padding: 22px 24px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #eef0f3;
    }

    .reminder-modal-header h5 {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
        color: #111827;
    }

    .reminder-modal-header .btn-close {
        background: none;
        border: none;
        font-size: 20px;
        line-height: 1;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px;
    }

    .reminder-modal-header .btn-close:hover {
        color: #374151;
    }

    .reminder-modal-body {
        padding: 22px 24px;
    }

    .reminder-modal-body .form-label {
        font-size: 13.5px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }

    .reminder-modal-body .form-control,
    .reminder-modal-body textarea.form-control {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13.5px;
    }

    .reminder-modal-body .form-control:focus {
        border-color: #0d2c6c;
        box-shadow: 0 0 0 3px rgba(13,44,108,0.08);
    }

    .reminder-modal-body .mb-3 {
        margin-bottom: 20px !important;
    }

    .reminder-modal-footer {
        padding: 16px 24px 22px;
        border-top: none;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .reminder-modal-footer .btn-primary {
        background: #4f46e5;
        border-color: #4f46e5;
        border-radius: 10px;
        padding: 9px 22px;
        font-size: 13.5px;
        font-weight: 500;
    }

    .reminder-modal-footer .btn-primary:hover {
        background: #4338ca;
        border-color: #4338ca;
    }

    .reminder-modal-footer .btn-light {
        border-radius: 10px;
        padding: 9px 20px;
        font-size: 13.5px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
    }

    .button-group {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .collapsible-header {
        cursor: pointer;
        user-select: none;
    }

    .collapse-icon {
        margin-left: auto;
        transition: transform 0.25s ease;
        font-size: 20px;
    }

    .collapse-icon.rotated {
        transform: rotate(180deg);
    }

    .collapsible-body {
        overflow: hidden;
        transition: max-height 0.3s ease;
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

                        <div class="col-md-8 mb-4">
                            <div class="card custom-card h-100">

                                <div class="card-header custom-header collapsible-header d-flex justify-content-between align-items-center" onclick="toggleCard(this)">
                                    <div>
                                        <i class="mdi mdi-file-document-outline menu-icon icon-head me-2"></i>
                                        {{ $lead->company_business_name ?? 'Lead' }}
                                    </div>
                                    <i class="mdi mdi-chevron-down collapse-icon"></i>
                                </div>

                                <div class="card-body collapsible-body">

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
                                        <i class="mdi mdi-briefcase-outline"></i>
                                        <span class="label">Business Type:</span>
                                        <span>{{ $lead->business_type ?? '-' }}</span>
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
                                        <i class="mdi mdi-cake"></i>
                                        <span class="label">Date of Birth:</span>
                                        <span>{{ $lead->date_of_birth?->format('d M Y') ?? '-' }}</span>
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
                                        <i class="mdi mdi-cellphone"></i>
                                        <span class="label">Mobile:</span>
                                        <span>{{ $lead->mobile_no ? '+44 '.$lead->mobile_no : '-' }}</span>
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

                                    <div class="detail-row">
                                        <i class="mdi mdi-cash-multiple"></i>
                                        <span class="label">Gross Sales:</span>
                                        <span>{{ $lead->gross_sales ? '£'.number_format($lead->gross_sales, 2) : '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-cash"></i>
                                        <span class="label">Funds Required:</span>
                                        <span>{{ $lead->funds_required ? '£'.number_format($lead->funds_required, 2) : '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-calendar-range"></i>
                                        <span class="label">Funds Term (Months):</span>
                                        <span>{{ $lead->funds_term_months ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-home-outline"></i>
                                        <span class="label">Home Owner:</span>
                                        <span>{{ $lead->home_owner ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-file-percent-outline"></i>
                                        <span class="label">VAT Registered:</span>
                                        <span>{{ $lead->vat_registered ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-target"></i>
                                        <span class="label">Loan Purpose:</span>
                                        <span>{{ $lead->loan_purpose ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-text-box-outline"></i>
                                        <span class="label">Funds Usage Details:</span>
                                        <span>{{ $lead->funds_usage_details ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-map-marker-radius-outline"></i>
                                        <span class="label">Supply Address:</span>
                                        <span>{{ $lead->supply_address ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-mailbox-outline"></i>
                                        <span class="label">Postcode:</span>
                                        <span>{{ $lead->postcode ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-office-building-marker-outline"></i>
                                        <span class="label">Number of Sites:</span>
                                        <span>{{ $lead->number_of_sites ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-flash-outline"></i>
                                        <span class="label">MPAN:</span>
                                        <span>{{ $lead->mpan ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-gas-cylinder"></i>
                                        <span class="label">MPRN:</span>
                                        <span>{{ $lead->mprn ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-barcode"></i>
                                        <span class="label">SPID:</span>
                                        <span>{{ $lead->spid ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-note-text-outline"></i>
                                        <span class="label">Notes:</span>
                                        <span>{{ $lead->notes ?? '-' }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Overview -->
                        <div class="col-md-4 mb-4">
                            <div class="card custom-card h-100">

                                <div class="card-header custom-header collapsible-header d-flex justify-content-between align-items-center" onclick="toggleCard(this)">
                                    <div>
                                        <i class="mdi mdi-information-outline me-2 icon-head"></i>
                                        Lead Overview
                                    </div>
                                    <i class="mdi mdi-chevron-down collapse-icon"></i>
                                </div>

                                <div class="card-body px-3 py-2 collapsible-body">

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
<div id="addReminderModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box">

        <div class="reminder-modal-header">
            <h5>Add Reminder</h5>
            <button type="button" class="btn-close" onclick="closeAddReminderModal()">&times;</button>
        </div>

        <form method="POST" action="{{ route('leads.reminders.store', $lead) }}">
            @csrf

            <div class="reminder-modal-body">

                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="reminder_date" class="form-control"
                           min="{{ date('Y-m-d') }}" value="{{ old('reminder_date') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Time</label>
                    <input type="time" name="reminder_time" class="form-control"
                           value="{{ old('reminder_time') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Note (Optional)</label>
                    <textarea name="note" class="form-control" rows="4"
                              placeholder="">{{ old('note') }}</textarea>
                </div>

            </div>

            <div class="reminder-modal-footer">
                <button type="submit" class="btn btn-primary">Save Reminder</button>
            </div>

        </form>
    </div>
</div>
<div id="remindersModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box large">

        <div class="reminder-modal-header">
            <h5>Lead Reminders</h5>
            <button type="button" class="btn-close" onclick="closeRemindersModal()">&times;</button>
        </div>

        <div class="reminder-modal-body">

            <div id="remindersLoading" class="text-center py-4">
                <div class="spinner-border"></div>
                <div class="mt-2 text-muted">Loading reminders...</div>
            </div>

            <div id="remindersList"></div>

        </div>

    </div>
</div>
<!-- Notes and Documents -->
<div class="row mt-2">
    <div class="col-md-12">
        <div class="card custom-card">

            <div class="card-header custom-header">
                <i class="mdi mdi-chart-bar me-2 icon-head"></i>
                Notes and Documents
            </div>

            <div class="card-body">

                <h6 class="mb-2">Activity</h6>

                <div id="notesDocumentsFeed" class="mb-4">
                    <div class="text-center text-muted py-3" id="feedLoading">
                        Loading activity...
                    </div>
                </div>

                <hr class="my-3">

                <div id="quillEditor" style="height: 150px; background:#fff;"></div>

                <div class="d-flex align-items-center gap-2 mt-3">
                    <input type="file" id="documentFile" class="form-control file-upload-info">
                </div>

                <button type="button" class="btn btn-primary mt-3" id="sentBtn" onclick="sendActivity()">
                    <span id="sentBtnText">Sent</span>
                </button>

            </div>
        </div>
    </div>
</div>

<!-- Edit Note Modal -->
<div id="editActivityModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box">

        <div class="reminder-modal-header">
            <h5>Edit Note</h5>
            <button type="button" class="btn-close" onclick="closeEditModal()">&times;</button>
        </div>

        <div class="reminder-modal-body">
            <label class="form-label">Note</label>
            <div id="quillEditEditor" style="height: 150px; background:#fff;"></div>
        </div>

        <div class="reminder-modal-footer">
            <button type="button" class="btn btn-light" onclick="closeEditModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveEditedActivity()">Save</button>
        </div>

    </div>
</div>

<script>
    const quill = new Quill('#quillEditor', {
        theme: 'snow',
        placeholder: 'Write a note...',
        modules: {
            toolbar: [
                [{ font: [] }, { header: [1, 2, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ color: [] }, { background: [] }],
                [{ script: 'sub' }, { script: 'super' }],
                ['blockquote', 'code-block'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ indent: '-1' }, { indent: '+1' }],
                [{ align: [] }],
                ['link', 'image', 'video'],
                ['clean'],
            ],
        },
    });

    const quillEdit = new Quill('#quillEditEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link', 'clean'],
            ],
        },
    });

    const leadId = {{ $lead->id }};
    const currentUserId = {{ Auth::id() }};
    let editingActivityId = null;

    function csrfToken()
    {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function sendActivity()
    {
        const contentHtml = quill.root.innerHTML.trim();
        const hasContent = contentHtml && contentHtml !== '<p><br></p>';

        const fileInput = document.getElementById('documentFile');
        const hasFile = fileInput.files.length > 0;

        if (!hasContent && !hasFile) {
            Swal.fire('Missing info', 'Please write a note, choose a file, or both.', 'warning');
            return;
        }

        const btn = document.getElementById('sentBtn');
        btn.disabled = true;
        document.getElementById('sentBtnText').textContent = 'Sending...';

        const formData = new FormData();
        if (hasContent) formData.append('content', contentHtml);
        if (hasFile) formData.append('file', fileInput.files[0]);

        fetch(`/leads/${leadId}/activities`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                quill.root.innerHTML = '';
                fileInput.value = '';
                prependActivity(result.activity);
                Swal.fire({ icon: 'success', title: 'Posted', timer: 1200, showConfirmButton: false });
            } else {
                Swal.fire('Error', result.message ?? 'Unable to save.', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Unable to save. Please try again.', 'error');
            console.error(err);
        })
        .finally(() => {
            btn.disabled = false;
            document.getElementById('sentBtnText').textContent = 'Sent';
        });
    }

    function renderActivity(item)
    {
        const when = new Date(item.created_at).toLocaleString('en-GB', {
            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
        });

        const isOwner = Number(item.created_by) === Number(currentUserId);

        const fileBlock = item.file_path ? `
            <div class="mt-2">
                <i class="mdi mdi-file-document-outline me-1"></i>
                <a href="/storage/${item.file_path}" target="_blank">${escapeHtml(item.original_name)}</a>
            </div>
        ` : '';

        const contentBlock = item.content ? `<div class="mt-1 activity-content">${item.content}</div>` : '';

        const actions = isOwner ? `
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openEditModal(${item.id})">
                    <i class="mdi mdi-pencil-outline"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteActivity(${item.id})">
                    <i class="mdi mdi-delete-outline"></i>
                </button>
            </div>
        ` : '';

        return `
            <div class="reminder-item" data-activity-id="${item.id}" data-raw-content="${item.content ? encodeURIComponent(item.content) : ''}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">
                            <i class="mdi mdi-note-text-outline me-1"></i>
                            ${escapeHtml(item.creator?.name ?? 'Unknown')} &middot; ${when}
                        </div>
                        ${contentBlock}
                        ${fileBlock}
                    </div>
                    ${actions}
                </div>
            </div>
        `;
    }

    function prependActivity(item)
    {
        const feed = document.getElementById('notesDocumentsFeed');
        const empty = feed.querySelector('.text-muted.py-3');
        if (empty && empty.id !== 'feedLoading') empty.remove();

        feed.insertAdjacentHTML('afterbegin', renderActivity(item));
    }

    function loadFeed()
    {
        const feed = document.getElementById('notesDocumentsFeed');
        feed.innerHTML = '<div class="text-center text-muted py-3">Loading activity...</div>';

        fetch(`/leads/${leadId}/activities`, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(items => {
                if (!items.length) {
                    feed.innerHTML = '<div class="text-center text-muted py-3">No activity yet.</div>';
                    return;
                }
                feed.innerHTML = items.map(renderActivity).join('');
            })
            .catch(err => {
                feed.innerHTML = '<div class="alert alert-danger">Unable to load activity.</div>';
                console.error(err);
            });
    }

    function openEditModal(id)
    {
        const row = document.querySelector(`[data-activity-id="${id}"]`);
        const raw = row?.dataset.rawContent ? decodeURIComponent(row.dataset.rawContent) : '';

        editingActivityId = id;
        quillEdit.root.innerHTML = raw;

        document.getElementById('editActivityModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal()
    {
        document.getElementById('editActivityModal').classList.remove('show');
        document.body.style.overflow = '';
        editingActivityId = null;
    }

    function saveEditedActivity()
    {
        const contentHtml = quillEdit.root.innerHTML.trim();

        if (!contentHtml || contentHtml === '<p><br></p>') {
            Swal.fire('Missing info', 'Note cannot be empty.', 'warning');
            return;
        }

        fetch(`/lead-activities/${editingActivityId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ content: contentHtml }),
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                const row = document.querySelector(`[data-activity-id="${editingActivityId}"]`);
                row?.outerHTML && row.replaceWith(document.createRange().createContextualFragment(renderActivity(result.activity)));
                closeEditModal();
                Swal.fire({ icon: 'success', title: 'Updated', timer: 1200, showConfirmButton: false });
            } else {
                Swal.fire('Error', result.message ?? 'Unable to update.', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Unable to update.', 'error');
            console.error(err);
        });
    }

    function deleteActivity(id)
    {
        Swal.fire({
            title: 'Delete this Note and document ?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#d33',
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch(`/lead-activities/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    document.querySelector(`[data-activity-id="${id}"]`)?.remove();
                    Swal.fire({ icon: 'success', title: 'Deleted', timer: 1000, showConfirmButton: false });
                } else {
                    Swal.fire('Error', 'Unable to delete.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Unable to delete.', 'error');
                console.error(err);
            });
        });
    }

    function escapeHtml(value)
    {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    document.addEventListener('DOMContentLoaded', loadFeed);

    function toggleCard(header)
    {
        const body = header.nextElementSibling;
        const icon = header.querySelector('.collapse-icon');

        if (body.style.maxHeight && body.style.maxHeight !== '0px') {
            body.style.maxHeight = '0px';
            body.style.paddingTop = '0px';
            body.style.paddingBottom = '0px';
            icon.classList.add('rotated');
        } else {
            body.style.maxHeight = body.scrollHeight + 'px';
            body.style.paddingTop = '';
            body.style.paddingBottom = '';
            icon.classList.remove('rotated');
        }
    }

    // Initialize all collapsible cards as expanded on page load
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.collapsible-body').forEach(function (body) {
            body.style.maxHeight = body.scrollHeight + 'px';
        });
    });
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
