{{--
    The Save dialog for AU Savers leads - on Add Lead, and on Edit while
    the lead is still a draft (nowhere else: the listing and the lead's
    page only show the stage). The user
    picks the lead's stage: Call Back / Awaiting Additional Information
    keep it a draft (shown as that stage), Lead Submitted to Pricing
    submits it to MIS - one-way. The server enforces the same rules (see
    LeadController::applySaveStage()); this only collects the choice.

    Defines window.LeadStageDialog:
      - open(current, reminder) -> Promise of {stage, reminder} chosen, or null if
                                  cancelled. Call Back / Awaiting Additional
                                  Information also ask for a reminder (date,
                                  time and note all required) - posted as
                                  stage_reminder_* and saved as a normal lead
                                  reminder (LeadController::saveFormExtras()).
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

    /* Reminder - Call Back / Awaiting Additional Information */
    .lead-stage-reminder {
        margin-top: 14px;
        padding: 14px;
        border: 1px solid #e4e3fb;
        border-radius: 12px;
        background: #f8f8ff;
        text-align: left;
    }

    .lead-stage-reminder[hidden] {
        display: none;
    }

    .lead-stage-reminder-title {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0 0 10px;
        font-size: 13px;
        font-weight: 700;
        color: #26215c;
    }

    .lead-stage-reminder-title i {
        color: #5b52e0;
        font-size: 16px;
    }

    .lead-stage-reminder-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .lead-stage-reminder label {
        display: block;
        margin: 0 0 4px;
        font-size: 12px;
        font-weight: 600;
        color: #4b5163;
    }

    .lead-stage-reminder label .req {
        color: #dc3545;
    }

    .lead-stage-reminder .form-control {
        height: 40px;
        border-radius: 9px;
        border-color: #e2e5eb;
        font-size: 13.5px;
    }

    .lead-stage-reminder textarea.form-control {
        height: auto;
        min-height: 64px;
        resize: vertical;
    }

    .lead-stage-reminder .form-control:focus {
        border-color: #5b52e0;
        box-shadow: 0 0 0 3px rgba(91, 82, 224, 0.12);
    }

    .lead-stage-reminder-note {
        margin-top: 10px;
    }

    .lead-stage-reminder-help {
        margin: 8px 0 0;
        font-size: 11.5px;
        color: #7a8092;
    }

    @media (max-width: 480px) {
        .lead-stage-reminder-grid {
            grid-template-columns: 1fr;
        }
    }

    .lead-stage-submit-hint i {
        font-size: 16px;
        color: #e07b00;
        flex-shrink: 0;
    }
</style>

<script>
window.LeadStageDialog = (function () {

    // The draft stages offered to this user's role (Stage Management),
    // then Lead Submitted to Pricing. The dialog is for AU Savers only.
    const STAGES = @js(\App\Models\Lead::saveStages(auth()->user(), \App\Models\Product::AU_SAVERS_ID));
    const DRAFT_STAGES = @js(array_keys(\App\Models\Lead::draftStageLabels()));
    const SUBMIT_STAGE = @js(\App\Models\Lead::STATUS_LEAD_SUBMITTED_TO_PRICING);
    const AU_SAVERS_ID = @js(\App\Models\Product::AU_SAVERS_ID);
    const REMINDER_FIELDS = ['date', 'time', 'note'];

    function isAuSavers(productId) {
        return parseInt(productId, 10) === AU_SAVERS_ID;
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = String(value ?? '');
        return div.innerHTML;
    }

    function today() {
        const d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function open(current, reminder) {
        reminder = reminder || {};

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
                <div id="leadStageReminder" class="lead-stage-reminder" hidden>
                    <p class="lead-stage-reminder-title"><i class="mdi mdi-alarm"></i> Set a reminder to follow up</p>
                    <div class="lead-stage-reminder-grid">
                        <div>
                            <label for="leadStageReminderDate">Date <span class="req">*</span></label>
                            <input type="date" id="leadStageReminderDate" class="form-control" min="${today()}" value="${escapeHtml(reminder.date)}">
                        </div>
                        <div>
                            <label for="leadStageReminderTime">Time <span class="req">*</span></label>
                            <input type="time" id="leadStageReminderTime" class="form-control" value="${escapeHtml(reminder.time)}">
                        </div>
                    </div>
                    <div class="lead-stage-reminder-note">
                        <label for="leadStageReminderNote">Notes <span class="req">*</span></label>
                        <textarea id="leadStageReminderNote" class="form-control" maxlength="2000" placeholder="What to follow up on">${escapeHtml(reminder.note)}</textarea>
                    </div>
                    <p class="lead-stage-reminder-help">You'll be reminded on your dashboard, and it shows in the lead's Reminders.</p>
                </div>
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
                const reminderBox = document.getElementById('leadStageReminder');
                const sync = () => {
                    hint.hidden = select.value !== SUBMIT_STAGE;
                    reminderBox.hidden = !DRAFT_STAGES.includes(select.value);
                    Swal.resetValidationMessage();
                };

                select.addEventListener('change', sync);
                sync();
            },
            preConfirm: () => {
                const value = document.getElementById('leadStageSelect').value;

                if (!value) {
                    Swal.showValidationMessage('Please select a stage.');
                    return false;
                }

                if (!DRAFT_STAGES.includes(value)) {
                    return { stage: value, reminder: null };
                }

                const date = document.getElementById('leadStageReminderDate').value;
                const time = document.getElementById('leadStageReminderTime').value;
                const note = document.getElementById('leadStageReminderNote').value.trim();

                if (!date || !time) {
                    Swal.showValidationMessage('Please set the reminder date and time for ' + STAGES[value] + '.');
                    return false;
                }

                if (date < today()) {
                    Swal.showValidationMessage('Reminder date cannot be in the past.');
                    return false;
                }

                if (!note) {
                    Swal.showValidationMessage('Please enter the reminder notes for ' + STAGES[value] + '.');
                    document.getElementById('leadStageReminderNote').focus();
                    return false;
                }

                return { stage: value, reminder: { date, time, note } };
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

    // Hidden stage_reminder_* inputs on the form - created on first use.
    function reminderInput(form, field) {
        const name = 'stage_reminder_' + field;
        let input = form.querySelector('input[name="' + name + '"]');

        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            form.appendChild(input);
        }

        return input;
    }

    function currentReminder(form) {
        const reminder = {};
        REMINDER_FIELDS.forEach((field) => { reminder[field] = reminderInput(form, field).value; });
        return reminder;
    }

    function setReminder(form, reminder) {
        REMINDER_FIELDS.forEach((field) => { reminderInput(form, field).value = reminder ? (reminder[field] || '') : ''; });
    }

    function intercept(event, form) {
        const input = form.querySelector('input[name="lead_stage"]');

        if (!input) {
            return false;
        }

        // Not an AU Savers draft - no stage is sent (the server would refuse one).
        if (!applies(form)) {
            input.value = '';
            setReminder(form, null);
            return false;
        }

        // Second pass, after the dialog was confirmed - let it through.
        if (form.dataset.stageConfirmed === '1') {
            form.dataset.stageConfirmed = '';
            return false;
        }

        event.preventDefault();

        const submitter = event.submitter;

        open(input.value || input.dataset.current || '', currentReminder(form)).then((choice) => {
            if (!choice) {
                return;
            }

            input.value = choice.stage;
            setReminder(form, choice.reminder);
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
