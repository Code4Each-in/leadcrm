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

    .btn-delete {
        height: 35px;
        align-content: center;
    }

    .edit-lead-btn {
        height: 35px;
    }

    .view-btn {
        height: 35px;
        align-content: center;
    }

    .modal-dialog {
        max-width: 700px !important;
        margin: 1.75rem auto !important;
    }

    .modal-body {
        max-height: 70vh !important;
        overflow-y: auto !important;
    }

    .dataTables_wrapper {
        width: 100% !important;
    }

    @media (max-width: 768px) {

        table.dataTable td {
            white-space: normal !important;
        }

        .btn {
            margin-bottom: 5px;
        }

        .modal-dialog {
            max-width: 92% !important;
            margin: 1rem auto !important;
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

    @media (max-width: 576px) {

        .modal-dialog {
            max-width: 100% !important;
            margin: 0 !important;
        }

        .modal-content {
            height: 100% !important;
            border-radius: 0 !important;
        }

        .modal-body {
            max-height: none !important;
        }

        .modal-footer .btn {
            width: 100%;
        }

        .modal-title,
        .modal-header h5 {
            font-size: 16px;
        }
    }
</style>


@php
    $role = strtolower(optional(auth()->user()->role)->name);

    $isSuperAdmin = $role === 'super admin';

    $isAdminOrMIS = in_array($role, [
        'admin',
        'mis user'
    ]);

    $canCreateLead = in_array($role, [
        'super admin',
        'admin',
        'mis user'
    ]);

    /*
    |--------------------------------------------------------------------------
    | AGILE ONE
    |--------------------------------------------------------------------------
    | There is only one agency in the system.
    | Agency ID is handled server-side by LeadController.
    */
@endphp
<div class="row">
    <div class="col-md-12 grid-margin">

        <div class="card">

            <div class="card-body">

                <!-- Header -->
                <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap">

                    <h4 class="card-title mb-0">
                        Leads ({{ $totalLeads }})
                    </h4>

                    <div class="d-flex flex-column flex-sm-row gap-2 mt-2 mt-sm-0">

                        {{-- Add Lead --}}
                        @if($canCreateLead)

                            <button
                                class="btn btn-primary mr-3"
                                data-toggle="modal"
                                data-target="#createModal"
                            >
                                Add Lead
                            </button>

                        @endif


                        {{-- Excel Upload --}}
                        @if($isAdminOrMIS)

                            <form
                                id="uploadExcelForm"
                                action="{{ route('import') }}"
                                method="POST"
                                enctype="multipart/form-data"
                                class="mb-0 mr-3"
                            >

                                @csrf

                                <input
                                    type="file"
                                    name="file"
                                    accept=".xls,.xlsx,.csv"
                                    style="display:none;"
                                    id="excelFileInput"
                                >

                                <!-- <button
                                    type="button"
                                    class="btn btn-secondary"
                                    id="selectExcelBtn"
                                >
                                    Upload Excel
                                </button> -->

                            </form>


                            <!-- {{-- Download Template --}}
                            <a
                                href="{{ route('leads.template') }}"
                                class="btn btn-info"
                            >
                                Download Template
                            </a> -->

                        @endif

                    </div>

                </div>
                <div
                    id="loader"
                    style="
                        display:none;
                        position:fixed;
                        top:0;
                        left:0;
                        width:100%;
                        height:100%;
                        background:rgba(255,255,255,0.7);
                        z-index:9999;
                        text-align:center;
                        padding-top:20%;
                    "
                >

                    <div class="spinner-border text-primary"></div>

                    <p>
                        Uploading Excel, please wait...
                    </p>

                </div>

                <div class="table-responsive">

                    <table
                        id="leadsTable"
                        class="table table-striped"
                        width="100%"
                    >

                        <thead>

                            <tr>

                                <th>Name</th>

                                <th>Company</th>

                                <th>Assigned To</th>

                                <th>Status</th>

                                <th>Source</th>

                                <th width="150">
                                    Action
                                </th>

                            </tr>

                        </thead>

                    </table>

                </div>

            </div>

        </div>

    </div>
</div>

@if($canCreateLead)

<div
    class="modal fade"
    id="createModal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg">

        <form
            id="createLeadForm"
            method="POST"
            action="{{ route('leads.store') }}"
            enctype="multipart/form-data"
        >

            @csrf

            <div class="modal-content">


                <!-- Header -->
                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Lead
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                    >
                        &times;
                    </button>

                </div>


                <!-- Body -->
                <div class="modal-body">


                    <!-- Name / Phone -->
                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Name
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Full Name"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Phone
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    placeholder="Phone"
                                >

                            </div>

                        </div>

                    </div>


                    <!-- Email / Company -->
                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="Email"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Company
                                </label>

                                <input
                                    type="text"
                                    name="company"
                                    class="form-control"
                                    placeholder="Company"
                                >

                            </div>

                        </div>

                    </div>


                    <!-- City / Source -->
                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    City
                                </label>

                                <input
                                    type="text"
                                    name="city"
                                    class="form-control"
                                    placeholder="City"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Source
                                </label>

                                <input
                                    type="text"
                                    name="source"
                                    class="form-control"
                                    placeholder="Source"
                                >

                            </div>

                        </div>

                    </div>

                    <input
                        type="hidden"
                        name="agency_id"
                        value="1"
                    >

                    <div class="row">

                        <div class="col-md-12">

                            <div class="form-group">

                                <label>
                                    Assign User
                                </label>

                                <select
                                    name="assigned_user_id"
                                    id="create_user_select"
                                    class="form-control"
                                >

                                    <option value="">
                                        -- Select User --
                                    </option>

                                    @foreach($users as $user)

                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </div>


                    <!-- Notes -->
                    <div class="form-group">

                        <label class="required-label">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            class="form-control"
                            rows="3"
                            placeholder="Notes"
                        ></textarea>

                    </div>

                </div>


                <!-- Footer -->
                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="createSubmitBtn"
                    >
                        Save
                    </button>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Cancel
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@endif

@foreach($leads as $lead)

<div
    class="modal fade"
    id="editModal{{ $lead->id }}"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg">

        <form
            class="editLeadForm"
            method="POST"
            data-id="{{ $lead->id }}"
            data-url="{{ route('leads.update', $lead->id) }}"
            enctype="multipart/form-data"
        >

            @csrf

            <div class="modal-content">


                <!-- Header -->
                <div class="modal-header">

                    <h5 class="modal-title">
                        Edit Lead
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                    >
                        &times;
                    </button>

                </div>


                <!-- Body -->
                <div class="modal-body">


                    <!-- Name / Phone -->
                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Name
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ $lead->name }}"
                                    class="form-control"
                                    placeholder="Full Name"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Phone
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ $lead->phone }}"
                                    class="form-control"
                                    placeholder="Phone"
                                >

                            </div>

                        </div>

                    </div>


                    <!-- Email / Company -->
                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ $lead->email }}"
                                    class="form-control"
                                    placeholder="Email"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Company
                                </label>

                                <input
                                    type="text"
                                    name="company"
                                    value="{{ $lead->company }}"
                                    class="form-control"
                                    placeholder="Company"
                                >

                            </div>

                        </div>

                    </div>


                    <!-- City / Source -->
                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    City
                                </label>

                                <input
                                    type="text"
                                    name="city"
                                    value="{{ $lead->city }}"
                                    class="form-control"
                                    placeholder="City"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Source
                                </label>

                                <input
                                    type="text"
                                    name="source"
                                    value="{{ $lead->source }}"
                                    class="form-control"
                                    placeholder="Source"
                                >

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Status
                                </label>

                                @php

                                    $statuses = [
                                        'Not Started',
                                        'In Progress',
                                        'Hold',
                                        'Lost',
                                        'Complete'
                                    ];

                                @endphp

                                <select
                                    name="status"
                                    class="form-control"
                                >

                                    @foreach($statuses as $status)

                                        <option
                                            value="{{ $status }}"
                                            {{ $lead->status == $status ? 'selected' : '' }}
                                        >
                                            {{ $status }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        <input
                            type="hidden"
                            name="agency_id"
                            value="1"
                        >


                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="required-label">
                                    Assign User
                                </label>

                                @php
                                    $selectedUserIds = $lead->users
                                        ->pluck('id')
                                        ->toArray();
                                @endphp

                                <select
                                    name="assigned_user_id"
                                    id="edit_user_{{ $lead->id }}"
                                    class="form-control"
                                >

                                    <option value="">
                                        -- Select User --
                                    </option>

                                    @foreach($users as $user)

                                        <option
                                            value="{{ $user->id }}"
                                            {{ in_array($user->id, $selectedUserIds) ? 'selected' : '' }}
                                        >
                                            {{ $user->name }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </div>


                    <!-- Notes -->
                    <div class="form-group">

                        <label class="required-label">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            class="form-control"
                            rows="3"
                            placeholder="Notes"
                        >{{ $lead->notes }}</textarea>

                    </div>

                </div>


                <!-- Footer -->
                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-success"
                    >
                        Update
                    </button>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Cancel
                    </button>

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

        let message =
            '<b>Import Failed Details:</b><br><br>';

        failedRows.forEach(row => {

            message +=
                `Row ${row.row_number}: ${row.reason}<br>`;

        });

        Swal.fire({

            icon: 'error',

            title: 'Import Errors',

            html: message,

            width: 600

        });

    }, 500);

});

