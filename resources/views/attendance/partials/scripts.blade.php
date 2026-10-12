{{-- Shared table setup for attendance/index and attendance/admin. Each
     page calls initAttendanceTable() with its own table, report URL and
     leading columns; the trailing attendance columns, toolbar wiring,
     break modal and mobile tap-to-expand are identical on both. --}}
<script>
function attendanceEscape(str) {
    return $('<div>').text(str == null || str === '' ? '-' : str).html();
}

function attendanceListable(label) {
    return function (td) {
        $(td).addClass('col-listable').attr('data-label', label);
    };
}

function showAttendanceBreaks(breaks, subtitle, totalDuration) {
    $('#breakModalDate').text(subtitle);
    $('#breakSummaryCount').text(breaks ? breaks.length : 0);
    $('#breakSummaryTotal').text(totalDuration || '0h 00m');

    const $wrap = $('#breakDetailsBody').empty();

    if (!breaks || !breaks.length) {
        $wrap.append(
            '<div class="break-empty-state">'
            + '<i class="mdi mdi-emoticon-happy-outline"></i>'
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
                +     '<i class="mdi mdi-clock-outline"></i>'
                +     attendanceEscape(b.start)
                +     '<i class="mdi mdi-arrow-right"></i>'
                +     attendanceEscape(b.end)
                +     '<span class="break-duration-badge' + (isOngoing ? ' ongoing' : '') + '">'
                +       (isOngoing ? 'Ongoing' : attendanceEscape(b.duration))
                +     '</span>'
                +   '</div>'
                +   '<div class="break-item-reason"><i class="mdi mdi-message-text-outline"></i>' + attendanceEscape(b.reason) + '</div>'
                + '</div>'
                + '</div>'
            );
        });
    }

    $('#breakDetailsModal').modal('show');
}

/**
 * options: tableId, url, leadingColumns (array of DataTables columns
 * placed before the shared ones), filters (map of query param ->
 * input selector), searchPlaceholder, modalSubtitle(row).
 */
function initAttendanceTable(options) {
    const $table = $('#' + options.tableId);

    const columns = options.leadingColumns.concat([
        {
            data: 'time_in',
            createdCell: attendanceListable('Time In'),
        },
        {
            data: 'time_out',
            render: function (data) {
                return data === '-' ? '<span class="att-muted">-</span>' : attendanceEscape(data);
            },
            createdCell: attendanceListable('Time Out'),
        },
        {
            data: 'break_duration',
            createdCell: attendanceListable('Break Duration'),
        },
        {
            data: 'break_count',
            orderable: false,
            render: function (count) {
                return '<button type="button" class="view-breaks-btn" title="View breaks">'
                    + '<i class="mdi mdi-coffee-outline"></i>' + count + '</button>';
            },
            createdCell: attendanceListable('Breaks'),
        },
        {
            data: 'total_hours',
            render: function (data) {
                return '<span class="att-hours-pill">' + attendanceEscape(data) + '</span>';
            },
            createdCell: attendanceListable('Total Hours'),
        },
    ]);

    const table = $table.DataTable({
        columns: columns,
        order: options.order,
        pageLength: 10,
        autoWidth: false,
        language: {
            emptyTable: 'No attendance records found',
            zeroRecords: 'No matching attendance records found',
        },

        // Move the native search/length controls into the toolbar -
        // same approach as Users / Login Logs.
        initComplete: function () {
            $('#' + options.tableId + '_filter input')
                .attr('placeholder', options.searchPlaceholder)
                .appendTo('#attToolbarSearch');
            $('#' + options.tableId + '_filter').remove();

            $('#' + options.tableId + '_length select').appendTo('#attToolbarLength');
            $('#' + options.tableId + '_length').remove();
        },

        drawCallback: function () {
            const hasRows = this.api().page.info().recordsDisplay > 0;
            $('#' + options.tableId + '_wrapper .dataTables_paginate').toggle(hasRows);
        },
    });

    const filterSelectors = Object.values(options.filters).join(', ');

    function loadReport() {
        const params = {};
        $.each(options.filters, function (param, selector) {
            params[param] = $(selector).val();
        });

        $.get(options.url, params, function (res) {
            table.clear().rows.add(res.data).draw();
        });
    }

    $(filterSelectors).on('change', loadReport);

    $('#attResetFilters').on('click', function () {
        $(filterSelectors).val('');
        $('#attToolbarSearch input').val('');
        table.search('');
        loadReport();
    });

    $table.on('click', '.view-breaks-btn', function (e) {
        e.stopPropagation(); // don't also toggle the mobile card
        const row = table.row($(this).closest('tr')).data();
        showAttendanceBreaks(row.breaks, options.modalSubtitle(row), row.break_duration);
    });

    // Mobile card expand/collapse - ignored on desktop where every
    // column is visible anyway (see the CSS media query).
    $table.on('click', 'tbody tr', function () {
        $(this).toggleClass('row-expanded');
    });

    loadReport();
}

/** Date column: shows "12 Oct 2026", sorts on the ISO date. */
function attendanceDateColumn(asHeading) {
    return {
        data: 'date',
        render: function (data, type, row) {
            if (type === 'sort' || type === 'type') {
                return row.date_sort;
            }
            return asHeading
                ? '<span class="att-strong">' + attendanceEscape(data) + '</span><i class="mdi mdi-chevron-down expand-chevron"></i>'
                : attendanceEscape(data);
        },
        createdCell: asHeading
            ? function (td) { $(td).addClass('col-heading'); }
            : attendanceListable('Date'),
    };
}
</script>
