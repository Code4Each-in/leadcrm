{{-- Dashboard styles, scoped to #dashboard. Colours are the theme's
     (#4B49AC primary); status pills match the lead listing. --}}
<style>
    #dashboard {
        --dash-text: #1a1f2b;
        --dash-muted: #7a8294;
        --dash-border: #e9ebf0;
        --dash-soft: #f6f7fb;
        --tone-primary: #4b49ac;
        --tone-success: #1f9d67;
        --tone-warning: #e8a317;
        --tone-danger: #e5534b;
        --tone-info: #3d8bfd;
        --tone-purple: #7978e9;
        --tone-neutral: #6b7385;
    }

    /* ---------- Welcome banner ---------- */
    #dashboard .welcome-banner {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px 24px;
        margin-bottom: 28px;
        padding: 26px 30px;
        border-radius: 20px;
        color: #fff;
        background: linear-gradient(120deg, #3f3d9e 0%, #4b49ac 45%, #7978e9 100%);
        box-shadow: 0 18px 36px -18px rgba(75, 73, 172, .55);
    }

    /* Soft decorative circles - purely visual. */
    #dashboard .welcome-shape {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        background: rgba(255, 255, 255, .08);
    }

    #dashboard .welcome-shape.shape-one {
        width: 260px;
        height: 260px;
        top: -120px;
        right: -60px;
    }

    #dashboard .welcome-shape.shape-two {
        width: 160px;
        height: 160px;
        bottom: -90px;
        right: 220px;
        background: rgba(255, 255, 255, .06);
    }

    #dashboard .welcome-main,
    #dashboard .welcome-side {
        position: relative;
        z-index: 1;
    }

    #dashboard .welcome-main {
        min-width: 0;
        flex: 1 1 420px;
    }

    #dashboard .welcome-agency {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        max-width: 100%;
        padding: 4px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .16);
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .6px;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #dashboard .welcome-title {
        margin: 12px 0 6px;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
        color: #fff;
        overflow-wrap: anywhere;
    }

    #dashboard .welcome-text {
        margin: 0;
        font-size: 14px;
        color: rgba(255, 255, 255, .82);
    }

    #dashboard .welcome-text i {
        margin-right: 5px;
    }

    #dashboard .welcome-sep {
        margin: 0 6px;
        opacity: .6;
    }

    #dashboard .welcome-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 16px;
    }

    #dashboard .welcome-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 10px;
        background: #fff;
        color: #3f3d9e;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    #dashboard .welcome-chip i {
        font-size: 15px;
        color: #f5a700;
    }

    #dashboard .welcome-chip:hover {
        color: #3f3d9e;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px -6px rgba(0, 0, 0, .3);
    }

    #dashboard .welcome-chip.is-empty {
        background: rgba(255, 255, 255, .14);
        color: rgba(255, 255, 255, .85);
    }

    #dashboard .welcome-chip.is-empty i {
        color: inherit;
    }

    #dashboard .welcome-side {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
        max-width: 100%;
    }

    #dashboard .welcome-role {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        max-width: 260px;
        padding: 7px 14px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .35);
        font-size: 13px;
        font-weight: 600;
    }

    #dashboard .welcome-role span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #dashboard .welcome-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: 12px;
        background: #fff;
        color: #4b49ac;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 8px 18px -8px rgba(0, 0, 0, .35);
        transition: transform .15s ease;
    }

    #dashboard .welcome-btn:hover {
        color: #3f3d9e;
        transform: translateY(-1px);
    }

    /* ---------- KPI cards ---------- */
    #dashboard .kpi-row > [class*="col-"] {
        margin-bottom: 24px;
    }

    #dashboard .kpi-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        height: 100%;
        min-height: 150px;
        background: #fff;
        border: 1px solid var(--dash-border);
        border-radius: 16px;
        padding: 22px 24px;
        color: inherit;
        text-decoration: none;
        transition: box-shadow .15s ease, transform .15s ease, border-color .15s ease;
    }

    #dashboard a.kpi-card:hover {
        border-color: #dcdff0;
        box-shadow: 0 10px 24px rgba(26, 31, 43, .08);
        transform: translateY(-2px);
    }

    #dashboard .kpi-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    #dashboard .kpi-label {
        font-size: 14px;
        font-weight: 600;
        color: #4a5163;
        line-height: 1.35;
    }

    #dashboard .kpi-icon,
    #dashboard .row-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
        background: var(--tone);
    }

    #dashboard .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        font-size: 24px;
        box-shadow: 0 6px 14px -6px var(--tone);
    }

    #dashboard .kpi-value {
        font-size: 34px;
        font-weight: 700;
        line-height: 1;
        color: var(--dash-text);
    }

    #dashboard .kpi-hint {
        font-size: 12.5px;
        color: var(--dash-muted);
        margin-top: 8px;
    }

    #dashboard .tone-primary { --tone: var(--tone-primary); }
    #dashboard .tone-success { --tone: var(--tone-success); }
    #dashboard .tone-warning { --tone: var(--tone-warning); }
    #dashboard .tone-danger  { --tone: var(--tone-danger); }
    #dashboard .tone-info    { --tone: var(--tone-info); }
    #dashboard .tone-purple  { --tone: var(--tone-purple); }
    #dashboard .tone-neutral { --tone: var(--tone-neutral); }

    /* ---------- Panels ---------- */
    #dashboard .panel-row > [class*="col-"] {
        margin-bottom: 24px;
    }

    #dashboard .dash-panel {
        height: 100%;
        background: #fff;
        border: 1px solid var(--dash-border);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
    }

    #dashboard .panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 1px solid #f0f1f5;
    }

    #dashboard .panel-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--dash-text);
        margin: 0;
    }

    #dashboard .panel-subtitle {
        font-size: 12.5px;
        color: var(--dash-muted);
        margin: 3px 0 0;
    }

    #dashboard .panel-link {
        font-size: 13px;
        font-weight: 600;
        color: var(--tone-primary);
        white-space: nowrap;
    }

    #dashboard .panel-body {
        padding: 8px 10px 10px;
        flex: 1;
    }

    #dashboard .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 140px;
        padding: 20px;
        text-align: center;
        font-size: 13.5px;
        color: var(--dash-muted);
    }

    #dashboard .empty-state i {
        font-size: 30px;
        color: #c3c8d4;
    }

    /* ---------- List rows (leads, attention, reminders) ---------- */
    #dashboard .dash-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px;
        border-radius: 12px;
        color: inherit;
        text-decoration: none;
    }

    #dashboard .dash-row + .dash-row {
        border-top: 1px solid #f3f4f7;
        border-radius: 0 0 12px 12px;
    }

    #dashboard a.dash-row:hover {
        background: var(--dash-soft);
        border-radius: 12px;
    }

    #dashboard .row-main {
        flex: 1;
        min-width: 0;
    }

    #dashboard .row-title {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--dash-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #dashboard .row-meta {
        display: block;
        font-size: 12.5px;
        color: var(--dash-muted);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #dashboard .row-meta .lead-id {
        font-weight: 600;
        color: var(--tone-primary);
    }

    #dashboard .row-side {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 5px;
        flex-shrink: 0;
    }

    #dashboard .row-time {
        font-size: 12px;
        color: var(--dash-muted);
        white-space: nowrap;
    }

    #dashboard .row-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        font-size: 20px;
    }

    #dashboard .row-count {
        min-width: 38px;
        padding: 5px 10px;
        border-radius: 9px;
        text-align: center;
        font-size: 15px;
        font-weight: 700;
        color: var(--dash-text);
        background: var(--dash-soft);
    }

    #dashboard .date-chip {
        width: 52px;
        flex-shrink: 0;
        text-align: center;
        border-radius: 11px;
        padding: 6px 0;
        background: var(--dash-soft);
        line-height: 1.15;
    }

    #dashboard .date-chip .day {
        display: block;
        font-size: 17px;
        font-weight: 700;
        color: var(--dash-text);
    }

    #dashboard .date-chip .month {
        display: block;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--dash-muted);
    }

    #dashboard .date-chip.is-today {
        background: var(--tone-primary);
    }

    #dashboard .date-chip.is-today .day,
    #dashboard .date-chip.is-today .month {
        color: #fff;
    }

    /* ---------- Status pills (lead listing colours) ---------- */
    #dashboard .status-pill {
        display: inline-block;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        padding: 4px 11px;
        white-space: nowrap;
        background: #eceff3;
        color: #4b5563;
    }

    #dashboard .status-pill.pill-draft { background: #fff3cd; color: #8a6d00; }
    #dashboard .status-pill.pill-open { background: #d4f4e2; color: #1a7a4c; }
    #dashboard .status-pill.pill-am { background: #efe8fb; color: #6438c2; }
    #dashboard .status-pill.pill-legacy { background: #e0f5f3; color: #0b7a6f; }
    #dashboard .status-pill.pill-sent_back { background: #fdeede; color: #b45f06; }
    #dashboard .status-pill.pill-hold { background: #e6f4fb; color: #0a6c93; }
    #dashboard .status-pill.pill-lost { background: #fdeaea; color: #c62828; }
    #dashboard .status-pill.pill-closed { background: #eceff3; color: #4b5563; }
    #dashboard .status-pill.pill-lead { background: #e6f4ea; color: #2e7d32; }
    #dashboard .status-pill.pill-pricing { background: #fff6dc; color: #9a6b00; }
    #dashboard .status-pill.pill-issues { background: #fdeaea; color: #c62828; }
    #dashboard .status-pill.pill-tender { background: #efe8fb; color: #6438c2; }
    #dashboard .status-pill.pill-loa { background: #e7f1ff; color: #2264d1; }
    #dashboard .status-pill.pill-contracts { background: #e0f5f3; color: #0b7a6f; }
    #dashboard .status-pill.pill-live { background: #e2f5e9; color: #1a7a4c; }

    #dashboard .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #26215c;
        margin-bottom: 16px;
    }

    #dashboard .section-subtitle {
        font-size: 12px;
        color: #8a8a9a;
        margin-top: -10px;
        margin-bottom: 18px;
    }

    #dashboard .updated-note {
        font-size: 12px;
        color: #a3aab8;
    }

    /* ---------- New Notifications (shared notif-panel, dashboard look) ---------- */
    #dashboard .notif-panel {
        border-color: var(--dash-border);
        border-radius: 16px;
    }

    #dashboard .notif-panel .notif-head {
        padding: 16px 22px;
    }

    #dashboard .notif-scroll {
        max-height: 330px;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #d5d9e2 transparent;
    }

    #dashboard .notif-scroll::-webkit-scrollbar {
        width: 6px;
    }

    #dashboard .notif-scroll::-webkit-scrollbar-thumb {
        background: #d5d9e2;
        border-radius: 999px;
    }

    #dashboard .notif-scroll .notif-item:last-child {
        border-bottom: 0;
    }

    /* ---------- Today's reminders alert (bottom-right) ---------- */
    #dashboard .reminder-alert {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 1040;
        width: 340px;
        max-width: calc(100vw - 32px);
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-left: 4px solid #f5b000;
        border-radius: 14px;
        box-shadow: 0 14px 32px -10px rgba(120, 80, 0, .30);
        overflow: hidden;
        animation: reminderIn .3s ease-out both;
        transition: opacity .2s ease, transform .2s ease;
    }

    #dashboard .reminder-alert.is-leaving {
        opacity: 0;
        transform: translateY(12px);
    }

    @keyframes reminderIn {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: none; }
    }

    #dashboard .reminder-alert-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 10px 11px 12px;
    }

    #dashboard .reminder-alert-icon {
        width: 28px;
        height: 28px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f5b000;
        color: #fff;
        font-size: 15px;
    }

    #dashboard .reminder-alert-title {
        flex: 1;
        font-size: 13.5px;
        font-weight: 700;
        color: #7a4f05;
    }

    #dashboard .reminder-alert-close {
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #b7862a;
        font-size: 16px;
        cursor: pointer;
    }

    #dashboard .reminder-alert-close:hover {
        background: #fef3c7;
        color: #6b4505;
    }

    #dashboard .reminder-alert-list {
        border-top: 1px solid #fcecb3;
    }

    #dashboard .reminder-alert-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 9px 12px;
        color: inherit;
        text-decoration: none;
    }

    #dashboard .reminder-alert-row + .reminder-alert-row {
        border-top: 1px solid #fcecb3;
    }

    #dashboard .reminder-alert-row:hover {
        background: #fef6d6;
    }

    #dashboard .reminder-alert-row.is-queued {
        display: none;
    }

    #dashboard .reminder-alert-time {
        flex-shrink: 0;
        min-width: 66px;
        margin-top: 1px;
        text-align: center;
        font-size: 11.5px;
        font-weight: 700;
        color: #92600a;
        background: #fef3c7;
        border-radius: 999px;
        padding: 2px 8px;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    #dashboard .reminder-alert-time.is-due {
        background: #f5b000;
        color: #fff;
    }

    #dashboard .reminder-alert-text {
        flex: 1;
        min-width: 0;
    }

    #dashboard .reminder-alert-note,
    #dashboard .reminder-alert-lead {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #dashboard .reminder-alert-note {
        font-size: 13px;
        font-weight: 600;
        color: var(--dash-text);
    }

    #dashboard .reminder-alert-lead {
        font-size: 12px;
        color: #92600a;
        margin-top: 1px;
    }

    #dashboard .reminder-alert-more {
        display: block;
        padding: 8px 12px;
        border-top: 1px solid #fcecb3;
        font-size: 12.5px;
        font-weight: 600;
        color: #92600a;
        text-align: center;
    }

    #dashboard .reminder-alert-more:hover {
        background: #fef6d6;
        color: #6b4505;
    }

    @media (prefers-reduced-motion: reduce) {
        #dashboard .reminder-alert {
            animation: none;
        }
    }

    @media (max-width: 767px) {
        /* Welcome banner - compact, with the two chips as side-by-side
           summary tiles. The role is in the profile menu, so it is
           left out here. */
        #dashboard .welcome-banner {
            gap: 14px;
            padding: 18px;
            margin-bottom: 16px;
            border-radius: 16px;
        }

        #dashboard .welcome-main {
            flex-basis: 100%;
        }

        #dashboard .welcome-agency {
            font-size: 11px;
            padding: 3px 10px;
        }

        #dashboard .welcome-title {
            margin: 10px 0 4px;
            font-size: 21px;
        }

        #dashboard .welcome-text {
            font-size: 12.5px;
        }

        #dashboard .welcome-sep,
        #dashboard .welcome-tagline,
        #dashboard .welcome-role,
        #dashboard .welcome-shape.shape-two {
            display: none;
        }

        #dashboard .welcome-shape.shape-one {
            width: 180px;
            height: 180px;
            top: -90px;
            right: -70px;
        }

        #dashboard .welcome-chips {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 14px;
        }

        #dashboard .welcome-chip {
            display: grid;
            grid-template-columns: auto 1fr;
            grid-template-rows: auto auto;
            column-gap: 8px;
            row-gap: 0;
            align-items: center;
            padding: 10px 12px;
            border-radius: 12px;
        }

        #dashboard .welcome-chip i {
            grid-row: 1 / span 2;
            font-size: 20px;
        }

        #dashboard .welcome-chip .chip-count {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.1;
        }

        #dashboard .welcome-chip .chip-label {
            font-size: 11px;
            font-weight: 500;
            line-height: 1.25;
            opacity: .85;
        }

        #dashboard .welcome-side {
            flex-basis: 100%;
            align-items: stretch;
        }

        /* Only the Add Lead button is left on phones - no button, no row. */
        #dashboard .welcome-side:not(.has-action) {
            display: none;
        }

        #dashboard .welcome-btn {
            justify-content: center;
            padding: 10px 16px;
        }

        #dashboard .notif-scroll {
            max-height: 280px;
        }

        /* Today's reminders sit in the page under the banner instead of
           floating over the content. */
        #dashboard .reminder-alert {
            position: static;
            width: auto;
            max-width: none;
            margin-bottom: 16px;
            border-radius: 14px;
            box-shadow: none;
            animation: none;
        }

        #dashboard .reminder-alert-head {
            padding: 10px 8px 10px 12px;
        }

        #dashboard .reminder-alert-row {
            padding: 10px 12px;
        }

        #dashboard .kpi-row > [class*="col-"],
        #dashboard .panel-row > [class*="col-"] {
            margin-bottom: 16px;
        }

        /* Two cards per row on phones. */
        #dashboard .kpi-row > [class*="col-"] {
            padding-left: 8px;
            padding-right: 8px;
        }

        #dashboard .kpi-row {
            margin-left: -8px;
            margin-right: -8px;
        }

        #dashboard .kpi-card {
            min-height: 0;
            padding: 16px;
            gap: 10px;
            border-radius: 14px;
        }

        #dashboard .kpi-label {
            font-size: 13px;
        }

        #dashboard .kpi-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            font-size: 20px;
        }

        #dashboard .kpi-value {
            font-size: 26px;
        }

        #dashboard .kpi-hint {
            font-size: 11.5px;
        }

        #dashboard .panel-head {
            padding: 16px;
        }

        #dashboard .dash-row {
            padding: 12px 8px;
        }
    }
</style>
