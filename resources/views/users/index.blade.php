@extends('layout')
@section('title', ' Users')
@section('subtitle', 'Users')
@section('content')
<style>
    /* ==========================================================
       Header - same pattern as Leads/Roles
       ========================================================== */
    #usersCard .users-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 1px solid #eef0f3;
        margin-bottom: 22px;
        gap: 14px;
        flex-wrap: wrap;
    }

    #usersCard .users-eyebrow {
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

    #usersCard .users-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    #usersCard .users-header h4 {
        font-weight: 700;
        font-size: 27px;
        color: #1a1f2b;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }

    #usersCard .users-header p {
        color: #8a92a3;
        font-size: 13.5px;
        margin: 0;
    }

    #usersCard .btn-add-user {
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

    #usersCard .btn-add-user:hover {
        background: #5b52e8;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(108, 99, 255, 0.34);
        color: #fff;
    }

    /* ==========================================================
       Toolbar (search + filters + reset + page length) - same
       structure/classes as the Leads/Roles toolbar
       ========================================================== */
    #usersCard .users-toolbar {
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

    #usersCard .toolbar-search {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1 1 220px;
        min-width: 160px;
    }

    #usersCard .toolbar-search i {
        color: #a4aab5;
        font-size: 18px;
    }

    #usersCard .toolbar-search input[type="search"] {
        border: none;
        outline: none;
        font-size: 13.5px;
        width: 100%;
        color: #384153;
        background: transparent;
    }

    #usersCard .toolbar-divider {
        width: 1px;
        height: 24px;
        background: #eef0f3;
        flex-shrink: 0;
    }

    #usersCard .toolbar-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        background: #f4f5f8;
        border: 1px solid #e9ebef;
        border-radius: 10px;
        padding: 6px 10px 6px 12px;
    }

    #usersCard .toolbar-filter label {
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5164;
        white-space: nowrap;
        margin: 0px;
    }

    #usersCard .toolbar-filter select {
        border: 1px solid #dcdfe6;
        border-radius: 7px;
        background: #fff;
        font-size: 13px;
        font-weight: 500;
        color: #1a1f2b;
        padding: 6px 26px 6px 10px;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 10 6'%3E%3Cpath fill='%236c7280' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 9px center;
        background-size: 9px;
    }

    #usersCard .toolbar-filter select:hover {
        border-color: #c7cbd4;
    }

    #usersCard .toolbar-filter select:focus {
        outline: none;
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
    }

    #usersCard .btn-reset-filters {
        border: 1px solid #e2e5eb;
        background: #fff;
        color: #6c7280;
        font-size: 13px;
        font-weight: 500;
        padding: 7px 14px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
        transition: background 0.12s ease, color 0.12s ease;
    }

    #usersCard .btn-reset-filters:hover {
        background: #f4f5f7;
        color: #384153;
    }

    #usersCard .btn-reset-filters i {
        font-size: 15px;
    }

    #usersCard .toolbar-length {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
        font-size: 12.5px;
        color: #a4aab5;
        flex-shrink: 0;
    }

    #usersCard .toolbar-length select {
        border: 1px solid #e6e8ec;
        border-radius: 8px;
        background: #f8f9fb;
        font-size: 13px;
        color: #384153;
        padding: 5px 8px;
    }

    /* ==========================================================
       Table shell - same look as #applicationsTable/#rolesTable
       ========================================================== */
    #usersCard .table-responsive {
        overflow-x: auto;
        scrollbar-width: none;
    }

    #usersCard .table-responsive::-webkit-scrollbar {
        display: none;
    }

    #usersTable {
        margin-bottom: 0;
        width: 100% !important;
    }

    #usersTable thead th {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #1a1f2b;
        border-top: none;
        border-bottom: 2px solid #eef0f3;
        padding: 12px 0px;
        white-space: nowrap;
    }

    #usersTable tbody td {
        padding: 11px 10px;
        font-size: 13px;
        color: #384153;
        vertical-align: middle;
    }

    #usersTable.table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #fbfbfd;
    }

    #usersTable tbody tr:hover {
        background-color: #f4f6fb;
    }

    /* ==========================================================
       Status/device/2FA toggles - the same hand-built pill switch
       used for the Status column on the Leads table (a real
       <input type="checkbox"> hidden behind a styled track), not
       Bootstrap's default custom-switch look. Page-global (not
       scoped to #usersTable) since the Add User modal's Device
       Access section reuses this exact component too.
       ========================================================== */
    .status-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;
    }

    .status-toggle input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .status-toggle .toggle-track {
        position: relative;
        width: 36px;
        height: 20px;
        border-radius: 20px;
        background: #d7dae1;
        flex-shrink: 0;
        transition: background 0.15s ease;
    }

    .status-toggle .toggle-track::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        transition: transform 0.15s ease;
    }

    /* Main Active/Inactive toggle - green when on, same "positive
       state" color Leads uses for its Published status. Excludes
       --sm explicitly so specificity can't fight the purple rule
       further down for the smaller secondary toggles. */
    #usersTable .status-toggle:not(.status-toggle--sm) input:checked + .toggle-track {
        background: #34c777;
    }

    #usersTable .status-toggle:not(.status-toggle--sm) input:checked + .toggle-track::after {
        transform: translateX(16px);
    }

    .status-toggle input:focus-visible + .toggle-track {
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.28);
    }

    .status-toggle .toggle-label {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.2px;
        color: #8a92a3;
        white-space: nowrap;
        min-width: 52px;
    }

    #usersTable .status-toggle input:checked ~ .toggle-label {
        color: #1a7a4c;
    }

    .status-toggle.is-loading {
        opacity: 0.55;
        pointer-events: none;
    }

    /* Secondary toggles (2FA / device access) - smaller, purple
       accent instead of green since these are feature switches,
       not the primary account status. Used both in the table and
       in the Add User modal's Device Access section, where the
       purple "on" color is the only one that applies. */
    .status-toggle--sm .toggle-track {
        width: 30px;
        height: 18px;
    }

    .status-toggle--sm .toggle-track::after {
        width: 14px;
        height: 14px;
    }

    .status-toggle--sm input:checked + .toggle-track::after {
        transform: translateX(12px);
    }

    .status-toggle--sm input:checked + .toggle-track {
        background: #6c63ff;
    }

    .status-toggle.is-disabled {
        cursor: not-allowed;
    }

    .status-toggle.is-disabled .toggle-track {
        opacity: 0.6;
    }

    /* ==========================================================
       Action buttons - circular tinted icons, same as Leads/Roles
       ========================================================== */
    #usersTable .action-btns {
        display: flex;
        gap: 6px;
        flex-wrap: nowrap;
    }

    #usersTable .action-btns .btn-icon {
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

    #usersTable .action-btns .btn-icon i {
        font-size: 15px;
        margin: 0;
    }

    #usersTable .action-btns .btn-edit {
        background-color: #eef0ff;
        color: #5b52e0;
    }

    #usersTable .action-btns .btn-logs {
        background-color: #eaf2ff;
        color: #2264d1;
    }

    #usersTable .action-btns .btn-remove {
        background-color: #fdeaea;
        color: #d33a3a;
    }

    #usersTable .action-btns .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(26, 31, 43, 0.14);
    }

    /* Loading state for an icon-only action button/link - the icon
       itself is swapped to a spinning mdi-loading glyph via JS (see
       setBtnLoading below), this just dims it and blocks further
       clicks while a request is in flight, matching the dimmed
       .status-toggle.is-loading treatment used for the toggles. */
    #usersTable .action-btns .btn-icon.is-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Loading state for the modal Save/Update buttons - text is
       swapped for a spinner + "Saving..." via JS. */
    .modal-footer .btn-primary:disabled {
        opacity: 0.75;
        cursor: not-allowed;
    }

    /* ==========================================================
       DataTables chrome
       ========================================================== */
    #usersTable_wrapper .dataTables_info {
        font-size: 12.5px;
        color: #8a92a3;
        padding-top: 14px;
    }

    #usersTable_wrapper .dataTables_paginate {
        padding-top: 10px;
    }

    #usersTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        margin: 0 2px;
        border: 1px solid transparent !important;
        font-size: 13px;
    }

    #usersTable_wrapper .dataTables_paginate .paginate_button.current {
        background: #6c63ff !important;
        color: #fff !important;
        border-color: #6c63ff !important;
    }

    #usersTable_wrapper .dataTables_processing {
        background: rgba(255, 255, 255, 0.85);
        font-size: 13px;
        color: #6c63ff;
        font-weight: 500;
    }

    #usersTable_wrapper td.dataTables_empty {
        padding: 48px 0;
        color: #8a92a3;
        font-size: 13.5px;
    }

    .dataTables_wrapper {
        width: 100% !important;
    }

    @media (max-width: 768px) {
        table.dataTable td {
            white-space: normal !important;
        }
    }

    /* ==========================================================
       Modal - same centered-card treatment as Roles, just wider
       since the User form has many more fields
       ========================================================== */
    .required-label::after {
        content: ' *';
        color: red;
    }

    .modal {
        padding: 0 !important;
    }

    .modal-dialog {
        max-width: 620px !important;
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

    .modal-body select.form-control {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 10 6'%3E%3Cpath fill='%236c7280' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 9px;
    }

    .modal-body small.form-text {
        font-size: 12px;
        color: #a4aab5;
        margin-top: 4px;
    }

    /* Two-column layout for shorter, related fields (Name/Email,
       Password/Role, etc.) - collapses to one column on mobile via
       the ≤576px rule further down. Products/Address/Profile stay
       full-width since they don't pair naturally. */
    .modal-body .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 16px;
    }

    /* Device Access - each device gets its own row (icon badge +
       label + the same pill switch used everywhere else on the
       page), instead of a segmented Enabled/Disabled control. */
    .device-access-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .device-access-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        background: #f8f9fb;
        border: 1px solid #eef0f3;
        border-radius: 10px;
    }

    .device-access-option-info {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13.5px;
        font-weight: 500;
        color: #384153;
    }

    .device-access-option-info i {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #eef0ff;
        color: #6c63ff;
        font-size: 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Product multi-select - same selectable chip/button UI as the
       single-select Product field on the Leads Add Lead form
       (.yes-no-group/.yes-no-option/.yes-no-button there), adapted
       to checkboxes so more than one can be active at once. */
    .product-select-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .product-select-option {
        position: relative;
        margin: 0;
        padding: 0;
        cursor: pointer;
    }

    .product-select-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .product-select-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 95px;
        height: 42px;
        padding: 0 16px;
        background: #fbfbfd;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        color: #6c7280;
        font-size: 13.5px;
        font-weight: 500;
        text-align: center;
        transition: border-color 0.15s ease, background-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
    }

    .product-select-option:hover .product-select-chip {
        border-color: #6c63ff;
        background: rgba(108, 99, 255, 0.04);
        color: #6c63ff;
    }

    .product-select-option input:checked + .product-select-chip {
        background: rgba(108, 99, 255, 0.09);
        border-color: #6c63ff;
        color: #6c63ff;
        box-shadow: 0 3px 10px rgba(108, 99, 255, 0.12);
    }

    .product-select-option input:focus-visible + .product-select-chip {
        outline: 2px solid rgba(108, 99, 255, 0.35);
        outline-offset: 2px;
    }

    @media (max-width: 575px) {
        .product-select-group {
            /* flex-direction: column; */
            align-items: stretch;
        }
    }

    /* Product selection - required-state styling, shown only when
       actually invalid instead of a permanent hint line underneath
       the buttons. */
    .product-select-group.is-invalid {
        padding: 8px;
        /* margin: -8px; */
        border: 1px solid #d33a3a;
        border-radius: 10px;
        background: rgba(211, 58, 58, 0.03);
    }

    /* Product selection error - a small bordered notice with an
       icon (matches the destructive/red color used elsewhere on
       this page) instead of a plain Bootstrap .invalid-feedback
       line sitting under the chip row. */
    .product-select-group + .invalid-feedback {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 10px;
        padding: 8px 12px;
        background: rgba(211, 58, 58, 0.06);
        border: 1px solid rgba(211, 58, 58, 0.25);
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 500;
        color: #d33a3a;
    }

    .product-select-group + .invalid-feedback i {
        font-size: 15px;
        flex-shrink: 0;
    }

    /* Small info icon next to a field label, e.g. clarifying that
       Products supports more than one selection. The tooltip text
       itself is rendered by JS as a position:fixed element appended
       to <body> (see the field-info-icon script below) rather than
       an absolutely-positioned ::after, so it can never be clipped
       by the modal body's overflow-y:auto and always sits above the
       modal's own z-index. */
    .field-info-icon {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        margin-left: 4px;
        border-radius: 50%;
        background: #eef0ff;
        color: #6c63ff;
        font-size: 12px;
        cursor: help;
        vertical-align: middle;
    }

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

    /* File upload - the same joined filename+Browse control used
       on the Leads activity/document upload field, instead of a
       plain Bootstrap input-group. */
    .file-upload-field {
        display: flex;
        align-items: stretch;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color 0.12s ease, box-shadow 0.12s ease;
    }

    .file-upload-field:hover,
    .file-upload-field:focus-within {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.12);
    }

    .file-upload-field .file-upload-default {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        overflow: hidden;
    }

    .file-upload-field .file-upload-info {
        flex: 1 1 auto;
        min-width: 0;
        border: none !important;
        border-radius: 0 !important;
        background: #fff;
        color: #8a92a3;
        font-size: 13.5px;
        padding: 10px 12px;
        cursor: pointer;
    }

    .file-upload-field .file-upload-info:focus {
        outline: none;
        box-shadow: none !important;
    }

    .file-upload-field .file-upload-info.has-file {
        color: #384153;
    }

    .file-upload-field .file-upload-browse {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        border-left: 1px solid #e2e5eb;
        background: #f4f5f8;
        color: #5b52e0;
        font-size: 13px;
        font-weight: 500;
        padding: 10px 16px;
        white-space: nowrap;
        transition: background 0.12s ease;
    }

    .file-upload-field .file-upload-browse:hover {
        background: #eef0ff;
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

    .modal-footer .btn-secondary,
    .modal-footer .btn-light {
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 13.5px;
        font-weight: 500;
        border: 1px solid #e2e5eb;
        background: #fff;
        color: #384153;
        margin: 0;
    }

    .modal-footer .btn-secondary:hover,
    .modal-footer .btn-light:hover {
        background: #f4f5f7;
        color: #384153;
    }

    @media (max-width: 576px) {

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

        .modal-body .form-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ==========================================================
       SweetAlert2 - the exact soft-alert shell used on Leads/Roles,
       reused verbatim. Two color variants: red for the destructive
       Delete action, purple for the non-destructive Status/2FA/
       Device confirmations - still one shared design, not five.
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

    .swal-delete-icon.swal-icon-neutral {
        background: #eef0ff;
        color: #5b52e0;
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

    .swal-btn-primary {
        background: #6c63ff !important;
        color: #fff !important;
        font-size: 13.5px !important;
        font-weight: 500 !important;
        padding: 9px 20px !important;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    .swal-btn-primary:hover {
        background: #5b52e8 !important;
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

    .user-address {
        width: 180px;
        max-width: 180px;
        white-space: normal;
        word-break: break-word;
        line-height: 1.4;
        font-size: 13px;
        color: #384153;
        overflow: hidden;
    }

    .login-access-wrapper {
        min-width: 135px;
        width: 135px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .device-access-row {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
        min-height: 22px;
    }

    .device-label {
        font-size: 12px;
        font-weight: 500;
        color: #384153;
        margin-bottom: 8px;
    }

    /* expand/collapse chevron next to the user's name - hidden on
       desktop where every column is already visible, shown once
       the mobile card layout kicks in below */
    .expand-chevron {
        display: none;
        color: #8a92a3;
        font-size: 19px;
        transition: transform 0.2s ease;
    }

    /* Name, then Active/Inactive underneath - unchanged desktop
       layout. The mobile media query further down switches this to
       a single row (name left, status right) instead. */
    #usersTable .user-name-cell {
        display: block;
        width: 100%;
    }

    #usersTable .user-name-text {
        display: block;
        font-weight: 700;
        margin-bottom: 6px;
    }

    #usersTable .user-name-cell .status-toggle {
        margin-top: 0;
    }

    /* ==========================================================
       Mobile - header/toolbar
       ========================================================== */
    @media (max-width: 768px) {

        #usersCard .users-header {
            flex-direction: column;
        }

        #usersCard .btn-add-user {
            width: 100%;
            justify-content: center;
        }

        #usersCard .users-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        #usersCard .toolbar-search {
            flex-basis: auto;
            width: 100%;
        }

        #usersCard .toolbar-divider {
            display: none;
        }

        #usersCard .toolbar-filter {
            width: 100%;
            justify-content: space-between;
        }

        #usersCard .toolbar-filter select {
            flex: 1;
            margin-left: 8px;
        }

        #usersCard .btn-reset-filters {
            width: 100%;
            justify-content: center;
        }

        #usersCard .toolbar-length {
            margin-left: 0;
            width: 100%;
            justify-content: space-between;
        }

        #usersTable_wrapper .dataTables_paginate,
        #usersTable_wrapper .dataTables_info {
            text-align: center;
        }

        /* ==========================================================
           Mobile card layout for the users table - same technique
           as the Leads table (plain CSS/JS, not the DataTables
           Responsive extension): each <tr> becomes a card, Name
           stays the always-visible heading with Action right under
           it, and everything else only shows once the row is
           tapped and gets .row-expanded.
           ========================================================== */
        #usersTable thead {
            display: none;
        }

        #usersTable,
        #usersTable tbody {
            display: block;
            width: 100%;
        }

        #usersTable tbody tr {
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid #eef0f3;
            border-radius: 12px;
            margin-bottom: 10px;
            padding: 12px 14px;
            cursor: pointer;
        }

        #usersTable tbody td {
            display: none;
            border: none !important;
            padding: 0 !important;
        }

        #usersTable tbody td.dataTables_empty {
            display: block !important;
            text-align: center;
            color: #8a92a3;
            font-size: 13px;
            padding: 6px 0 !important;
        }

        #usersTable tbody td.col-heading {
            display: flex;
            order: 1;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-bottom: 10px !important;
        }

        /* Mobile only: Name and Active/Inactive share one line
           (name left, status right) instead of the desktop's
           stacked layout. */
        #usersTable .user-name-cell {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
        }

        #usersTable .user-name-text {
            display: inline;
            margin-bottom: 0;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #usersTable tbody td.col-action {
            display: flex;
            order: 2;
            gap: 8px;
        }

        .expand-chevron {
            display: inline-block;
        }

        #usersTable tbody tr.row-expanded .expand-chevron {
            transform: rotate(180deg);
        }

        #usersTable tbody tr.row-expanded td.col-listable {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 9px 0 0 !important;
            margin-top: 9px;
            border-top: 1px solid #f1f2f6 !important;
            font-size: 13px;
        }

        #usersTable tbody tr.row-expanded td.col-listable.col-stacked {
            flex-direction: column;
            align-items: stretch;
            text-align: left;
        }

        #usersTable tbody td:nth-child(2).col-listable { order: 3; }
        #usersTable tbody td:nth-child(3).col-listable { order: 4; }
        #usersTable tbody td:nth-child(4).col-listable { order: 5; }
        #usersTable tbody td:nth-child(5).col-listable { order: 6; }
        #usersTable tbody td:nth-child(6).col-listable { order: 7; }

        #usersTable tbody td.col-listable::before {
            content: attr(data-label);
            font-weight: 600;
            color: #8a92a3;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            flex-shrink: 0;
        }

        #usersTable tbody td.col-listable {
            color: #384153;
            text-align: right;
        }

        #usersTable tbody td.col-listable.col-stacked::before {
            margin-bottom: 4px;
        }

        /* Right-aligning col-listable above would otherwise push
           the status toggle's own label away from its switch. */
        #usersTable .status-toggle {
            text-align: left;
            margin-bottom: 0px;
        }

        /* Mobile: Devices dropped the desktop's narrow 135px-wide
           box in favor of full-width rows so each device (Desktop/
           Tablet/Mobile) reads the same way the 2FA row does -
           an uppercase label on the left, its toggle on the right,
           separated by a thin divider instead of sitting in a
           cramped column. Functionality (toggle URLs/handlers) is
           unchanged - only sizing/typography differs from desktop. */
        .login-access-wrapper {
            width: 100%;
            min-width: 0;
            gap: 0;
        }

        /* row-reverse flips the DOM order (toggle, then label) back
           to label-first visually, without needing different markup
           per breakpoint. */
        .device-access-row {
            flex-direction: row-reverse;
            justify-content: space-between;
            gap: 10px;
            min-height: 22px;
            padding-top: 8px;
            border-top: 1px solid #f1f2f6;
        }

        .device-access-row:first-child {
            padding-top: 0;
            border-top: none;
        }

        .device-label {
            min-width: auto;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #8a92a3;
        }
    }
