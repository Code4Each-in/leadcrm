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
        padding: 5px 11px !important;
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
            overflow-x: auto;
            scrollbar-width: thin;
            -webkit-overflow-scrolling: touch;
        }

        #leadsCard .table-responsive::-webkit-scrollbar {
            display: block;
            height: 4px;
        }

        #applicationsTable_wrapper .dataTables_paginate,
        #applicationsTable_wrapper .dataTables_info {
            text-align: center;
        }
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

                    <a
                        href="{{ route('leads.create') }}"
                        class="btn btn-primary btn-add-lead"
                    >
                        <i class="mdi mdi-plus"></i>
                        Add Lead
                    </a>

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
                                    Lead ID
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

function waitForJQuery(callback)
{
    if (typeof $ !== 'undefined') {

        callback();

    } else {

        setTimeout(function () {

            waitForJQuery(callback);

        }, 50);

    }
}


waitForJQuery(function () {

    document.addEventListener('DOMContentLoaded', function () {

        const dataTable = $('#applicationsTable').DataTable({

            processing: true,

            serverSide: true,

            pageLength: 10,

            ordering: true,

            responsive: true,

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

                    render: function (id) {

                        return `<span class="lead-id-badge">${id}</span>`;

                    }
                },

                {
                    data: 'product',

                    render: function (data) {

                        return data
                            ? data.name
                            : 'N/A';

                    }
                },

                {
                    data: 'company_business_name',

                    render: function (data) {

                        return data || 'N/A';

                    }
                },

                {
                    data: 'company_number',

                    render: function (data) {

                        return data || 'N/A';

                    }
                },

                {
                    data: 'customer_name',

                    render: function (data) {

                        return data || 'N/A';

                    }
                },

                {
                    data: 'status',

                    render: function (data, type, row) {

                        const normalized = (data || 'draft').toLowerCase();
                        const isPublished = normalized === 'published';

                        return `
                            <label class="status-toggle" data-id="${row.id}">
                                <input
                                    type="checkbox"
                                    class="status-toggle-input"
                                    data-id="${row.id}"
                                    ${isPublished ? 'checked' : ''}
                                >
                                <span class="toggle-track"></span>
                                <span class="toggle-label">${isPublished ? 'Published' : 'Draft'}</span>
                            </label>
                        `;

                    }
                },

                {
                    data: 'id',

                    orderable: false,

                    searchable: false,

                    render: function (id, type, row) {

                        let buttons = `

                            <div class="action-btns">

                                <a
                                    href="/leads/${id}"
                                    class="btn btn-sm btn-icon btn-view"
                                    title="View"
                                >
                                    <i class="mdi mdi-eye"></i>
                                </a>

                                <a
                                    href="/leads/${id}/edit"
                                    class="btn btn-sm btn-icon btn-edit"
                                    title="Edit"
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
                        |     Can delete Draft + Published
                        |
                        | Normal User:
                        |     Can delete Draft only
                        |
                        */

                        const isAdmin = @json(
                            in_array(
                                strtolower(auth()->user()->role->name),
                                ['admin', 'super admin']
                            )
                        );

                        const isDraft = row.status &&
                            row.status.toLowerCase() === 'draft';

                        if (isAdmin || isDraft) {
                            buttons += `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-icon btn-remove btn-delete"
                                    data-id="${id}"
                                    title="Delete"
                                >
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            `;
                        }

                        buttons += `</div>`;
                        return buttons;
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

                    dataTable.ajax.reload(null, false);

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
        * DELETE - SweetAlert confirm + AJAX DELETE request
        */
        $(document).on('click', '.btn-delete', function () {

            const id = $(this).data('id');

            const table = $('#applicationsTable').DataTable();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This Lead will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
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

                        Swal.fire(
                            'Deleted!',
                            'The Lead has been deleted.',
                            'success'
                        );

                        table.ajax.reload(null, false);

                    },

                    error: function () {

                        Swal.fire(
                            'Error',
                            'Something went wrong while deleting. Please try again.',
                            'error'
                        );

                    }

                });

            });

        });

    });

});

</script>

@endsection