@extends('layout')
@section('title', 'Attendance')
@section('subtitle', 'Attendance')
@section('content')

<style>
.report-card { border: 1px solid #EEEDFE; border-radius: 12px; }
.report-card .card-header { background: #EEEDFE; border-bottom: 1px solid #AFA9EC; font-weight: 600; color: #26215C; }
#attendanceReport thead { background: #F7F6FE; color: #26215C; }

.view-breaks-btn { border-radius: 20px; font-weight: 600; }

/* Break details modal */
.break-modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 50px rgba(38,33,92,.25); }
.break-modal-header {
    background: linear-gradient(135deg, #7B6EF6 0%, #26215C 100%);
    color: #fff;
    border-bottom: none;
    padding: 1.25rem 1.5rem;
    align-items: flex-start;
}
.break-modal-header .modal-title { font-weight: 700; font-size: 1.05rem; margin-bottom: 2px; }
.break-modal-header .modal-title i { margin-right: 8px; }
.break-modal-subtitle { font-size: .82rem; opacity: .85; }
.break-modal-header .close { color: #fff; opacity: .85; text-shadow: none; }
.break-modal-header .close:hover { opacity: 1; }

.break-summary-strip { background: #F7F6FE; border-bottom: 1px solid #EEEDFE; padding: 14px 0; }
.break-summary-item { text-align: center; }
.break-summary-value { font-size: 1.35rem; font-weight: 700; color: #26215C; line-height: 1.2; }
.break-summary-label { font-size: .7rem; color: #8b86b8; text-transform: uppercase; letter-spacing: .05em; margin-top: 2px; }
.break-summary-divider { width: 1px; background: #E1DEF8; }

.break-list-wrap { max-height: 380px; overflow-y: auto; padding: 6px 1.5rem; }
.break-list-wrap::-webkit-scrollbar { width: 6px; }
.break-list-wrap::-webkit-scrollbar-thumb { background: #DAD5F5; border-radius: 6px; }

.break-item { display: flex; align-items: flex-start; gap: 14px; padding: 14px 0; border-bottom: 1px solid #F1F0FB; }
.break-item:last-child { border-bottom: none; }
.break-item-index {
    flex-shrink: 0; width: 30px; height: 30px; border-radius: 50%;
    background: #EEEDFE; color: #6C5CE7; font-weight: 700; font-size: .8rem;
    display: flex; align-items: center; justify-content: center;
}
.break-item-body { flex: 1; min-width: 0; }
.break-item-time { font-weight: 600; color: #26215C; font-size: .92rem; }
.break-item-time .arrow { color: #B7B1EA; margin: 0 6px; }
.break-duration-badge {
    display: inline-block; background: #EEEDFE; color: #6C5CE7;
    font-size: .7rem; font-weight: 700; padding: 2px 10px; border-radius: 12px; margin-left: 8px; vertical-align: middle;
}
.break-duration-badge.ongoing { background: #FFF1DB; color: #C57B00; }
.break-item-reason { color: #6c757d; font-size: .84rem; margin-top: 4px; }
.break-item-reason i { color: #B7B1EA; margin-right: 5px; }

.break-empty-state { text-align: center; padding: 48px 20px; color: #A9A3D6; }
.break-empty-state i { font-size: 2.2rem; display: block; margin-bottom: 10px; }
.break-empty-state span { font-size: .9rem; }
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

<div class="modal fade" id="breakDetailsModal" tabindex="-1" role="dialog" aria-labelledby="breakDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content break-modal-content">
            <div class="modal-header break-modal-header">
                <div>
                    <h5 class="modal-title" id="breakDetailsModalLabel"><i class="bi bi-cup-hot"></i>Break Details</h5>
                    <div class="break-modal-subtitle" id="breakModalDate"></div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="break-summary-strip d-flex align-items-center justify-content-around">
                <div class="break-summary-item">
                    <div class="break-summary-value" id="breakSummaryCount">0</div>
                    <div class="break-summary-label">Breaks Taken</div>
                </div>
                <div class="break-summary-divider" style="height:32px"></div>
                <div class="break-summary-item">
                    <div class="break-summary-value" id="breakSummaryTotal">0h 00m</div>
                    <div class="break-summary-label">Total Break Time</div>
                </div>
            </div>

            <div class="modal-body p-0">
                <div class="break-list-wrap" id="breakDetailsBody"></div>
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
            {
                data: 'break_count',
                orderable: false,
                className: 'text-center',
                render: function (count) {
                    return '<button type="button" class="btn btn-sm btn-outline-primary view-breaks-btn">'
                        + count + ' <i class="bi bi-eye"></i></button>';
                },
            },
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

    function escapeHtml(str) {
        return $('<div>').text(str || '-').html();
    }

    function showBreakDetails(breaks, date, totalDuration) {
        $('#breakModalDate').text(date);
        $('#breakSummaryCount').text(breaks ? breaks.length : 0);
        $('#breakSummaryTotal').text(totalDuration || '0h 00m');

        const $wrap = $('#breakDetailsBody').empty();

        if (!breaks || !breaks.length) {
            $wrap.append(
                '<div class="break-empty-state">'
                + '<i class="bi bi-emoji-smile"></i>'
                + '<span>No breaks taken on this day</span>'
                + '</div>'
            );
        } else {
            breaks.forEach(function (b, i) {
                const isOngoing = b.end === 'Ongoing';
                $wrap.append(
                    '<div class="break-item">'
                    + '<div class="break-item-index">' + (i + 1) + '</div>'
                    + '<div class="break-item-body">'
                    +   '<div class="break-item-time">'
                    +     '<i class="bi bi-clock"></i> '
                    +     escapeHtml(b.start)
                    +     '<span class="arrow">&rarr;</span>'
                    +     escapeHtml(b.end)
                    +     '<span class="break-duration-badge' + (isOngoing ? ' ongoing' : '') + '">'
                    +       (isOngoing ? 'Ongoing' : escapeHtml(b.duration))
                    +     '</span>'
                    +   '</div>'
                    +   '<div class="break-item-reason"><i class="bi bi-chat-left-text"></i>' + escapeHtml(b.reason) + '</div>'
                    + '</div>'
                    + '</div>'
                );
            });
        }

        $('#breakDetailsModal').modal('show');
    }

    $('#attendanceReport tbody').on('click', '.view-breaks-btn', function () {
        const rowData = table.row($(this).closest('tr')).data();
        showBreakDetails(rowData.breaks, rowData.date, rowData.break_duration);
    });

    $('#filterBtn').on('click', loadReport);
    loadReport();
});
</script>
@endsection