</style>
@php
    $authUser = Auth::user();
    $isSuperAdmin = strtolower($authUser->role->name) === 'super admin';
    $agency = $authUser->agency; // assuming relationship `agency` exists
    $today = now()->format('Y-m-d');
@endphp
<div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card" id="usersCard">
                <div class="card-body">

                {{-- Header --}}
                <div class="users-header">

                    <div>
                        <div class="users-eyebrow">
                            User Management
                        </div>
                        <h4 class="card-title mb-1">
                            Users
                        </h4>
                        <p>
                            Manage every user's access, role and login devices.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="btn btn-primary btn-add-user"
                        data-toggle="modal"
                        data-target="#createModal"
                    >
                        <i class="mdi mdi-plus"></i>
                        Add User
                    </button>

                </div>

                {{-- Toolbar: search + filters + reset + page length --}}
                <div class="users-toolbar">

                    <div class="toolbar-search" id="toolbarSearchSlot">
                        <i class="mdi mdi-magnify"></i>
                        {{-- native DataTables search input is moved in here via JS --}}
                    </div>

                    <div class="toolbar-divider"></div>

                    <div class="toolbar-filter">
                        <label for="filterRole">Role</label>
                        <select id="filterRole">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                @if($role->name != 'Super Admin')
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="toolbar-filter">
                        <label for="filterStatus">Status</label>
                        <select id="filterStatus">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <button type="button" id="resetFiltersBtn" class="btn-reset-filters">
                        <i class="mdi mdi-refresh"></i>
                        Reset
                    </button>

                    <div class="toolbar-length" id="toolbarLengthSlot">
                        Rows
                        {{-- native DataTables length select is moved in here via JS --}}
                    </div>

                </div>

                    <!-- Success -->
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="usersTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Address</th>
                                    <th>2FA</th>
                                    <th>Devices</th>
                                    <!-- <th>Tablet</th>
                                    <th>Mobile Login</th> -->
                                    <!-- <th>Status</th> -->
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createModal">
    <div class="modal-dialog modal-lg">
        <form  id="createUserForm" method="POST" class="forms-sample" action="{{ route('users.store') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add User</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            Products <span class="text-danger">*</span>
                            <i
                                class="mdi mdi-information-outline field-info-icon"
                                tabindex="0"
                                data-tooltip="Users can select more than one product."
                            ></i>
                        </label>

                        <div class="product-select-group" id="create_product_group">
                            @foreach($products as $product)
                                <label class="product-select-option">
                                    <input
                                        type="checkbox"
                                        name="product_id[]"
                                        value="{{ $product->id }}"
                                    >
                                    <span class="product-select-chip">
                                        {{ $product->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="required-label">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Name" >
                        </div>

                        <div class="form-group">
                            <label class="required-label">Email address</label>
                            <input  name="email" class="form-control" placeholder="Email" >
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="required-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password" >
                        </div>

                        <div class="form-group">
                            <label class="required-label">Role</label>
                            <select name="role_id" class="form-control">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    @if($role->name != 'Super Admin')
                                        <option value="{{ $role->id }}"
                                            {{ isset($user) && $user->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="required-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" max="{{ $today }}">
                        </div>

                        <div class="form-group">
                            <label class="required-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ $isSuperAdmin ? old('city') : $agency->city }}" placeholder="City">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="required-label">State</label>
                            <input type="text" name="state" class="form-control" value="{{ $isSuperAdmin ? old('state') : $agency->state }}" placeholder="State">
                        </div>

                        <div class="form-group">
                            <label class="required-label">Zip</label>
                            <input type="text" name="zip" class="form-control" value="{{ $isSuperAdmin ? old('zip') : $agency->zip }}" placeholder="Zip">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required-label">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ $isSuperAdmin ? old('address') : $agency->address }}" placeholder="Address">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold d-block mb-2">
                            Device Access
                        </label>

                        <div class="device-access-options">

                            <div class="device-access-option">
                                <div class="device-access-option-info">
                                    <i class="mdi mdi-tablet"></i>
                                    <span>Tablet Access</span>
                                </div>

                                <label class="status-toggle status-toggle--sm">
                                    <input type="hidden" name="is_tablet" value="0">
                                    <input
                                        type="checkbox"
                                        name="is_tablet"
                                        value="1"
                                        {{ old('is_tablet', 0) == 1 ? 'checked' : '' }}
                                    >
                                    <span class="toggle-track"></span>
                                </label>
                            </div>

                            <div class="device-access-option">
                                <div class="device-access-option-info">
                                    <i class="mdi mdi-cellphone"></i>
                                    <span>Mobile Access</span>
                                </div>

                                <label class="status-toggle status-toggle--sm">
                                    <input type="hidden" name="is_mobile" value="0">
                                    <input
                                        type="checkbox"
                                        name="is_mobile"
                                        value="1"
                                        {{ old('is_mobile', 0) == 1 ? 'checked' : '' }}
                                    >
                                    <span class="toggle-track"></span>
                                </label>
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label>Profile Photo</label>
                        <div class="file-upload-field" id="profileField">
                            <input type="text" class="file-upload-info" id="fileName" placeholder="No file chosen" readonly>
                            <input type="file" id="profileInput" name="profile" accept="image/*" class="file-upload-default">
                            <button type="button" class="file-upload-browse">
                                <i class="mdi mdi-paperclip"></i>
                                Browse
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            JPG, JPEG or PNG. Max 2MB.
                        </small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="createUserSaveBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!--Edit Modals  -->
<!--
    Edit Modal - a single shared instance instead of one per user.
    Its fields are filled in via JS (from the DataTable row's own
    data, already loaded client-side - no extra request needed)
    right before it's shown, same pattern used on the Roles page.
    That's what lets Add/Edit refresh only the table below instead
    of the whole page.
-->
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-lg">
        <form id="editUserForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            Products <span class="text-danger">*</span>
                            <i
                                class="mdi mdi-information-outline field-info-icon"
                                tabindex="0"
                                data-tooltip="Users can select more than one product."
                            ></i>
                        </label>

                        <div class="product-select-group" id="edit_product_group">
                            @foreach($products as $product)
                                <label class="product-select-option">
                                    <input
                                        type="checkbox"
                                        name="product_id[]"
                                        value="{{ $product->id }}"
                                    >
                                    <span class="product-select-chip">
                                        {{ $product->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="required-label">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Name" >
                        </div>

                        <div class="form-group">
                            <label class="required-label">Email address</label>
                            <input type="email" name="email" class="form-control" placeholder="Email" >
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Password <small class="text-muted">(leave blank to keep old)</small></label>
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>

                        <div class="form-group">
                            <label class="required-label">Role</label>
                            <select name="role_id" class="form-control">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    @if($role->name != 'Super Admin')
                                        <option value="{{ $role->id }}">
                                            {{ $role->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="required-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" max="{{ $today }}">
                        </div>

                        <div class="form-group">
                            <label class="required-label">City</label>
                            <input type="text" name="city" class="form-control">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="required-label">State</label>
                            <input type="text" name="state" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="required-label">Zip</label>
                            <input type="text" name="zip" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required-label">Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Profile Photo</label>

                        <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                            <img src="{{ asset('assets/images/default-profile.png') }}"
                                alt="Profile"
                                id="editProfilePreview"
                                style="width: 50px;
                                        height: 50px;
                                        object-fit: cover;
                                        border-radius: 50%;
                                        border: 2px solid #ddd;">
                        </div>

                        <div class="file-upload-field" id="editProfileField">
                            <input type="text"
                                class="file-upload-info"
                                id="editFileName"
                                placeholder="No file chosen"
                                readonly>

                            <input type="file"
                                id="editProfileInput"
                                name="profile"
                                accept="image/*"
                                class="file-upload-default">

                            <button type="button" class="file-upload-browse">
                                <i class="mdi mdi-paperclip"></i>
                                Browse
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            Leave blank to keep the current photo.
                        </small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="editUserSaveBtn" class="btn btn-primary">Update</button>
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
        setTimeout(function () { waitForJQuery(callback); }, 50);
    }
}
let usersTable;
waitForJQuery(function () {

    /*
    * Icon tooltip - originally built for the Products field-info
    * icon, now reused for every icon-only action in this table
    * (Edit/Login Logs/Delete, Status/2FA/Device toggles) via the
    * same [data-tooltip] attribute so every tooltip in the app
    * looks and behaves identically. Rendered as a position:fixed
    * element appended to <body> and positioned from the trigger's
    * own getBoundingClientRect(), instead of a CSS ::after anchored
    * to the icon - the icon can live inside .modal-body, which
    * scrolls (overflow-y: auto), so an absolutely-positioned
    * tooltip near the top of the modal was getting clipped/hidden
    * by that scroll container. Fixed positioning + a high z-index
    * (see .field-info-tooltip) keeps it fully visible above the
    * modal on both desktop and mobile; :focus covers tap-to-show on
    * touch devices for focusable triggers (buttons/links).
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

        // Flip below the icon if there isn't enough room above
        // (e.g. Products is the first field in the modal).
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
    // Capture phase - scroll events (e.g. the modal-body's own
    // scrollbar) don't bubble, so a plain delegated/bubbling
    // listener would miss them.
    document.addEventListener('scroll', hideFieldTooltip, true);

    document.addEventListener('DOMContentLoaded', function () {

        usersTable = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ordering: true,

            ajax: {
                url: "{{ route('users.index') }}",
                data: function (d) {
                    d.role_id = $('#filterRole').val();
                    d.status = $('#filterStatus').val();
                }
            },

            columns: [
                {
                    data: 'name',
                    name: 'name',
                    render: function (data, type, row) {

                        return `
                            <div class="user-name-cell">
                                <span class="user-name-text">
                                    ${data ?? ''}
                                    <i class="mdi mdi-chevron-down expand-chevron"></i>
                                </span>

                                <label
                                    class="status-toggle"
                                    data-id="${row.id}"
                                    data-tooltip="Activate or deactivate this user's account"
                                >
                                    <input
                                        type="checkbox"
                                        class="toggle-status"
                                        data-id="${row.id}"
                                        data-url="/users/toggle-status/${row.id}"
                                        ${row.status ? 'checked' : ''}
                                    >
                                    <span class="toggle-track"></span>
                                    <span class="toggle-label">
                                        ${row.status ? 'Active' : 'Inactive'}
                                    </span>
                                </label>
                            </div>
                        `;
                    },
                    createdCell: function (td) {
                        $(td).addClass('col-heading');
                    }
                },
                {
                    data: 'email',
                    createdCell: function (td) {
                        $(td).addClass('col-listable').attr('data-label', 'Email');
                    }
                },
                {
                    data: 'role',
                    render: function (data) {
                        return data ? data.name : 'N/A';
                    },
                    createdCell: function (td) {
                        $(td).addClass('col-listable').attr('data-label', 'Role');
                    }
                },
                {
                    data: 'address',
                    name: 'address',
                    render: function (data) {

                        if (!data) {
                            return '<span class="text-muted">—</span>';
                        }

                        return `
                            <p class="user-address mb-0" title="${data}">
                                ${data}
                            </p>
                        `;
                    },
                    createdCell: function (td) {
                        $(td).addClass('col-listable').attr('data-label', 'Address');
                    }
                },
                {
                    data: 'otp_enabled',
                    name: 'otp_enabled',
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <label class="status-toggle status-toggle--sm" data-tooltip="Require OTP verification at login (2FA)">
                                <input
                                    type="checkbox"
                                    class="toggle-otp"
                                    data-id="${row.id}"
                                    data-url="/users/toggle-otp/${row.id}"
                                    ${data ? 'checked' : ''}
                                >
                                <span class="toggle-track"></span>
                            </label>
                        `;
                    },
                    createdCell: function (td) {
                        $(td).addClass('col-listable').attr('data-label', '2FA');
                    }
                },
                {
                    data: null,
                    name: 'login_access',
                    orderable: false,
                    searchable: false,

                    render: function (data, type, row) {

                        return `
                            <div class="login-access-wrapper">

                                <!-- Desktop -->
                                <div class="device-access-row">

                                    <label
                                        class="status-toggle status-toggle--sm is-disabled"
                                        data-tooltip="Desktop access is mandatory and cannot be disabled."
                                    >
                                        <input type="checkbox" checked disabled>
                                        <span class="toggle-track"></span>
                                    </label>

                                    <span class="device-label">
                                        Desktop
                                    </span>

                                </div>


                                <!-- Tablet -->
                                <div class="device-access-row">

                                    <label class="status-toggle status-toggle--sm" data-tooltip="Allow login from tablet devices">
                                        <input
                                            type="checkbox"
                                            class="toggle-tablet"
                                            data-id="${row.id}"
                                            data-url="/users/toggle-tablet/${row.id}"
                                            ${row.is_tablet ? 'checked' : ''}
                                        >
                                        <span class="toggle-track"></span>
                                    </label>

                                    <span class="device-label">
                                        Tablet
                                    </span>

                                </div>


                                <!-- Mobile -->
                                <div class="device-access-row">

                                    <label class="status-toggle status-toggle--sm" data-tooltip="Allow login from mobile devices">
                                        <input
                                            type="checkbox"
                                            class="toggle-mobile"
                                            data-id="${row.id}"
                                            data-url="/users/toggle-mobile/${row.id}"
                                            ${row.is_mobile ? 'checked' : ''}
                                        >
                                        <span class="toggle-track"></span>
                                    </label>

                                    <span class="device-label">
                                        Mobile
                                    </span>

                                </div>
                            </div>
                        `;
                    },
                    createdCell: function (td) {
                        $(td).addClass('col-listable col-stacked').attr('data-label', 'Devices');
                    }
                },

                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (id, type, row) {
                        return `
                            <div class="action-btns">

                                <a
                                    href="/login-logs?user_id=${id}"
                                    class="btn btn-sm btn-icon btn-logs"
                                    target="_blank"
                                    rel="noopener"
                                    data-tooltip="Login Logs">
                                    <i class="mdi mdi-history"></i>
                                </a>

                                <button
                                    class="btn btn-sm btn-icon btn-edit editBtn"
                                    data-id="${id}"
                                    data-status="${row.status}"
                                    data-tooltip="Edit">
                                    <i class="mdi mdi-pencil-box"></i>
                                </button>

                                <a
                                    href="/users/delete/${id}"
                                    class="btn btn-sm btn-icon btn-remove btn-delete"
                                    data-id="${id}"
                                    data-tooltip="Delete">
                                    <i class="mdi mdi-delete"></i>
                                </a>

                            </div>
                        `;
                    },
                    createdCell: function (td) {
                        $(td).addClass('col-action');
                    }
                }
            ],

            /*
            * Relocates the DataTables-generated search input and
            * page-length select into the custom toolbar slots, same
            * approach used on the Leads/Roles pages.
            */
            initComplete: function () {

                $('#usersTable_filter input')
                    .attr('placeholder', 'Search users...')
                    .appendTo('#toolbarSearchSlot');

                $('#usersTable_filter').remove();

                $('#usersTable_length select')
                    .appendTo('#toolbarLengthSlot');

                $('#usersTable_length').remove();

            },

            drawCallback: function () {

                const hasRows = this.api().page.info().recordsDisplay > 0;

                $('#usersTable_wrapper .dataTables_paginate')
                    .toggle(hasRows);

            }
        });

        /*
        * MOBILE CARD EXPAND/COLLAPSE - tapping a row reveals its
        * col-listable fields (Email, Role, Address, 2FA, Devices),
        * same approach used on the Leads table. Ignored on desktop
        * since every column is already visible there (see the CSS
        * media query). Taps on the action buttons or any toggle
        * switch are excluded so they keep working normally instead
        * of also toggling the card.
        */
        $(document).on('click', '#usersTable tbody tr', function (e) {

            if ($(e.target).closest('.col-action, .status-toggle').length) {
                return;
            }

            $(this).toggleClass('row-expanded');

        });

    });
    $('#filterRole, #filterStatus').on('change', function () {
        usersTable.draw();
    });

    $('#resetFiltersBtn').on('click', function () {
        $('#filterRole').val('');
        $('#filterStatus').val('');
        usersTable.draw();
    });
    /*
    * File upload field - the same joined filename+Browse control
    * used on the Leads activity/document upload. Clicking anywhere
    * in the field (text area, button, icon) opens the hidden file
    * input; there's one such field in the Create modal and one per
    * user in each Edit modal, so this is delegated rather than
    * bound to a single element.
    */
    $(document).on('click', '.file-upload-field', function () {

        $(this).find('input[type="file"]').trigger('click');

    });

    // File input display
    $(document).on('change', 'input[type="file"]', function () {

        const nameFieldId = this.id.replace('profileInput', 'fileName');

        const nameField = document.getElementById(nameFieldId);

        if (this.files.length === 0) {

            if (nameField) {
                nameField.value = '';
                nameField.classList.remove('has-file');
            }

            return;
        }

        if (nameField) {
            nameField.value = this.files[0].name;
            nameField.classList.add('has-file');
        }

        // Preview new image
        let previewId = this.id.replace('profileInput', 'profilePreview');

        let preview = document.getElementById(previewId);

        if (preview) {

            let reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
            };

            reader.readAsDataURL(this.files[0]);
        }
    });

    function clearErrors(form) {

        $(form).find('.is-invalid').removeClass('is-invalid');

        $(form).find('.invalid-feedback').remove();
    }

    /*
    * Soft, non-blocking toast (auto-dismisses) instead of a blocking
    * modal - same notification style used on Leads/Roles, and by
    * the global flash-message toast in layout.blade.php.
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

    /*
    * Shared button loader - swaps a submit/action button's own
    * label for a spinning mdi-loading glyph (the mdi-spin animation
    * ships with the app's own icon font already, no extra CSS
    * needed) and disables it, so a slow request can't be fired
    * twice from a double click. The original markup is restored
    * exactly on turnOff, whether the request succeeded or failed.
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

    /*
    * Same soft-alert card used on Leads/Roles for delete confirmation
    * (swal-leads-popup / swal-delete-icon / swal-btn-* classes),
    * reused here for every confirmation on this page instead of
    * each action getting its own alert design. `danger: true` gives
    * the red destructive styling (Delete); otherwise it uses the
    * purple non-destructive styling (Status/2FA/Device toggles).
    */
    function softConfirm(options) {

        return Swal.fire({
            html: `
                <div class="swal-delete-icon${options.danger ? '' : ' swal-icon-neutral'}">
                    <i class="mdi ${options.icon}"></i>
                </div>
                <h2 class="swal-delete-title">${options.title}</h2>
                <p class="swal-delete-text">${options.text}</p>
            `,
            showCancelButton: true,
            confirmButtonText: options.confirmText || 'Yes',
            cancelButtonText: options.cancelText || 'Cancel',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                popup: 'swal-leads-popup',
                confirmButton: options.danger ? 'swal-btn-danger' : 'swal-btn-primary',
                cancelButton: 'swal-btn-cancel'
            }
        });

    }

    function resetCreateModal() {

        const modal = $('#createModal');
        const form = modal.find('form')[0];

        // Reset form
        if (form) {
            form.reset();
        }

        // Clear validation errors
        clearErrors(modal);

        // Explicitly clear all inputs
        modal.find('input[name="name"]').val('');
        modal.find('input[name="email"]').val('');
        modal.find('input[name="password"]').val('');
        modal.find('input[name="date_of_birth"]').val('');

        // Clear file
        modal.find('input[type="file"]').val('');

        // Clear filename
        modal.find('.file-upload-info').val('').removeClass('has-file');

        // Products/Device Access checkboxes - form.reset() above
        // already restores these to their default (unchecked)
        // state, this is just a defensive no-op safety net.
        modal.find('input[type="checkbox"]').each(function () {
            this.checked = this.defaultChecked;
        });

        // Products validation state - clearErrors() above already
        // removes this (it's a .invalid-feedback inside the modal),
        // but the group's own is-invalid class isn't a form field so
        // form.reset() won't touch it; clear it explicitly so a
        // stale red outline can't survive into the next open.
        modal.find('.product-select-group')
            .removeClass('is-invalid')
            .next('.invalid-feedback').remove();

        // Reset role
        modal.find('select[name="role_id"]').val('');

        // Because city/state/zip/address have agency values,
        // restore them from their original HTML value.
        modal.find('input[name="city"]').val(
            modal.find('input[name="city"]').prop('defaultValue')
        );

        modal.find('input[name="state"]').val(
            modal.find('input[name="state"]').prop('defaultValue')
        );

        modal.find('input[name="zip"]').val(
            modal.find('input[name="zip"]').prop('defaultValue')
        );

        modal.find('input[name="address"]').val(
            modal.find('input[name="address"]').prop('defaultValue')
        );
    }

    /*
    * Edit modal is a single shared instance (populated fresh from
    * the clicked row's own data every time it's opened - see the
    * .editBtn click handler below), so in normal use the next open
    * always repopulates every field from scratch anyway. This still
    * does a full reset (not just errors/file) so the modal never
    * momentarily shows the previous user's data - or a leftover
    * error state - between one close and the next open, regardless
    * of how it was closed (Cancel, the X, Esc, or clicking outside).
    */
    function resetEditModal() {

        const modal = $('#editModal');
        const form = modal.find('form')[0];

        if (form) {
            form.reset();
        }

        clearErrors(modal);

        modal.find('input[name="name"]').val('');
        modal.find('input[name="email"]').val('');
        modal.find('input[name="password"]').val('');
        modal.find('input[name="date_of_birth"]').val('');
        modal.find('input[name="city"]').val('');
        modal.find('input[name="state"]').val('');
        modal.find('input[name="zip"]').val('');
        modal.find('input[name="address"]').val('');

        modal.find('select[name="role_id"]').val('');

        // Products/Device Access checkboxes - form.reset() above
        // already restores these to their default (unchecked)
        // state, this is just a defensive no-op safety net.
        modal.find('input[type="checkbox"]').each(function () {
            this.checked = this.defaultChecked;
        });

        // Products validation state - clearErrors() above already
        // removes this, but the group's own is-invalid class isn't a
        // form field so form.reset() won't touch it.
        modal.find('.product-select-group')
            .removeClass('is-invalid')
            .next('.invalid-feedback').remove();

        modal.find('input[type="file"]').val('');

        modal.find('.file-upload-info').val('').removeClass('has-file');

        $('#editProfilePreview').attr(
            'src',
            "{{ asset('assets/images/default-profile.png') }}"
        );
    }

    function showFieldError(field, message) {

        const input = $(field);

        input.addClass('is-invalid');

        const existing = input.next('.invalid-feedback');

        if (existing.length) {
            // Update in place - a field that fails a *different*
            // check on the next attempt shouldn't keep showing its
            // previous error message.
            existing.text(message);
        } else {
            input.after(
                '<div class="invalid-feedback">' + message + '</div>'
            );
        }
    }
    function clearFieldError(field) {

        const input = $(field);

        input.removeClass('is-invalid');
        input.next('.invalid-feedback').remove();
    }

    // Product group error markup - a small icon + message box (see
    // the ".product-select-group + .invalid-feedback" CSS) instead
    // of a plain text line, shared by client-side and server-side
    // (422) validation so both look identical.
    function productErrorHtml(message) {
        return '<div class="invalid-feedback">' +
            '<i class="mdi mdi-alert-circle-outline"></i>' +
            message +
            '</div>';
    }

    function validateUserForm(form) {

        let valid = true;

        const name = $(form).find('[name="name"]');
        const email = $(form).find('[name="email"]');
        const role = $(form).find('[name="role_id"]');
        const dob = $(form).find('[name="date_of_birth"]');
        const city = $(form).find('[name="city"]');
        const state = $(form).find('[name="state"]');
        const zip = $(form).find('[name="zip"]');
        const address = $(form).find('[name="address"]');

        // Name
        if ($.trim(name.val()) === '') {
            showFieldError(name, 'Please enter the user name.');
            valid = false;
        } else {
            clearFieldError(name);
        }

        // Email
        if ($.trim(email.val()) === '') {
            showFieldError(email, 'Please enter the email address.');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.val())) {
            showFieldError(email, 'Please enter a valid email address.');
            valid = false;
        } else {
            clearFieldError(email);
        }

        // Role
        if (!role.val()) {
            showFieldError(role, 'Please select a role.');
            valid = false;
        } else {
            clearFieldError(role);
        }

        // DOB
        if (!dob.val()) {

            showFieldError(dob, 'Please select the date of birth.');
            valid = false;

        } else {

            // "Today" anchored to UK time, not the viewer's device/UTC -
            // otherwise this boundary check can be off by a day depending
            // on where in the world the form is being filled in from.
            const londonParts = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Europe/London',
                year: 'numeric', month: '2-digit', day: '2-digit',
            }).formatToParts(new Date());
            const londonLookup = {};
            londonParts.forEach(p => { londonLookup[p.type] = p.value; });
            const today = `${londonLookup.year}-${londonLookup.month}-${londonLookup.day}`;

            if (dob.val() > today) {
                showFieldError(
                    dob,
                    'Date of birth cannot be a future date.'
                );
                valid = false;
            } else {
                clearFieldError(dob);
            }
        }

        // City
        if ($.trim(city.val()) === '') {
            showFieldError(city, 'Please enter the city.');
            valid = false;
        } else {
            clearFieldError(city);
        }

        // State
        if ($.trim(state.val()) === '') {
            showFieldError(state, 'Please enter the state.');
            valid = false;
        } else {
            clearFieldError(state);
        }

        // ZIP
        if ($.trim(zip.val()) === '') {
            showFieldError(zip, 'Please enter the ZIP code.');
            valid = false;
        } else {
            clearFieldError(zip);
        }

        // Address
        if ($.trim(address.val()) === '') {
            showFieldError(address, 'Please enter the address.');
            valid = false;
        } else {
            clearFieldError(address);
        }

        // Products - at least one selected
        const productGroup = $(form).find('.product-select-group');
        const hasProduct = productGroup.find('input[type="checkbox"]:checked').length > 0;

        if (!hasProduct) {

            productGroup.addClass('is-invalid');

            if (productGroup.next('.invalid-feedback').length === 0) {
                productGroup.after(
                    productErrorHtml('Please select at least one product.')
                );
            }

            valid = false;

        } else {

            productGroup.removeClass('is-invalid');
            productGroup.next('.invalid-feedback').remove();

        }

        return valid;
    }
    function validateCreatePassword(form) {

        // ONLY CREATE USER
        if (form.id !== 'createUserForm') {
            return true;
        }

        const password = $(form).find('input[name="password"]');
        const value = password.val() || '';

        // Empty password
        if ($.trim(value) === '') {

            password.addClass('is-invalid');

            // Remove existing error first
            password.next('.invalid-feedback').remove();

            password.after(
                '<div class="invalid-feedback">Please enter a password.</div>'
            );

            return false;
        }

        // Less than 8 characters
        if (value.length < 8) {

            password.addClass('is-invalid');

            password.next('.invalid-feedback').remove();

            password.after(
                '<div class="invalid-feedback">Password must be at least 8 characters.</div>'
            );

            return false;
        }

        // Valid
        password.removeClass('is-invalid');
        password.next('.invalid-feedback').remove();

        return true;
    }


    function showErrors(form, errors) {

        const $form = $(form);

        clearErrors($form);

        $.each(errors, function (field, messages) {

            // Products is a checkbox group, not a single named field
            // - point the error at the whole button group instead.
            if (field === 'product_id') {

                const productGroup = $form.find('.product-select-group');

                productGroup.addClass('is-invalid');

                productGroup.after(
                    productErrorHtml(messages[0])
                );

                return;
            }

            const input = $form.find('[name="' + field + '"]');

            if (!input.length) {
                console.log('Validation field not found:', field);
                return;
            }

            input.addClass('is-invalid');

            input.next('.invalid-feedback').remove();

            input.after(
                '<div class="invalid-feedback">' +
                messages[0] +
                '</div>'
            );
        });
    }


    $(document).on(
        'blur',
        '#createUserForm input:not([name="password"]), #createUserForm select, #createUserForm textarea, #editUserForm input:not([name="password"]), #editUserForm select, #editUserForm textarea',
        function () {

            const field = $(this);

            if ($.trim(field.val()) !== '') {
                clearFieldError(field);
            }
        }
    );

    // Live-clear the Products error as soon as at least one is picked
    $(document).on(
        'change',
        '#createUserForm .product-select-group input, #editUserForm .product-select-group input',
        function () {

            const productGroup = $(this).closest('.product-select-group');

            if (productGroup.find('input[type="checkbox"]:checked').length > 0) {
                productGroup.removeClass('is-invalid');
                productGroup.next('.invalid-feedback').remove();
            }
        }
    );

    $(document).on('blur', '#createUserForm input[name="password"]', function () {

        const form = document.getElementById('createUserForm');

        validateCreatePassword(form);

    });


    // Reset on close, bound two ways so it's not solely dependent on
    // Bootstrap's own modal events firing: hide.bs.modal covers Esc
    // and clicking the backdrop (outside the modal), while the
    // explicit click handler on the Cancel/X buttons below fires the
    // reset immediately on click, before/independently of Bootstrap's
    // own dismiss handling. Same dual-binding approach already used
    // on the Roles page for this exact concern.
    $('#createModal').on('hide.bs.modal hidden.bs.modal', function () {
        resetCreateModal();
    });

    $('#createModal').on('show.bs.modal', function () {
        resetCreateModal();
    });

    $(document).on('click', '#createModal [data-dismiss="modal"]', function () {
        resetCreateModal();
    });

    $('#editModal').on('hide.bs.modal hidden.bs.modal', function () {
        resetEditModal();
    });

    $(document).on('click', '#editModal [data-dismiss="modal"]', function () {
        resetEditModal();
    });

        // CREATE FORM AJAX SUBMIT
    $('#createUserForm').on('submit', function (e) {

        e.preventDefault();

        const form = this;

        // Clear old errors
        clearErrors(form);

        // Validate normal fields
        const normalFieldsValid = validateUserForm(form);

        // Validate password separately
        const passwordValid = validateCreatePassword(form);

        // STOP if anything is invalid
        if (!normalFieldsValid || !passwordValid) {
            return;
        }

        const formData = new FormData(form);

        const $saveBtn = $('#createUserSaveBtn');

        setBtnLoading($saveBtn, true, 'Saving...');

        $.ajax({

            url: $(form).attr('action'),

            type: 'POST',

            data: formData,

            processData: false,

            contentType: false,

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            success: function (res) {

                if (res.success) {

                    $('#createModal').modal('hide');

                    // Table only - no page reload. Edit now uses a
                    // single shared modal populated from the row's
                    // own DataTable data, so a freshly created user
                    // doesn't need a server-rendered modal waiting
                    // for it the way the old per-user modals did.
                    usersTable.ajax.reload(null, false);

                    showToast('success', res.success || 'User created successfully.');

                }
            },

            error: function (xhr) {

                console.log('CREATE USER ERROR:', xhr.responseJSON);

                if (xhr.status === 422) {

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.errors
                    ) {

                        showErrors(
                            form,
                            xhr.responseJSON.errors
                        );

                    } else {

                        showToast('error', 'Please check the entered information.');
                    }

                } else {

                    showToast('error', 'Something went wrong. Please try again.');
                }
            },

            complete: function () {

                setBtnLoading($saveBtn, false);

            }

        });

    });
    // Clicking Cancel/the X triggers Bootstrap's own hide.bs.modal,
    // already handled by the bindings above - nothing extra needed
    // here.

        // EDIT FORM AJAX SUBMIT
    $(document).on('submit', '#editUserForm', function (e) {

        e.preventDefault();

        const form = this;
        const modal = $(form).closest('.modal');

        clearErrors(modal);

        // Frontend validation
        if (!validateUserForm(form)) {
            return;
        }

        const formData = new FormData(form);

        const $saveBtn = $('#editUserSaveBtn');

        setBtnLoading($saveBtn, true, 'Saving...');

        $.ajax({
            url: $(form).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (res) {

                if (res.success) {

                    modal.modal('hide');

                    // Table only - no page reload. Filters/sort/
                    // page stay exactly where they were since this
                    // is an in-place redraw, not a fresh ajax call
                    // from page 1.
                    usersTable.ajax.reload(null, false);

                    showToast('success', res.success || 'User updated successfully.');

                }
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

    // ":id" is swapped out below for the real id, since
    // route('users.update', ...) needs a real (existing) user to
    // resolve against at Blade-compile time.
    const editUserUrlTemplate = "{{ route('users.update', ':id') }}";

    $(document).on('click', '.editBtn', function () {

        const id = $(this).data('id');
        const rowData = usersTable.row($(this).closest('tr')).data();

        if (!rowData) {
            return;
        }

        const $modal = $('#editModal');
        const $form = $('#editUserForm');

        $form.attr('action', editUserUrlTemplate.replace(':id', id));

        $form.find('input[name="name"]').val(rowData.name);
        $form.find('input[name="email"]').val(rowData.email);
        $form.find('input[name="password"]').val('');
        $form.find('select[name="role_id"]').val(rowData.role_id);
        $form.find('input[name="date_of_birth"]').val(rowData.date_of_birth);
        $form.find('input[name="city"]').val(rowData.city);
        $form.find('input[name="state"]').val(rowData.state);
        $form.find('input[name="zip"]').val(rowData.zip);
        $form.find('input[name="address"]').val(rowData.address);

        // Products - check the ones this user already has
        const assignedProducts = (rowData.product_id || []).map(String);

        $form.find('.product-select-group input[type="checkbox"]').each(function () {
            this.checked = assignedProducts.indexOf(String(this.value)) !== -1;
        });

        // Profile preview + file field, reset to this user's saved
        // photo (or the default placeholder) until/unless they pick
        // a new file
        $form.find('input[type="file"]').val('');
        $('#editFileName').val('').removeClass('has-file');

        $('#editProfilePreview').attr(
            'src',
            rowData.profile
                ? '/' + rowData.profile
                : "{{ asset('assets/images/default-profile.png') }}"
        );

        clearErrors($modal);

        $modal.modal('show');

    });
    // DELETE - same soft-alert confirm used on Leads/Roles
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();

        const $btn = $(this);

        // Already deleting this row - ignore the extra click instead
        // of firing a second request.
        if ($btn.hasClass('is-loading')) {
            return;
        }

        const url = $btn.attr('href');

        // Pull the row's own data so the dialog can name the actual
        // user being deleted instead of a generic message.
        const rowData = usersTable.row($btn.closest('tr')).data();
        const userLabel = (rowData && (rowData.name || rowData.email)) || 'This user';
        const escapedUserLabel = $('<div>').text(userLabel).html();

        softConfirm({
            icon: 'mdi-trash-can-outline',
            danger: true,
            title: 'Delete this user?',
            text: `<strong>${escapedUserLabel}</strong> will be permanently removed. This action can't be undone.`,
            confirmText: 'Delete'
        }).then(function (result) {
            if (result.isConfirmed) {

                $btn.addClass('is-loading');
                $btn.find('i').attr('class', 'mdi mdi-loading mdi-spin');

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (res) {
                        if (res.success) {

                            usersTable.ajax.reload(null, false);

                            showToast('success', res.success || 'User deleted successfully.');
                        }
                    },
                    error: function () {
                        showToast('error', 'Something went wrong. Please try again.');
                    },
                    complete: function () {

                        $btn.removeClass('is-loading');
                        $btn.find('i').attr('class', 'mdi mdi-delete');

                    }
                });
            }
        });
    });
    // TOGGLE STATUS
    $(document).on('change', '.toggle-status', function () {

        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.prop('checked');
        const userId = checkbox.data('id'); // new intended status

        // Ask user for confirmation - same soft-alert shell as
        // Leads/Roles' delete confirm, purple (non-destructive)
        // variant since this isn't a destructive action.
        softConfirm({
            icon: 'mdi-account-switch-outline',
            title: isChecked ? 'Activate this user?' : 'Deactivate this user?',
            text: isChecked
                ? 'This user will be able to log in again.'
                : 'This user will no longer be able to log in.',
            confirmText: isChecked ? 'Yes, activate' : 'Yes, deactivate'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with AJAX
            const $wrapper = checkbox.closest('.status-toggle');

            $wrapper.addClass('is-loading');

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {

                        checkbox.prop('checked', res.status);

                        // Update the Active/Inactive text next to
                        // the switch immediately - it used to only
                        // change after a manual page refresh.
                        checkbox.closest('.status-toggle')
                            .find('.toggle-label')
                            .text(res.status ? 'Active' : 'Inactive');

                        // Update Edit button with latest status
                        $('.editBtn[data-id="' + checkbox.data('id') + '"]')
                            .attr('data-status', res.status)
                            .data('status', res.status);

                        showToast('success', res.message || (res.status ? 'User activated.' : 'User deactivated.'));

                        // Re-sync the table itself (not a page
                        // reload) - needed so a row leaves the list
                        // right away when the Status filter is
                        // active and no longer matches it.
                        usersTable.ajax.reload(null, false);

                },
                error: function (xhr) {
                    checkbox.prop('checked', !isChecked);

                    let message = 'Something went wrong';

                    try {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            let res = JSON.parse(xhr.responseText);
                            message = res.message || message;
                        }
                    } catch (e) {
                        console.log('Parse error:', e);
                    }

                    showToast('error', message);
                },
                complete: function () {
                    $wrapper.removeClass('is-loading');
                }
            });
            } else {
                // User canceled → revert checkbox
                checkbox.prop('checked', !isChecked);
            }
        });
    });
    $(document).on('change', '.toggle-otp', function () {

        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.prop('checked');
        const userId = checkbox.data('id');

        // Immediately prevent accidental state change
        checkbox.prop('checked', !isChecked);

        softConfirm({
            icon: 'mdi-shield-key-outline',

            title: isChecked
                ? 'Enable OTP Login?'
                : 'Disable OTP Login?',

            text: isChecked
                ? 'This user will need to enter an OTP after entering their password.'
                : 'This user will be able to login without OTP verification.',

            confirmText: isChecked
                ? 'Yes, enable OTP'
                : 'Yes, disable OTP'
        }).then((result) => {

            if (!result.isConfirmed) {
                // User cancelled
                checkbox.prop('checked', !isChecked);
                return;
            }

            const $wrapper = checkbox.closest('.status-toggle');

            $.ajax({
                url: url,
                type: 'POST',

                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                beforeSend: function () {
                    checkbox.prop('disabled', true);
                    $wrapper.addClass('is-loading');
                },

                success: function (res) {

                    checkbox.prop('checked', res.otp_enabled);

                    showToast('success', res.message || (res.otp_enabled ? 'OTP login enabled.' : 'OTP login disabled.'));
                },

                error: function (xhr) {

                    // Restore original state
                    checkbox.prop('checked', !isChecked);

                    let message = 'Something went wrong.';

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {
                        message = xhr.responseJSON.message;
                    }

                    showToast('error', message);
                },

                complete: function () {
                    checkbox.prop('disabled', false);
                    $wrapper.removeClass('is-loading');
                }
            });
        });
    });
    $(document).on('change', '.toggle-mobile', function () {

        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.prop('checked');

        // Keep original state until user confirms
        checkbox.prop('checked', !isChecked);

        softConfirm({
            icon: 'mdi-cellphone',

            title: isChecked
                ? 'Enable Mobile Login?'
                : 'Disable Mobile Login?',

            text: isChecked
                ? 'This user will be allowed to login from a mobile device.'
                : 'This user will no longer be allowed to login from a mobile device.',

            confirmText: isChecked
                ? 'Yes, enable'
                : 'Yes, disable'
        }).then((result) => {

            if (!result.isConfirmed) {
                checkbox.prop('checked', !isChecked);
                return;
            }

            const $wrapper = checkbox.closest('.status-toggle');

            $.ajax({
                url: url,
                type: 'POST',

                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                beforeSend: function () {
                    checkbox.prop('disabled', true);
                    $wrapper.addClass('is-loading');
                },

                success: function (res) {

                    checkbox.prop('checked', res.is_mobile);

                    showToast('success', res.message || (res.is_mobile ? 'Mobile login enabled.' : 'Mobile login disabled.'));
                },

                error: function (xhr) {

                    checkbox.prop('checked', !isChecked);

                    let message = 'Something went wrong.';

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {
                        message = xhr.responseJSON.message;
                    }

                    showToast('error', message);
                },

                complete: function () {
                    checkbox.prop('disabled', false);
                    $wrapper.removeClass('is-loading');
                }
            });
        });
    });
    $(document).on('change', '.toggle-tablet', function () {

        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.prop('checked');

        // Restore original state until confirmation
        checkbox.prop('checked', !isChecked);

        softConfirm({
            icon: 'mdi-tablet',

            title: isChecked
                ? 'Enable Tablet Login?'
                : 'Disable Tablet Login?',

            text: isChecked
                ? 'This user will be allowed to login from a tablet device.'
                : 'This user will no longer be allowed to login from a tablet device.',

            confirmText: isChecked
                ? 'Yes, enable'
                : 'Yes, disable'
        }).then((result) => {

            if (!result.isConfirmed) {
                checkbox.prop('checked', !isChecked);
                return;
            }

            const $wrapper = checkbox.closest('.status-toggle');

            $.ajax({
                url: url,
                type: 'POST',

                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                beforeSend: function () {
                    checkbox.prop('disabled', true);
                    $wrapper.addClass('is-loading');
                },

                success: function (res) {

                    checkbox.prop('checked', res.is_tablet);

                    showToast('success', res.message || (res.is_tablet ? 'Tablet login enabled.' : 'Tablet login disabled.'));
                },

                error: function (xhr) {

                    checkbox.prop('checked', !isChecked);

                    let message = 'Something went wrong.';

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {
                        message = xhr.responseJSON.message;
                    }

                    showToast('error', message);
                },

                complete: function () {
                    checkbox.prop('disabled', false);
                    $wrapper.removeClass('is-loading');
                }
            });
        });
    });
    $(document).on('change', '.desktop-access-toggle', function () {

        const checkbox = $(this);

        // Immediately restore the toggle to ON
        checkbox.prop('checked', true);

        Swal.fire({
            icon: 'info',
            title: 'Desktop Access Cannot Be Disabled',
            text: 'Desktop access is mandatory for all users and cannot be disabled.',
            confirmButtonText: 'OK'
        });
    });
});

</script>
@endsection
