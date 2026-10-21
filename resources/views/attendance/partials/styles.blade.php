{{-- Shared by attendance/index and attendance/admin. Header, toolbar,
     table, DataTables chrome and mobile card layout are the same
     pattern (and values) as Login Logs / Users, scoped to
     #attendanceCard. --}}
<style>
    /* ==========================================================
       Header
       ========================================================== */
    #attendanceCard .att-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 1px solid #eef0f3;
        margin-bottom: 22px;
        gap: 14px;
        flex-wrap: wrap;
    }

    #attendanceCard .att-eyebrow {
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

    #attendanceCard .att-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    #attendanceCard .att-page-header h4 {
        font-weight: 700;
        font-size: 27px;
        color: #1a1f2b;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }

    #attendanceCard .att-page-header p {
        color: #8a92a3;
        font-size: 13.5px;
        margin: 0;
    }

    /* My Attendance / All Employees switch (Admin/Super Admin only) -
       same chip style as the Knowledge Base category filter. */
    #attendanceCard .att-tabs {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    #attendanceCard .att-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid #eeeef5;
        background: #fff;
        color: #6f6f80;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    #attendanceCard .att-tab i {
        font-size: 15px;
    }

    #attendanceCard .att-tab:hover {
        border-color: #d9d6ff;
        color: #4b49ac;
        text-decoration: none;
    }

    #attendanceCard .att-tab.active {
        background: #4b49ac;
        border-color: #4b49ac;
        color: #fff;
    }

    /* ==========================================================
       Toolbar (search + filters + reset + page length)
       ========================================================== */
    #attendanceCard .att-toolbar {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        border: 1px solid #eef0f3;
        border-radius: 12px;
        padding: 10px 16px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    #attendanceCard .toolbar-search {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1 1 200px;
        min-width: 160px;
    }

    #attendanceCard .toolbar-search i {
        color: #a4aab5;
        font-size: 18px;
    }

    #attendanceCard .toolbar-search input[type="search"] {
        border: none;
        outline: none;
        font-size: 13.5px;
        width: 100%;
        color: #384153;
        background: transparent;
    }

    #attendanceCard .toolbar-divider {
        width: 1px;
        height: 24px;
        background: #eef0f3;
        flex-shrink: 0;
    }

    #attendanceCard .toolbar-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        background: #f4f5f8;
        border: 1px solid #e9ebef;
        border-radius: 10px;
        padding: 6px 10px 6px 12px;
    }

    #attendanceCard .toolbar-filter label {
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5164;
        white-space: nowrap;
        margin: 0;
    }

    #attendanceCard .toolbar-filter select,
    #attendanceCard .toolbar-filter input[type="date"] {
        border: 1px solid #dcdfe6;
        border-radius: 7px;
        background: #fff;
        font-size: 13px;
        font-weight: 500;
        color: #1a1f2b;
        padding: 6px 10px;
        cursor: pointer;
        min-width: 0;
    }

    #attendanceCard .toolbar-filter select {
        padding: 6px 26px 6px 10px;
        max-width: 200px;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 10 6'%3E%3Cpath fill='%236c7280' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 9px center;
        background-size: 9px;
    }

    #attendanceCard .toolbar-filter select:hover,
    #attendanceCard .toolbar-filter input[type="date"]:hover {
        border-color: #c7cbd4;
    }

    #attendanceCard .toolbar-filter select:focus,
    #attendanceCard .toolbar-filter input[type="date"]:focus {
        outline: none;
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
    }

    #attendanceCard .btn-reset-filters {
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

    #attendanceCard .btn-reset-filters:hover {
        background: #f4f5f7;
        color: #384153;
    }

    #attendanceCard .btn-reset-filters i {
        font-size: 15px;
    }

    #attendanceCard .toolbar-length {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
        font-size: 12.5px;
        color: #a4aab5;
        flex-shrink: 0;
    }

    #attendanceCard .toolbar-length select {
        border: 1px solid #e6e8ec;
        border-radius: 8px;
        background: #f8f9fb;
        font-size: 13px;
        color: #384153;
        padding: 5px 8px;
    }

    /* ==========================================================
       Table
       ========================================================== */
    #attendanceCard .table-responsive {
        overflow-x: auto;
        scrollbar-width: none;
    }

    #attendanceCard .table-responsive::-webkit-scrollbar {
        display: none;
    }

    #attendanceCard .att-table {
        margin-bottom: 0;
        width: 100% !important;
    }

    #attendanceCard .att-table thead th {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #1a1f2b;
        border-top: none;
        border-bottom: 2px solid #eef0f3;
        padding: 12px 10px;
        white-space: nowrap;
    }

    #attendanceCard .att-table tbody td {
        padding: 11px 10px;
        font-size: 13px;
        color: #384153;
        vertical-align: middle;
    }

    #attendanceCard .att-table.table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #fbfbfd;
    }

    #attendanceCard .att-table tbody tr:hover {
        background-color: #f4f6fb;
    }

    #attendanceCard .att-strong {
        font-weight: 600;
        color: #1a1f2b;
    }

    #attendanceCard .att-muted {
        color: #a4aab5;
    }

    /* Secondary line under a mobile card heading (e.g. the date under
       the employee's name) - the desktop table has its own column. */
    #attendanceCard .att-heading-sub {
        display: none;
    }

    /* Break count - pill button, same badge language as Login Logs'
       device pills. */
    #attendanceCard .view-breaks-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        border: none;
        background: #eef0ff;
        color: #4b49ac;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
        transition: background 0.12s ease;
    }

    #attendanceCard .view-breaks-btn:hover {
        background: #e0e2ff;
    }

    #attendanceCard .view-breaks-btn i {
        font-size: 14px;
    }

    #attendanceCard .att-hours-pill {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        background: #d4f4e2;
        color: #1a7a4c;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    /* ==========================================================
       DataTables chrome (info + pagination)
       ========================================================== */
    #attendanceCard .dataTables_info {
        font-size: 12.5px;
        color: #8a92a3;
        padding-top: 14px;
    }

    #attendanceCard .dataTables_paginate {
        padding-top: 10px;
    }

    #attendanceCard .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        margin: 0 2px;
        border: 1px solid transparent !important;
        font-size: 13px;
    }

    #attendanceCard .dataTables_paginate .paginate_button.current {
        background: #6c63ff !important;
        color: #fff !important;
        border-color: #6c63ff !important;
    }

    #attendanceCard td.dataTables_empty {
        padding: 48px 0;
        color: #8a92a3;
        font-size: 13.5px;
    }

    #attendanceCard .expand-chevron {
        display: none;
        color: #8a92a3;
        font-size: 19px;
        transition: transform 0.2s ease;
    }

    /* ==========================================================
       Mobile - toolbar stacks, rows become tap-to-expand cards
       (same approach as Login Logs / Leads / Users).
       ========================================================== */
    @media (max-width: 768px) {

        #attendanceCard .att-page-header {
            flex-direction: column;
        }

        #attendanceCard .att-page-header h4 {
            font-size: 23px;
        }

        #attendanceCard .att-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        #attendanceCard .toolbar-search {
            flex-basis: auto;
            width: 100%;
        }

        #attendanceCard .toolbar-divider {
            display: none;
        }

        #attendanceCard .toolbar-filter {
            width: 100%;
            justify-content: space-between;
        }

        #attendanceCard .toolbar-filter select,
        #attendanceCard .toolbar-filter input[type="date"] {
            flex: 1;
            max-width: none;
            margin-left: 8px;
        }

        #attendanceCard .btn-reset-filters {
            width: 100%;
            justify-content: center;
        }

        #attendanceCard .toolbar-length {
            margin-left: 0;
            width: 100%;
            justify-content: space-between;
        }

        #attendanceCard .table-responsive {
            overflow-x: hidden;
        }

        #attendanceCard .dataTables_paginate,
        #attendanceCard .dataTables_info {
            text-align: center;
        }

        #attendanceCard .att-table thead {
            display: none;
        }

        #attendanceCard .att-table,
        #attendanceCard .att-table tbody {
            display: block;
            width: 100%;
        }

        #attendanceCard .att-table tbody tr {
            display: flex;
            flex-direction: column;
            background: #fff !important;
            border: 1px solid #eef0f3;
            border-radius: 12px;
            margin-bottom: 10px;
            padding: 12px 14px;
            cursor: pointer;
        }

        #attendanceCard .att-table tbody td {
            display: none;
            border: none !important;
            padding: 0 !important;
        }

        #attendanceCard .att-table tbody td.dataTables_empty {
            display: block !important;
            text-align: center;
            color: #8a92a3;
            font-size: 13px;
            padding: 6px 0 !important;
        }

        #attendanceCard .att-table tbody td.col-heading {
            display: flex;
            order: 1;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 14.5px;
        }

        #attendanceCard .expand-chevron {
            display: inline-block;
        }

        #attendanceCard .att-heading-sub {
            display: block;
            font-size: 12px;
            font-weight: 400;
            color: #8a92a3;
        }

        #attendanceCard .att-table tbody tr.row-expanded .expand-chevron {
            transform: rotate(180deg);
        }

        /* Detail rows sort after the heading (order: 1). Without an
           explicit order they default to 0 and jump above the date /
           employee name as soon as the card is expanded. */
        #attendanceCard .att-table tbody td.col-listable {
            order: 2;
        }

        #attendanceCard .att-table tbody tr.row-expanded td.col-listable {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 0 0 !important;
            margin-top: 9px;
            border-top: 1px solid #f1f2f6 !important;
            font-size: 13px;
            text-align: right;
        }

        #attendanceCard .att-table tbody td.col-listable::before {
            content: attr(data-label);
            font-weight: 600;
            color: #8a92a3;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            flex-shrink: 0;
            text-align: left;
        }
    }

    /* ==========================================================
       Break details modal
       ========================================================== */
    /* The theme gives every .modal-dialog margin-top: 100px on top of
       Bootstrap's centred min-height: calc(100% - 20px), so the dialog
       was ~90px taller than the viewport and .modal showed a scrollbar.
       It also fixes .modal-content at 700px wide. */
    #breakDetailsModal .modal-dialog {
        max-width: 560px;
        margin: 1.75rem auto;
        min-height: calc(100% - 3.5rem);
    }

    #breakDetailsModal .modal-content {
        width: 100%;
    }

    #breakDetailsModal .modal-dialog .modal-content .modal-header {
        padding: 18px 22px;
    }

    .break-modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 50px rgba(26, 31, 43, .22); }
    .break-modal-header {
        background: #fff;
        border-bottom: 1px solid #eef0f3;
        padding: 18px 22px;
        align-items: flex-start;
    }
    .break-modal-header .modal-title { display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 16px; color: #1a1f2b; margin-bottom: 2px; }
    .break-modal-header .modal-title i { color: #4b49ac; font-size: 20px; }
    .break-modal-subtitle { font-size: 12.5px; color: #8a92a3; }
    .break-modal-header .close { color: #8a92a3; opacity: 1; text-shadow: none; }
    .break-modal-header .close:hover { color: #1a1f2b; }

    .break-summary-strip { background: #f7f8fc; border-bottom: 1px solid #eef0f3; padding: 14px 0; }
    .break-summary-item { text-align: center; flex: 1; }
    .break-summary-value { font-size: 1.35rem; font-weight: 700; color: #1a1f2b; line-height: 1.2; }
    .break-summary-label { font-size: 11px; font-weight: 600; color: #8a92a3; text-transform: uppercase; letter-spacing: .4px; margin-top: 2px; }
    .break-summary-divider { width: 1px; height: 32px; background: #e2e5f0; }

    /* Only long break lists scroll, inside the modal, with a slim bar. */
    .break-list-wrap { max-height: min(380px, calc(100vh - 260px)); overflow-y: auto; overflow-x: hidden; padding: 6px 22px; scrollbar-width: thin; scrollbar-color: #dcdff0 transparent; }
    .break-list-wrap::-webkit-scrollbar { width: 6px; }
    .break-list-wrap::-webkit-scrollbar-thumb { background: #dcdff0; border-radius: 6px; }

    .break-item { display: flex; align-items: flex-start; gap: 14px; padding: 14px 0; border-bottom: 1px solid #f1f2f6; }
    .break-item:last-child { border-bottom: none; }
    .break-item-index {
        flex-shrink: 0; width: 30px; height: 30px; border-radius: 50%;
        background: #eef0ff; color: #4b49ac; font-weight: 700; font-size: 12px;
        display: flex; align-items: center; justify-content: center;
    }
    .break-item-body { flex: 1; min-width: 0; }
    .break-item-time { display: flex; align-items: center; flex-wrap: wrap; gap: 4px 6px; font-weight: 600; color: #1a1f2b; font-size: 14px; }
    .break-item-time .mdi { color: #a4aab5; }
    .break-duration-badge {
        display: inline-block; background: #eef0ff; color: #4b49ac;
        font-size: 11.5px; font-weight: 700; padding: 2px 10px; border-radius: 12px; margin-left: 4px;
    }
    .break-duration-badge.ongoing { background: #fff4db; color: #b77900; }
    .break-item-reason { display: flex; gap: 6px; color: #6c7280; font-size: 13px; margin-top: 4px; overflow-wrap: anywhere; }
    .break-item-reason i { color: #a4aab5; }
    .break-item-notes { display: flex; gap: 6px; color: #8a92a3; font-size: 12px; font-style: italic; margin-top: 2px; overflow-wrap: anywhere; }
    .break-item-notes i { color: #a4aab5; }

    .break-empty-state { text-align: center; padding: 48px 20px; color: #8a92a3; }
    .break-empty-state i { font-size: 2.2rem; display: block; margin-bottom: 10px; color: #c7cbd4; }
    .break-empty-state span { font-size: 13.5px; }

    @media (max-width: 575px) {
        #breakDetailsModal .modal-dialog { margin: 12px; min-height: calc(100% - 24px); }
        .break-list-wrap { padding: 6px 16px; max-height: calc(100vh - 240px); }
        #breakDetailsModal .modal-dialog .modal-content .modal-header { padding: 16px; }
    }
</style>
