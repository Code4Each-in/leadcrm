@extends('layout')

@section('title', 'Applications')
@section('subtitle', 'Application Management')

@section('content')

<style>
    /* Status pill styling - scoped, table classes untouched */
    #applicationsTable .status-badge {
        display: inline-block;
        padding: 12px 18px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        text-transform: capitalize;
        letter-spacing: 0.3px;
    }

    #applicationsTable .status-draft {
        background-color: #fff3cd;
        color: #8a6d00;
    }

    #applicationsTable .status-published {
        background-color: #d4f4e2;
        color: #1a7a4c;
    }

    #applicationsTable .status-na {
        background-color: #eceef1;
        color: #6c757d;
    }

    /* Action buttons - icon-only, tighter, no wrap */
    #applicationsTable .action-btns {
        display: flex;
        gap: 6px;
        flex-wrap: nowrap;
    }

    #applicationsTable .action-btns .btn-icon {
        width: 40px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        line-height: 1;
    }

    #applicationsTable .action-btns .btn-icon i {
        font-size: 16px;
        margin: 0;
    }
</style>

<div class="row">

    <div class="col-md-12 grid-margin stretch-card">

        <div class="card">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <h4 class="card-title mb-1">
                            Leads
                        </h4>

                    </div>

                    <a
                        href="{{ route('leads.create') }}"
                        class="btn btn-primary"
                    >
                        <i class="mdi mdi-plus"></i>
                        Add Lead
                    </a>

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

        $('#applicationsTable').DataTable({

            processing: true,

            serverSide: true,

            pageLength: 10,

            ordering: true,

            responsive: true,

            ajax: "{{ route('leads.index') }}",

            columns: [

                {
                    data: 'id'
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

                    render: function (data) {

                        if (!data) {
                            return '<span class="status-badge status-na">N/A</span>';
                        }

                        const normalized = data.toLowerCase();

                        const badgeClass = normalized === 'published'
                            ? 'status-published'
                            : 'status-draft';

                        return `<span class="status-badge ${badgeClass}">${data}</span>`;

                    }
                },

                {
                    data: 'id',

                    orderable: false,

                    searchable: false,

                    render: function (id, type, row) {

                        return `

                            <div class="action-btns">

                                <a
                                    href="/leads/${id}"
                                    class="btn btn-sm btn-info btn-icon"
                                    title="View"
                                >
                                    <i class="mdi mdi-eye"></i>
                                </a>

                                <a
                                    href="/leads/${id}/edit"
                                    class="btn btn-sm btn-primary btn-icon"
                                    title="Edit"
                                >
                                    <i class="mdi mdi-pencil-box"></i>
                                </a>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger btn-icon btn-delete"
                                    data-id="${id}"
                                    title="Delete"
                                >
                                    <i class="mdi mdi-delete"></i>
                                </button>

                            </div>

                        `;

                    }
                }

            ]

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
