{{-- Notification look shared by the header bell and the dashboard
     panel. Included once per page from the header. --}}
<style>
    /* ---------- Bell dropdown shell ---------- */
    .navbar .dropdown-menu.notif-dropdown {
        width: 380px !important;   /* beats the per-breakpoint widths set for the other navbar dropdowns */
        max-width: calc(100vw - 24px) !important;
        padding: 0;
        border: 1px solid #e6e9f0;
        border-radius: 12px;
        box-shadow: 0 12px 32px rgba(16, 24, 40, 0.14);
        overflow: hidden;
    }

    .notif-dropdown .notif-head,
    .notif-panel .notif-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid #eef0f4;
        background: #fff;
    }

    .notif-head-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1f2937;
    }

    .notif-count {
        min-width: 20px;
        padding: 1px 7px;
        border-radius: 10px;
        background: #1976d2;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        text-align: center;
    }

    .notif-mark-all {
        background: none;
        border: 0;
        padding: 0;
        font-size: 12px;
        font-weight: 600;
        color: #1976d2;
        white-space: nowrap;
        cursor: pointer;
    }

    .notif-mark-all:hover { text-decoration: underline; }

    .notif-scroll {
        max-height: 420px;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    .notif-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 32px 16px;
        color: #9aa0ac;
        font-size: 13px;
        text-align: center;
    }

    .notif-empty i { font-size: 34px; color: #cfd5df; }

    .notif-foot {
        padding: 10px 16px;
        border-top: 1px solid #eef0f4;
        background: #fafbfd;
        text-align: center;
        font-size: 12px;
        color: #8a92a3;
    }

    /* ---------- One notification ---------- */
    .notif-item {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px 16px 13px 18px;
        border-bottom: 1px solid #f1f3f7;
        background: #fff;
        color: inherit;
        text-decoration: none;
        white-space: normal;
        transition: background 0.15s ease;
    }

    .notif-item:last-child { border-bottom: 0; }

    .notif-item:hover,
    .notif-item:focus {
        background: #f4f8fe;
        color: inherit;
        text-decoration: none;
        outline: none;
    }

    /* Unread: tinted row + accent bar; read: quiet and muted. */
    .notif-item.is-unread { background: #f3f8ff; }
    .notif-item.is-unread::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #1976d2;
    }
    .notif-item.is-unread:hover { background: #e8f1fd; }
    .notif-item.is-read .notif-title { font-weight: 500; color: #4b5563; }
    .notif-item.is-read .notif-message { color: #8a92a3; }
    .notif-item.is-read .notif-icon { opacity: 0.7; }

    .notif-icon {
        flex: 0 0 auto;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .notif-icon.tone-blue   { background: #e3effd; color: #1976d2; }
    .notif-icon.tone-purple { background: #efe8fb; color: #7048c9; }
    .notif-icon.tone-orange { background: #fdf0e0; color: #d97706; }
    .notif-icon.tone-teal   { background: #dff5f2; color: #0f8f82; }
    .notif-icon.tone-green  { background: #e2f5e9; color: #1a7a4c; }
    .notif-icon.tone-grey   { background: #eceff3; color: #4b5563; }
    .notif-icon.tone-red    { background: #fdeaea; color: #d33a3a; }

    .notif-body {
        flex: 1 1 auto;
        min-width: 0;            /* lets long text wrap instead of stretching the row */
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .notif-top {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .notif-title {
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .notif-dot {
        flex: 0 0 auto;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #1976d2;
    }

    .notif-message {
        font-size: 12.5px;
        line-height: 1.45;
        color: #4b5563;
        overflow-wrap: anywhere;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notif-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 4px 12px;
        margin-top: 2px;
        font-size: 11px;
        color: #8a92a3;
    }

    .notif-meta i { margin-right: 3px; font-size: 12px; vertical-align: -1px; }
    .notif-lead { color: #1976d2; font-weight: 600; overflow-wrap: anywhere; }

    /* ---------- Dashboard panel ---------- */
    .notif-panel {
        border: 1px solid #e6e9f0;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .notif-panel .notif-item { padding-left: 20px; padding-right: 20px; }
    .notif-cta {
        align-self: center;
        flex: 0 0 auto;
        padding: 5px 12px;
        border-radius: 8px;
        background: #eef4fd;
        color: #1976d2;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .notif-item:hover .notif-cta { background: #1976d2; color: #fff; }

    /* ---------- Small screens ---------- */
    @media (max-width: 575.98px) {
        /* The navbar dropdown is positioned by the theme; on a phone
           pin it to the viewport so it can never overflow off-screen. */
        .navbar .dropdown-menu.notif-dropdown {
            position: fixed !important;
            top: 62px !important;
            left: 12px !important;
            right: 12px !important;
            transform: none !important;
            width: auto !important;
            max-width: none !important;
        }

        .notif-scroll { max-height: 65vh; }

        .notif-cta { display: none; }
        .notif-item { padding: 12px 14px 12px 16px; }
    }
</style>
