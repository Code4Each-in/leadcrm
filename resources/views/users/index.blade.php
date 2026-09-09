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
    $today = now()->format('Y-m-d');
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
    <label for="create_product_id">
        Products <span class="text-danger">*</span>
    </label>

    <select
        name="product_id[]"
        id="create_product_id"
        class="form-select"
        multiple
        required
    >
        @foreach($products as $product)
            <option value="{{ $product->id }}">
                {{ $product->name }}
            </option>
        @endforeach
    </select>

    <small class="form-text text-muted">
        Select one or more products.
    </small>
</div>
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

                    <div class="form-group">
                        <label class="required-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" max="{{ $today }}">
                    </div>
                    <div class="form-group">
                        <label class="required-label">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ $isSuperAdmin ? old('address') : $agency->address }}" placeholder="Address">
                    </div>
                    <div class="form-group">
                        <label class="required-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ $isSuperAdmin ? old('city') : $agency->city }}" placeholder="City">
                    </div>
                    <div class="form-group">
                        <label class="required-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ $isSuperAdmin ? old('state') : $agency->state }}" placeholder="State">
                    </div>
                    <div class="form-group">
                        <label class="required-label">Zip</label>
                        <input type="text" name="zip" class="form-control" value="{{ $isSuperAdmin ? old('zip') : $agency->zip }}" placeholder="Zip">
                    </div>
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

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
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
                    <div class="form-group">
                        <label class="required-label">
                            Products
                        </label>

                        @php
                            $assignedProducts = is_array($user->product_id)
                                ? $user->product_id
                                : json_decode($user->product_id ?? '[]', true);

                            $assignedProducts = $assignedProducts ?? [];
                        @endphp

                        <select
                            name="product_id[]"
                            class="form-control edit-product-select"
                            multiple
                            required
                        >
                            @foreach($products as $product)
                                <option
                                    value="{{ $product->id }}"
                                    {{ in_array($product->id, $assignedProducts) ? 'selected' : '' }}
                                >
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>

                        <small class="form-text text-muted">
                            Select one or more products for this user.
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="required-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ $user->date_of_birth }}" class="form-control" max="{{ $today }}">
                    </div>
                    <!-- Address fields (prefill for non-superadmin) -->
                    <div class="form-group">
                        <label class="required-label">Address</label>
                        <input type="text"
                            name="address"
                            class="form-control"
                            value="{{ $isSuperAdmin ? $user->address : $agency->address }}"
                            >
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

                        <div class="form-group">
                            <label>Profile</label>

                            <div class="mb-2">
                                @if($user->profile)
                                <img src="{{ asset($user->profile) }}"
                                    alt="Profile"
                                    id="profilePreview_{{ $user->id }}"
                                    data-original-src="{{ asset($user->profile) }}"
                                    style="width: 50px;
                                            height: 50px;
                                            object-fit: cover;
                                            border-radius: 50%;
                                            border: 2px solid #ddd;">

                                @else
                                <img src="{{ asset('assets/images/default-profile.png') }}"
                                    alt="Default Profile"
                                    id="profilePreview_{{ $user->id }}"
                                    data-original-src="{{ asset('assets/images/default-profile.png') }}"
                                    style="width: 50px;
                                            height: 50px;
                                            object-fit: cover;
                                            border-radius: 50%;
                                            border: 2px solid #ddd;">

                                @endif
                            </div>

                            <div class="input-group">
                                <input type="file"
                                    id="profileInput_{{ $user->id }}"
                                    name="profile"
                                    accept="image/*"
                                    style="display: none;">

                                <input type="text"
                                    class="form-control file-upload-info"
                                    id="fileName_{{ $user->id }}"
                                    placeholder="Choose new image"
                                    readonly>

                                <div class="input-group-append">
                                    <button type="button"
                                            class="btn btn-primary"
                                            onclick="document.getElementById('profileInput_{{ $user->id }}').click();">
                                        Change
                                    </button>
                                </div>
                            </div>
                        </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
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
                                data-id="${id}"
                                data-status="${row.status}">
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

        if (this.files.length === 0) {
            return;
        }

        let id = this.id.replace('profileInput', 'fileName');

        let fileInput = document.getElementById(id);

        if (fileInput) {
            fileInput.value = this.files[0].name;
        }

        // Preview new image
        let previewId = this.id.replace('profileInput', 'profilePreview');

        let preview = document.getElementById(previewId);

        if (preview) {

            let reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
            };

            reader.readAsDataURL(this.files[0]);
        }
    });

