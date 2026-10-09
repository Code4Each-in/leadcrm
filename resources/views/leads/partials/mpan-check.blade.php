{{--
    Add Lead's duplicate-MPAN check. Before the form is saved, the MPANs
    entered - the MPAN field, or every MPAN in a Multiple Site sites CSV
    - are looked up (POST leads.mpanMatches). When other leads already
    hold one, the user is asked to confirm; "Yes, create it" saves the
    lead with confirm_duplicate_mpan=1 and it is flagged as a duplicate
    ("D"). The server refuses a duplicate MPAN without that confirmation
    (LeadController::store()), so this only collects the answer.

    Defines window.LeadMpanCheck.intercept(event, form) - for the form's
    submit handler, once its own checks have passed; true when it has
    taken over the submission. Expects a hidden
    input[name="confirm_duplicate_mpan"] in the form.

    The confirmation is the leads pages' soft alert (same popup as the
    confirmations on the lead's page and the listing - see
    partials/soft-alert-styles).
--}}
@include('leads.partials.soft-alert-styles')

<style>
    /* The matching leads, one row per MPAN */
    .mpan-match-list {
        list-style: none;
        margin: 12px 0 10px;
        padding: 0;
        text-align: left;
    }

    .mpan-match-list li {
        padding: 9px 12px;
        border: 1px solid #f1e3d0;
        border-radius: 10px;
        background: #fffaf3;
        font-size: 13px;
        color: #384153;
        word-break: break-word;
    }

    .mpan-match-list li + li {
        margin-top: 6px;
    }

    .mpan-match-list .mpan-match-mpan {
        display: block;
        font-weight: 700;
        color: #1a1f2b;
        margin-bottom: 2px;
    }

    .mpan-match-list a {
        color: #5b52e0;
        font-weight: 600;
    }
</style>

<script>
window.LeadMpanCheck = (function () {

    const URL = @json(route('leads.mpanMatches'));

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = String(value ?? '');
        return div.innerHTML;
    }

    function enteredMpans(form) {
        const sites = form.querySelector('#number_of_sites');

        if (sites && sites.value === 'Multiple Site') {
            return typeof window.getSitesCsvMpans === 'function' ? window.getSitesCsvMpans() : [];
        }

        const mpan = form.querySelector('#mpan');

        return mpan && !mpan.disabled && mpan.value.trim() ? [mpan.value.trim()] : [];
    }

    function confirmDuplicates(matches) {
        const rows = matches.map(function (match) {
            const leads = match.leads.map(function (lead) {
                const label = `Lead #${escapeHtml(lead.display_id)}` + (lead.name ? ` - ${escapeHtml(lead.name)}` : '');

                return lead.url
                    ? `<a href="${escapeHtml(lead.url)}" target="_blank" rel="noopener">${label}</a>`
                    : label;
            }).join(', ');

            return `<li><span class="mpan-match-mpan">MPAN ${escapeHtml(match.mpan)}</span>Already used by ${leads}</li>`;
        }).join('');

        const one = matches.length === 1;

        return Swal.fire({
            html: `
                <div class="swal-delete-icon tone-warning">
                    <i class="mdi mdi-content-duplicate"></i>
                </div>
                <h2 class="swal-delete-title">${one ? 'Duplicate MPAN found' : 'Duplicate MPANs found'}</h2>
                <p class="swal-delete-text">We already have a lead with ${one ? 'this MPAN' : 'these MPANs'}:</p>
                <ul class="mpan-match-list">${rows}</ul>
                <p class="swal-delete-text">Do you really want to create this lead? It will be flagged as a duplicate (<strong>D</strong>).</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, create it',
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'swal-leads-popup',
                confirmButton: 'swal-btn-warning',
                cancelButton: 'swal-btn-cancel',
            },
        }).then(result => result.isConfirmed);
    }

    function intercept(event, form) {
        const input = form.querySelector('input[name="confirm_duplicate_mpan"]');
        const mpans = enteredMpans(form);

        if (!input || mpans.length === 0) {
            if (input) input.value = '';
            return false;
        }

        // Already checked for exactly these MPANs (and confirmed, if
        // they were duplicates) - let it through.
        const key = mpans.slice().sort().join(',');

        if (form.dataset.mpanCheckedFor === key) {
            return false;
        }

        event.preventDefault();

        const submitter = event.submitter;
        const proceed = function (duplicate) {
            input.value = duplicate ? '1' : '';
            form.dataset.mpanCheckedFor = key;
            form.requestSubmit(submitter && submitter.form === form ? submitter : undefined);
        };

        fetch(URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ mpans: mpans }),
        })
            .then(res => (res.ok ? res.json() : { matches: [] }))
            .then(function (data) {
                const matches = (data && data.matches) || [];

                if (matches.length === 0) {
                    proceed(false);
                    return;
                }

                confirmDuplicates(matches).then(function (confirmed) {
                    if (confirmed) proceed(true);
                });
            })
            // Couldn't check - save anyway; the server still refuses an
            // unconfirmed duplicate, with the reason.
            .catch(() => proceed(false));

        return true;
    }

    return { intercept };
})();
</script>
