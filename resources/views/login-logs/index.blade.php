@extends('layout')

@section('title', 'Login Logs')
@section('subtitle', 'Login Activity')

@section('content')

<style>
    /* ==========================================================
       Header - same pattern as Leads/Users/Roles
       ========================================================== */
    #loginLogsCard .logs-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 1px solid #eef0f3;
        margin-bottom: 22px;
        gap: 14px;
        flex-wrap: wrap;
    }

    #loginLogsCard .logs-eyebrow {
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

    #loginLogsCard .logs-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    #loginLogsCard .logs-header h4 {
        font-weight: 700;
        font-size: 27px;
        color: #1a1f2b;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }

    #loginLogsCard .logs-header p {
        color: #8a92a3;
        font-size: 13.5px;
        margin: 0;
    }

    /* ==========================================================
       Toolbar (filters + reset + page length) - same structure/
       classes as the Leads/Users/Roles toolbar
       ========================================================== */
    #loginLogsCard .logs-toolbar {
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

    #loginLogsCard .toolbar-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        background: #f4f5f8;
        border: 1px solid #e9ebef;
        border-radius: 10px;
        padding: 6px 10px 6px 12px;
    }

    #loginLogsCard .toolbar-filter label {
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5164;
        white-space: nowrap;
        margin: 0px;
    }

    #loginLogsCard .toolbar-filter select,
    #loginLogsCard .toolbar-filter input[type="date"] {
        border: 1px solid #dcdfe6;
        border-radius: 7px;
        background: #fff;
        font-size: 13px;
        font-weight: 500;
        color: #1a1f2b;
        padding: 6px 10px;
        cursor: pointer;
    }

    #loginLogsCard .toolbar-filter select {
        padding: 6px 26px 6px 10px;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 10 6'%3E%3Cpath fill='%236c7280' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 9px center;
        background-size: 9px;
    }

    #loginLogsCard .toolbar-filter select:hover,
    #loginLogsCard .toolbar-filter input[type="date"]:hover {
        border-color: #c7cbd4;
    }

    #loginLogsCard .toolbar-filter select:focus,
    #loginLogsCard .toolbar-filter input[type="date"]:focus {
        outline: none;
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
    }

    #loginLogsCard .btn-reset-filters {
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

    #loginLogsCard .btn-reset-filters:hover {
        background: #f4f5f7;
        color: #384153;
    }

    #loginLogsCard .btn-reset-filters i {
        font-size: 15px;
    }

    #loginLogsCard .toolbar-length {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
        font-size: 12.5px;
        color: #a4aab5;
        flex-shrink: 0;
    }

    #loginLogsCard .toolbar-length select {
        border: 1px solid #e6e8ec;
        border-radius: 8px;
        background: #f8f9fb;
        font-size: 13px;
        color: #384153;
        padding: 5px 8px;
    }

    /* ==========================================================
       Table shell - same look as #applicationsTable/#usersTable
       ========================================================== */
    #loginLogsCard .table-responsive {
        overflow-x: auto;
        scrollbar-width: none;
    }

    #loginLogsCard .table-responsive::-webkit-scrollbar {
        display: none;
    }

    #logsTable {
        margin-bottom: 0;
        width: 100% !important;
    }

    #logsTable thead th {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #1a1f2b;
        border-top: none;
        border-bottom: 2px solid #eef0f3;
        padding: 12px 10px;
        white-space: nowrap;
    }

    #logsTable tbody td {
        padding: 11px 10px;
        font-size: 13px;
        color: #384153;
        vertical-align: middle;
    }

    #logsTable.table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #fbfbfd;
    }

    #logsTable tbody tr:hover {
        background-color: #f4f6fb;
    }

    /* User cell - name + email, name doubles as the mobile card
       heading (same pattern as Users' name column). */
    #logsTable .log-user-name {
        font-weight: 600;
        color: #1a1f2b;
    }

    #logsTable .log-user-email {
        display: block;
        color: #8a92a3;
        font-size: 12px;
    }

    #logsTable .log-deleted-user {
        color: #a4aab5;
        font-style: italic;
    }

    /* Date/time cells - date and time inline on the same line, time
       muted and slightly smaller, on both desktop and mobile. Both
       spans are wrapped in .log-datetime so they count as a single
       flex item next to the mobile card's ::before label below -
       without that wrapper, the label/date/time were three separate
       flex children and justify-content:space-between spread all
       three apart, opening a large gap between date and time. */
    #logsTable .log-datetime {
        display: inline-flex;
        align-items: baseline;
    }

    #logsTable .log-date {
        color: #384153;
    }

    #logsTable .log-time {
        color: #8a92a3;
        font-size: 11.5px;
        margin-left: 6px;
        white-space: nowrap;
    }

    #logsTable .log-muted {
        color: #a4aab5;
    }

    /* Device - a colored pill per device type, same "status
       styling" language as the Draft/Published + Role badges used
       elsewhere in the app. */
    #logsTable .device-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    #logsTable .device-pill i {
        font-size: 14px;
    }

    #logsTable .device-pill.device-desktop {
        background: #eaf2ff;
        color: #2264d1;
    }

    #logsTable .device-pill.device-tablet {
        background: #eef0ff;
        color: #5b52e0;
    }

    #logsTable .device-pill.device-mobile {
        background: #d4f4e2;
        color: #1a7a4c;
    }

    #logsTable .device-pill.device-unknown {
        background: #f4f5f8;
        color: #6c7280;
    }

    /* Location - country flag + line */
    #logsTable .log-location .fi {
        margin-right: 6px;
        border-radius: 2px;
    }

    /* ==========================================================
       DataTables chrome (info + pagination)
       ========================================================== */
    #logsTable_wrapper .dataTables_info {
        font-size: 12.5px;
        color: #8a92a3;
        padding-top: 14px;
    }

    #logsTable_wrapper .dataTables_paginate {
        padding-top: 10px;
    }

    #logsTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        margin: 0 2px;
        border: 1px solid transparent !important;
        font-size: 13px;
    }

    #logsTable_wrapper .dataTables_paginate .paginate_button.current {
        background: #6c63ff !important;
        color: #fff !important;
        border-color: #6c63ff !important;
    }

    #logsTable_wrapper .dataTables_processing {
        background: rgba(255, 255, 255, 0.85);
        font-size: 13px;
        color: #6c63ff;
        font-weight: 500;
    }

    #logsTable_wrapper td.dataTables_empty {
        padding: 48px 0;
        color: #8a92a3;
        font-size: 13.5px;
    }

    /* Icon tooltip - dark bubble, positioned via JS off the
       trigger's own bounding rect (see script below). Same style
       used across Leads/Users/Roles for every icon-only action, so
       tooltips look identical throughout the app. */
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

    .expand-chevron {
        display: none;
        color: #8a92a3;
        font-size: 19px;
        transition: transform 0.2s ease;
    }

    /* ==========================================================
       Mobile
       ========================================================== */
    @media (max-width: 768px) {

        #loginLogsCard .logs-header {
            flex-direction: column;
        }

        #loginLogsCard .logs-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        #loginLogsCard .toolbar-filter {
            width: 100%;
            justify-content: space-between;
        }

        #loginLogsCard .toolbar-filter select,
        #loginLogsCard .toolbar-filter input[type="date"] {
            flex: 1;
            margin-left: 8px;
        }

        #loginLogsCard .btn-reset-filters {
            width: 100%;
            justify-content: center;
        }

        #loginLogsCard .toolbar-length {
            margin-left: 0;
            width: 100%;
            justify-content: space-between;
        }

        #loginLogsCard .table-responsive {
            overflow-x: hidden;
        }

        #logsTable_wrapper .dataTables_paginate,
        #logsTable_wrapper .dataTables_info {
            text-align: center;
        }

        /* Mobile card layout - same plain CSS/JS approach used on
           the Leads table. User (with email) is the always-visible
           heading; every other field only appears once the row is
           tapped and gets .row-expanded. */
        #logsTable thead {
            display: none;
        }

        #logsTable,
        #logsTable tbody {
            display: block;
            width: 100%;
        }

        #logsTable tbody tr {
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid #eef0f3;
            border-radius: 12px;
            margin-bottom: 10px;
            padding: 12px 14px;
            cursor: pointer;
        }

        #logsTable tbody td {
            display: none;
            border: none !important;
            padding: 0 !important;
        }

        #logsTable tbody td.dataTables_empty {
            display: block !important;
            text-align: center;
            color: #8a92a3;
            font-size: 13px;
            padding: 6px 0 !important;
        }

        #logsTable tbody td.col-heading {
            display: flex;
            order: 1;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 14.5px;
            padding-bottom: 10px !important;
        }

        .expand-chevron {
            display: inline-block;
        }

        #logsTable tbody tr.row-expanded .expand-chevron {
            transform: rotate(180deg);
        }

        #logsTable tbody tr.row-expanded td.col-listable {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 0 0 !important;
            margin-top: 9px;
            border-top: 1px solid #f1f2f6 !important;
            font-size: 13px;
        }

        #logsTable tbody td:nth-child(2).col-listable { order: 2; }
        #logsTable tbody td:nth-child(3).col-listable { order: 3; }
        #logsTable tbody td:nth-child(4).col-listable { order: 4; }
        #logsTable tbody td:nth-child(5).col-listable { order: 5; }
        #logsTable tbody td:nth-child(6).col-listable { order: 6; }
        #logsTable tbody td:nth-child(7).col-listable { order: 7; }

        #logsTable tbody td.col-listable::before {
            content: attr(data-label);
            font-weight: 600;
            color: #8a92a3;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            flex-shrink: 0;
        }

        #logsTable tbody td.col-listable {
            color: #384153;
            text-align: right;
        }

        #logsTable .log-user-email {
            text-align: left;
        }
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card" id="loginLogsCard">
            <div class="card-body">

                {{-- Header --}}
                <div class="logs-header">
                    <div>
                        <div class="logs-eyebrow">
                            Activity Log
                        </div>
                        <h4 class="card-title mb-1">
                            Login Logs
                        </h4>
                        <p>
                            Track user login and logout activity across every device.
                        </p>
                    </div>
                </div>

                {{-- Toolbar: filters + reset + page length --}}
                <div class="logs-toolbar">
                    <div class="toolbar-filter">
                        <label for="filterUser">User</label>
                        <select id="filterUser" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="toolbar-filter">
                        <label for="filterRole">Role</label>
                        <select id="filterRole" name="role_id">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="toolbar-filter">
                        <label for="filterLogin">Login</label>
                        <input
                            type="date"
                            id="filterLogin"
                            name="login"
                            max="{{ date('Y-m-d') }}"
                        >
                    </div>

                    <div class="toolbar-filter">
                        <label for="filterLogout">Logout</label>
                        <input
                            type="date"
                            id="filterLogout"
                            name="logout"
                            max="{{ date('Y-m-d') }}"
                        >
                    </div>

                    <button type="button" id="resetLogFilters" class="btn-reset-filters">
                        <i class="mdi mdi-refresh"></i>
                        Reset
                    </button>

                    <div class="toolbar-length" id="toolbarLengthSlot">
                        Rows
                        {{-- native DataTables length select is moved in here via JS --}}
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="logsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Login</th>
                                <th>Logout</th>
                                <th>Device</th>
                                <th>IP Address</th>
                                <th>Location</th>
                            </tr>
                        </thead>

                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

