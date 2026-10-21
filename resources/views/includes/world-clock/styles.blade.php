{{-- Header world clock (includes/world-clock/nav.blade.php). Selectors
     start with .navbar .wc-nav so they beat the theme's
     .navbar .navbar-menu-wrapper .navbar-nav ... rules, like the
     attendance pill next to it. --}}
<style>
    .wc-nav {
        --wc-uk: #4b49ac;
        --wc-uk-tint: #eeedfe;
        --wc-in: #d48a00;
        --wc-in-tint: #fff4db;
    }

    /* ---------- Desktop: inline clocks (>=992px) ---------- */
    .navbar .wc-nav .wc-inline {
        display: flex;
        align-items: center;
        gap: 14px;
        height: 50px;
        padding: 0 16px 0 7px;
        border-radius: 14px;
        background: #f7f8fc;
        border: 1px solid #e2e5f0;
    }

    .navbar .wc-nav .wc-inline-zone {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .navbar .wc-nav .wc-inline-sep {
        width: 1px;
        height: 30px;
        background: #e2e5f0;
    }

    /* Mini analog face - same look as the dashboard card's clocks. */
    .navbar .wc-nav .wc-analog {
        position: relative;
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #fcfcff;
        border: 1px solid #e6e6f0;
        box-shadow: 0 4px 12px rgba(38, 33, 92, 0.08);
    }

    .navbar .wc-nav .wc-tick {
        position: absolute;
        inset: 0;
        transform: rotate(calc(var(--i) * 30deg));
    }

    .navbar .wc-nav .wc-tick::before {
        content: '';
        display: block;
        width: 1px;
        height: 3px;
        margin: 2px auto 0;
        background: #cfd0de;
    }

    .navbar .wc-nav .wc-tick.major::before {
        width: 2px;
        height: 4px;
        background: var(--wc-tone);
    }

    .navbar .wc-nav .wc-hand {
        position: absolute;
        left: 50%;
        bottom: 50%;
        transform-origin: 50% 100%;
        border-radius: 2px;
    }

    .navbar .wc-nav .wc-hand-h { width: 2px;   height: 26%; margin-left: -1px;    background: var(--wc-tone); }
    .navbar .wc-nav .wc-hand-m { width: 1.5px; height: 36%; margin-left: -0.75px; background: var(--wc-tone-2); }
    .navbar .wc-nav .wc-hand-s { width: 1px;   height: 40%; margin-left: -0.5px;  background: #f96868; }

    .navbar .wc-nav .wc-pivot {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 4px;
        height: 4px;
        margin: -2px 0 0 -2px;
        border-radius: 50%;
        background: var(--wc-tone);
    }

    .wc-nav .tz-uk { --wc-tone: #4b49ac; --wc-tone-2: #6c63ff; }
    .wc-nav .tz-in { --wc-tone: #b77900; --wc-tone-2: #f2a654; }

    .navbar .wc-nav .wc-inline-info {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
        white-space: nowrap;
    }

    .navbar .wc-nav .wc-inline-city {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: var(--wc-tone);
    }

    .navbar .wc-nav .wc-inline-city .wc-badge {
        padding: 0 5px;
        border-radius: 8px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    .navbar .wc-nav .tz-uk .wc-badge { background: var(--wc-uk-tint); color: var(--wc-uk); }
    .navbar .wc-nav .tz-in .wc-badge { background: var(--wc-in-tint); color: #b77900; }

    .navbar .wc-nav .wc-inline-time {
        font-size: 14.5px;
        font-weight: 800;
        color: #26215c;
        font-variant-numeric: tabular-nums;
        letter-spacing: 0.2px;
    }

    .navbar .wc-nav .wc-inline-date {
        font-size: 10.5px;
        color: #8a8f9c;
    }

    /* Desktop shows the inline clocks only - no button, no panel. */
    @media (min-width: 992px) {
        .navbar .wc-nav .wc-pill,
        .navbar .wc-nav .wc-dropdown {
            display: none !important;
        }
    }

    /* Below desktop the header shows the button instead; the panel
       (.wc-panel-clocks) reuses the same clocks. */
    @media (max-width: 991.98px) {
        .navbar .wc-nav > .wc-inline {
            display: none;
        }
    }

    /* Small laptops: drop the date line to keep the header uncluttered. */
    @media (min-width: 992px) and (max-width: 1199.98px) {
        .navbar .wc-nav .wc-inline {
            gap: 10px;
            height: 46px;
            padding-right: 12px;
        }

        .navbar .wc-nav .wc-inline-date {
            display: none;
        }

        .navbar .wc-nav .wc-analog {
            width: 34px;
            height: 34px;
        }
    }

    /* ---------- Tablet / phone: pill (dropdown trigger) ---------- */
    .navbar .wc-nav .wc-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        height: 40px;
        padding: 0 14px 0 4px;
        border-radius: 999px;
        background: #f7f8fc;
        border: 1px solid #e2e5f0;
        color: #1a1f2b;
        font-size: 13px;
        text-decoration: none;
        white-space: nowrap;
        transition: box-shadow 0.2s, background 0.2s;
    }

    .navbar .wc-nav .wc-pill:hover,
    .navbar .wc-nav.show .wc-pill {
        background: #fff;
        box-shadow: 0 4px 14px -6px rgba(75, 73, 172, 0.45);
    }

    .navbar .wc-nav .wc-pill::after {
        display: none; /* no Bootstrap caret */
    }

    .navbar .wc-nav .wc-pill-icon {
        width: 32px;
        height: 32px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #fff;
        color: var(--wc-uk);
        box-shadow: 0 1px 3px rgba(26, 31, 43, 0.08);
    }

    /* Cancel the theme's margin-right on every navbar dropdown icon. */
    .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown.wc-nav .wc-pill-icon i {
        margin: 0;
        font-size: 17px;
        line-height: 1;
    }

    .navbar .wc-nav .wc-pill-zone {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }

    .navbar .wc-nav .wc-pill-label {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 10.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6b7280;
    }

    .navbar .wc-nav .wc-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .wc-nav .tz-uk .wc-dot { background: var(--wc-uk); }
    .wc-nav .tz-in .wc-dot { background: var(--wc-in); }

    .navbar .wc-nav .wc-pill-time {
        font-size: 13.5px;
        font-weight: 700;
        color: #1a1f2b;
        font-variant-numeric: tabular-nums;
        letter-spacing: 0.2px;
    }

    .navbar .wc-nav .wc-pill-sep {
        width: 1px;
        height: 22px;
        background: #e2e5f0;
    }

    /* ---------- Dropdown panel ---------- */
    .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .navbar-dropdown.wc-dropdown {
        width: 350px;
        padding: 0;
        border: 1px solid #e9ebf0;
        border-radius: 16px;
        box-shadow: 0 18px 40px -12px rgba(26, 31, 43, 0.25);
        overflow: hidden;
    }

    @media (min-width: 576px) {
        .navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .navbar-dropdown.wc-dropdown {
            left: auto !important;
            right: 0;
        }
    }

    .wc-dropdown i {
        margin-right: 0 !important; /* theme adds .5rem to every dropdown icon */
    }

    .wc-dropdown .wc-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        background: linear-gradient(135deg, #f3f2ff 0%, #f8f9fc 100%);
        border-bottom: 1px solid #eef0f4;
    }

    .wc-dropdown .wc-head-icon {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #fff;
        color: var(--wc-uk);
        font-size: 18px;
        box-shadow: 0 1px 3px rgba(26, 31, 43, 0.08);
    }

    .wc-dropdown .wc-head-title {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1a1f2b;
    }

    .wc-dropdown .wc-head-sub {
        margin: 0;
        font-size: 11.5px;
        color: #8a8f9c;
    }

    /* Panel: the desktop clocks, a little roomier. */
    .navbar .wc-nav .wc-dropdown .wc-panel-clocks {
        justify-content: space-between;
        height: auto;
        margin: 14px 16px;
        padding: 12px 14px;
    }

    .navbar .wc-nav .wc-panel-clocks .wc-analog {
        width: 46px;
        height: 46px;
    }

    .navbar .wc-nav .wc-panel-clocks .wc-inline-time {
        font-size: 16px;
    }

    .navbar .wc-nav .wc-panel-clocks .wc-inline-sep {
        height: 40px;
    }

    /* Very narrow phones: stack the two clocks. */
    @media (max-width: 359px) {
        .navbar .wc-nav .wc-dropdown .wc-panel-clocks {
            flex-direction: column;
            align-items: flex-start;
        }

        .navbar .wc-nav .wc-panel-clocks .wc-inline-sep {
            width: 100%;
            height: 1px;
        }
    }

    .wc-dropdown .wc-foot {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 11px 16px;
        background: #fafbfd;
        border-top: 1px solid #eef0f4;
        font-size: 12px;
        color: #6b7280;
    }

    .wc-dropdown .wc-foot i {
        color: var(--wc-in);
        font-size: 15px;
    }

    /* ---------- Responsive ---------- */

    /* Tablets: no icon, just the two times. */
    @media (max-width: 991.98px) {
        .navbar .wc-nav .wc-pill {
            gap: 8px;
            padding: 0 12px;
        }

        .navbar .wc-nav .wc-pill-icon {
            display: none;
        }
    }

    @media (min-width: 992px) {
        .navbar .navbar-menu-wrapper .navbar-nav .nav-item.wc-nav {
            margin-right: 0.25rem;
        }
    }

    /* Phones: just a clock tile, like the bell; the panel is pinned
       full-width so it can't run off the edge. */
    @media (max-width: 575.98px) {
        .navbar .wc-nav .wc-pill {
            width: 36px;
            height: 36px;
            padding: 0;
            justify-content: center;
            border-radius: 10px;
        }

        .navbar .wc-nav .wc-pill-icon {
            display: flex;
            width: auto;
            height: auto;
            background: none;
            box-shadow: none;
        }

        .navbar .wc-nav .wc-pill-zone,
        .navbar .wc-nav .wc-pill-sep {
            display: none;
        }

        .navbar .dropdown-menu.wc-dropdown {
            position: fixed !important;
            top: 66px !important;
            left: 10px !important;
            right: 10px !important;
            transform: none !important;
            width: auto !important;
            max-width: 420px;
            margin-left: auto !important;
            border-radius: 14px !important;
        }
    }

    @media (max-width: 359px) {
        .navbar .wc-nav .wc-pill {
            width: 32px;
            height: 32px;
        }
    }
</style>