</script>

@endif

<script>

const IS_SUPER_ADMIN =
    {{ $isSuperAdmin ? 'true' : 'false' }};

const IS_ADMIN_OR_MIS =
    {{ $isAdminOrMIS ? 'true' : 'false' }};

const CAN_CREATE_LEAD =
    {{ $canCreateLead ? 'true' : 'false' }};

</script>


<script>

(function waitForJQ() {

    if (typeof $ === 'undefined') {

        setTimeout(waitForJQ, 50);

        return;
    }

    const fileInput =
        document.getElementById('excelFileInput');

    const selectBtn =
        document.getElementById('selectExcelBtn');

    const form =
        document.getElementById('uploadExcelForm');

    const loader =
        document.getElementById('loader');


    if (selectBtn && fileInput && form) {

        selectBtn.addEventListener('click', function () {

            fileInput.click();

        });


        fileInput.addEventListener('change', function () {

            if (fileInput.files.length > 0) {

                if (loader) {

                    loader.style.display = 'block';

                }

                selectBtn.disabled = true;

                form.submit();

            }

        });

    }
    $(document).ready(function () {

        $('#leadsTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ordering: true,
            responsive: true,

            ajax: {
                url: "{{ route('leads.index') }}",
                type: "GET",
                error: function (xhr) {

                    console.error('Lead listing error:', xhr.responseText);

                    Swal.fire({
                        icon: 'error',
                        title: 'Unable to load leads',
                        text: 'There was a problem loading the lead listing. Please check the console for details.'
                    });
                }
            },

            columns: [
                {
                    data: 'name',
                    name: 'name',
                    defaultContent: 'N/A'
                },
                {
                    data: 'company',
                    name: 'company',
                    defaultContent: 'N/A'
                },
                {
                    data: 'assigned_user',
                    name: 'assigned_user',
                    defaultContent: 'N/A'
                },
                {
                    data: 'status',
                    name: 'status',
                    defaultContent: 'N/A'
                },
                {
                    data: 'source',
                    name: 'source',
                    defaultContent: 'N/A'
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,

                    render: function (id) {

                        return `
                            <!-- <a href="/leads/${id}"
                            class="btn btn-sm btn-primary view-btn"
                            target="_blank">
                                <i class="mdi mdi-eye"></i> View
                            </a> -->

                            <button
                                type="button"
                                class="btn btn-sm btn-primary edit-lead-btn"
                                data-id="${id}">
                                <i class="mdi mdi-pencil-box"></i> Edit
                            </button>

                            <a href="/leads/${id}/delete"
                            class="btn btn-sm btn-danger btn-delete">
                                <i class="mdi mdi-delete"></i> Delete
                            </a>
                        `;
                    }
                }
            ],

            language: {
                emptyTable: "No leads found",
                zeroRecords: "No matching leads found",
                processing: "Loading leads..."
            }
        });

        $('#createModal').on('hidden.bs.modal', function () {

            const $modal = $(this);
            const $form = $modal.find('#createLeadForm');

            if (!$form.length) {
                return;
            }

            // Clear all validation errors
            clearErrors($form);

            // Clear text inputs
            $form.find('input[type="text"]').val('');

            // Clear email
            $form.find('input[type="email"]').val('');

            // Clear phone
            $form.find('input[name="phone"]').val('');

            // Clear textarea
            $form.find('textarea').val('');

            // Reset select fields
            $form.find('select').each(function () {

                $(this).val('');

                $(this).trigger('change');

            });

            // Restore agency ID
            $form.find('input[name="agency_id"]').val('1');

            // Reset submit button
            const $btn = $form.find('#createSubmitBtn');

            $btn
                .prop('disabled', false)
                .text('Save');

        });


        $(document).on(
            'hidden.bs.modal',
            '.editLeadForm',
            function () {

                const $form = $(this);

                // Remove validation errors
                clearErrors($form);

                // Reset button
                const $btn = $form.find('[type="submit"]');

                $btn
                    .prop('disabled', false)
                    .text('Update');

                /*
                * Restore original values from Blade.
                *
                * reset() restores the values that existed when
                * the page was loaded, which is what we want here.
                */
                if ($form[0]) {
                    $form[0].reset();
                }

                // Clear validation again because reset()
                // does not remove validation classes/messages
                clearErrors($form);

            }
        );

    });

    $('#leadsTable').on(
        'click',
        '.edit-lead-btn',
        function () {

            let id =
                $(this).data('id');

            $('#editModal' + id).modal('show');

        }
    );

    function clearErrors($form) {

        if (!$form || !$form.length) {
            return;
        }

        // Remove validation classes
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.is-valid').removeClass('is-valid');

        // Remove all validation messages
        $form.find('.invalid-feedback').remove();
        $form.find('.validation-error').remove();

    }

    function showErrors($form, errors) {

        $.each(errors, function (field, messages) {

            const $input =
                $form
                    .find(`[name="${field}[]"], [name="${field}"]`)
                    .first();

            $input.addClass('is-invalid');

            $input
                .closest('.form-group')
                .append(
                    `<div class="invalid-feedback d-block">
                        ${messages[0]}
                    </div>`
                );

        });

    }

    function resetModalForm($modal) {

        const $form = $modal.find('form');

        if (!$form.length) {
            return;
        }

        // Clear validation errors
        clearErrors($form);

        // Reset form fields to their original HTML values
        $form[0].reset();

        // Remove any manually added error messages
        $form.find('.invalid-feedback').remove();

        // Remove invalid state
        $form.find('.is-invalid').removeClass('is-invalid');

        // Reset Select2 if you use it
        $form.find('select').each(function () {

            $(this).trigger('change');

        });

        // Reset submit button
        const $submitBtn = $form.find('[type="submit"]');

        if ($submitBtn.length) {

            $submitBtn
                .prop('disabled', false)
                .text(
                    $form.hasClass('editLeadForm')
                        ? 'Update'
                        : 'Save'
                );
        }
    }


    $('#createModal').on(
        'hidden.bs.modal',
        function () {

            const $modal = $(this);

            resetModalForm($modal);

        }
    );


    $(document).on(
        'hidden.bs.modal',
        '[id^="editModal"]',
        function () {

            const $modal = $(this);

            resetModalForm($modal);

        }
    );


    $(document).on(
        'show.bs.modal',
        '[id^="editModal"], #createModal',
        function () {

            const $form = $(this).find('form');

            if ($form.length) {

                clearErrors($form);

            }

        }
    );


    $(document).on(
        'submit',
        '#createLeadForm',
        function (e) {

            e.preventDefault();

            const $form =
                $(this);

            const $btn =
                $('#createSubmitBtn');

            clearErrors($form);

            $btn
                .prop('disabled', true)
                .text('Saving…');


            $.ajax({

                url:
                    '{{ route("leads.store") }}',

                method:
                    'POST',

                data:
                    new FormData($form[0]),

                processData:
                    false,

                contentType:
                    false,


                success: function (res) {

                    if (res.success) {

                        $('#createModal')
                            .modal('hide');

                        Swal.fire({

                            icon: 'success',

                            title: 'Created!',

                            text: res.success,

                            timer: 1500,

                            showConfirmButton: false

                        }).then(() => {

                            location.reload();

                        });

                    }

                },


                error: function (xhr) {

                    if (xhr.status === 422) {

                        showErrors(
                            $form,
                            xhr.responseJSON.errors
                        );

                    } else {

                        Swal.fire(
                            'Error',
                            'Something went wrong. Please try again.',
                            'error'
                        );

                    }

                },


                complete: function () {

                    $btn
                        .prop('disabled', false)
                        .text('Save');

                }

            });

        }
    );


    $(document).on(
        'submit',
        '.editLeadForm',
        function (e) {

            e.preventDefault();

            const $form =
                $(this);

            const url =
                $form.data('url');

            const $btn =
                $form.find('[type="submit"]');

            clearErrors($form);

            $btn
                .prop('disabled', true)
                .text('Updating…');


            $.ajax({

                url: url,

                method: 'POST',

                data:
                    new FormData($form[0]),

                processData: false,

                contentType: false,


                success: function (res) {

                    if (res.success) {

                        $form
                            .closest('.modal')
                            .modal('hide');

                        Swal.fire({

                            icon: 'success',

                            title: 'Updated!',

                            text: res.success,

                            timer: 1500,

                            showConfirmButton: false

                        }).then(() => {

                            location.reload();

                        });

                    }

                },


                error: function (xhr) {

                    if (xhr.status === 422) {

                        showErrors(
                            $form,
                            xhr.responseJSON.errors
                        );

                    } else {

                        Swal.fire(
                            'Error',
                            'Something went wrong. Please try again.',
                            'error'
                        );

                    }

                },


                complete: function () {

                    $btn
                        .prop('disabled', false)
                        .text('Update');

                }

            });

        }
    );

    $(document).on(
        'click',
        '.btn-delete',
        function (e) {

            e.preventDefault();

            const url =
                $(this).attr('href');


            Swal.fire({

                title:
                    'Are you sure?',

                text:
                    'This Lead will be permanently deleted!',

                icon:
                    'warning',

                showCancelButton:
                    true,

                confirmButtonColor:
                    '#d33',

                cancelButtonColor:
                    '#6c757d',

                confirmButtonText:
                    'Yes, delete it!',

                cancelButtonText:
                    'Cancel'

            }).then(function (result) {

                if (result.isConfirmed) {

                    $.ajax({

                        url: url,

                        method: 'GET',


                        success: function (res) {

                            if (res.success) {

                                Swal.fire({

                                    icon:
                                        'success',

                                    title:
                                        'Deleted!',

                                    text:
                                        res.success,

                                    timer:
                                        1500,

                                    showConfirmButton:
                                        false

                                }).then(function () {

                                    location.reload();

                                });

                            }

                        },


                        error: function () {

                            Swal.fire({

                                icon:
                                    'error',

                                title:
                                    'Error!',

                                text:
                                    'Something went wrong. Please try again.'

                            });

                        }

                    });

                }

            });

        }
    );

    $(document).on(
        'change',
        'input[type="file"]',
        function () {

            const id =
                this.id.replace(
                    'documentInput_',
                    'documentName_'
                );

            const nameField =
                document.getElementById(id);

            if (
                nameField &&
                this.files.length > 0
            ) {

                nameField.value =
                    this.files[0].name;

            }

        }
    );

    document
        .querySelectorAll('.lead-status-simple')
        .forEach(function (select) {

            function updateBadgeColor(el) {

                const status =
                    el.value;

                let colorClass;

                switch (status) {

                    case 'Not Started':
                        colorClass =
                            'badge-secondary';
                        break;

                    case 'In Progress':
                        colorClass =
                            'badge-warning';
                        break;

                    case 'Hold':
                        colorClass =
                            'badge-info';
                        break;

                    case 'Lost':
                        colorClass =
                            'badge-danger';
                        break;

                    case 'Complete':
                        colorClass =
                            'badge-success';
                        break;

                    default:
                        colorClass =
                            'badge-secondary';

                }

                el.className =
                    'form-control lead-status-simple ' +
                    colorClass;

            }


            updateBadgeColor(select);


            select.addEventListener(
                'change',
                function () {

                    updateBadgeColor(this);

                    const leadId =
                        this.dataset.leadId;

                    const status =
                        this.value;


                    fetch(
                        `/leads/${leadId}/status`,
                        {

                            method: 'POST',

                            headers: {

                                'Content-Type':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    '{{ csrf_token() }}'

                            },

                            body:
                                JSON.stringify({
                                    status: status
                                })

                        }
                    )

                    .then(res => res.json())

                    .then(data => {

                        Swal.fire({

                            icon:
                                'success',

                            title:
                                'Status Updated',

                            text:
                                data.success,

                            timer:
                                2000,

                            showConfirmButton:
                                false

                        });

                    })

                    .catch(err => {

                        Swal.fire({

                            icon:
                                'error',

                            title:
                                'Oops...',

                            text:
                                'Something went wrong!'

                        });

                        console.error(err);

                    });

                }
            );

        });


})();

