<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

@include('includes.notifications.styles')
<style>
    .navbar {
        background: #ffffff;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
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

    .navbar-brand img {
        height: 36px;
        width: auto;
    }

    .navbar-brand-text {
        font-size: 18px;
        font-weight: 700;
        color: #3f3cbb;
    }

    .navbar-brand.brand-logo-mini {
        display: none;
    }

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

    .navbar-toggler:focus {
        outline: none;
        box-shadow: none;
    }

    .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .navbar-dropdown {
        position: absolute;
        font-size: 0.9rem;
        margin-top: 0;
        right: 0;
        left: 0 !important;
        top: 70px;
    }

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

    #agency-select {
        width: 260px !important;
    }

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

    .count-indicator:hover {
        background: #eef0fb;
        color: #3f3cbb;
    }

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
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.10);
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

    .preview-item:hover {
        background: #fafafa;
    }

    .preview-subject {
        font-size: 13px;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
    }

    .small-text {
        font-size: 12px;
        color: #777;
        line-height: 1.4;
        margin: 0;
    }

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

    .nav-profile .nav-link:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    .nav-profile img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        border: 2px solid #e2e5f0;
    }

    .user-name {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        line-height: 1.2;
        white-space: nowrap;
    }

    .user-role {
        font-size: 11px;
        color: #888;
        white-space: nowrap;
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ---------- Profile dropdown ---------- */
    .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .navbar-dropdown.profile-menu {
        left: auto !important;
        right: -12px;
        width: 290px;
        max-width: calc(100vw - 24px);
        padding: 0;
        border: 1px solid #e9ebf0;
        border-radius: 16px;
        box-shadow: 0 18px 40px -12px rgba(26, 31, 43, 0.22);
        overflow: hidden;
    }

    /* The theme pins every navbar dropdown to left: 0, so a wide menu
       starts at its icon and runs off the right of the screen. Line the
       notification and attendance menus up with their icon's right edge
       instead, so they open leftwards. (Phones: the notification menu is
       pinned full-width - see includes/notifications/styles.blade.php.) */
    @media (min-width: 576px) {
        .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .navbar-dropdown.notif-dropdown,
        .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .navbar-dropdown.attendance-dropdown {
            left: auto !important;
            right: 0;
        }
    }

    .profile-menu-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: linear-gradient(135deg, #f3f2ff 0%, #f8f9fc 100%);
        border-bottom: 1px solid #eef0f4;
    }

    .profile-menu-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #e2e5f0;
    }

    .profile-menu-identity {
        min-width: 0;
        flex: 1;
    }

    .profile-menu-name {
        font-size: 14px;
        font-weight: 700;
        color: #1a1f2b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-menu-email {
        font-size: 12px;
        color: #7a8294;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 1px;
    }

    .profile-menu-role {
        display: inline-block;
        max-width: 100%;
        margin-top: 6px;
        padding: 2px 10px;
        border-radius: 999px;
        background: #4b49ac;
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: top;
    }

    .profile-menu-body {
        padding: 8px;
    }

    .profile-menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 9px 10px;
        border-radius: 10px;
        color: #1a1f2b;
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .profile-menu-item:hover,
    .profile-menu-item:focus {
        background: #f4f5fa;
        color: #1a1f2b;
        text-decoration: none;
    }

    .profile-menu-icon {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #eeeefb;
        color: #4b49ac;
        font-size: 18px;
        line-height: 1;
    }

    /* The glyph's own line-height pushes it off-centre - neutralise it. */
    .profile-menu-icon i,
    .profile-menu-icon i::before {
        display: block;
        line-height: 1;
    }

    .profile-menu-text {
        flex: 1;
        min-width: 0;
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.25;
    }

    .profile-menu-text small {
        display: block;
        font-size: 11.5px;
        font-weight: 400;
        color: #8a92a3;
    }

    .profile-menu-arrow {
        color: #b4bac6;
        font-size: 18px;
    }

    .profile-menu-item.is-danger {
        margin-top: 2px;
    }

    .profile-menu-item.is-danger .profile-menu-icon {
        background: #fdecec;
        color: #e5534b;
    }

    .profile-menu-item.is-danger:hover {
        background: #fff5f5;
    }

    .profile-menu-item.is-danger .profile-menu-text {
        color: #d43c34;
    }

    .navbar-dropdown .dropdown-item {
        font-size: 13.5px;
        padding: 10px 16px;
        color: #333;
        display: flex;
        align-items: center;
        transition: background 0.15s;
    }

    .navbar-dropdown .dropdown-item:hover {
        background: #f5f5f5;
    }

    .navbar-dropdown .dropdown-item.text-danger {
        color: #e53935 !important;
    }

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
        box-shadow: 0 0 0 3px rgba(63, 60, 187, 0.10) !important;
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

    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        font-size: 12px !important;
        color: #fff !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255, 255, 255, 0.7) !important;
        background: transparent !important;
        border: none !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fff !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        margin-top: 0 !important;
        color: #b0b5c8 !important;
        font-size: 16px !important;
        position: absolute !important;
        right: 10px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
    }

    .select2-dropdown {
        border: 1.5px solid #e2e5f0 !important;
        border-radius: 12px !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
        overflow: hidden !important;
        margin-top: 4px !important;
    }

    .select2-search--dropdown {
        padding: 8px !important;
    }

    .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #e2e5f0 !important;
        border-radius: 8px !important;
        padding: 7px 12px !important;
        font-size: 13px !important;
        outline: none !important;
    }

    .select2-search--dropdown .select2-search__field:focus {
        border-color: #3f3cbb !important;
        box-shadow: 0 0 0 2px rgba(63, 60, 187, 0.10) !important;
    }

    .select2-results__option {
        font-size: 13.5px !important;
        padding: 9px 12px !important;
        border-radius: 8px !important;
        color: #3a3d52 !important;
        transition: background 0.15s !important;
    }

    .select2-results__option--highlighted {
        background: #f0efff !important;
        color: #3f3cbb !important;
    }

    .select2-results__option[aria-selected="true"] {
        background: #ebe9ff !important;
        color: #3f3cbb !important;
        font-weight: 600 !important;
    }

    /* =========================================================
       select2 sizing - effective values unchanged from the old
       per-breakpoint blocks. NB: these aren't scoped to the navbar,
       so they also apply to select2 fields in page content.
       ========================================================= */
    @media (min-width: 1441px) {
        .select2-container {
            width: 550px !important;
            max-width: 100%;
        }
    }

    @media (max-width: 1440px) {
        .select2-container {
            width: 500px !important;
            max-width: 100%;
        }
    }

    @media (max-width: 1199px) and (min-width: 1025px) {
        .select2-container {
            width: 380px !important;
            max-width: 100%;
        }
    }

    @media (max-width: 1024px) {
        .select2-container {
            width: 300px !important;
            max-width: 100%;
        }
    }

    @media (max-width: 1023px) and (min-width: 768px) {
        .select2-container {
            display: none !important;
        }
    }

    @media (max-width: 480px) {
        .select2-container--default .select2-selection--multiple {
            display: none !important;
        }
    }

    /* =========================================================
       RESPONSIVE NAVBAR
       Below 992px the theme turns the sidebar into an off-canvas
       panel pinned at top: 60px and offsets the page by 60px, so
       the header is a single 60px row there at every phone/tablet
       width (it used to shrink to 56/52/48/44px and could wrap,
       leaving gaps above the sidebar or overlapping the page).
       ========================================================= */
    @media (max-width: 991px) {

        .navbar {
            min-height: 60px;
            flex-wrap: nowrap;
        }

        /* .navbar-prefixed to beat the theme's own <992px rules
           (55px brand box, 40px-wide mini logo). */
        .navbar .navbar-brand-wrapper {
            width: auto;
            min-width: 0;
            height: 60px;
            flex: 0 0 auto;
            padding: 0 12px 0 16px;
            border-right: none;
        }

        .navbar .navbar-brand-wrapper .navbar-brand.brand-logo {
            display: none;
        }

        .navbar .navbar-brand-wrapper .navbar-brand.brand-logo-mini {
            display: flex;
            align-items: center;
            padding: 0;
        }

        .navbar .navbar-brand-wrapper .brand-logo-mini img {
            width: auto;
            height: 30px;
        }

        .navbar .navbar-menu-wrapper {
            width: auto;
            height: 60px;
            padding: 0 12px 0 4px;
            flex-wrap: nowrap;
        }

        /* Desktop "shrink sidebar" toggle - meaningless once the
           sidebar is off-canvas. */
        .navbar-toggler[data-toggle="minimize"] {
            display: none !important;
        }

        .navbar-nav.navbar-center-nav {
            display: none !important;
        }

        .navbar-nav-right {
            gap: 8px;
            min-width: 0;
        }

        /* The theme adds 1rem either side of every item on top of the
           gap above - enough to overflow a 360px phone. */
        .navbar .navbar-menu-wrapper .navbar-nav .nav-item {
            margin-left: 0;
            margin-right: 0;
        }

        /* Dropdowns are positioned against the menu row here (the
           theme makes .nav-item static), so -12px would push the
           profile menu off the right edge. */
        .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .navbar-dropdown.profile-menu {
            right: 0;
        }

        .user-name,
        .user-role {
            display: none;
        }

        .navbar-dropdown.preview-list {
            width: 300px;
        }

        /* Off-canvas menu button - same 40px tile as the bell and
           attendance pill next to it. */
        .navbar .navbar-menu-wrapper .navbar-toggler.navbar-toggler-right {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            margin: 0 0 0 8px;
            padding: 0;
            background: #f7f8fc;
            border: 1px solid #e2e5f0;
            border-radius: 10px;
            color: #444;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .navbar .navbar-menu-wrapper .navbar-toggler.navbar-toggler-right:hover {
            background: #eef0fb;
            color: #3f3cbb;
        }
    }

    /* Phones */
    @media (max-width: 575.98px) {

        .navbar .navbar-brand-wrapper {
            padding: 0 8px 0 12px;
        }

        .navbar .navbar-brand-wrapper .brand-logo-mini img {
            height: 26px;
        }

        .navbar .navbar-menu-wrapper {
            padding: 0 10px 0 0;
        }

        .navbar-nav-right {
            gap: 6px;
        }

        .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .count-indicator,
        .navbar .navbar-menu-wrapper .navbar-toggler.navbar-toggler-right {
            width: 36px;
            height: 36px;
        }

        .navbar .att-nav .attendance-pill {
            height: 36px;
        }

        .navbar .navbar-menu-wrapper .navbar-toggler.navbar-toggler-right {
            margin-left: 6px;
        }

        .nav-profile .nav-link {
            padding: 2px;
        }

        .nav-profile img {
            width: 32px;
            height: 32px;
        }

        /* Pin the attendance menu to the viewport like the
           notification menu (includes/notifications/styles), so it
           can't run off the right edge. */
        .navbar .dropdown-menu.attendance-dropdown {
            position: fixed !important;
            top: 66px !important;
            left: 10px !important;
            right: 10px !important;
            transform: none !important;
            width: auto !important;
            max-width: 420px;
            margin-left: auto !important;
            max-height: calc(100vh - 76px);
            overflow-x: hidden !important;
            overflow-y: auto !important;
            border-radius: 14px !important;
        }
    }

    /* Very narrow phones */
    @media (max-width: 359px) {

        .navbar .navbar-brand-wrapper {
            padding: 0 6px 0 10px;
        }

        .navbar .navbar-brand-wrapper .brand-logo-mini img {
            height: 22px;
        }

        .navbar-nav-right {
            gap: 4px;
        }

        .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .count-indicator,
        .navbar .navbar-menu-wrapper .navbar-toggler.navbar-toggler-right {
            width: 32px;
            height: 32px;
        }

        .navbar .att-nav .attendance-pill {
            height: 32px;
        }

        .nav-profile img {
            width: 28px;
            height: 28px;
        }
    }

    /* =========================================================
       Attendance widget
       The <li> carries data-status (not_started / working /
       on_break / completed) and every status colour hangs off it.
       Selectors start with .navbar .att-nav so they beat the theme's
       .navbar .navbar-menu-wrapper .navbar-nav ... rules (1rem link
       font, icon margin-right, dropdown padding).
       ========================================================= */
    .att-nav {
        --att-tone: #6c7280;
        --att-tint: #f4f5f8;
        --att-line: #e2e5f0;
    }

    .att-nav[data-status="working"] {
        --att-tone: #1a7a4c;
        --att-tint: #e6f7ee;
        --att-line: #bfe8d1;
    }

    .att-nav[data-status="on_break"] {
        --att-tone: #b77900;
        --att-tint: #fff4db;
        --att-line: #f6dfa6;
    }

    .att-nav[data-status="completed"] {
        --att-tone: #2264d1;
        --att-tint: #eaf2ff;
        --att-line: #c9dcfb;
    }

    /* ---------- Pill (dropdown trigger) ---------- */
    .navbar .att-nav .attendance-pill {
        display: flex;
        align-items: center;
        gap: 9px;
        height: 40px;
        padding: 0 12px 0 4px;
        border-radius: 999px;
        background: var(--att-tint);
        border: 1px solid var(--att-line);
        color: #1a1f2b;
        font-size: 13px;
        text-decoration: none;
        white-space: nowrap;
        transition: box-shadow 0.2s, background 0.2s, border-color 0.2s;
    }

    .navbar .att-nav .attendance-pill:hover,
    .navbar .att-nav.show .attendance-pill {
        box-shadow: 0 4px 14px -6px var(--att-tone);
    }

    .navbar .att-nav .attendance-pill::after {
        display: none; /* no Bootstrap caret */
    }

    .navbar .att-nav .att-pill-icon {
        position: relative;
        width: 32px;
        height: 32px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #fff;
        color: var(--att-tone);
        box-shadow: 0 1px 3px rgba(26, 31, 43, 0.08);
    }

    /* The theme gives every navbar dropdown icon margin-right: .5rem,
       which pushed this glyph off-centre - cancel it (same specificity
       + .att-nav), and neutralise the glyph's own line-height like
       .profile-menu-icon does. */
    .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown.att-nav .att-pill-icon i {
        margin: 0;
        font-size: 17px;
        line-height: 1;
        vertical-align: baseline;
    }

    .navbar .att-nav .att-pill-icon i,
    .navbar .att-nav .att-pill-icon i::before {
        display: block;
        line-height: 1;
    }

    .navbar .att-nav .attendance-dot {
        position: absolute;
        top: 1px;
        right: 1px;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        border: 2px solid #fff;
        background: #9ca3af;
    }

    .navbar .att-nav .attendance-dot.working {
        background: #22c55e;
        animation: attPulse 1.6s infinite;
    }

    .navbar .att-nav .attendance-dot.on_break {
        background: #f59e0b;
    }

    .navbar .att-nav .attendance-dot.completed {
        background: #3b82f6;
    }

    @keyframes attPulse {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, .6); }
        70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    .navbar .att-nav .att-pill-text {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }

    .navbar .att-nav .attendance-timer {
        font-size: 13.5px;
        font-weight: 700;
        color: #1a1f2b;
        font-variant-numeric: tabular-nums;
        letter-spacing: 0.2px;
    }

    .navbar .att-nav .att-pill-label {
        font-size: 10.5px;
        font-weight: 600;
        color: var(--att-tone);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    /* ---------- Dropdown panel ---------- */
    .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .navbar-dropdown.attendance-dropdown {
        width: 310px;
        padding: 0;
        border: 1px solid #e9ebf0;
        border-radius: 16px;
        box-shadow: 0 18px 40px -12px rgba(26, 31, 43, 0.25);
        overflow: hidden;
    }

    .attendance-dropdown i {
        margin-right: 0 !important; /* theme adds .5rem to every dropdown icon */
    }

    .attendance-dropdown .att-hero {
        position: relative;
        padding: 16px 18px 18px;
        color: #fff;
        background:
            radial-gradient(circle at 100% 0%, rgba(255, 255, 255, 0.18) 0, rgba(255, 255, 255, 0) 55%),
            linear-gradient(135deg, #4b49ac 0%, #6c63ff 100%);
    }

    .attendance-dropdown .att-hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .attendance-dropdown .att-hero-date {
        font-size: 12px;
        font-weight: 600;
        opacity: 0.85;
    }

    .attendance-dropdown .att-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .attendance-dropdown .att-status-badge::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #d1d5db;
    }

    .att-nav[data-status="working"] .att-status-badge::before { background: #4ade80; }
    .att-nav[data-status="on_break"] .att-status-badge::before { background: #fbbf24; }
    .att-nav[data-status="completed"] .att-status-badge::before { background: #93c5fd; }

    .attendance-dropdown .att-hero-timer {
        margin-top: 12px;
        font-size: 32px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: 0.5px;
        font-variant-numeric: tabular-nums;
    }

    .attendance-dropdown .att-hero-caption {
        margin-top: 5px;
        font-size: 12px;
        opacity: 0.8;
    }

    .attendance-dropdown .att-body {
        padding: 14px 16px 16px;
    }

    .attendance-dropdown .att-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    .attendance-dropdown .att-stat {
        display: flex;
        flex-direction: column;
        gap: 3px;
        padding: 9px 10px;
        border-radius: 10px;
        background: #f7f8fc;
        border: 1px solid #eef0f5;
        min-width: 0;
    }

    .attendance-dropdown .att-stat-label {
        font-size: 10.5px;
        font-weight: 600;
        color: #8a92a3;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .attendance-dropdown .att-stat-value {
        font-size: 13.5px;
        font-weight: 700;
        color: #1a1f2b;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .attendance-dropdown .att-current-reason {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 8px 10px;
        border-radius: 10px;
        background: #fff4db;
        color: #8a5a00;
        font-size: 12.5px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .attendance-dropdown .att-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }

    .attendance-dropdown .att-punch-btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 0;
        padding: 10px 8px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
    }

    .attendance-dropdown .att-punch-btn i {
        font-size: 16px;
    }

    .attendance-dropdown .att-punch-btn:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    .attendance-dropdown .att-punch-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Primary actions: solid. Secondary (break / clock out while
       working): tinted, so the two side-by-side read differently. */
    .attendance-dropdown .att-punch-btn.act-time_in {
        background: #4b49ac;
        color: #fff;
        box-shadow: 0 6px 14px -8px #4b49ac;
    }

    .attendance-dropdown .att-punch-btn.act-end_break {
        background: #f59e0b;
        color: #fff;
        box-shadow: 0 6px 14px -8px #b45309;
    }

    .attendance-dropdown .att-punch-btn.act-start_break {
        background: #fff4db;
        border-color: #f6dfa6;
        color: #8a5a00;
    }

    .attendance-dropdown .att-punch-btn.act-time_out {
        background: #fdecec;
        border-color: #f7cfcd;
        color: #d43c34;
    }

    .attendance-dropdown .att-punch-btn.act-done {
        background: #eaf2ff;
        border-color: #c9dcfb;
        color: #2264d1;
        opacity: 1;
    }

    .attendance-dropdown .att-foot-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        border-top: 1px solid #eef0f4;
        background: #fafbfd;
        color: #4b49ac;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    .attendance-dropdown .att-foot-link .mdi-arrow-right {
        margin-left: auto !important;
        transition: transform 0.15s ease;
    }

    .attendance-dropdown .att-foot-link:hover {
        background: #f3f2ff;
        color: #3f3e91;
        text-decoration: none;
    }

    .attendance-dropdown .att-foot-link:hover .mdi-arrow-right {
        transform: translateX(3px);
    }

    /* ---------- Break reason panel ---------- */
    .attendance-dropdown .att-reason-panel {
        padding: 14px 16px 16px;
    }

    .attendance-dropdown .att-reason-title {
        font-size: 13px;
        font-weight: 700;
        color: #1a1f2b;
        margin-bottom: 10px;
    }

    .attendance-dropdown .att-reason-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
    }

    .attendance-dropdown .att-reason-item {
        padding: 7px 12px;
        border: 1px solid #e2e5f0;
        border-radius: 999px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5164;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }

    .attendance-dropdown .att-reason-item:hover {
        border-color: #d9d6ff;
        color: #4b49ac;
    }

    .attendance-dropdown .att-reason-item.selected {
        background: #4b49ac;
        border-color: #4b49ac;
        color: #fff;
    }

    .attendance-dropdown .att-reason-other-input {
        display: none;
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #dcdfe6;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 13px;
        margin-bottom: 10px;
        outline: none;
    }

    .attendance-dropdown .att-reason-other-input:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
    }

    .attendance-dropdown .att-reason-actions {
        display: flex;
        gap: 8px;
    }

    .attendance-dropdown .att-reason-confirm,
    .attendance-dropdown .att-reason-cancel {
        flex: 1;
        border: none;
        border-radius: 10px;
        padding: 10px 0;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .attendance-dropdown .att-reason-confirm {
        background: #4b49ac;
        color: #fff;
    }

    .attendance-dropdown .att-reason-confirm:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .attendance-dropdown .att-reason-cancel {
        background: #f4f5f8;
        color: #4a5164;
    }

    /* ---------- Responsive ---------- */
    /* Desktop: the theme's 1rem either side of each item plus the 8px
       flex gap left ~40px before the bell; trim the timer's side. */
    @media (min-width: 992px) {
        .navbar .navbar-menu-wrapper .navbar-nav .nav-item.att-nav {
            margin-right: 0.25rem;
        }
    }

    @media (max-width: 991px) {
        .navbar .att-nav .att-pill-label {
            display: none;
        }
    }

    /* Phones: icon + timer only; the panel is pinned full-width (see
       the RESPONSIVE NAVBAR block above). */
    @media (max-width: 575.98px) {
        .navbar .att-nav .attendance-pill {
            gap: 6px;
            padding: 0 10px 0 2px;
        }

        .navbar .att-nav .att-pill-icon {
            width: 30px;
            height: 30px;
        }

        .navbar .att-nav .attendance-timer {
            font-size: 12.5px;
        }

        /* Compact panel */
        .attendance-dropdown .att-hero {
            padding: 12px 14px 14px;
        }

        .attendance-dropdown .att-hero-date {
            font-size: 11.5px;
        }

        .attendance-dropdown .att-status-badge {
            padding: 2px 9px;
            font-size: 10.5px;
        }

        .attendance-dropdown .att-hero-timer {
            margin-top: 8px;
            font-size: 26px;
        }

        .attendance-dropdown .att-hero-caption {
            margin-top: 3px;
            font-size: 11.5px;
        }

        .attendance-dropdown .att-body,
        .attendance-dropdown .att-reason-panel {
            padding: 12px;
        }

        .attendance-dropdown .att-stats {
            gap: 6px;
        }

        .attendance-dropdown .att-stat {
            padding: 7px 8px;
            gap: 2px;
        }

        .attendance-dropdown .att-stat-label {
            font-size: 10px;
        }

        .attendance-dropdown .att-stat-value {
            font-size: 12.5px;
        }

        .attendance-dropdown .att-current-reason {
            margin-top: 8px;
            padding: 7px 9px;
            font-size: 12px;
        }

        .attendance-dropdown .att-actions {
            margin-top: 10px;
            gap: 6px;
        }

        .attendance-dropdown .att-punch-btn {
            padding: 9px 6px;
            font-size: 12.5px;
        }

        .attendance-dropdown .att-reason-item {
            padding: 6px 11px;
            font-size: 12px;
        }

        .attendance-dropdown .att-reason-confirm,
        .attendance-dropdown .att-reason-cancel {
            padding: 9px 0;
            font-size: 12.5px;
        }

        .attendance-dropdown .att-foot-link {
            padding: 10px 12px;
            font-size: 12.5px;
        }
    }

    /* Narrow phones: stats would squeeze below ~90px each - let a
       long time wrap instead of clipping. */
    @media (max-width: 359px) {
        .attendance-dropdown .att-stat-value {
            white-space: normal;
        }

        .attendance-dropdown .att-punch-btn i {
            display: none;
        }
    }

    @media (max-width: 400px) {
        .navbar .att-nav .att-pill-text {
            display: none;
        }

        .navbar .att-nav .attendance-pill {
            padding: 0 2px;
        }
    }

        .chat-unread-toast {
            display: none;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;

            width: 360px;
            max-width: calc(100vw - 500px);

            background: #fff;

            padding: 7px 9px;
        }

        .chat-unread-toast-inner {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-unread-icon {
            width: 32px;
            height: 32px;
            min-width: 32px;

            border-radius: 50%;
            background: #f8d7da;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-unread-icon i {
            font-size: 16px;
            color: #dc3545;
        }

        .chat-unread-message {
            flex: 1;
            min-width: 0;
        }

        #chat-unread-toast-text {
            font-size: 12px;
            font-weight: 600;
            color: #343a40;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #chat-unread-count-text {
            color: #dc3545;
            font-weight: 700;
        }

        .chat-open-btn {
            padding: 5px 10px !important;
            white-space: nowrap;
        }

        .chat-unread-close {
            border: 0;
            background: transparent;

            color: #999;
            font-size: 20px;
            line-height: 1;

            cursor: pointer;
            padding: 0 3px;

            flex-shrink: 0;
        }

        .chat-unread-close:hover {
            color: #555;
        }
        @media (max-width: 991px) {

        .chat-unread-toast {
            width: 320px;
            max-width: calc(100vw - 80px);
        }

        #chat-unread-toast-text {
            font-size: 12px;
        }

        .chat-open-btn {
            font-size: 12px !important;
            padding: 5px 8px !important;
        }
    }
    @media (max-width: 576px) {

        .chat-unread-toast {
            width: calc(100vw - 24px);
            max-width: none;

            left: 50%;
            top: 50%;

            padding: 8px 9px;
            border-radius: 8px;
        }

        .chat-unread-toast-inner {
            gap: 7px;
        }

        .chat-unread-icon {
            width: 30px;
            height: 30px;
            min-width: 30px;
        }

        .chat-unread-icon i {
            font-size: 14px;
        }

        #chat-unread-toast-text {
            font-size: 11px;
        }

        .chat-open-btn {
            font-size: 11px !important;
            padding: 5px 7px !important;
        }

        .chat-unread-close {
            font-size: 18px;
            padding: 0 2px;
        }
    }
    @media (max-width: 400px) {

        .chat-unread-toast {
            width: calc(100vw - 16px);
            padding: 7px;
        }

        .chat-unread-toast-inner {
            gap: 5px;
        }

        .chat-unread-icon {
            width: 28px;
            height: 28px;
            min-width: 28px;
        }

        #chat-unread-toast-text {
            font-size: 10px;
        }

        .chat-open-btn {
            font-size: 10px !important;
            padding: 4px 6px !important;
        }

        .chat-unread-close {
            font-size: 17px;
        }
    }
