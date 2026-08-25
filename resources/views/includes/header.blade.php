<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
/* ============================================================
   LEAD BRIDGE NAVBAR — FULLY RESPONSIVE
   ============================================================ */

/* ---------- Reset / Base ---------- */
* { box-sizing: border-box; }

.navbar {
    background: #ffffff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1030;
    padding: 0;
    display: flex;
    flex-direction: row;
    align-items: center;
    min-height: 64px;
    flex-wrap: nowrap;
}

/* ============================================================
   BRAND WRAPPER
   ============================================================ */
.navbar-brand-wrapper {
    width: 220px;
    min-width: 220px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 0 16px;
    flex-shrink: 0;
    border-right: 1px solid #f0f1f7;
}

.navbar-brand img { height: 36px; width: auto; }
.navbar-brand-text { font-size: 18px; font-weight: 700; color: #3f3cbb; }
.navbar-brand.brand-logo-mini { display: none; }

/* ============================================================
   MENU WRAPPER
   ============================================================ */
.navbar-menu-wrapper {
    flex: 1;
    height: 64px;
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 0 16px;
    min-width: 0;
    position: relative;
}

/* Sidebar mini toggle */
.navbar-toggler.align-self-center {
    background: none;
    border: none;
    cursor: pointer;
    padding: 6px 10px;
    color: #666;
    font-size: 18px;
    flex-shrink: 0;
    margin-right: 8px;
}
.navbar-toggler:focus { outline: none; box-shadow: none; }

/* ============================================================
   CENTER: Agency select
   ============================================================ */
.navbar-nav.navbar-center-nav {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
}

#agency-select { width: 260px !important; }

/* ============================================================
   RIGHT: Bell + Profile
   ============================================================ */
.navbar-nav-right {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
    list-style: none;
    margin: 0;
    margin-left: auto;
    padding: 0;
    flex-shrink: 0;
}

/* ---------- Bell ---------- */
.count-indicator {
    position: relative;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    background: #f7f8fc;
    border: 1px solid #e2e5f0;
    border-radius: 10px;
    color: #444;
    transition: background 0.2s;
    text-decoration: none;
}
.count-indicator:hover { background: #eef0fb; color: #3f3cbb; }

.count-indicator .count {
    position: absolute;
    top: -5px;
    right: -5px;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 9px;
    font-size: 10px;
    font-weight: 700;
    line-height: 18px;
    text-align: center;
    background: #e53935;
    color: #fff;
    border: 2px solid #fff;
}

/* ---------- Notification dropdown ---------- */
.navbar-dropdown.preview-list {
    width: 320px;
    border-radius: 14px;
    border: 1px solid #e8e8e8;
    box-shadow: 0 8px 30px rgba(0,0,0,0.10);
    padding: 0;
    overflow: hidden;
}

.dropdown-header {
    padding: 14px 16px;
    font-size: 13px;
    font-weight: 700;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
}

.preview-item {
    display: flex;
    align-items: flex-start;
    gap: 11px;
    padding: 12px 16px;
    border-bottom: 1px solid #f5f5f5;
    text-decoration: none;
}
.preview-item.unread {
    background: #f0f7ff;
    border-left: 3px solid #1976d2;
}
.preview-item:hover { background: #fafafa; }
.preview-subject { font-size: 13px; font-weight: 600; color: #1a1a1a; margin: 0; }
.small-text { font-size: 12px; color: #777; line-height: 1.4; margin: 0; }

/* ---------- Profile ---------- */
.nav-profile .nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 8px;
    border-radius: 10px;
    text-decoration: none;
    transition: background 0.2s;
}
.nav-profile .nav-link:hover { background: rgba(0,0,0,0.05); }

.nav-profile img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 2px solid #e2e5f0;
}
.user-name { font-size: 14px; font-weight: 600; color: #1a1a1a; line-height: 1.2; white-space: nowrap; }
.user-role { font-size: 11px; color: #888; white-space: nowrap; }

/* ---------- Profile dropdown ---------- */
.navbar-dropdown.shadow-sm {
    border-radius: 12px;
    border: 1px solid #e8e8e8;
    box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
    overflow: hidden;
    min-width: 180px;
}
.navbar-dropdown .dropdown-item {
    font-size: 13.5px;
    padding: 10px 16px;
    color: #333;
    display: flex;
    align-items: center;
    transition: background 0.15s;
}
.navbar-dropdown .dropdown-item:hover { background: #f5f5f5; }
.navbar-dropdown .dropdown-item.text-danger { color: #e53935 !important; }

/* ============================================================
   SELECT2 OVERRIDES
   ============================================================ */
.select2-container--default .select2-selection--multiple {
    height: 40px !important;
    max-height: 40px !important;
    overflow: hidden !important;
    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    padding: 3px 36px 3px 10px !important;
    gap: 4px !important;
    border: 1.5px solid #e2e5f0 !important;
    border-radius: 10px !important;
    background: #f7f8fc !important;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.select2-container--default.select2-container--focus .select2-selection--multiple,
.select2-container--default.select2-container--open .select2-selection--multiple {
    border-color: #3f3cbb !important;
    box-shadow: 0 0 0 3px rgba(63,60,187,0.10) !important;
    background: #fff !important;
    outline: none !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: linear-gradient(135deg, #3f3cbb, #5553d4) !important;
    border: none !important;
    border-radius: 6px !important;
    color: #fff !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    padding: 3px 8px 3px 20px !important;
    margin: 0 !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__display { font-size: 12px !important; color: #fff !important; }
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: rgba(255,255,255,0.7) !important; background: transparent !important; border: none !important; }
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover { color: #fff !important; }
.select2-container--default .select2-selection--multiple .select2-selection__clear { margin-top: 0 !important; color: #b0b5c8 !important; font-size: 16px !important; position: absolute !important; right: 10px !important; top: 50% !important; transform: translateY(-50%) !important; }
.select2-dropdown { border: 1.5px solid #e2e5f0 !important; border-radius: 12px !important; box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important; overflow: hidden !important; margin-top: 4px !important; }
.select2-search--dropdown { padding: 8px !important; }
.select2-search--dropdown .select2-search__field { border: 1.5px solid #e2e5f0 !important; border-radius: 8px !important; padding: 7px 12px !important; font-size: 13px !important; outline: none !important; }
.select2-search--dropdown .select2-search__field:focus { border-color: #3f3cbb !important; box-shadow: 0 0 0 2px rgba(63,60,187,0.10) !important; }
.select2-results__option { font-size: 13.5px !important; padding: 9px 12px !important; border-radius: 8px !important; color: #3a3d52 !important; transition: background 0.15s !important; }
.select2-results__option--highlighted { background: #f0efff !important; color: #3f3cbb !important; }
.select2-results__option[aria-selected="true"] { background: #ebe9ff !important; color: #3f3cbb !important; font-weight: 600 !important; }

/* ============================================================
   TABLET LANDSCAPE  769px – 1023px
   ============================================================ */
@media (max-width: 1023px) and (min-width: 768px) {

    .select2-container{
            display: none !important;
        }
    /* Shrink brand area */
    .navbar-brand-wrapper {
        width: 60px;
        min-width: 60px;
        padding: 0 12px;
        justify-content: center;
        border-right: 1px solid #f0f1f7;
    }
    .navbar-brand.brand-logo   { display: none; }
    .navbar-brand.brand-logo-mini { display: flex; align-items: center; }
    .navbar-brand.brand-logo-mini img { height: 30px; }

    /* ── Hide agency select entirely below 1024px ── */
    .navbar-nav.navbar-center-nav { display: none !important; }

    /* Hide name/role in profile */
    .user-name, .user-role { display: none; }

    /* Slightly smaller bell */
    .count-indicator { width: 38px; height: 38px; }

    /* Dropdown panel narrower */
    .navbar-dropdown.preview-list { width: 290px; }

}

/* ============================================================
   TABLET PORTRAIT  481px – 768px
   ============================================================ */
@media (max-width: 768px) and (min-width: 481px) {

    .navbar {
        flex-wrap: wrap;
        min-height: auto;
    }

    /* ── Row 1: brand + right icons ── */
    .navbar-brand-wrapper {
        width: auto;
        min-width: unset;
        height: 56px;
        flex: 0 0 auto;
        padding: 0 12px;
        border-right: none;
    }
    .navbar-brand.brand-logo   { display: none; }
    .navbar-brand.brand-logo-mini { display: flex; align-items: center; }
    .navbar-brand.brand-logo-mini img { height: 28px; }

    .navbar-menu-wrapper {
        height: 56px;
        padding: 0 12px 0 4px;
        flex-wrap: nowrap;
    }

    /* Hide sidebar mini-toggle (sidebar uses off-canvas on mobile) */
    .navbar-toggler.align-self-center { display: none; }

    /* ── Hide agency select entirely below 1024px ── */
    .navbar-nav.navbar-center-nav { display: none !important; }

    /* Right nav stays row 1 */
    .navbar-nav-right { margin-left: auto; gap: 6px; }

    .user-name, .user-role { display: none; }
    .count-indicator { width: 36px; height: 36px; }

    .navbar-dropdown.preview-list {
        width: 300px;
    }

    /* Off-canvas toggler visible */
    .navbar-toggler.navbar-toggler-right {
        display: flex !important;
        align-items: center;
        background: none;
        border: none;
        font-size: 18px;
        color: #555;
        padding: 6px;
        cursor: pointer;
        margin-left: 4px;
    }

}

/* ============================================================
   MOBILE  ≤ 480px
   ============================================================ */
@media (max-width: 480px) {

    .navbar {
        flex-wrap: wrap;
        min-height: auto;
        padding: 0;
    }

    /* ── Row 1 ── */
    .navbar-brand-wrapper {
        height: 52px;
        width: auto;
        min-width: unset;
        padding: 0 10px;
        flex: 0 0 auto;
        border-right: none;
    }
    .navbar-brand.brand-logo   { display: none; }
    .navbar-brand.brand-logo-mini { display: flex; align-items: center; }
    .navbar-brand.brand-logo-mini img { height: 26px; }

    .navbar-menu-wrapper {
        height: 52px;
        padding: 0 10px 0 4px;
        flex-wrap: wrap;
    }

    .navbar-toggler.align-self-center { display: none; }

    /* ── Hide agency select entirely below 1024px ── */
    .navbar-nav.navbar-center-nav { display: none !important; }

       .navbar-nav-right {
        gap: 2px;
        flex-shrink: 1;
    }

    /* Hide profile text */
    .user-name, .user-role { display: none; }

    /* Smaller avatar */
    .nav-profile img { width: 32px; height: 32px; }

    /* Smaller bell */
    .count-indicator { width: 34px; height: 34px; }

    /* Notification dropdown fills screen width */
    .navbar-dropdown.preview-list {
        width: calc(100vw - 20px);
        right: -10px !important;
        left: auto !important;
        border-radius: 10px;
    }

    /* Off-canvas toggler */
   .navbar-toggler.navbar-toggler-right {
        display: block !important;
        position: relative;
        z-index: 1050;
        margin-left: 6px;
    }

    .navbar-dropdown.shadow-sm {
        min-width: 160px;
    }
}
@media (max-width: 1440px){
    .select2-container {
        width: 500px !important;
        max-width: 100%;
    }
}
@media (max-width: 1024px){
    .select2-container {
        width: 300px !important;
        max-width: 100%;
    }
}
@media (max-width: 425px) and (min-width: 320px){
    .select2-container--default .select2-selection--multiple {
        display: none !important;
    }
}
</style>

<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

    {{-- ── Brand ── --}}
    <div class="navbar-brand-wrapper d-flex align-items-center justify-content-center">

        {{-- Full logo (desktop) --}}
        <a class="navbar-brand brand-logo" href="#">
            @if(auth()->check() && auth()->user()->agency_id)
                @php $agency = $currentAgency ?? null; @endphp
                @if($agency && $agency->logo)
                    <img src="{{ asset($agency->logo) }}" alt="logo"/>
                @else
                    <span class="navbar-brand-text">{{ $agency->agency_name ?? 'Agency' }}</span>
                @endif
            @else
                <img src="{{ asset('assets/images/leadbridge_logo.svg') }}" alt="logo"/>
            @endif
        </a>

        {{-- Mini logo (tablet/mobile) --}}
        <a class="navbar-brand brand-logo-mini" href="#">
            @if(auth()->check() && auth()->user()->agency_id && $currentAgency && $currentAgency->logo)
                <img src="{{ asset($currentAgency->logo) }}" alt="logo"/>
            @else
                <img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo"/>
            @endif
        </a>

    </div>

    {{-- ── Menu wrapper ── --}}
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

        {{-- Sidebar mini toggle (desktop only) --}}
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>

        {{-- Agency select --}}
        @if(optional(auth()->user()->role)->name == 'Super Admin')
        <ul class="navbar-nav navbar-center-nav">
            <li class="nav-item" style="width:100%">
                <select id="agency-select" class="form-control select2" multiple>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}"
                            {{ in_array($agency->id, session('agency_ids', [])) ? 'selected' : '' }}>
                            {{ $agency->agency_name }}
                        </option>
                    @endforeach
                </select>
            </li>
        </ul>
        @endif

        {{-- Bell + Profile --}}
        <ul class="navbar-nav navbar-nav-right">

            {{-- Bell --}}
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle"
                   id="notificationDropdown"
                   href="#"
                   data-toggle="dropdown"
                   aria-expanded="false">
                    <i class="icon-bell mx-0"></i>
                    <span class="count">{{ $unreadCount ?? 0 }}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                     aria-labelledby="notificationDropdown">
                    <p class="mb-0 font-weight-bold dropdown-header">Notifications</p>

                    @forelse($notifications as $notification)
                        <a class="dropdown-item preview-item {{ $notification->read_at ? '' : 'unread' }}" href="#">
                            <div class="preview-item-content">
                                <p class="preview-subject">{{ $notification->data['title'] ?? '' }}</p>
                                <p class="small-text text-muted">{{ $notification->data['message'] ?? '' }}</p>
                            </div>
                        </a>
                    @empty
                        <a class="dropdown-item text-center text-muted" style="font-size:13px; padding:16px;">
                            No notifications
                        </a>
                    @endforelse
                </div>
            </li>

            {{-- Profile --}}
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <img src="{{ auth()->user()->profile
                            ? asset(auth()->user()->profile)
                            : asset('assets/images/default-profile.png') }}"
                         alt="profile">
                    <div class="text-left">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ auth()->user()->role->name }}</div>
                    </div>
                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown shadow-sm"
                     aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                        <i class="ti-user text-primary mr-2"></i> Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    @if(strtolower(auth()->user()->role->name) == 'admin')
                        <a class="dropdown-item" href="{{ route('agency.show') }}">
                            <i class="ti-briefcase text-primary mr-2"></i> Agency
                        </a>
                        <div class="dropdown-divider"></div>
                    @endif
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                        <i class="ti-power-off mr-2"></i> Logout
                    </a>
                </div>
            </li>

        </ul>

        {{-- Off-canvas toggle (tablet/mobile) --}}
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>

    </div>
</nav>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#agency-select').select2({
        placeholder: "Select Agency",
        allowClear: true,
        dropdownParent: $('body'),
        width: '850px'
    });

    $('#agency-select').on('change', function () {
        let agencyIds = $(this).val();
        $.ajax({
            url: "{{ route('set.agency') }}",
            type: "POST",
            data: {
                agency_ids: agencyIds,
                _token: "{{ csrf_token() }}"
            },
            success: function () {
                location.reload();
            }
        });
    });
});
</script>
