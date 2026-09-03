@extends('layout')
@section('title', 'Roles')
@section('subtitle', 'Roles')
@section('content')
<style>
   .required-label::after {
        content: ' *';
        color: red;
    }

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
            width: calc(100% - 20px) !important;
            max-width: calc(100% - 20px) !important;
            height: auto !important;
            margin: 10px auto !important;
            display: flex !important;
            align-items: center !important;
        }

        .modal-content {
            width: 100% !important;
            height: auto !important;
            max-height: 90vh !important;
            border-radius: 10px !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .modal-body {
            flex: 1 !important;
            max-height: 70vh !important;
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

</style>
<div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="card-title">Roles</h4>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                            Add Role
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="rolesTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Created At</th>
                                    <th width="200">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->created_at->format('d-m-Y h:i A') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info"
                                            data-toggle="modal"
                                            data-target="#editModal{{ $role->id }}">
                                            <i class="mdi mdi-pencil-box"></i> Edit
                                        </button>
                                        <a href="{{ route('roles.delete', $role->id) }}"
                                           class="btn btn-sm btn-danger btn-delete">
                                           <i class="mdi mdi-delete"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createRoleForm" method="POST" action="{{ route('roles.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Role</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required-label">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter role name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Edit Modals -->
@foreach($roles as $role)
<div class="modal fade" id="editModal{{ $role->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="editRoleForm" method="POST" action="{{ route('roles.update', $role->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Role</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required-label">Role Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $role->name }}" data-original="{{ $role->name }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<script>
function waitForJQuery(callback) {
    if (typeof $ !== 'undefined') {
        callback();
    } else {
        setTimeout(function () { waitForJQuery(callback); }, 50);
    }
}

waitForJQuery(function () {

function clearErrors($modal) {

    $modal.find('.is-invalid').removeClass('is-invalid');
    $modal.find('.invalid-feedback').remove();

}


function resetModal($modal) {

    if (!$modal || !$modal.length) {
        return;
    }

    const $form = $modal.find('form');

    // Clear validation
    clearErrors($modal);


    // CREATE
    if ($modal.attr('id') === 'createModal') {

        $form.find('input[name="name"]').val('');

    }


    // EDIT
    else {

        $form.find('input[name="name"][data-original]')
            .each(function () {

                $(this).val(
                    $(this).attr('data-original')
                );

            });

    }

}


/*
|--------------------------------------------------------------------------
| CANCEL + X
|--------------------------------------------------------------------------
*/

$(document).on(
    'click',
    '[data-dismiss="modal"]',
    function () {

        resetModal(
            $(this).closest('.modal')
        );

    }
);


/*
|--------------------------------------------------------------------------
| BACKDROP CLICK
|--------------------------------------------------------------------------
*/

$(document).on(
    'click',
    '.modal-backdrop',
    function () {

        const $modal = $('.modal.show');

        if ($modal.length) {

            resetModal($modal);

        }

    }
);


/*
|--------------------------------------------------------------------------
| ESC / PROGRAMMATIC CLOSE / ANY OTHER CLOSE
|--------------------------------------------------------------------------
*/

$(document).on(
    'hidden.bs.modal',
    '.modal',
    function () {

        resetModal(
            $(this)
        );

    }
);


    function showErrors(form, errors) {

        const $form = $(form);
        const $modal = $form.closest('.modal');

        // Clear existing errors first
        clearErrors($modal);

        $.each(errors, function (field, messages) {

            const $input = $form.find('[name="' + field + '"]').first();

            if (!$input.length) {
                return;
            }

            $input.addClass('is-invalid');

            $input.closest('.form-group').append(
                '<div class="invalid-feedback d-block">' +
                messages[0] +
                '</div>'
            );

        });

    }


    $('#createRoleForm').on('submit', function (e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
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
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showErrors(form, xhr.responseJSON.errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            }
        });
    });


    $(document).on('submit', '.editRoleForm', function (e) {
        e.preventDefault();
        const form = this;
        const modal = $(form).closest('.modal');
        const formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
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
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showErrors(form, xhr.responseJSON.errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            }
        });
    });


    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This role will be permanently deleted!',
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.success,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function () {
                            location.reload();
                        });
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
