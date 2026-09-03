@extends('layout')
@section('title', 'Leads')
@section('subtitle', 'Leads')
@section('content')
<style>

    .required-label::after {
        content: ' *';
        color: red;
    }
    .select2-container .select2-selection--single {
    box-sizing: border-box;
    cursor: pointer;
    display: block;
    height: 45px !important;
     }
    .lead-status-simple {
        padding: 4px 8px;
        font-size: 13px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #fff;
        outline: none;
        cursor: pointer;
    }

    .lead-status-simple:focus {
        border-color: #999;
    }
</style>
<style>
    .btn-delete{
        height: 35px;
        align-content: center;
    }
    .edit-lead-btn{
        height: 35px;
    }
    .view-btn{
        height: 35px;
        align-content: center;
    }
    .modal-dialog {
        max-width: 600px;   /* good desktop default */
        margin: 1.75rem auto;
    }

    /* Modal content scroll fix */
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
@php
    $isSuperAdmin   = in_array(strtolower($authUser->role->name), ['super admin']);
    $isAdminOrMIS   = in_array(strtolower($authUser->role->name), ['admin', 'mis user']);
    $isAdminOrSuper = $isSuperAdmin || $isAdminOrMIS;
    $role = strtolower(optional(auth()->user()->role)->name);
@endphp



