@extends('layout')
@section('title', 'Stage Management')
@section('subtitle', 'Stage Management')
@section('content')
<style>
    /* ==========================================================
       Header
       ========================================================== */
    #stagesCard .stages-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 1px solid #eef0f3;
        margin-bottom: 22px;
        gap: 14px;
        flex-wrap: wrap;
    }

    #stagesCard .stages-eyebrow {
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

    #stagesCard .stages-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    #stagesCard .stages-header h4 {
        font-weight: 700;
        font-size: 27px;
        color: #1a1f2b;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }

    #stagesCard .stages-header p {
        color: #8a92a3;
        font-size: 13.5px;
        margin: 0;
    }

    #stagesCard .stages-header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    #stagesCard .btn-add-stage,
    #stagesCard .btn-add-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        font-weight: 500;
        font-size: 14px;
        border-radius: 9px;
        white-space: nowrap;
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
    }

    #stagesCard .btn-add-stage {
        background: #6c63ff;
        border: none;
        color: #fff;
        box-shadow: 0 2px 6px rgba(108, 99, 255, 0.28);
    }

    #stagesCard .btn-add-stage:hover {
        background: #5b52e8;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(108, 99, 255, 0.34);
        color: #fff;
    }

    #stagesCard .btn-add-group {
        background: #fff;
        border: 1px solid #d9d6ff;
        color: #5b52e0;
    }

    #stagesCard .btn-add-group:hover {
        background: #f6f5ff;
        transform: translateY(-1px);
        color: #5b52e0;
    }

    /* ==========================================================
       Toolbar (search + product)
       ========================================================== */
    #stagesCard .stages-toolbar {
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

    #stagesCard .toolbar-search {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1 1 220px;
        min-width: 160px;
    }

    #stagesCard .toolbar-search i {
        color: #a4aab5;
        font-size: 18px;
    }

    #stagesCard .toolbar-search input {
        border: none;
        outline: none;
        font-size: 13.5px;
        width: 100%;
        color: #384153;
        background: transparent;
    }

    #stagesCard .toolbar-product {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
        font-size: 12.5px;
        color: #8a92a3;
        flex-shrink: 0;
    }

    #stagesCard .toolbar-product select {
        border: 1px solid #e6e8ec;
        border-radius: 8px;
        background: #f8f9fb;
        font-size: 13px;
        color: #384153;
        padding: 6px 10px;
        min-width: 160px;
    }

    #stagesCard .stages-note {
        display: flex;
        gap: 8px;
        align-items: flex-start;
        margin: 0 0 18px;
        padding: 10px 12px;
        border: 1px solid #e4e3fb;
        border-radius: 10px;
        background: #f8f8ff;
        font-size: 12.5px;
        color: #4b5163;
    }

    #stagesCard .stages-note i {
        color: #6c63ff;
        font-size: 16px;
        line-height: 1.2;
    }

    /* ==========================================================
       Group cards
       ========================================================== */
    .stage-group-card {
        border: 1px solid #eef0f3;
        border-radius: 12px;
        background: #fff;
        margin-bottom: 16px;
        overflow: hidden;
    }

    .stage-group-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #fafbfc;
        border-bottom: 1px solid #eef0f3;
        flex-wrap: wrap;
    }

    .stage-group-title {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1 1 240px;
        flex-wrap: wrap;
    }

    .stage-group-order {
        font-size: 12px;
        font-weight: 600;
        color: #8a92a3;
    }

    .stage-group-count {
        font-size: 12px;
        color: #8a92a3;
    }

    .stage-group-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-stage-in-group {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 12px;
        border-radius: 8px;
        border: 1px solid #d9d6ff;
        background: #fff;
        color: #5b52e0;
        font-size: 12.5px;
        font-weight: 500;
        white-space: nowrap;
    }

    .btn-stage-in-group:hover {
        background: #f6f5ff;
        color: #5b52e0;
    }

    /* Same colours as the stage pills on the Leads listing. */
    .stage-group-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        background: #eceff3;
        color: #4b5563;
    }

    .stage-lead { background: #e6f4ea; color: #2e7d32; }
    .stage-pricing { background: #fff6dc; color: #9a6b00; }
    .stage-closing { background: #fdeaea; color: #c62828; }
    .stage-tender { background: #efe8fb; color: #6438c2; }
    .stage-loa { background: #e7f1ff; color: #2264d1; }
    .stage-contracts { background: #e0f5f3; color: #0b7a6f; }

    .stage-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .stage-tag {
        display: inline-flex;
        padding: 2px 8px;
        border-radius: 6px;
        background: #f2f3f7;
        color: #4b5163;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
    }

    .stage-table {
        width: 100%;
        margin: 0;
    }

    .stage-table thead th {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #8a92a3;
        border-top: none;
        border-bottom: 1px solid #eef0f3;
        padding: 10px 16px;
        white-space: nowrap;
    }

    .stage-table tbody td {
        padding: 10px 16px;
        font-size: 14px;
        color: #384153;
        vertical-align: middle;
        border-top: 1px solid #f3f4f7;
    }

    .stage-table tbody tr:first-child td {
        border-top: none;
    }

    .stage-table tbody tr:hover {
        background-color: #f8f9fc;
    }

    .stage-table .col-order { width: 80px; }
    .stage-table .col-action { width: 100px; }

    .stage-table .stage-name {
        font-weight: 600;
        color: #1a1f2b;
    }

    .stage-order {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 24px;
        padding: 0 6px;
        border-radius: 6px;
        background: #f2f3f7;
        color: #384153;
        font-size: 12.5px;
        font-weight: 600;
    }

    .stage-group-empty {
        padding: 18px 16px;
        font-size: 13px;
        color: #8a92a3;
        text-align: center;
    }

    .stages-empty {
        padding: 40px 16px;
        text-align: center;
        color: #8a92a3;
        font-size: 13.5px;
        border: 1px dashed #e2e5eb;
        border-radius: 12px;
    }

    .stages-empty i {
        display: block;
        font-size: 32px;
        color: #c7cbd4;
        margin-bottom: 6px;
    }

    /* ==========================================================
       Action buttons
       ========================================================== */
    .action-btns {
        display: flex;
        gap: 6px;
        flex-wrap: nowrap;
    }

    .action-btns .btn-icon {
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

    .action-btns .btn-icon i {
        font-size: 15px;
        margin: 0;
    }

    .action-btns .btn-edit {
        background-color: #eef0ff;
        color: #5b52e0;
    }

    .action-btns .btn-remove {
        background-color: #fdeaea;
        color: #d33a3a;
    }

    .action-btns .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(26, 31, 43, 0.14);
    }

    .action-btns .btn-icon.is-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    /* ==========================================================
       Modals - restyled to match the app instead of the default
       Bootstrap look
       ========================================================== */
    .required-label::after {
        content: ' *';
        color: red;
    }

    /* Centred on both axes: zero the modal's own padding, let the
       dialog take the modal's full height and centre within it. */
    .stage-modal {
        padding: 0 !important;
    }

    .stage-modal .modal-dialog {
        max-width: 540px !important;
        width: calc(100% - 32px);
        min-height: calc(100% - 48px);
        margin: 24px auto !important;
        display: flex;
        align-items: center;
    }

    .stage-modal .modal-dialog > form {
        width: 100%;
    }

    .stage-modal .modal-content {
        border-radius: 14px !important;
        border: none !important;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
        width: 100%;
        max-height: calc(100vh - 48px);
        display: flex;
        flex-direction: column;
    }

    .stage-modal .modal-header {
        background: #fff;
        padding: 20px 24px 16px !important;
        border-bottom: 1px solid #eef0f3 !important;
        align-items: flex-start;
    }

    .stage-modal .modal-heading {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .stage-modal .modal-heading-icon {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        border-radius: 10px;
        background: #f0efff;
        color: #6c63ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stage-modal .modal-title {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #1a1f2b;
    }

    .stage-modal .modal-subtitle {
        margin: 2px 0 0;
        font-size: 12.5px;
        color: #8a92a3;
    }

    .stage-modal .modal-header .close {
        background: none;
        border: none;
        font-size: 22px;
        line-height: 1;
        color: #a4aab5;
        cursor: pointer;
        opacity: 1;
        text-shadow: none;
        margin: 0 0 0 auto;
        padding: 4px;
    }

    .stage-modal .modal-header .close:hover {
        color: #384153;
    }

    .stage-modal .modal-body {
        padding: 20px 24px 4px !important;
        overflow-y: auto !important;
        min-height: 0;
    }

    .stage-modal .form-group {
        margin-bottom: 18px;
    }

    .stage-modal .form-group > label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #384153;
        margin-bottom: 6px;
    }

    .stage-modal .form-control {
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13.5px;
        height: auto;
    }

    .stage-modal .form-control:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
    }

    .stage-modal .form-row-split {
        display: grid;
        grid-template-columns: 1fr 130px;
        gap: 14px;
    }

    .stage-field-hint {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: #7a8092;
    }

    .stage-applies-to {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 8px;
        font-size: 12px;
        color: #7a8092;
    }

    .stage-modal .modal-footer {
        padding: 14px 24px 20px !important;
        border-top: 1px solid #f3f4f7 !important;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .stage-modal .modal-footer .btn-primary {
        background: #6c63ff;
        border-color: #6c63ff;
        border-radius: 9px;
        padding: 9px 22px;
        font-size: 13.5px;
        font-weight: 500;
        margin: 0;
    }

    .stage-modal .modal-footer .btn-primary:hover {
        background: #5b52e8;
        border-color: #5b52e8;
    }

    .stage-modal .modal-footer .btn-secondary {
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 13.5px;
        font-weight: 500;
        border: 1px solid #e2e5eb;
        background: #fff;
        color: #384153;
        margin: 0;
    }

    .stage-modal .modal-footer .btn-secondary:hover {
        background: #f4f5f7;
        color: #384153;
    }

    .stage-modal .modal-footer .btn-primary:disabled {
        opacity: 0.75;
        cursor: not-allowed;
    }

    /* Role / product / colour pickers - the Users page's product chips. */
    .product-select-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
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
        gap: 6px;
        min-width: 90px;
        height: 38px;
        padding: 0 14px;
        background: #fbfbfd;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        color: #6c7280;
        font-size: 13px;
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

    .product-select-group.is-invalid {
        padding: 8px;
        border: 1px solid #d33a3a;
        border-radius: 10px;
        background: rgba(211, 58, 58, 0.03);
    }

    .color-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .color-dot.stage-lead { background: #2e7d32; }
    .color-dot.stage-pricing { background: #d29a00; }
    .color-dot.stage-closing { background: #c62828; }
    .color-dot.stage-tender { background: #6438c2; }
    .color-dot.stage-loa { background: #2264d1; }
    .color-dot.stage-contracts { background: #0b7a6f; }

    .color-select-group .product-select-chip {
        min-width: 0;
    }

    /* ==========================================================
       SweetAlert2 - delete confirmation, same soft-alert style
       used on the Leads page.
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

    .swal-delete-icon.swal-icon-warning {
        background: #fff4e5;
        color: #d97706;
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

    /* ==========================================================
       Mobile
       ========================================================== */
    @media (max-width: 768px) {
        #stagesCard .stages-header {
            flex-direction: column;
        }

        #stagesCard .stages-header-actions,
        #stagesCard .btn-add-stage,
        #stagesCard .btn-add-group {
            width: 100%;
            justify-content: center;
        }

        #stagesCard .stages-header-actions .btn {
            flex: 1 1 0;
        }

        #stagesCard .stages-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        #stagesCard .toolbar-search {
            flex-basis: auto;
            width: 100%;
        }

        #stagesCard .toolbar-product {
            margin-left: 0;
            justify-content: space-between;
        }

        /* Stage rows as small cards. */
        .stage-table thead {
            display: none;
        }

        .stage-table,
        .stage-table tbody {
            display: block;
            width: 100%;
        }

        .stage-table tbody tr {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px 10px;
            padding: 12px 16px;
            border-top: 1px solid #f3f4f7;
        }

        .stage-table tbody tr:first-child {
            border-top: none;
        }

        .stage-table tbody td {
            border: none !important;
            padding: 0 !important;
        }

        .stage-table .col-order,
        .stage-table .col-action {
            width: auto;
        }

        .stage-table tbody td.col-name {
            flex: 1 1 0;
            min-width: 0;
        }

        .stage-table tbody td.col-roles {
            order: 3;
            flex: 1 1 100%;
        }
    }

    @media (max-width: 576px) {
        .stage-modal .modal-dialog {
            width: calc(100% - 24px);
            min-height: calc(100% - 32px);
            margin: 16px auto !important;
        }

        .stage-modal .modal-content {
            max-height: calc(100vh - 32px);
        }

        .stage-modal .modal-header {
            padding: 16px 18px 14px !important;
        }

        .stage-modal .modal-body {
            padding: 18px 18px 4px !important;
        }

        .stage-modal .form-row-split {
            grid-template-columns: 1fr;
        }

        .stage-modal .modal-footer {
            padding: 14px 18px 18px !important;
            flex-direction: column-reverse;
        }

        .stage-modal .modal-footer .btn {
            width: 100%;
            min-height: 44px;
        }

        .product-select-chip {
            min-width: 0;
        }
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card" id="stagesCard">
            <div class="card-body">

                {{-- Header --}}
                <div class="stages-header">
                    <div>
                        <div class="stages-eyebrow">Stage Management</div>
                        <h4 class="card-title mb-1">Lead Stages</h4>
                        <p>Manage the groups and stages leads move through, which products they apply to and who can pick each stage.</p>
                    </div>

                    <div class="stages-header-actions">
                        <button type="button" class="btn btn-add-group" id="addGroupBtn">
                            <i class="mdi mdi-folder-plus-outline"></i>
                            Add Group
                        </button>
                        <button type="button" class="btn btn-primary btn-add-stage" id="addStageBtn">
                            <i class="mdi mdi-plus"></i>
                            Add Stage
                        </button>
                    </div>
                </div>

                <p class="stages-note">
                    <i class="mdi mdi-information-outline"></i>
                    <span>
                        Each group belongs to one or more products, and its stages apply to those products.
                        A stage shows in the Lead Stages dropdown for the roles it's assigned to - Admin and Super Admin can always pick every stage.
                        Each group has its own order, starting at 1. A stage can't be deleted while leads are at it.
                    </span>
                </p>

                {{-- Toolbar: search + product --}}
                <div class="stages-toolbar">
                    <div class="toolbar-search">
                        <i class="mdi mdi-magnify"></i>
                        <input type="search" id="stageSearch" placeholder="Search stages..." autocomplete="off">
                    </div>
                    <label class="toolbar-product mb-0" for="productFilter">
                        Product
                        <select id="productFilter">
                            <option value="">All products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div id="stageGroups">
                    <div class="stages-empty"><i class="mdi mdi-loading mdi-spin"></i>Loading stages...</div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Add / Edit Group - one modal, filled in by JS. --}}
<div class="modal fade stage-modal" id="groupModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="groupForm" method="POST" action="{{ route('lead-stage-groups.store') }}" novalidate>
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-heading">
                        <div class="modal-heading-icon"><i class="mdi mdi-folder-outline"></i></div>
                        <div>
                            <h5 class="modal-title" id="groupModalTitle">Add Group</h5>
                            <p class="modal-subtitle">A set of stages for one or more products.</p>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="form-row-split">
                        <div class="form-group" data-field="name">
                            <label class="required-label" for="groupName">Group Name</label>
                            <input type="text" name="name" id="groupName" class="form-control" maxlength="64" placeholder="e.g. Pricing">
                        </div>

                        <div class="form-group" data-field="sort_order">
                            <label for="groupOrder">Order</label>
                            <input type="number" name="sort_order" id="groupOrder" class="form-control" min="1" max="9999" step="1" placeholder="Last">
                        </div>
                    </div>

                    <div class="form-group" data-field="products">
                        <label class="required-label">Products</label>
                        <div class="product-select-group">
                            @foreach($products as $product)
                                <label class="product-select-option">
                                    <input type="checkbox" name="products[]" value="{{ $product->id }}">
                                    <span class="product-select-chip">{{ $product->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <small class="stage-field-hint">Every stage in this group applies to these products.</small>
                    </div>

                    <div class="form-group" data-field="color">
                        <label class="required-label">Colour</label>
                        <div class="product-select-group color-select-group">
                            @foreach($colors as $slug => $colorName)
                                <label class="product-select-option">
                                    <input type="radio" name="color" value="{{ $slug }}">
                                    <span class="product-select-chip"><span class="color-dot stage-{{ $slug }}"></span>{{ $colorName }}</span>
                                </label>
                            @endforeach
                        </div>
                        <small class="stage-field-hint">Used for this group's stage labels on the Leads pages.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="groupSaveBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Add / Edit Stage - one modal, filled in by JS. --}}
<div class="modal fade stage-modal" id="stageModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="stageForm" method="POST" action="{{ route('lead-stages.store') }}" novalidate>
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-heading">
                        <div class="modal-heading-icon"><i class="mdi mdi-flag-variant-outline"></i></div>
                        <div>
                            <h5 class="modal-title" id="stageModalTitle">Add Stage</h5>
                            <p class="modal-subtitle">A step a lead can be moved to.</p>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="form-group" data-field="group_id">
                        <label class="required-label" for="stageGroup">Group</label>
                        <select name="group_id" id="stageGroup" class="form-control"></select>
                        <div class="stage-applies-to" id="stageAppliesTo"></div>
                    </div>

                    <div class="form-row-split">
                        <div class="form-group" data-field="name">
                            <label class="required-label" for="stageName">Stage Name</label>
                            <input type="text" name="name" id="stageName" class="form-control" maxlength="100" placeholder="e.g. Pricing in Progress">
                        </div>

                        <div class="form-group" data-field="sort_order">
                            <label for="stageOrder">Order</label>
                            <input type="number" name="sort_order" id="stageOrder" class="form-control" min="1" max="9999" step="1" placeholder="Last">
                        </div>
                    </div>
                    <small class="stage-field-hint" style="margin-top: -12px; margin-bottom: 18px;">Order is the stage's position within its group. Leave it blank to add the stage at the end.</small>

                    <div class="form-group" data-field="roles">
                        <label class="required-label">Roles</label>
                        <div class="product-select-group">
                            @foreach($roles as $role)
                                <label class="product-select-option">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}">
                                    <span class="product-select-chip">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <small class="stage-field-hint">Only these roles can see and pick this stage (plus Admin and Super Admin).</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="stageSaveBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    function waitForJQuery(callback) {
        if (window.jQuery && jQuery.fn.modal) {
            callback(jQuery);
        } else {
            setTimeout(function () { waitForJQuery(callback); }, 50);
        }
    }

    waitForJQuery(function ($) {
        $(function () {
            const indexUrl = @json(route('lead-stages.index'));
            const stageStoreUrl = @json(route('lead-stages.store'));
            const stageUpdateUrl = @json(route('lead-stages.update', ':id'));
            const stageDestroyUrl = @json(route('lead-stages.destroy', ':id'));
            const groupStoreUrl = @json(route('lead-stage-groups.store'));
            const groupUpdateUrl = @json(route('lead-stage-groups.update', ':id'));
            const groupDestroyUrl = @json(route('lead-stage-groups.destroy', ':id'));
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const filterKey = 'leadStages.product';

            const $list = $('#stageGroups');
            const $search = $('#stageSearch');
            const $product = $('#productFilter');

            let groups = [];

            function escapeHtml(value) {
                return $('<div>').text(value == null ? '' : String(value)).html();
            }

            function tags(names) {
                if (!names || !names.length) return '<span class="text-muted">-</span>';
                return '<div class="stage-tags">' + names.map(n => `<span class="stage-tag">${escapeHtml(n)}</span>`).join('') + '</div>';
            }

            function showToast(icon, message) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: icon,
                    title: message,
                    showConfirmButton: false,
                    timer: icon === 'success' ? 2200 : 3600,
                    timerProgressBar: true
                });
            }

            function setBtnLoading($btn, loading, text) {
                if (loading) {
                    if ($btn.data('original-html') === undefined) $btn.data('original-html', $btn.html());
                    $btn.prop('disabled', true).addClass('is-loading')
                        .html('<i class="mdi mdi-loading mdi-spin"></i> ' + (text || 'Please wait...'));
                } else {
                    $btn.prop('disabled', false).removeClass('is-loading');
                    if ($btn.data('original-html') !== undefined) $btn.html($btn.data('original-html'));
                }
            }

            // ---- Product filter, remembered per browser ----
            try {
                const saved = localStorage.getItem(filterKey);
                if (saved && $product.find(`option[value="${saved}"]`).length) $product.val(saved);
            } catch (e) {}

            $product.on('change', function () {
                try { localStorage.setItem(filterKey, this.value); } catch (e) {}
                render();
            });

            $search.on('input', render);

            // ---- Listing ----
            function load() {
                return $.ajax({ url: indexUrl, headers: { 'Accept': 'application/json' } })
                    .done(function (res) {
                        groups = res.groups || [];
                        render();
                    })
                    .fail(function () {
                        $list.html('<div class="stages-empty"><i class="mdi mdi-alert-circle-outline"></i>Unable to load the stages. Please refresh the page.</div>');
                    });
            }

            function render() {
                const productId = Number($product.val()) || null;
                const term = $search.val().trim().toLowerCase();

                const visible = groups.filter(g => !productId || g.product_ids.includes(productId));

                if (!visible.length) {
                    $list.html(`<div class="stages-empty"><i class="mdi mdi-folder-outline"></i>${
                        groups.length ? 'No groups for this product yet - add one with Add Group.' : 'No groups yet - add one with Add Group.'
                    }</div>`);
                    return;
                }

                let shown = 0;

                const html = visible.map(group => {
                    const stages = term
                        ? group.stages.filter(s => s.name.toLowerCase().includes(term))
                        : group.stages;

                    // Searching - only the groups with a match.
                    if (term && !stages.length) return '';
                    shown++;

                    const rows = stages.map(stage => `
                        <tr>
                            <td class="col-order"><span class="stage-order">${stage.sort_order}</span></td>
                            <td class="col-name"><span class="stage-name">${escapeHtml(stage.name)}</span></td>
                            <td class="col-roles">${tags(stage.roles)}</td>
                            <td class="col-action">
                                <div class="action-btns">
                                    <button type="button" class="btn btn-sm btn-icon btn-edit editStageBtn" data-id="${stage.id}" title="Edit stage" aria-label="Edit stage">
                                        <i class="mdi mdi-pencil-box"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-remove deleteStageBtn" data-id="${stage.id}" title="Delete stage" aria-label="Delete stage">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`).join('');

                    const count = group.stages.length;

                    return `
                        <div class="stage-group-card">
                            <div class="stage-group-head">
                                <div class="stage-group-title">
                                    <span class="stage-group-order">#${group.sort_order}</span>
                                    <span class="stage-group-pill stage-${escapeHtml(group.color)}">
                                        <span class="color-dot stage-${escapeHtml(group.color)}"></span>${escapeHtml(group.name)}
                                    </span>
                                    ${tags(group.products)}
                                    <span class="stage-group-count">${count} ${count === 1 ? 'stage' : 'stages'}</span>
                                </div>
                                <div class="stage-group-actions">
                                    <button type="button" class="btn-stage-in-group addStageInGroupBtn" data-id="${group.id}">
                                        <i class="mdi mdi-plus"></i> Add Stage
                                    </button>
                                    <div class="action-btns">
                                        <button type="button" class="btn btn-sm btn-icon btn-edit editGroupBtn" data-id="${group.id}" title="Edit group" aria-label="Edit group">
                                            <i class="mdi mdi-pencil-box"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-icon btn-remove deleteGroupBtn" data-id="${group.id}" title="Delete group" aria-label="Delete group">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            ${stages.length ? `
                                <table class="table stage-table">
                                    <thead>
                                        <tr>
                                            <th class="col-order">Order</th>
                                            <th>Stage Name</th>
                                            <th>Roles</th>
                                            <th class="col-action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rows}</tbody>
                                </table>`
                                : '<div class="stage-group-empty">No stages in this group yet.</div>'}
                        </div>`;
                }).join('');

                $list.html(shown ? html : '<div class="stages-empty"><i class="mdi mdi-magnify"></i>No matching stages found.</div>');
            }

            function findGroup(id) {
                return groups.find(g => g.id === Number(id)) || null;
            }

            function findStage(id) {
                for (const group of groups) {
                    const stage = group.stages.find(s => s.id === Number(id));
                    if (stage) return stage;
                }
                return null;
            }

            // ---- Form errors ----
            function clearErrors($form) {
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();
            }

            function showErrors($form, errors) {
                clearErrors($form);
                $.each(errors, function (field, messages) {
                    const name = field.split('.')[0];
                    const $group = $form.find(`[data-field="${name}"]`);
                    if (!$group.length || $group.find('.invalid-feedback').length) return;

                    $group.find('.form-control, .product-select-group').first().addClass('is-invalid');
                    $group.append(`<div class="invalid-feedback d-block">${escapeHtml(messages[0])}</div>`);
                });
            }

            $('#groupForm, #stageForm').on('input change', 'input, select', function () {
                const $group = $(this).closest('[data-field]');
                $group.find('.is-invalid').removeClass('is-invalid');
                $group.find('.invalid-feedback').remove();
            });

            function submitForm($form, $btn, $modal) {
                if ($btn.hasClass('is-loading')) return;

                setBtnLoading($btn, true, 'Saving...');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: new FormData($form[0]),
                    processData: false,
                    contentType: false,
                    headers: { 'Accept': 'application/json' },
                    success: function (res) {
                        $modal.modal('hide');
                        load();
                        showToast('success', res.success || 'Saved.');
                    },
                    error: function (xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            showErrors($form, xhr.responseJSON.errors);
                        } else {
                            showToast('error', 'Something went wrong. Please try again.');
                        }
                    },
                    complete: function () {
                        setBtnLoading($btn, false);
                    }
                });
            }

            function confirmDelete(title, html) {
                return Swal.fire({
                    html: `
                        <div class="swal-delete-icon">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </div>
                        <h2 class="swal-delete-title">${title}</h2>
                        <p class="swal-delete-text">${html}</p>
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
                });
            }

            // Why a delete can't go ahead - the same soft dialog, one button.
            function blockedDialog(title, html) {
                return Swal.fire({
                    html: `
                        <div class="swal-delete-icon swal-icon-warning">
                            <i class="mdi mdi-alert-outline"></i>
                        </div>
                        <h2 class="swal-delete-title">${title}</h2>
                        <p class="swal-delete-text">${html}</p>
                    `,
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-leads-popup',
                        confirmButton: 'swal-btn-primary'
                    }
                });
            }

            function plural(count, one, many) {
                return `${count} ${count === 1 ? one : many}`;
            }

            function deleteRequest($btn, url, fallback, blockedTitle) {
                $btn.addClass('is-loading').find('i').attr('class', 'mdi mdi-loading mdi-spin');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _method: 'DELETE', _token: csrf },
                    headers: { 'Accept': 'application/json' },
                    success: function (res) {
                        load();
                        showToast('success', res.success || 'Deleted.');
                    },
                    error: function (xhr) {
                        $btn.removeClass('is-loading').find('i').attr('class', 'mdi mdi-delete');
                        const message = (xhr.responseJSON && xhr.responseJSON.message) || fallback;

                        // Refused (in use since the page loaded) - explained in a dialog.
                        if (xhr.status === 422) {
                            blockedDialog(blockedTitle, escapeHtml(message));
                            load();
                        } else {
                            showToast('error', message);
                        }
                    }
                });
            }

            // ---- Groups ----
            const $groupModal = $('#groupModal');
            const $groupForm = $('#groupForm');
            const $groupSaveBtn = $('#groupSaveBtn');

            function openGroupModal(group) {
                $groupForm[0].reset();
                clearErrors($groupForm);

                $('#groupModalTitle').text(group ? 'Edit Group' : 'Add Group');
                $groupSaveBtn.text(group ? 'Update' : 'Save').removeData('original-html');
                $groupForm.attr('action', group ? groupUpdateUrl.replace(':id', group.id) : groupStoreUrl);
                $groupForm.find('input[name="_method"]').val(group ? 'PUT' : 'POST');

                if (group) {
                    $('#groupName').val(group.name);
                    $('#groupOrder').val(group.sort_order);
                    $groupForm.find('input[name="color"]').each(function () {
                        this.checked = this.value === group.color;
                    });
                    $groupForm.find('input[name="products[]"]').each(function () {
                        this.checked = group.product_ids.includes(Number(this.value));
                    });
                } else {
                    // A new group starts with the product being viewed.
                    const productId = $product.val();
                    $groupForm.find('input[name="color"]').first().prop('checked', true);
                    if (productId) $groupForm.find(`input[name="products[]"][value="${productId}"]`).prop('checked', true);
                }

                $groupModal.modal('show');
            }

            $('#addGroupBtn').on('click', () => openGroupModal(null));

            $(document).on('click', '.editGroupBtn', function () {
                const group = findGroup($(this).data('id'));
                if (group) openGroupModal(group);
            });

            $groupForm.on('submit', function (e) {
                e.preventDefault();
                submitForm($groupForm, $groupSaveBtn, $groupModal);
            });

            $(document).on('click', '.deleteGroupBtn', function () {
                const $btn = $(this);
                if ($btn.hasClass('is-loading')) return;

                const group = findGroup($btn.data('id'));
                if (!group) return;

                if (group.stages.length) {
                    blockedDialog(
                        "This group can't be deleted",
                        `<strong>${escapeHtml(group.name)}</strong> still has ${plural(group.stages.length, 'stage', 'stages')}. Delete ${group.stages.length === 1 ? 'it' : 'them'} or move ${group.stages.length === 1 ? 'it' : 'them'} to another group first.`
                    );
                    return;
                }

                confirmDelete('Delete this group?', `<strong>${escapeHtml(group.name)}</strong> will be permanently removed from Stage Management.`)
                    .then(result => {
                        if (result.isConfirmed) deleteRequest($btn, groupDestroyUrl.replace(':id', group.id), 'Unable to delete this group.', "This group can't be deleted");
                    });
            });

            // ---- Stages ----
            const $stageModal = $('#stageModal');
            const $stageForm = $('#stageForm');
            const $stageSaveBtn = $('#stageSaveBtn');
            const $stageGroup = $('#stageGroup');
            let editingStage = null;

            // Groups to pick from - the product being viewed first; a name
            // used for more than one product shows its products.
            function fillGroupOptions(selectedId) {
                const productId = Number($product.val()) || null;
                const options = groups
                    .slice()
                    .sort((a, b) => {
                        if (productId) {
                            const diff = Number(!a.product_ids.includes(productId)) - Number(!b.product_ids.includes(productId));
                            if (diff) return diff;
                        }
                        return a.sort_order - b.sort_order;
                    })
                    .map(g => {
                        const shared = groups.filter(o => o.name.toLowerCase() === g.name.toLowerCase()).length > 1;
                        const label = shared ? `${g.name} (${g.products.join(', ')})` : g.name;
                        return `<option value="${g.id}">${escapeHtml(label)}</option>`;
                    });

                $stageGroup.html('<option value="">Select group</option>' + options.join(''));
                $stageGroup.val(selectedId ? String(selectedId) : '');
                showAppliesTo();
            }

            function showAppliesTo() {
                const group = findGroup($stageGroup.val());
                $('#stageAppliesTo').html(group
                    ? `<i class="mdi mdi-package-variant-closed"></i> Applies to ${tags(group.products)}`
                    : '');
            }

            $stageGroup.on('change', showAppliesTo);

            function openStageModal(stage, groupId) {
                editingStage = stage;
                $stageForm[0].reset();
                clearErrors($stageForm);

                $('#stageModalTitle').text(stage ? 'Edit Stage' : 'Add Stage');
                $stageSaveBtn.text(stage ? 'Update' : 'Save').removeData('original-html');
                $stageForm.attr('action', stage ? stageUpdateUrl.replace(':id', stage.id) : stageStoreUrl);
                $stageForm.find('input[name="_method"]').val(stage ? 'PUT' : 'POST');

                fillGroupOptions(stage ? stage.group_id : groupId);

                if (stage) {
                    $('#stageName').val(stage.name);
                    $('#stageOrder').val(stage.sort_order);
                    $stageForm.find('input[name="roles[]"]').each(function () {
                        this.checked = stage.role_ids.includes(Number(this.value));
                    });
                }

                $stageModal.modal('show');
            }

            $('#addStageBtn').on('click', function () {
                if (!groups.length) {
                    showToast('error', 'Add a group first - every stage belongs to a group.');
                    return;
                }
                openStageModal(null, null);
            });

            $(document).on('click', '.addStageInGroupBtn', function () {
                openStageModal(null, $(this).data('id'));
            });

            $(document).on('click', '.editStageBtn', function () {
                const stage = findStage($(this).data('id'));
                if (stage) openStageModal(stage, null);
            });

            // A stage moved to another group goes to the end of it unless
            // an Order is given - so clear the old group's number.
            $stageGroup.on('change', function () {
                if (editingStage) {
                    $('#stageOrder').val(Number(this.value) === editingStage.group_id ? editingStage.sort_order : '');
                }
            });

            $stageForm.on('submit', function (e) {
                e.preventDefault();
                submitForm($stageForm, $stageSaveBtn, $stageModal);
            });

            $(document).on('click', '.deleteStageBtn', function () {
                const $btn = $(this);
                if ($btn.hasClass('is-loading')) return;

                const stage = findStage($btn.data('id'));
                if (!stage) return;

                if (stage.leads_count > 0) {
                    blockedDialog(
                        "This stage can't be deleted",
                        `${plural(stage.leads_count, 'lead is', 'leads are')} currently at <strong>${escapeHtml(stage.name)}</strong>. Move ${stage.leads_count === 1 ? 'it' : 'them'} to another stage first.`
                    );
                    return;
                }

                confirmDelete('Delete this stage?', `<strong>${escapeHtml(stage.name)}</strong> will no longer be offered in the Lead Stages dropdown. Past stage history keeps its name.`)
                    .then(result => {
                        if (result.isConfirmed) deleteRequest($btn, stageDestroyUrl.replace(':id', stage.id), 'Unable to delete this stage.', "This stage can't be deleted");
                    });
            });

            load();
        });
    });
})();
</script>
@endsection
