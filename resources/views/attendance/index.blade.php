@extends('layout')
@section('title', 'Attendance')
@section('subtitle', 'Attendance')
@section('content')

<style>
.report-card { border: 1px solid #EEEDFE; border-radius: 12px; }
.report-card .card-header { background: #EEEDFE; border-bottom: 1px solid #AFA9EC; font-weight: 600; color: #26215C; }
#attendanceReport thead { background: #F7F6FE; color: #26215C; }
</style>

<div class="row">
    <div class="col-12">
        <div class="report-card card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span>My Attendance Report</span>
                <div class="d-flex align-items-center gap-2">
                    <input type="date" id="filterFrom" class="form-control form-control-sm" style="width:140px">
                    <span class="text-muted small">to</span>
                    <input type="date" id="filterTo" class="form-control form-control-sm" style="width:140px">
                    <button class="btn btn-sm btn-outline-secondary" id="filterBtn">Filter</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="attendanceReport" class="table table-striped align-middle mb-0" style="width:100%">
                        <thead>
                            <tr>
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
    const table = $('#attendanceReport').DataTable({
        columns: [
            { data: 'date' },
            { data: 'time_in' },
            { data: 'time_out' },
            { data: 'break_duration' },
            { data: 'total_hours' },
        ],
        order: [[0, 'desc']],
        pageLength: 10,
    });

    function loadReport() {
        $.get('{{ route('attendance.report') }}', {
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
