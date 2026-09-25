@extends('layout')

@section('title', 'Lead Details')
@section('subtitle', 'View Lead Information')

@section('content')


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
        font-size: 14px;
        color: #020202;
        min-width: 180px;
        padding-top: 1px;
    }

    .detail-row .value {
        color: #020202;
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

    .status-assigned {
        background: #e7f1ff;
        color: #2264d1;
    }
    .status-assigned::before { background: #2264d1; }

    .status-inprogress {
        background: #e0f5f3;
        color: #0b7a6f;
    }
    .status-inprogress::before { background: #0f9d8f; }

    .status-review {
        background: #efe8fb;
        color: #6438c2;
    }
    .status-review::before { background: #7048c9; }

    .status-sentback {
        background: #fdeede;
        color: #b45f06;
    }
    .status-sentback::before { background: #e07b00; }

    .status-closed {
        background: #eceff3;
        color: #4b5563;
    }
    .status-closed::before { background: #6b7280; }

    .status-hold {
        background: #e6f4fb;
        color: #0a6c93;
    }
    .status-hold::before { background: #1394c4; }

    .status-lost {
        background: #fdeaea;
        color: #c62828;
    }
    .status-lost::before { background: #d33a3a; }

    /* Assigned Team card - current owner banner, team rows, workflow
       actions. Reuses the Assigned card's avatar look above. */
    .team-owner {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        margin-bottom: 14px;
        border: 1px solid #dfe6f5;
        border-radius: 10px;
        background: #f5f8ff;
    }

    .team-owner .assigned-avatar {
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e7f1ff;
        color: #2264d1;
        font-size: 19px;
    }

    .team-owner-eyebrow {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #8a92a3;
    }

    .team-owner-name {
        font-size: 14px;
        font-weight: 700;
        color: #1f2937;
        overflow-wrap: anywhere;
    }

    .team-role-chip {
        display: inline-block;
        margin-left: 6px;
        padding: 1px 8px;
        border-radius: 10px;
        background: #e7f1ff;
        color: #2264d1;
        font-size: 11px;
        font-weight: 600;
        vertical-align: 1px;
    }

    .team-list {
        margin-bottom: 14px;
    }

    .team-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 9px 0;
        border-bottom: 1px dashed #eceff4;
        font-size: 13px;
    }

    .team-row:last-child { border-bottom: 0; }

    .team-row .team-label {
        flex: 0 0 auto;
        color: #8a92a3;
        font-weight: 500;
    }

    .team-row .team-value {
        text-align: right;
        color: #1f2937;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .team-row .team-sub {
        display: block;
        margin-top: 1px;
        font-size: 11.5px;
        font-weight: 500;
        color: #8a92a3;
    }

    .team-row .team-value.is-muted { color: #9aa0ac; font-weight: 500; }

    .workflow-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 10px;
    }

    .ls2-btn-workflow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 10px 16px;
        font-size: 13.5px;
        font-weight: 600;
        border: 0;
        border-radius: 9px;
        color: #fff;
        background: #5b52e0;
        transition: background 0.12s ease, transform 0.12s ease;
    }

    .ls2-btn-workflow:hover { background: #4a43d1; color: #fff; transform: translateY(-1px); }
    .ls2-btn-workflow.is-success { background: #1a8f5a; }
    .ls2-btn-workflow.is-success:hover { background: #157a4c; }
    .ls2-btn-workflow.is-warning { background: #e07b00; }
    .ls2-btn-workflow.is-warning:hover { background: #c46c00; }
    .ls2-btn-workflow.is-status { background: #2264d1; }
    .ls2-btn-workflow.is-status:hover { background: #1b52ad; }
    .ls2-btn-workflow:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    /* Loading / success states, shared by every workflow button
       (card buttons, modal confirm buttons, Assign / Reassign). The
       button keeps its size and colour, gets a spinner + a "-ing..."
       label while the request runs, and a check on success. */
    .wf-action.is-busy:disabled { opacity: 1; cursor: progress; }
    .wf-action.is-success-state:disabled { opacity: 1; cursor: default; }

    .wf-spinner {
        display: inline-block;
        width: 15px;
        height: 15px;
        flex-shrink: 0;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: wfSpin 0.7s linear infinite;
    }

    /* Soft (light) buttons: Assign / Reassign, Cancel-style */
    .ls2-btn-soft-primary .wf-spinner {
        border-color: rgba(91, 82, 224, 0.25);
        border-top-color: #5b52e0;
    }

    .ls2-btn-workflow.is-success-state { background: #1a8f5a; }

    @keyframes wfSpin { to { transform: rotate(360deg); } }

    @media (prefers-reduced-motion: reduce) {
        .wf-spinner { animation-duration: 1.6s; }
    }

    /* Update Lead Status - the Account Manager's Hold / Lost / Close picker */
    .status-options {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 16px;
    }

    .status-option {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
        padding: 11px 14px;
        border: 1px solid #e2e5eb;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.12s ease, background 0.12s ease, box-shadow 0.12s ease;
    }

    .status-option:hover { background: #f9fafc; }

    .status-option input { position: absolute; opacity: 0; pointer-events: none; }

    .status-option:has(input:checked) {
        border-color: #5b52e0;
        background: #f6f5ff;
        box-shadow: 0 0 0 3px rgba(91, 82, 224, 0.12);
    }

    .status-option:has(input:focus-visible) { box-shadow: 0 0 0 3px rgba(91, 82, 224, 0.3); }

    .status-option:has(input:disabled) { opacity: 0.55; cursor: not-allowed; background: #f6f7fa; }

    .status-option-icon {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .status-option-icon.tone-hold { background: #e6f4fb; color: #0a6c93; }
    .status-option-icon.tone-lost { background: #fdeaea; color: #d33a3a; }
    .status-option-icon.tone-closed { background: #e2f5e9; color: #1a7a4c; }

    .status-option strong { display: block; font-size: 13.5px; color: #1f2937; }
    .status-option small { display: block; font-size: 12px; color: #8a92a3; line-height: 1.35; }

    .workflow-hint {
        margin: 0 0 10px;
        padding: 9px 12px;
        border-radius: 8px;
        background: #f6f7fa;
        font-size: 12.5px;
        color: #6c7280;
    }

    /* Workflow modals + history table */
    .workflow-modal-lead {
        margin: 0 0 14px;
        font-size: 13px;
        color: #6c7280;
    }

    .workflow-note-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-left: 6px;
        padding: 1px 9px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }
    .workflow-note-sent_back { background: #fdeede; color: #b45f06; }
    .workflow-note-closed { background: #e2f5e9; color: #1a7a4c; }
    .workflow-note-hold { background: #e6f4fb; color: #0a6c93; }
    .workflow-note-lost { background: #fdeaea; color: #c62828; }

    .history-table-wrap { overflow-x: auto; }

    .history-table {
        width: 100%;
        min-width: 640px;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    .history-table th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #8a92a3;
        background: #f9fafc;
        border-bottom: 1px solid #eceff4;
        white-space: nowrap;
    }

    .history-table td {
        padding: 10px 12px;
        vertical-align: top;
        border-bottom: 1px solid #f1f3f7;
        color: #1f2937;
    }

    .history-table tr:last-child td { border-bottom: 0; }
    .history-table .history-when { white-space: nowrap; }
    .history-table .history-role { display: block; font-size: 11px; color: #8a92a3; }
    .history-table .history-note { display: block; margin-top: 3px; font-size: 11.5px; color: #6c7280; font-style: italic; }
    .history-table .history-arrow { color: #9aa0ac; padding: 0 4px; }
    .history-current td { background: #f5f8ff; }

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

    /* Publishing is one-way - once published, the toggle becomes
       non-interactive rather than letting anyone try to flip it
       back to draft and hit the server-side rejection. */
    .status-toggle.is-readonly {
        opacity: 0.55;
        cursor: default;
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

    .ls2-btn-icon-danger:hover,
    .ls2-btn-icon-danger:focus,
    .ls2-btn-icon-danger:active {
        color: #d33a3a;
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

    /* Icon tooltip - dark bubble, positioned via JS off the
       trigger's own bounding rect (see script below). Same style
       used for the Users page's Product-info tooltip, reused here
       for the Edit/Delete action icons so tooltips look identical
       across the whole app. Fixed positioning + a high z-index
       keeps it fully visible above any card/section, never clipped
       by a scrolling or overflow:hidden container. */
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

    /* Assigned card - current AE + AE picker */
    .assigned-current {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        margin-bottom: 12px;
        border: 1px solid #eef0f3;
        border-radius: 10px;
        background: #f9fafc;
    }

    .assigned-current .assigned-avatar {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e7f1ff;
        color: #2264d1;
        font-size: 17px;
    }

    .assigned-current .assigned-name {
        font-size: 13.5px;
        font-weight: 600;
        color: #1f2937;
        line-height: 1.25;
    }

    .assigned-current .assigned-email,
    .assigned-current .assigned-empty {
        font-size: 12px;
        color: #8a92a3;
        margin: 0;
    }

    #assignAeSelect {
        width: 100%;
        height: 40px;
        padding: 0 12px;
        margin-bottom: 12px;
        border: 1px solid #dfe3ec;
        border-radius: 9px;
        background: #fff;
        font-size: 13px;
        color: #1f2937;
    }

    #assignAeSelect:focus {
        outline: none;
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.15);
    }

    #assignAeSelect:disabled {
        background: #f4f5f8;
        cursor: not-allowed;
    }

    #assignAeError {
        margin: -4px 0 10px;
        font-size: 12px;
        color: #c62828;
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

    /* Pricing History list rows - visually identical to .reminder-item
       above, but its own class rather than reusing that name, since
       this is a different feature (Pricing) than Reminders. */
    .pricing-history-item {
        border: 1px solid #eef0f3;
        border-radius: 10px;
        padding: 6px 15px;
        margin-bottom: 10px;
        background: #fff;
    }

    .pricing-history-item:last-child {
        margin-bottom: 0;
    }

    .pricing-history-item .pricing-history-details {
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
        font-size: 14px;
        color: #8a92a3;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .activity-user {
        font-weight: 600;
        color: #020202;
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
        color: #c8cbd4;
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
        overscroll-behavior: contain;
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
    .reminder-modal-body .form-select {
        box-sizing: border-box;
        height: 42px;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 13.5px;
        width: 100%;
        background-color: #fff;
    }

    .reminder-modal-body textarea.form-control {
        height: auto;
        padding: 10px 12px;
    }

    .reminder-modal-body .form-control:focus,
    .reminder-modal-body .form-select:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
        outline: none;
    }

    .reminder-modal-body .form-control:disabled {
        background-color: #f5f6f8;
        color: #7c8494;
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

    /* Soft-alert tones for non-destructive confirmations (Start
       Process, Reassign) - same popup, title, text and button shape as
       the delete confirmation above, only the icon / confirm colours
       change (matching .ls2-btn-soft-primary and the orange
       "Sent Back" accent used elsewhere on this page). */
    .swal-delete-icon.tone-primary { background: #eef0ff; color: #5b52e0; }
    .swal-delete-icon.tone-warning { background: #fdeede; color: #e07b00; }

    .swal-btn-primary,
    .swal-btn-warning {
        color: #fff !important;
        font-size: 13.5px !important;
        font-weight: 500 !important;
        padding: 9px 20px !important;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    .swal-btn-primary { background: #5b52e0 !important; }
    .swal-btn-primary:hover { background: #4a43d1 !important; }
    .swal-btn-warning { background: #e07b00 !important; }
    .swal-btn-warning:hover { background: #c46c00 !important; }

    /* ==========================================================
       Pricing modal - its own namespace (.pricing-modal-*), not
       .reminder-modal-* - Reminders/Notes/Logs already use that name
       for their own modals, and Pricing is a different feature, so it
       gets independent classes rather than reusing theirs. Visually
       matches that same rounded/purple look on purpose, built as its
       own copy so a future change to either one can't accidentally
       break the other.
       ========================================================== */

    .pricing-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(26, 31, 43, 0.45);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .pricing-modal-overlay.show {
        display: flex;
    }

    /* Fixed header + fixed footer, only the body scrolls: the box
       itself is a flex column capped at 90vh, header/footer are
       flex: 0 0 auto (never shrink, never scroll), and
       .pricing-modal-body is the one flexible item that grows to
       fill whatever's left and scrolls internally once content
       overflows it. The Add/Edit forms wrap body+footer in a <form>,
       so that also needs to be a flex column (.pricing-modal-box
       form below) for the same split to reach through it; the
       History modal has no <form>, so .pricing-modal-body there is
       a direct flex child of the box instead - the same rule covers
       both. */
    .pricing-modal-box {
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 12px 40px rgba(0,0,0,.18);
    }

    .pricing-modal-box.large {
        max-width: 700px;
    }

    .pricing-modal-box form {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
    }

    .pricing-modal-header {
        flex: 0 0 auto;
        background: #fff;
        color: #1a1f2b;
        padding: 22px 24px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #eef0f3;
    }

    .pricing-modal-header h5 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #1a1f2b;
    }

    .pricing-modal-header .btn-close {
        background: none;
        border: none;
        font-size: 20px;
        line-height: 1;
        color: #a4aab5;
        cursor: pointer;
        padding: 4px;
    }

    .pricing-modal-header .btn-close:hover {
        color: #384153;
    }

    .pricing-modal-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overscroll-behavior: contain;
        padding: 22px 24px;
    }

    .pricing-modal-body label {
        font-size: 13.5px;
        font-weight: 500;
        color: #384153;
    }

    .pricing-modal-body .form-control,
    .pricing-modal-body .form-select {
        box-sizing: border-box;
        height: 42px;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 13.5px;
        width: 100%;
        background-color: #fff;
    }

    .pricing-modal-body .form-control:focus,
    .pricing-modal-body .form-select:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
        outline: none;
    }

    .pricing-modal-body .form-control:disabled {
        background-color: #f5f6f8;
        color: #7c8494;
    }

    .pricing-modal-body .form-control.is-invalid,
    .pricing-modal-body .form-select.is-invalid,
    .pricing-modal-body .select2-selection--single.is-invalid {
        border-color: #d33a3a;
    }

    .pricing-modal-body .invalid-feedback {
        color: #d33a3a;
        font-size: 12px;
    }

    .pricing-modal-footer {
        flex: 0 0 auto;
        padding: 16px 24px 22px;
        border-top: 1px solid #eef0f3;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .pricing-modal-footer .btn-primary {
        background: #6c63ff;
        border-color: #6c63ff;
        border-radius: 9px;
        padding: 9px 22px;
        font-size: 13.5px;
        font-weight: 500;
    }

    .pricing-modal-footer .btn-primary:hover {
        background: #5b52e8;
        border-color: #5b52e8;
    }

    .pricing-modal-footer .btn-light {
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 13.5px;
        border: 1px solid #e2e5eb;
        background: #fff;
        color: #384153;
    }

    .pricing-modal-body .form-error {
        color: #d33a3a;
        font-size: 12.5px;
        margin-top: 4px;
    }

    /* Select2 reskinned to match the .form-control boxes above -
       same height, border, radius, purple focus ring - instead of
       the library's generic default look, so it reads as "one of
       these fields" rather than a different kind of control.
       Deliberately NOT display:flex on the outer box: Select2's own
       base CSS already makes .select2-selection__rendered a
       full-width block, which is what lets its clear ("x") button's
       float:right land at the true right edge of the box instead of
       immediately next to the selected text. */
    .pricing-modal-body .select2-container--default .select2-selection--single {
        box-sizing: border-box;
        height: 42px;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        padding: 0;
    }

    .pricing-modal-body .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
        padding-right: 34px;
        font-size: 13.5px;
        color: #1a1f2b;
    }

    .pricing-modal-body .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #a4aab5;
    }

    .pricing-modal-body .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }

    .pricing-modal-body .select2-container--default .select2-selection--single .select2-selection__clear {
        margin: 8px 30px 0px 0px;
        color: #a4aab5;
        font-size: 15px;
    }

    .pricing-modal-body .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: #384153;
    }

    .pricing-modal-body .select2-container--default.select2-container--open .select2-selection--single,
    .pricing-modal-body .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.14);
    }

    /* Select2's dropdown panel is appended to <body> (dropdownParent),
       as a SIBLING of our modal overlay - so without an explicit
       z-index above the overlay's 99999, the open results list renders
       BEHIND the modal and looks empty/invisible. Same root cause
       already fixed for SweetAlert2 above (.swal2-container). */
    .select2-dropdown {
        z-index: 100000;
    }

    /* Select2's results list scrolls internally (overflow-y: auto,
       from its own base CSS) - overscroll-behavior: contain stops the
       scroll from "chaining" through to whatever's scrollable behind
       it (the modal body, and beyond that the page) once you hit the
       top/bottom of the list, instead of continuing to scroll them. */
    .select2-results__options {
        overscroll-behavior: contain;
    }

    /* Annual Spend is the headline number this whole form exists to
       produce, so it gets its own full-width, visually distinct row
       instead of sitting in a plain input field like the rest. */
    .pricing-annual-spend-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #f0efff;
        border: 1px solid #d9d7f5;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 4px;
    }

    .pricing-annual-spend-summary .pricing-annual-spend-label {
        font-size: 13px;
        font-weight: 600;
        color: #384153;
    }

    .pricing-annual-spend-summary .pricing-annual-spend-label small {
        display: block;
        font-weight: 400;
        font-size: 11.5px;
        color: #7c8494;
        margin-top: 2px;
    }

    .pricing-annual-spend-summary .pricing-annual-spend-value {
        font-size: 22px;
        font-weight: 700;
        color: #6c63ff;
        white-space: nowrap;
    }

    .pricing-calc-breakdown {
        background: #f7f7fc;
        border: 1px solid #e9e8fb;
        border-radius: 10px;
        padding: 14px 16px;
        margin-top: 4px;
    }

    #pricingCalcBreakdown {
        margin-top: 10px;
    }

    /* display: flex (not inline-flex) so each toggle button always
       takes its own row and stacks under the one before it - as
       inline-flex, two of these next to each other (Show More / View
       Calculation) with nothing but a hidden, zero-height div between
       them would sit side by side on the same line instead. */
    .pricing-calc-toggle {
        background: none;
        border: none;
        color: #6c63ff;
        font-size: 12.5px;
        font-weight: 500;
        padding: 6px 0 0;
        cursor: pointer;
        display: flex;
        width: fit-content;
        align-items: center;
        gap: 5px;
    }

    .pricing-calc-toggle:hover {
        text-decoration: underline;
    }

    .pricing-calc-breakdown h6 {
        font-size: 12.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6c63ff;
        margin: 0 0 10px;
    }

    .pricing-calc-breakdown .pricing-calc-formula {
        font-size: 12px;
        color: #7c8494;
        margin-bottom: 10px;
        font-family: 'SFMono-Regular', Consolas, monospace;
        white-space: pre-wrap;
        line-height: 1.6;
    }

    .pricing-calc-breakdown .pricing-calc-line {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 13px;
        color: #384153;
        padding: 4px 0;
        font-family: 'SFMono-Regular', Consolas, monospace;
    }

    .pricing-calc-breakdown .pricing-calc-line.total {
        border-top: 1px dashed #d9d7f5;
        margin-top: 6px;
        padding-top: 8px;
        font-weight: 700;
        color: #1a1f2b;
        font-family: inherit;
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

@php
    // Badge colour per stored status (see Lead::STATUS_LABELS).
    $statusBadgeClasses = [
        'published' => 'status-complete',
        'assigned' => 'status-assigned',
        'in_progress' => 'status-inprogress',
        'with_account_manager' => 'status-review',
        'sent_back' => 'status-sentback',
        'hold' => 'status-hold',
        'lost' => 'status-lost',
        'closed' => 'status-closed',
    ];
@endphp

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
                                {{ $lead->company_business_name ?? $lead->customer_name ?? 'Lead #'.$lead->display_id }}
                            </h3>

                            <span
                                class="status-badge {{ $statusBadgeClasses[$lead->status] ?? 'status-progress' }}"
                                id="headerStatusBadge"
                            >{{ $lead->status_label }}</span>
                        </div>
                    </div>

                    {{-- Edit / Delete permissions --}}
                    @php
                        $user = Auth::user();

                        $isAdmin = $user->isAdminOrAbove();

                        // See LeadPolicy::update() - false for an Account
                        // Executive on a published lead, even one they
                        // created; otherwise matches who can view the lead.
                        $canEdit = $user->can('update', $lead);

                        // Publishing is one-way: once a lead is
                        // published (shown as "Open") or Assigned,
                        // nobody - not even Admin/Super Admin - can
                        // move it back to draft, so the toggle is only
                        // ever interactive while the lead is a draft.
                        $canToggleStatus = $canEdit && $lead->isDraft();

                        // Only admins can delete published leads.
                        // Normal users can delete draft leads.
                        $canDelete = $isAdmin || $lead->status === 'draft';

                        // Pricing: MIS User, Admin, Super Admin can add/
                        // edit/delete (see LeadPricingPolicy); everyone
                        // else who can view the lead sees it read-only.
                        $canManagePricing = $user->can('create', \App\Models\LeadPricing::class);

                        $leadLabel = $lead->company_business_name ?? $lead->customer_name ?? ('Lead #'.$lead->display_id);
                    @endphp

                    <div class="ls2-header-actions button-group">

                        {{-- Edit - icon only, same style/behavior as Delete --}}
                        @if($canEdit)
                            <a
                                href="{{ route('leads.edit', $lead) }}"
                                class="btn ls2-btn-icon-primary"
                                data-tooltip="Edit lead"
                            >
                                <i class="mdi mdi-pencil-box"></i>
                            </a>
                        @endif

                        {{-- Delete - same rule as the row-level delete button on
                             index.blade.php (Admin/Super Admin, or a draft lead).
                             Always rendered (not conditionally) so the inline status
                             toggle in Lead Overview can show/hide it live when
                             the status changes, the same way index.blade.php's
                             table re-renders its own delete button on every
                             status update - no page reload needed either way. --}}
                        <button
                            type="button"
                            class="btn ls2-btn-icon-danger"
                            id="deleteLeadBtn"
                            data-id="{{ $lead->id }}"
                            data-label="{{ $leadLabel }}"
                            data-tooltip="Delete lead"
                            @unless($canDelete) style="display:none;" @endunless
                        >
                            <i class="mdi mdi-delete"></i>
                        </button>

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
                                    <i class="mdi mdi-account row-icon"></i>
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
                @if($lead->isAuSavers())
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

                {{-- Pricing (AU Savers only). MIS User, Admin and Super
                     Admin can add/edit/delete (see LeadPricingPolicy);
                     everyone else who can view the lead sees this
                     read-only. A lead can have many pricing records -
                     the most recent one is shown here as "current",
                     older ones remain available via Pricing History. --}}
                @if($lead->isAuSavers())
                    @php $pricing = $lead->currentPricing; @endphp
                    <div class="card custom-card mb-4">

                        <div class="card-header custom-header collapsible-header" data-default-open="true" onclick="toggleCard(this)">
                            <div class="head-left">
                                <div class="icon-chip"><i class="mdi mdi-currency-gbp"></i></div>
                                <span>Pricing</span>
                            </div>
                            <i class="mdi mdi-chevron-down collapse-icon"></i>
                        </div>

                        <div class="collapsible-body">
                            <div class="collapsible-inner">
                                <div class="card-body">

                                    <div id="pricingEmptyState" class="{{ $pricing ? 'd-none' : '' }}">
                                        <p class="text-muted mb-3">No pricing has been added for this lead yet.</p>
                                    </div>

                                    <div id="pricingCurrent" class="{{ $pricing ? '' : 'd-none' }}">

                                        <div class="detail-row">
                                            <i class="mdi mdi-domain row-icon"></i>
                                            <span class="label">Supplier:</span>
                                            <span class="value" id="pricingSupplier">{{ $pricing?->supplier?->name ?? '-' }}</span>
                                        </div>

                                        <div class="detail-row">
                                            <i class="mdi mdi-flag row-icon"></i>
                                            <span class="label">Status:</span>
                                            <span class="value">
                                                <span
                                                    id="pricingStatusBadge"
                                                    class="status-badge {{ $pricing?->status === 'published' ? 'status-complete' : 'status-progress' }}"
                                                >{{ ucfirst($pricing?->status ?? 'draft') }}</span>
                                            </span>
                                        </div>

                                        <div class="detail-row">
                                            <i class="mdi mdi-flash-outline row-icon"></i>
                                            <span class="label">Rate Type:</span>
                                            <span class="value" id="pricingRateType">{{ $pricing?->rate_type === 'multi' ? 'Multi-Rate (Day/Evening/Night)' : 'Single-Rate' }}</span>
                                        </div>

                                        <div class="detail-row">
                                            <i class="mdi mdi-calendar-range-outline row-icon"></i>
                                            <span class="label">Contract Term:</span>
                                            <span class="value" id="pricingContractTerm">{{ $pricing?->contract_term_months ? $pricing->contract_term_months.' months' : '-' }}</span>
                                        </div>

                                        <div class="detail-row">
                                            <i class="mdi mdi-lightning-bolt-outline row-icon"></i>
                                            <span class="label">Total EAC:</span>
                                            <span class="value" id="pricingEac">{{ $pricing ? number_format((float) $pricing->total_eac_kwh, 2).' kWh' : '-' }}</span>
                                        </div>

                                        <div class="detail-row">
                                            <i class="mdi mdi-cash-multiple row-icon"></i>
                                            <span class="label">Annual Spend:</span>
                                            <span class="value" id="pricingAnnualSpend"><strong>{{ $pricing ? '£'.number_format((float) $pricing->annual_spend, 2) : '-' }}</strong></span>
                                        </div>

                                        {{-- Sits right after Annual Spend so the extra fields it
                                             reveals (consumption, rates, SC, uplift) appear as a
                                             continuation of the field list above - not sandwiched
                                             between it and the toggle buttons below, which stay
                                             put after everything regardless of expand state. --}}
                                        <div class="d-none" id="pricingSummaryMore"></div>

                                        <button type="button" class="pricing-calc-toggle" id="pricingShowMoreToggle" onclick="togglePricingSummaryMore()">
                                            <i class="mdi mdi-chevron-down" id="pricingShowMoreIcon"></i>
                                            <span id="pricingShowMoreLabel">Show More</span>
                                        </button>

                                        <button type="button" class="pricing-calc-toggle" id="pricingCalcToggle" onclick="togglePricingCalcBreakdown()">
                                            <i class="mdi mdi-calculator-variant-outline"></i>
                                            View Calculation
                                        </button>

                                        <div class="pricing-calc-breakdown d-none" id="pricingCalcBreakdown"></div>

                                    </div>

                                    <div class="ls2-reminders-actions mt-3">
                                        @if($canManagePricing)
                                            <button type="button" class="ls2-btn-soft-primary" onclick="openAddPricingModal()">
                                                <i class="mdi mdi-plus"></i>
                                                Add Pricing
                                            </button>
                                            <button
                                                type="button"
                                                class="ls2-btn-outline"
                                                id="editPricingDraftBtn"
                                                style="{{ $pricing && $pricing->status === 'draft' ? '' : 'display:none;' }}"
                                                onclick="openEditPricingModal({{ $pricing?->id }})"
                                            >
                                                <i class="mdi mdi-pencil-box"></i>
                                                Edit Draft
                                            </button>
                                        @endif
                                        <button type="button" class="ls2-btn-outline" onclick="openPricingHistoryModal()">
                                            <i class="mdi mdi-history"></i>
                                            Pricing History
                                        </button>
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
                            <span class="value" id="overviewStatusValue">
                                @if($canToggleStatus)
                                    <label class="status-toggle" data-id="{{ $lead->id }}">
                                        <input
                                            type="checkbox"
                                            class="status-toggle-input"
                                            id="leadStatusToggle"
                                            data-id="{{ $lead->id }}"
                                            {{ $lead->status === 'published' ? 'checked' : '' }}
                                        >
                                        <span class="toggle-track"></span>
                                        <span class="toggle-label">{{ $lead->status === 'published' ? 'Open' : 'Draft' }}</span>
                                    </label>
                                @else
                                    <span class="status-badge {{ $statusBadgeClasses[$lead->status] ?? 'status-progress' }}">{{ $lead->status_label }}</span>
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
                            <span class="value">#{{ $lead->display_id }}</span>
                        </div>

                        @if ($lead->base_lead_id)
                            <div class="detail-row">
                                <i class="mdi mdi-domain row-icon"></i>
                                <span class="label">Multisite Batch</span>
                                <span class="value">
                                    Base #{{ $lead->base_lead_id }} - Site {{ $lead->site_sequence }} of {{ $lead->siblingSites()->count() }}
                                </span>
                            </div>
                        @elseif ($lead->isPendingMultisite())
                            <div class="detail-row">
                                <i class="mdi mdi-domain row-icon"></i>
                                <span class="label">Multisite Batch</span>
                                <span class="value">
                                    Pending - {{ $lead->sites_count }} site leads will be created when this lead is published.
                                </span>
                            </div>
                        @endif

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

                {{-- Assigned Team - who is on this lead (MIS / AE / Account
                     Manager), who holds it right now, and the workflow
                     actions available to the current user. Visible to
                     Admin / Super Admin / MIS and to the AE / Account
                     Manager on the lead. Every action reloads the page
                     so this card, the badges and the history stay in step. --}}
                @if($canSeeWorkflow)
                @php
                    $when = fn ($date) => $date ? $date->format('d M Y, h:i A') : null;
                    $owner = $lead->assignee;
                    $ownerRole = $owner?->role?->name;
                    $aeOnLead = $lead->accountExecutive ?? ($lead->isWithAe() ? $owner : null);
                @endphp
                <div class="card custom-card mb-4" id="workflowCard">

                    <div class="custom-header">
                        <div class="head-left">
                            <div class="icon-chip"><i class="mdi mdi-account-group-outline"></i></div>
                            <span>Assigned Team</span>
                        </div>
                    </div>

                    <div class="ls2-reminders-body">

                        {{-- Current owner - the one person responsible right now --}}
                        <div class="team-owner">
                            <div class="assigned-avatar"><i class="mdi mdi-account-star-outline"></i></div>
                            <div>
                                <span class="team-owner-eyebrow">Current Owner</span>
                                @if($owner)
                                    <span class="team-owner-name">{{ $owner->name }}</span>
                                    @if($ownerRole)<span class="team-role-chip">{{ $ownerRole }}</span>@endif
                                @else
                                    <span class="team-owner-name">Not assigned yet</span>
                                @endif
                            </div>
                        </div>

                        <div class="team-list">

                            <div class="team-row">
                                <span class="team-label">Status</span>
                                <span class="team-value">
                                    <span class="status-badge {{ $statusBadgeClasses[$lead->status] ?? 'status-progress' }}">{{ $lead->status_label }}</span>
                                </span>
                            </div>

                            <div class="team-row">
                                <span class="team-label">MIS</span>
                                @if($lead->assigner)
                                    <span class="team-value">
                                        {{ $lead->assigner->name }}
                                        @if($lead->assigner->role)<span class="team-sub">{{ $lead->assigner->role->name }}</span>@endif
                                    </span>
                                @else
                                    <span class="team-value is-muted">-</span>
                                @endif
                            </div>

                            <div class="team-row">
                                <span class="team-label">AE</span>
                                @if($aeOnLead)
                                    <span class="team-value">
                                        {{ $aeOnLead->name }}
                                        @if($lead->ae_assigned_at)<span class="team-sub">Assigned {{ $when($lead->ae_assigned_at) }}</span>@endif
                                    </span>
                                @else
                                    <span class="team-value is-muted">Not assigned</span>
                                @endif
                            </div>

                            <div class="team-row">
                                <span class="team-label">Account Manager</span>
                                @if($lead->accountManager)
                                    <span class="team-value">
                                        {{ $lead->accountManager->name }}
                                        @if($lead->am_assigned_at)<span class="team-sub">Assigned {{ $when($lead->am_assigned_at) }}</span>@endif
                                    </span>
                                @else
                                    <span class="team-value is-muted">Not assigned</span>
                                @endif
                            </div>

                            @if($lead->process_started_at)
                                <div class="team-row">
                                    <span class="team-label">Process Started</span>
                                    <span class="team-value">
                                        {{ $when($lead->process_started_at) }}
                                        @if($lead->processStarter)<span class="team-sub">by {{ $lead->processStarter->name }}</span>@endif
                                    </span>
                                </div>
                            @endif

                            {{-- Who moved it to its current Hold / Lost / Closed state, and when --}}
                            @php
                                $endRow = match ($lead->status) {
                                    'hold' => ['by' => 'Put On Hold By', 'on' => 'On Hold Since', 'user' => $lead->holder, 'at' => $lead->hold_at],
                                    'lost' => ['by' => 'Marked Lost By', 'on' => 'Marked Lost On', 'user' => $lead->lostBy, 'at' => $lead->lost_at],
                                    'closed' => ['by' => 'Closed By', 'on' => 'Closed On', 'user' => $lead->closer, 'at' => $lead->closed_at],
                                    default => null,
                                };
                            @endphp
                            @if($endRow && $endRow['at'])
                                <div class="team-row">
                                    <span class="team-label">{{ $endRow['by'] }}</span>
                                    <span class="team-value">
                                        {{ $endRow['user']?->name ?? '-' }}
                                        @if($endRow['user']?->role)<span class="team-sub">{{ $endRow['user']->role->name }}</span>@endif
                                    </span>
                                </div>
                                <div class="team-row">
                                    <span class="team-label">{{ $endRow['on'] }}</span>
                                    <span class="team-value">{{ $when($endRow['at']) }}</span>
                                </div>
                            @endif

                        </div>

                        {{-- Workflow actions - only the ones this user may take now --}}
                        @if($canStartProcess || $canMoveToAm || $canSendBack || $canUpdateStatus)
                            <div class="workflow-actions">
                                @if($canStartProcess)
                                    <button type="button" class="ls2-btn-workflow wf-action" id="startProcessBtn" onclick="startProcess()">
                                        <i class="mdi mdi-play-circle-outline"></i> Start Process
                                    </button>
                                @endif

                                @if($canMoveToAm)
                                    <button type="button" class="ls2-btn-workflow wf-action" onclick="openWorkflowModal('moveToAmModal')">
                                        <i class="mdi mdi-account-arrow-right-outline"></i> Move to Account Manager
                                    </button>
                                @endif

                                @if($canUpdateStatus)
                                    <button type="button" class="ls2-btn-workflow is-status wf-action" id="updateStatusBtn" onclick="openUpdateStatusModal()">
                                        <i class="mdi mdi-swap-vertical-circle-outline"></i> Update Lead Status
                                    </button>
                                @endif

                                @if($canSendBack)
                                    <button type="button" class="ls2-btn-workflow is-warning wf-action" onclick="openWorkflowModal('sendBackModal')">
                                        <i class="mdi mdi-undo-variant"></i> Send Back to AE
                                    </button>
                                @endif
                            </div>
                        @elseif(Auth::user()->isAe() && $lead->isOnHold())
                            <p class="workflow-hint"><i class="mdi mdi-pause-circle-outline"></i> This lead is on hold with the Account Manager.</p>
                        @elseif(Auth::user()->isAe() && $lead->isWithAccountManager())
                            <p class="workflow-hint"><i class="mdi mdi-timer-sand"></i> Waiting for the Account Manager to review this lead.</p>
                        @elseif(Auth::user()->isManager() && $lead->isWithAe())
                            <p class="workflow-hint"><i class="mdi mdi-timer-sand"></i> This lead is back with the Account Executive.</p>
                        @elseif($lead->isFinished())
                            <p class="workflow-hint"><i class="mdi mdi-check-circle-outline"></i> This lead has been {{ $lead->isLost() ? 'marked as lost' : 'closed' }}.</p>
                        @endif

                        <div class="ls2-reminders-actions">
                            <button type="button" class="ls2-btn-outline" onclick="openWorkflowModal('assignmentHistoryModal')">
                                <i class="mdi mdi-history"></i>
                                Assignment History ({{ $assignmentHistory->count() }})
                            </button>
                        </div>

                    </div>
                </div>
                @endif

                {{-- Assign Account Executive - Admin / Super Admin / MIS only.
                     This is the only place a lead is assigned or reassigned
                     *to an AE* (the AE -> Account Manager hand-over and the
                     Account Manager's send-back have their own buttons in the
                     Assigned Team card above). Works like the Reminders card
                     (quick-action card + fetch() to a JSON endpoint + toast). --}}
                @if($canSeeAssignment)
                @php
                    $holder = $lead->assignee;
                    $holderRole = $holder?->role?->name;
                    $holdsAsAe = $lead->isWithAe();
                    $assignLabel = match (true) {
                        $lead->isWithAccountManager() => 'Take Back & Reassign to AE',
                        $holdsAsAe => 'Reassign to AE',
                        default => 'Assign to AE',
                    };
                @endphp
                <div class="card custom-card mb-4" id="assignedCard">

                    <div class="custom-header">
                        <div class="head-left">
                            <div class="icon-chip"><i class="mdi mdi-account-check-outline"></i></div>
                            <span>Assign Account Executive</span>
                        </div>
                    </div>

                    <div class="ls2-reminders-body">

                        <div class="assigned-current" id="assignedCurrent">
                            <div class="assigned-avatar"><i class="mdi mdi-account"></i></div>
                            <div>
                                @if($holder)
                                    <div class="assigned-name">{{ $holder->name }}@if($holderRole)<span class="team-role-chip">{{ $holderRole }}</span>@endif</div>
                                    <p class="assigned-email">{{ $holder->email }} &middot; {{ $lead->isFinished() ? 'was the last owner' : 'holds it now' }}</p>
                                @else
                                    <div class="assigned-name">Not assigned yet</div>
                                    <p class="assigned-empty">No Account Executive has this lead.</p>
                                @endif
                            </div>
                        </div>

                        @if($canAssign)
                            @if($aeUsers->isNotEmpty())
                                <select id="assignAeSelect" aria-label="Select Account Executive"
                                        data-holder="{{ $holder?->name }}" data-holder-role="{{ $holderRole }}">
                                    <option value="">Select Account Executive</option>
                                    @foreach($aeUsers as $ae)
                                        <option value="{{ $ae->id }}" @selected($holdsAsAe && (int) $lead->assigned_to === $ae->id)>
                                            {{ $ae->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <div id="assignAeError" hidden></div>

                                <div class="ls2-reminders-actions">
                                    <button
                                        type="button"
                                        class="ls2-btn-soft-primary wf-action"
                                        id="assignAeBtn"
                                        onclick="assignLeadToAe()"
                                    >
                                        <i class="mdi mdi-account-arrow-right"></i>
                                        <span id="assignAeBtnText">{{ $assignLabel }}</span>
                                    </button>
                                </div>

                                @if($holder)
                                    <p class="workflow-hint mt-2 mb-0">
                                        Reassigning hands this lead from <strong>{{ $holder->name }}</strong> to the AE you pick,
                                        resets it to <strong>Assigned</strong> and notifies both of them. Every reassignment is kept in the Assignment History.
                                    </p>
                                @endif
                            @else
                                <p class="mb-0">No active Account Executives have access to this lead's product.</p>
                            @endif
                        @elseif($lead->isFinished())
                            <p class="mb-0">A {{ $lead->isLost() ? 'lost' : 'closed' }} lead cannot be reassigned.</p>
                        @elseif($lead->isWithAccountManager())
                            <p class="mb-0">
                                This lead is with Account Manager <strong>{{ $holder?->name }}</strong>. They can send it back to the AE or close it -
                                only an Admin can take it back.
                            </p>
                        @else
                            <p class="mb-0">Only an Open lead can be assigned - publish this lead first.</p>
                        @endif

                    </div>
                </div>
                @endif

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

@if($canSeeWorkflow)
<!-- Assignment History Modal - the full MIS -> AE -> Account Manager trail,
     oldest first, straight from lead_assignments. -->
<div id="assignmentHistoryModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box large" style="max-width: 900px;">

        <div class="reminder-modal-header">
            <h5>Assignment History</h5>
            <button type="button" class="btn-close" onclick="closeWorkflowModal('assignmentHistoryModal')">&times;</button>
        </div>

        <div class="history-table-wrap">
            @if($assignmentHistory->isEmpty())
                <div class="logs-empty" style="color:#8a92a3;padding:24px;text-align:center;">No assignments yet.</div>
            @else
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date &amp; Time</th>
                            <th>Action</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                            <th>Performed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignmentHistory as $entry)
                            <tr @class(['history-current' => $loop->last])>
                                <td class="history-when">{{ $entry->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <strong>{{ $entry->action_label }}</strong>
                                    @if($entry->note)<span class="history-note">"{{ $entry->note }}"</span>@endif
                                </td>
                                <td>
                                    @if($entry->from_user_name)
                                        {{ $entry->from_user_name }}
                                        @if($entry->from_role)<span class="history-role">{{ $entry->from_role }}</span>@endif
                                    @else - @endif
                                </td>
                                <td>
                                    @if($entry->to_user_name)
                                        {{ $entry->to_user_name }}
                                        @if($entry->to_role)<span class="history-role">{{ $entry->to_role }}</span>@endif
                                    @else - @endif
                                </td>
                                <td>
                                    {{ \App\Models\Lead::statusLabel($entry->from_status) }}
                                    <span class="history-arrow">&rarr;</span>
                                    {{ \App\Models\Lead::statusLabel($entry->to_status) }}
                                </td>
                                <td>
                                    {{ $entry->performed_by_name ?? '-' }}
                                    @if($entry->performed_by_role)<span class="history-role">{{ $entry->performed_by_role }}</span>@endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</div>
@endif

@if($canMoveToAm)
<!-- Move to Account Manager - the AE picks who the lead goes to. -->
<div id="moveToAmModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box">

        <div class="reminder-modal-header">
            <h5>Select Account Manager</h5>
            <button type="button" class="btn-close" onclick="closeWorkflowModal('moveToAmModal')">&times;</button>
        </div>

        <div class="reminder-modal-body">
            <p class="workflow-modal-lead">Choose the Account Manager who should review Lead #{{ $lead->display_id }}.</p>

            @if($accountManagers->isNotEmpty())
                <select id="moveToAmSelect" class="form-select" aria-label="Select Account Manager">
                    <option value="">Select Account Manager</option>
                    @foreach($accountManagers as $manager)
                        <option value="{{ $manager->id }}" @selected((int) $lead->account_manager_id === $manager->id)>{{ $manager->name }}</option>
                    @endforeach
                </select>
            @else
                <p class="mb-0">There are no active Account Managers to move this lead to.</p>
            @endif

            <div class="form-error" id="moveToAmError" hidden></div>
        </div>

        <div class="reminder-modal-footer">
            <button type="button" class="btn btn-light wf-action" onclick="closeWorkflowModal('moveToAmModal')">Cancel</button>
            @if($accountManagers->isNotEmpty())
                <button type="button" class="btn btn-primary wf-action" id="moveToAmBtn" onclick="moveToAccountManager()">Move to Account Manager</button>
            @endif
        </div>

    </div>
</div>
@endif

@if($canSendBack)
<!-- Send Back to AE -->
<div id="sendBackModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box">

        <div class="reminder-modal-header">
            <h5>Send Back to AE</h5>
            <button type="button" class="btn-close" onclick="closeWorkflowModal('sendBackModal')">&times;</button>
        </div>

        <div class="reminder-modal-body">
            <p class="workflow-modal-lead">
                Lead #{{ $lead->display_id }} will go back to
                <strong>{{ $lead->accountExecutive?->name ?? 'the Account Executive' }}</strong> for further work.
            </p>

            <label class="form-label" for="sendBackNote">Note (optional) - saved to Notes &amp; Documents</label>
            <textarea id="sendBackNote" class="form-control" rows="3" maxlength="1000" placeholder="What needs more work?"></textarea>

            <div class="form-error" id="sendBackError" hidden></div>
        </div>

        <div class="reminder-modal-footer">
            <button type="button" class="btn btn-light wf-action" onclick="closeWorkflowModal('sendBackModal')">Cancel</button>
            <button type="button" class="btn btn-primary wf-action" id="sendBackBtn" onclick="sendBackToAe()">Send Back to AE</button>
        </div>

    </div>
</div>
@endif

@if($canUpdateStatus)
<!-- Update Lead Status - the Account Manager's one control for Hold / Lost / Close. -->
<div id="updateStatusModal" class="reminder-modal-overlay">
    <div class="reminder-modal-box">

        <div class="reminder-modal-header">
            <h5>Update Lead Status</h5>
            <button type="button" class="btn-close" onclick="closeWorkflowModal('updateStatusModal')">&times;</button>
        </div>

        <div class="reminder-modal-body">
            <p class="workflow-modal-lead">Choose the new status for Lead #{{ $lead->display_id }}. You stay the lead's owner.</p>

            <div class="status-options" role="radiogroup" aria-label="New lead status">
                <label class="status-option">
                    <input type="radio" name="amStatus" value="hold" onchange="onUpdateStatusChoice()" @disabled($lead->isOnHold())>
                    <span class="status-option-icon tone-hold"><i class="mdi mdi-pause-circle-outline"></i></span>
                    <span>
                        <strong>Hold</strong>
                        <small>{{ $lead->isOnHold() ? 'This lead is already on hold.' : 'Pause the lead - it stays with you.' }}</small>
                    </span>
                </label>

                <label class="status-option">
                    <input type="radio" name="amStatus" value="lost" onchange="onUpdateStatusChoice()">
                    <span class="status-option-icon tone-lost"><i class="mdi mdi-close-circle-outline"></i></span>
                    <span>
                        <strong>Lost</strong>
                        <small>The customer will not proceed. A reason is required.</small>
                    </span>
                </label>

                <label class="status-option">
                    <input type="radio" name="amStatus" value="closed" onchange="onUpdateStatusChoice()">
                    <span class="status-option-icon tone-closed"><i class="mdi mdi-check-circle-outline"></i></span>
                    <span>
                        <strong>Close</strong>
                        <small>The process is complete.</small>
                    </span>
                </label>
            </div>

            <label class="form-label" for="updateStatusNote" id="updateStatusNoteLabel">Note (optional) - saved to Notes &amp; Documents</label>
            <textarea id="updateStatusNote" class="form-control" rows="3" maxlength="1000"></textarea>

            <div class="form-error" id="updateStatusError" hidden></div>
        </div>

        <div class="reminder-modal-footer">
            <button type="button" class="btn btn-light wf-action" onclick="closeWorkflowModal('updateStatusModal')">Cancel</button>
            <button type="button" class="btn btn-primary wf-action" id="updateStatusConfirmBtn" onclick="submitUpdateStatus()" disabled>Select a status</button>
        </div>

    </div>
</div>
@endif

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

@if($lead->isAuSavers() && $canManagePricing)
<!-- Add Pricing Modal -->
<div id="addPricingModal" class="pricing-modal-overlay">
    <div class="pricing-modal-box large">

        <div class="pricing-modal-header">
            <h5>Add Pricing</h5>
            <button type="button" class="btn-close" onclick="closeAddPricingModal()">&times;</button>
        </div>

        <form id="addPricingForm">
            <div class="pricing-modal-body">
                @include('leads.partials.pricing-form-fields', ['prefix' => 'addPricing'])
                <div class="form-error" id="addPricingError" hidden></div>
            </div>

            <div class="pricing-modal-footer">
                <button type="button" class="btn btn-light" onclick="closeAddPricingModal()">Cancel</button>
                <button type="button" class="btn btn-light" id="addPricingDraftBtn" onclick="submitPricingForm('addPricing', 'draft')">Save as Draft</button>
                <button type="button" class="btn btn-primary" id="addPricingPublishBtn" onclick="submitPricingForm('addPricing', 'published')">Publish</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Pricing (Draft) Modal -->
<div id="editPricingModal" class="pricing-modal-overlay">
    <div class="pricing-modal-box large">

        <div class="pricing-modal-header">
            <h5>Edit Pricing Draft</h5>
            <button type="button" class="btn-close" onclick="closeEditPricingModal()">&times;</button>
        </div>

        <form id="editPricingForm">
            <div class="pricing-modal-body">
                @include('leads.partials.pricing-form-fields', ['prefix' => 'editPricing'])
                <div class="form-error" id="editPricingError" hidden></div>
            </div>

            <div class="pricing-modal-footer">
                <button type="button" class="btn btn-light" onclick="closeEditPricingModal()">Cancel</button>
                <button type="button" class="btn btn-light" id="editPricingDraftBtn2" onclick="submitPricingForm('editPricing', 'draft')">Save as Draft</button>
                <button type="button" class="btn btn-primary" id="editPricingPublishBtn" onclick="submitPricingForm('editPricing', 'published')">Publish</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($lead->isAuSavers())
<!-- Pricing History Modal -->
<div id="pricingHistoryModal" class="pricing-modal-overlay">
    <div class="pricing-modal-box large">

        <div class="pricing-modal-header">
            <h5>Pricing History</h5>
            <button type="button" class="btn-close" onclick="closePricingHistoryModal()">&times;</button>
        </div>

        <div class="pricing-modal-body">

            <div id="pricingHistoryLoading" class="text-center py-4">
                <div class="spinner-border"></div>
                <div class="mt-2 text-muted">Loading pricing history...</div>
            </div>

            <div id="pricingHistoryList"></div>

        </div>

    </div>
</div>

<!-- Pricing View Modal - full field detail for one history record -->
<div id="pricingViewModal" class="pricing-modal-overlay">
    <div class="pricing-modal-box large">

        <div class="pricing-modal-header">
            <h5>Pricing Details</h5>
            <button type="button" class="btn-close" onclick="closePricingViewModal()">&times;</button>
        </div>

        <div class="pricing-modal-body" id="pricingViewModalBody"></div>

    </div>
</div>
@endif

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

    /*
    * Icon tooltip - same [data-tooltip] driven, position:fixed
    * bubble used on the Leads/Users/Roles listing pages (originally
    * built for the Users page's Products field-info icon), reused
    * here for the header Edit/Delete icons and the Notes/Documents/
    * Reminders Edit/Delete icons so every tooltip in the app looks
    * and behaves identically. Fixed positioning + a high z-index
    * (see .field-info-tooltip) keeps it fully visible even though a
    * trigger can sit inside a card or a scrolling container, and
    * :focus covers tap-to-show on touch devices for focusable
    * triggers (buttons/links).
    */
    let fieldTooltipEl = null;

    function showFieldTooltip(icon)
    {
        hideFieldTooltip();

        const text = icon.getAttribute('data-tooltip');

        if (!text) {
            return;
        }

        fieldTooltipEl = document.createElement('div');
        fieldTooltipEl.className = 'field-info-tooltip';
        fieldTooltipEl.textContent = text;
        document.body.appendChild(fieldTooltipEl);

        const iconRect = icon.getBoundingClientRect();
        const tipRect = fieldTooltipEl.getBoundingClientRect();

        let top = iconRect.top - tipRect.height - 8;

        if (top < 8) {
            top = iconRect.bottom + 8;
        }

        let left = iconRect.left + (iconRect.width / 2) - (tipRect.width / 2);
        left = Math.max(8, Math.min(left, window.innerWidth - tipRect.width - 8));

        fieldTooltipEl.style.top = top + 'px';
        fieldTooltipEl.style.left = left + 'px';
    }

    function hideFieldTooltip()
    {
        if (fieldTooltipEl) {
            fieldTooltipEl.remove();
            fieldTooltipEl = null;
        }
    }

    document.addEventListener('mouseover', function (e) {
        const icon = e.target.closest('[data-tooltip]');
        if (icon) showFieldTooltip(icon);
    });

    document.addEventListener('mouseout', function (e) {
        const icon = e.target.closest('[data-tooltip]');
        if (icon) hideFieldTooltip();
    });

    document.addEventListener('focus', function (e) {
        const icon = e.target.closest && e.target.closest('[data-tooltip]');
        if (icon) showFieldTooltip(icon);
    }, true);

    document.addEventListener('blur', function (e) {
        const icon = e.target.closest && e.target.closest('[data-tooltip]');
        if (icon) hideFieldTooltip();
    }, true);

    window.addEventListener('resize', hideFieldTooltip);
    document.addEventListener('scroll', hideFieldTooltip, true);

    const leadId = {{ $lead->id }};
    const currentUserId = {{ Auth::id() }};
    // Admin / Super Admin get full manage access to notes, documents
    // and reminders regardless of who created them - mirrors the
    // exact backend check in LeadActivityController/LeadController,
    // so the UI never offers an action the server would then reject.
    const isAdmin = @js($isAdmin);
    const canManagePricing = @js($canManagePricing ?? false);
    let editingActivityId = null;
    let editingReminderId = null;
    let feedItems = [];
    let remindersCache = [];
    let pricingHistoryCache = [];

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
    * ASSIGNED - assign / reassign this lead to an Account
    * Executive (POST /leads/{lead}/assign). Same fetch() + toast
    * pattern as Reminders; on success the page reloads (after a
    * short toast) so the team card, badges and history refresh.
    * ============================================================
    */
    function assignLeadToAe()
    {
        const select = document.getElementById('assignAeSelect');
        const errorBox = document.getElementById('assignAeError');

        errorBox.hidden = true;

        if (!select.value) {
            errorBox.textContent = 'Please select an Account Executive.';
            errorBox.hidden = false;
            return;
        }

        // Someone already holds it - make the consequence explicit first.
        const holder = select.dataset.holder;

        if (!holder) {
            submitAssignment();
            return;
        }

        const fromLabel = holder + (select.dataset.holderRole ? ' (' + select.dataset.holderRole + ')' : '');

        softConfirm({
            icon: 'mdi-account-switch-outline',
            tone: 'warning',
            title: 'Reassign this lead?',
            textHtml: `It will move from <strong>${escapeHtml(fromLabel)}</strong> to
                <strong>${escapeHtml(select.options[select.selectedIndex].text.trim())}</strong>
                and reset to Assigned.`,
            confirmText: 'Reassign',
        }).then(result => {
            if (result.isConfirmed) submitAssignment();
        });
    }

    function submitAssignment()
    {
        const select = document.getElementById('assignAeSelect');
        const reassigning = !!select.dataset.holder;

        runWorkflowAction({
            url: @json(route('leads.assign', $lead)),
            payload: { ae_id: select.value },
            button: document.getElementById('assignAeBtn'),
            busyText: reassigning ? 'Reassigning lead...' : 'Assigning lead...',
            successText: reassigning ? 'Lead reassigned' : 'Lead assigned',
            errorEl: document.getElementById('assignAeError'),
        });
    }

    /*
    * ============================================================
    * WORKFLOW - Assign / Reassign / Start Process / Move to
    * Account Manager / Send Back to AE / Update Lead Status
    * (Hold, Lost, Close). Every action goes through
    * runWorkflowAction(): the button shows a spinner and a
    * "...ing" label straight away, every workflow control is
    * disabled until the server answers (no double clicks), a
    * success shows a check + toast and reloads the page so the
    * Assigned Team card, badges and Assignment History show the
    * new state, and a failure restores everything and reports the
    * error without leaving the page.
    * ============================================================
    */
    let workflowBusy = false;

    function openWorkflowModal(id)
    {
        if (workflowBusy) return;

        document.getElementById(id).classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeWorkflowModal(id)
    {
        // Keep the modal (and its spinner) up while a request is running.
        if (workflowBusy) return;

        document.getElementById(id).classList.remove('show');
        document.body.style.overflow = '';
    }

    function reloadAfterToast(message)
    {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1400,
            timerProgressBar: true,
        }).then(() => window.location.reload());
    }

    function workflowErrorToast(message)
    {
        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: message, showConfirmButton: false, timer: 3500 });
    }

    function startWorkflowBusy(button, busyText)
    {
        workflowBusy = true;

        button.dataset.originalHtml = button.innerHTML;
        document.querySelectorAll('.wf-action').forEach(el => { el.disabled = true; });

        button.classList.add('is-busy');
        button.setAttribute('aria-busy', 'true');
        button.innerHTML = '<span class="wf-spinner" aria-hidden="true"></span><span>' + escapeHtml(busyText) + '</span>';
    }

    function endWorkflowBusy(button)
    {
        workflowBusy = false;

        button.classList.remove('is-busy');
        button.removeAttribute('aria-busy');
        if (button.dataset.originalHtml !== undefined) button.innerHTML = button.dataset.originalHtml;

        document.querySelectorAll('.wf-action').forEach(el => { el.disabled = false; });

        // Controls that are only enabled by a choice re-check themselves.
        if (typeof onUpdateStatusChoice === 'function' && document.getElementById('updateStatusConfirmBtn')) onUpdateStatusChoice();
    }

    function showWorkflowSuccess(button, successText)
    {
        button.classList.remove('is-busy');
        button.classList.add('is-success-state');
        button.innerHTML = '<i class="mdi mdi-check-circle-outline"></i><span>' + escapeHtml(successText) + '</span>';
    }

    /*
    * POSTs one workflow action. opts: url, payload, button (the
    * control that was clicked), busyText, successText, errorEl
    * (optional inline error box - otherwise a toast is used).
    */
    function runWorkflowAction({ url, payload, button, busyText, successText, errorEl })
    {
        if (workflowBusy) return;

        if (errorEl) errorEl.hidden = true;

        startWorkflowBusy(button, busyText);

        const fail = message => {
            endWorkflowBusy(button);

            if (errorEl) {
                errorEl.textContent = message;
                errorEl.hidden = false;
            } else {
                workflowErrorToast(message);
            }
        };

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload || {}),
        })
        .then(res => res.json().catch(() => ({})).then(body => ({ ok: res.ok, body })))
        .then(({ ok, body }) => {
            if (!ok) {
                const firstError = body && body.errors && Object.values(body.errors)[0];
                fail((firstError && firstError[0]) || (body && body.message) || 'Something went wrong. Please try again.');
                return;
            }

            // Stays disabled (workflowBusy) until the reload.
            showWorkflowSuccess(button, successText);
            reloadAfterToast(body.message || successText);
        })
        .catch(() => fail('Could not reach the server. Please check your connection and try again.'));
    }

    /*
    * The project's soft confirmation alert (same popup as the delete
    * confirmations on this page) for non-destructive actions.
    * tone: 'primary' | 'warning'. textHtml must already be escaped.
    * Returns the SweetAlert promise.
    */
    function softConfirm({ icon, tone, title, textHtml, confirmText })
    {
        return Swal.fire({
            html: `
                <div class="swal-delete-icon tone-${tone}">
                    <i class="mdi ${icon}"></i>
                </div>
                <h2 class="swal-delete-title">${escapeHtml(title)}</h2>
                <p class="swal-delete-text">${textHtml}</p>
            `,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                popup: 'swal-leads-popup',
                confirmButton: tone === 'warning' ? 'swal-btn-warning' : 'swal-btn-primary',
                cancelButton: 'swal-btn-cancel',
            },
        });
    }

    function startProcess()
    {
        if (workflowBusy) return;

        const button = document.getElementById('startProcessBtn');

        softConfirm({
            icon: 'mdi-play-circle-outline',
            tone: 'primary',
            title: 'Start processing this lead?',
            textHtml: 'The lead will move to <strong>In Progress</strong> and the start time will be recorded.',
            confirmText: 'Start Process',
        }).then(result => {
            if (!result.isConfirmed) return;

            runWorkflowAction({
                url: @json(route('leads.startProcess', $lead)),
                payload: {},
                button,
                busyText: 'Starting process...',
                successText: 'Process started',
                errorEl: null,
            });
        });
    }

    function moveToAccountManager()
    {
        const select = document.getElementById('moveToAmSelect');
        const errorBox = document.getElementById('moveToAmError');

        if (!select.value) {
            errorBox.textContent = 'Please select an Account Manager.';
            errorBox.hidden = false;
            return;
        }

        runWorkflowAction({
            url: @json(route('leads.moveToAccountManager', $lead)),
            payload: { account_manager_id: select.value },
            button: document.getElementById('moveToAmBtn'),
            busyText: 'Moving to Account Manager...',
            successText: 'Lead moved successfully',
            errorEl: errorBox,
        });
    }

    function sendBackToAe()
    {
        runWorkflowAction({
            url: @json(route('leads.sendBack', $lead)),
            payload: { note: document.getElementById('sendBackNote').value },
            button: document.getElementById('sendBackBtn'),
            busyText: 'Sending lead back to AE...',
            successText: 'Lead sent back',
            errorEl: document.getElementById('sendBackError'),
        });
    }

    /*
    * Update Lead Status - Hold / Lost / Close, one control. What
    * each choice says on the confirm button, while it runs, and on
    * success:
    */
    const UPDATE_STATUS_TEXT = {
        hold:   { confirm: 'Put on Hold',   busy: 'Putting lead on hold...', done: 'Lead put on hold', note: 'Note (optional)' },
        lost:   { confirm: 'Mark as Lost',  busy: 'Marking lead as lost...', done: 'Lead marked as lost', note: 'Reason (required)' },
        closed: { confirm: 'Close Lead',    busy: 'Closing lead...',         done: 'Lead closed', note: 'Closing note (optional)' },
    };

    function selectedUpdateStatus()
    {
        const checked = document.querySelector('input[name="amStatus"]:checked');
        return checked ? checked.value : null;
    }

    function openUpdateStatusModal()
    {
        openWorkflowModal('updateStatusModal');
        onUpdateStatusChoice();
    }

    function onUpdateStatusChoice()
    {
        const status = selectedUpdateStatus();
        const button = document.getElementById('updateStatusConfirmBtn');
        const noteLabel = document.getElementById('updateStatusNoteLabel');

        if (!button || workflowBusy) return;

        button.disabled = !status;
        button.textContent = status ? UPDATE_STATUS_TEXT[status].confirm : 'Select a status';
        noteLabel.textContent = (status ? UPDATE_STATUS_TEXT[status].note : 'Note (optional)') + ' - saved to Notes & Documents';
        document.getElementById('updateStatusError').hidden = true;
    }

    function submitUpdateStatus()
    {
        const status = selectedUpdateStatus();
        const errorBox = document.getElementById('updateStatusError');
        const note = document.getElementById('updateStatusNote').value;

        if (!status) {
            errorBox.textContent = 'Please choose Hold, Lost or Close.';
            errorBox.hidden = false;
            return;
        }

        if (status === 'lost' && !note.trim()) {
            errorBox.textContent = 'Please enter the reason this lead was lost.';
            errorBox.hidden = false;
            return;
        }

        runWorkflowAction({
            url: @json(route('leads.accountManagerStatus', $lead)),
            payload: { status, note },
            button: document.getElementById('updateStatusConfirmBtn'),
            busyText: UPDATE_STATUS_TEXT[status].busy,
            successText: UPDATE_STATUS_TEXT[status].done,
            errorEl: errorBox,
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
            badge.textContent = 'Open';
        } else if (status === 'assigned') {
            badge.className = 'status-badge status-assigned';
            badge.textContent = 'Assigned';
        } else {
            badge.className = 'status-badge status-progress';
            badge.textContent = 'Draft';
        }
    }

    /*
    * Same delete-visibility rule as the row-level delete button on
    * index.blade.php (Admin/Super Admin, or a draft lead) - just
    * applied live here instead of on every DataTables row redraw,
    * so switching a lead's status doesn't need a page reload for
    * the header Delete icon to catch up.
    */
    function updateHeaderDeleteButton(status)
    {
        const deleteBtn = document.getElementById('deleteLeadBtn');
        if (!deleteBtn) return;

        deleteBtn.style.display = (isAdmin || status === 'draft') ? '' : 'none';
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

            // Publishing a pending Multiple Site draft expands it
            // into its full batch of site leads (see
            // LeadController::expandMultisiteBatch()) - this lead's
            // own lead_id changes as part of that (e.g. "1500"
            // becomes "1500-1"), so the current URL is now stale.
            // Redirect to the lead's new one rather than leaving the
            // page showing a dead link if reloaded.
            if (result && result.expanded && result.lead_id) {

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: result.message || 'Status updated.',
                    showConfirmButton: false,
                    timer: 2200,
                    timerProgressBar: true,
                });

                window.location.href = `{{ url('/leads') }}/${result.lead_id}`;
                return;
            }

            label.textContent = newStatus === 'published' ? 'Open' : 'Draft';
            updateHeaderStatusBadge(newStatus);
            updateHeaderDeleteButton(newStatus);
            loadLogs();

            // Publishing is one-way - once published, disable the
            // toggle so it can't be flipped back to draft (matches
            // canToggleStatus, which a fresh page load would render
            // with).
            if (newStatus === 'published') {
                wrapper.classList.add('is-readonly');
                checkbox.disabled = true;
            }

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
            label.textContent = previousStatus === 'published' ? 'Open' : 'Draft';

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
            hour12: false, timeZone: 'Europe/London',
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
        // Workflow notes (Send Back / Close) are a permanent record.
        if (item.workflow_action) return '';

        const isOwner = Number(item.created_by) === Number(currentUserId);
        if (!isOwner && !isAdmin) return '';

        return `
            <div class="activity-actions">
                <button type="button" class="activity-action-btn" data-tooltip="Edit" onclick="openEditModal(${item.id})">
                    <i class="mdi mdi-pencil-box"></i>
                </button>
                <button type="button" class="activity-action-btn activity-action-danger" data-tooltip="Delete" onclick="deleteActivity(${item.id})">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        `;
    }

    function workflowNoteBadge(item)
    {
        if (!item.workflow_action) return '';

        const label = { closed: 'Lead Closed', sent_back: 'Sent Back to AE', hold: 'Lead On Hold', lost: 'Lead Lost' }[item.workflow_action] || 'Workflow';

        return `<span class="workflow-note-badge workflow-note-${item.workflow_action}"><i class="mdi mdi-lock-outline"></i> ${label}</span>`;
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
                        ${workflowNoteBadge(item)}
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
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Please write a note, choose a file, or both.',
                showConfirmButton: false,
                timer: 2400,
                timerProgressBar: true,
            });
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
                    title: 'Notes added successfully.',
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
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Note cannot be empty.',
                showConfirmButton: false,
                timer: 2400,
                timerProgressBar: true,
            });
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
                    title: 'Notes updated successfully.',
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
                        title: 'Notes deleted successfully.',
                        showConfirmButton: false,
                        timer: 1600,
                        timerProgressBar: true,
                    });
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Unable to delete.',
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
                    title: 'Unable to delete.',
                    showConfirmButton: false,
                    timer: 2400,
                    timerProgressBar: true,
                });
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
            lead_assigned: 'mdi-account-check-outline',
            lead_process_started: 'mdi-play-circle-outline',
            lead_moved_to_am: 'mdi-account-arrow-right-outline',
            lead_sent_back: 'mdi-undo-variant',
            lead_closed: 'mdi-check-circle-outline',
            lead_on_hold: 'mdi-pause-circle-outline',
            lead_lost: 'mdi-close-circle-outline',
        };

        return icons[item.action] || 'mdi-information-outline';
    }

    function logBadgeInfo(item)
    {
        const action = item.action || '';

        if (action === 'lead_status_changed') return { label: 'STATUS', cls: 'log-badge-status' };
        if (['lead_assigned', 'lead_process_started', 'lead_moved_to_am', 'lead_sent_back', 'lead_on_hold', 'lead_lost', 'lead_closed'].includes(action)) return { label: 'WORKFLOW', cls: 'log-badge-status' };
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
            date: d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', timeZone: 'Europe/London' }),
            // 'en-US' (not 'en-GB') so the AM/PM marker renders uppercase,
            // matching the "h:i A" convention used everywhere else in the app.
            time: d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true, timeZone: 'Europe/London' }),
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
    * PRICING (AU Savers only). Add/Edit forms are submitted via
    * fetch() (same pattern as Add Reminder above); Annual Spend is
    * recalculated live client-side as a preview using the exact same
    * formula as App\Support\LeadPricingCalculator, but the server's
    * calculation on save is always the value actually stored.
    * ============================================================
    */

    function pricingNumVal(id)
    {
        const el = document.getElementById(id);
        const value = el ? parseFloat(el.value) : NaN;
        return isNaN(value) ? 0 : value;
    }

    function togglePricingRateFields(prefix)
    {
        const rateTypeEl = document.getElementById(prefix + 'RateType');
        if (!rateTypeEl) return;

        const isMulti = rateTypeEl.value === 'multi';

        document.getElementById(prefix + 'SingleEacWrap').style.display = isMulti ? 'none' : '';

        ['DayWrap', 'EveningConsumptionWrap', 'NightConsumptionWrap', 'TotalEacDisplayWrap', 'NightRateWrap', 'EveningRateWrap']
            .forEach(function (suffix) {
                document.getElementById(prefix + suffix).style.display = isMulti ? '' : 'none';
            });

        document.getElementById(prefix + 'UnitRateLabel').textContent = isMulti
            ? 'Day Unit Rate p/kWh'
            : 'Unit Rate p/kWh';

        recalculatePricingPreview(prefix);
    }

    function formatMoney(n)
    {
        return '£' + Number(n || 0).toFixed(2);
    }

    /*
    * Rate/charge fields go up to 4 decimal places (step="0.0001"),
    * but shouldn't be padded out with trailing zeros the user never
    * typed - 20.1 should read as "20.1p", not "20.10p". Rounds to 4dp
    * first (avoiding stray floating-point digits like
    * 20.099999999999998) then lets toString() drop trailing zeros.
    */
    function formatPence(n)
    {
        const rounded = Math.round((Number(n) || 0) * 10000) / 10000;
        return rounded.toString() + 'p';
    }

    function formatKwh(n)
    {
        return Number(n || 0).toLocaleString('en-GB', { maximumFractionDigits: 2 }) + ' kWh';
    }

    /*
    * Single source of truth (client-side) for turning a pricing
    * record's raw fields into the same Annual Spend figure and a
    * human-readable breakdown of how it was reached. Mirrors
    * App\Support\LeadPricingCalculator::annualSpend() exactly - kept
    * in sync manually since there's no shared JS/PHP calculation
    * layer in this app. Used by the live form preview, the current
    * pricing summary card and the pricing history list, so "how is
    * this calculated" always reads the same way everywhere it's shown.
    * The server always recalculates and stores the authoritative
    * figure independently in LeadPricingController before saving.
    */
    function computePricingBreakdown(d)
    {
        const uplift = Number(d.uplift_pence) || 0;
        const sc = Number(d.sc_pence_per_day) || 0;
        const standingCharge = (sc * 365) / 100;
        const lines = [];
        let formula;
        let total;
        let totalEac;

        if (d.rate_type === 'multi') {
            const day = Number(d.day_consumption_kwh) || 0;
            const evening = Number(d.evening_consumption_kwh) || 0;
            const night = Number(d.night_consumption_kwh) || 0;
            const dayBase = Number(d.unit_rate_pence) || 0;
            const eveningBase = Number(d.evening_unit_rate_pence) || 0;
            const nightBase = Number(d.night_unit_rate_pence) || 0;

            const dayRate = dayBase + uplift;
            const eveningRate = eveningBase + uplift;
            const nightRate = nightBase + uplift;

            const dayCost = (day * dayRate) / 100;
            const eveningCost = (evening * eveningRate) / 100;
            const nightCost = (night * nightRate) / 100;

            totalEac = day + evening + night;
            total = dayCost + eveningCost + nightCost + standingCharge;

            formula = 'Annual Spend = [(Day kWh x Day Rate) + (Evening kWh x Evening Rate) + (Night kWh x Night Rate)] / 100 + (SC p/day x 365 / 100)\n'
                + `Day Rate = ${formatPence(dayBase)} base + ${formatPence(uplift)} uplift = ${formatPence(dayRate)}/kWh\n`
                + `Evening Rate = ${formatPence(eveningBase)} base + ${formatPence(uplift)} uplift = ${formatPence(eveningRate)}/kWh\n`
                + `Night Rate = ${formatPence(nightBase)} base + ${formatPence(uplift)} uplift = ${formatPence(nightRate)}/kWh`;

            lines.push({ label: `Day: ${formatKwh(day)} x ${formatPence(dayRate)}`, value: formatMoney(dayCost) });
            lines.push({ label: `Evening: ${formatKwh(evening)} x ${formatPence(eveningRate)}`, value: formatMoney(eveningCost) });
            lines.push({ label: `Night: ${formatKwh(night)} x ${formatPence(nightRate)}`, value: formatMoney(nightCost) });
        } else {
            totalEac = Number(d.total_eac_kwh) || 0;
            const base = Number(d.unit_rate_pence) || 0;
            const customerRate = base + uplift;
            const usageCost = (totalEac * customerRate) / 100;

            total = usageCost + standingCharge;

            formula = 'Annual Spend = Total EAC x (Customer Rate / 100) + (SC p/day x 365 / 100)\n'
                + `Customer Rate = ${formatPence(base)} base + ${formatPence(uplift)} uplift = ${formatPence(customerRate)}/kWh`;

            lines.push({ label: `Usage: ${formatKwh(totalEac)} x ${formatPence(customerRate)}`, value: formatMoney(usageCost) });
        }

        lines.push({ label: `Standing Charge: ${formatPence(sc)}/day x 365`, value: formatMoney(standingCharge) });

        return { formula, lines, total, totalEac };
    }

    function renderPricingCalcHtml(d)
    {
        const breakdown = computePricingBreakdown(d);

        const lineHtml = breakdown.lines.map(function (line) {
            return `
                <div class="pricing-calc-line">
                    <span>${escapeHtml(line.label)}</span>
                    <span>${line.value}</span>
                </div>
            `;
        }).join('');

        return `
            <h6>How this is calculated</h6>
            <div class="pricing-calc-formula">${escapeHtml(breakdown.formula)}</div>
            ${lineHtml}
            <div class="pricing-calc-line total">
                <span>Annual Spend</span>
                <span>${formatMoney(breakdown.total)}</span>
            </div>
        `;
    }

    function recalculatePricingPreview(prefix)
    {
        const data = collectPricingFormData(prefix);
        const breakdown = computePricingBreakdown(data);

        if (data.rate_type === 'multi') {
            document.getElementById(prefix + 'TotalEacDisplay').value = formatKwh(breakdown.totalEac);
        }

        document.getElementById(prefix + 'AnnualSpendPreview').textContent = formatMoney(breakdown.total);
        document.getElementById(prefix + 'CalcBreakdown').innerHTML = renderPricingCalcHtml(data);
    }

    function collectPricingFormData(prefix, status)
    {
        const rateType = document.getElementById(prefix + 'RateType').value;

        const data = {
            supplier_id: document.getElementById(prefix + 'Supplier').value,
            rate_type: rateType,
            contract_term_months: document.getElementById(prefix + 'ContractTerm').value,
            sc_pence_per_day: document.getElementById(prefix + 'Sc').value,
            unit_rate_pence: document.getElementById(prefix + 'UnitRate').value,
            uplift_pence: document.getElementById(prefix + 'Uplift').value,
        };

        if (status) {
            data.status = status;
        }

        if (rateType === 'multi') {
            data.day_consumption_kwh = document.getElementById(prefix + 'DayConsumption').value;
            data.evening_consumption_kwh = document.getElementById(prefix + 'EveningConsumption').value;
            data.night_consumption_kwh = document.getElementById(prefix + 'NightConsumption').value;
            data.night_unit_rate_pence = document.getElementById(prefix + 'NightRate').value;
            data.evening_unit_rate_pence = document.getElementById(prefix + 'EveningRate').value;
        } else {
            data.total_eac_kwh = document.getElementById(prefix + 'TotalEac').value;
        }

        return data;
    }

    function setPricingFormError(prefix, message)
    {
        const el = document.getElementById(prefix + 'Error');
        if (!el) return;

        el.hidden = !message;
        el.textContent = message || '';
    }

    /*
    * Clears every per-field error slot and .is-invalid border in the
    * given form - called at the start of every submit attempt so a
    * fixed field's old error doesn't linger next to it.
    */
    function clearPricingFieldErrors(prefix)
    {
        const form = document.getElementById(prefix + 'Form');
        if (!form) return;

        form.querySelectorAll('.invalid-feedback').forEach(el => { el.textContent = ''; });
        form.querySelectorAll('.is-invalid').forEach(el => { el.classList.remove('is-invalid'); });
    }

    /*
    * Renders a Laravel validation error bag ({field: [messages]})
    * directly under each field it belongs to, via each .form-group
    * wrapper's data-field attribute - so "Please select a supplier"
    * shows under Supplier, not lumped into one banner at the bottom
    * of the form. Uses this app's normal Bootstrap .is-invalid /
    * .invalid-feedback pattern (public/assets/css/vertical-layout-
    * light/style.css already themes both, including for Select2).
    * Any error for a field this form doesn't have (e.g. a
    * multi-rate-only field while single-rate is selected) falls back
    * to the generic banner instead of being silently dropped.
    */
    function setPricingFieldErrors(prefix, errors)
    {
        const form = document.getElementById(prefix + 'Form');
        if (!form || !errors) return;

        const leftover = [];

        Object.keys(errors).forEach(function (field) {
            const message = [].concat(errors[field])[0];
            const wrapper = form.querySelector(`[data-field="${field}"]`);

            if (!wrapper) {
                leftover.push(message);
                return;
            }

            const errorEl = wrapper.querySelector('.invalid-feedback');
            const input = wrapper.querySelector('select, input');

            if (errorEl) errorEl.textContent = message;
            if (input) input.classList.add('is-invalid');

            // Select2 hides the real <select> and renders its own
            // box - the theme's red-border rule targets THAT box
            // specifically (.select2-selection--single.is-invalid),
            // not the outer .select2-container.
            const select2Selection = wrapper.querySelector('.select2-selection--single');
            if (select2Selection) select2Selection.classList.add('is-invalid');
        });

        if (leftover.length) {
            setPricingFormError(prefix, leftover.join(' '));
        }
    }

    function updatePricingSummary(pricing)
    {
        const empty = document.getElementById('pricingEmptyState');
        const current = document.getElementById('pricingCurrent');
        const editBtn = document.getElementById('editPricingDraftBtn');

        if (!empty || !current) return;

        if (!pricing) {
            empty.classList.remove('d-none');
            current.classList.add('d-none');
            if (editBtn) editBtn.style.display = 'none';
            return;
        }

        empty.classList.add('d-none');
        current.classList.remove('d-none');

        document.getElementById('pricingSupplier').textContent = pricing.supplier?.name ?? '-';

        const statusBadge = document.getElementById('pricingStatusBadge');
        statusBadge.textContent = pricing.status.charAt(0).toUpperCase() + pricing.status.slice(1);
        statusBadge.className = 'status-badge ' + (pricing.status === 'published' ? 'status-complete' : 'status-progress');

        document.getElementById('pricingRateType').textContent = pricing.rate_type === 'multi'
            ? 'Multi-Rate (Day/Evening/Night)'
            : 'Single-Rate';

        document.getElementById('pricingContractTerm').textContent = pricing.contract_term_months
            ? `${pricing.contract_term_months} months`
            : '-';

        document.getElementById('pricingEac').textContent = `${Number(pricing.total_eac_kwh).toFixed(2)} kWh`;
        document.getElementById('pricingAnnualSpend').innerHTML = `<strong>£${Number(pricing.annual_spend).toFixed(2)}</strong>`;

        document.getElementById('pricingSummaryMore').innerHTML = renderPricingSummaryExtraRows(pricing);
        document.getElementById('pricingCalcBreakdown').innerHTML = renderPricingCalcHtml(pricing);

        if (editBtn) {
            if (pricing.status === 'draft') {
                editBtn.style.display = '';
                editBtn.setAttribute('onclick', `openEditPricingModal(${pricing.id})`);
            } else {
                editBtn.style.display = 'none';
            }
        }
    }

    function togglePricingSummaryMore()
    {
        const box = document.getElementById('pricingSummaryMore');
        const icon = document.getElementById('pricingShowMoreIcon');
        const label = document.getElementById('pricingShowMoreLabel');
        if (!box) return;

        const nowVisible = box.classList.toggle('d-none') === false;
        if (icon) icon.className = nowVisible ? 'mdi mdi-chevron-up' : 'mdi mdi-chevron-down';
        if (label) label.textContent = nowVisible ? 'Show Less' : 'Show More';
    }

    function togglePricingCalcBreakdown()
    {
        const box = document.getElementById('pricingCalcBreakdown');
        const toggle = document.getElementById('pricingCalcToggle');
        if (!box || !toggle) return;

        const nowVisible = box.classList.toggle('d-none') === false;
        toggle.innerHTML = nowVisible
            ? '<i class="mdi mdi-calculator-variant-outline"></i> Hide Calculation'
            : '<i class="mdi mdi-calculator-variant-outline"></i> View Calculation';
    }

    function loadPricingHistoryAndRefreshSummary()
    {
        return fetch(`/leads/${leadId}/pricing`, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(records => {
                pricingHistoryCache = records;
                updatePricingSummary(records[0] || null);
                return records;
            })
            .catch(() => []);
    }

    function submitPricingForm(prefix, status)
    {
        setPricingFormError(prefix, '');
        clearPricingFieldErrors(prefix);

        const isEdit = prefix === 'editPricing';
        const data = collectPricingFormData(prefix, status);

        const url = isEdit
            ? `/lead-pricing/${document.getElementById('editPricingModal').dataset.pricingId}`
            : `/leads/${leadId}/pricing`;

        fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(res => res.json().then(payload => ({ ok: res.ok, payload })))
        .then(({ ok, payload }) => {
            if (ok && payload.success) {
                updatePricingSummary(payload.pricing);

                if (isEdit) {
                    closeEditPricingModal();
                } else {
                    closeAddPricingModal();
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: payload.message || 'Pricing saved.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
            } else if (payload.errors) {
                setPricingFieldErrors(prefix, payload.errors);
            } else {
                setPricingFormError(prefix, payload.message || 'Unable to save pricing.');
            }
        })
        .catch(err => {
            setPricingFormError(prefix, 'Unable to save pricing. Please try again.');
            console.error(err);
        });
    }

    /*
    * Select2-enhanced <select> elements (Supplier, Rate Type) hide
    * the real element and render their own UI from it - setting
    * .value directly (as a plain <select> would need) updates the
    * hidden element but not what's actually displayed. jQuery's
    * .val().trigger('change') is what Select2 listens for to redraw.
    */
    function setPricingSelectValue(elementId, value)
    {
        const select = document.getElementById(elementId);
        if (!select) return;

        if (window.jQuery && window.jQuery.fn.select2) {
            window.jQuery(select).val(value ?? '').trigger('change');
        } else {
            select.value = value ?? '';
        }
    }

    function openAddPricingModal()
    {
        const form = document.getElementById('addPricingForm');
        if (!form) return;

        form.reset();
        setPricingSelectValue('addPricingSupplier', '');
        setPricingSelectValue('addPricingRateType', 'single');
        togglePricingRateFields('addPricing');
        setPricingFormError('addPricing', '');
        clearPricingFieldErrors('addPricing');

        document.getElementById('addPricingModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeAddPricingModal()
    {
        document.getElementById('addPricingModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    function openEditPricingModal(id)
    {
        if (!id) return;

        fetch(`/leads/${leadId}/pricing`, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(records => {
                pricingHistoryCache = records;

                const pricing = records.find(p => Number(p.id) === Number(id));
                if (!pricing) return;

                document.getElementById('editPricingModal').dataset.pricingId = pricing.id;

                setPricingSelectValue('editPricingSupplier', pricing.supplier_id);
                setPricingSelectValue('editPricingRateType', pricing.rate_type);
                document.getElementById('editPricingContractTerm').value = pricing.contract_term_months;
                document.getElementById('editPricingSc').value = pricing.sc_pence_per_day;
                document.getElementById('editPricingUnitRate').value = pricing.unit_rate_pence;
                document.getElementById('editPricingUplift').value = pricing.uplift_pence;

                if (pricing.rate_type === 'multi') {
                    document.getElementById('editPricingDayConsumption').value = pricing.day_consumption_kwh;
                    document.getElementById('editPricingEveningConsumption').value = pricing.evening_consumption_kwh;
                    document.getElementById('editPricingNightConsumption').value = pricing.night_consumption_kwh;
                    document.getElementById('editPricingNightRate').value = pricing.night_unit_rate_pence;
                    document.getElementById('editPricingEveningRate').value = pricing.evening_unit_rate_pence;
                } else {
                    document.getElementById('editPricingTotalEac').value = pricing.total_eac_kwh;
                }

                togglePricingRateFields('editPricing');
                setPricingFormError('editPricing', '');
                clearPricingFieldErrors('editPricing');

                document.getElementById('editPricingModal').classList.add('show');
                document.body.style.overflow = 'hidden';
            });
    }

    function closeEditPricingModal()
    {
        document.getElementById('editPricingModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    /*
    * The full field list for one pricing record - Rate Type, Contract
    * Term, EAC (split into Day/Evening/Night when multi-rate), every
    * rate, SC and Uplift - the same fields the Add/Edit form collects.
    * Hidden behind "Show More" in the history list by default so the
    * compact summary stays scannable; same fields the current-pricing
    * card on the page shows for the latest record.
    */
    /*
    * The full field list for one pricing record - Supplier, Status,
    * Rate Type, Contract Term, EAC (split into Day/Evening/Night when
    * multi-rate), every rate, SC, Uplift, Annual Spend and who/when
    * it was added. No calculation breakdown - just the data itself.
    * Used by the Pricing View modal (see openPricingViewModal) and by
    * "Show More" on the current-pricing summary card.
    */
    function detailRowsHtml(rows)
    {
        return rows.map(([icon, label, value]) => `
            <div class="detail-row">
                <i class="mdi ${icon} row-icon"></i>
                <span class="label">${label}</span>
                <span class="value">${value}</span>
            </div>
        `).join('');
    }

    /*
    * The consumption/rate/SC/uplift rows - shared between the
    * "Show More" section on the current-pricing summary card (which
    * already shows Supplier/Status/Rate Type/Contract Term/Total
    * EAC/Annual Spend separately, so only needs "the rest") and the
    * Pricing View modal (which shows everything, see
    * renderPricingFullDetailRows below).
    */
    function pricingRateDetailRows(pricing)
    {
        const rows = [];

        if (pricing.rate_type === 'multi') {
            rows.push(['mdi-weather-sunny', 'Day Consumption', formatKwh(pricing.day_consumption_kwh)]);
            rows.push(['mdi-weather-sunset', 'Evening Consumption', formatKwh(pricing.evening_consumption_kwh)]);
            rows.push(['mdi-weather-night', 'Night Consumption', formatKwh(pricing.night_consumption_kwh)]);
            rows.push(['mdi-cash', 'Day Unit Rate', `${formatPence(pricing.unit_rate_pence)}/kWh`]);
            rows.push(['mdi-cash', 'Evening Unit Charge', `${formatPence(pricing.evening_unit_rate_pence)}/kWh`]);
            rows.push(['mdi-cash', 'Night Unit Charge', `${formatPence(pricing.night_unit_rate_pence)}/kWh`]);
        } else {
            rows.push(['mdi-cash', 'Unit Rate', `${formatPence(pricing.unit_rate_pence)}/kWh`]);
        }

        rows.push(['mdi-cash', 'SC p/day', `${formatPence(pricing.sc_pence_per_day)}/day`]);
        rows.push(['mdi-trending-up', 'Uplift', `${formatPence(pricing.uplift_pence)}/kWh`]);

        return rows;
    }

    /*
    * Every field for one pricing record, data only (no calculation -
    * that's intentionally left to the summary card's own "View
    * Calculation" toggle). Used by the Pricing View modal.
    */
    function renderPricingFullDetailRows(pricing)
    {
        const createdBy = escapeHtml(pricing.creator?.name ?? 'Unknown');
        const createdAt = pricing.created_at ? new Date(pricing.created_at).toLocaleString('en-GB') : '-';
        const statusClass = pricing.status === 'published' ? 'status-complete' : 'status-progress';

        return detailRowsHtml([
            ['mdi-domain', 'Supplier', escapeHtml(pricing.supplier?.name ?? '-')],
            ['mdi-flag', 'Status', `<span class="status-badge ${statusClass}">${pricing.status.charAt(0).toUpperCase() + pricing.status.slice(1)}</span>`],
            ['mdi-flash-outline', 'Rate Type', pricing.rate_type === 'multi' ? 'Multi-Rate (Day/Evening/Night)' : 'Single-Rate'],
            ['mdi-calendar-range-outline', 'Contract Term', pricing.contract_term_months ? `${pricing.contract_term_months} months` : '-'],
            ['mdi-lightning-bolt-outline', 'Total EAC', formatKwh(pricing.total_eac_kwh)],
            ...pricingRateDetailRows(pricing),
            ['mdi-cash-multiple', 'Annual Spend', `<strong>£${Number(pricing.annual_spend).toFixed(2)}</strong>`],
            ['mdi-account-circle-outline', 'Added By', `${createdBy} - ${createdAt}`],
        ]);
    }

    /*
    * "Show More" on the current-pricing summary card - just the
    * fields not already shown there (consumption breakdown, rates,
    * SC, uplift).
    */
    function renderPricingSummaryExtraRows(pricing)
    {
        return detailRowsHtml(pricingRateDetailRows(pricing));
    }

    function renderPricingHistoryItem(pricing)
    {
        const statusClass = pricing.status === 'published' ? 'status-complete' : 'status-progress';
        const createdBy = escapeHtml(pricing.creator?.name ?? 'Unknown');
        const createdAt = pricing.created_at ? new Date(pricing.created_at).toLocaleString('en-GB') : '-';

        const editButton = (canManagePricing && pricing.status === 'draft') ? `
            <button type="button" class="activity-action-btn" data-tooltip="Edit" onclick="openEditPricingModal(${pricing.id})">
                <i class="mdi mdi-pencil-box"></i>
            </button>
        ` : '';

        const deleteButton = canManagePricing ? `
            <button type="button" class="activity-action-btn activity-action-danger" data-tooltip="Delete" onclick="deletePricingRecord(${pricing.id})">
                <i class="mdi mdi-delete"></i>
            </button>
        ` : '';

        return `
            <div class="pricing-history-item" data-pricing-id="${pricing.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="pricing-history-details">
                        <div class="detail-row">
                            <i class="mdi mdi-domain row-icon"></i>
                            <span class="label">Supplier</span>
                            <span class="value">${escapeHtml(pricing.supplier?.name ?? '-')}</span>
                        </div>
                        <div class="detail-row">
                            <i class="mdi mdi-flag row-icon"></i>
                            <span class="label">Status</span>
                            <span class="value"><span class="status-badge ${statusClass}">${pricing.status.charAt(0).toUpperCase() + pricing.status.slice(1)}</span></span>
                        </div>
                        <div class="detail-row">
                            <i class="mdi mdi-cash-multiple row-icon"></i>
                            <span class="label">Annual Spend</span>
                            <span class="value"><strong>£${Number(pricing.annual_spend).toFixed(2)}</strong></span>
                        </div>
                        <div class="detail-row">
                            <i class="mdi mdi-account-circle-outline row-icon"></i>
                            <span class="label">Added By</span>
                            <span class="value">${createdBy} - ${createdAt}</span>
                        </div>
                    </div>
                    <div class="activity-actions">
                        <button type="button" class="activity-action-btn" data-tooltip="View" onclick="openPricingViewModal(${pricing.id})">
                            <i class="mdi mdi-eye-outline"></i>
                        </button>
                        ${editButton}
                        ${deleteButton}
                    </div>
                </div>
            </div>
        `;
    }

    /*
    * "View" on a history row - opens a dedicated modal with every
    * field for that record (data only, no calculation - that's
    * intentionally not shown here, only on the current-pricing
    * summary card via its own "View Calculation" toggle).
    */
    function openPricingViewModal(id)
    {
        const pricing = pricingHistoryCache.find(p => Number(p.id) === Number(id));
        if (!pricing) return;

        document.getElementById('pricingViewModalBody').innerHTML = renderPricingFullDetailRows(pricing);
        document.getElementById('pricingViewModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closePricingViewModal()
    {
        document.getElementById('pricingViewModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    function openPricingHistoryModal()
    {
        document.getElementById('pricingHistoryModal').classList.add('show');
        document.body.style.overflow = 'hidden';

        const loading = document.getElementById('pricingHistoryLoading');
        const list = document.getElementById('pricingHistoryList');

        loading.style.display = '';
        list.innerHTML = '';

        fetch(`/leads/${leadId}/pricing`, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(records => {
                pricingHistoryCache = records;
                loading.style.display = 'none';
                list.innerHTML = records.length
                    ? records.map(renderPricingHistoryItem).join('')
                    : '<p class="text-muted">No pricing records yet.</p>';

                updatePricingSummary(records[0] || null);
            })
            .catch(() => {
                loading.style.display = 'none';
                list.innerHTML = '<p class="text-danger">Unable to load pricing history.</p>';
            });
    }

    function closePricingHistoryModal()
    {
        document.getElementById('pricingHistoryModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    function deletePricingRecord(id)
    {
        Swal.fire({
            title: 'Delete this pricing record?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch(`/lead-pricing/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    openPricingHistoryModal();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || 'Pricing record deleted.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && window.jQuery.fn.select2) {
            window.jQuery('.pricing-supplier-select').select2({
                placeholder: 'Select supplier',
                allowClear: true,
                width: '100%',
                dropdownParent: window.jQuery('body'),
            });

            // Rate Type only ever has 2 fixed options and always
            // needs one selected - no search box, no clear button -
            // but still Select2 so its dropdown/background matches
            // the Supplier field instead of a plain native <select>.
            window.jQuery('.pricing-rate-type-select').select2({
                width: '100%',
                dropdownParent: window.jQuery('body'),
                minimumResultsForSearch: Infinity,
            });
        }

        if (document.getElementById('pricingCurrent')) {
            loadPricingHistoryAndRefreshSummary();
        }
    });

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
    * A reminder is "due" only on its exact reminder date - not
    * before it, and not once it's passed either. Only the date is
    * compared (not time), so a reminder scheduled for later today
    * still counts as due, but one from yesterday or earlier no
    * longer does.
    */
    /*
    * reminder_date arrives as a bare "YYYY-MM-DD" (no time/zone -
    * see LeadReminder's `date:Y-m-d` cast). Parsed this way instead
    * of `new Date("YYYY-MM-DD")`, which the JS spec parses as UTC
    * midnight - fine for timezones ahead of UTC, but one day behind
    * for any timezone behind UTC. Splitting and constructing the
    * Date explicitly makes it always land on the intended local
    * calendar day, regardless of the viewer's timezone.
    */
    function parseReminderDate(dateStr)
    {
        const [year, month, day] = dateStr.split('-').map(Number);
        return new Date(year, month - 1, day);
    }

    /*
    * "Today" is anchored to the UK calendar day (the office's
    * operating timezone), not the viewer's device - otherwise a
    * reminder could flip in/out of "due" depending on where in the
    * world someone happens to be viewing from.
    */
    function todayInLondon()
    {
        const parts = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Europe/London',
            year: 'numeric', month: '2-digit', day: '2-digit',
        }).formatToParts(new Date());

        const lookup = {};
        parts.forEach(p => { lookup[p.type] = p.value; });

        return new Date(Number(lookup.year), Number(lookup.month) - 1, Number(lookup.day));
    }

    function isReminderDue(reminder)
    {
        const today = todayInLondon();

        const date = parseReminderDate(reminder.reminder_date);
        date.setHours(0, 0, 0, 0);

        return date.getTime() === today.getTime();
    }

    /*
    * Yellow banner near the header - a lightweight one-off check on
    * page load (same GET /leads/{lead}/reminders endpoint the
    * Reminders card already uses) purely to decide whether the
    * banner should show. Only counts reminders whose date is today -
    * a reminder scheduled for next week shouldn't raise an alert
    * today, and one from last week shouldn't keep raising it either.
    * Not tied to the List/View/Edit modal flow.
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

            const due = reminders.filter(isReminderDue);

            if (due.length) {
                document.getElementById('reminderBannerText').textContent = due.length === 1
                    ? 'You have 1 reminder due for this lead.'
                    : `You have ${due.length} reminders due for this lead.`;
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
        const date = parseReminderDate(reminder.reminder_date);

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
                <button type="button" class="activity-action-btn" data-tooltip="Edit" onclick="openEditReminderModal(${reminder.id})">
                    <i class="mdi mdi-pencil-box"></i>
                </button>
                <button type="button" class="activity-action-btn activity-action-danger" data-tooltip="Delete" onclick="deleteReminder(${reminder.id})">
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
                        title: 'Reminder deleted successfully.',
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