</style>

<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

    {{-- ── Brand ── --}}
    <div class="navbar-brand-wrapper d-flex align-items-center justify-content-center">

        {{-- Full logo (desktop) --}}
        <!-- <a class="navbar-brand brand-logo" href="#">
            @if(auth()->check() && auth()->user()->agency_id)
                @php $agency = $currentAgency ?? null; @endphp
                @if($agency && $agency->logo)
                    <img src="{{ asset('assets/images/agile-logo.svg') }}" alt="logo"/>
                @else
                    <span class="navbar-brand-text">{{ $agency->agency_name ?? 'Agency' }}</span>
                @endif
            @else
                <img src="{{ asset('assets/images/agile-logo.svg') }}" alt="logo"/>
            @endif
        </a> -->
        <a class="navbar-brand brand-logo" href="{{ route('dashboard') }}" title="Go to Dashboard">
            <img src="{{ asset('assets/images/agile-logo.svg') }}" alt="logo" />
        </a>

        {{-- Mini logo (tablet/mobile) --}}
        <!-- <a class="navbar-brand brand-logo-mini" href="#">
            @if(auth()->check() && auth()->user()->agency_id && $currentAgency && $currentAgency->logo)
                <img src="{{ asset($currentAgency->logo) }}" alt="logo"/>
            @else
                <img src="{{ asset('assets/images/agile-logo.svg') }}" alt="logo"/>
            @endif
        </a> -->
        <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}" title="Go to Dashboard">
            <img src="{{ asset('assets/images/agile-mini.svg') }}" alt="logo" />
        </a>

    </div>

    {{-- ── Menu wrapper ── --}}
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

        {{-- Sidebar mini toggle (desktop only) --}}
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>

        {{-- Bell + Profile --}}
        <ul class="navbar-nav navbar-nav-right">

            {{-- Attendance - timer pill + today panel. data-status is
                 set by the script below and drives every colour. --}}
            @auth
            <li class="nav-item dropdown att-nav" id="attNav" data-status="not_started">
                <a class="nav-link attendance-pill dropdown-toggle" href="#" data-toggle="dropdown" id="attendanceDropdown"
                   aria-haspopup="true" aria-expanded="false" title="Attendance">
                    <span class="att-pill-icon">
                        <i class="mdi mdi-clock-outline" id="navAttIcon"></i>
                        <span class="attendance-dot" id="navAttDot"></span>
                    </span>
                    <span class="att-pill-text">
                        <span class="attendance-timer" id="navAttTimer">00:00:00</span>
                        <span class="att-pill-label" id="navAttLabel">Not started</span>
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown attendance-dropdown" aria-labelledby="attendanceDropdown">
                    <div class="att-hero">
                        <div class="att-hero-top">
                            <span class="att-hero-date" id="navAttDate"></span>
                            <span class="att-status-badge" id="navAttStatusText">Loading…</span>
                        </div>
                        <div class="att-hero-timer" id="navAttBigTimer">00:00:00</div>
                        <div class="att-hero-caption">Worked today</div>
                    </div>

                    <div class="att-body" id="attBody">
                        <div class="att-stats">
                            <div class="att-stat">
                                <span class="att-stat-label">Time In</span>
                                <span class="att-stat-value" id="navAttTimeIn">--:--</span>
                            </div>
                            <div class="att-stat">
                                <span class="att-stat-label">Time Out</span>
                                <span class="att-stat-value" id="navAttTimeOut">--:--</span>
                            </div>
                            <div class="att-stat">
                                <span class="att-stat-label">Breaks</span>
                                <span class="att-stat-value" id="navAttBreak">0h 00m</span>
                            </div>
                        </div>
                        <div class="att-current-reason" id="attCurrentReason" style="display:none;"></div>
                        <div class="att-actions" id="attActions"></div>
                    </div>

                    <div class="att-reason-panel" id="attReasonPanel" style="display:none;">
                        <div class="att-reason-title">Why are you taking a break?</div>
                        <div class="att-reason-list" id="attReasonList"></div>
                        <input type="text" id="attReasonOtherInput" class="att-reason-other-input" placeholder="Enter reason" maxlength="255">
                        <div class="att-reason-actions">
                            <button type="button" class="att-reason-cancel" id="attReasonCancel">Cancel</button>
                            <button type="button" class="att-reason-confirm" id="attReasonConfirm" disabled>Start Break</button>
                        </div>
                    </div>

                    <a href="{{ route('attendance.index') }}" class="att-foot-link">
                        <i class="mdi mdi-chart-timeline-variant"></i>
                        View attendance report
                        <i class="mdi mdi-arrow-right"></i>
                    </a>
                </div>
            </li>
            @endauth

            {{-- Notifications bell. $notifications / $unreadCount come
                 from the view composer in AppServiceProvider. Each item
                 goes through notifications.open, which marks it read and
                 redirects (e.g. to the lead's View page). Row markup and
                 styles are shared with the dashboard panel - see
                 includes/notifications/. --}}
            @auth
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle"
                   id="notificationDropdown"
                   href="#"
                   data-toggle="dropdown"
                   aria-expanded="false"
                   title="Notifications">
                    <i class="icon-bell mx-0"></i>
                    @if(($unreadCount ?? 0) > 0)
                        <span class="count">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown notif-dropdown"
                     aria-labelledby="notificationDropdown">

                    <div class="notif-head">
                        <h6 class="notif-head-title">
                            Notifications
                            @if(($unreadCount ?? 0) > 0)
                                <span class="notif-count">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                            @endif
                        </h6>

                        @if(($unreadCount ?? 0) > 0)
                            <form method="POST" action="{{ route('notifications.readAll') }}" class="m-0">
                                @csrf
                                <button type="submit" class="notif-mark-all">Mark all as read</button>
                            </form>
                        @endif
                    </div>

                    <div class="notif-scroll">
                        @forelse($notifications as $notification)
                            @include('includes.notifications.item', ['notification' => $notification])
                        @empty
                            <div class="notif-empty">
                                <i class="mdi mdi-bell-sleep-outline"></i>
                                <span>You're all caught up.<br>No notifications yet.</span>
                            </div>
                        @endforelse
                    </div>

                    @if(count($notifications) > 0)
                        <div class="notif-foot">Showing your latest {{ count($notifications) }}</div>
                    @endif
                </div>
            </li>
            @endauth

            {{-- Profile --}}
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown"
                    title="{{ auth()->user()->name }}" aria-label="Account menu">
                    <img id="headerProfileAvatar" src="{{ auth()->user()->profile
                            ? asset(auth()->user()->profile)
                            : asset('assets/images/default-profile.png') }}"
                        alt="profile">
                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown profile-menu"
                    aria-labelledby="profileDropdown">
                    <div class="profile-menu-head">
                        <img src="{{ auth()->user()->profile
                                ? asset(auth()->user()->profile)
                                : asset('assets/images/default-profile.png') }}"
                            alt="" class="profile-menu-avatar" id="headerMenuAvatar">
                        <div class="profile-menu-identity">
                            <div class="profile-menu-name" id="headerProfileName">{{ auth()->user()->name }}</div>
                            <div class="profile-menu-email">{{ auth()->user()->email }}</div>
                            @if(auth()->user()->role)
                                <span class="profile-menu-role" title="{{ auth()->user()->role->name }}">{{ auth()->user()->role->name }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="profile-menu-body">
                        <a class="profile-menu-item" href="{{ route('profile.index') }}">
                            <span class="profile-menu-icon"><i class="mdi mdi-account-outline"></i></span>
                            <span class="profile-menu-text">
                                My Profile
                                <small>Account details and photo</small>
                            </span>
                            <i class="mdi mdi-chevron-right profile-menu-arrow"></i>
                        </a>

                        <a class="profile-menu-item is-danger" href="{{ route('logout') }}">
                            <span class="profile-menu-icon"><i class="mdi mdi-logout"></i></span>
                            <span class="profile-menu-text">Log out</span>
                        </a>
                    </div>
                </div>
            </li>

        </ul>

        {{-- Off-canvas toggle (tablet/mobile) --}}
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>

    </div>
    {{-- Chat unread notification --}}
  <div id="chat-unread-toast" class="chat-unread-toast">

    <div class="chat-unread-toast-inner">

        {{-- Chat Icon --}}
        <div class="chat-unread-icon">
            <i class="ti-comments"></i>
        </div>

        {{-- Message --}}
        <div class="chat-unread-message">
            <div id="chat-unread-toast-text">
                You have
                <span id="chat-unread-count-text">0</span>
                unread messages.
            </div>
        </div>

        {{-- Open Chat --}}
        <button type="button"
                id="chat-open-unread-btn"
                class="btn btn-primary btn-sm chat-open-btn">
            Open Chat
        </button>

        {{-- Close --}}
        <button type="button"
                id="chat-unread-toast-close"
                class="chat-unread-close"
                aria-label="Close">
            &times;
        </button>

    </div>

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


});
</script>

