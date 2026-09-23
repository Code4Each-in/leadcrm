@extends('layout')

@section('title', 'Applications')
@section('subtitle', 'Application Management')

@section('content')

<style>
    /* ==========================================================
       Header
       ========================================================== */
    #leadsCard .leads-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 1px solid #eef0f3;
        margin-bottom: 22px;
        gap: 14px;
        flex-wrap: wrap;
    }

    #leadsCard .leads-eyebrow {
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

    #leadsCard .leads-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    #leadsCard .leads-header h4 {
        font-weight: 700;
        font-size: 27px;
        color: #1a1f2b;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }

    #leadsCard .leads-header p {
        color: #8a92a3;
        font-size: 13.5px;
        margin: 0;
    }

    #leadsCard .btn-add-lead {
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

    #leadsCard .btn-add-lead:hover {
        background: #5b52e8;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(108, 99, 255, 0.34);
        color: #fff;
    }

    /* ==========================================================
       Stat cards - status stats + one card per product, same look
       ========================================================== */
    #leadsCard .stats-row {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    #leadsCard .stat-card {
        flex: 1 1 150px;
        background: #fff;
        border: 1px solid #eef0f3;
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    #leadsCard .stat-card .stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    #leadsCard .stat-card.stat-total .stat-icon {
        background: #eef0ff;
        color: #5b52e0;
    }

    #leadsCard .stat-card.stat-draft .stat-icon {
        background: #fff3cd;
        color: #8a6d00;
    }

    #leadsCard .stat-card.stat-published .stat-icon {
        background: #d4f4e2;
        color: #1a7a4c;
    }

    #leadsCard .stat-card.stat-product .stat-icon {
        background: #e7f1ff;
        color: #2264d1;
    }

    #leadsCard .product-leads-heading {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #8a92a3;
        margin-bottom: 10px;
    }

    #leadsCard .stats-row-products {
        margin-bottom: 20px;
    }

    #leadsCard .stat-card .stat-value {
        font-size: 20px;
        font-weight: 700;
        color: #1a1f2b;
        line-height: 1.2;
    }

    #leadsCard .stat-card .stat-label {
        font-size: 11.5px;
        font-weight: 500;
        color: #8a92a3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ==========================================================
       Toolbar (search + filters + reset + page length)
       ========================================================== */
    #leadsCard .leads-toolbar {
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

    #leadsCard .toolbar-search {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1 1 220px;
        min-width: 160px;
    }

    #leadsCard .toolbar-search i {
        color: #a4aab5;
        font-size: 18px;
    }

    #leadsCard .toolbar-search input[type="search"] {
        border: none;
        outline: none;
        font-size: 13.5px;
        width: 100%;
        color: #384153;
        background: transparent;
    }

    #leadsCard .toolbar-divider {
        width: 1px;
        height: 24px;
        background: #eef0f3;
        flex-shrink: 0;
    }

    #leadsCard .toolbar-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        background: #f4f5f8;
        border: 1px solid #e9ebef;
        border-radius: 10px;
        padding: 6px 10px 6px 12px;
    }

    #leadsCard .toolbar-filter label {
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5164;
        white-space: nowrap;
        margin: 0px;
    }

    #leadsCard .toolbar-filter select {
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

    #leadsCard .toolbar-filter select:hover {
        border-color: #c7cbd4;
    }

    #leadsCard .toolbar-filter select:focus {
        outline: none;
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
    }

    #leadsCard .btn-reset-filters {
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

    #leadsCard .btn-reset-filters:hover {
        background: #f4f5f7;
        color: #384153;
    }

    #leadsCard .btn-reset-filters i {
        font-size: 15px;
    }

    #leadsCard .toolbar-length {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
        font-size: 12.5px;
        color: #a4aab5;
        flex-shrink: 0;
    }

    #leadsCard .toolbar-length select {
        border: 1px solid #e6e8ec;
        border-radius: 8px;
        background: #f8f9fb;
        font-size: 13px;
        color: #384153;
        padding: 5px 8px;
    }

    /* ==========================================================
       Lead ID badge
       ========================================================== */
    #applicationsTable .lead-id-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 24px;
        padding: 0 8px;
        border-radius: 6px;
        background: #eef0ff;
        color: #4a43d1;
        font-size: 12.5px;
        font-weight: 700;
    }

    .expand-chevron {
        display: none;
        color: #8a92a3;
        font-size: 19px;
        transition: transform 0.2s ease;
    }

    /* ==========================================================
       Table shell - scrollbar hidden on desktop, dark headings

       NOTE: .table-responsive is the OUTER wrapper in the markup;
       #applicationsTable_wrapper is generated by DataTables INSIDE
       it. So the selector must be #leadsCard .table-responsive
       (table-responsive as the ancestor), not the other way round -
       "#applicationsTable_wrapper .table-responsive" never matches
       anything, which is why the native scrollbar was showing.
       ========================================================== */
    #leadsCard .table-responsive {
        overflow-x: auto;
        scrollbar-width: none;
    }

    #leadsCard .table-responsive::-webkit-scrollbar {
        display: none;
    }

    #applicationsTable {
        margin-bottom: 0;
        width: 100% !important;
    }

    #applicationsTable thead th {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #1a1f2b;
        border-top: none;
        border-bottom: 2px solid #eef0f3;
        padding: 12px 10px;
        white-space: nowrap;
    }

    #applicationsTable tbody td {
        padding: 11px 10px;
        font-size: 13px;
        color: #384153;
        vertical-align: middle;
    }

    #applicationsTable.table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #fbfbfd;
    }

    #applicationsTable tbody tr:hover {
        background-color: #f4f6fb;
    }

    /* ==========================================================
       Status - inline editable toggle switch (Draft <-> Published)
       Replaces the old native <select>, which rendered with
       inconsistent browser chrome once styled as a pill.
       ========================================================== */
    #applicationsTable .status-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;
    }

    #applicationsTable .status-toggle input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    #applicationsTable .status-toggle .toggle-track {
        position: relative;
        width: 36px;
        height: 20px;
        border-radius: 20px;
        background: #fbd469;
        flex-shrink: 0;
        transition: background 0.15s ease;
    }

    #applicationsTable .status-toggle .toggle-track::after {
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

    #applicationsTable .status-toggle input:checked + .toggle-track {
        background: #34c777;
    }

    #applicationsTable .status-toggle input:checked + .toggle-track::after {
        transform: translateX(16px);
    }

    #applicationsTable .status-toggle input:focus-visible + .toggle-track {
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.28);
    }

    #applicationsTable .status-toggle .toggle-label {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.2px;
        color: #8a6d00;
        white-space: nowrap;
        min-width: 62px;
    }

    #applicationsTable .status-toggle input:checked ~ .toggle-label {
        color: #1a7a4c;
    }

    #applicationsTable .status-toggle.is-loading {
        opacity: 0.55;
        pointer-events: none;
    }

    /* A user who can't edit this lead (LeadPolicy::update()) sees the
       status as plain, non-interactive text instead of a toggle. */
    #applicationsTable .status-toggle.is-readonly {
        opacity: 0.55;
        cursor: default;
        pointer-events: none;
    }

    /* ==========================================================
       Action buttons
       ========================================================== */
    #applicationsTable .action-btns {
        display: flex;
        gap: 6px;
        flex-wrap: nowrap;
    }

    #applicationsTable .action-btns .btn-icon {
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

    #applicationsTable .action-btns .btn-icon i {
        font-size: 15px;
        margin: 0;
    }

    #applicationsTable .action-btns .btn-view {
        background-color: #eaf2ff;
        color: #2264d1;
    }

    #applicationsTable .action-btns .btn-edit {
        background-color: #eef0ff;
        color: #5b52e0;
    }

    #applicationsTable .action-btns .btn-remove {
        background-color: #fdeaea;
        color: #d33a3a;
    }

    #applicationsTable .action-btns .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(26, 31, 43, 0.14);
    }

    /* Icon tooltip - dark bubble, positioned via JS off the
       trigger's own bounding rect (see script below). Same style
       used for the Users page's Product-info tooltip, reused here
       for the View/Edit/Delete action icons and the Status toggle
       so tooltips look identical across the whole app. */
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
    #applicationsTable_wrapper .dataTables_info {
        font-size: 12.5px;
        color: #8a92a3;
        padding-top: 14px;
    }

    #applicationsTable_wrapper .dataTables_paginate {
        padding-top: 10px;
    }

    #applicationsTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        margin: 0 2px;
        border: 1px solid transparent !important;
        /* padding: 5px 11px !important; */
        font-size: 13px;
    }

    #applicationsTable_wrapper .dataTables_paginate .paginate_button.current {
        background: #6c63ff !important;
        color: #fff !important;
        border-color: #6c63ff !important;
    }

    #applicationsTable_wrapper .dataTables_processing {
        background: rgba(255, 255, 255, 0.85);
        font-size: 13px;
        color: #6c63ff;
        font-weight: 500;
    }

    #applicationsTable_wrapper td.dataTables_empty {
        padding: 48px 0;
        color: #8a92a3;
        font-size: 13.5px;
    }

    /* ==========================================================
       Mobile
       ========================================================== */
    @media (max-width: 768px) {

        #leadsCard .leads-header {
            flex-direction: column;
        }

        #leadsCard .btn-add-lead {
            width: 100%;
            justify-content: center;
        }

        #leadsCard .stats-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            flex-wrap: unset;
        }

        #leadsCard .stat-card {
            flex: unset;
            width: 100%;
            padding: 12px 14px;
        }

        #leadsCard .stats-row-products {
            grid-template-columns: repeat(2, 1fr);
        }

        #leadsCard .stat-card.stat-product {
            padding: 10px 12px;
            gap: 10px;
        }

        #leadsCard .stat-card.stat-product .stat-icon {
            width: 32px;
            height: 32px;
            font-size: 15px;
        }

        #leadsCard .stat-card.stat-product .stat-value {
            font-size: 17px;
        }

        #leadsCard .product-leads-heading {
            margin-top: 4px;
        }

        #leadsCard .leads-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        #leadsCard .toolbar-search {
            flex-basis: auto;
            width: 100%;
        }

        #leadsCard .toolbar-divider {
            display: none;
        }

        #leadsCard .toolbar-filter {
            width: 100%;
            justify-content: space-between;
        }

        #leadsCard .toolbar-filter select {
            flex: 1;
            margin-left: 8px;
        }

        #leadsCard .btn-reset-filters {
            width: 100%;
            justify-content: center;
        }

        #leadsCard .toolbar-length {
            margin-left: 0;
            width: 100%;
            justify-content: space-between;
        }

        #leadsCard .table-responsive {
            overflow-x: hidden;
        }

        #applicationsTable_wrapper .dataTables_paginate,
        #applicationsTable_wrapper .dataTables_info {
            text-align: center;
        }

        /* ==========================================================
           Mobile card layout for the leads table.

           Built with plain CSS/JS instead of the DataTables
           Responsive extension (that plugin needs a CDN script to
           load in a specific order relative to DataTables core,
           which kept failing silently in this app). This approach
           doesn't depend on any extra script at all.

           Each <tr> becomes a card. Column order in the markup is
           fixed (Lead ID, Product, Company Name, Company Number,
           Customer Name, Status, Action), so `order` is used to
           rearrange what's shown without touching the underlying
           table/columns config:
             - Company Name (3rd column) is the always-visible
               heading, pulled to the top.
             - Action (7th/last column) always shows right under it.
             - Everything else only appears - in its original
               column order - once the row is tapped and gets the
               .row-expanded class.
           ========================================================== */
        #applicationsTable thead {
            display: none;
        }

        #applicationsTable,
        #applicationsTable tbody {
            display: block;
            width: 100%;
        }

        #applicationsTable tbody tr {
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid #eef0f3;
            border-radius: 12px;
            margin-bottom: 10px;
            padding: 12px 14px;
            cursor: pointer;
        }

        #applicationsTable tbody td {
            display: none;
            border: none !important;
            padding: 0 !important;
        }

        #applicationsTable tbody td.dataTables_empty {
            display: block !important;
            text-align: center;
            color: #8a92a3;
            font-size: 13px;
            padding: 6px 0 !important;
        }

        #applicationsTable tbody td.col-heading {
            display: flex;
            order: 1;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 14.5px;
            font-weight: 700;
            color: #1a1f2b;
            padding-bottom: 10px !important;
        }

        #applicationsTable tbody td.col-action {
            display: flex;
            order: 2;
            gap: 8px;
        }

        .expand-chevron {
            display: inline-block;
        }

        #applicationsTable tbody tr.row-expanded .expand-chevron {
            transform: rotate(180deg);
        }

        #applicationsTable tbody tr.row-expanded td.col-listable {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 0 0 !important;
            margin-top: 9px;
            border-top: 1px solid #f1f2f6 !important;
            font-size: 13px;
        }

        #applicationsTable tbody td:nth-child(1).col-listable { order: 3; }
        #applicationsTable tbody td:nth-child(2).col-listable { order: 4; }
        #applicationsTable tbody td:nth-child(4).col-listable { order: 5; }
        #applicationsTable tbody td:nth-child(5).col-listable { order: 6; }
        #applicationsTable tbody td:nth-child(6).col-listable { order: 7; }

        #applicationsTable tbody td.col-listable::before {
            content: attr(data-label);
            font-weight: 600;
            color: #8a92a3;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            flex-shrink: 0;
        }

        #applicationsTable tbody td.col-listable {
            color: #384153;
            text-align: right;
        }

        /* Right-aligning col-listable above was inheriting into the
           status toggle's own label too, so "Draft" (shorter than
           "Published") looked like it had a stray gap before it.
           Reset it here so the switch and its text always sit
           snugly together regardless of which word is showing. */
        #applicationsTable .status-toggle {
            text-align: left;
        }
    }

    /* ==========================================================
       SweetAlert2 - delete confirmation, restyled to match the
       app instead of the default SweetAlert look
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
</style>

<div class="row">

    <div class="col-md-12 grid-margin stretch-card">

        <div class="card" id="leadsCard">

            <div class="card-body">

                {{-- Header --}}
                <div class="leads-header">

                    <div>
                        <div class="leads-eyebrow">
                            Lead Management
                        </div>
                        <h4 class="card-title mb-1">
                            Leads
                        </h4>
                        <p>
                            Track and manage every lead across all products.
                        </p>
                    </div>

                    <div class="leads-header-actions">

                        <a
                            href="{{ route('leads.create') }}"
                            class="btn btn-primary btn-add-lead"
                        >
                            <i class="mdi mdi-plus"></i>
                            Add Lead
                        </a>

                        <button
                            type="button"
                            class="btn btn-csv-action"
                            id="openCsvTemplateModalBtn"
                        >
                            <i class="mdi mdi-download"></i>
                            Download CSV Template
                        </button>

                        <button
                            type="button"
                            class="btn btn-csv-action"
                            id="openCsvImportModalBtn"
                        >
                            <i class="mdi mdi-upload"></i>
                            Import CSV
                        </button>

                    </div>

                </div>

                {{--
                    Shared product-picker modal for "Download CSV
                    Template" / "Import CSV" - which product the user
                    picks drives the CSV template columns / which
                    leads the import will create, matching the same
                    product scoping already used on the manual
                    Add Lead form.
                --}}
                <div class="csv-modal-overlay" id="csvProductModal" style="display:none;">
                    <div class="csv-modal">

                        <div class="csv-modal-header">
                            <h5 id="csvProductModalTitle">Select a Product</h5>
                            <button type="button" class="csv-modal-close" id="csvProductModalClose" aria-label="Close">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>

                        <p class="csv-modal-description" id="csvProductModalDescription">
                            Choose which product this CSV is for.
                        </p>

                        <div class="csv-modal-products" id="csvProductModalProducts">
                            @foreach ($products as $product)
                                <label class="csv-product-option">
                                    <input
                                        type="radio"
                                        name="csv_product_id"
                                        value="{{ $product->id }}"
                                    >
                                    <span class="csv-product-button">
                                        {{ $product->name }}
                                    </span>
                                </label>
                            @endforeach

                            @if ($products->isEmpty())
                                <p class="text-muted mb-0">No products are available for your account.</p>
                            @endif
                        </div>

                        <div class="csv-modal-actions">
                            <a
                                href="#"
                                class="btn btn-primary px-4 disabled"
                                id="csvProductModalContinue"
                            >
                                Continue
                            </a>
                        </div>

                    </div>
                </div>

                {{--
                    Stat cards - fully dynamic from the controller.
                    Total / Draft / Published come from $totalLeadsCount,
                    $draftLeadsCount, $publishedLeadsCount.
                --}}
                <div class="stats-row">

                    <div class="stat-card stat-total">
                        <div class="stat-icon"><i class="mdi mdi-format-list-bulleted"></i></div>
                        <div>
                            <div class="stat-value" id="statTotalValue">{{ $totalLeadsCount ?? 0 }}</div>
                            <div class="stat-label">Total leads</div>
                        </div>
                    </div>

                    <div class="stat-card stat-draft">
                        <div class="stat-icon"><i class="mdi mdi-file-clock-outline"></i></div>
                        <div>
                            <div class="stat-value" id="statDraftValue">{{ $draftLeadsCount ?? 0 }}</div>
                            <div class="stat-label">Draft</div>
                        </div>
                    </div>

                    <div class="stat-card stat-published">
                        <div class="stat-icon"><i class="mdi mdi-check-circle-outline"></i></div>
                        <div>
                            <div class="stat-value" id="statPublishedValue">{{ $publishedLeadsCount ?? 0 }}</div>
                            <div class="stat-label">Published</div>
                        </div>
                    </div>

                </div>

                {{--
                    Per-product lead counts - one card per product, each
                    carrying a real leads_count computed in
                    LeadController@index, scoped the same way the table
                    itself is scoped. Kept in its own labeled row below
                    the main totals so it reads as "leads per product",
                    not just more of the same stats.
                --}}
                @if($products->count())
                    <div class="product-leads-heading">
                        Leads by Product
                    </div>

                    <div class="stats-row stats-row-products">

                        @foreach($products as $product)
                            <div class="stat-card stat-product">
                                <div class="stat-icon"><i class="mdi mdi-tag-outline"></i></div>
                                <div>
                                    <div class="stat-value">{{ $product->leads_count ?? 0 }}</div>
                                    <div class="stat-label">{{ $product->name }}</div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                @endif

                {{-- Toolbar: search + filters + reset + page length --}}
                <div class="leads-toolbar">

                    <div class="toolbar-search" id="toolbarSearchSlot">
                        <i class="mdi mdi-magnify"></i>
                        {{-- native DataTables search input is moved in here via JS --}}
                    </div>

                    <div class="toolbar-divider"></div>

                    <div class="toolbar-filter">
                        <label for="statusFilter">Status</label>
                        <select id="statusFilter">
                            <option value="">All</option>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>

                    <div class="toolbar-filter">
                        <label for="productFilter">Product</label>
                        <select id="productFilter">
                            <option value="">All</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
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

                <div class="table-responsive">

                    <table
                        id="applicationsTable"
                        class="table table-striped"
                    >

                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Company Name
                                </th>

                                <th>
                                    Company Number
                                </th>

                                <th>
                                    Customer Name
                                </th>

                                <th>
                                    Status
                                </th>


                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>
                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

/*
* Delete permission - Admin / Super Admin can delete a Draft or a
* Published lead; a normal user only a Draft (their own, enforced
* server-side in LeadController@destroy). Computed once here so both
* the row render below and the inline status-toggle handler use the
* exact same rule when deciding whether to show the Delete icon.
*/
const isAdmin = @json(auth()->user()->isAdminOrAbove());

