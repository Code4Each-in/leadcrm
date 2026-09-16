@extends('layout')
@section('title', 'Roles')
@section('subtitle', 'Roles')
@section('content')
<style>
    /* ==========================================================
       Header
       ========================================================== */
    #rolesCard .roles-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 1px solid #eef0f3;
        margin-bottom: 22px;
        gap: 14px;
        flex-wrap: wrap;
    }

    #rolesCard .roles-eyebrow {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: #6c63ff;
        margin-bottom: 10px;
    }

    #rolesCard .roles-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    #rolesCard .roles-header h4 {
        font-weight: 700;
        font-size: 27px;
        color: #1a1f2b;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }

    #rolesCard .roles-header p {
        color: #8a92a3;
        font-size: 13.5px;
        margin: 0;
    }

    #rolesCard .btn-add-role {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        font-weight: 500;
        font-size: 14px;
        border-radius: 9px;
        white-space: nowrap;
        background: #6c63ff;
        border: none;
        box-shadow: 0 2px 6px rgba(108, 99, 255, 0.28);
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
    }

    #rolesCard .btn-add-role:hover {
        background: #5b52e8;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(108, 99, 255, 0.34);
        color: #fff;
    }

    /* ==========================================================
       Toolbar (search + page length)
       ========================================================== */
    #rolesCard .roles-toolbar {
        display: flex;
        align-items: center;
        gap: 16px;
        background: #fff;
        border: 1px solid #eef0f3;
        border-radius: 12px;
        padding: 10px 16px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    #rolesCard .toolbar-search {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1 1 220px;
        min-width: 160px;
    }

    #rolesCard .toolbar-search i {
        color: #a4aab5;
        font-size: 18px;
    }

    #rolesCard .toolbar-search input[type="search"] {
        border: none;
        outline: none;
        font-size: 13.5px;
        width: 100%;
        color: #384153;
        background: transparent;
    }

    #rolesCard .toolbar-length {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
        font-size: 12.5px;
        color: #a4aab5;
        flex-shrink: 0;
    }

    #rolesCard .toolbar-length select {
        border: 1px solid #e6e8ec;
        border-radius: 8px;
        background: #f8f9fb;
        font-size: 13px;
        color: #384153;
        padding: 5px 8px;
    }

    /* ==========================================================
       Table shell
       ========================================================== */
    #rolesCard .table-responsive {
        overflow-x: auto;
        scrollbar-width: none;
    }

    #rolesCard .table-responsive::-webkit-scrollbar {
        display: none;
    }

    #rolesTable {
        margin-bottom: 0;
        width: 100% !important;
    }

    #rolesTable thead th {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #1a1f2b;
        border-top: none;
        border-bottom: 2px solid #eef0f3;
        padding: 12px 10px;
        white-space: nowrap;
    }

    #rolesTable tbody td {
        padding: 11px 10px;
        font-size: 14px;
        color: #384153;
        vertical-align: middle;
    }

    #rolesTable.table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #fbfbfd;
    }

    #rolesTable tbody tr:hover {
        background-color: #f4f6fb;
    }

    /* ==========================================================
       Action buttons
       ========================================================== */
    #rolesTable .action-btns {
        display: flex;
        gap: 6px;
        flex-wrap: nowrap;
    }

    #rolesTable .action-btns .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        line-height: 1;
        border: none;
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
    }

    #rolesTable .action-btns .btn-icon i {
        font-size: 15px;
        margin: 0;
    }

    #rolesTable .action-btns .btn-edit {
        background-color: #eef0ff;
        color: #5b52e0;
    }

    #rolesTable .action-btns .btn-remove {
        background-color: #fdeaea;
        color: #d33a3a;
    }

    #rolesTable .action-btns .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(26, 31, 43, 0.14);
    }

    /* Loading state for an icon-only action button - the icon is
       swapped to a spinning mdi-loading glyph via JS, this just
       dims it and blocks further clicks while a request is in
       flight, matching the Users/Leads tables. */
    #rolesTable .action-btns .btn-icon.is-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Icon tooltip - dark bubble, positioned via JS off the
       trigger's own bounding rect (see script below). Same style
       used for the Users page's Product-info tooltip, reused here
       for the Edit/Delete action icons so tooltips look identical
       across the whole app. */
    .field-info-tooltip {
        position: fixed;
        max-width: 190px;
        padding: 7px 10px;
        border-radius: 7px;
        background: #1a1f2b;
        color: #fff;
        font-size: 11.5px;
        font-weight: 500;
        line-height: 1.4;
        text-align: center;
        white-space: normal;
        pointer-events: none;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
        z-index: 3000;
    }

    /* ==========================================================
       DataTables chrome (info + pagination - filter/length inputs
       are relocated into the custom toolbar via JS)
       ========================================================== */
    #rolesTable_wrapper .dataTables_info {
        font-size: 12.5px;
        color: #8a92a3;
        padding-top: 14px;
    }

    #rolesTable_wrapper .dataTables_paginate {
        padding-top: 10px;
    }

    #rolesTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        margin: 0 2px;
        border: 1px solid transparent !important;
        font-size: 13px;
    }

    #rolesTable_wrapper .dataTables_paginate .paginate_button.current {
        background: #6c63ff !important;
        color: #fff !important;
        border-color: #6c63ff !important;
    }

    #rolesTable_wrapper .dataTables_processing {
        background: rgba(255, 255, 255, 0.85);
        font-size: 13px;
        color: #6c63ff;
        font-weight: 500;
    }

    #rolesTable_wrapper td.dataTables_empty {
        padding: 48px 0;
        color: #8a92a3;
        font-size: 13.5px;
    }

    /* ==========================================================
       Modal - restyled to match the app instead of the default
       Bootstrap look
       ========================================================== */
    .required-label::after {
        content: ' *';
        color: red;
    }

    /*
       Bootstrap only ever gives the dialog a top margin, so on a
       short form like this one it sits high up with dead space
       below it. Centering it properly (both axes, at every screen
       size) is the standard flex trick: zero out the modal's own
       padding, then let the dialog grow to the modal's full height
       and center its content within that.
    */
    .modal {
        padding: 0 !important;
    }

    .modal-dialog {
        max-width: 460px !important;
        width: calc(100% - 32px);
        min-height: calc(100% - 48px);
        margin: 24px auto !important;
        display: flex;
        align-items: center;
    }

    .modal-dialog > form {
        width: 100%;
    }

    .modal-content {
        border-radius: 14px !important;
        border: none !important;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
        width: 100%;
        max-height: calc(100vh - 48px);
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        background: #fff;
        padding: 22px 24px 18px !important;
        border-bottom: 1px solid #eef0f3 !important;
        align-items: center;
    }

    .modal-header h5,
    .modal-header .modal-title {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #1a1f2b;
    }

    .modal-header .close {
        background: none;
        border: none;
        font-size: 22px;
        line-height: 1;
        color: #a4aab5;
        cursor: pointer;
        /* padding: 4px; */
        opacity: 1;
        text-shadow: none;
    }

    .modal-header .close:hover {
        color: #384153;
    }

    .modal-body {
        padding: 22px 24px !important;
        overflow-y: auto !important;
        min-height: 0;
    }

    .modal-body .form-group label {
        font-size: 13.5px;
        font-weight: 500;
        color: #384153;
        margin-bottom: 6px;
    }

    .modal-body .form-control {
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13.5px;
    }

    .modal-body .form-control:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
    }

    .modal-footer {
        padding: 16px 24px 22px !important;
        border-top: none !important;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .modal-footer .btn-primary {
        background: #6c63ff;
        border-color: #6c63ff;
        border-radius: 9px;
        padding: 9px 22px;
        font-size: 13.5px;
        font-weight: 500;
        margin: 0;
    }

    .modal-footer .btn-primary:hover {
        background: #5b52e8;
        border-color: #5b52e8;
    }

    .modal-footer .btn-secondary {
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 13.5px;
        font-weight: 500;
        border: 1px solid #e2e5eb;
        background: #fff;
        color: #384153;
        margin: 0;
    }

    .modal-footer .btn-secondary:hover {
        background: #f4f5f7;
        color: #384153;
    }

    .modal-footer .btn-primary:disabled {
        opacity: 0.75;
        cursor: not-allowed;
    }

    /* ==========================================================
       SweetAlert2 - delete confirmation, same soft-alert style
       used on the Leads page, reused verbatim here.
       ========================================================== */
    .swal-leads-popup {
        border-radius: 20px !important;
        padding: 32px 28px 28px !important;
    }

    .swal-delete-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: #fdeaea;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: #d33a3a;
    }

    .swal-delete-title {
        font-size: 19px !important;
        color: #1a1f2b !important;
        font-weight: 700 !important;
        margin: 0 0 8px !important;
    }

    .swal-delete-text {
        font-size: 13.5px !important;
        color: #8a92a3 !important;
        line-height: 1.5;
        margin: 0 0 6px !important;
    }

    .swal-delete-text strong {
        color: #384153;
    }

    .swal-leads-popup.swal2-show {
        animation: swalLeadsPopIn 0.22s ease-out;
    }

    @keyframes swalLeadsPopIn {
        from {
            opacity: 0;
            transform: scale(0.92);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .swal-leads-popup .swal2-html-container {
        margin: 0 !important;
    }

    .swal-leads-popup .swal2-actions {
        margin-top: 22px !important;
        gap: 10px;
    }

    .swal-btn-danger {
        background: #d33a3a !important;
        color: #fff !important;
        font-size: 13.5px !important;
        font-weight: 500 !important;
        padding: 9px 20px !important;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    .swal-btn-danger:hover {
        background: #b92e2e !important;
    }

    .swal-btn-cancel {
        background: #fff !important;
        color: #6c7280 !important;
        border: 1px solid #e2e5eb !important;
        font-size: 13.5px !important;
        font-weight: 500 !important;
        padding: 9px 20px !important;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    .swal-btn-cancel:hover {
        background: #f4f5f7 !important;
    }

    /* ==========================================================
       Mobile
       ========================================================== */
    @media (max-width: 768px) {

        #rolesCard .roles-header {
            flex-direction: column;
        }

        #rolesCard .btn-add-role {
            width: 100%;
            justify-content: center;
        }

        #rolesCard .roles-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        #rolesCard .toolbar-search {
            flex-basis: auto;
            width: 100%;
        }

        #rolesCard .toolbar-length {
            margin-left: 0;
            width: 100%;
            justify-content: space-between;
        }

        #rolesTable_wrapper .dataTables_paginate,
        #rolesTable_wrapper .dataTables_info {
            text-align: center;
        }

        /* Card layout for the roles table on mobile - simple, since
           there are only two data columns besides actions. */
        #rolesTable thead {
            display: none;
        }

        #rolesTable,
        #rolesTable tbody {
            display: block;
            width: 100%;
        }

        #rolesTable tbody tr {
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid #eef0f3;
            border-radius: 12px;
            margin-bottom: 10px;
            padding: 12px 14px;
        }

        #rolesTable tbody td {
            border: none !important;
            padding: 0 !important;
        }

        #rolesTable tbody td.dataTables_empty {
            display: block !important;
            text-align: center;
            color: #8a92a3;
            font-size: 13px;
            padding: 6px 0 !important;
        }

        #rolesTable tbody td.col-heading {
            order: 1;
            font-size: 14.5px;
            font-weight: 700;
            color: #1a1f2b;
            padding-bottom: 8px !important;
        }

        #rolesTable tbody td.col-meta {
            order: 2;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0 0 !important;
            margin-top: 8px;
            border-top: 1px solid #f1f2f6 !important;
            font-size: 13px;
            color: #384153;
        }

        #rolesTable tbody td.col-meta::before {
            content: 'Created At';
            font-weight: 600;
            color: #8a92a3;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            flex-shrink: 0;
        }

        #rolesTable tbody td.col-action {
            order: 3;
            display: flex;
            gap: 8px;
            padding-top: 10px !important;
        }
    }

    @media (max-width: 576px) {

        /* Same centered card as desktop, just narrower - a full
           height/edge-to-edge sheet made sense for a longer form,
           but leaves an awkward wall of empty space below a single
           field, which is what looked broken here. */
        .modal-dialog {
            width: calc(100% - 24px);
            min-height: calc(100% - 32px);
            margin: 16px auto !important;
        }

        .modal-content {
            max-height: calc(100vh - 32px);
        }

        .modal-header {
            padding: 18px 18px 14px !important;
        }

        .modal-header h5,
        .modal-header .modal-title {
            font-size: 16px;
        }

        .modal-body {
            padding: 18px !important;
        }

        .modal-footer {
            padding: 14px 18px 18px !important;
            flex-direction: column;
        }

        .modal-footer .btn {
            width: 100%;
            min-height: 44px;
            margin-bottom: 8px;
        }

        .modal-footer .btn:last-child {
            margin-bottom: 0;
        }
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card" id="rolesCard">
            <div class="card-body">

                {{-- Header --}}
                <div class="roles-header">

                    <div>
                        <div class="roles-eyebrow">
                            Role Management
                        </div>
                        <h4 class="card-title mb-1">
                            Roles
                        </h4>
                        <p>
                            Manage the roles available across the system.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="btn btn-primary btn-add-role"
                        data-toggle="modal"
                        data-target="#createModal"
                    >
                        <i class="mdi mdi-plus"></i>
                        Add Role
                    </button>

                </div>

                {{-- Toolbar: search + page length --}}
                <div class="roles-toolbar">

                    <div class="toolbar-search" id="toolbarSearchSlot">
                        <i class="mdi mdi-magnify"></i>
                        {{-- native DataTables search input is moved in here via JS --}}
                    </div>

                    <div class="toolbar-length" id="toolbarLengthSlot">
                        Rows
                        {{-- native DataTables length select is moved in here via JS --}}
                    </div>

                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="rolesTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Created At</th>
                                <th width="200">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createRoleForm" method="POST" action="{{ route('roles.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Role</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required-label">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter role name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="createRoleSaveBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!--
    Edit Modal - a single shared instance instead of one per role.
    Its form action and field are filled in via JS (from the
    DataTable row's own data) right before it's shown, so editing a
    role - or a role just created - never depends on the page having
    been rendered/reloaded since. That's what lets both Add and Edit
    below refresh only the table instead of the whole page.
-->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editRoleForm" method="POST" action="">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Role</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required-label">Role Name</label>
                        <input type="text" name="name" class="form-control" value="" data-original="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="editRoleSaveBtn" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function waitForJQuery(callback) {
    if (typeof $ !== 'undefined') {
        callback();
    } else {
        setTimeout(function () {
            waitForJQuery(callback);
        }, 50);
    }
}

waitForJQuery(function () {

    $(document).ready(function () {

        /*
        * Icon tooltip - same [data-tooltip] driven, position:fixed
        * bubble used on the Users page (originally built for its
        * Products field-info icon), reused here for the Edit/
        * Delete action icons so every tooltip in the app looks and
        * behaves identically. Fixed positioning keeps it fully
        * visible even though the trigger can sit inside a scrolling
        * container, and :focus covers tap-to-show on touch devices
        * for focusable triggers (buttons/links).
        */
        let $fieldTooltip = null;

        function showFieldTooltip(icon) {

            hideFieldTooltip();

            const text = icon.getAttribute('data-tooltip');

            if (!text) {
                return;
            }

            $fieldTooltip = $('<div class="field-info-tooltip"></div>')
                .text(text)
                .appendTo('body');

            const iconRect = icon.getBoundingClientRect();
            const tipEl = $fieldTooltip[0];
            const tipRect = tipEl.getBoundingClientRect();

            let top = iconRect.top - tipRect.height - 8;

            if (top < 8) {
                top = iconRect.bottom + 8;
            }

            let left = iconRect.left + (iconRect.width / 2) - (tipRect.width / 2);
            left = Math.max(8, Math.min(left, window.innerWidth - tipRect.width - 8));

            tipEl.style.top = top + 'px';
            tipEl.style.left = left + 'px';
        }

        function hideFieldTooltip() {

            if ($fieldTooltip) {
                $fieldTooltip.remove();
                $fieldTooltip = null;
            }
        }

        $(document).on('mouseenter focus', '[data-tooltip]', function () {
            showFieldTooltip(this);
        });

        $(document).on('mouseleave blur', '[data-tooltip]', function () {
            hideFieldTooltip();
        });

        $(document).on('hide.bs.modal', hideFieldTooltip);
        $(window).on('resize', hideFieldTooltip);
        document.addEventListener('scroll', hideFieldTooltip, true);

        /*
        * Shared button loader - swaps a submit/action button's own
        * label for a spinning mdi-loading glyph (mdi-spin ships
        * with the app's icon font already) and disables it, so a
        * slow request can't be fired twice from a double click.
        */
        function setBtnLoading($btn, loading, loadingText) {

            if (!$btn || !$btn.length) {
                return;
            }

            if (loading) {

                if ($btn.data('original-html') === undefined) {
                    $btn.data('original-html', $btn.html());
                }

                $btn.prop('disabled', true).addClass('is-loading');
                $btn.html(
                    '<i class="mdi mdi-loading mdi-spin"></i> ' +
                    (loadingText || 'Please wait...')
                );

            } else {

                $btn.prop('disabled', false).removeClass('is-loading');

                if ($btn.data('original-html') !== undefined) {
                    $btn.html($btn.data('original-html'));
                }
            }
        }

        const rolesTable = $('#rolesTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ordering: true,
            searching: true,

            ajax: {
                url: "{{ route('roles.index') }}"
            },

            order: [[1, 'desc']],

            columns: [
                {
                    data: 'name',
                    createdCell: function (td) {
                        $(td).addClass('col-heading');
                    }
                },
                {
                    data: 'created_at',
                    render: function (data) {
                        return data
                            ? new Date(data).toLocaleString('en-GB', {
                                day: '2-digit', month: '2-digit', year: 'numeric'
                            })
                            : '';
                    },
                    createdCell: function (td) {
                        $(td).addClass('col-meta');
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (id, type, row) {
                        return `
                        <div class="action-btns">
                            <button class="btn btn-sm btn-icon btn-edit editBtn"
                                data-id="${id}"
                                data-tooltip="Edit">
                                <i class="mdi mdi-pencil-box"></i>
                            </button>

                            <button
                                type="button"
                                class="btn btn-sm btn-icon btn-remove btn-delete"
                                data-id="${id}"
                                data-tooltip="Delete">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                        `;
                    },
                    createdCell: function (td) {
                        $(td).addClass('col-action');
                    }
                }
            ],

            language: {
                emptyTable: "No roles found",
                zeroRecords: "No matching roles found"
            },

            /*
            * Relocates the DataTables-generated search input and
            * page-length select into the custom toolbar slots, same
            * approach used on the Leads page.
            */
            initComplete: function () {

                $('#rolesTable_filter input')
                    .attr('placeholder', 'Search roles...')
                    .appendTo('#toolbarSearchSlot');

                $('#rolesTable_filter').remove();

                $('#rolesTable_length select')
                    .appendTo('#toolbarLengthSlot');

                $('#rolesTable_length').remove();

            },

            drawCallback: function () {

                const hasRows = this.api().page.info().recordsDisplay > 0;

                $('#rolesTable_wrapper .dataTables_paginate')
                    .toggle(hasRows);

            }
        });

        // ":id" is swapped out below for the real id, since
        // route('roles.update', ...) needs a real (existing) role
        // to resolve against at Blade-compile time.
        const editRoleUrlTemplate = "{{ route('roles.update', ':id') }}";

        $(document).on('click', '.editBtn', function () {

            const id = $(this).data('id');
            const rowData = rolesTable.row($(this).closest('tr')).data();

            if (!rowData) {
                return;
            }

            const $input = $('#editRoleForm input[name="name"]');

            $('#editRoleForm').attr(
                'action',
                editRoleUrlTemplate.replace(':id', id)
            );

            $input.val(rowData.name).attr('data-original', rowData.name);

            $('#editModal').modal('show');

        });

        function clearErrors($modal) {

            $modal.find('.is-invalid').removeClass('is-invalid');
            $modal.find('.invalid-feedback').remove();

        }

        function resetModal($modal) {

            if (!$modal || !$modal.length) {
                return;
            }

            const $form = $modal.find('form');

            clearErrors($modal);

            // CREATE MODAL
            if ($modal.attr('id') === 'createModal') {

                $form.find('input[name="name"]').val('');

            }

            // EDIT MODAL
            else {

                $form.find('input[name="name"][data-original]')
                    .each(function () {

                        $(this).val(
                            $(this).attr('data-original')
                        );

                    });

            }

        }

        $(document).on(
            'click',
            '[data-dismiss="modal"]',
            function () {

                resetModal(
                    $(this).closest('.modal')
                );

            }
        );


        $(document).on(
            'hidden.bs.modal',
            '.modal',
            function () {

                resetModal($(this));

            }
        );

        function showErrors(form, errors) {

            const $form = $(form);
            const $modal = $form.closest('.modal');

            clearErrors($modal);

            $.each(errors, function (field, messages) {

                const $input = $form
                    .find('[name="' + field + '"]')
                    .first();

                if (!$input.length) {
                    return;
                }

                $input.addClass('is-invalid');

                $input
                    .closest('.form-group')
                    .append(
                        '<div class="invalid-feedback d-block">' +
                        messages[0] +
                        '</div>'
                    );

            });

        }

        /*
        * Soft, non-blocking toast (auto-dismisses) instead of a
        * blocking modal - matches the notification style used
        * throughout the Leads page and the global flash-message
        * toast in layout.blade.php.
        */
        function showToast(icon, message) {

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: message,
                showConfirmButton: false,
                timer: icon === 'success' ? 2200 : 2800,
                timerProgressBar: true
            });

        }

        $('#createRoleForm').on('submit', function (e) {

            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const $saveBtn = $('#createRoleSaveBtn');

            setBtnLoading($saveBtn, true, 'Saving...');

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function (res) {

                    $('#createModal').modal('hide');

                    rolesTable.ajax.reload(null, false);

                    showToast('success', res.success || 'Role created successfully.');

                },

                error: function (xhr) {

                    if (xhr.status === 422) {

                        showErrors(
                            form,
                            xhr.responseJSON.errors
                        );

                    } else {

                        showToast('error', 'Something went wrong. Please try again.');

                    }

                },

                complete: function () {

                    setBtnLoading($saveBtn, false);

                }

            });

        });


        $(document).on(
            'submit',
            '#editRoleForm',
            function (e) {

                e.preventDefault();

                const form = this;
                const $modal = $(form).closest('.modal');
                const formData = new FormData(form);
                const $saveBtn = $('#editRoleSaveBtn');

                setBtnLoading($saveBtn, true, 'Saving...');

                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function (res) {

                        $modal.modal('hide');

                        rolesTable.ajax.reload(null, false);

                        showToast('success', res.success || 'Role updated successfully.');

                    },

                    error: function (xhr) {

                        if (xhr.status === 422) {

                            showErrors(
                                form,
                                xhr.responseJSON.errors
                            );

                        } else {

                            showToast('error', 'Something went wrong. Please try again.');

                        }

                    },

                    complete: function () {

                        setBtnLoading($saveBtn, false);

                    }

                });

            }
        );

        /*
        * DELETE - SweetAlert soft-alert confirm, same markup/style
        * as the Leads page, + AJAX GET request (matches the
        * roles.delete route).
        */
        $(document).on(
            'click',
            '.btn-delete',
            function () {

                const $btn = $(this);

                // Already deleting this row - ignore the extra
                // click instead of firing a second request.
                if ($btn.hasClass('is-loading')) {
                    return;
                }

                const id = $btn.data('id');
                const url = `/roles/delete/${id}`;

                // Pull the row's own data (not an inline HTML
                // attribute) so an unusual role name - one with a
                // quote character, say - can't break out of markup.
                const rowData = rolesTable.row($btn.closest('tr')).data();

                const roleLabel = (rowData && rowData.name) || 'This role';

                // Minimal HTML-escape since roleLabel is interpolated
                // straight into the dialog's markup below.
                const escapedRoleLabel = $('<div>').text(roleLabel).html();

                Swal.fire({
                    html: `
                        <div class="swal-delete-icon">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </div>
                        <h2 class="swal-delete-title">Delete this role?</h2>
                        <p class="swal-delete-text">
                            <strong>${escapedRoleLabel}</strong> will be permanently
                            removed. This action can't be undone.
                        </p>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-leads-popup',
                        confirmButton: 'swal-btn-danger',
                        cancelButton: 'swal-btn-cancel'
                    }
                }).then(function (result) {

                    if (!result.isConfirmed) {
                        return;
                    }

                    $btn.addClass('is-loading');
                    $btn.find('i').attr('class', 'mdi mdi-loading mdi-spin');

                    $.ajax({

                        url: url,
                        method: 'GET',

                        success: function (res) {

                            rolesTable.ajax.reload(null, false);

                            showToast('success', res.success || 'Role deleted successfully.');

                        },

                        error: function () {

                            showToast('error', 'Something went wrong while deleting. Please try again.');

                        },

                        complete: function () {

                            $btn.removeClass('is-loading');
                            $btn.find('i').attr('class', 'mdi mdi-delete');

                        }

                    });

                });

            }
        );

    });

});
</script>


@endsection
