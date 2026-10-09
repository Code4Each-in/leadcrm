{{--
    The Save dialog for AU Savers leads - on Add Lead, and on Edit while
    the lead is still a draft (nowhere else: the listing and the lead's
    page only show the stage). The user
    picks the lead's stage: Call Back / Awaiting Additional Information
    keep it a draft (shown as that stage), Lead Submitted to Pricing
    submits it to MIS - one-way. The server enforces the same rules (see
    LeadController::applySaveStage()); this only collects the choice.

    Defines window.LeadStageDialog:
      - open(current)          -> Promise of the chosen stage, or null if cancelled
      - intercept(event, form) -> for a lead form's submit handler, once its
                                  own checks have passed; true when it has
                                  taken over the submission (dialog open)
      - syncButtons(form)      -> shows the AU Savers buttons (one Save /
                                  Update) or the usual Publish + Save as Draft
                                  pair, for the product picked

    A form opts in with data-stage-dialog="on", a hidden
    input[name="lead_stage"] (data-current = the draft stage it is at),
    its primary submit button marked data-stage-primary (with
    data-label-au / data-label-other) and its Save as Draft button marked
    data-stage-draft.

    Shown as the leads pages' soft alert (see partials/soft-alert-styles).
--}}
@include('leads.partials.soft-alert-styles')

<style>
    .lead-stage-select {
        margin-top: 14px;
        height: 42px;
        border-radius: 10px;
        border-color: #e2e5eb;
        font-size: 14px;
        color: #1a1f2b;
    }

    .lead-stage-select:focus {
        border-color: #5b52e0;
        box-shadow: 0 0 0 3px rgba(91, 82, 224, 0.12);
    }

    .lead-stage-submit-hint {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        padding: 10px 12px;
        border: 1px solid #f6dcc0;
        border-radius: 10px;
        background: #fff7ee;
        font-size: 12.5px;
        line-height: 1.5;
        color: #8a4b00;
        text-align: left;
    }

    .lead-stage-submit-hint[hidden] {
        display: none;
    }

    .lead-stage-submit-hint i {
        font-size: 16px;
        color: #e07b00;
        flex-shrink: 0;
    }
</style>

<script>
window.LeadStageDialog = (function () {

    const STAGES = @js(\App\Models\Lead::SAVE_STAGES);
    const SUBMIT_STAGE = @js(\App\Models\Lead::STATUS_LEAD_SUBMITTED_TO_PRICING);
    const AU_SAVERS_ID = @js(\App\Models\Product::AU_SAVERS_ID);

    function isAuSavers(productId) {
        return parseInt(productId, 10) === AU_SAVERS_ID;
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = String(value);
        return div.innerHTML;
    }

    function open(current) {
        const options = Object.entries(STAGES)
            .map(([value, label]) => `<option value="${value}"${value === current ? ' selected' : ''}>${escapeHtml(label)}</option>`)
            .join('');

        return Swal.fire({
            html: `
                <div class="swal-delete-icon tone-primary">
                    <i class="mdi mdi-flag-variant-outline"></i>
                </div>
                <h2 class="swal-delete-title">Select Lead Stage</h2>
                <p class="swal-delete-text">Choose this lead's current stage before saving.</p>
                <select id="leadStageSelect" class="form-select lead-stage-select" aria-label="Lead stage">
                    <option value="">Select stage</option>
                    ${options}
                </select>
                <div id="leadStageSubmitHint" class="lead-stage-submit-hint" hidden>
                    <i class="mdi mdi-alert-outline"></i>
                    <span>The lead will be submitted to MIS for pricing. You will no longer be able to edit or delete it,
                    and it can't be moved back to Call Back or Awaiting Additional Information.</span>
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'Confirm & Save',
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            reverseButtons: true,
            focusConfirm: false,
            customClass: {
                popup: 'swal-leads-popup',
                confirmButton: 'swal-btn-primary',
                cancelButton: 'swal-btn-cancel',
            },
            didOpen: () => {
                const select = document.getElementById('leadStageSelect');
                const hint = document.getElementById('leadStageSubmitHint');
                const sync = () => { hint.hidden = select.value !== SUBMIT_STAGE; };

                select.addEventListener('change', sync);
                sync();
            },
            preConfirm: () => {
                const value = document.getElementById('leadStageSelect').value;

                if (!value) {
                    Swal.showValidationMessage('Please select a stage.');
                    return false;
                }

                return value;
            },
        }).then((result) => (result.isConfirmed ? result.value : null));
    }

    function selectedProduct(form) {
        const checked = form.querySelector('input[name="product_id"]:checked');

        return checked ? checked.value : null;
    }

    function applies(form) {
        return form.dataset.stageDialog === 'on' && isAuSavers(selectedProduct(form));
    }

    function intercept(event, form) {
        const input = form.querySelector('input[name="lead_stage"]');

        if (!input) {
            return false;
        }

        // Not an AU Savers draft - no stage is sent (the server would refuse one).
        if (!applies(form)) {
            input.value = '';
            return false;
        }

        // Second pass, after the dialog was confirmed - let it through.
        if (form.dataset.stageConfirmed === '1') {
            form.dataset.stageConfirmed = '';
            return false;
        }

        event.preventDefault();

        const submitter = event.submitter;

        open(input.value || input.dataset.current || '').then((stage) => {
            if (!stage) {
                return;
            }

            input.value = stage;
            form.dataset.stageConfirmed = '1';
            form.requestSubmit(submitter && submitter.form === form ? submitter : undefined);
        });

        return true;
    }

    function syncButtons(form) {
        const isAu = applies(form);
        const primary = form.querySelector('[data-stage-primary]');
        const draft = form.querySelector('[data-stage-draft]');

        if (primary) {
            const label = primary.querySelector('.js-stage-primary-label');

            if (label) {
                label.textContent = isAu ? primary.dataset.labelAu : primary.dataset.labelOther;
            }
        }

        if (draft) {
            draft.hidden = isAu;
            draft.disabled = isAu;
        }
    }

    return { open, intercept, syncButtons, isAuSavers, STAGES };
})();
</script>
