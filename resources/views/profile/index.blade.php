@extends('layout')
@section('title', 'Profile')
@section('subtitle', 'Profile')
@section('content')
<div class="row d-flex justify-content-center">

    <!-- Left Profile Card -->
    <div class="col-md-12 grid-margin d-flex">
        <div class="card profile-left-card w-100 d-flex flex-column p-0 overflow-hidden">
            {{-- Cover gradient banner --}}
            <div class="profile-cover"></div>

            {{-- Avatar overlapping cover --}}
            <div class="text-center profile-avatar-wrap">
                <img
                    src="{{ auth()->user()->profile
                        ? asset(auth()->user()->profile)
                        : asset('assets/images/default-profile.png') }}"
                    class="profile-avatar rounded-circle"
                    alt="{{ auth()->user()->name }}">
            </div>

            {{-- Name / Email / Role badge --}}
            <div class="text-center px-4 pb-3 profile-name-section">
                <h5 class="mb-0 fw-bold">{{ auth()->user()->name }}</h5>
                <p class="text-muted small mb-2">{{ auth()->user()->email }}</p>
                @if(auth()->user()->role)
                    <span class="badge bg-gradient-primary px-3 py-1">
                        {{ auth()->user()->role->name }}
                    </span>
                @endif
            </div>

            {{-- Stats Row --}}
            <div class="profile-stats d-flex border-top text-center mt-auto">

                <div class="flex-fill py-3 border-end">
                    <h6 class="mb-0 fw-bold">{{ $leadCount }}</h6>
                    <small class="text-muted">Lead</small>
                </div>

                <div class="flex-fill py-3 border-end">
                    <h6 class="mb-0 fw-bold">{{ auth()->user()->city ?? '—' }}</h6>
                    <small class="text-muted">City</small>
                </div>

                <div class="flex-fill py-3">
                    <h6 class="mb-0 fw-bold">{{ $teamCount }}</h6>
                    <small class="text-muted">Team Member</small>
                </div>

            </div>
        </div>
    </div>

    <!-- Right Edit Profile Card -->
    <div class="col-md-12 grid-margin d-flex">
        <div class="card w-100 h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold mb-4">Edit Profile</h5>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="flex-grow-1 d-flex flex-column" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Name / Email --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="profile-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                class="form-control profile-input @error('name') is-invalid @enderror"
                                value="{{ old('name', auth()->user()->name) }}"
                                placeholder="Full Name">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="profile-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                class="form-control profile-input @error('email') is-invalid @enderror"
                                value="{{ old('email', auth()->user()->email) }}"
                                placeholder="Email">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Date of Birth / Role (read-only) --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="profile-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth"
                                class="form-control profile-input @error('date_of_birth') is-invalid @enderror"
                                value="{{ old('date_of_birth', auth()->user()->date_of_birth ? \Carbon\Carbon::parse(auth()->user()->date_of_birth)->format('Y-m-d') : '') }}">
                            @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="profile-label">Role</label>
                            <input type="text" class="form-control profile-input"
                                value="{{ auth()->user()->role->name ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3">
                        <label class="profile-label">Address <span class="text-danger">*</span></label>
                        <textarea name="address" rows="3"
                            class="form-control profile-input @error('address') is-invalid @enderror"
                            placeholder="Street address">{{ old('address', auth()->user()->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- City / State / ZIP --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="profile-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="city"
                                class="form-control profile-input @error('city') is-invalid @enderror"
                                value="{{ old('city', auth()->user()->city) }}"
                                placeholder="City">
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="profile-label">State <span class="text-danger">*</span></label>
                            <input type="text" name="state"
                                class="form-control profile-input @error('state') is-invalid @enderror"
                                value="{{ old('state', auth()->user()->state) }}"
                                placeholder="State">
                            @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="profile-label">ZIP Code <span class="text-danger">*</span></label>
                            <input type="text" name="zip"
                                class="form-control profile-input @error('zip') is-invalid @enderror"
                                value="{{ old('zip', auth()->user()->zip) }}"
                                placeholder="ZIP Code">
                            @error('zip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Profile Photo --}}
                    <div class="mb-4">
                        <label class="profile-label">Profile Photo</label>

                        {{-- Image preview --}}
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <img id="profilePreview"
                                src="{{ auth()->user()->profile ? asset(auth()->user()->profile) : asset('assets/images/default-profile.png') }}"
                                class="rounded-circle current-thumb"
                                alt="Profile Preview">
                        </div>

                        <div class="input-group">
                            {{-- Hidden real file input --}}
                            <input type="file"
                                id="profilePhotoInput"
                                name="profile"
                                accept="image/jpg,image/jpeg,image/png"
                                style="display: none;">

                            {{-- Display-only text box showing chosen filename --}}
                            <input type="text"
                                id="profilePhotoName"
                                class="form-control profile-input @error('profile') is-invalid @enderror"
                                placeholder="Upload Image"
                                readonly>

                            {{-- Hidden input to temporarily store base64 of selected file --}}
                            <input type="hidden" name="pendingProfile" id="pendingProfile" value="{{ old('pendingProfile') }}">
                            <input type="hidden" name="pendingProfileName" id="pendingProfileName" value="{{ old('pendingProfileName') }}">

                            {{-- Trigger button --}}
                            <span class="input-group-append">
                                <button class="btn btn-primary file-upload-browse" type="button"
                                        onclick="document.getElementById('profilePhotoInput').click();">
                                    Upload
                                </button>
                            </span>

                            @error('profile')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <small class="text-muted mt-1 d-block">JPG, JPEG or PNG · Max 2MB</small>
                    </div>

                    {{-- Submit --}}
                    <div class="text-end mt-auto">
                        <button type="submit" class="btn btn-primary btn-update-profile px-5">
                            Update Profile
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

<style>
/* Card styling */
.card {
    border-radius: 14px;
    border: none;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    background: #fff;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(0,0,0,0.08);
}

/* Profile left card */
.profile-left-card {
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Cover */
.profile-cover {
    height: 180px;
    background: linear-gradient(135deg, #6a8dff, #7b3ff2);
}
.profile-avatar-wrap::after {
    content: '';
    position: absolute;
    width: 100px;
    height: 100px;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 50%;
    background: rgba(0,0,0,0.05);
    z-index: 1;
}
/* Avatar */
.profile-avatar-wrap {
    margin-top: -55px;
    margin-bottom: 15px;
    position: relative;
    z-index: 2;
}

.profile-avatar {
    width: 95px;
    height: 95px;
    object-fit: cover;
    border-radius: 50%;
    border: 5px solid #fff;
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

/* Name section */
.profile-name-section h5 {
    font-size: 1.2rem;
    margin-bottom: 4px;
}

.profile-name-section p {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 8px;
}

.badge.bg-gradient-primary {
    background: linear-gradient(135deg, #4b49ac, #6a67ce);
    color: #fff;
    font-weight: 600;
    padding: 0.25rem 1rem;
    border-radius: 20px;
}

/* Stats */
.profile-stats {
    background: #f4f4f9;
    margin-top: auto;
    padding-top: 1rem;
    display: flex;
    justify-content: space-between;
    border-top: 1px solid #ddd;
}

.profile-stats h6 {
    font-size: 1rem;
    font-weight: 700;
    color: #4b49ac;
}

.profile-stats small {
    font-size: 0.75rem;
    color: #999;
}

/* Inputs */
.profile-input {
    background-color: #fbfbf9;
    border: 1px solid #e6e6e0;
    border-radius: 10px;
    font-size: 0.85rem;
    padding: 10px 12px;
}

.profile-input:focus {
    background-color: #fff;
    border-color: #6a67ce;
    box-shadow: 0 0 0 3px rgba(106,103,206,0.15);
}

/* Labels */
.profile-label {
    font-size: 0.75rem;
    color: #8c8c8c;
    margin-bottom: 4px;
}

/* Button */
.btn-update-profile {
    background: linear-gradient(135deg, #4b49ac, #6a67ce);
    color: #fff;
    border-radius: 30px;
    font-weight: 600;
    padding: 10px 40px;
}

.btn-update-profile:hover {
    background: linear-gradient(135deg, #3a3897, #5a57c7);
}

/* File upload button */
.file-upload-browse {
    background: #4b49ac;
    color: #fff;
    border-radius: 0 10px 10px 0;
}

/* Image preview */
.current-thumb {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    border: 2px solid #eee;
}
.card-body > form > .row,
.card-body > form > .mb-3 {
    background: #fafafa;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 15px;
}
#profilePhotoName {
    background: #fff;
    border-right: none;
}
.file-upload-browse {
    border-radius: 0 12px 12px 0;
}
.btn-update-profile,
.file-upload-browse {
    background: linear-gradient(135deg, #4b49ac, #6a67ce);
    transition: all 0.3s ease;
}
.btn-update-profile:hover,
.file-upload-browse:hover {
    background: linear-gradient(135deg, #3a3897, #5a57c7);
    transform: translateY(-2px);
}
.profile-name-section h5 { font-size: 1.4rem; }
.profile-name-section p { font-size: 0.9rem; color: #777; }
.profile-label { font-weight: 600; color: #666; }
.card { padding: 1.2rem; }
.profile-left-card { padding-bottom: 20px; }
.card-body { gap: 1rem; display: flex; flex-direction: column; }
</style>

<script>
const fileInput = document.getElementById('profilePhotoInput');
const nameBox = document.getElementById('profilePhotoName');
const preview = document.getElementById('profilePreview');
const pendingInput = document.getElementById('pendingProfile');
const pendingNameInput = document.getElementById('pendingProfileName');

fileInput.addEventListener('change', function () {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        nameBox.value = file.name;
        pendingNameInput.value = file.name;

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            pendingInput.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// On page load, restore preview and filename if they exist
window.addEventListener('DOMContentLoaded', () => {
    const oldPending = pendingInput.value;
    const oldName = pendingNameInput.value;

    if (oldPending) {
        preview.src = oldPending;
    }
    if (oldName) {
        nameBox.value = oldName;
    }
});
</script>
@endsection
