@extends('layout')
@section('title', 'All Attendance')
@section('subtitle', 'Attendance - All Employees')
@section('content')

@include('attendance.partials.styles')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card" id="attendanceCard">
            <div class="card-body">

                @include('attendance.partials.header', [
                    'heading' => 'All Employees',
                    'description' => 'Time in/out, breaks and hours worked across the team.',
                ])

                <div class="att-toolbar">
                    <div class="toolbar-search" id="attToolbarSearch">
                        <i class="mdi mdi-magnify"></i>
                        {{-- native DataTables search input is moved in here via JS --}}
                    </div>

                    <div class="toolbar-divider"></div>

                    <div class="toolbar-filter">
                        <label for="filterEmployee">Employee</label>
                        <select id="filterEmployee">
                            <option value="">All Employees</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="toolbar-filter">
                        <label for="filterFrom">From</label>
                        <input type="date" id="filterFrom">
                    </div>

                    <div class="toolbar-filter">
                        <label for="filterTo">To</label>
                        <input type="date" id="filterTo">
                    </div>

                    <button type="button" id="attResetFilters" class="btn-reset-filters">
                        <i class="mdi mdi-refresh"></i>
                        Reset
                    </button>

                    <div class="toolbar-length" id="attToolbarLength">
                        Rows
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="adminAttendanceReport" class="table table-striped att-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Break Duration</th>
                                <th>Breaks</th>
                                <th>Total Hours</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@include('attendance.partials.breaks-modal')

@endsection

@section('js_scripts')
@include('attendance.partials.scripts')
<script>
$(function () {
    initAttendanceTable({
        tableId: 'adminAttendanceReport',
        url: @json(route('attendance.admin.report')),
        leadingColumns: [
            {
                data: 'employee',
                render: function (data, type, row) {
                    if (type !== 'display') {
                        return data;
                    }
                    return '<span><span class="att-strong">' + attendanceEscape(data) + '</span>'
                        + '<span class="att-heading-sub">' + attendanceEscape(row.date) + '</span></span>'
                        + '<i class="mdi mdi-chevron-down expand-chevron"></i>';
                },
                createdCell: function (td) { $(td).addClass('col-heading'); },
            },
            attendanceDateColumn(false),
        ],
        order: [[1, 'desc']],
        filters: { user_id: '#filterEmployee', from: '#filterFrom', to: '#filterTo' },
        searchPlaceholder: 'Search employees...',
        modalSubtitle: function (row) { return row.employee + ' - ' + row.date; },
    });
});
</script>
@endsection
