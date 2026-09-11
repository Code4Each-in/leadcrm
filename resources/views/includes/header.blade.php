<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

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
    }

    /* ---------- Profile dropdown ---------- */
    .navbar-dropdown.shadow-sm {
        border-radius: 12px;
        border: 1px solid #e8e8e8;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.10) !important;
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

    @media (max-width: 1023px) and (min-width: 768px) {

        .select2-container {
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

        .navbar-brand.brand-logo {
            display: none;
        }

        .navbar-brand.brand-logo-mini {
            display: flex;
            align-items: center;
        }

        .navbar-brand.brand-logo-mini img {
            height: 30px;
        }

        /* ── Hide agency select entirely below 1024px ── */
        .navbar-nav.navbar-center-nav {
            display: none !important;
        }

        /* Hide name/role in profile */
        .user-name,
        .user-role {
            display: none;
        }

        /* Slightly smaller bell */
        .count-indicator {
            width: 38px;
            height: 38px;
        }

        /* Dropdown panel narrower */
        .navbar-dropdown.preview-list {
            width: 290px;
        }

    }

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

        .navbar-brand.brand-logo {
            display: none;
        }

        .navbar-brand.brand-logo-mini {
            display: flex;
            align-items: center;
        }

        .navbar-brand.brand-logo-mini img {
            height: 28px;
        }

        .navbar-menu-wrapper {
            height: 56px;
            padding: 0 12px 0 4px;
            flex-wrap: nowrap;
        }

        /* Hide sidebar mini-toggle (sidebar uses off-canvas on mobile) */
        .navbar-toggler.align-self-center {
            display: none;
        }

        /* ── Hide agency select entirely below 1024px ── */
        .navbar-nav.navbar-center-nav {
            display: none !important;
        }

        /* Right nav stays row 1 */
        .navbar-nav-right {
            margin-left: auto;
            gap: 6px;
        }

        .user-name,
        .user-role {
            display: none;
        }

        .count-indicator {
            width: 36px;
            height: 36px;
        }

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

        .navbar-brand.brand-logo {
            display: none;
        }

        .navbar-brand.brand-logo-mini {
            display: flex;
            align-items: center;
        }

        .navbar-brand.brand-logo-mini img {
            height: 26px;
        }

        .navbar-menu-wrapper {
            height: 52px;
            padding: 0 10px 0 4px;
            flex-wrap: wrap;
        }

        .navbar-toggler.align-self-center {
            display: none;
        }

        /* ── Hide agency select entirely below 1024px ── */
        .navbar-nav.navbar-center-nav {
            display: none !important;
        }

        .navbar-nav-right {
            gap: 2px;
            flex-shrink: 1;
        }

        /* Hide profile text */
        .user-name,
        .user-role {
            display: none;
        }

        /* Smaller avatar */
        .nav-profile img {
            width: 32px;
            height: 32px;
        }

        /* Smaller bell */
        .count-indicator {
            width: 34px;
            height: 34px;
        }

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

    @media (max-width: 1440px) {
        .select2-container {
            width: 500px !important;
            max-width: 100%;
        }
    }

    @media (max-width: 1024px) {
        .select2-container {
            width: 300px !important;
            max-width: 100%;
        }
    }

    @media (max-width: 425px) and (min-width: 320px) {
        .select2-container--default .select2-selection--multiple {
            display: none !important;
        }
    }

    /* =========================================================
   RESPONSIVE BREAKPOINTS — NAVBAR
   ========================================================= */

    /* ---------- ≥1441px : Large / wide desktop ---------- */
    @media (min-width: 1441px) {
        .select2-container {
            width: 550px !important;
            max-width: 100%;
        }
    }

    /* ---------- 1200px – 1440px : Standard desktop ---------- */
    @media (max-width: 1440px) {
        .select2-container {
            width: 500px !important;
            max-width: 100%;
        }
    }

    /* ---------- 1025px – 1199px : Small desktop / laptop ---------- */
    @media (max-width: 1199px) and (min-width: 1025px) {
        .select2-container {
            width: 380px !important;
            max-width: 100%;
        }
    }

    /* ---------- 1024px : general shrink rule ---------- */
    @media (max-width: 1024px) {
        .select2-container {
            width: 300px !important;
            max-width: 100%;
        }
    }

    /* ---------- 768px – 1023px : Tablets ---------- */
    @media (max-width: 1023px) and (min-width: 768px) {

        .select2-container {
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

        .navbar-brand.brand-logo {
            display: none;
        }

        .navbar-brand.brand-logo-mini {
            display: flex;
            align-items: center;
        }

        .navbar-brand.brand-logo-mini img {
            height: 30px;
        }

        /* Hide agency select entirely below 1024px */
        .navbar-nav.navbar-center-nav {
            display: none !important;
        }

        /* Hide name/role in profile */
        .user-name,
        .user-role {
            display: none;
        }

        /* Slightly smaller bell */
        .count-indicator {
            width: 38px;
            height: 38px;
        }

        /* Dropdown panel narrower */
        .navbar-dropdown.preview-list {
            width: 290px;
        }
    }

    /* ---------- 481px – 768px : Large phones / small tablets (portrait) ---------- */
    @media (max-width: 768px) and (min-width: 481px) {

        .navbar {
            flex-wrap: wrap;
            min-height: auto;
        }

        /* Row 1: brand + right icons */
        .navbar-brand-wrapper {
            width: auto;
            min-width: unset;
            height: 56px;
            flex: 0 0 auto;
            padding: 0 12px;
            border-right: none;
        }

        .navbar-brand.brand-logo {
            display: none;
        }

        .navbar-brand.brand-logo-mini {
            display: flex;
            align-items: center;
        }

        .navbar-brand.brand-logo-mini img {
            height: 28px;
        }

        .navbar-menu-wrapper {
            height: 56px;
            padding: 0 12px 0 4px;
            flex-wrap: nowrap;
        }

        /* Hide sidebar mini-toggle (sidebar uses off-canvas on mobile) */
        .navbar-toggler.align-self-center {
            display: none;
        }

        /* Hide agency select entirely below 1024px */
        .navbar-nav.navbar-center-nav {
            display: none !important;
        }

        /* Right nav stays row 1 */
        .navbar-nav-right {
            margin-left: auto;
            gap: 6px;
        }

        .user-name,
        .user-role {
            display: none;
        }

        .count-indicator {
            width: 36px;
            height: 36px;
        }

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

    /* ---------- 376px – 480px : Standard mobile ---------- */
    @media (max-width: 480px) and (min-width: 376px) {

        .navbar {
            flex-wrap: wrap;
            min-height: auto;
            padding: 0;
        }

        .navbar-brand-wrapper {
            height: 52px;
            width: auto;
            min-width: unset;
            padding: 0 10px;
            flex: 0 0 auto;
            border-right: none;
        }

        .navbar-brand.brand-logo {
            display: none;
        }

        .navbar-brand.brand-logo-mini {
            display: flex;
            align-items: center;
        }

        .navbar-brand.brand-logo-mini img {
            height: 26px;
        }

        .navbar-menu-wrapper {
            height: 52px;
            padding: 0 10px 0 4px;
            flex-wrap: wrap;
        }

        .navbar-toggler.align-self-center {
            display: none;
        }

        .navbar-nav.navbar-center-nav {
            display: none !important;
        }

        .navbar-nav-right {
            gap: 2px;
            flex-shrink: 1;
        }

        .user-name,
        .user-role {
            display: none;
        }

        .nav-profile img {
            width: 32px;
            height: 32px;
        }

        .count-indicator {
            width: 34px;
            height: 34px;
        }

        .navbar-dropdown.preview-list {
            width: calc(100vw - 20px);
            right: -10px !important;
            left: auto !important;
            border-radius: 10px;
        }

        .navbar-toggler.navbar-toggler-right {
            display: block !important;
            position: relative;
            z-index: 1050;
            margin-left: 6px;
        }

        .navbar-dropdown.shadow-sm {
            min-width: 160px;
        }

        .select2-container--default .select2-selection--multiple {
            display: none !important;
        }
    }

    /* ---------- 320px – 375px : Small mobile ---------- */
    @media (max-width: 375px) and (min-width: 320px) {

        .navbar {
            flex-wrap: wrap;
            min-height: auto;
            padding: 0;
        }

        .navbar-brand-wrapper {
            height: 48px;
            width: auto;
            min-width: unset;
            padding: 0 8px;
            flex: 0 0 auto;
            border-right: none;
        }

        .navbar-brand.brand-logo {
            display: none;
        }

        .navbar-brand.brand-logo-mini {
            display: flex;
            align-items: center;
        }

        .navbar-brand.brand-logo-mini img {
            height: 22px;
        }

        .navbar-menu-wrapper {
            height: 48px;
            padding: 0 8px 0 4px;
            flex-wrap: wrap;
        }

        .navbar-toggler.align-self-center {
            display: none;
        }

        .navbar-nav.navbar-center-nav {
            display: none !important;
        }

        .navbar-nav-right {
            gap: 0;
            flex-shrink: 1;
        }

        .user-name,
        .user-role {
            display: none;
        }

        .nav-profile img {
            width: 28px;
            height: 28px;
        }

        .count-indicator {
            width: 30px;
            height: 30px;
        }

        .navbar-dropdown.preview-list {
            width: calc(100vw - 12px);
            right: -6px !important;
            left: auto !important;
            border-radius: 8px;
        }

        .navbar-toggler.navbar-toggler-right {
            display: block !important;
            position: relative;
            z-index: 1050;
            margin-left: 4px;
            padding: 4px;
            font-size: 16px;
        }

        .navbar-dropdown.shadow-sm {
            min-width: 150px;
        }

        .select2-container--default .select2-selection--multiple {
            display: none !important;
        }
    }

    /* ---------- <320px : Extra small / older devices ---------- */
    @media (max-width: 319px) {

        .navbar {
            flex-wrap: wrap;
            min-height: auto;
            padding: 0;
        }

        .navbar-brand-wrapper {
            height: 44px;
            width: auto;
            min-width: unset;
            padding: 0 6px;
            flex: 0 0 auto;
            border-right: none;
        }

        .navbar-brand.brand-logo {
            display: none;
        }

        .navbar-brand.brand-logo-mini {
            display: flex;
            align-items: center;
        }

        .navbar-brand.brand-logo-mini img {
            height: 20px;
        }

        .navbar-menu-wrapper {
            height: 44px;
            padding: 0 6px 0 2px;
            flex-wrap: wrap;
        }

        .navbar-toggler.align-self-center {
            display: none;
        }

        .navbar-nav.navbar-center-nav {
            display: none !important;
        }

        .user-name,
        .user-role {
            display: none;
        }

        .nav-profile img {
            width: 26px;
            height: 26px;
        }

        .count-indicator {
            width: 28px;
            height: 28px;
        }

        .navbar-dropdown.preview-list {
            width: calc(100vw - 8px);
            right: -4px !important;
            left: auto !important;
            border-radius: 6px;
        }

        .navbar-toggler.navbar-toggler-right {
            display: block !important;
            margin-left: 2px;
            padding: 3px;
            font-size: 15px;
        }

        .select2-container--default .select2-selection--multiple {
            display: none !important;
        }
    }

    /* ---------- Attendance widget ---------- */
    .attendance-pill {
        display: flex;
        align-items: center;
        gap: 7px;
        height: 40px;
        padding: 0 12px;
        border-radius: 10px;
        background: #f7f8fc;
        border: 1px solid #e2e5f0;
        color: #333;
        font-size: 13px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        text-decoration: none;
        transition: background 0.2s;
        white-space: nowrap;
    }

    .attendance-pill:hover {
        background: #eef0fb;
        color: #333;
    }

    .attendance-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #9CA3AF;
        flex-shrink: 0;
    }

    .attendance-dot.working {
        background: #22C55E;
        animation: attPulse 1.6s infinite;
    }

    .attendance-dot.on_break {
        background: #F59E0B;
    }

    .attendance-dot.completed {
        background: #3B82F6;
    }

    @keyframes attPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, .6);
        }

        70% {
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        }
    }

    .attendance-dropdown {
        width: 240px;
        border-radius: 14px;
        padding: 0;
        overflow: hidden;
    }

    .attendance-dropdown .att-head {
        padding: 13px 16px;
        border-bottom: 1px solid #f0f0f0;
        background: #fafafa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .attendance-dropdown .att-head .att-status-text {
        font-size: 13px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .attendance-dropdown .att-head a {
        font-size: 11.5px;
        color: #534AB7;
        text-decoration: none;
    }

    .attendance-dropdown .att-body {
        padding: 16px;
    }

    .att-punch-btn {
        width: 100%;
        border: none;
        border-radius: 10px;
        padding: 10px 0;
        font-weight: 700;
        font-size: 14px;
        color: #fff;
        background: linear-gradient(135deg, #534AB7, #26215C);
        transition: transform 0.12s ease;
    }

    .att-punch-btn:hover {
        transform: translateY(-1px);
    }

    .att-punch-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .att-punch-btn.on_break {
        background: linear-gradient(135deg, #F59E0B, #B45309);
    }

    .att-punch-btn.completed {
        background: #9CA3AF;
    }

    .att-meta {
        display: flex;
        justify-content: space-between;
        margin-top: 13px;
        font-size: 11.5px;
        color: #888;
    }

    .att-meta .val {
        color: #333;
        font-weight: 600;
    }

    /* ---------- Multiple actions (Start Break / Time Out) ---------- */
    .att-actions {
        display: flex;
        gap: 8px;
    }

    .att-actions .att-punch-btn {
        flex: 1;
        padding: 10px 4px;
        font-size: 12.5px;
        white-space: nowrap;
    }

    .att-current-reason {
        font-size: 11px;
        color: #B45309;
        margin-top: 6px;
    }

    /* ---------- Break reason panel ---------- */
    .att-reason-panel {
        padding: 16px;
    }

    .att-reason-title {
        font-size: 12.5px;
        font-weight: 700;
        color: #26215C;
        margin-bottom: 10px;
    }

    .att-reason-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 10px;
    }

    .att-reason-item {
        text-align: left;
        padding: 8px 10px;
        border: 1.5px solid #e2e5f0;
        border-radius: 8px;
        background: #f7f8fc;
        font-size: 13px;
        color: #333;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
    }

    .att-reason-item:hover {
        background: #eef0fb;
        border-color: #AFA9EC;
    }

    .att-reason-item.selected {
        background: #ebe9ff;
        border-color: #534AB7;
        color: #26215C;
        font-weight: 600;
    }

    .att-reason-other-input {
        display: none;
        width: 100%;
        box-sizing: border-box;
        border: 1.5px solid #e2e5f0;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 13px;
        margin-bottom: 10px;
        outline: none;
    }

    .att-reason-other-input:focus {
        border-color: #534AB7;
    }

    .att-reason-actions {
        display: flex;
        gap: 8px;
    }

    .att-reason-confirm,
    .att-reason-cancel {
        flex: 1;
        border: none;
        border-radius: 8px;
        padding: 9px 0;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .att-reason-confirm {
        background: linear-gradient(135deg, #534AB7, #26215C);
        color: #fff;
    }

    .att-reason-confirm:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .att-reason-cancel {
        background: #f0f0f0;
        color: #555;
    }

    @media (max-width: 480px) {
        .attendance-pill {
            padding: 0 8px;
            gap: 0;
        }

        .attendance-pill .attendance-timer {
            display: none;
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
        <a class="navbar-brand brand-logo" href="#">
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
        <a class="navbar-brand brand-logo-mini" href="#">

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

            {{-- Bell --}}
            <!-- <li class="nav-item dropdown">
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
            </li> -->

            {{-- Attendance --}}
            @auth
            <li class="nav-item dropdown">
                <a class="nav-link attendance-pill dropdown-toggle" href="#" data-toggle="dropdown" id="attendanceDropdown">
                    <span class="attendance-dot" id="navAttDot"></span>
                    <span class="attendance-timer" id="navAttTimer">00:00:00</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown attendance-dropdown" aria-labelledby="attendanceDropdown">
                    <div class="att-head">
                        <span class="att-status-text" id="navAttStatusText">Loading…</span>
                        <a href="{{ route('attendance.index') }}">View report</a>
                    </div>
                    <div class="att-body" id="attBody">
                        <div class="att-actions" id="attActions"></div>
                        <div class="att-current-reason" id="attCurrentReason" style="display:none;"></div>
                        <div class="att-meta">
                            <span>In: <span class="val" id="navAttTimeIn">--:--</span></span>
                            <span>Out: <span class="val" id="navAttTimeOut">--:--</span></span>
                        </div>
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
                </div>
            </li>
            @endauth

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
        // (computed server-side in Asia/Kolkata by AttendanceController).
        // syncedAt: client Date.now() at the moment workedBase was fetched.
        // We only ever ADD real elapsed milliseconds on top of workedBase
        // while actively working - we never recompute from wall-clock time,
        // so refreshing the page (or being on break) can't inflate the timer.
        let workedBase = 0;
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
        }

        // =========================
        // ACTION BUTTONS
        // =========================
        function renderActions(actions) {
            $actions.empty();

            if (!actions || !actions.length) {
                $('<button>', {
                    type: 'button',
                    class: 'att-punch-btn completed',
                    text: 'Completed',
                    disabled: true
                }).appendTo($actions);
                return;
            }

            actions.forEach(function (a) {
                $('<button>', {
                    type: 'button',
                    class: 'att-punch-btn ' + (status || ''),
                    'data-action': a.action,
                    text: a.label
                }).appendTo($actions);
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
            return date.toLocaleTimeString('en-IN', {
                timeZone: 'Asia/Kolkata',
                hour12: false
            });
        }

        function applyState(data) {

            status = data.status;

            // Authoritative snapshot from the server (IST-computed).
            // Every applyState() call - whether from the initial load, a
            // refresh, or a punch - resyncs to this value instead of
            // trusting anything the client previously calculated.
            workedBase = Number(data.worked_seconds) || 0;
            syncedAt = Date.now();

            if (timerIntervalId) {
                clearInterval(timerIntervalId);
                timerIntervalId = null;
            }

            if (!data.time_in) {
                $timer.text('00:00:00');
            } else {
                renderTimer();
                if (status === 'working') {
                    timerIntervalId = setInterval(renderTimer, 1000);
                }
                // on_break / completed -> no interval, stays frozen at workedBase.
            }

            $dot.attr('class', 'attendance-dot ' + data.status);
            $statusText.text(
                data.status
                .replace('_', ' ')
                .replace(/\b\w/g, c => c.toUpperCase())
            );

            if (data.status === 'on_break' && data.current_break_reason) {
                $currentReason.text('Reason: ' + data.current_break_reason).show();
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