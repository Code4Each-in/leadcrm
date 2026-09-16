@extends('layout')

@section('title', 'Profile')
@section('subtitle', 'Profile')

@section('content')

<style>
    /* ==========================================================
       Header - same eyebrow/title/subtitle pattern as Leads/Users/
       Roles, so the page opens with the same rhythm as every other
       section instead of looking like a different app.
       ========================================================== */
    #profilePageCard .profile-page-header {
        padding-bottom: 20px;
        border-bottom: 1px solid #eef0f3;
        margin-bottom: 22px;
    }

    #profilePageCard .profile-page-eyebrow {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: #6c63ff;
        margin-bottom: 10px;
    }

    #profilePageCard .profile-page-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    #profilePageCard .profile-page-header h4 {
        font-weight: 700;
        font-size: 27px;
        color: #1a1f2b;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }

    #profilePageCard .profile-page-header p {
        color: #8a92a3;
        font-size: 13.5px;
        margin: 0;
    }

    /* ==========================================================
       Top layout - summary card (left) + stats/products (right)
       ========================================================== */
    #profilePageCard .profile-layout {
        display: flex;
        gap: 18px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    #profilePageCard .profile-summary-card {
        flex: 0 0 280px;
        background: #fff;
        border: 1px solid #eef0f3;
        border-radius: 14px;
        overflow: hidden;
    }

    #profilePageCard .profile-side {
        flex: 1 1 auto;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    /* Cover + avatar - the app's own two purple shades, not an
       unrelated blue/violet gradient, so the card still reads as
       part of this app rather than a different product. */
    #profilePageCard .profile-cover {
        height: 92px;
        background: linear-gradient(135deg, #6c63ff, #5b52e0);
    }

    #profilePageCard .profile-avatar-wrap {
        position: relative;
        width: 92px;
        height: 92px;
        margin: -46px auto 0;
    }

    #profilePageCard .profile-avatar {
        width: 92px;
        height: 92px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 6px 16px rgba(26, 31, 43, 0.16);
        display: block;
        background: #f4f5f8;
    }

    /* Camera badge - quick shortcut to the same hidden file input
       used by the Profile Photo field further down the page, so
       there's only ever one upload control to keep in sync. */
    #profilePageCard .profile-avatar-edit-btn {
        position: absolute;
        right: -2px;
        bottom: -2px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid #fff;
        background: #6c63ff;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
    }

    #profilePageCard .profile-avatar-edit-btn:hover {
        background: #5b52e0;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(108, 99, 255, 0.35);
    }

    #profilePageCard .profile-identity {
        text-align: center;
        padding: 14px 20px 22px;
    }

    #profilePageCard .profile-identity h5 {
        font-size: 17px;
        font-weight: 700;
        color: #1a1f2b;
        margin: 0 0 3px;
    }

    #profilePageCard .profile-identity p {
        font-size: 13px;
        color: #8a92a3;
        margin: 0 0 10px;
        word-break: break-word;
    }

    #profilePageCard .profile-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 20px;
        background: #eef0ff;
        color: #5b52e0;
        font-size: 12px;
        font-weight: 600;
    }

    /* ==========================================================
       Stats row - same tinted-icon tile used for the Leads stat
       cards, reused here for account-level info instead of lead
       counts.
       ========================================================== */
    #profilePageCard .profile-stats-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    #profilePageCard .profile-stat-card {
        flex: 1 1 150px;
        background: #fff;
        border: 1px solid #eef0f3;
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    #profilePageCard .profile-stat-card .stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    #profilePageCard .profile-stat-card .stat-value {
        font-size: 15px;
        font-weight: 700;
        color: #1a1f2b;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #profilePageCard .profile-stat-card .stat-label {
        font-size: 11.5px;
        font-weight: 500;
        color: #8a92a3;
        white-space: nowrap;
    }

    #profilePageCard .stat-role .stat-icon { background: #eef0ff; color: #5b52e0; }
    #profilePageCard .stat-status-active .stat-icon { background: #d4f4e2; color: #1a7a4c; }
    #profilePageCard .stat-status-inactive .stat-icon { background: #fdeaea; color: #d33a3a; }
    #profilePageCard .stat-2fa-on .stat-icon { background: #eef0ff; color: #6c63ff; }
    #profilePageCard .stat-2fa-off .stat-icon { background: #f4f5f8; color: #6c7280; }
    #profilePageCard .stat-leads .stat-icon { background: #eaf2ff; color: #2264d1; }
    #profilePageCard .stat-login .stat-icon { background: #f4f5f8; color: #6c7280; }

    /* ==========================================================
       Assigned Products - display-only, same chip color language
       as the Users page's product selector (checked/active state),
       just not interactive here.
       ========================================================== */
    #profilePageCard .profile-products-panel {
        background: #fff;
        border: 1px solid #eef0f3;
        border-radius: 12px;
        padding: 16px;
    }

    #profilePageCard .profile-products-heading {
        display: flex;
        align-items: center;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #8a92a3;
        margin-bottom: 12px;
    }

    #profilePageCard .profile-products-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    #profilePageCard .profile-product-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        background: rgba(108, 99, 255, 0.09);
        border: 1px solid rgba(108, 99, 255, 0.35);
        color: #5b52e0;
        font-size: 12.5px;
        font-weight: 600;
    }

    #profilePageCard .profile-product-chip i {
        font-size: 13px;
    }

    #profilePageCard .profile-products-empty {
        color: #a4aab5;
        font-size: 13px;
    }

    /* Small info icon next to a label - identical component to the
       Users page's Products field-info icon, reused verbatim here
       to explain why Assigned Products can't be edited. */
    .field-info-icon {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        margin-left: 6px;
        border-radius: 50%;
        background: #eef0ff;
        color: #6c63ff;
        font-size: 12px;
        cursor: help;
        vertical-align: middle;
    }

    .field-info-tooltip {
        position: fixed;
        max-width: 190px;
        padding: 7px 10px;
        border-radius: 7px;
        background: #1a1f2b;
        color: #fff;
        font-size: 11.5px;
        font-weight: 500;
        line-height: 1.4;
        text-align: center;
        white-space: normal;
        pointer-events: none;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
        z-index: 3000;
    }

    /* ==========================================================
       Profile Information - editable form, same field styling as
       the Users modal's form-group/form-control.
       ========================================================== */
    #profilePageCard .profile-info-card {
        background: #fff;
        border: 1px solid #eef0f3;
        border-radius: 14px;
        padding: 22px 24px;
    }

    #profilePageCard .profile-info-card h5 {
        font-size: 16px;
        font-weight: 700;
        color: #1a1f2b;
        margin: 0 0 4px;
    }

    #profilePageCard .profile-info-card > p {
        font-size: 13px;
        color: #8a92a3;
        margin: 0 0 20px;
    }

    #profilePageCard .form-group label {
        font-size: 13.5px;
        font-weight: 500;
        color: #384153;
        margin-bottom: 6px;
        display: inline-block;
    }

    #profilePageCard .form-control {
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13.5px;
        width: 100%;
        color: #1a1f2b;
    }

    #profilePageCard .form-control:focus {
        outline: none;
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
    }

    #profilePageCard .form-control[readonly] {
        background: #f8f9fb;
        color: #6c7280;
        cursor: not-allowed;
    }

    #profilePageCard .form-control.is-invalid {
        border-color: #d33a3a;
    }

    #profilePageCard .invalid-feedback {
        display: block;
        font-size: 12px;
        color: #d33a3a;
        margin-top: 5px;
    }

    /* File upload field - identical component to the one used on
       the Users page (Add/Edit User modals), reused verbatim so the
       upload control looks the same everywhere it appears. */
    #profilePageCard .file-upload-field {
        display: flex;
        align-items: stretch;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color 0.12s ease, box-shadow 0.12s ease;
    }

    #profilePageCard .file-upload-field:hover,
    #profilePageCard .file-upload-field:focus-within {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.12);
    }

    #profilePageCard .file-upload-field .file-upload-default {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        overflow: hidden;
    }

    #profilePageCard .file-upload-field .file-upload-info {
        flex: 1 1 auto;
        min-width: 0;
        border: none !important;
        border-radius: 0 !important;
        background: #fff;
        color: #8a92a3;
        font-size: 13.5px;
        padding: 10px 12px;
        cursor: pointer;
    }

    #profilePageCard .file-upload-field .file-upload-info:focus {
        outline: none;
        box-shadow: none !important;
    }

    #profilePageCard .file-upload-field .file-upload-info.has-file {
        color: #384153;
    }

    #profilePageCard .file-upload-field .file-upload-browse {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        border-left: 1px solid #e2e5eb;
        background: #f4f5f8;
        color: #5b52e0;
        font-size: 13px;
        font-weight: 500;
        padding: 10px 16px;
        white-space: nowrap;
        transition: background 0.12s ease;
    }

    #profilePageCard .file-upload-field .file-upload-browse:hover {
        background: #eef0ff;
    }

    /* Save button - same solid-primary look as Add Lead/Add User. */
    #profilePageCard .btn-save-profile {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 26px;
        font-weight: 500;
        font-size: 14px;
        border-radius: 9px;
        white-space: nowrap;
        background: #6c63ff;
        border: none;
        color: #fff;
        box-shadow: 0 2px 6px rgba(108, 99, 255, 0.28);
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
    }

    #profilePageCard .btn-save-profile:hover {
        background: #5b52e8;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(108, 99, 255, 0.34);
        color: #fff;
    }

    #profilePageCard .btn-save-profile:disabled {
        opacity: 0.75;
        cursor: not-allowed;
        transform: none;
    }

    #profilePageCard .profile-form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 6px;
    }

    /* ==========================================================
       Mobile
       ========================================================== */
    @media (max-width: 900px) {
        #profilePageCard .profile-layout {
            flex-direction: column;
        }

        #profilePageCard .profile-summary-card {
            flex: 1 1 auto;
            width: 100%;
        }
    }

    @media (max-width: 575px) {
        #profilePageCard .profile-stat-card .stat-value {
            white-space: normal;
        }

        #profilePageCard .profile-info-card {
            padding: 18px;
        }

        #profilePageCard .profile-form-actions {
            justify-content: stretch;
        }

        #profilePageCard .btn-save-profile {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="row">

    <div class="col-md-12 grid-margin stretch-card">

        <div class="card" id="profilePageCard">

            <div class="card-body">

                {{-- Header --}}
                <div class="profile-page-header">
                    <div class="profile-page-eyebrow">Account</div>
                    <h4 class="card-title mb-1">My Profile</h4>
                    <p>View your account details and keep your personal information up to date.</p>
                </div>

                <div class="profile-layout">

                    {{-- LEFT: photo + identity --}}
                    <div class="profile-summary-card">

                        <div class="profile-cover"></div>

                        <div class="profile-avatar-wrap">
                            <img
                                id="profileAvatarPreview"
                                src="{{ auth()->user()->profile ? asset(auth()->user()->profile) : asset('assets/images/default-profile.png') }}"
                                class="profile-avatar"
                                alt="{{ auth()->user()->name }}"
                            >
                            <button
                                type="button"
                                class="profile-avatar-edit-btn"
                                id="avatarEditBtn"
                                data-tooltip="Change profile photo"
                            >
                                <i class="mdi mdi-camera"></i>
                            </button>
                        </div>

                        <div class="profile-identity">
                            <h5 id="profileDisplayName">{{ auth()->user()->name }}</h5>
                            <p id="profileDisplayEmail">{{ auth()->user()->email }}</p>
                            @if(auth()->user()->role)
                                <span class="profile-role-badge">
                                    <i class="mdi mdi-shield-account-outline"></i>
                                    {{ auth()->user()->role->name }}
                                </span>
                            @endif
                        </div>

                    </div>

                    {{-- RIGHT: dynamic stats + assigned products --}}
                    <div class="profile-side">

                        {{--
                            All values below come straight from the
                            logged-in user's own record (and, for
                            Leads/Last Login, a quick scoped query in
                            ProfileController@index) - nothing here
                            is hardcoded.
                        --}}
                        <div class="profile-stats-row">

                            <div class="profile-stat-card stat-role">
                                <div class="stat-icon"><i class="mdi mdi-shield-account-outline"></i></div>
                                <div>
                                    <div class="stat-value">{{ auth()->user()->role->name ?? 'N/A' }}</div>
                                    <div class="stat-label">Role</div>
                                </div>
                            </div>

                            <div class="profile-stat-card {{ auth()->user()->status ? 'stat-status-active' : 'stat-status-inactive' }}">
                                <div class="stat-icon">
                                    <i class="mdi {{ auth()->user()->status ? 'mdi-check-circle-outline' : 'mdi-close-circle-outline' }}"></i>
                                </div>
                                <div>
                                    <div class="stat-value">{{ auth()->user()->status ? 'Active' : 'Inactive' }}</div>
                                    <div class="stat-label">Account Status</div>
                                </div>
                            </div>

                            <div class="profile-stat-card {{ auth()->user()->otp_enabled ? 'stat-2fa-on' : 'stat-2fa-off' }}">
                                <div class="stat-icon">
                                    <i class="mdi mdi-shield-key-outline"></i>
                                </div>
                                <div>
                                    <div class="stat-value">{{ auth()->user()->otp_enabled ? 'Enabled' : 'Disabled' }}</div>
                                    <div class="stat-label">2FA / OTP Login</div>
                                </div>
                            </div>

                            <div class="profile-stat-card stat-leads">
                                <div class="stat-icon"><i class="mdi mdi-briefcase-outline"></i></div>
                                <div>
                                    <div class="stat-value">{{ $leadCount }}</div>
                                    <div class="stat-label">Leads Created</div>
                                </div>
                            </div>

                            <div class="profile-stat-card stat-login">
                                <div class="stat-icon"><i class="mdi mdi-clock-outline"></i></div>
                                <div>
                                    <div class="stat-value">
                                        {{ $lastLogin ? $lastLogin->login_at->format('d M Y, h:i A') : 'No logins yet' }}
                                    </div>
                                    <div class="stat-label">Last Login</div>
                                </div>
                            </div>

                        </div>

                        {{--
                            Assigned Products - display-only. Products
                            are managed by an administrator from the
                            Users page, not from here.
                        --}}
                        <div class="profile-products-panel">
                            <div class="profile-products-heading">
                                Assigned Products
                                <i
                                    class="mdi mdi-information-outline field-info-icon"
                                    tabindex="0"
                                    data-tooltip="Products are assigned by an administrator and can't be changed from your profile."
                                ></i>
                            </div>

                            <div class="profile-products-chips">
                                @forelse($assignedProducts as $product)
                                    <span class="profile-product-chip">
                                        <i class="mdi mdi-tag-outline"></i>
                                        {{ $product->name }}
                                    </span>
                                @empty
                                    <span class="profile-products-empty">No products assigned yet.</span>
                                @endforelse
                            </div>
                        </div>

                    </div>

                </div>

                {{-- Profile Information - editable --}}
                <div class="profile-info-card">

                    <h5>Profile Information</h5>
                    <p>Update your personal details and profile photo.</p>

                    <form id="profileForm" enctype="multipart/form-data" novalidate>
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ auth()->user()->name }}" placeholder="Full Name">
                                    <div class="invalid-feedback" hidden></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ auth()->user()->email }}" placeholder="Email">
                                    <div class="invalid-feedback" hidden></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" class="form-control"
                                        max="{{ now()->format('Y-m-d') }}"
                                        value="{{ auth()->user()->date_of_birth ? \Carbon\Carbon::parse(auth()->user()->date_of_birth)->format('Y-m-d') : '' }}">
                                    <div class="invalid-feedback" hidden></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Role</label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->role->name ?? 'N/A' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label>Address <span class="text-danger">*</span></label>
                            <textarea name="address" rows="3" class="form-control"
                                placeholder="Street address">{{ auth()->user()->address }}</textarea>
                            <div class="invalid-feedback" hidden></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control"
                                        value="{{ auth()->user()->city }}" placeholder="City">
                                    <div class="invalid-feedback" hidden></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State <span class="text-danger">*</span></label>
                                    <input type="text" name="state" class="form-control"
                                        value="{{ auth()->user()->state }}" placeholder="State">
                                    <div class="invalid-feedback" hidden></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>ZIP Code <span class="text-danger">*</span></label>
                                    <input type="text" name="zip" class="form-control"
                                        value="{{ auth()->user()->zip }}" placeholder="ZIP Code">
                                    <div class="invalid-feedback" hidden></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label>Profile Photo</label>
                            <div class="file-upload-field" id="profilePhotoField">
                                <input type="text" class="file-upload-info" id="profilePhotoName" placeholder="No file chosen" readonly>
                                <input type="file" id="profilePhotoInput" name="profile"
                                    accept="image/jpg,image/jpeg,image/png" class="file-upload-default">
                                <button type="button" class="file-upload-browse">
                                    <i class="mdi mdi-paperclip"></i>
                                    Browse
                                </button>
                            </div>
                            <small class="form-text text-muted">JPG, JPEG or PNG. Max 2MB.</small>
                            <div class="invalid-feedback" hidden></div>
                        </div>

                        <div class="profile-form-actions">
                            <button type="submit" id="profileSaveBtn" class="btn-save-profile">
                                <i class="mdi mdi-content-save-outline"></i>
                                Save Changes
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
function waitForJQuery(callback) {
    if (typeof $ !== 'undefined') {
        callback();
    } else {
        setTimeout(function () { waitForJQuery(callback); }, 50);
    }
}