<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between mb-3 align-items-center">
                        <h4 class="card-title mb-0">
                            Leads ({{ $totalLeads }})
                        </h4>

                    <div class="d-flex flex-column flex-sm-row gap-2">

                        @if(in_array($role, ['super admin','admin','mis user']))
                            <button class="btn btn-primary mr-3" data-toggle="modal" data-target="#createModal">
                                Add Lead
                            </button>
                        @endif
                        @if(in_array($role, ['admin','mis user']))
                            <form id="uploadExcelForm" action="{{ route('import') }}" method="POST" enctype="multipart/form-data" class="mb-0 mr-3">
                                @csrf
                                <input type="file" name="file" accept=".xls,.xlsx,.csv" style="display: none;" id="excelFileInput">
                                <button type="button" class="btn btn-secondary" id="selectExcelBtn">Upload Excel</button>
                            </form>

                            <a href="{{ route('leads.template') }}" class="btn btn-info">Download Template</a>
                        @endif
                    </div>
                </div>
                <div id="loader" style="
                    display:none;
                    position:fixed;
                    top:0; left:0;
                    width:100%; height:100%;
                    background:rgba(255,255,255,0.7);
                    z-index:9999;
                    text-align:center;
                    padding-top:20%; ">
                    <div class="spinner-border text-primary"></div>
                    <p>Uploading Excel, please wait...</p>
                </div>
                <div class="table-responsive">
                    <table id="leadsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Source</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!--  CREATE MODAL = -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="createLeadForm"   method="POST" action="{{ route('leads.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Lead</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Full Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="Phone">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">Company</label>
                                <input type="text" name="company" class="form-control" placeholder="Company">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">City</label>
                                <input type="text" name="city" class="form-control" placeholder="City">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">Source</label>
                                <input type="text" name="source" class="form-control" placeholder="Source">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @if($isSuperAdmin)
                            {{-- Agency (admin/super admin only) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required-label">Agency</label>
                                    <select name="agency_id"
                                            id="create_agency_select"
                                            class="form-control agency-select">
                                        <option value="">--Select Agency--</option>
                                        @foreach($agencies as $agency)
                                            <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Assign User (populated by agency change) --}}

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Assign User</label>
                                    <select name="assigned_user_id"
                                            id="create_user_select"
                                            class="form-control"
                                            >
                                        <option value="">-- Select Agency First --</option>
                                    </select>
                                </div>
                            </div>
                        @else
                            {{-- Hidden agency for user role (auto-filled server-side) --}}
                            {{-- No agency input shown; agency_id injected in controller --}}

                            {{-- Assign User: pre-loaded with same-agency users --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Assign User</label>
                                    <select name="assigned_user_id"
                                            id="create_user_select"
                                            class="form-control">
                                        <option value="">-- Select User --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="required-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Notes"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="createSubmitBtn">Save Lead</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!--  EDIT MODALS = -->
@foreach($leads as $lead)
<div class="modal fade" id="editModal{{ $lead->id }}" >
    <div class="modal-dialog modal-lg">
        <form class="editLeadForm"
                method="POST"
              data-id="{{ $lead->id }}"
              data-url="{{ route('leads.update', $lead->id) }}"
              enctype="multipart/form-data">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Lead</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">Name</label>
                                <input type="text" name="name" value="{{ $lead->name }}" class="form-control" placeholder="Full Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">Phone</label>
                                <input type="text" name="phone" value="{{ $lead->phone }}" class="form-control" placeholder="Phone">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">Email</label>
                                <input type="email" name="email" value="{{ $lead->email }}" class="form-control" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">Company</label>
                                <input type="text" name="company" value="{{ $lead->company }}" class="form-control" placeholder="Company">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">City</label>
                                <input type="text" name="city" value="{{ $lead->city }}" class="form-control" placeholder="City">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required-label">Source</label>
                                <input type="text" name="source" value="{{ $lead->source }}" class="form-control" placeholder="Source">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-{{ $isSuperAdmin  ? '4' : '12' }}">
                            <div class="form-group">
                                <label class="required-label">Status</label>
                                    <select name="status" class="form-control">
                                        @php
                                            $statuses = ['Not Started', 'In Progress', 'Hold', 'Lost', 'Complete'];
                                        @endphp
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ $lead->status == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>

                        @if($isSuperAdmin)
                            {{-- Agency (admin/super admin only) --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required-label">Agency</label>
                                    <select name="agency_id"
                                            class="form-control agency-select"
                                            data-user-target="edit_user_{{ $lead->id }}"
                                            data-selected-users="{{ json_encode($lead->users->pluck('id')->toArray()) }}">
                                        <option value="">-- Select Agency --</option>
                                        @foreach($agencies as $agency)
                                            <option value="{{ $agency->id }}"
                                                    {{ $lead->agency_id == $agency->id ? 'selected' : '' }}>
                                                {{ $agency->agency_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Assigned users (admin/super admin) --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required-label">Assign User</label>
                                    @php $selectedUserIds = $lead->users->pluck('id')->toArray(); @endphp
                                        <select name="assigned_user_id"
                                                id="edit_user_{{ $lead->id }}"
                                                class="form-control">
                                            <option value="">-- Select User --</option>
                                            @foreach($users->where('agency_id', $lead->agency_id) as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ in_array($user->id, $selectedUserIds) ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>
                        @else
                            {{-- User role: show same-agency users, pre-select existing --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="required-label">Assign User</label>
                                    @php $selectedUserIds = $lead->users->pluck('id')->toArray(); @endphp
                                        <select name="assigned_user_id"
                                                id="edit_user_{{ $lead->id }}"
                                                class="form-control">
                                            <option value="">-- Select User --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ in_array($user->id, $selectedUserIds) ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="required-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Notes">{{ $lead->notes }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
@if(session('failed_rows') && count(session('failed_rows')) > 0)
<script>
document.addEventListener("DOMContentLoaded", function () {

    setTimeout(() => {

        let failedRows = @json(session('failed_rows'));
        let message = '<b>Import Failed Details:</b><br><br>';

        failedRows.forEach(row => {
            message += `Row ${row.row_number}: ${row.reason}<br>`;
        });

        Swal.fire({
            icon: 'error',
            title: 'Import Errors',
            html: message,
            width: 600
        });

    }, 500); // delay so it runs AFTER global swal

});
</script>
@endif
<script>

const ALL_USERS = {!! json_encode($users->map(function($u) {
    return ['id' => $u->id, 'name' => $u->name, 'agency_id' => $u->agency_id];
})) !!};


    const IS_SUPER_ADMIN    = {{ $isSuperAdmin ? 'true' : 'false' }};
    const IS_ADMIN_OR_MIS   = {{ $isAdminOrMIS ? 'true' : 'false' }};
    const IS_ADMIN_OR_SUPER = {{ $isAdminOrSuper ? 'true' : 'false' }};

</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('excelFileInput');
    const selectBtn = document.getElementById('selectExcelBtn');
    const form = document.getElementById('uploadExcelForm');
    const loader = document.getElementById('loader');

    if (selectBtn && fileInput && form) {

        // Open file picker
        selectBtn.addEventListener('click', function() {
            fileInput.click();
        });

        // Auto-submit on file select
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {

                // Show loader
                if (loader) loader.style.display = 'block';

                // Disable button to prevent multiple clicks
                selectBtn.disabled = true;

                // Submit form
                form.submit();
            }
        });

    }
});
$(document).ready(function () {
    $('#leadsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ordering: true,
        responsive: true,
        ajax: "{{ route('leads.index') }}",
        columns: [
            { data: 'name' },
            { data: 'company' },
            { data: 'assigned_user' },
            { data: 'status' },
            { data: 'source' },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(id){
                    return `
                        <a href="/leads/${id}" class="btn btn-sm btn-primary view-btn" target="_blank">
                            <i class="mdi mdi-eye"></i> View
                        </a>
                        <button class="btn btn-sm btn-primary edit-lead-btn" data-id="${id}">
                            <i class="mdi mdi-pencil-box"></i> Edit
                        </button>
                        <a href="/leads/${id}/delete" class="btn btn-sm btn-danger btn-delete">
                            <i class="mdi mdi-delete"></i> Delete
                        </a>
                    `;
                }
            }
        ]
    });

    // Use delegated event for dynamically loaded buttons
    $('#leadsTable').on('click', '.edit-lead-btn', function () {
        let id = $(this).data('id');
        $('#editModal' + id).modal('show');
    });
});
(function waitForJQ() {
    if (typeof $ === 'undefined') { setTimeout(waitForJQ, 50); return; }

    function populateUsers(targetId, agencyId, selectedIds) {
        const $sel    = $('#' + targetId);
        const selected = (selectedIds || []).map(String);

        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }

        $sel.empty();

        if (!agencyId) {
            $sel.append('<option value="">-- Please select an Agency first --</option>');
            $sel.prop('disabled', true);
            $sel.select2({
                width: '100%',
                placeholder: '-- Please select an Agency first --',
                allowClear: false
            });
            return;
        }

        const filtered = ALL_USERS.filter(u => String(u.agency_id) === String(agencyId));

        if (filtered.length) {
            $sel.append('<option value="">-- Select users --</option>');
            filtered.forEach(function (u) {
                const isSelected = selected.includes(String(u.id)) ? 'selected' : '';
                $sel.append(`<option value="${u.id}" ${isSelected}>${u.name}</option>`);
            });
            $sel.prop('disabled', false);
        } else {
            $sel.append('<option value="" disabled>No users in this agency</option>');
            $sel.prop('disabled', true);
        }

        const $parentModal = $sel.closest('.modal');

        $sel.select2({
            width: '100%',
            dropdownParent: $parentModal.length ? $parentModal : $(document.body),
            placeholder: '-- Select users --',
            allowClear: true
        });

        if (selected.length) {
            $sel.val(selected).trigger('change');
        }
    }
    document.querySelectorAll('.lead-status-simple').forEach(function(select){
        function updateBadgeColor(el) {
            const status = el.value;
            let colorClass;
            switch(status){
                case 'Not Started': colorClass = 'badge-secondary'; break;
                case 'In Progress': colorClass = 'badge-warning'; break;
                case 'Hold':        colorClass = 'badge-info'; break;
                case 'Lost':        colorClass = 'badge-danger'; break;
                case 'Complete':    colorClass = 'badge-success'; break;
                default:            colorClass = 'badge-secondary';
            }
            el.className = 'form-control lead-status-simple ' + colorClass;
        }

        updateBadgeColor(select);

        select.addEventListener('change', function(){
            updateBadgeColor(this);
            const leadId = this.dataset.leadId;
            const status = this.value;

            fetch(`/leads/${leadId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({status: status})
            })
            .then(res => res.json())
            .then(data => {
                // Show SweetAlert success
                Swal.fire({
                    icon: 'success',
                    title: 'Status Updated',
                    text: data.success,
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(err => {
                // Show SweetAlert error
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                });
                console.error(err);
            });
        });
    });
    // Agency change handler (only fires for admin/super admin)
    $(document).on('change', '.agency-select', function () {
        const agencyId = $(this).val();

        if ($(this).attr('id') === 'create_agency_select') {
            populateUsers('create_user_select', agencyId, []);
        } else {
            const targetId    = $(this).data('user-target');
            const selectedIds = $(this).data('selected-users') || [];
            populateUsers(targetId, agencyId, selectedIds);
        }
    });

    // Create modal open
    $('#createModal').on('show.bs.modal', function () {
        const $form = $(this).find('#createLeadForm');
        $form[0].reset();

        if (IS_SUPER_ADMIN) {
            // Super admin — reset agency + user selects
            $('#create_agency_select').val('').trigger('change');
            $('#create_user_select')
                .empty()
                .append('<option value="">-- Select Agency First --</option>')
                .prop('disabled', true)
                .trigger('change');

        } else if (IS_ADMIN_OR_MIS) {
            // Admin/MIS — no agency select, just reset user select
            const $userSel = $('#create_user_select');
            if ($userSel.hasClass('select2-hidden-accessible')) {
                $userSel.select2('destroy');
            }
            $userSel.val(null).select2({
                width         : '100%',
                dropdownParent: $('#createModal'),
                placeholder   : 'Select users...',
                allowClear    : true,
            });
        }

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();
    });

    // Edit modal
    $(document).on('show.bs.modal', '[id^="editModal"]', function () {
        const $modal = $(this);
        const $form = $modal.find('.editLeadForm');
        if (!$form.length) return;

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();

        if (IS_SUPER_ADMIN) {
            $modal.find('.agency-select').each(function () {
                if ($(this).val()) $(this).trigger('change');
            });
        }
    });
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

    $(document).on('submit', '#createLeadForm', function(e) {

        e.preventDefault();

        const $form = $(this);
        const $btn  = $('#createSubmitBtn');
        clearErrors($form);

        $btn.prop('disabled', true).text('Saving…');

        $.ajax({
            url        : '{{ route("leads.store") }}',
            method     : 'POST',
            data       : new FormData($form[0]),
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    $('#createModal').modal('hide');
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
                $btn.prop('disabled', false).text('Save Lead');
            },
        });
    });

    $(document).on('submit', '.editLeadForm', function (e) {
        e.preventDefault();

        const $form  = $(this);
        const url    = $form.data('url');
         console.log('Submitting to:', url);
        const $btn   = $form.find('[type="submit"]');
        clearErrors($form);

        $btn.prop('disabled', true).text('Updating…');

        $.ajax({
            url        : url,
            method     : 'POST',
            data       : new FormData($form[0]),
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    $form.closest('.modal').modal('hide');
                    Swal.fire({
                        icon : 'success',
                        title: 'Updated!',
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
                $btn.prop('disabled', false).text('Update');
            },
        });
    });
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This Lead will be permanently deleted!',
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
    $(document).on('change', 'input[type="file"]', function () {
        const id = this.id.replace('documentInput_', 'documentName_');
        const nameField = document.getElementById(id);
        if (nameField && this.files.length > 0) {
            nameField.value = this.files[0].name;
        }
    });

})();

</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let htmlContent = '';

    @if ($errors->any())
        htmlContent += '<b>Validation Errors:</b><ul>';
        @foreach ($errors->all() as $error)
            htmlContent += `<li>{{ $error }}</li>`;
        @endforeach
        htmlContent += '</ul><br>';
    @endif

    @if (session('error'))
        htmlContent += `<b>Error:</b> {{ session('error') }}<br><br>`;
    @endif

    @if(session('success'))
        htmlContent += `<b>{{ session('success') }}</b><br><br>`;
        const failedRows = @json(session('failedRows', []));
        if(failedRows.length > 0) {
            htmlContent += '<b>Failed Rows:</b><ul>';
            failedRows.forEach(function(fail) {
                htmlContent += `<li>Row ${fail.row_number || 'N/A'}: ${fail.reason || 'Unknown error'}</li>`;
            });
            htmlContent += '</ul>';
        }
    @endif

    if(htmlContent.length > 0) {
        Swal.fire({
            icon: htmlContent.includes('Validation Errors') || htmlContent.includes('Error') ? 'error' : 'success',
            title: 'Upload Result',
            html: htmlContent,
            width: 600
        });
    }
});
</script>
@endsection
