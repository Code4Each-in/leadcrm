@extends('layout')

@section('title', 'Lead Details')
@section('subtitle', 'View Lead Information')

@section('content')

{{--
    Redesigned Lead Show page (UI/UX preview) - v2.

    This is a NEW, separate view - leads/show.blade.php is untouched and
    still used by the leads.show route. This file is rendered by the
    leads.show2 route only.

    Visual language follows leads/index2.blade.php (indigo/#6c63ff accent,
    card styling, icon chips, badges, buttons, status toggle, delete
    confirmation). All data, permissions, routes and backend endpoints
    below are unchanged from the previous version of this file - only
    markup/CSS/JS presentation was reworked:
      - Header decluttered, Edit is icon-only, duplicate subtitle removed.
      - Accordions rebuilt on a CSS grid (0fr/1fr) transition instead of
        JS-measured max-height, which was the cause of the open/close
        layout bug. Company Information opens by default on desktop only.
      - Lead Overview is now a compact inline label:value layout, with
        Status editable in place via the same PATCH /leads/{lead}/status
        endpoint and toggle pattern used on leads/index2.
      - Lead Logs moved above Notes & Documents and redesigned as a
        compact, internally-scrollable "console" list.
      - Notes and Documents are rendered as two clearly separated lists
        from the same LeadActivity feed/endpoints (an item is a "note"
        when it has content, a "document" when it only has a file).
      - Delete confirmations (lead / note / document / reminder) reuse
        the soft-alert SweetAlert2 style from leads/index2.
--}}