function clearErrors(form) {

    $(form).find('.is-invalid').removeClass('is-invalid');

    $(form).find('.invalid-feedback').remove();
}
function resetCreateModal() {

    const modal = $('#createModal');
    const form = modal.find('form')[0];

    // Reset form
    if (form) {
        form.reset();
    }

    // Clear validation errors
    clearErrors(modal);

    // Explicitly clear all inputs
    modal.find('input[name="name"]').val('');
    modal.find('input[name="email"]').val('');
    modal.find('input[name="password"]').val('');
    modal.find('input[name="date_of_birth"]').val('');

    // Clear file
    modal.find('input[type="file"]').val('');

    // Clear filename
    modal.find('.file-upload-info').val('');

    // Reset role
    modal.find('select[name="role_id"]').val('');

    // Because city/state/zip/address have agency values,
    // restore them from their original HTML value.
    modal.find('input[name="city"]').val(
        modal.find('input[name="city"]').prop('defaultValue')
    );

    modal.find('input[name="state"]').val(
        modal.find('input[name="state"]').prop('defaultValue')
    );

    modal.find('input[name="zip"]').val(
        modal.find('input[name="zip"]').prop('defaultValue')
    );

    modal.find('textarea[name="address"]').val(
        modal.find('textarea[name="address"]').prop('defaultValue')
    );
}

function resetEditModal(modal) {

    const $modal = $(modal);

    // Clear validation errors
    clearErrors($modal);

    // Restore original HTML values
    $modal.find('input, select, textarea').each(function () {

        const field = $(this);

        // Ignore CSRF / hidden fields
        if (field.attr('type') === 'hidden') {
            return;
        }

        // File input
        if (field.attr('type') === 'file') {
            field.val('');
            return;
        }

        // Restore original value
        this.value = this.defaultValue;
    });

    // Restore select values from original selected option
    $modal.find('select').each(function () {

        $(this).find('option').each(function () {

            $(this).prop(
                'selected',
                this.defaultSelected
            );

        });

    });

    // Clear selected filename
    $modal.find('.file-upload-info').val('');

    // Restore profile preview
    const profileInput = $modal.find('input[type="file"]');

    if (profileInput.length) {

        const previewId = profileInput.attr('id')
            .replace('profileInput', 'profilePreview');

        const preview = $('#' + previewId);

        if (preview.length) {

            // Get original image from data attribute if available
            const originalImage = preview.attr('data-original-src');

            if (originalImage) {
                preview.attr('src', originalImage);
            }
        }
    }
}

function showFieldError(field, message) {

    const input = $(field);

    input.addClass('is-invalid');

    // Don't add duplicate error
    if (input.next('.invalid-feedback').length === 0) {
        input.after(
            '<div class="invalid-feedback">' + message + '</div>'
        );
    }
}
function clearFieldError(field) {

    const input = $(field);

    input.removeClass('is-invalid');
    input.next('.invalid-feedback').remove();
}
function validateUserForm(form) {

    let valid = true;

    const name = $(form).find('[name="name"]');
    const email = $(form).find('[name="email"]');
    const role = $(form).find('[name="role_id"]');
    const dob = $(form).find('[name="date_of_birth"]');
    const city = $(form).find('[name="city"]');
    const state = $(form).find('[name="state"]');
    const zip = $(form).find('[name="zip"]');
    const address = $(form).find('[name="address"]');

    // Name
    if ($.trim(name.val()) === '') {
        showFieldError(name, 'Please enter the user name.');
        valid = false;
    } else {
        clearFieldError(name);
    }

    // Email
    if ($.trim(email.val()) === '') {
        showFieldError(email, 'Please enter the email address.');
        valid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.val())) {
        showFieldError(email, 'Please enter a valid email address.');
        valid = false;
    } else {
        clearFieldError(email);
    }

    // Role
    if (!role.val()) {
        showFieldError(role, 'Please select a role.');
        valid = false;
    } else {
        clearFieldError(role);
    }

    // DOB
    if (!dob.val()) {

        showFieldError(dob, 'Please select the date of birth.');
        valid = false;

    } else {

        const today = new Date().toISOString().split('T')[0];

        if (dob.val() > today) {
            showFieldError(
                dob,
                'Date of birth cannot be a future date.'
            );
            valid = false;
        } else {
            clearFieldError(dob);
        }
    }

    // City
    if ($.trim(city.val()) === '') {
        showFieldError(city, 'Please enter the city.');
        valid = false;
    } else {
        clearFieldError(city);
    }

    // State
    if ($.trim(state.val()) === '') {
        showFieldError(state, 'Please enter the state.');
        valid = false;
    } else {
        clearFieldError(state);
    }

    // ZIP
    if ($.trim(zip.val()) === '') {
        showFieldError(zip, 'Please enter the ZIP code.');
        valid = false;
    } else {
        clearFieldError(zip);
    }

    // Address
    if ($.trim(address.val()) === '') {
        showFieldError(address, 'Please enter the address.');
        valid = false;
    } else {
        clearFieldError(address);
    }

    return valid;
}
function validateCreatePassword(form) {

    // ONLY CREATE USER
    if (form.id !== 'createUserForm') {
        return true;
    }

    const password = $(form).find('input[name="password"]');
    const value = password.val() || '';

    // Empty password
    if ($.trim(value) === '') {

        password.addClass('is-invalid');

        // Remove existing error first
        password.next('.invalid-feedback').remove();

        password.after(
            '<div class="invalid-feedback">Please enter a password.</div>'
        );

        return false;
    }

    // Less than 8 characters
    if (value.length < 8) {

        password.addClass('is-invalid');

        password.next('.invalid-feedback').remove();

        password.after(
            '<div class="invalid-feedback">Password must be at least 8 characters.</div>'
        );

        return false;
    }

    // Valid
    password.removeClass('is-invalid');
    password.next('.invalid-feedback').remove();

    return true;
}