/*
* Edit permission - matches LeadPolicy::update(): an Account
* Executive can never edit a published lead, even one they created.
* Computed once here so the row render below can decide whether to
* show the Edit icon (the actual enforcement is server-side in
* LeadController@edit / @update - this only hides a control the
* backend would reject anyway).
*/
const isAccountExecutive = @json(auth()->user()->isAe());

/*
* Runs `callback` once the page is ready to be manipulated - either
* immediately (if the DOM has already finished parsing) or once
* DOMContentLoaded fires.
*/
function onDomReady(callback)
{
    if (document.readyState !== 'loading') {

        callback();

    } else {

        document.addEventListener('DOMContentLoaded', callback);

    }
}


/*
* Polls until jQuery + DataTables core are both available, then
* runs `callback`. The mobile card layout below is built with our
* own CSS/JS (col-heading / col-listable / col-action + a click
* handler), not the DataTables Responsive extension - that plugin
* depended on a CDN script loading in the right order and proved
* unreliable, so it's no longer used here.
*/
function waitFor(check, callback)
{
    if (check()) {

        callback();

    } else {

        setTimeout(function () {

            waitFor(check, callback);

        }, 50);

    }
}


waitFor(
    function () {
        return typeof $ !== 'undefined' && $.fn && $.fn.dataTable;
    },
    function () {
        onDomReady(initApplicationsTable);
    }
);


