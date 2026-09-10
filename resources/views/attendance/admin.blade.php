@extends('layout')
@section('title', 'All Attendance')
@section('subtitle', 'Attendance - All Employees')
@section('content')

<style>
.report-card { border: 1px solid #EEEDFE; border-radius: 12px; }
.report-card .card-header { background: #EEEDFE; border-bottom: 1px solid #AFA9EC; font-weight: 600; color: #26215C; }
#adminAttendanceReport thead { background: #F7F6FE; color: #26215C; }
</style>

<div class="row">
    <div class="col-12">
        <div class="report-card card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span>All Employees Attendance</span>
                <div class="d-flex align-items-center gap-2">
                    <select id="filterEmployee" class="form-control form-control-sm" style="width:180px">
                        <option value="">All Employees</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" id="filterFrom" class="form-control form-control-sm" style="width:140px">
                    <span class="text-muted small">to</span>
                    <input type="date" id="filterTo" class="form-control form-control-sm" style="width:140px">
                    <button class="btn btn-sm btn-outline-secondary" id="filterBtn">Filter</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="adminAttendanceReport" class="table table-striped align-middle mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Break Duration</th>
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

@endsection

@section('js_scripts')
<script>
$(function () {
    const table = $('#adminAttendanceReport').DataTable({
        columns: [
            { data: 'employee' },
            { data: 'date' },
            { data: 'time_in' },
            { data: 'time_out' },
            { data: 'break_duration' },
            { data: 'total_hours' },
        ],
        order: [[1, 'desc']],
        pageLength: 10,
    });

    function loadReport() {
        $.get('{{ route('attendance.admin.report') }}', {
            user_id: $('#filterEmployee').val(),
            from: $('#filterFrom').val(),
            to: $('#filterTo').val(),
        }, function (res) {
            table.clear().rows.add(res.data).draw();
        });
    }

    $('#filterBtn').on('click', loadReport);
    loadReport();
});
</script>
@endsection