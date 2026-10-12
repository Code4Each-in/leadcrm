@extends('layout')
@section('title', 'Attendance')
@section('subtitle', 'Attendance')
@section('content')

@include('attendance.partials.styles')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card" id="attendanceCard">
            <div class="card-body">

                @include('attendance.partials.header', [
                    'heading' => 'My Attendance',
                    'description' => 'Your daily time in/out, breaks and hours worked. Punch in and out from the timer in the header.',
                ])

                <div class="att-toolbar">
                    <div class="toolbar-search" id="attToolbarSearch">
                        <i class="mdi mdi-magnify"></i>
                        {{-- native DataTables search input is moved in here via JS --}}
                    </div>

                    <div class="toolbar-divider"></div>

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
                    <table id="attendanceReport" class="table table-striped att-table">
                        <thead>
                            <tr>
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
        tableId: 'attendanceReport',
        url: @json(route('attendance.report')),
        leadingColumns: [attendanceDateColumn(true)],
        order: [[0, 'desc']],
        filters: { from: '#filterFrom', to: '#filterTo' },
        searchPlaceholder: 'Search attendance...',
        modalSubtitle: function (row) { return row.date; },
    });
});
</script>
@endsection