<style>
    /* ==========================================================
       Cards - same building block reused for every section
       ========================================================== */
    .custom-card {
        border-radius: 14px;
        border: 1px solid #eef0f3;
        background: #fff;
        box-shadow: 0 2px 10px rgba(26, 31, 43, 0.04);
    }

    .custom-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 15px 20px;
        border-bottom: 1px solid #eef0f3;
        font-weight: 700;
        font-size: 14.5px;
        color: #1a1f2b;
    }

    .custom-header .head-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .custom-header .head-left span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .icon-chip {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: #eef0ff;
        color: #5b52e0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    /* ==========================================================
       Accordion - CSS-grid driven (0fr <-> 1fr row track), no JS
       height math. The track's intrinsic size always matches the
       real content, at any length, on resize, on every open/close
       cycle - so there is no stale pixel value that can desync
       from actual content and clip/overlap what's below it.
       ========================================================== */
    .collapsible-header {
        cursor: pointer;
        user-select: none;
    }

    .collapse-icon {
        color: #a4aab5;
        font-size: 20px;
        transition: transform 0.25s ease;
        flex-shrink: 0;
    }

    .collapse-icon.rotated {
        transform: rotate(180deg);
    }

    .collapsible-body {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.28s ease;
    }

    .collapsible-body.is-open {
        grid-template-rows: 1fr;
    }

    .collapsible-inner {
        overflow: hidden;
        min-height: 0;
    }

    .collapsible-inner .card-body {
        padding: 14px 20px 18px;
    }

    /* ==========================================================
       Detail rows (label + value) inside a card body
       ========================================================== */
    .detail-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 3px 14px;
        padding: 11px 0;
        border-bottom: 1px solid #f4f5f8;
        font-size: 13.5px;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-row i.row-icon {
        width: 22px;
        color: #6c63ff;
        font-size: 16px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .label {
        font-weight: 600;
        font-size: 12.5px;
        color: #8a92a3;
        min-width: 180px;
        padding-top: 1px;
    }

    .detail-row .value {
        color: #384153;
        flex: 1 1 180px;
        min-width: 0;
        word-break: break-word;
    }

    /* Lead Overview - compact inline "Label: value" variant of the
       same detail-row markup above (same HTML, tighter rules so it
       reads as one scannable line per fact instead of a stacked
       label-then-value block). Scoped so every other accordion
       keeps its normal spacious layout. */
    .overview-body .detail-row {
        padding: 8px 0;
        gap: 4px 8px;
    }

    .overview-body .label {
        min-width: 0;
        flex: 0 0 auto;
    }

    .overview-body .label::after {
        content: ':';
    }

    .overview-body .value {
        flex: 1 1 auto;
    }

    /* ==========================================================
       Status badge
       ========================================================== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 13px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 600;
    }

    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-complete {
        background: #d4f4e2;
        color: #1a7a4c;
    }
    .status-complete::before { background: #1a7a4c; }

    .status-progress {
        background: #fff3cd;
        color: #8a6d00;
    }
    .status-progress::before { background: #e0a800; }

    /* Status - inline editable toggle switch (Draft <-> Published),
       same pattern as leads/index2.blade.php. */
    .status-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;
    }

    .status-toggle input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .status-toggle .toggle-track {
        position: relative;
        width: 36px;
        height: 20px;
        border-radius: 20px;
        background: #fbd469;
        flex-shrink: 0;
        transition: background 0.15s ease;
    }

    .status-toggle .toggle-track::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        transition: transform 0.15s ease;
    }

    .status-toggle input:checked + .toggle-track {
        background: #34c777;
    }

    .status-toggle input:checked + .toggle-track::after {
        transform: translateX(16px);
    }

    .status-toggle input:focus-visible + .toggle-track {
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.28);
    }

    .status-toggle .toggle-label {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.2px;
        color: #8a6d00;
        white-space: nowrap;
    }

    .status-toggle input:checked ~ .toggle-label {
        color: #1a7a4c;
    }

    .status-toggle.is-loading {
        opacity: 0.55;
        pointer-events: none;
    }

    /* ==========================================================
       Page header
       ========================================================== */
    .ls2-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }

    .ls2-header-main {
        min-width: 0;
    }

    .ls2-eyebrow {
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

    .ls2-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    .ls2-title-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ls2-title {
        font-weight: 700;
        font-size: 24px;
        color: #1a1f2b;
        letter-spacing: -0.3px;
        margin: 0;
        word-break: break-word;
    }

    .ls2-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    /* ==========================================================
       Buttons
       ========================================================== */
    .ls2-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        font-weight: 500;
        font-size: 13.5px;
        border-radius: 9px;
        white-space: nowrap;
        background: #6c63ff;
        border: none;
        color: #fff;
        box-shadow: 0 2px 6px rgba(108, 99, 255, 0.28);
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
    }

    .ls2-btn-primary:hover {
        background: #5b52e8;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(108, 99, 255, 0.34);
    }

    .ls2-btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        font-weight: 500;
        font-size: 13.5px;
        border-radius: 9px;
        white-space: nowrap;
        background: #fff;
        border: 1px solid #e2e5eb;
        color: #6c7280;
        transition: background 0.12s ease, color 0.12s ease;
    }

    .ls2-btn-ghost:hover {
        background: #f4f5f7;
        color: #384153;
    }

    .ls2-btn-icon-danger,
    .ls2-btn-icon-primary {
        width: 42px;
        height: 42px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: none;
        font-size: 17px;
        flex-shrink: 0;
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }

    .ls2-btn-icon-danger {
        background: #fdeaea;
        color: #d33a3a;
    }

    .ls2-btn-icon-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(211, 58, 58, 0.28);
    }

    .ls2-btn-icon-primary {
        background: #eef0ff;
        color: #5b52e0;
    }

    .ls2-btn-icon-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(91, 82, 224, 0.28);
        color: #5b52e0;
    }

    .ls2-btn-soft-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 16px;
        font-weight: 500;
        font-size: 13px;
        border-radius: 9px;
        border: none;
        background: #eef0ff;
        color: #5b52e0;
        transition: background 0.12s ease;
        flex: 1 1 auto;
    }

    .ls2-btn-soft-primary:hover {
        background: #e1e3ff;
        color: #4a43d1;
    }

    .ls2-btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 16px;
        font-weight: 500;
        font-size: 13px;
        border-radius: 9px;
        border: 1px solid #e2e5eb;
        background: #fff;
        color: #6c7280;
        transition: background 0.12s ease, color 0.12s ease;
        flex: 1 1 auto;
    }

    .ls2-btn-outline:hover {
        background: #f4f5f7;
        color: #384153;
    }

    /* ==========================================================
       Sidebar - Reminders quick actions card
       ========================================================== */
    .ls2-reminders-body {
        padding: 4px 20px 20px;
    }

    .ls2-reminders-body p {
        font-size: 13px;
        color: #8a92a3;
        margin: 0 0 14px;
        line-height: 1.5;
    }

    .ls2-reminders-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Reminder rows - the details (Date & Time / Created By / Note)
       show directly here using the same detail-row pattern as every
       other information card on this page - no separate "View"
       step. Only Edit/Delete are interactive. */
    .reminder-item {
        border: 1px solid #eef0f3;
        border-radius: 10px;
        padding: 6px 15px;
        margin-bottom: 10px;
        background: #fff;
    }

    .reminder-item:last-child {
        margin-bottom: 0;
    }

    .reminder-item .reminder-details {
        flex: 1 1 auto;
        min-width: 0;
    }

    /* Yellow "you have a reminder" banner near the header - reuses
       the exact same amber tokens as .status-progress, not a new
       color. Hidden by default; shown by JS only when this lead
       actually has at least one reminder. */
    .ls2-reminder-banner {
        display: none;
        align-items: center;
        gap: 10px;
        background: #fff3cd;
        color: #8a6d00;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .ls2-reminder-banner.show {
        display: flex;
    }

    .ls2-reminder-banner i {
        font-size: 17px;
        flex-shrink: 0;
    }

    .ls2-reminder-banner span {
        flex: 1 1 auto;
    }

    .ls2-reminder-banner button {
        border: none;
        background: rgba(138, 109, 0, 0.12);
        color: #8a6d00;
        font-size: 12.5px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        flex-shrink: 0;
        transition: background 0.12s ease;
    }

    .ls2-reminder-banner button:hover {
        background: rgba(138, 109, 0, 0.2);
    }

    /* Additional Note row inside Company Information - same
       detail-row markup as the rest of that card, just needs
       pre-line wrapping since the source field is a free-text area. */
    .detail-row .value.notes-value {
        white-space: pre-line;
    }

    /* ==========================================================
       Notes & Documents - shared composer + two feed lists, both
       rendered from the same LeadActivity payload.
       ========================================================== */
    .feed-composer {
        margin-bottom: 22px;
    }

    /* Choose-file control - one joined control (readonly filename +
       Browse), not two separate boxes with a gap between them.
       Clicking anywhere in it - the text area or the button -
       triggers the hidden native file input. */
    .file-upload-field {
        display: flex;
        align-items: stretch;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color 0.12s ease, box-shadow 0.12s ease;
    }

    .file-upload-field:hover,
    .file-upload-field:focus-within {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.12);
    }

    .file-upload-field .file-upload-default {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        overflow: hidden;
    }

    .file-upload-field .file-upload-info {
        flex: 1 1 auto;
        min-width: 0;
        border: none;
        background: #fff;
        color: #8a92a3;
        font-size: 13.5px;
        padding: 10px 12px;
        cursor: pointer;
    }

    .file-upload-field .file-upload-info:focus {
        outline: none;
    }

    .file-upload-field .file-upload-info.has-file {
        color: #384153;
    }

    .file-upload-field .file-upload-browse {
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

    .file-upload-field .file-upload-browse:hover {
        background: #eef0ff;
    }

    .feed-section + .feed-section {
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid #eef0f3;
    }

    .feed-heading {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #1a1f2b;
        margin-bottom: 10px;
    }

    .feed-heading i {
        color: #6c63ff;
        font-size: 16px;
    }

    .feed-count {
        margin-left: auto;
        font-size: 11.5px;
        font-weight: 600;
        color: #8a92a3;
        background: #f4f5f8;
        padding: 2px 9px;
        border-radius: 20px;
    }

    .activity-row {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 12px 0;
        border-bottom: 1px solid #f4f5f8;
    }

    .activity-row:last-child {
        border-bottom: none;
    }

    .activity-row-main {
        flex: 1;
        min-width: 0;
    }

    .activity-meta {
        font-size: 12px;
        color: #8a92a3;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .activity-user {
        font-weight: 600;
        color: #6c7280;
    }

    .activity-dot {
        color: #d7dae0;
    }

    .activity-content {
        font-size: 13.5px;
        color: #384153;
        line-height: 1.55;
        word-break: break-word;
    }

    .activity-file-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: 12.5px;
        color: #5b52e0;
        background: #eef0ff;
        padding: 5px 11px;
        border-radius: 8px;
        text-decoration: none;
    }

    .activity-file-chip:hover {
        background: #e1e3ff;
        color: #4a43d1;
    }

    .document-row {
        align-items: center;
    }

    .document-icon {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        background: #d4f4e2;
        color: #1a7a4c;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .document-name {
        font-size: 13.5px;
        font-weight: 600;
        color: #384153;
        text-decoration: none;
        display: inline-block;
        word-break: break-word;
    }

    .document-name:hover {
        color: #5b52e0;
    }

    .activity-actions {
        display: flex;
        gap: 4px;
        flex-shrink: 0;
    }

    /* Same edit/remove icon-button design as leads/index2.blade.php
       (.action-btns .btn-edit / .btn-remove) - persistently tinted,
       not just colored on hover - reused here for Notes, Documents
       and Reminders alike. */
    .activity-action-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: none;
        background: #eef0ff;
        color: #5b52e0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }

    .activity-action-btn.activity-action-danger {
        background: #fdeaea;
        color: #d33a3a;
    }

    .activity-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(26, 31, 43, 0.14);
    }

    .feed-empty {
        text-align: center;
        color: #8a92a3;
        font-size: 13px;
        padding: 20px 0;
    }

    /* Notes & Documents - one combined list, internal scroll so a
       lead with many entries doesn't inflate the whole page (same
       idea as the Logs console, just light-themed for this card). */
    #activityFeed {
        max-height: 420px;
        overflow-y: auto;
        padding-right: 4px;
    }

    #activityFeed::-webkit-scrollbar {
        width: 6px;
    }

    #activityFeed::-webkit-scrollbar-thumb {
        background: #e2e5eb;
        border-radius: 6px;
    }

    /* ==========================================================
       Lead Logs - compact "console" style audit feed. Genuinely
       reflects the existing lead_logs data (action/module/user/
       timestamp) - not a claim of live/real-time updates, just a
       denser, more scannable presentation for a list that can
       reasonably reach 100+ rows.
       ========================================================== */
    .logs-count-badge {
        font-size: 11.5px;
        font-weight: 600;
        color: #6c7280;
        background: #f4f5f8;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .logs-live-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #34c777;
        flex-shrink: 0;
    }

    .logs-console {
        background: #1a1f2b;
        padding: 4px 4px;
        max-height: 400px;
        overflow-y: auto;
        border-radius: 0 0 14px 14px;
    }

    /* Inside the Logs modal there's no card chrome below it to
       protect, so it can use more of the available height. */
    .logs-console-modal {
        max-height: 60vh;
    }

    .logs-console::-webkit-scrollbar {
        width: 8px;
    }

    .logs-console::-webkit-scrollbar-thumb {
        background: #333a4d;
        border-radius: 8px;
    }

    .log-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        flex-wrap: wrap;
    }

    .log-row:last-child {
        border-bottom: none;
    }

    .log-row-time {
        display: flex;
        flex-direction: column;
        gap: 1px;
        font-family: 'SFMono-Regular', Consolas, 'Courier New', monospace;
        flex-shrink: 0;
        min-width: 92px;
    }

    .log-date {
        font-size: 10.5px;
        color: #6c7280;
    }

    .log-time {
        font-size: 11.5px;
        color: #c8cbd4;
    }

    .log-badge {
        flex-shrink: 0;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.4px;
        padding: 3px 8px;
        border-radius: 5px;
        align-self: flex-start;
        margin-top: 1px;
        white-space: nowrap;
    }

    .log-badge-create { background: rgba(26, 122, 76, .28); color: #7fe3ab; }
    .log-badge-update { background: rgba(91, 82, 224, .3); color: #a7a1ff; }
    .log-badge-delete { background: rgba(211, 58, 58, .3); color: #ff9d9d; }
    .log-badge-status { background: rgba(224, 168, 0, .25); color: #ffd873; }
    .log-badge-info,
    .log-badge-system { background: rgba(154, 163, 181, .2); color: #aab1c0; }

    .log-row-body {
        flex: 1;
        min-width: 180px;
    }

    .log-row-desc {
        font-size: 12.8px;
        color: #dfe2e8;
        line-height: 1.5;
        word-break: break-word;
        display: flex;
        align-items: flex-start;
        gap: 6px;
    }

    .log-row-desc i {
        font-size: 14px;
        color: #7d84f2;
        margin-top: 1px;
        flex-shrink: 0;
    }

    .log-row-user {
        font-size: 11px;
        color: #6c7280;
        margin-top: 3px;
    }

    .logs-empty {
        text-align: center;
        color: #6c7280;
        font-size: 13px;
        padding: 26px 0;
    }

    .logs-error {
        color: #ff9d9d;
    }

    /* ==========================================================
       Modals (hand-rolled overlay, same behavior as before)
       ========================================================== */
    .reminder-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(26, 31, 43, 0.45);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    /* SweetAlert2's own container defaults to a much lower z-index
       than our modals above - so a Swal.fire() triggered while a
       reminder/note/lead modal is open was rendering *behind* it
       (invisible, but still open and clickable once the modal in
       front of it closed). Forcing it above 99999 here guarantees
       every confirm dialog / toast always shows on top, regardless
       of which of our own modals is currently open. */
    .swal2-container {
        z-index: 100000 !important;
    }

    .reminder-modal-overlay.show {
        display: flex;
    }

    .reminder-modal-box {
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 12px 40px rgba(0,0,0,.18);
    }

    .reminder-modal-box.large {
        max-width: 700px;
    }

    .reminder-modal-header {
        background: #fff;
        color: #1a1f2b;
        padding: 22px 24px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #eef0f3;
    }

    .reminder-modal-header h5 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #1a1f2b;
    }

    .reminder-modal-header .btn-close {
        background: none;
        border: none;
        font-size: 20px;
        line-height: 1;
        color: #a4aab5;
        cursor: pointer;
        padding: 4px;
    }

    .reminder-modal-header .btn-close:hover {
        color: #384153;
    }

    .reminder-modal-body {
        padding: 22px 24px;
    }

    .reminder-modal-body .form-label {
        font-size: 13.5px;
        font-weight: 500;
        color: #384153;
        margin-bottom: 6px;
    }

    .reminder-modal-body .form-control,
    .reminder-modal-body textarea.form-control {
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13.5px;
    }

    .reminder-modal-body .form-control:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
    }

    .reminder-modal-body .mb-3 {
        margin-bottom: 20px !important;
    }

    .reminder-modal-footer {
        padding: 16px 24px 22px;
        border-top: none;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .reminder-modal-footer .btn-primary {
        background: #6c63ff;
        border-color: #6c63ff;
        border-radius: 9px;
        padding: 9px 22px;
        font-size: 13.5px;
        font-weight: 500;
    }

    .reminder-modal-footer .btn-primary:hover {
        background: #5b52e8;
        border-color: #5b52e8;
    }

    .reminder-modal-footer .btn-light {
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 13.5px;
        border: 1px solid #e2e5eb;
        background: #fff;
        color: #384153;
    }

    .reminder-modal-footer .btn-danger {
        background: #fdeaea;
        border: none;
        color: #d33a3a;
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 13.5px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .reminder-modal-footer .btn-danger:hover {
        background: #fbdada;
    }

    .reminder-modal-body .form-error {
        color: #d33a3a;
        font-size: 12.5px;
        margin-top: 4px;
    }

    /* ==========================================================
       SweetAlert2 - delete confirmation, same soft-alert style
       used on leads/index2.blade.php, reused verbatim here.
       ========================================================== */
    .swal-leads-popup {
        border-radius: 20px !important;
        padding: 32px 28px 28px !important;
    }

    .swal-delete-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: #fdeaea;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: #d33a3a;
    }

    .swal-delete-title {
        font-size: 19px !important;
        color: #1a1f2b !important;
        font-weight: 700 !important;
        margin: 0 0 8px !important;
    }

    .swal-delete-text {
        font-size: 13.5px !important;
        color: #8a92a3 !important;
        line-height: 1.5;
        margin: 0 0 6px !important;
    }

    .swal-delete-text strong {
        color: #384153;
    }

    .swal-leads-popup.swal2-show {
        animation: swalLeadsPopIn 0.22s ease-out;
    }

    @keyframes swalLeadsPopIn {
        from {
            opacity: 0;
            transform: scale(0.92);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .swal-leads-popup .swal2-html-container {
        margin: 0 !important;
    }

    .swal-leads-popup .swal2-actions {
        margin-top: 22px !important;
        gap: 10px;
    }

    .swal-btn-danger {
        background: #d33a3a !important;
        color: #fff !important;
        font-size: 13.5px !important;
        font-weight: 500 !important;
        padding: 9px 20px !important;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    .swal-btn-danger:hover {
        background: #b92e2e !important;
    }

    .swal-btn-cancel {
        background: #fff !important;
        color: #6c7280 !important;
        border: 1px solid #e2e5eb !important;
        font-size: 13.5px !important;
        font-weight: 500 !important;
        padding: 9px 20px !important;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    .swal-btn-cancel:hover {
        background: #f4f5f7 !important;
    }

    /* ==========================================================
       Mobile
       ========================================================== */
    @media (max-width: 768px) {
        .ls2-header {
            flex-direction: column;
        }

        .ls2-header-actions {
            width: 100%;
        }

        .ls2-header-actions .ls2-btn-ghost {
            flex: 1 1 auto;
            justify-content: center;
        }

        .label {
            min-width: 140px;
        }

        .logs-console {
            max-height: 320px;
        }

        .log-row {
            padding: 10px 12px;
        }
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin">

        {{-- ============================================================
             Page header - identity, status, primary actions
             ============================================================ --}}
        <div class="card custom-card mb-4">
            <div class="card-body">
                <div class="ls2-header">

                    <div class="ls2-header-main">
                        <div class="ls2-eyebrow">
                            <i class="mdi mdi-file-account-outline"></i>
                            Lead Details
                        </div>

                        <div class="ls2-title-row">
                            <h3 class="ls2-title">
                                {{ $lead->company_business_name ?? $lead->customer_name ?? 'Lead #'.$lead->id }}
                            </h3>

                            @if($lead->status === 'published')
                                <span class="status-badge status-complete" id="headerStatusBadge">Published</span>
                            @else
                                <span class="status-badge status-progress" id="headerStatusBadge">Draft</span>
                            @endif
                        </div>
                    </div>

                    {{-- Edit / Delete permissions --}}
                    @php
                        $user = Auth::user();
                        $roleName = strtolower($user->role->name ?? '');

                        $isAdmin = in_array($roleName, ['admin', 'super admin']);

                        // Everyone can edit
                        $canEdit = true;

                        // Only admins can delete published leads.
                        // Normal users can delete draft leads.
                        $canDelete = $isAdmin || $lead->status === 'draft';

                        $leadLabel = $lead->company_business_name ?? $lead->customer_name ?? ('Lead #'.$lead->id);
                    @endphp

                    <div class="ls2-header-actions button-group">

                        {{-- Edit - icon only, same style/behavior as Delete --}}
                        @if($canEdit)
                            <a
                                href="{{ route('leads.edit', $lead) }}"
                                class="btn ls2-btn-icon-primary"
                                title="Edit lead"
                            >
                                <i class="mdi mdi-pencil-box"></i>
                            </a>
                        @endif

                        {{-- Delete --}}
                        @if($canDelete)
                            <button
                                type="button"
                                class="btn ls2-btn-icon-danger"
                                id="deleteLeadBtn"
                                data-id="{{ $lead->id }}"
                                data-label="{{ $leadLabel }}"
                                title="Delete lead"
                            >
                                <i class="mdi mdi-delete"></i>
                            </button>
                        @endif

                        {{-- Back --}}
                        <a
                            href="{{ route('leads.index') }}"
                            class="btn ls2-btn-ghost"
                        >
                            <i class="mdi mdi-arrow-left"></i>
                            Back
                        </a>

                    </div>

                </div>
            </div>
        </div>

        {{-- Yellow reminder banner - hidden until JS confirms this lead
             actually has reminders (GET /leads/{lead}/reminders, same
             endpoint the Reminders card already uses). --}}
        <div class="ls2-reminder-banner" id="reminderBanner">
            <i class="mdi mdi-bell-alert-outline"></i>
            <span id="reminderBannerText">You have reminders set for this lead.</span>
            <button type="button" onclick="openRemindersModal()">View</button>
        </div>

        {{-- ============================================================
             Body - grouped detail cards (left) + overview/reminders (right)
             ============================================================ --}}
        <div class="row">

            <div class="col-lg-8 mb-4">

                {{-- Company Information - open by default on desktop --}}
                <div class="card custom-card mb-4">

                    <div class="card-header custom-header collapsible-header" data-default-open="true" onclick="toggleCard(this)">
                        <div class="head-left">
                            <div class="icon-chip"><i class="mdi mdi-office-building-outline"></i></div>
                            <span>Company Information</span>
                        </div>
                        <i class="mdi mdi-chevron-down collapse-icon"></i>
                    </div>

                    <div class="collapsible-body">
                        <div class="collapsible-inner">
                            <div class="card-body">

                                <div class="detail-row">
                                    <i class="mdi mdi-office-building row-icon"></i>
                                    <span class="label">Company / Business Name:</span>
                                    <span class="value">{{ $lead->company_business_name ?? '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-pound row-icon"></i>
                                    <span class="label">Company Number:</span>
                                    <span class="value">{{ $lead->company_number ?? '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-domain row-icon"></i>
                                    <span class="label">Company Type:</span>
                                    <span class="value">{{ $lead->company_type ?? '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-briefcase-outline row-icon"></i>
                                    <span class="label">Business Type:</span>
                                    <span class="value">{{ $lead->business_type ?? '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-calendar row-icon"></i>
                                    <span class="label">Business Start Date:</span>
                                    <span class="value">{{ $lead->business_start_date?->format('d M Y') ?? '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-map-marker row-icon"></i>
                                    <span class="label">Registered Address:</span>
                                    <span class="value">{{ $lead->business_registered_address ?? '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-map-marker-outline row-icon"></i>
                                    <span class="label">Trading Address:</span>
                                    <span class="value">{{ $lead->business_trading_address ?? '-' }}</span>
                                </div>

                                {{-- Additional Note - lives on the Lead itself
                                     ($lead->notes), shown here as part of Company
                                     Information rather than as its own section. --}}
                                <div class="detail-row">
                                    <i class="mdi mdi-note-text-outline row-icon"></i>
                                    <span class="label">Additional Note:</span>
                                    <span class="value notes-value">{{ $lead->notes ?? 'No additional notes have been added for this lead.' }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="card custom-card mb-4">

                    <div class="card-header custom-header collapsible-header" onclick="toggleCard(this)">
                        <div class="head-left">
                            <div class="icon-chip"><i class="mdi mdi-account-box-outline"></i></div>
                            <span>Contact Information</span>
                        </div>
                        <i class="mdi mdi-chevron-down collapse-icon"></i>
                    </div>

                    <div class="collapsible-body">
                        <div class="collapsible-inner">
                            <div class="card-body">

                                <div class="detail-row">
                                    <i class="mdi mdi-account row-icon"></i>
                                    <span class="label">Customer Name:</span>
                                    <span class="value">{{ $lead->customer_name ?? '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-account-tie row-icon"></i>
                                    <span class="label">Contact Person:</span>
                                    <span class="value">{{ $lead->contact_person ?? '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-cake row-icon"></i>
                                    <span class="label">Date of Birth:</span>
                                    <span class="value">{{ $lead->date_of_birth?->format('d M Y') ?? '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-email row-icon"></i>
                                    <span class="label">Email:</span>
                                    <span class="value">{{ $lead->email ?? '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-phone row-icon"></i>
                                    <span class="label">Phone:</span>
                                    <span class="value">{{ $lead->phone_no ? '+44 '.$lead->phone_no : '-' }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="mdi mdi-cellphone row-icon"></i>
                                    <span class="label">Mobile:</span>
                                    <span class="value">{{ $lead->mobile_no ? '+44 '.$lead->mobile_no : '-' }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Financial Information (NFS / AF4U only) --}}
                @if(in_array(strtolower($lead->product->name ?? ''), ['nfs', 'af4u']))
                    <div class="card custom-card mb-4">

                        <div class="card-header custom-header collapsible-header" onclick="toggleCard(this)">
                            <div class="head-left">
                                <div class="icon-chip"><i class="mdi mdi-cash-multiple"></i></div>
                                <span>Financial Information</span>
                            </div>
                            <i class="mdi mdi-chevron-down collapse-icon"></i>
                        </div>

                        <div class="collapsible-body">
                            <div class="collapsible-inner">
                                <div class="card-body">

                                    <div class="detail-row">
                                        <i class="mdi mdi-cash-multiple row-icon"></i>
                                        <span class="label">Gross Sales:</span>
                                        <span class="value">{{ $lead->gross_sales ? '£'.number_format($lead->gross_sales, 2) : '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-cash row-icon"></i>
                                        <span class="label">Funds Required:</span>
                                        <span class="value">{{ $lead->funds_required ? '£'.number_format($lead->funds_required, 2) : '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-calendar-range row-icon"></i>
                                        <span class="label">Funds Term (Months):</span>
                                        <span class="value">{{ $lead->funds_term_months ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-home-outline row-icon"></i>
                                        <span class="label">Home Owner:</span>
                                        <span class="value">{{ $lead->home_owner ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-file-percent-outline row-icon"></i>
                                        <span class="label">VAT Registered:</span>
                                        <span class="value">{{ $lead->vat_registered ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-target row-icon"></i>
                                        <span class="label">Loan Purpose:</span>
                                        <span class="value">{{ $lead->loan_purpose ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-text-box-outline row-icon"></i>
                                        <span class="label">Funds Usage Details:</span>
                                        <span class="value">{{ $lead->funds_usage_details ?? '-' }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Utility / Supply Information (AU Savers only) --}}
                @if(strtolower($lead->product->name ?? '') == 'au savers')
                    <div class="card custom-card mb-4">

                        <div class="card-header custom-header collapsible-header" onclick="toggleCard(this)">
                            <div class="head-left">
                                <div class="icon-chip"><i class="mdi mdi-flash-outline"></i></div>
                                <span>Utility / Supply Information</span>
                            </div>
                            <i class="mdi mdi-chevron-down collapse-icon"></i>
                        </div>

                        <div class="collapsible-body">
                            <div class="collapsible-inner">
                                <div class="card-body">

                                    <div class="detail-row">
                                        <i class="mdi mdi-map-marker-radius-outline row-icon"></i>
                                        <span class="label">Supply Address:</span>
                                        <span class="value">{{ $lead->supply_address ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-mailbox-outline row-icon"></i>
                                        <span class="label">Postcode:</span>
                                        <span class="value">{{ $lead->postcode ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-office-building-marker-outline row-icon"></i>
                                        <span class="label">Number of Sites:</span>
                                        <span class="value">{{ $lead->number_of_sites ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-flash-outline row-icon"></i>
                                        <span class="label">MPAN:</span>
                                        <span class="value">{{ $lead->mpan ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-gas-cylinder row-icon"></i>
                                        <span class="label">MPRN:</span>
                                        <span class="value">{{ $lead->mprn ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-barcode row-icon"></i>
                                        <span class="label">SPID:</span>
                                        <span class="value">{{ $lead->spid ?? '-' }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- RIGHT: Overview + Reminders --}}
            <div class="col-lg-4 mb-4">

                {{-- Lead Overview - compact inline label:value, Status editable --}}
                <div class="card custom-card mb-4">

                    <div class="custom-header">
                        <div class="head-left">
                            <div class="icon-chip"><i class="mdi mdi-information-outline"></i></div>
                            <span>Lead Overview</span>
                        </div>
                    </div>

                    <div class="card-body overview-body">

                        <div class="detail-row">
                            <i class="mdi mdi-flag row-icon"></i>
                            <span class="label">Status</span>
                            <span class="value">
                                @if($canEdit)
                                    <label class="status-toggle" data-id="{{ $lead->id }}">
                                        <input
                                            type="checkbox"
                                            class="status-toggle-input"
                                            id="leadStatusToggle"
                                            data-id="{{ $lead->id }}"
                                            {{ $lead->status === 'published' ? 'checked' : '' }}
                                        >
                                        <span class="toggle-track"></span>
                                        <span class="toggle-label">{{ $lead->status === 'published' ? 'Published' : 'Draft' }}</span>
                                    </label>
                                @else
                                    @if($lead->status === 'published')
                                        <span class="status-badge status-complete">Published</span>
                                    @else
                                        <span class="status-badge status-progress">Draft</span>
                                    @endif
                                @endif
                            </span>
                        </div>

                        <div class="detail-row">
                            <i class="mdi mdi-package-variant row-icon"></i>
                            <span class="label">Product</span>
                            <span class="value">{{ $lead->product->name ?? '-' }}</span>
                        </div>

                        <div class="detail-row">
                            <i class="mdi mdi-pound-box-outline row-icon"></i>
                            <span class="label">Lead ID</span>
                            <span class="value">#{{ $lead->id }}</span>
                        </div>

                        <div class="detail-row">
                            <i class="mdi mdi-account-circle-outline row-icon"></i>
                            <span class="label">Created By</span>
                            <span class="value">{{ $lead->creator->name ?? '-' }}</span>
                        </div>

                        <div class="detail-row">
                            <i class="mdi mdi-calendar-outline row-icon"></i>
                            <span class="label">Created On</span>
                            <span class="value">{{ $lead->created_at?->format('d M Y, h:i A') ?? '-' }}</span>
                        </div>

                        <div class="detail-row">
                            <i class="mdi mdi-update row-icon"></i>
                            <span class="label">Last Updated</span>
                            <span class="value">{{ $lead->updated_at?->format('d M Y, h:i A') ?? '-' }}</span>
                        </div>

                    </div>
                </div>

                {{-- Reminders quick actions --}}
                <div class="card custom-card mb-4">

                    <div class="custom-header">
                        <div class="head-left">
                            <div class="icon-chip"><i class="mdi mdi-bell-outline"></i></div>
                            <span>Reminders</span>
                        </div>
                    </div>

                    <div class="ls2-reminders-body">
                        <p>Stay on top of follow-ups for this lead - schedule a new reminder or review what's already set.</p>

                        <div class="ls2-reminders-actions">
                            <button
                                type="button"
                                class="ls2-btn-soft-primary"
                                onclick="openAddReminderModal()"
                            >
                                <i class="mdi mdi-bell-plus"></i>
                                Add Reminder
                            </button>

                            <button
                                type="button"
                                class="ls2-btn-outline"
                                onclick="openRemindersModal()"
                            >
                                <i class="mdi mdi-bell"></i>
                                View Reminders
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Lead Logs - the console-style audit trail itself lives
                     in a modal (see below) so 100+ entries never inflate
                     this page; this card is just a compact summary +
                     trigger, kept right under Reminders in this same
                     narrower column instead of as its own full-width row. --}}
                <div class="card custom-card">

                    <div class="custom-header">
                        <div class="head-left">
                            <div class="icon-chip"><i class="mdi mdi-history"></i></div>
                            <span class="logs-live-dot"></span>
                            <span>Lead Logs</span>
                        </div>
                        <span class="logs-count-badge" id="logsCountBadge">0 logs</span>
                    </div>

                    <div class="ls2-reminders-body">
                        <p>Full audit trail for this lead - status changes, notes, documents and reminders, newest first.</p>

                        <div class="ls2-reminders-actions">
                            <button type="button" class="ls2-btn-soft-primary" onclick="openLogsModal()">
                                <i class="mdi mdi-eye-outline"></i>
                                View Logs
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ============================================================
             Notes & Documents - one shared feed (LeadActivity), split
             into two lists client-side: items with text go under Notes,
             file-only items go under Documents.
             ============================================================ --}}
        <div class="card custom-card">

            <div class="custom-header">
                <div class="head-left">
                    <div class="icon-chip"><i class="mdi mdi-note-multiple-outline"></i></div>
                    <span>Notes &amp; Documents</span>
                </div>
                <span class="logs-count-badge" id="feedCount">0</span>
            </div>

            <div class="card-body">

                <div class="feed-section">
                    <div id="activityFeed">
                        <div class="feed-empty">Loading...</div>
                    </div>
                </div>

                <div class="feed-composer feed-section">
                    <div class="feed-heading">
                        <i class="mdi mdi-pencil-plus-outline"></i>
                        Add Note / Document
                    </div>

                    <div id="quillEditor" style="height: 150px; background:#fff;"></div>

                    <div class="file-upload-field mt-3" id="documentFileField">
                        <input type="text" id="documentFileName" class="file-upload-info" placeholder="No file chosen" readonly>
                        <input type="file" id="documentFile" class="file-upload-default">
                        <button type="button" class="file-upload-browse">
                            <i class="mdi mdi-paperclip"></i>
                            Browse
                        </button>
                    </div>

                    <button type="button" class="btn ls2-btn-primary mt-3" id="sentBtn" onclick="sendActivity()">
                        <span id="sentBtnText">Sent</span>
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>

<div id="addReminderModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box">

        <div class="reminder-modal-header">
            <h5>Add Reminder</h5>
            <button type="button" class="btn-close" onclick="closeAddReminderModal()">&times;</button>
        </div>

        <form id="addReminderForm" method="POST" action="{{ route('leads.reminders.store', $lead) }}">
            @csrf

            <div class="reminder-modal-body">

                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="reminder_date" class="form-control"
                           min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Time</label>
                    <input type="time" name="reminder_time" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Note (Optional)</label>
                    <textarea name="note" class="form-control" rows="4" placeholder=""></textarea>
                </div>

                <div class="form-error" id="addReminderError" hidden></div>

            </div>

            <div class="reminder-modal-footer">
                <button type="button" class="btn btn-light" onclick="closeAddReminderModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="addReminderSubmitBtn">Save Reminder</button>
            </div>

        </form>
    </div>
</div>
<div id="remindersModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box large">

        <div class="reminder-modal-header">
            <h5>Lead Reminders</h5>
            <button type="button" class="btn-close" onclick="closeRemindersModal()">&times;</button>
        </div>

        <div class="reminder-modal-body">

            <div id="remindersLoading" class="text-center py-4">
                <div class="spinner-border"></div>
                <div class="mt-2 text-muted">Loading reminders...</div>
            </div>

            <div id="remindersList"></div>

        </div>

    </div>
</div>

<!-- View Reminder Modal -->
<!-- Edit Reminder Modal -->
<div id="editReminderModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box">

        <div class="reminder-modal-header">
            <h5>Edit Reminder</h5>
            <button type="button" class="btn-close" onclick="closeEditReminderModal()">&times;</button>
        </div>

        <div class="reminder-modal-body">

            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" id="editReminderDate" class="form-control" min="{{ date('Y-m-d') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Time</label>
                <input type="time" id="editReminderTime" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Note (Optional)</label>
                <textarea id="editReminderNote" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-error" id="editReminderError" hidden></div>

        </div>

        <div class="reminder-modal-footer">
            <button type="button" class="btn btn-light" onclick="closeEditReminderModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveEditedReminder()">Save Changes</button>
        </div>

    </div>
</div>

<!-- Lead Logs Modal - same console markup/behavior as before, now
     shown on demand instead of taking up permanent page space. -->
<div id="logsModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box large">

        <div class="reminder-modal-header">
            <h5>Lead Logs</h5>
            <button type="button" class="btn-close" onclick="closeLogsModal()">&times;</button>
        </div>

        <div class="logs-console logs-console-modal">
            <div id="activityLogFeed">
                <div class="logs-empty" id="logsLoading">Loading activity log...</div>
            </div>
        </div>

    </div>
</div>

<!-- Edit Note Modal -->
<div id="editActivityModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box">

        <div class="reminder-modal-header">
            <h5>Edit Note</h5>
            <button type="button" class="btn-close" onclick="closeEditModal()">&times;</button>
        </div>

        <div class="reminder-modal-body">
            <label class="form-label">Note</label>
            <div id="quillEditEditor" style="height: 150px; background:#fff;"></div>
        </div>

        <div class="reminder-modal-footer">
            <button type="button" class="btn btn-light" onclick="closeEditModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveEditedActivity()">Save</button>
        </div>

    </div>
</div>

<script>
    const quill = new Quill('#quillEditor', {
        theme: 'snow',
        placeholder: 'Write a note...',
        modules: {
            toolbar: [
                [{ font: [] }, { header: [1, 2, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ color: [] }, { background: [] }],
                [{ script: 'sub' }, { script: 'super' }],
                ['blockquote', 'code-block'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ indent: '-1' }, { indent: '+1' }],
                [{ align: [] }],
                ['link', 'image', 'video'],
                ['clean'],
            ],
        },
    });

    const quillEdit = new Quill('#quillEditEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link', 'clean'],
            ],
        },
    });

    const leadId = {{ $lead->id }};
    const currentUserId = {{ Auth::id() }};
    // Admin / Super Admin get full manage access to notes, documents
    // and reminders regardless of who created them - mirrors the
    // exact backend check in LeadActivityController/LeadController,
    // so the UI never offers an action the server would then reject.
    const isAdmin = @js($isAdmin);
    let editingActivityId = null;
    let editingReminderId = null;
    let feedItems = [];
    let remindersCache = [];

    function csrfToken()
    {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function escapeHtml(value)
    {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    /*
    * ============================================================
    * ACCORDIONS - CSS grid (0fr/1fr) driven, toggled by a single
    * class. Company Information opens by default on desktop only;
    * everything else (and every section on mobile) starts closed.
    * ============================================================
    */
    function isMobileViewport()
    {
        return window.matchMedia('(max-width: 768px)').matches;
    }

    function toggleCard(header)
    {
        const body = header.nextElementSibling;
        const icon = header.querySelector('.collapse-icon');
        const isOpen = body.classList.toggle('is-open');
        icon.classList.toggle('rotated', isOpen);
    }

    function initAccordions()
    {
        document.querySelectorAll('.collapsible-header').forEach(function (header) {
            const body = header.nextElementSibling;
            const icon = header.querySelector('.collapse-icon');
            const shouldOpen = header.dataset.defaultOpen === 'true' && !isMobileViewport();

            body.classList.toggle('is-open', shouldOpen);
            icon.classList.toggle('rotated', shouldOpen);
        });
    }

    /*
    * ============================================================
    * STATUS - inline editable toggle in Lead Overview. Reuses the
    * exact PATCH /leads/{lead}/status endpoint and update/toast/
    * revert-on-error behavior from leads/index2.blade.php.
    * ============================================================
    */
    function updateHeaderStatusBadge(status)
    {
        const badge = document.getElementById('headerStatusBadge');
        if (!badge) return;

        if (status === 'published') {
            badge.className = 'status-badge status-complete';
            badge.textContent = 'Published';
        } else {
            badge.className = 'status-badge status-progress';
            badge.textContent = 'Draft';
        }
    }

    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('status-toggle-input')) return;

        const checkbox = e.target;
        const wrapper = checkbox.closest('.status-toggle');
        const label = wrapper.querySelector('.toggle-label');
        const id = checkbox.dataset.id;
        const newStatus = checkbox.checked ? 'published' : 'draft';
        const previousStatus = newStatus === 'published' ? 'draft' : 'published';

        wrapper.classList.add('is-loading');

        fetch(`/leads/${id}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: newStatus }),
        })
        .then(res => {
            if (!res.ok) throw new Error('Status update failed.');
            return res.json();
        })
        .then(result => {
            label.textContent = newStatus === 'published' ? 'Published' : 'Draft';
            updateHeaderStatusBadge(newStatus);
            loadLogs();

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: result && result.message ? result.message : 'Status updated.',
                showConfirmButton: false,
                timer: 2200,
                timerProgressBar: true,
            });
        })
        .catch(() => {
            checkbox.checked = previousStatus === 'published';
            label.textContent = previousStatus === 'published' ? 'Published' : 'Draft';

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Could not update the status. Please try again.',
                showConfirmButton: false,
                timer: 2800,
                timerProgressBar: true,
            });
        })
        .finally(() => wrapper.classList.remove('is-loading'));
    });

    /*
    * ============================================================
    * NOTES & DOCUMENTS - one feed (LeadActivity), rendered as two
    * lists. `feedItems` is kept as the source of truth and both
    * lists are re-rendered from it on every change - simpler and
    * more reliable than patching two DOM lists by hand.
    * ============================================================
    */
    function hasNoteContent(item)
    {
        return !!(item.content && item.content.trim().length);
    }

    function hasAttachedFile(item)
    {
        return !!item.file_path;
    }

    function formatFeedWhen(iso)
    {
        return new Date(iso).toLocaleString('en-GB', {
            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
        });
    }

    function formatFileSize(bytes)
    {
        if (!bytes) return '';
        const kb = bytes / 1024;
        if (kb < 1024) return `${kb.toFixed(0)} KB`;
        return `${(kb / 1024).toFixed(1)} MB`;
    }

    function renderItemActions(item)
    {
        const isOwner = Number(item.created_by) === Number(currentUserId);
        if (!isOwner && !isAdmin) return '';

        return `
            <div class="activity-actions">
                <button type="button" class="activity-action-btn" title="Edit" onclick="openEditModal(${item.id})">
                    <i class="mdi mdi-pencil-box"></i>
                </button>
                <button type="button" class="activity-action-btn activity-action-danger" title="Delete" onclick="deleteActivity(${item.id})">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        `;
    }

    function renderNoteItem(item)
    {
        const when = formatFeedWhen(item.created_at);

        const fileBlock = hasAttachedFile(item) ? `
            <a class="activity-file-chip" href="/storage/${item.file_path}" target="_blank">
                <i class="mdi mdi-paperclip"></i> ${escapeHtml(item.original_name)}
            </a>
        ` : '';

        return `
            <div class="activity-row" data-activity-id="${item.id}" data-raw-content="${item.content ? encodeURIComponent(item.content) : ''}">
                <div class="activity-row-main">
                    <div class="activity-meta">
                        <span class="activity-user">${escapeHtml(item.creator?.name ?? 'Unknown')}</span>
                        <span class="activity-dot">&middot;</span>
                        <span>${when}</span>
                    </div>
                    <div class="activity-content">${item.content}</div>
                    ${fileBlock}
                </div>
                ${renderItemActions(item)}
            </div>
        `;
    }

    function renderDocumentItem(item)
    {
        const when = formatFeedWhen(item.created_at);
        const sizeLabel = formatFileSize(item.file_size);

        return `
            <div class="activity-row document-row" data-activity-id="${item.id}" data-raw-content="${item.content ? encodeURIComponent(item.content) : ''}">
                <div class="document-icon"><i class="mdi mdi-file-document-outline"></i></div>
                <div class="activity-row-main">
                    <a class="document-name" href="/storage/${item.file_path}" target="_blank">${escapeHtml(item.original_name)}</a>
                    <div class="activity-meta">
                        <span class="activity-user">${escapeHtml(item.creator?.name ?? 'Unknown')}</span>
                        <span class="activity-dot">&middot;</span>
                        <span>${when}</span>
                        ${sizeLabel ? `<span class="activity-dot">&middot;</span><span>${sizeLabel}</span>` : ''}
                    </div>
                </div>
                ${renderItemActions(item)}
            </div>
        `;
    }

    /*
    * One combined list - each item renders as a note row (if it has
    * text, attachment shown inline within it) or a document row (if
    * it's file-only). feedItems is already newest-first (backend
    * orders by latest()), and upsert/remove below keep it that way.
    */
    function renderFeedLists(items)
    {
        const feed = document.getElementById('activityFeed');

        feed.innerHTML = items.length
            ? items.map(item => hasNoteContent(item) ? renderNoteItem(item) : renderDocumentItem(item)).join('')
            : '<div class="feed-empty">No notes or documents yet.</div>';

        document.getElementById('feedCount').textContent = items.length;
    }

    function loadFeed()
    {
        const feed = document.getElementById('activityFeed');
        feed.innerHTML = '<div class="feed-empty">Loading...</div>';

        fetch(`/leads/${leadId}/activities`, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(items => {
                feedItems = items;
                renderFeedLists(feedItems);
            })
            .catch(err => {
                feed.innerHTML = '<div class="alert alert-danger">Unable to load notes and documents.</div>';
                console.error(err);
            });
    }

    function upsertActivityItem(item)
    {
        const idx = feedItems.findIndex(i => i.id === item.id);

        if (idx >= 0) {
            feedItems[idx] = item;
        } else {
            feedItems.unshift(item);
        }

        renderFeedLists(feedItems);
    }

    function removeActivityItem(id)
    {
        feedItems = feedItems.filter(i => i.id !== id);
        renderFeedLists(feedItems);
    }

    /*
    * Choose-file control - a hidden native input triggered by the
    * visible "Browse" button, with the chosen filename mirrored
    * into a readonly text field. Same pattern already used on the
    * Agency/Users/Profile pages; wired directly here (rather than
    * relying on assets/js/file-upload.js) since that shared script
    * isn't actually included by the app's layout.
    */
    function resetDocumentFileField()
    {
        const nameField = document.getElementById('documentFileName');
        nameField.value = '';
        nameField.classList.remove('has-file');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('documentFile');
        const fieldWrapper = document.getElementById('documentFileField');
        const nameField = document.getElementById('documentFileName');

        if (!fileInput || !fieldWrapper || !nameField) return;

        // One click target for the whole control - the readonly
        // text area and the Browse button both bubble up here, so
        // there's no separate listener needed per element.
        fieldWrapper.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                nameField.value = fileInput.files[0].name;
                nameField.classList.add('has-file');
            } else {
                resetDocumentFileField();
            }
        });
    });

    function sendActivity()
    {
        const contentHtml = quill.root.innerHTML.trim();
        const hasContent = contentHtml && contentHtml !== '<p><br></p>';

        const fileInput = document.getElementById('documentFile');
        const hasFile = fileInput.files.length > 0;

        if (!hasContent && !hasFile) {
            Swal.fire('Missing info', 'Please write a note, choose a file, or both.', 'warning');
            return;
        }

        const btn = document.getElementById('sentBtn');
        btn.disabled = true;
        document.getElementById('sentBtnText').textContent = 'Sending...';

        const formData = new FormData();
        if (hasContent) formData.append('content', contentHtml);
        if (hasFile) formData.append('file', fileInput.files[0]);

        fetch(`/leads/${leadId}/activities`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                quill.root.innerHTML = '';
                fileInput.value = '';
                resetDocumentFileField();
                upsertActivityItem(result.activity);
                loadLogs();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Posted',
                    showConfirmButton: false,
                    timer: 1800,
                    timerProgressBar: true,
                });
            } else {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: result.message ?? 'Unable to save.',
                    showConfirmButton: false,
                    timer: 2400,
                    timerProgressBar: true,
                });
            }
        })
        .catch(err => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Unable to save. Please try again.',
                showConfirmButton: false,
                timer: 2400,
                timerProgressBar: true,
            });
            console.error(err);
        })
        .finally(() => {
            btn.disabled = false;
            document.getElementById('sentBtnText').textContent = 'Sent';
        });
    }

    function openEditModal(id)
    {
        const row = document.querySelector(`[data-activity-id="${id}"]`);
        const raw = row?.dataset.rawContent ? decodeURIComponent(row.dataset.rawContent) : '';

        editingActivityId = id;
        quillEdit.root.innerHTML = raw;

        document.getElementById('editActivityModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal()
    {
        document.getElementById('editActivityModal').classList.remove('show');
        document.body.style.overflow = '';
        editingActivityId = null;
    }

    function saveEditedActivity()
    {
        const contentHtml = quillEdit.root.innerHTML.trim();

        if (!contentHtml || contentHtml === '<p><br></p>') {
            Swal.fire('Missing info', 'Note cannot be empty.', 'warning');
            return;
        }

        fetch(`/lead-activities/${editingActivityId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ content: contentHtml }),
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                upsertActivityItem(result.activity);
                closeEditModal();
                loadLogs();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Updated',
                    showConfirmButton: false,
                    timer: 1800,
                    timerProgressBar: true,
                });
            } else {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: result.message ?? 'Unable to update.',
                    showConfirmButton: false,
                    timer: 2400,
                    timerProgressBar: true,
                });
            }
        })
        .catch(err => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Unable to update.',
                showConfirmButton: false,
                timer: 2400,
                timerProgressBar: true,
            });
            console.error(err);
        });
    }

    function deleteActivity(id)
    {
        const row = document.querySelector(`[data-activity-id="${id}"]`);
        const isDocument = row?.classList.contains('document-row');

        Swal.fire({
            html: `
                <div class="swal-delete-icon">
                    <i class="mdi mdi-trash-can-outline"></i>
                </div>
                <h2 class="swal-delete-title">Delete this ${isDocument ? 'document' : 'note'}?</h2>
                <p class="swal-delete-text">This action can't be undone.</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                popup: 'swal-leads-popup',
                confirmButton: 'swal-btn-danger',
                cancelButton: 'swal-btn-cancel',
            },
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch(`/lead-activities/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    removeActivityItem(id);
                    loadLogs();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Deleted',
                        showConfirmButton: false,
                        timer: 1600,
                        timerProgressBar: true,
                    });
                } else {
                    Swal.fire('Error', 'Unable to delete.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Unable to delete.', 'error');
                console.error(err);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', loadFeed);

    /*
    * ============================================================
    * LEAD LOGS - read-only console-style audit feed. Loaded once
    * on page load, same single-fetch pattern as loadFeed() above.
    * No edit/delete UI is ever wired to these entries - the log
    * is write-once. Only existing action/module values are used.
    * ============================================================
    */
    function logIcon(item)
    {
        const icons = {
            lead_created: 'mdi-plus-circle-outline',
            lead_updated: 'mdi-pencil-outline',
            lead_status_changed: 'mdi-flag-outline',
            lead_deleted: 'mdi-delete-outline',
            lead_restored: 'mdi-backup-restore',
            lead_viewed: 'mdi-eye-outline',
            note_created: 'mdi-note-plus-outline',
            note_updated: 'mdi-note-edit-outline',
            note_deleted: 'mdi-note-remove-outline',
            document_uploaded: 'mdi-file-upload-outline',
            document_deleted: 'mdi-file-remove-outline',
            reminder_created: 'mdi-bell-plus-outline',
            reminder_deleted: 'mdi-bell-remove-outline',
        };

        return icons[item.action] || 'mdi-information-outline';
    }

    function logBadgeInfo(item)
    {
        const action = item.action || '';

        if (action === 'lead_status_changed') return { label: 'STATUS', cls: 'log-badge-status' };
        if (action === 'lead_viewed') return { label: 'INFO', cls: 'log-badge-info' };
        if (action === 'lead_restored') return { label: 'SYSTEM', cls: 'log-badge-system' };
        if (action === 'document_uploaded' || action.endsWith('_created')) return { label: 'CREATE', cls: 'log-badge-create' };
        if (action.endsWith('_updated')) return { label: 'UPDATE', cls: 'log-badge-update' };
        if (action.endsWith('_deleted')) return { label: 'DELETE', cls: 'log-badge-delete' };

        return { label: (item.module || 'lead').toUpperCase(), cls: 'log-badge-info' };
    }

    function formatLogWhen(iso)
    {
        const d = new Date(iso);

        return {
            date: d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }),
            time: d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }),
        };
    }

    function renderLog(item)
    {
        const when = formatLogWhen(item.created_at);
        const badge = logBadgeInfo(item);
        const userName = escapeHtml(item.user_name || item.user?.name || 'Someone');

        return `
            <div class="log-row">
                <div class="log-row-time">
                    <span class="log-date">${when.date}</span>
                    <span class="log-time">${when.time}</span>
                </div>
                <span class="log-badge ${badge.cls}">${badge.label}</span>
                <div class="log-row-body">
                    <div class="log-row-desc">
                        <i class="mdi ${logIcon(item)}"></i>
                        ${escapeHtml(item.description)}
                    </div>
                    <div class="log-row-user">${userName}</div>
                </div>
            </div>
        `;
    }

    function loadLogs()
    {
        const feed = document.getElementById('activityLogFeed');
        const countBadge = document.getElementById('logsCountBadge');

        feed.innerHTML = '<div class="logs-empty">Loading activity log...</div>';

        fetch(`/leads/${leadId}/logs`, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(items => {
                if (countBadge) {
                    countBadge.textContent = items.length === 1 ? '1 log' : `${items.length} logs`;
                }

                if (!items.length) {
                    feed.innerHTML = '<div class="logs-empty">No activity recorded yet.</div>';
                    return;
                }

                feed.innerHTML = items.map(renderLog).join('');
            })
            .catch(err => {
                feed.innerHTML = '<div class="logs-empty logs-error">Unable to load the activity log.</div>';
                console.error(err);
            });
    }

    document.addEventListener('DOMContentLoaded', loadLogs);

    function openLogsModal()
    {
        document.getElementById('logsModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeLogsModal()
    {
        document.getElementById('logsModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    /*
    * ============================================================
    * PAGE INIT - accordions + delete-lead confirmation (soft-alert
    * style reused from leads/index2.blade.php).
    * ============================================================
    */
    document.addEventListener('DOMContentLoaded', function () {
        initAccordions();

        const deleteButton = document.getElementById('deleteLeadBtn');

        if (!deleteButton) {
            return;
        }

        deleteButton.addEventListener('click', function () {

            const id = this.dataset.id;
            const escapedLeadLabel = escapeHtml(this.dataset.label || 'This lead');

            Swal.fire({
                html: `
                    <div class="swal-delete-icon">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </div>
                    <h2 class="swal-delete-title">Delete this lead?</h2>
                    <p class="swal-delete-text">
                        <strong>${escapedLeadLabel}</strong> will be permanently
                        removed. This action can't be undone.
                    </p>
                `,
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                buttonsStyling: false,
                reverseButtons: true,
                customClass: {
                    popup: 'swal-leads-popup',
                    confirmButton: 'swal-btn-danger',
                    cancelButton: 'swal-btn-cancel',
                },
            }).then(function (result) {

                if (!result.isConfirmed) {
                    return;
                }

                fetch(`/leads/${id}`, {

                    method: 'DELETE',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }

                })
                .then(response => {

                    if (!response.ok) {
                        throw new Error('Delete request failed.');
                    }

                    return response.json();

                })
                .then(data => {

                    Swal.fire({
                        title: 'Deleted!',
                        text: data.message || 'The Lead has been deleted.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {

                        window.location.href = "{{ route('leads.index') }}";

                    });

                })
                .catch(error => {

                    console.error(error);

                    Swal.fire(
                        'Error',
                        'Something went wrong while deleting. Please try again.',
                        'error'
                    );

                });

            });

        });
    });

    /*
    * ============================================================
    * REMINDERS - unchanged endpoints/behavior; delete confirmation
    * upgraded from a bare confirm() to the same soft-alert style.
    * ============================================================
    */

    /*
    * Yellow banner near the header - a lightweight one-off check on
    * page load (same GET /leads/{lead}/reminders endpoint the
    * Reminders card already uses) purely to decide whether the
    * banner should show. Not tied to the List/View/Edit modal flow.
    */
    function checkReminderBanner()
    {
        fetch('{{ route('leads.reminders', $lead) }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(res => res.ok ? res.json() : [])
        .then(reminders => {
            const banner = document.getElementById('reminderBanner');
            if (!banner) return;

            if (reminders.length) {
                document.getElementById('reminderBannerText').textContent = reminders.length === 1
                    ? 'You have 1 reminder set for this lead.'
                    : `You have ${reminders.length} reminders set for this lead.`;
                banner.classList.add('show');
            } else {
                banner.classList.remove('show');
            }
        })
        .catch(() => {});
    }

    document.addEventListener('DOMContentLoaded', checkReminderBanner);

    function openAddReminderModal()
    {
        document
            .getElementById('addReminderModal')
            .classList.add('show');

        document.body.style.overflow = 'hidden';
    }

    function closeAddReminderModal()
    {
        document
            .getElementById('addReminderModal')
            .classList.remove('show');

        document.body.style.overflow = '';

        document.getElementById('addReminderForm').reset();
        setAddReminderError('');
    }

    function setAddReminderError(message)
    {
        const el = document.getElementById('addReminderError');
        el.hidden = !message;
        el.textContent = message || '';
    }

    /*
    * Add Reminder - submitted via fetch() instead of a plain form
    * POST, so success shows a toast (not the app-wide redirect-back
    * alert) and the yellow banner updates immediately, with no page
    * reload either way.
    */
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('addReminderForm');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            setAddReminderError('');

            const submitBtn = document.getElementById('addReminderSubmitBtn');
            submitBtn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    closeAddReminderModal();
                    checkReminderBanner();
                    loadLogs();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || 'Reminder added successfully.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                } else {
                    const message = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : (data.message || 'Unable to add reminder.');
                    setAddReminderError(message);
                }
            })
            .catch(err => {
                setAddReminderError('Unable to add reminder. Please try again.');
                console.error(err);
            })
            .finally(() => {
                submitBtn.disabled = false;
            });
        });
    });

    /*
    * List / View / Edit are one stack - only one of the three is
    * ever visible at a time. hideReminderOverlay() swaps which one
    * is on top without touching the page scroll-lock;
    * returnToRemindersList() is the single "go back" path used by
    * every exit (X, Cancel, a successful Save, a successful
    * Delete), so there's never a modal left open-but-hidden behind
    * another one - which was the actual cause of Delete appearing
    * to "go back" to a stale reminder modal.
    */
    function hideReminderOverlay(id)
    {
        document.getElementById(id)?.classList.remove('show');
    }

    function returnToRemindersList()
    {
        hideReminderOverlay('editReminderModal');

        document.getElementById('remindersModal').classList.add('show');
        document.body.style.overflow = 'hidden';

        loadReminders();

        // Keeps the yellow banner's count and the logs list in sync
        // the moment a reminder is edited or deleted, without a
        // page refresh.
        checkReminderBanner();
        loadLogs();
    }

    function openRemindersModal()
    {
        returnToRemindersList();
    }

    function closeRemindersModal()
    {
        document
            .getElementById('remindersModal')
            .classList.remove('show');

        document.body.style.overflow = '';
    }
    function formatReminderWhen(reminder)
    {
        const date = new Date(reminder.reminder_date);

        const formattedDate = date.toLocaleDateString('en-GB', {
            day: '2-digit', month: 'short', year: 'numeric',
        });

        const time = reminder.reminder_time ? reminder.reminder_time.substring(0, 5) : '';

        return time ? `${formattedDate}, ${time}` : formattedDate;
    }

    function canManageReminder(reminder)
    {
        return isAdmin || Number(reminder.created_by) === Number(currentUserId);
    }

    function findReminder(id)
    {
        return remindersCache.find(r => Number(r.id) === Number(id));
    }

    /*
    * Reminder details render directly in the list - same three
    * fields the old separate "View Reminder" modal used to show
    * (Date & Time / Created By / Note), laid out with the same
    * detail-row pattern used across the rest of this page. Nothing
    * here opens another modal; only Edit/Delete do.
    */
    function renderReminderItem(reminder)
    {
        const canManage = canManageReminder(reminder);
        const creatorName = escapeHtml(reminder.creator?.name ?? 'Unknown');

        const noteRow = reminder.note ? `
            <div class="detail-row">
                <i class="mdi mdi-note-text-outline row-icon"></i>
                <span class="label">Note</span>
                <span class="value notes-value">${escapeHtml(reminder.note)}</span>
            </div>
        ` : '';

        const actions = canManage ? `
            <div class="activity-actions">
                <button type="button" class="activity-action-btn" title="Edit" onclick="openEditReminderModal(${reminder.id})">
                    <i class="mdi mdi-pencil-box"></i>
                </button>
                <button type="button" class="activity-action-btn activity-action-danger" title="Delete" onclick="deleteReminder(${reminder.id})">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        ` : '';

        return `
            <div class="reminder-item" data-reminder-id="${reminder.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="reminder-details">
                        <div class="detail-row">
                            <i class="mdi mdi-calendar-clock row-icon"></i>
                            <span class="label">Date &amp; Time</span>
                            <span class="value">${formatReminderWhen(reminder)}</span>
                        </div>
                        <div class="detail-row">
                            <i class="mdi mdi-account-circle-outline row-icon"></i>
                            <span class="label">Created By</span>
                            <span class="value">${creatorName}</span>
                        </div>
                        ${noteRow}
                    </div>
                    ${actions}
                </div>
            </div>
        `;
    }


    /*
    * EDIT REMINDER - PUT /lead-reminders/{id}, same validation rules
    * as Add Reminder. Owner or Admin/Super Admin only - the button
    * that opens this is itself gated by canManageReminder(), and the
    * backend (LeadController@updateReminder) enforces the same rule
    * independently.
    */
    function openEditReminderModal(id)
    {
        const reminder = findReminder(id);
        if (!reminder) return;

        editingReminderId = id;

        document.getElementById('editReminderDate').value = reminder.reminder_date ? reminder.reminder_date.substring(0, 10) : '';
        document.getElementById('editReminderTime').value = reminder.reminder_time ? reminder.reminder_time.substring(0, 5) : '';
        document.getElementById('editReminderNote').value = reminder.note ?? '';
        setEditReminderError('');

        // Only one reminder overlay at a time - hide the List behind
        // Edit instead of leaving both open.
        hideReminderOverlay('remindersModal');

        document.getElementById('editReminderModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeEditReminderModal()
    {
        editingReminderId = null;
        hideReminderOverlay('editReminderModal');
        returnToRemindersList();
    }

    function setEditReminderError(message)
    {
        const el = document.getElementById('editReminderError');
        el.hidden = !message;
        el.textContent = message || '';
    }

    function saveEditedReminder()
    {
        const reminder_date = document.getElementById('editReminderDate').value;
        const reminder_time = document.getElementById('editReminderTime').value;
        const note = document.getElementById('editReminderNote').value;

        if (!reminder_date || !reminder_time) {
            setEditReminderError('Please select both a date and a time.');
            return;
        }

        setEditReminderError('');

        fetch(`/lead-reminders/${editingReminderId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ reminder_date, reminder_time, note }),
        })
        .then(res => res.json().then(data => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                closeEditReminderModal();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message || 'Reminder updated.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
            } else {
                const message = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'Unable to update reminder.');
                setEditReminderError(message);
            }
        })
        .catch(err => {
            setEditReminderError('Unable to update reminder. Please try again.');
            console.error(err);
        });
    }

    function loadReminders()
    {
        const loading = document.getElementById('remindersLoading');
        const list = document.getElementById('remindersList');

        loading.style.display = 'block';
        list.innerHTML = '';

        fetch('{{ route('leads.reminders', $lead) }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load reminders.');
            }

            return response.json();
        })
        .then(reminders => {

            loading.style.display = 'none';
            remindersCache = reminders;

            if (!reminders.length) {

                list.innerHTML = `
                    <div class="text-center py-5 text-muted">

                        <i
                            class="mdi mdi-bell-off-outline"
                            style="font-size:40px;"
                        ></i>

                        <div class="mt-2">
                            No reminders found for this lead.
                        </div>

                    </div>
                `;

                return;
            }

            list.innerHTML = reminders.map(renderReminderItem).join('');
        })
        .catch(error => {

            loading.style.display = 'none';

            list.innerHTML = `
                <div class="alert alert-danger">
                    Unable to load reminders.
                </div>
            `;

            console.error(error);
        });
    }


    /*
    * Always triggered straight from a row in the still-open List
    * modal now (there's no separate View step any more) - on
    * success it just refreshes the list in place.
    */
    function deleteReminder(reminderId)
    {
        Swal.fire({
            html: `
                <div class="swal-delete-icon">
                    <i class="mdi mdi-trash-can-outline"></i>
                </div>
                <h2 class="swal-delete-title">Delete this reminder?</h2>
                <p class="swal-delete-text">This action can't be undone.</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                popup: 'swal-leads-popup',
                confirmButton: 'swal-btn-danger',
                cancelButton: 'swal-btn-cancel',
            },
        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }

            fetch(`/lead-reminders/${reminderId}`, {

                method: 'DELETE',

                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }

            })
            .then(response => {

                if (!response.ok) {
                    throw new Error('Unable to delete reminder.');
                }

                return response.json();

            })
            .then(result => {

                if (result.success) {
                    editingReminderId = null;
                    returnToRemindersList();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Reminder deleted.',
                        showConfirmButton: false,
                        timer: 1800,
                        timerProgressBar: true,
                    });
                }

            })
            .catch(error => {

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Unable to delete reminder.',
                    showConfirmButton: false,
                    timer: 2400,
                    timerProgressBar: true,
                });

                console.error(error);

            });
        });
    }
</script>
@endsection