function showErrors(form, errors) {

    const $form = $(form);

    clearErrors($form);

    $.each(errors, function (field, messages) {

        const input = $form.find('[name="' + field + '"]');

        if (!input.length) {
            console.log('Validation field not found:', field);
            return;
        }

        input.addClass('is-invalid');

        input.next('.invalid-feedback').remove();

        input.after(
            '<div class="invalid-feedback">' +
            messages[0] +
            '</div>'
        );
    });
}


$(document).on(
    'blur',
    '#createUserForm input:not([name="password"]), #createUserForm select, #createUserForm textarea, .editUserForm input:not([name="password"]), .editUserForm select, .editUserForm textarea',
    function () {

        const field = $(this);

        if ($.trim(field.val()) !== '') {
            clearFieldError(field);
        }
    }
);

$(document).on('blur', '#createUserForm input[name="password"]', function () {

    const form = document.getElementById('createUserForm');

    validateCreatePassword(form);

});


$('#createModal').on('hidden.bs.modal', function () {
    resetCreateModal();
});

$('#createModal').on('show.bs.modal', function () {
    resetCreateModal();
});
$('[id^="editModal"]').on('show.bs.modal', function () {

    // Clear old errors whenever opening
    clearErrors(this);

});

$('[id^="editModal"]').on('hidden.bs.modal', function () {

    // Completely restore original values
    resetEditModal(this);

});

    // CREATE FORM AJAX SUBMIT
$('#createUserForm').on('submit', function (e) {

    e.preventDefault();

    const form = this;

    // Clear old errors
    clearErrors(form);

    // Validate normal fields
    const normalFieldsValid = validateUserForm(form);

    // Validate password separately
    const passwordValid = validateCreatePassword(form);

    // STOP if anything is invalid
    if (!normalFieldsValid || !passwordValid) {
        return;
    }

    const formData = new FormData(form);

    $.ajax({

        url: $(form).attr('action'),

        type: 'POST',

        data: formData,

        processData: false,

        contentType: false,

        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

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

            console.log('CREATE USER ERROR:', xhr.responseJSON);

            if (xhr.status === 422) {

                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.errors
                ) {

                    showErrors(
                        form,
                        xhr.responseJSON.errors
                    );

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check the entered information.'
                    });
                }

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
$(document).on('click', '[data-dismiss="modal"]', function () {

    const modal = $(this).closest('.modal');

    if (modal.attr('id') === 'createModal') {

        resetCreateModal();

    } else {

        resetEditModal(modal);

    }

});


    // EDIT FORM AJAX SUBMIT
$(document).on('submit', '.editUserForm', function (e) {

    e.preventDefault();

    const form = this;
    const modal = $(form).closest('.modal');

    clearErrors(modal);

    // Frontend validation
    if (!validateUserForm(form)) {
        return;
    }

    const formData = new FormData(form);

    $.ajax({
        url: $(form).attr('action'),
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

                showErrors(
                    form,
                    xhr.responseJSON.errors
                );

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

    $(document).on('click', '.editBtn', function () {
        let id = $(this).data('id');
        let status = $(this).data('status');

           let modal = $('#editModal' + id);

            // Set current status from DataTable
            modal.find('select[name="status"]').val(String(status));

            // Open modal
            modal.modal('show');
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
        const isChecked = checkbox.prop('checked');
        const userId = checkbox.data('id'); // new intended status

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

                        checkbox.prop('checked', res.status);

                        // Update Edit button with latest status
                        $('.editBtn[data-id="' + checkbox.data('id') + '"]')
                            .attr('data-status', res.status)
                            .data('status', res.status);
                                        Swal.fire({
                        icon: 'success',
                        title: res.status ? 'Activated' : 'Deactivated',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    });

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
<script>
$(document).ready(function () {

    $('#create_product_id').select2({
        placeholder: 'Select products',
        width: '100%',
        allowClear: true
    });

    $('.edit-product-select').select2({
        placeholder: 'Select products',
        width: '100%',
        allowClear: true
    });

});
</script>
@endsection
