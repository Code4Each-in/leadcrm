@extends('layout')
@section('title', 'Agencies')
@section('subtitle', 'Agencies')
@section('content')
<style>
    .required-label::after {
        content: ' *';
        color: red;
    }
    .btn-delete{
        height: 35px;
        align-content: center;
    }
    .edit-agency-btn{
        height: 35px;
    }
</style>
<style>

    .modal-dialog {
        max-width: 600px;   /* good desktop default */
        margin: 1.75rem auto;
    }

    /* Modal content scroll fix */
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    @media (max-width: 768px) {

        .modal-dialog {
            max-width: 92%;
            margin: 1rem auto;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header,
        .modal-body,
        .modal-footer {
            padding: 12px 14px;
        }
    }

    @media (max-width: 576px) {

        /* Full width feel */
        .modal-dialog {
            max-width: 100%;
            margin: 0;
            height: 100%;
        }

        .modal-content {
            height: 100%;
            border-radius: 0;
            display: flex;
            flex-direction: column;
        }

        .modal-body {
            flex: 1;
            max-height: none;
            overflow-y: auto;
        }

        /* Better spacing */
        .modal-header,
        .modal-body,
        .modal-footer {
            padding: 12px;
        }

        /* Stack buttons */
        .modal-footer {
            flex-direction: column;
        }

        .modal-footer .btn {
            width: 100%;
            margin-bottom: 8px;
        }

        .modal-footer .btn:last-child {
            margin-bottom: 0;
        }

        /* Title smaller */
        .modal-title,
        .modal-header h5 {
            font-size: 16px;
        }
    }


    /* Desktop default */
    .modal-dialog {
        max-width: 700px !important;
        margin: 1.75rem auto !important;
    }

    /* Body scroll fix */
    .modal-body {
        max-height: 70vh !important;
        overflow-y: auto !important;
    }


    @media (max-width: 700px) {

        .modal {
            padding: 0 !important;
        }

        .modal-dialog {
            width: 100% !important;
            max-width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            display: flex !important;
            justify-content: center;
            align-items: stretch !important;
            margin: 10px !important;
        }

        .modal-content {
            width: 100% !important;
            height: 70% !important;
            border-radius: 0 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .modal-header {
            flex-shrink: 0;
        }

        .modal-body {
            flex: 1 !important;
            max-height: none !important;
            overflow-y: auto !important;
        }

        .modal-footer {
            flex-shrink: 0;
            display: flex !important;
            flex-direction: column !important;
        }

        .modal-footer .btn {
            width: 100% !important;
            margin-bottom: 8px !important;
        }

        .modal-footer .btn:last-child {
            margin-bottom: 0 !important;
        }
    }
    /* Prevent table overflow issues */
    .dataTables_wrapper {
        width: 100% !important;
    }

    /* Make action buttons wrap on mobile */
    @media (max-width: 768px) {
        table.dataTable td {
            white-space: normal !important;
        }

        .btn {
            margin-bottom: 5px;
        }
    }
</style>
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">
                    <h4 class="card-title">Agencies</h4>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                        Add Agency
                    </button>
                </div>

            <div class="table-responsive">
                <table id="agenciesTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Agency Name</th>
                            <th>Primary Contact Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows loaded via AJAX -->
                    </tbody>
                </table>
            </div>

            </div>
        </div>
    </div>
</div>

<!-- CREATE MODAL -->
<div class="modal fade" id="createModal">
    <div class="modal-dialog">
        <form id="createAgencyForm" method="POST" action="{{ route('agencies.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add Agency</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label class="required-label">Agency Name</label>
                        <input type="text" name="agency_name" class="form-control" placeholder="Agency Name" >
                    </div>

                    <div class="form-group">
                        <label class="required-label">Primary Contact Name</label>
                        <input type="text" name="primary_contact_name" class="form-control" placeholder="Primary Contact Name" >
                    </div>

                    <div class="form-group">
                        <label class="required-label">Primary Email</label>
                        <input type="email" name="primary_email" class="form-control" placeholder="Primary Email" >
                    </div>

                    <div class="form-group">
                        <label class="required-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>

                    <!-- <div class="form-group">
                        <label>Logo</label>
                        <input type="file" name="logo" class="form-control">
                    </div> -->
                    <div class="form-group">
                        <label>Logo</label>
                        <div class="input-group">
                            <input type="file" id="logoinput" name="logo" style="display: none;">
                            <input type="text"  class="form-control file-upload-info" id="fileName" placeholder="Upload Logo" readonly>
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button"
                                    onclick="document.getElementById('logoinput').click();">
                                    Upload
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required-label">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Phone">
                    </div>
                    <div class="form-group">
                        <label class="required-label">Address</label>
                        <textarea name="address" class="form-control" placeholder="Address"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="required-label">City</label>
                        <input type="text" name="city" class="form-control" placeholder="City">
                    </div>
                    <div class="form-group">
                        <label class="required-label">State</label>
                        <input type="text" name="state" class="form-control" placeholder="State">
                    </div>

                    <div class="form-group">
                        <label class="required-label">Zip</label>
                        <input type="text" name="zip" class="form-control" placeholder="Zip">
                    </div>
                </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Save Agency</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>

            </div>
        </form>
    </div>
</div>

<!-- EDIT MODALS -->
@foreach($agencies as $agency)
<div class="modal fade" id="editModal{{ $agency->id }}">
    <div class="modal-dialog">
        <form class="editAgencyForm" method="POST" action="{{ route('agencies.update', $agency->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Agency</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label class="required-label">Agency Name</label>
                        <input type="text" name="agency_name" value="{{ $agency->agency_name }}" class="form-control" placeholder="Agency Name" >
                    </div>

                    <div class="form-group">
                        <label class="required-label">Primary Contact Name</label>
                        <input type="text" name="primary_contact_name" value="{{ $agency->primary_contact_name }}" class="form-control" placeholder="Primary Contact Name">
                    </div>

                    <div class="form-group">
                        <label class="required-label">Primary Email</label>
                        <input type="email" name="primary_email" value="{{ $agency->primary_email }}" class="form-control" placeholder="Primary Email">
                    </div>

                    <div class="form-group">
                        <label>Password (optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>


                     <div class="form-group">
                        <label>Logo</label>
                        <div class="input-group">
                            <input type="file" id="logoinput_{{ $agency->id}}" name="logo" style="display: none;">
                            <input type="text"  class="form-control file-upload-info" id="fileName_{{ $agency->id }}" placeholder="Upload Logo" readonly>
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button"
                                    onclick="document.getElementById('logoinput_{{ $agency->id }}').click();">
                                    Upload
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required-label">Phone</label>
                        <input type="text" name="phone" value="{{ $agency->phone }}" class="form-control" placeholder="Phone" >
                    </div>
                    <div class="form-group">
                        <label class="required-label">Address</label>
                        <textarea name="address" class="form-control">{{ $agency->address }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="required-label">City</label>
                        <input type="text" name="city" value="{{ $agency->city }}" class="form-control" placeholder="City" >
                    </div>
                    <div class="form-group">
                        <label class="required-label">State</label>
                        <input type="text" name="state" value="{{ $agency->state }}" class="form-control" placeholder="State" >
                    </div>
                    <div class="form-group">
                        <label class="required-label">Zip</label>
                        <input type="text" name="zip" value="{{ $agency->zip }}" class="form-control" placeholder="Zip" >
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<script>
$(document).ready(function () {
    $('#agenciesTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ordering: true,
            responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },

        autoWidth: false,
        scrollX: false,

        ajax: "{{ route('agencies.index') }}",
        columns: [
            { data: 'agency_name' },
            { data: 'primary_contact_name' },
            { data: 'phone' },
            { data: 'address' },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                    render: function(id){
                        let deleteUrl = "{{ route('agencies.delete', ':id') }}";
                        deleteUrl = deleteUrl.replace(':id', id);

                        return `
                            <button class="btn btn-sm btn-primary edit-agency-btn" data-id="${id}">
                                <i class="mdi mdi-pencil-box"></i> Edit
                            </button>
                            <a href="${deleteUrl}" class="btn btn-sm btn-danger btn-delete">
                                <i class="mdi mdi-delete"></i> Delete
                            </a>
                        `;
                    }
            }
        ]
    });

    // Delegated click for dynamically loaded buttons
    $('#agenciesTable').on('click', '.edit-agency-btn', function () {
        let id = $(this).data('id');
        $('#editModal' + id).modal('show');
    });
});
function waitForJQuery(callback) {
    if (typeof $ !== 'undefined') {
        callback();
    } else {
        setTimeout(function () { waitForJQuery(callback); }, 50);
    }
}

waitForJQuery(function () {

   $(document).on('change', 'input[type="file"]', function () {
        let id = this.id.replace('logoinput', 'fileName');
        let fileInput = document.getElementById(id);
        if (fileInput && this.files.length > 0) {
            fileInput.value = this.files[0].name;
        }
    });
    // Clear errors
    function clearErrors(modal) {
        $(modal).find('.is-invalid').removeClass('is-invalid');
        $(modal).find('.invalid-feedback').remove();
    }

    // Show Laravel errors in modal
    function showErrors(modal, errors) {
        $.each(errors, function (field, messages) {
            const el = $(modal).find('[name="' + field + '"]');
            el.addClass('is-invalid');
            el.after('<div class="invalid-feedback">' + messages[0] + '</div>');
        });
    }

    // CREATE modal — clear on open
    $('#createModal').on('show.bs.modal', function () {
        clearErrors(this);
        $(this).find('input:not([type="hidden"])').val('');
        $(this).find('textarea').val('');
        $(this).find('select').prop('selectedIndex', 0);
        $(this).find('.file-upload-info').val('');
    });

    // EDIT modals — clear errors + store originals on open
    $('[id^="editModal"]').on('show.bs.modal', function () {
        clearErrors(this);
        $(this).find('input:not([type="hidden"]), textarea, select').each(function () {
            $(this).data('orig', $(this).val());
        });
    });

    // EDIT modals — restore if not submitted
    $('[id^="editModal"]').on('hide.bs.modal', function () {
        if (!$(this).data('submitted')) {
            $(this).find('input:not([type="hidden"]), textarea, select').each(function () {
                $(this).val($(this).data('orig') || '');
            });
            $(this).find('.file-upload-info').val('');
            clearErrors(this);
        }
        $(this).data('submitted', false);
    });

    // CREATE FORM AJAX SUBMIT
    $('#createAgencyForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors(this);

        const form = this;
        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    $('#createModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Created!',
                        text: res.success,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function () {
                        location.reload();
                    });
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showErrors(form, xhr.responseJSON.errors);
                }
            }
        });
    });

    // EDIT FORM AJAX SUBMIT
    $(document).on('submit', '.editAgencyForm', function (e) {
        e.preventDefault();
        const modal = $(this).closest('.modal');
        clearErrors(modal);

        const form = this;
        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    modal.data('submitted', true);
                    modal.modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: res.success,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function () {
                        location.reload();
                    });
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showErrors(form, xhr.responseJSON.errors);
                }
            }
        });
    });

    // DELETE WITH SWAL CONFIRMATION
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This user will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.success,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function () {
                                location.reload();
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.'
                        });
                    }
                });
            }
        });
    });

});

</script>

@endsection
