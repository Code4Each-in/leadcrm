{{-- Header toolbar - one design system for every control on the right:
     world clock | attendance, notifications, profile.

     Every control is the same "tile": equal height, radius, border and
     hover, a 32px circle on the left and a two-line label (small caps
     caption over a bold value). Spacing comes from one gap, not from
     per-item margins. Scoped to #hdrToolbar so it reliably wins over the
     theme's long .navbar .navbar-menu-wrapper ... selectors and the
     older per-widget rules in header.blade.php / world-clock/styles. --}}
<style>
    :root {
        --hdr-tile-h: 44px;
        --hdr-radius: 12px;
        --hdr-circle: 32px;
        --hdr-gap: 10px;
        --hdr-border: #e6e8f1;
        --hdr-border-hover: #d6d9ea;
        --hdr-bg: #ffffff;
        --hdr-bg-hover: #f8f8fd;
        --hdr-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
        --hdr-shadow-hover: 0 6px 16px -8px rgba(38, 33, 92, 0.28);
        --hdr-ink: #1f2335;
        --hdr-muted: #7a8092;
        --hdr-accent: #4b49ac;
    }

    /* ---------- Bar ---------- */
    .navbar {
        box-shadow: 0 1px 0 #eceef5, 0 6px 18px -14px rgba(16, 24, 40, 0.25);
    }

    #hdrToolbar {
        gap: var(--hdr-gap);
        align-items: center;
    }

    #hdrToolbar > .nav-item {
        margin: 0 !important;
    }

    #hdrToolbar .hdr-divider {
        align-self: center;
        flex-shrink: 0;
        width: 1px;
        height: 26px;
        margin: 0 4px;
        background: var(--hdr-border);
        list-style: none;
    }

    /* Sidebar collapse button: a ghost tile aligned with the toolbar. */
    .navbar .navbar-menu-wrapper .navbar-toggler[data-toggle="minimize"] {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        margin-right: 8px;
        border-radius: 10px;
        color: #5b6070;
        transition: background 0.2s, color 0.2s;
    }

    .navbar .navbar-menu-wrapper .navbar-toggler[data-toggle="minimize"]:hover {
        background: #f1f2f9;
        color: var(--hdr-accent);
    }

    /* ---------- The shared tile ---------- */
    #hdrToolbar .wc-nav > .wc-inline,
    #hdrToolbar .wc-pill,
    #hdrToolbar .attendance-pill,
    #hdrToolbar .count-indicator,
    #hdrToolbar .nav-profile > .nav-link {
        display: flex;
        align-items: center;
        height: var(--hdr-tile-h);
        margin: 0;
        border-radius: var(--hdr-radius);
        background: var(--hdr-bg);
        border: 1px solid var(--hdr-border);
        box-shadow: var(--hdr-shadow);
        color: var(--hdr-ink);
        text-decoration: none;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }

    #hdrToolbar .wc-pill:hover,
    #hdrToolbar .wc-nav.show .wc-pill,
    #hdrToolbar .attendance-pill:hover,
    #hdrToolbar .att-nav.show .attendance-pill,
    #hdrToolbar .count-indicator:hover,
    #hdrToolbar .dropdown.show > .count-indicator,
    #hdrToolbar .nav-profile > .nav-link:hover,
    #hdrToolbar .nav-profile.show > .nav-link {
        background: var(--hdr-bg-hover);
        border-color: var(--hdr-border-hover);
        box-shadow: var(--hdr-shadow-hover);
    }

    /* Two-line label: caption over value - the same in every tile. */
    #hdrToolbar .wc-inline-city,
    #hdrToolbar .att-pill-label,
    #hdrToolbar .hdr-profile-role {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        line-height: 1.2;
    }

    #hdrToolbar .wc-inline-time,
    #hdrToolbar .attendance-timer,
    #hdrToolbar .hdr-profile-name {
        font-size: 14px;
        font-weight: 700;
        line-height: 1.25;
        color: var(--hdr-ink);
        font-variant-numeric: tabular-nums;
        letter-spacing: 0.1px;
    }

    /* ---------- World clock ---------- */
    #hdrToolbar .wc-nav > .wc-inline {
        gap: 12px;
        padding: 0 14px 0 6px;
    }

    #hdrToolbar .wc-nav > .wc-inline .wc-analog {
        width: var(--hdr-circle);
        height: var(--hdr-circle);
        box-shadow: none;
        border-color: #e3e4ef;
    }

    #hdrToolbar .wc-nav > .wc-inline .wc-tick::before {
        margin-top: 2px;
        height: 2px;
    }

    #hdrToolbar .wc-nav > .wc-inline .wc-tick.major::before {
        height: 3px;
    }

    /* Header shows time only - the date lives in the panel / banner. */
    #hdrToolbar .wc-nav > .wc-inline .wc-inline-date {
        display: none;
    }

    #hdrToolbar .wc-nav > .wc-inline .wc-inline-sep {
        height: 24px;
    }

    #hdrToolbar .wc-nav > .wc-inline .wc-badge {
        font-size: 9px;
        padding: 0 5px;
        letter-spacing: 0.2px;
    }

    #hdrToolbar .wc-pill {
        gap: 10px;
        padding: 0 14px;
    }

    #hdrToolbar .wc-pill-icon {
        width: var(--hdr-circle);
        height: var(--hdr-circle);
        background: #f1f1fb;
        box-shadow: none;
    }

    /* ---------- Attendance ---------- */
    #hdrToolbar .attendance-pill {
        gap: 10px;
        padding: 0 14px 0 6px;
        background: var(--att-tint);
        border-color: var(--att-line);
    }

    #hdrToolbar .attendance-pill:hover,
    #hdrToolbar .att-nav.show .attendance-pill {
        background: var(--att-tint);
        border-color: var(--att-tone);
        box-shadow: 0 6px 16px -8px var(--att-tone);
    }

    #hdrToolbar .att-pill-icon {
        width: var(--hdr-circle);
        height: var(--hdr-circle);
    }

    /* Caption on top, value below - like the clock and profile tiles. */
    #hdrToolbar .att-pill-text {
        flex-direction: column-reverse;
    }

    #hdrToolbar .att-pill-label {
        color: var(--att-tone);
    }

    /* ---------- Notifications ---------- */
    #hdrToolbar .count-indicator {
        width: var(--hdr-tile-h);
        justify-content: center;
        padding: 0;
        color: #4b5063;
        font-size: 17px;
    }

    #hdrToolbar .count-indicator:hover {
        color: var(--hdr-accent);
    }

    #hdrToolbar .count-indicator .count {
        top: -6px;
        right: -6px;
        box-shadow: 0 2px 6px rgba(229, 57, 53, 0.35);
    }

    /* ---------- Profile ---------- */
    #hdrToolbar .nav-profile > .nav-link {
        gap: 10px;
        padding: 0 10px 0 5px;
    }

    #hdrToolbar .nav-profile > .nav-link::after {
        display: none; /* Bootstrap caret - we draw our own chevron */
    }

    #hdrToolbar .nav-profile img {
        width: var(--hdr-circle);
        height: var(--hdr-circle);
        border: 0;
        box-shadow: 0 0 0 2px #fff, 0 0 0 3px var(--hdr-border);
    }

    #hdrToolbar .hdr-profile-text {
        display: flex;
        flex-direction: column;
        min-width: 0;
        max-width: 150px;
    }

    #hdrToolbar .hdr-profile-role {
        color: var(--hdr-muted);
    }

    #hdrToolbar .hdr-profile-role,
    #hdrToolbar .hdr-profile-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #hdrToolbar .hdr-profile-chevron {
        margin: 0 !important;
        font-size: 16px;
        color: var(--hdr-muted);
        transition: transform 0.2s;
    }

    #hdrToolbar .nav-profile.show .hdr-profile-chevron {
        transform: rotate(180deg);
    }

    /* ======================= Responsive ======================= */

    /* Small laptops: profile collapses to its avatar. */
    @media (max-width: 1199.98px) {
        #hdrToolbar .hdr-profile-text,
        #hdrToolbar .hdr-profile-chevron {
            display: none;
        }

        #hdrToolbar .nav-profile > .nav-link {
            width: var(--hdr-tile-h);
            justify-content: center;
            padding: 0;
        }

        #hdrToolbar .wc-nav > .wc-inline {
            gap: 10px;
            padding-right: 12px;
        }
    }

    /* Tablet: slightly smaller tiles; the clock becomes a button. */
    @media (max-width: 991.98px) {
        :root {
            --hdr-tile-h: 40px;
            --hdr-radius: 11px;
            --hdr-circle: 28px;
            --hdr-gap: 8px;
        }

        #hdrToolbar .hdr-divider,
        #hdrToolbar .wc-nav > .wc-inline {
            display: none;
        }

        #hdrToolbar .attendance-pill {
            padding: 0 12px 0 5px;
        }

        .navbar .navbar-menu-wrapper .navbar-toggler.navbar-toggler-right {
            width: var(--hdr-tile-h);
            height: var(--hdr-tile-h);
            margin-left: var(--hdr-gap);
            border-radius: var(--hdr-radius);
            border-color: var(--hdr-border);
            background: var(--hdr-bg);
            box-shadow: var(--hdr-shadow);
        }
    }

    /* Phones: icon tiles, all the same square. */
    @media (max-width: 575.98px) {
        :root {
            --hdr-tile-h: 36px;
            --hdr-radius: 10px;
            --hdr-circle: 26px;
            --hdr-gap: 6px;
        }

        #hdrToolbar .wc-pill {
            width: var(--hdr-tile-h);
            justify-content: center;
            padding: 0;
        }

        #hdrToolbar .wc-pill-icon {
            background: none;
        }

        #hdrToolbar .attendance-pill {
            gap: 6px;
            padding: 0 10px 0 4px;
        }

        #hdrToolbar .attendance-timer {
            font-size: 12.5px;
        }

        #hdrToolbar .count-indicator {
            font-size: 15px;
        }
    }

    /* Narrow phones: attendance shows its icon only. */
    @media (max-width: 400px) {
        #hdrToolbar .attendance-pill {
            width: var(--hdr-tile-h);
            justify-content: center;
            padding: 0;
        }
    }

    @media (max-width: 359px) {
        :root {
            --hdr-tile-h: 32px;
            --hdr-circle: 24px;
            --hdr-gap: 4px;
        }
    }
</style>