waitForJQuery(function () {

    /*
    * Icon tooltip - same [data-tooltip] driven, position:fixed
    * bubble used on the Leads/Users/Roles/Login Logs pages, reused
    * here for the avatar's camera icon and the Assigned Products
    * info icon so tooltips look and behave identically everywhere.
    */
    let $fieldTooltip = null;

    function showFieldTooltip(icon) {

        hideFieldTooltip();

        const text = icon.getAttribute('data-tooltip');

        if (!text) {
            return;
        }

        $fieldTooltip = $('<div class="field-info-tooltip"></div>')
            .text(text)
            .appendTo('body');

        const iconRect = icon.getBoundingClientRect();
        const tipEl = $fieldTooltip[0];
        const tipRect = tipEl.getBoundingClientRect();

        let top = iconRect.top - tipRect.height - 8;

        if (top < 8) {
            top = iconRect.bottom + 8;
        }

        let left = iconRect.left + (iconRect.width / 2) - (tipRect.width / 2);
        left = Math.max(8, Math.min(left, window.innerWidth - tipRect.width - 8));

        tipEl.style.top = top + 'px';
        tipEl.style.left = left + 'px';
    }

    function hideFieldTooltip() {

        if ($fieldTooltip) {
            $fieldTooltip.remove();
            $fieldTooltip = null;
        }
    }

    $(document).on('mouseenter focus', '[data-tooltip]', function () {
        showFieldTooltip(this);
    });

    $(document).on('mouseleave blur', '[data-tooltip]', function () {
        hideFieldTooltip();
    });

    $(window).on('resize', hideFieldTooltip);
    document.addEventListener('scroll', hideFieldTooltip, true);

    /*
    * Soft, non-blocking toast - same notification style used on
    * Leads/Users/Roles instead of the old inline alert-success box.
    */
    function showToast(icon, message) {

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: message,
            showConfirmButton: false,
            timer: icon === 'success' ? 2200 : 2800,
            timerProgressBar: true
        });

    }

    function clearErrors(form) {
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('.invalid-feedback').attr('hidden', true).text('');
    }

    function showErrors(form, errors) {

        clearErrors(form);

        $.each(errors, function (field, messages) {

            const $input = $(form).find('[name="' + field + '"]').first();

            if (!$input.length) {
                return;
            }

            $input.addClass('is-invalid');

            $input
                .closest('.form-group')
                .find('.invalid-feedback')
                .text(messages[0])
                .removeAttr('hidden');

        });

    }

    /*
    * Shared button loader - same pattern used on the Leads/Users/
    * Roles pages (swap the label for a spinning mdi-loading glyph
    * and disable, so a slow request can't fire twice).
    */
    function setBtnLoading($btn, loading, loadingText) {

        if (loading) {

            if ($btn.data('original-html') === undefined) {
                $btn.data('original-html', $btn.html());
            }

            $btn.prop('disabled', true);
            $btn.html('<i class="mdi mdi-loading mdi-spin"></i> ' + (loadingText || 'Please wait...'));

        } else {

            $btn.prop('disabled', false);

            if ($btn.data('original-html') !== undefined) {
                $btn.html($btn.data('original-html'));
            }
        }
    }

    /*
    * File upload field - same delegated click-anywhere-opens-the-
    * hidden-input behavior used on the Users page.
    */
    $(document).on('click', '.file-upload-field', function () {
        $(this).find('input[type="file"]').trigger('click');
    });

    const $fileInput = $('#profilePhotoInput');
    const $fileName = $('#profilePhotoName');
    const $avatarPreview = $('#profileAvatarPreview');

    function previewSelectedFile(file) {

        if (!file) {
            return;
        }

        $fileName.val(file.name).addClass('has-file');

        const reader = new FileReader();

        reader.onload = function (e) {
            $avatarPreview.attr('src', e.target.result);
        };

        reader.readAsDataURL(file);
    }

    $fileInput.on('change', function () {

        if (this.files && this.files[0]) {
            previewSelectedFile(this.files[0]);
        }

    });

    // Camera badge on the avatar - just another trigger for the
    // very same hidden input the Profile Photo field uses below, so
    // there's only ever one source of truth for the selected file.
    $('#avatarEditBtn').on('click', function () {
        $fileInput.trigger('click');
    });

    /*
    * PROFILE UPDATE - AJAX submit (POST + _method=PUT, the usual
    * Laravel spoofing for a file upload that a real PUT request
    * can't carry reliably). No page reload either way: on success
    * the identity card, header avatar/name and the Save button all
    * update in place; on a 422 the fields show their own errors.
    */
    $('#profileForm').on('submit', function (e) {

        e.preventDefault();

        const form = this;

        clearErrors(form);

        // Belt-and-suspenders alongside the input's own max="" date -
        // some browsers still let a future date through on manual
        // typing/paste, so catch it here before it ever reaches the
        // server (which also rejects it via before_or_equal:today).
        const $dob = $(form).find('[name="date_of_birth"]');
        if ($dob.val() && $dob.val() > "{{ now()->format('Y-m-d') }}") {
            showErrors(form, { date_of_birth: ['Date of birth cannot be a future date.'] });
            return;
        }

        const formData = new FormData(form);
        formData.append('_method', 'PUT');

        const $saveBtn = $('#profileSaveBtn');

        setBtnLoading($saveBtn, true, 'Saving...');

        $.ajax({

            url: "{{ route('profile.update') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (res) {

                if (res.profile_url) {

                    // Cache-bust so a same-named file (or a proxy/
                    // browser cache) can never keep showing the old
                    // photo after an update.
                    const bustedUrl = res.profile_url +
                        (res.profile_url.indexOf('?') === -1 ? '?' : '&') + 't=' + Date.now();

                    $avatarPreview.attr('src', bustedUrl);
                    $('#headerProfileAvatar').attr('src', bustedUrl);
                }

                if (res.name) {
                    $('#profileDisplayName').text(res.name);
                    $('#headerProfileName').text(res.name);
                }

                if (res.email) {
                    $('#profileDisplayEmail').text(res.email);
                }

                // Selected-file state is only meaningful until the
                // upload it described has actually been saved.
                $fileName.val('').removeClass('has-file');
                $fileInput.val('');

                showToast('success', res.success || 'Profile updated successfully.');

            },

            error: function (xhr) {

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {

                    showErrors(form, xhr.responseJSON.errors);
                    showToast('error', 'Please check the highlighted fields.');

                } else {

                    showToast('error', 'Something went wrong. Please try again.');
                }
            },

            complete: function () {

                setBtnLoading($saveBtn, false);

            }

        });

    });

});
</script>

@endsection