</script>

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        let htmlContent = '';


        @if ($errors->any())

            htmlContent +=
                '<b>Validation Errors:</b><ul>';

            @foreach ($errors->all() as $error)

                htmlContent +=
                    `<li>{{ $error }}</li>`;

            @endforeach

            htmlContent +=
                '</ul><br>';

        @endif


        @if (session('error'))

            htmlContent +=
                `<b>Error:</b> {{ session('error') }}<br><br>`;

        @endif


        @if(session('success'))

            htmlContent +=
                `<b>{{ session('success') }}</b><br><br>`;

            const failedRows =
                @json(session('failedRows', []));


            if (failedRows.length > 0) {

                htmlContent +=
                    '<b>Failed Rows:</b><ul>';


                failedRows.forEach(function (fail) {

                    htmlContent +=
                        `<li>
                            Row ${fail.row_number || 'N/A'}:
                            ${fail.reason || 'Unknown error'}
                        </li>`;

                });


                htmlContent +=
                    '</ul>';

            }

        @endif


        if (htmlContent.length > 0) {

            Swal.fire({

                icon:
                    htmlContent.includes('Validation Errors') ||
                    htmlContent.includes('Error')
                        ? 'error'
                        : 'success',

                title:
                    'Upload Result',

                html:
                    htmlContent,

                width:
                    600

            });

        }

    }
);

</script>

@endsection
