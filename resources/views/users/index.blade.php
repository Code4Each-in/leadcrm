@extends('layout')
@section('title', ' Users')
@section('subtitle', 'Users')
@section('content')
<style>
    .required-label::after {
        content: ' *';
        color: red;
    }

</style>
<style>

    .modal-dialog {
        max-width: 700px;   /* good desktop default */
        margin: 1.75rem auto;
    }
    .btn-delete{
        height: 35px;
        align-content: center;
    }
    .editBtn{
        height: 35px;
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
@php
    $authUser = Auth::user();
    $isSuperAdmin = strtolower($authUser->role->name) === 'super admin';
    $agency = $authUser->agency; // assuming relationship `agency` exists
@endphp
<div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <!-- <h4 class="card-title"> @if($isSuperAdmin)
                                           Users
                                        @else
                                            {{ $agency->agency_name }}
                                        @endif</h4> -->
                            <h4 class="card-title">Users</h4>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                            Add User
                        </button>
                    </div>

                    <!-- Success -->
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="usersTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Address</th>
                                    <!-- <th>Agency</th> -->
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createModal">
    <div class="modal-dialog modal-lg">
        <form  id="createUserForm" method="POST" class="forms-sample" action="{{ route('users.store') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add User</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label class="required-label">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Name" >
                    </div>

                    <div class="form-group">
                        <label class="required-label">Email address</label>
                        <input  name="email" class="form-control" placeholder="Email" >
                    </div>

                    <div class="form-group">
                        <label class="required-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" >
                    </div>

                    <div class="form-group">
                        <label class="required-label">Role</label>
                        <select name="role_id" class="form-control">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                @if($role->name != 'Super Admin' || $isSuperAdmin)
                                    <option value="{{ $role->id }}"
                                        {{ isset($user) && $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    @if($isSuperAdmin)
                    <!-- <div class="form-group">
                        <label class="required-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div> -->
                    @endif

                    <div class="form-group">
                        <label class="required-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="required-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ $isSuperAdmin ? old('city') : $agency->city }}">
                    </div>
                    <div class="form-group">
                        <label class="required-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ $isSuperAdmin ? old('state') : $agency->state }}">
                    </div>
                    <div class="form-group">
                        <label class="required-label">Zip</label>
                        <input type="text" name="zip" class="form-control" value="{{ $isSuperAdmin ? old('zip') : $agency->zip }}">
                    </div>
                    <!-- Agency (only for superadmin) -->
                    <!-- @if($isSuperAdmin)
                    <div class="form-group">
                        <label>Agency</label>
                        <select name="agency_id" class="form-control">
                            <option value="">Select Agency</option>
                            @foreach($agencies as $agencyItem)
                                <option value="{{ $agencyItem->id }}">{{ $agencyItem->agency_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif -->

                    <div class="form-group">
                        <label>Profile</label>
                        <div class="input-group">
                            <input type="file" id="profileInput" name="profile" style="display: none;">
                            <input type="text" class="form-control file-upload-info" id="fileName" placeholder="Upload Image" readonly>
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button"
                                    onclick="document.getElementById('profileInput').click();">
                                    Upload
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required-label">Address</label>
                        <textarea name="address" class="form-control" rows="4">{{ $isSuperAdmin ? old('address') : $agency->address }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save User</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!--Edit Modals  -->
@foreach($users as $user)
<div class="modal fade" id="editModal{{ $user->id }}">
    <div class="modal-dialog modal-lg">
        <form class="editUserForm" data-id="{{ $user->id }}" method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label class="required-label">Name</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control" placeholder="Name" >
                    </div>

                    <div class="form-group">
                        <label class="required-label">Email address</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="form-control" placeholder="Email" >
                    </div>

                    <div class="form-group">
                        <label>Password <small class="text-muted">(leave blank to keep old)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>

                    <div class="form-group">
                        <label class="required-label">Role</label>
                        <select name="role_id" class="form-control">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                @if($role->name != 'Super Admin' || $isSuperAdmin)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <!-- Status (only for superadmin) -->
                    @if($isSuperAdmin)
                    <div class="form-group">
                        <label class="required-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="required-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ $user->date_of_birth }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="required-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ $isSuperAdmin ? $user->city : $agency->city }}">
                    </div>
                    <div class="form-group">
                        <label class="required-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ $isSuperAdmin ? $user->state : $agency->state }}">
                    </div>
                    <div class="form-group">
                        <label class="required-label">Zip</label>
                        <input type="text" name="zip" class="form-control" value="{{ $isSuperAdmin ? $user->zip : $agency->zip }}">
                    </div>
                    <!-- Agency (only for superadmin) -->
                    <!-- @if($isSuperAdmin)
                    <div class="form-group">
                        <label>Agency</label>
                        <select name="agency_id" class="form-control">
                            <option value="">Select Agency</option>
                            @foreach($agencies as $agencyItem)
                                <option value="{{ $agencyItem->id }}" {{ $user->agency_id == $agencyItem->id ? 'selected' : '' }}>
                                    {{ $agencyItem->agency_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif -->
                    <div class="form-group">
                        <label>Profile</label>
                        <div class="input-group">
                            <input type="file" id="profileInput_{{ $user->id }}" name="profile" style="display: none;">
                            <input type="text" class="form-control file-upload-info"
                                   id="fileName_{{ $user->id }}"
                                   placeholder="Upload Image" readonly>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary"
                                    onclick="document.getElementById('profileInput_{{ $user->id }}').click();">
                                    Upload
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Address fields (prefill for non-superadmin) -->
                    <div class="form-group">
                        <label class="required-label">Address</label>
                        <textarea name="address" class="form-control" rows="4">
                            {{ $isSuperAdmin ? $user->address : $agency->address }}
                        </textarea>
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

function waitForJQuery(callback) {
    if (typeof $ !== 'undefined') {
        callback();
    } else {
        setTimeout(function () { waitForJQuery(callback); }, 50);
    }
}

waitForJQuery(function () {
    document.addEventListener('DOMContentLoaded', function () {

        $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ordering: true,
            responsive: true,

            ajax: "{{ route('users.index') }}",

            columns: [
                { data: 'name' },
                { data: 'email' },

                {
                    data: 'role',
                    render: function (data) {
                        return data ? data.name : 'N/A';
                    }
                },

                {
                    data: null,
                    render: function (row) {
                        return [
                            row.address,
                            row.city,
                            row.state,
                            row.zip
                        ].filter(Boolean).join(', ');
                    }
                },

              /*  {
                    data: 'agency',
                    render: function (data) {
                        return data ? data.agency_name : 'N/A';
                    }
                }, */
                {
                    data: 'status',
                    render: function (data, type, row) {
                        return `
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                    class="custom-control-input toggle-status"
                                    id="status_${row.id}"
                                    data-id="${row.id}"
                                    data-url="/users/toggle-status/${row.id}"
                                    ${data ? 'checked' : ''}>
                                <label class="custom-control-label" for="status_${row.id}"></label>
                            </div>
                        `;
                    }
                },
                {
                    data: 'id',
                    render: function (id, type, row) {
                        return `
                            <button class="btn btn-sm btn-primary editBtn"
                                data-id="${id}">
                                <i class="mdi mdi-pencil-box"></i> Edit
                            </button>

                            <a href="/users/delete/${id}"
                                class="btn btn-sm btn-danger btn-delete">
                                <i class="mdi mdi-delete"></i> Delete
                            </a>
                        `;
                    }
                }
            ]
        });

    });
    // File input display
    $(document).on('change', 'input[type="file"]', function () {
        let id = this.id.replace('profileInput', 'fileName');
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
    $('#createUserForm').on('submit', function (e) {
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
    $(document).on('submit', '.editUserForm', function (e) {
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
    $(document).on('click', '.editBtn', function () {
        let id = $(this).data('id');

        $('#editModal' + id).modal('show');
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
    // TOGGLE STATUS
    $(document).on('change', '.toggle-status', function () {

        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.prop('checked'); // new intended status

        // Ask user for confirmation
        Swal.fire({
            title: isChecked ? 'Activate this user?' : 'Deactivate this user?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: isChecked ? 'Yes, activate' : 'Yes, deactivate',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with AJAX
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: res.status ? 'Activated' : 'Deactivated',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    });

                    checkbox.prop('checked', res.status);
                },
                error: function (xhr) {
                    checkbox.prop('checked', !isChecked);

                    let message = 'Something went wrong';

                    try {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            let res = JSON.parse(xhr.responseText);
                            message = res.message || message;
                        }
                    } catch (e) {
                        console.log('Parse error:', e);
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot Deactivate',
                        text: message
                    });
                }
            });
            } else {
                // User canceled → revert checkbox
                checkbox.prop('checked', !isChecked);
            }
        });
    });
});

</script>

@endsection