@auth
<script>
    $(function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const statusUrl = "{{ route('attendance.status') }}";
        const punchUrl = "{{ route('attendance.punch') }}";
        const $dot = $('#navAttDot');
        const $timer = $('#navAttTimer');
        const $statusText = $('#navAttStatusText');
        const $body = $('#attBody');
        const $actions = $('#attActions');
        const $currentReason = $('#attCurrentReason');
        const $timeIn = $('#navAttTimeIn');
        const $timeOut = $('#navAttTimeOut');
        const $nav = $('#attNav');
        const $bigTimer = $('#navAttBigTimer');
        const $label = $('#navAttLabel');
        const $icon = $('#navAttIcon');
        const $break = $('#navAttBreak');

        const STATUS_UI = {
            not_started: { label: 'Not started', icon: 'mdi-clock-outline' },
            working:     { label: 'Working',     icon: 'mdi-briefcase-clock-outline' },
            on_break:    { label: 'On break',    icon: 'mdi-coffee-outline' },
            completed:   { label: 'Day complete', icon: 'mdi-check-circle-outline' }
        };

        const ACTION_ICONS = {
            time_in: 'mdi-login-variant',
            start_break: 'mdi-coffee-outline',
            end_break: 'mdi-play-circle-outline',
            time_out: 'mdi-logout-variant'
        };

        $('#navAttDate').text(new Date().toLocaleDateString('en-GB', {
            weekday: 'short', day: 'numeric', month: 'short', timeZone: 'Europe/London'
        }));

        const $reasonPanel = $('#attReasonPanel');
        const $reasonList = $('#attReasonList');
        const $reasonOtherInput = $('#attReasonOtherInput');
        const $reasonCancel = $('#attReasonCancel');
        const $reasonConfirm = $('#attReasonConfirm');

        // Predefined reasons shown in the picker. "Other" reveals a free-text
        // field; whatever the user types becomes the actual stored reason.
        const BREAK_REASONS = [
            'Lunch Break',
            'Tea / Coffee Break',
            'Personal Break',
            'Short Break',
            'Other'
        ];

        let selectedReason = null;

        // =========================
        // SAFE VARIABLES
        // =========================
        // workedBase: authoritative worked-seconds as of the last server sync
        // (computed server-side in Europe/London by AttendanceController).
        // syncedAt: client Date.now() at the moment workedBase was fetched.
        // We only ever ADD real elapsed milliseconds on top of workedBase
        // while actively working - we never recompute from wall-clock time,
        // so refreshing the page (or being on break) can't inflate the timer.
        let workedBase = 0;
        let breakBase = 0;
        let syncedAt = null;
        let status = null;
        let timerIntervalId = null;

        // =========================
        // FORMATTER
        // =========================
        function formatHMS(seconds) {
            seconds = Number(seconds) || 0;
            if (!isFinite(seconds) || seconds < 0) {
                seconds = 0;
            }
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = Math.floor(seconds % 60);
            return [
                h.toString().padStart(2, '0'),
                m.toString().padStart(2, '0'),
                s.toString().padStart(2, '0')
            ].join(':');
        }

        // =========================
        // TIMER
        // =========================
        function renderTimer()
        {
            let totalSeconds = workedBase;

            // Only add ticking time while actually working.
            // On break / completed / not started -> stays frozen at workedBase.
            if (status === 'working' && syncedAt) {
                totalSeconds += Math.floor((Date.now() - syncedAt) / 1000);
            }

            if (totalSeconds < 0) totalSeconds = 0;
            $timer.text(formatHMS(totalSeconds));
            $bigTimer.text(formatHMS(totalSeconds));

            // Break total ticks the other way round: only while on break.
            let breakSeconds = breakBase;
            if (status === 'on_break' && syncedAt) {
                breakSeconds += Math.floor((Date.now() - syncedAt) / 1000);
            }
            $break.text(formatHM(breakSeconds));
        }

        function formatHM(seconds) {
            seconds = Math.max(0, Number(seconds) || 0);
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            return h + 'h ' + m.toString().padStart(2, '0') + 'm';
        }

        // =========================
        // ACTION BUTTONS
        // =========================
        function renderActions(actions) {
            $actions.empty();

            if (!actions || !actions.length) {
                $('<button>', {
                    type: 'button',
                    class: 'att-punch-btn act-done',
                    disabled: true
                }).append('<i class="mdi mdi-check-circle-outline"></i>', document.createTextNode('Day complete'))
                  .appendTo($actions);
                return;
            }

            actions.forEach(function (a) {
                $('<button>', {
                    type: 'button',
                    class: 'att-punch-btn act-' + a.action,
                    'data-action': a.action
                }).append(
                    $('<i>', { class: 'mdi ' + (ACTION_ICONS[a.action] || 'mdi-gesture-tap') }),
                    document.createTextNode(a.label)
                ).appendTo($actions);
            });
        }

        function setActionsDisabled(disabled) {
            $actions.find('button').prop('disabled', disabled);
        }

        // =========================
        // REASON PANEL
        // =========================
        // A break can only start once a reason has been picked here - the
        // punch request for "start_break" is never fired without one.
        function openReasonPanel() {
            selectedReason = null;
            $reasonOtherInput.val('').hide();
            $reasonConfirm.prop('disabled', true);
            $reasonList.empty();

            BREAK_REASONS.forEach(function (reason) {
                $('<button>', {
                    type: 'button',
                    class: 'att-reason-item',
                    'data-reason': reason,
                    text: reason
                }).appendTo($reasonList);
            });

            $body.hide();
            $reasonPanel.show();
        }

        function closeReasonPanel() {
            $reasonPanel.hide();
            $body.show();
        }

        $reasonList.on('click', '.att-reason-item', function (e) {
            // Without this, Bootstrap's dropdown treats the click as "outside
            // the toggle" (it only exempts input/select/textarea/form
            // elements from auto-close, not <button>) and closes the menu
            // before the selection is even applied.
            e.stopPropagation();

            $reasonList.find('.att-reason-item').removeClass('selected');
            $(this).addClass('selected');

            const reason = $(this).data('reason');

            if (reason === 'Other') {
                selectedReason = null;
                $reasonOtherInput.val('').show().trigger('focus');
                $reasonConfirm.prop('disabled', true);
            } else {
                selectedReason = reason;
                $reasonOtherInput.hide();
                $reasonConfirm.prop('disabled', false);
            }
        });

        $reasonOtherInput.on('input', function (e) {
            e.stopPropagation();
            const val = $(this).val().trim();
            selectedReason = val || null;
            $reasonConfirm.prop('disabled', !val);
        });

        $reasonOtherInput.on('click', function (e) {
            e.stopPropagation();
        });

        $reasonCancel.on('click', function (e) {
            e.stopPropagation();
            closeReasonPanel();
        });

        $reasonConfirm.on('click', function (e) {
            e.stopPropagation();
            if (!selectedReason) return;
            $reasonConfirm.prop('disabled', true);
            doPunch('start_break', selectedReason);
        });

        // =========================
        function formatTimeHMSTime(timestamp) { 
            if (!timestamp) return "--:--:--";
            let date = new Date(timestamp);
            return date.toLocaleTimeString('en-GB', {
                timeZone: 'Europe/London',
                hour12: false
            });
        }

        function applyState(data) {

            status = data.status;

            // Authoritative snapshot from the server (Europe/London-computed).
            // Every applyState() call - whether from the initial load, a
            // refresh, or a punch - resyncs to this value instead of
            // trusting anything the client previously calculated.
            workedBase = Number(data.worked_seconds) || 0;
            breakBase = Number(data.break_seconds) || 0;
            syncedAt = Date.now();

            if (timerIntervalId) {
                clearInterval(timerIntervalId);
                timerIntervalId = null;
            }

            if (!data.time_in) {
                $timer.text('00:00:00');
                $bigTimer.text('00:00:00');
                $break.text('0h 00m');
            } else {
                renderTimer();
                if (status === 'working' || status === 'on_break') {
                    timerIntervalId = setInterval(renderTimer, 1000);
                }
                // on_break / completed -> no interval, stays frozen at workedBase.
            }

            const ui = STATUS_UI[data.status] || STATUS_UI.not_started;

            $nav.attr('data-status', data.status);
            $dot.attr('class', 'attendance-dot ' + data.status);
            $statusText.text(ui.label);
            $label.text(ui.label);
            $icon.attr('class', 'mdi ' + ui.icon);

            if (data.status === 'on_break' && data.current_break_reason) {
                $currentReason.empty().append(
                    '<i class="mdi mdi-coffee-outline"></i>',
                    document.createTextNode(data.current_break_reason)
                ).show();
            } else {
                $currentReason.hide();
            }

            renderActions(data.available_actions);

            $timeIn.text(data.time_in || '--:--');
            $timeOut.text(data.time_out || '--:--');

            closeReasonPanel();
        }

        function toastMsg(message, success = true) {
            const $t = $('<div>').css({
                position: 'fixed',
                top: '72px',
                right: '20px',
                zIndex: 9999,
                background: success ? '#26215C' : '#e53935',
                color: '#fff',
                padding: '10px 18px',
                borderRadius: '8px',
                fontSize: '13px',
                boxShadow: '0 8px 20px rgba(0,0,0,.15)'
            }).text(message).appendTo('body');

            setTimeout(() => {
                $t.fadeOut(300, () => $t.remove());
            }, 2500);
        }

        function refreshStatus() {
            $.ajax({ url: statusUrl, cache: false }).done(applyState);
        }

        function doPunch(action, reason) {
            setActionsDisabled(true);

            const payload = { action: action };
            if (reason) payload.reason = reason;

            $.ajax({
                url: punchUrl,
                method: 'POST',
                data: payload,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            }).done(function(res) {

                if (!res.success) {
                    toastMsg(res.message, false);
                    refreshStatus();
                    return;
                }

                applyState(res);
                toastMsg(res.message, true);

            }).fail(function(xhr) {
                toastMsg(xhr.responseJSON?.message || 'Something went wrong.', false);
                refreshStatus();
            });
        }

        // =========================
        // INIT LOAD
        // =========================
        refreshStatus();

        // =========================
        // PUNCH ACTION
        // =========================
        // "Start Break" never fires a punch directly - it opens the reason
        // panel first. Every other action fires immediately, same as before.
        $actions.on('click', 'button[data-action]', function (e) {
            e.stopPropagation();

            const action = $(this).data('action');

            if (action === 'start_break') {
                openReasonPanel();
                return;
            }

            doPunch(action);
        });

    });
</script>
@endauth
