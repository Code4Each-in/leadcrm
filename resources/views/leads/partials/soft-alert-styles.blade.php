{{--
    The leads pages' soft alert (round tinted icon, bold title, grey text,
    pill buttons) for the Add Lead / Edit Lead dialogs - the stage dialog
    and the duplicate-MPAN confirmation. Same look as the confirmations on
    the lead's page and the listing. Included once per page (@once).
--}}
@once
<style>
    .swal2-container { z-index: 100000 !important; }

    .swal-leads-popup {
        border-radius: 20px !important;
        padding: 32px 28px 28px !important;
    }

    .swal-leads-popup.swal2-show {
        animation: swalLeadsPopIn 0.22s ease-out;
    }

    @keyframes swalLeadsPopIn {
        from { opacity: 0; transform: scale(0.92); }
        to { opacity: 1; transform: scale(1); }
    }

    .swal-leads-popup .swal2-html-container {
        margin: 0 !important;
    }

    .swal-leads-popup .swal2-actions {
        margin-top: 22px !important;
        gap: 10px;
    }

    .swal-delete-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
    }

    .swal-delete-icon.tone-warning { background: #fdeede; color: #e07b00; }

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

    .swal-btn-warning,
    .swal-btn-cancel {
        font-size: 13.5px !important;
        font-weight: 500 !important;
        padding: 9px 20px !important;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    .swal-btn-warning { background: #e07b00 !important; color: #fff !important; border: 0 !important; }
    .swal-btn-warning:hover { background: #c46c00 !important; }

    .swal-btn-cancel {
        background: #fff !important;
        color: #6c7280 !important;
        border: 1px solid #e2e5eb !important;
    }

    .swal-btn-cancel:hover { background: #f4f5f7 !important; }

    .swal-delete-icon.tone-primary { background: #eef0ff; color: #5b52e0; }

    .swal-btn-primary {
        font-size: 13.5px !important;
        font-weight: 500 !important;
        padding: 9px 20px !important;
        border-radius: 9px !important;
        box-shadow: none !important;
        background: #5b52e0 !important;
        color: #fff !important;
        border: 0 !important;
    }

    .swal-btn-primary:hover { background: #4a43d1 !important; }
</style>
@endonce