/*
* Icon tooltip - same [data-tooltip] driven, position:fixed bubble
* used on the Users page (originally built for its Products
* field-info icon), reused here for the View/Edit/Delete action
* icons and the Status toggle so every tooltip in the app looks
* and behaves identically. Fixed positioning keeps it fully visible
* even though the trigger sits inside a scrolling table container,
* and :focus covers tap-to-show on touch devices for focusable
* triggers (buttons/links).
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

$(window).on('resize', hideFieldTooltip);
document.addEventListener('scroll', hideFieldTooltip, true);


function initApplicationsTable() {

        const dataTable = $('#applicationsTable').DataTable({

            processing: true,

            serverSide: true,

            pageLength: 10,

            ordering: true,

            // Newest lead first by default (column 0 is the numeric
            // Lead ID / id, so this is a true creation-order sort) -
            // applies regardless of whether the lead was created
            // manually or via CSV import, since both write into the
            // same leads table this DataTable reads from. Without
            // this, DataTables' own default (column 0 ascending)
            // would show the oldest lead first.
            order: [[0, 'desc']],

            autoWidth: false,

            ajax: {

                url: "{{ route('leads.index') }}",

                // Pull the current filter values in fresh on every
                // request (not just at init) so status/product filters
                // are actually sent to the server on every draw.
                data: function (d) {

                    d.status = $('#statusFilter').val();

                    d.product_id = $('#productFilter').val();

                }

            },

            columns: [

                {
                    data: 'id',

                    type: 'num',

                    render: function (id, type, row) {

                        return `<span class="lead-id-badge">${row.display_id}</span>`;

                    },

                    createdCell: function (td) {

                        $(td).addClass('col-listable').attr('data-label', 'Lead ID');

                    }
                },

                {
                    data: 'product',

                    render: function (data) {

                        return data
                            ? data.name
                            : 'N/A';

                    },

                    createdCell: function (td) {

                        $(td).addClass('col-listable').attr('data-label', 'Product');

                    }
                },

                {
                    data: 'company_business_name',

                    render: function (data) {

                        return `
                            <span class="company-name-text">${data || 'N/A'}</span>
                            <i class="mdi mdi-chevron-down expand-chevron"></i>
                        `;

                    },

                    createdCell: function (td) {

                        $(td).addClass('col-heading');

                    }
                },

                {
                    data: 'company_number',

                    render: function (data) {

                        return data || 'N/A';

                    },

                    createdCell: function (td) {

                        $(td).addClass('col-listable').attr('data-label', 'Company Number');

                    }
                },

                {
                    data: 'customer_name',

                    render: function (data) {

                        return data || 'N/A';

                    },

                    createdCell: function (td) {

                        $(td).addClass('col-listable').attr('data-label', 'Customer Name');

                    }
                },

                {
                    data: 'status',

                    render: function (data, type, row) {

                        const normalized = (data || 'draft').toLowerCase();
                        const isPublished = normalized === 'published';

                        // Publishing is one-way - once a lead is
                        // published, nobody (not even Admin/Super
                        // Admin) can move it back to draft, so the
                        // toggle becomes permanently read-only from
                        // that point on. This is a status rule, not a
                        // role-based one, so it applies regardless of
                        // who's looking at it.
                        const canToggleStatus = !isPublished;

                        return `
                            <label class="status-toggle${canToggleStatus ? '' : ' is-readonly'}" data-id="${row.id}" data-tooltip="Toggle between Draft and Published">
                                <input
                                    type="checkbox"
                                    class="status-toggle-input"
                                    data-id="${row.id}"
                                    ${isPublished ? 'checked' : ''}
                                    ${canToggleStatus ? '' : 'disabled'}
                                >
                                <span class="toggle-track"></span>
                                <span class="toggle-label">${isPublished ? 'Published' : 'Draft'}</span>
                            </label>
                        `;

                    },

                    createdCell: function (td) {

                        $(td).addClass('col-listable').attr('data-label', 'Status');

                    }
                },

                {
                    data: 'id',

                    orderable: false,

                    searchable: false,

                    render: function (id, type, row) {

                        // display_id (business Lead ID e.g. "1500-3", or
                        // the internal id for legacy leads) is used for
                        // navigation links.
                        const routeKey = row.display_id;

                        const isDraft = row.status &&
                            row.status.toLowerCase() === 'draft';

                        // Matches LeadPolicy::update() - an Account
                        // Executive can't edit a published lead, even one
                        // they created.
                        const canEdit = !(isAccountExecutive && !isDraft);

                        let buttons = `

                            <div class="action-btns">

                                <a
                                    href="/leads/${routeKey}"
                                    class="btn btn-sm btn-icon btn-view"
                                    data-tooltip="View"
                                >
                                    <i class="mdi mdi-eye"></i>
                                </a>

                                <a
                                    href="/leads/${routeKey}/edit"
                                    class="btn btn-sm btn-icon btn-edit"
                                    data-tooltip="Edit"
                                    ${canEdit ? '' : 'style="display:none;"'}
                                >
                                    <i class="mdi mdi-pencil-box"></i>
                                </a>

                        `;

                        /*
                        |--------------------------------------------------------------------------
                        | Delete Permission
                        |--------------------------------------------------------------------------
                        |
                        | Admin / Super Admin:
                        |     Can delete Draft + Published - always visible
                        |
                        | Normal User:
                        |     Can delete Draft only
                        |
                        | Always rendered (not conditionally) so the inline status
                        | toggle's change handler below can just show/hide it when
                        | the status changes, instead of redrawing the whole table -
                        | same approach as the header Delete icon on leads/show.blade.php.
                        */

                        const canDelete = isAdmin || isDraft;

                        buttons += `
                            <button
                                type="button"
                                class="btn btn-sm btn-icon btn-remove btn-delete"
                                data-id="${id}"
                                data-tooltip="Delete"
                                ${canDelete ? '' : 'style="display:none;"'}
                            >
                                <i class="mdi mdi-delete"></i>
                            </button>
                        `;

                        buttons += `</div>`;
                        return buttons;
                    },

                    createdCell: function (td) {

                        $(td).addClass('col-action');

                    }
                }

            ],

            /*
            * Runs once after the table's first draw. Relocates the
            * DataTables-generated search input and page-length select
            * into our custom toolbar slots. Moving the actual DOM
            * nodes (not cloning) keeps every existing event handler
            * intact - functionality is unchanged, only position.
            */
            initComplete: function () {

                $('#applicationsTable_filter input')
                    .attr('placeholder', 'Search leads...')
                    .appendTo('#toolbarSearchSlot');

                $('#applicationsTable_filter').remove();

                $('#applicationsTable_length select')
                    .appendTo('#toolbarLengthSlot');

                $('#applicationsTable_length').remove();

            },

            /*
            * Runs after every draw. With zero results (no records at
            * all, or a filter/search that matches nothing) there's
            * nothing to page through, so Previous/Next buttons just
            * sat there doing nothing - hide the pagination control
            * in that case. The "Showing X of Y" line stays, since it
            * still explains why (e.g. "filtered from 2 total").
            */
            drawCallback: function () {

                const hasRows = this.api().page.info().recordsDisplay > 0;

                $('#applicationsTable_wrapper .dataTables_paginate')
                    .toggle(hasRows);

            }

        });


        /*
        * Inline status change - fires when a row's status toggle is
        * flipped. PATCHes /leads/{id}/status (add this route - see
        * notes) then updates the toggle's label/color and reloads
        * the table in place so the Total/Draft/Published stat cards
        * stay correct.
        */
        $(document).on('change', '.status-toggle-input', function () {

            const $checkbox = $(this);

            const $wrapper = $checkbox.closest('.status-toggle');

            const $label = $wrapper.find('.toggle-label');

            const id = $checkbox.data('id');

            const newStatus = $checkbox.is(':checked') ? 'published' : 'draft';

            const previousStatus = newStatus === 'published' ? 'draft' : 'published';

            $wrapper.addClass('is-loading');

            $.ajax({

                url: `/leads/${id}/status`,

                type: 'PATCH',

                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },

                success: function (response) {

                    $label.text(newStatus === 'published' ? 'Published' : 'Draft');

                    // Delete icon - same isAdmin || isDraft rule the row
                    // was rendered with, applied live so a normal user
                    // sees it vanish the instant they publish a lead (and
                    // reappear if they draft it again), without waiting
                    // on the table reload below (which often doesn't
                    // even run - see the comment on filterExcludesRow).
                    const $row = $wrapper.closest('tr');

                    $row.find('.btn-delete')
                        .toggle(isAdmin || newStatus === 'draft');

                    // Edit icon - same LeadPolicy::update() rule the row
                    // was rendered with (Account Executive loses edit
                    // access the instant their own lead is published),
                    // applied live for the same reason as the delete
                    // icon above.
                    $row.find('.btn-edit')
                        .toggle(!(isAccountExecutive && newStatus !== 'draft'));

                    // Toggle itself - publishing is one-way, so once
                    // a lead is published nobody can flip it back to
                    // draft (matches the read-only state the row would
                    // render with on a fresh load - see canToggleStatus
                    // above).
                    const becomesReadOnly = newStatus === 'published';

                    $wrapper
                        .toggleClass('is-readonly', becomesReadOnly)
                        .find('.status-toggle-input')
                        .prop('disabled', becomesReadOnly);

                    // Only redraw the table if the active status
                    // filter would now hide or reveal this row (e.g.
                    // filtering by "Draft" and this lead just got
                    // published), or if a Multiple Site lead saved as
                    // a draft was just expanded into its full batch of
                    // site leads (see LeadController::
                    // expandMultisiteBatch()) - a single-row DOM
                    // update can't show the new rows that creates, so
                    // the table needs to be reloaded from the server
                    // instead of a manual page refresh. Otherwise skip
                    // the reload - with a long list, reloading on
                    // every toggle used to redraw the whole table,
                    // close whichever mobile card the user had
                    // expanded, and drop them back wherever the reload
                    // happened to land, forcing them to hunt for the
                    // row again. The label above already reflects the
                    // change either way.
                    const currentStatusFilter = $('#statusFilter').val();

                    const filterExcludesRow = currentStatusFilter
                        && currentStatusFilter !== newStatus;

                    if (filterExcludesRow || (response && response.expanded)) {

                        dataTable.ajax.reload(null, false);

                    }

                    // Refresh the Total/Draft/Published stat cards in
                    // place - the server returns fresh counts scoped
                    // the same way the table itself is scoped.
                    if (response && response.counts) {

                        $('#statTotalValue').text(response.counts.total);

                        $('#statDraftValue').text(response.counts.draft);

                        $('#statPublishedValue').text(response.counts.published);

                    }

                    // Soft, non-blocking toast (auto-dismisses) instead
                    // of a full modal, since this is a quick inline edit.
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response && response.message
                            ? response.message
                            : 'Status updated.',
                        showConfirmButton: false,
                        timer: 2200,
                        timerProgressBar: true
                    });

                },

                error: function () {

                    $checkbox.prop('checked', previousStatus === 'published');

                    $label.text(previousStatus === 'published' ? 'Published' : 'Draft');

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Could not update the status. Please try again.',
                        showConfirmButton: false,
                        timer: 2800,
                        timerProgressBar: true
                    });

                },

                complete: function () {

                    $wrapper.removeClass('is-loading');

                }

            });

        });


        /*
        * Status / Product filters - re-draw the table (page reset
        * to 1) whenever either select changes. dataTable.draw()
        * re-runs the ajax.data() callback above, so the current
        * filter values are picked up and sent to the server.
        */
        $('#statusFilter, #productFilter').on('change', function () {

            dataTable.draw();

        });


        /*
        * Reset filters - clears the filter selects back to defaults
        * and re-draws the table so the reset actually takes effect.
        */
        $('#resetFiltersBtn').on('click', function () {

            $('#statusFilter').val('');

            $('#productFilter').val('');

            dataTable.draw();

        });


        /*
        * MOBILE CARD EXPAND/COLLAPSE - tapping a row reveals its
        * col-listable fields (Lead ID, Product, Company Number,
        * Customer Name, Status). Ignored on desktop since all
        * columns are already visible there (see the CSS media
        * query). Taps on the action buttons or the status switch
        * are excluded so they keep working normally instead of
        * also toggling the card.
        */
        $(document).on('click', '#applicationsTable tbody tr', function (e) {

            if ($(e.target).closest('.col-action, .status-toggle').length) {
                return;
            }

            $(this).toggleClass('row-expanded');

        });


        /*
        * DELETE - SweetAlert confirm + AJAX DELETE request
        */
        $(document).on('click', '.btn-delete', function () {

            const id = $(this).data('id');

            const table = $('#applicationsTable').DataTable();

            // Pull the row's own data so the dialog can name the
            // actual lead being deleted instead of a generic message.
            const rowData = table.row($(this).closest('tr')).data();

            const leadLabel = rowData && (rowData.company_business_name || rowData.customer_name)
                ? (rowData.company_business_name || rowData.customer_name)
                : 'This lead';

            // Minimal HTML-escape since leadLabel is interpolated
            // straight into the dialog's markup below.
            const escapedLeadLabel = $('<div>').text(leadLabel).html();

            Swal.fire({
                html: `
                    <div class="swal-delete-icon">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </div>
                    <h2 class="swal-delete-title">Delete this lead?</h2>
                    <p class="swal-delete-text">
                        <strong>${escapedLeadLabel}</strong> will be permanently
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

                $.ajax({

                    url: `/leads/${id}`,

                    type: 'DELETE',

                    data: {
                        _token: '{{ csrf_token() }}'
                    },

                    success: function () {

                        table.ajax.reload(null, false);

                        // Soft, non-blocking toast (auto-dismisses) instead
                        // of a full modal, matching the status-toggle flow.
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'The Lead has been deleted.',
                            showConfirmButton: false,
                            timer: 2200,
                            timerProgressBar: true
                        });

                    },

                    error: function () {

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Something went wrong while deleting. Please try again.',
                            showConfirmButton: false,
                            timer: 2800,
                            timerProgressBar: true
                        });

                    }

                });

            });

        });

}

// ============================================================
// CSV Template / Import - product picker modal
// ============================================================
(function () {

    const modal = document.getElementById('csvProductModal');
    const modalTitle = document.getElementById('csvProductModalTitle');
    const modalDescription = document.getElementById('csvProductModalDescription');
    const continueBtn = document.getElementById('csvProductModalContinue');
    const productRadios = () => document.querySelectorAll('input[name="csv_product_id"]');

    const templateBtn = document.getElementById('openCsvTemplateModalBtn');
    const importBtn = document.getElementById('openCsvImportModalBtn');

    let currentMode = null;

    const routes = {
        template: @json(route('leads.csv.template', ['product' => '__ID__'])),
        import: @json(route('leads.import.show', ['product' => '__ID__'])),
    };

    function openModal(mode) {

        if (!modal) {
            return;
        }

        currentMode = mode;

        productRadios().forEach(function (radio) {
            radio.checked = false;
        });

        continueBtn.classList.add('disabled');
        continueBtn.setAttribute('href', '#');

        if (mode === 'template') {
            modalTitle.textContent = 'Download CSV Template';
            modalDescription.textContent = 'Select a product to download its Lead CSV template.';
            continueBtn.textContent = 'Download Template';
        } else {
            modalTitle.textContent = 'Import CSV';
            modalDescription.textContent = 'Select a product - all leads in the uploaded CSV will be created under it.';
            continueBtn.textContent = 'Continue';
        }

        modal.style.display = 'flex';
    }

    function closeModal() {
        if (modal) {
            modal.style.display = 'none';
        }
    }

    if (templateBtn) {
        templateBtn.addEventListener('click', function () {
            openModal('template');
        });
    }

    if (importBtn) {
        importBtn.addEventListener('click', function () {
            openModal('import');
        });
    }

    const closeBtn = document.getElementById('csvProductModalClose');

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    document.addEventListener('change', function (e) {

        if (e.target.name !== 'csv_product_id') {
            return;
        }

        const productId = e.target.value;
        const url = (currentMode === 'template' ? routes.template : routes.import)
            .replace('__ID__', productId);

        continueBtn.classList.remove('disabled');
        continueBtn.setAttribute('href', url);
    });

    if (continueBtn) {
        continueBtn.addEventListener('click', function (e) {
            if (continueBtn.classList.contains('disabled')) {
                e.preventDefault();
            }
        });
    }

})();

</script>

<style>
    .leads-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-csv-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        border: 1px solid #e2e5eb;
        color: #6c7280;
        font-weight: 500;
        font-size: 13.5px;
        padding: 9px 18px;
        border-radius: 9px;
        transition: background 0.12s ease, color 0.12s ease, border-color 0.12s ease;
    }

    .btn-csv-action:hover,
    .btn-csv-action:focus {
        background: #f4f5f7;
        color: #384153;
        border-color: #c9d1e3;
    }

    @media (max-width: 575px) {
        .leads-header-actions {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
        }

        .leads-header-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }

    .csv-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(20, 22, 30, 0.45);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .csv-modal {
        background: #fff;
        border-radius: 14px;
        padding: 24px;
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 18px 48px rgba(0, 0, 0, 0.22);
    }

    .csv-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .csv-modal-header h5 {
        margin: 0;
        font-weight: 700;
        color: #1a1f2b;
        font-size: 18px;
    }

    .csv-modal-close {
        border: none;
        background: transparent;
        font-size: 1.2rem;
        color: #8a92a3;
        line-height: 1;
        cursor: pointer;
    }

    .csv-modal-close:hover {
        color: #384153;
    }

    .csv-modal-description {
        color: #8a92a3;
        font-size: 13.5px;
        margin-bottom: 18px;
    }

    .csv-modal-products {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 22px;
    }

    .csv-product-option {
        position: relative;
        margin: 0;
        cursor: pointer;
    }

    .csv-product-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .csv-product-button {
        display: flex;
        align-items: center;
        min-height: 46px;
        padding: 0 16px;
        background: #fbfbfd;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        color: #384153;
        font-size: 0.925rem;
        font-weight: 500;
        transition: border-color 0.15s ease, background-color 0.15s ease, color 0.15s ease;
    }

    .csv-product-option:hover .csv-product-button {
        border-color: #6c63ff;
        background: rgba(108, 99, 255, 0.04);
    }

    .csv-product-option input:checked + .csv-product-button {
        background: rgba(108, 99, 255, 0.09);
        border-color: #6c63ff;
        color: #6c63ff;
    }

    .csv-modal-actions {
        display: flex;
        justify-content: flex-end;
    }

    .csv-modal-actions .btn {
        border-radius: 9px;
        font-weight: 500;
    }

    .csv-modal-actions .btn.disabled {
        opacity: 0.5;
        pointer-events: none;
    }
</style>

@endsection