/*
* Icon tooltip - same [data-tooltip] driven, position:fixed bubble
* used on the Leads/Users/Roles pages, applied here so any icon-only
* element on this page (device pills) gets an identical tooltip.
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

let logsTable;

document.addEventListener('DOMContentLoaded', function () {

    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('user_id');

    if (userId) {
        $('#filterUser').val(userId);
    }

    logsTable = $('#logsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ordering: true,
        searching: false,
        autoWidth: false,
        order: [[2, 'desc']],

        ajax: {
            url: "{{ route('login-logs.index') }}",
            data: function (d) {
                d.user_id = $('#filterUser').val();
                d.role_id = $('#filterRole').val();
                d.login = $('#filterLogin').val();
                d.logout = $('#filterLogout').val();
            }
        },

        columns: [
            {
                data: 'user',
                name: 'user.name',
                render: function (data) {
                    if (!data) {
                        return `<span class="log-deleted-user">Deleted User</span>`;
                    }
                    return `
                        <span class="log-user-name">
                            ${data.name ?? ''}
                            <i class="mdi mdi-chevron-down expand-chevron"></i>
                        </span>
                        <span class="log-user-email">${data.email ?? ''}</span>
                    `;
                },
                createdCell: function (td) {
                    $(td).addClass('col-heading');
                }
            },
            {
                data: 'role',
                name: 'role',
                render: function (data) {
                    return data ? data.name : '<span class="log-muted">—</span>';
                },
                createdCell: function (td) {
                    $(td).addClass('col-listable').attr('data-label', 'Role');
                }
            },
            {
                data: 'login_at',
                name: 'login_at',
                render: function (data) {

                    if (!data) {
                        return '<span class="log-muted">—</span>';
                    }

                    const date = new Date(data);

                    return `
                        <span class="log-datetime">
                            <span class="log-date">${date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</span>
                            <span class="log-time">${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</span>
                        </span>
                    `;
                },
                createdCell: function (td) {
                    $(td).addClass('col-listable').attr('data-label', 'Login');
                }
            },
            {
                data: 'logout_at',
                name: 'logout_at',
                render: function (data) {

                    if (!data) {
                        return '<span class="log-muted">—</span>';
                    }

                    const date = new Date(data);

                    return `
                        <span class="log-datetime">
                            <span class="log-date">${date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</span>
                            <span class="log-time">${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</span>
                        </span>
                    `;
                },
                createdCell: function (td) {
                    $(td).addClass('col-listable').attr('data-label', 'Logout');
                }
            },
            {
                data: 'device',
                name: 'device',
                render: function (data) {

                    const normalized = (data || '').toLowerCase();

                    const map = {
                        desktop: { icon: 'mdi-monitor', label: 'Desktop' },
                        tablet:  { icon: 'mdi-tablet', label: 'Tablet' },
                        mobile:  { icon: 'mdi-cellphone', label: 'Mobile' }
                    };

                    const info = map[normalized] || { icon: 'mdi-help-circle-outline', label: data ? (data.charAt(0).toUpperCase() + data.slice(1)) : 'Unknown' };
                    const cssClass = map[normalized] ? `device-${normalized}` : 'device-unknown';

                    return `
                        <span class="device-pill ${cssClass}" data-tooltip="Logged in from a ${info.label.toLowerCase()} device">
                            <i class="mdi ${info.icon}"></i>
                            ${info.label}
                        </span>
                    `;
                },
                createdCell: function (td) {
                    $(td).addClass('col-listable').attr('data-label', 'Device');
                }
            },
            {
                data: 'ip_address',
                name: 'ip_address',
                render: function (data) {
                    return data || '<span class="log-muted">—</span>';
                },
                createdCell: function (td) {
                    $(td).addClass('col-listable').attr('data-label', 'IP Address');
                }
            },
            {
                data: 'location',
                orderable: false,
                render: function (data) {

                    if (!data || !data.line) {
                        return '<span class="log-muted">—</span>';
                    }

                    const flag = data.country_code
                        ? `<span class="fi fi-${data.country_code.toLowerCase()}"></span>`
                        : '';

                    return `<span class="log-location">${flag}${data.line}</span>`;
                },
                createdCell: function (td) {
                    $(td).addClass('col-listable').attr('data-label', 'Location');
                }
            }
        ],
        language: {
            emptyTable: 'No login activity found',
            zeroRecords: 'No matching login activity found'
        },

        /*
        * Relocates the DataTables-generated page-length select into
        * the custom toolbar slot, same approach used on the Leads/
        * Users/Roles pages. There's no free-text search box here -
        * the backend doesn't support one on this table yet - so
        * unlike those pages the native search input is just removed
        * outright instead of being relocated.
        */
        initComplete: function () {

            $('#logsTable_length select')
                .appendTo('#toolbarLengthSlot');

            $('#logsTable_length').remove();

        },

        drawCallback: function () {

            const hasRows = this.api().page.info().recordsDisplay > 0;

            $('#logsTable_wrapper .dataTables_paginate')
                .toggle(hasRows);

        }

    });

    $('#filterUser, #filterRole, #filterLogin, #filterLogout').on('change', function () {
        logsTable.draw();
    });

    $('#resetLogFilters').on('click', function () {

        $('#filterUser').val('');
        $('#filterRole').val('');
        $('#filterLogin').val('');
        $('#filterLogout').val('');

        const url = new URL(window.location.href);
        url.search = '';

        window.history.replaceState({}, document.title, url.pathname);

        logsTable.draw();
    });

    /*
    * MOBILE CARD EXPAND/COLLAPSE - tapping a row reveals its
    * col-listable fields, same approach used on the Leads/Users
    * tables. Ignored on desktop since every column is already
    * visible there (see the CSS media query).
    */
    $(document).on('click', '#logsTable tbody tr', function () {
        $(this).toggleClass('row-expanded');
    });

});

</script>
@endsection
