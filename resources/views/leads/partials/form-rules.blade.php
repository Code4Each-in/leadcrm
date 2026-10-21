{{--
    Add / Edit Lead - shared field rules (client side), mirroring
    App\Support\LeadValidationRules:

    - Publishing (or submitting an AU Savers lead to pricing) needs every
      field except Mobile No. and Date of Birth; drafts may be saved
      incomplete. Required fields get a red * for the selected product.
    - AU Savers: MPAN / MPRN / SPID each have a checkbox that enables its
      input; a ticked meter needs a value (on any save), and publishing
      needs at least one ticked + filled meter (Single Site).
    - Fuel Type is a group of checkbox chips (fuel_types[]).
    - Server-side errors are shown under their own fields.

    The form's own submit handler calls LeadFormRules.validate(event)
    and treats a true result as "has errors".
--}}
<style>
    .req-star {
        margin-left: 2px;
        color: #dc3545;
    }

    .req-star[hidden] {
        display: none;
    }

    /* Meter group */
    .meter-group {
        padding: 16px 18px 18px;
        border: 1px solid #e6e8f1;
        border-radius: 12px;
        background: #fafbfe;
        transition: border-color .2s, background .2s;
    }

    .meter-group.has-error {
        border-color: #f1aeb5;
        background: #fff8f8;
    }

    .meter-group-head {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        gap: 4px 10px;
        margin-bottom: 12px;
    }

    .meter-group-title {
        font-size: 14px;
        font-weight: 700;
        color: #1f2335;
    }

    .meter-group-hint {
        font-size: 12px;
        color: #7a8092;
    }

    .meter-group > .js-field-error {
        margin-top: 10px !important;
    }

    .meter-col + .meter-col {
        margin-top: 0;
    }

    @media (max-width: 767.98px) {
        .meter-col + .meter-col {
            margin-top: 14px;
        }
    }

    .meter-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 8px;
        cursor: pointer;
        user-select: none;
        font-weight: 600;
    }

    .meter-toggle-input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
    }

    .meter-toggle-box {
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #c3c7d6;
        border-radius: 5px;
        background: #fff;
        color: #fff;
        font-size: 13px;
        transition: background .15s, border-color .15s;
    }

    .meter-toggle-box i {
        opacity: 0;
        line-height: 1;
    }

    .meter-toggle-input:checked + .meter-toggle-box {
        background: #4b49ac;
        border-color: #4b49ac;
    }

    .meter-toggle-input:checked + .meter-toggle-box i {
        opacity: 1;
    }

    .meter-toggle-input:focus-visible + .meter-toggle-box {
        box-shadow: 0 0 0 3px rgba(75, 73, 172, .2);
    }

    .meter-col input.form-control:disabled {
        background: #f1f2f6;
        color: #9aa0ae;
        cursor: not-allowed;
    }

    /* Fuel Type - one chip card per fuel, as tall as the other inputs */
    .fuel-type-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    /* Beats the forms' own `.form-group label` margin. */
    .fuel-type-options .fuel-type-option {
        flex: 1 1 0;
        min-width: 120px;
        margin: 0;
        cursor: pointer;
        user-select: none;
    }

    .fuel-type-input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
    }

    .fuel-type-card {
        display: flex;
        align-items: center;
        gap: 10px;
        height: 46px;
        padding: 0 12px 0 8px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        background: #fff;
        transition: border-color .15s, background .15s, box-shadow .15s;
    }

    .fuel-type-option:hover .fuel-type-card {
        border-color: #b9b7e6;
        background: #fafaff;
    }

    .fuel-type-icon {
        width: 30px;
        height: 30px;
        flex: 0 0 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 17px;
        line-height: 1;
        background: var(--fuel-soft);
        color: var(--fuel-color);
    }

    .fuel-type-electricity { --fuel-color: #c98a00; --fuel-soft: #fff5d9; }
    .fuel-type-gas         { --fuel-color: #e0612b; --fuel-soft: #ffece3; }
    .fuel-type-water       { --fuel-color: #1f86d1; --fuel-soft: #e4f2fd; }

    .fuel-type-name {
        flex: 1;
        font-size: 14px;
        font-weight: 600;
        color: #1f2335;
    }

    .fuel-type-check {
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #c3c7d6;
        border-radius: 5px;
        background: #fff;
        color: #fff;
        font-size: 13px;
        transition: background .15s, border-color .15s;
    }

    .fuel-type-check i {
        opacity: 0;
        line-height: 1;
    }

    .fuel-type-input:checked + .fuel-type-card {
        border-color: #4b49ac;
        background: #f6f5ff;
        box-shadow: 0 0 0 1px #4b49ac inset;
    }

    .fuel-type-input:checked + .fuel-type-card .fuel-type-check {
        background: #4b49ac;
        border-color: #4b49ac;
    }

    .fuel-type-input:checked + .fuel-type-card .fuel-type-check i {
        opacity: 1;
    }

    .fuel-type-input:focus-visible + .fuel-type-card {
        box-shadow: 0 0 0 3px rgba(75, 73, 172, .2);
    }

    .fuel-type-hint {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: #7a8092;
    }

    #fuel-types-wrapper.has-error .fuel-type-card {
        border-color: #dc3545;
    }

    #fuel-types-wrapper.has-error .fuel-type-hint {
        display: none;
    }
</style>

<script>
window.LeadFormRules = (function () {

    const REQUIRED = @json(\App\Support\LeadValidationRules::REQUIRED_ON_PUBLISH);
    const LABELS = @json(\App\Support\LeadValidationRules::FIELD_LABELS);
    const METERS = @json(\App\Support\LeadValidationRules::METERS);
    const METER_REQUIRED = @json(\App\Support\LeadValidationRules::METER_REQUIRED_MESSAGE);
    const SUBMIT_STAGE = @json(\App\Models\Lead::STATUS_LEAD_SUBMITTED_TO_PRICING);
    const AU_SAVERS_ID = {{ \App\Models\Product::AU_SAVERS_ID }};
    const RADIO_FIELDS = ['home_owner', 'vat_registered', 'loan_purpose'];

    let form = null;

    function $(selector) { return form.querySelector(selector); }

    function productId() {
        const checked = $('input[name="product_id"]:checked');
        return checked ? parseInt(checked.value, 10) : null;
    }

    function isSiteLead() {
        const marker = $('input[name="meter_toggles"]');
        return !!marker && marker.value === 'site';
    }

    function hasOwnMeters() {
        const sites = $('#number_of_sites');
        return productId() === AU_SAVERS_ID && (isSiteLead() || !sites || sites.value !== 'Multiple Site');
    }

    function requiredFields() {
        const pid = productId();
        let fields = REQUIRED.common.slice();

        if (pid === 1 || pid === 2) {
            fields = fields.concat(REQUIRED.funding);
            const purpose = $('input[name="loan_purpose"]:checked');
            if (purpose && purpose.value === 'Other') {
                fields.push('funds_usage_details');
            }
        }

        if (pid === AU_SAVERS_ID) {
            fields = fields.concat(REQUIRED.au_savers);
        }

        return fields;
    }

    function control(name) {
        return $('[name="' + name + '"]:not([type="hidden"])') || $('[name="' + name + '[]"]');
    }

    // Where a field's error goes.
    function container(name) {
        if (name === 'meters') {
            return $('[data-meter-group]');
        }
        if (name === 'loan_purpose') {
            return $('.loan-purpose-section');
        }
        const el = control(name);
        return el ? el.closest('.form-group') : null;
    }

    function isEmpty(name) {
        if (RADIO_FIELDS.includes(name)) {
            return !$('input[name="' + name + '"]:checked');
        }
        const el = control(name);
        if (!el) {
            return false;
        }
        if (el.multiple) {
            return el.selectedOptions.length === 0;
        }
        if (el.type === 'checkbox') {
            return !form.querySelector('input[name="' + el.name + '"]:checked');
        }
        return el.value.trim() === '';
    }

    function showError(name, message) {
        const box = container(name);
        if (!box) {
            return;
        }

        clearError(name);

        const error = document.createElement('div');
        error.className = 'js-field-error text-danger mt-1';
        error.style.fontSize = '0.8rem';
        error.dataset.field = name;
        error.textContent = message;
        box.appendChild(error);

        if (name === 'meters') {
            box.classList.add('has-error');
        } else if (RADIO_FIELDS.includes(name)) {
            box.classList.add('radio-group-error');
        } else if (name === 'fuel_types') {
            $('#fuel-types-wrapper').classList.add('has-error');
        } else {
            const el = control(name);
            if (el) {
                el.classList.add('is-invalid');
            }
        }
    }

    function clearError(name) {
        const box = container(name);
        if (!box) {
            return;
        }

        // The meter group's own error is a direct child; each meter's
        // sits in its own form-group inside the group.
        (name === 'meters'
            ? box.querySelectorAll(':scope > .js-field-error')
            : box.querySelectorAll('.js-field-error, .validation-error')
        ).forEach(function (error) { error.remove(); });

        box.classList.remove('has-error', 'radio-group-error');

        if (name === 'fuel_types') {
            $('#fuel-types-wrapper')?.classList.remove('has-error');
        }

        const el = control(name);
        if (el) {
            el.classList.remove('is-invalid');
        }
    }

    // ---- Required * on labels, for the selected product ----

    function labelFor(name) {
        if (name === 'loan_purpose') {
            return $('.loan-purpose-title');
        }
        if (name === 'fuel_types') {
            return $('#fuel-types-label');
        }
        if (RADIO_FIELDS.includes(name)) {
            return container(name)?.querySelector('.radio-field-label');
        }
        const el = control(name);
        return el && el.id ? $('label[for="' + el.id + '"]') : null;
    }

    function markRequired() {
        const all = [].concat(REQUIRED.common, REQUIRED.funding, REQUIRED.au_savers, ['funds_usage_details']);
        const active = requiredFields().concat(
            (productId() === 1 || productId() === 2) ? ['funds_usage_details'] : []
        );

        all.forEach(function (name) {
            const label = labelFor(name);
            if (!label) {
                return;
            }
            let star = label.querySelector('.req-star');
            if (!star) {
                star = document.createElement('span');
                star.className = 'req-star';
                star.textContent = '*';
                star.title = 'Required to publish';
                label.appendChild(star);
            }
            star.hidden = !active.includes(name);
        });
    }

    // ---- Meters ----

    function meterInput(meter) { return $('#' + meter); }
    function meterToggle(meter) { return $('[data-meter-toggle="' + meter + '"]'); }

    function syncMeter(meter) {
        const toggle = meterToggle(meter);
        const input = meterInput(meter);
        if (!toggle || !input) {
            return;
        }
        // Multiple Site (unexpanded): sites-csv owns these inputs.
        if (!hasOwnMeters()) {
            return;
        }
        input.disabled = !toggle.checked;
        if (!toggle.checked) {
            clearError(meter);
        }
    }

    function syncMeterGroup() {
        const wrapper = $('#meter-fields');
        if (wrapper) {
            wrapper.style.display = hasOwnMeters() || productId() !== AU_SAVERS_ID ? '' : 'none';
        }
        METERS.forEach(syncMeter);
    }

    function validateTickedMeters() {
        let hasError = false;

        if (productId() !== AU_SAVERS_ID || !hasOwnMeters()) {
            return false;
        }

        METERS.forEach(function (meter) {
            const toggle = meterToggle(meter);
            const input = meterInput(meter);
            if (toggle && input && toggle.checked && input.value.trim() === '') {
                const label = LABELS[meter];
                showError(meter, 'Please enter the ' + label + ', or untick ' + label + '.');
                hasError = true;
            }
        });

        return hasError;
    }

    function hasAnyMeter() {
        return METERS.some(function (meter) {
            const toggle = meterToggle(meter);
            const input = meterInput(meter);
            return toggle && input && toggle.checked && input.value.trim() !== '';
        });
    }

    // ---- Publishing? ----

    function isPublishing(event) {
        const stageInput = $('input[name="lead_stage"]');

        // AU Savers draft: the Save dialog decides, on its second pass.
        if (productId() === AU_SAVERS_ID && form.dataset.stageDialog === 'on' && stageInput) {
            return form.dataset.stageConfirmed === '1' && stageInput.value === SUBMIT_STAGE;
        }

        const submitter = event && event.submitter;
        return !!submitter && submitter.name === 'status' && submitter.value === 'published';
    }

    /**
     * Runs from the form's submit handler. Returns true when there
     * are errors (they're already shown).
     */
    function validate(event) {
        // Start from a clean slate - errors this partial showed on an
        // earlier attempt (e.g. a refused publish) may not apply to this
        // one (e.g. saving a draft).
        form.querySelectorAll('.js-field-error[data-field]').forEach(function (error) {
            clearError(error.dataset.field);
        });

        let hasError = validateTickedMeters();

        if (!isPublishing(event)) {
            return hasError;
        }

        requiredFields().forEach(function (name) {
            if (isEmpty(name)) {
                showError(name, name === 'fuel_types'
                    ? 'Please select at least one fuel type.'
                    : (LABELS[name] || name) + ' is required.');
                hasError = true;
            }
        });

        if (productId() === AU_SAVERS_ID && hasOwnMeters() && !hasAnyMeter()) {
            showError('meters', METER_REQUIRED);
            hasError = true;
        }

        return hasError;
    }

    // Errors from the server, under their own fields.
    function showServerErrors(errors) {
        Object.keys(errors || {}).forEach(function (key) {
            const name = key.replace(/\.\d+$/, '');
            const box = container(name);
            // Some fields print their own server error already (Blade error blocks).
            if (box && (name === 'meters' || !box.querySelector('.validation-error'))) {
                showError(name, errors[key][0]);
            }
        });
    }

    function init(theForm, options) {
        form = theForm;
        if (!form) {
            return;
        }

        // Fuel Type chips
        form.querySelectorAll('input[name="fuel_types[]"]').forEach(function (box) {
            box.addEventListener('change', function () { clearError('fuel_types'); });
        });

        // Meter checkboxes
        METERS.forEach(function (meter) {
            const toggle = meterToggle(meter);
            const input = meterInput(meter);
            if (!toggle || !input) {
                return;
            }
            toggle.addEventListener('change', function () {
                syncMeter(meter);
                clearError('meters');
                if (toggle.checked) {
                    input.focus();
                }
            });
            input.addEventListener('input', function () {
                if (this.value.trim()) {
                    clearError('meters');
                }
            });
        });

        // Clear a required field's error once it's filled.
        [].concat(REQUIRED.common, REQUIRED.funding, ['number_of_sites', 'funds_usage_details']).forEach(function (name) {
            const nodes = RADIO_FIELDS.includes(name)
                ? form.querySelectorAll('input[name="' + name + '"]')
                : [control(name)].filter(Boolean);

            nodes.forEach(function (node) {
                node.addEventListener(node.tagName === 'SELECT' || node.type === 'radio' ? 'change' : 'input', function () {
                    if (!isEmpty(name)) {
                        clearError(name);
                    }
                });
            });
        });

        form.querySelectorAll('input[name="product_id"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                markRequired();
                syncMeterGroup();
            });
        });

        form.querySelectorAll('input[name="loan_purpose"]').forEach(function (radio) {
            radio.addEventListener('change', markRequired);
        });

        const sites = $('#number_of_sites');
        if (sites) {
            // After sites-csv's own handler has shown / hidden its fields.
            sites.addEventListener('change', function () { setTimeout(syncMeterGroup, 0); });
        }

        markRequired();
        syncMeterGroup();
        showServerErrors((options && options.errors) || {});
    }

    return { init, validate, showError, clearError };
})();
</script>
