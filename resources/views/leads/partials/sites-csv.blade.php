{{--
    Multiple Site "sites CSV" upload (Supply Address, MPAN, MPRN, SPID -
    one row per site), shared by Add Lead and Edit (unexpanded Multiple
    Site drafts only). Expects #number_of_sites, #sites_count and the
    site fields (#mpan, #mprn, #spid) on the page, inside a form with
    enctype="multipart/form-data".

    With Multiple Site selected, each site's Supply Address / MPAN /
    MPRN / SPID comes from the CSV, so this hides and disables the
    MPAN / MPRN / SPID form fields (a disabled input
    isn't submitted). Their values are put aside and restored if Single
    Site is chosen again. The form's own Supply Address field (shown
    for every product) is left alone - it is never copied onto a site,
    so a site whose CSV row has no Supply Address keeps it blank.

    The browser checks the row count, each row's MPAN / MPRN / SPID /
    Supply Address and duplicate MPANs as soon as a file is
    chosen and blocks saving (Draft or Publish) without a valid
    CSV; the server
    (App\Support\MultisiteSitesCsv) re-checks everything, including
    MPANs already used by other leads.

    @param string|null $numberOfSites  current Number of Sites value
    @param int         $heldSitesCount sites already held from an earlier
                                       upload (Edit), 0 if none
--}}
@php
    $heldSitesCount = (int) ($heldSitesCount ?? 0);
    $sitesCsvErrors = $errors->get('sites_csv');

    // Per-site field messages the browser shows - the same text the
    // server's MultisiteSitesCsv::validate() uses.
    $siteFieldMessages = \Illuminate\Support\Arr::only(\App\Support\LeadValidationRules::messages(), [
        'mpan.digits', 'mprn.digits_between', 'spid.digits_between', 'supply_address.max',
    ]);
@endphp

<div class="col-md-12" id="sites-csv-wrapper" style="{{ ($numberOfSites ?? null) === 'Multiple Site' ? '' : 'display:none;' }}">
    <div class="form-group sites-csv">

        <div class="sites-csv-head">
            <label for="sites_csv">
                Sites CSV<span class="text-danger">*</span>
            </label>

            <a href="{{ route('leads.sites.template') }}" class="sites-csv-template">
                <i class="mdi mdi-download"></i>
                Download template
            </a>
        </div>

        {{-- Same file-choose control as the Lead Import page (and Notes &
             Documents, Profile Photo, Users) - readonly filename + Browse
             button, click anywhere to open the hidden native input. --}}
        <div class="file-upload-field {{ $sitesCsvErrors ? 'has-error' : '' }}" id="sites-csv-field">
            <input type="text" class="file-upload-info" id="sites-csv-filename" placeholder="No file chosen" readonly>
            <input
                type="file"
                name="sites_csv"
                id="sites_csv"
                accept=".csv,text/csv"
                class="file-upload-default"
            >
            <button type="button" class="file-upload-browse">
                <i class="mdi mdi-paperclip"></i>
                Browse
            </button>
        </div>

        <small class="form-text text-muted sites-csv-help">
            Columns: <strong>Supply Address, MPAN, MPRN, SPID</strong> - one row per site (the header row is not counted).
            The number of rows must match the Number of Sites to Create, and every site needs its own MPAN (exactly 13 digits).
            MPRN (6-8 digits) and SPID (8-10 digits) are optional - left blank, the lead's own value is used.
            A Supply Address left blank stays blank for that site.
            <strong>Required</strong> - for Drafts as well as Published leads.
        </small>

        @if ($heldSitesCount > 0)
            <div id="sites-csv-held" class="sites-csv-note sites-csv-note-success">
                <i class="mdi mdi-check-circle-outline"></i>
                {{ $heldSitesCount }} {{ \Illuminate\Support\Str::plural('site', $heldSitesCount) }} already uploaded. Choose a new file only to replace them.
            </div>
        @endif

        <div id="sites-csv-status" class="sites-csv-note"></div>

        @if ($sitesCsvErrors)
            <div class="validation-error" id="sites-csv-server-errors">
                @if (count($sitesCsvErrors) === 1)
                    {{ $sitesCsvErrors[0] }}
                @else
                    <ul class="sites-csv-error-list">
                        @foreach ($sitesCsvErrors as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
                <div>Please choose the CSV file again.</div>
            </div>
        @endif

    </div>
</div>

<style>
    /* Scoped to the partial - neither Add Lead nor Edit defines the
       shared .file-upload-field control itself (it lives per page, see
       leads/import.blade.php), so the same rules are repeated here. */
    #sites-csv-wrapper .sites-csv-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 0.4rem;
    }

    #sites-csv-wrapper .sites-csv-head label {
        margin-bottom: 0;
    }

    /* Soft action - same indigo / tint as the Browse button. */
    #sites-csv-wrapper .sites-csv-template {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        background: #fff;
        color: #5b52e0;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.12s ease, border-color 0.12s ease;
    }

    #sites-csv-wrapper .sites-csv-template:hover,
    #sites-csv-wrapper .sites-csv-template:focus {
        background: #eef0ff;
        border-color: #d9dcff;
        color: #5b52e0;
    }

    #sites-csv-wrapper .file-upload-field {
        position: relative;
        display: flex;
        align-items: stretch;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.12s ease, box-shadow 0.12s ease;
    }

    #sites-csv-wrapper .file-upload-field:hover,
    #sites-csv-wrapper .file-upload-field:focus-within {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.12);
    }

    #sites-csv-wrapper .file-upload-field.has-error {
        border-color: #d33a3a;
    }

    #sites-csv-wrapper .file-upload-default {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        overflow: hidden;
    }

    #sites-csv-wrapper .file-upload-info {
        flex: 1 1 auto;
        min-width: 0;
        border: none;
        background: #fff;
        color: #8a92a3;
        font-size: 13.5px;
        padding: 10px 12px;
        cursor: pointer;
    }

    #sites-csv-wrapper .file-upload-info:focus {
        outline: none;
    }

    #sites-csv-wrapper .file-upload-info.has-file {
        color: #384153;
    }

    #sites-csv-wrapper .file-upload-browse {
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

    #sites-csv-wrapper .file-upload-browse:hover {
        background: #eef0ff;
    }

    #sites-csv-wrapper .sites-csv-help {
        display: block;
        margin-top: 6px;
        line-height: 1.5;
    }

    #sites-csv-wrapper .sites-csv-note {
        font-size: 0.78rem;
        margin-top: 5px;
    }

    #sites-csv-wrapper .sites-csv-note:empty {
        display: none;
    }

    #sites-csv-wrapper .sites-csv-note-success {
        color: #1e8e5a;
    }

    #sites-csv-wrapper .sites-csv-error-list {
        margin: 0;
        padding-left: 1.1rem;
    }
</style>

<script>
// Deferred to DOMContentLoaded: the site fields after this partial
// (MPAN, MPRN, SPID) aren't parsed yet when it runs.
document.addEventListener('DOMContentLoaded', function () {

    const numberOfSites = document.getElementById('number_of_sites');
    const sitesCount = document.getElementById('sites_count');
    const fileInput = document.getElementById('sites_csv');
    const wrapper = document.getElementById('sites-csv-wrapper');
    const field = document.getElementById('sites-csv-field');
    const status = document.getElementById('sites-csv-status');
    const fileName = document.getElementById('sites-csv-filename');
    const form = fileInput ? fileInput.form : null;

    // Per-site fields - with Multiple Site they come from the CSV.
    const siteInputs = ['mpan', 'mprn', 'spid']
        .map(id => document.getElementById(id))
        .filter(Boolean);

    const heldSitesCount = {{ $heldSitesCount }};

    const MESSAGES = {
        required: @json(\App\Support\LeadValidationRules::SITES_CSV_REQUIRED_MESSAGE),
        count: @json(\App\Support\LeadValidationRules::SITES_COUNT_MISMATCH_MESSAGE),
        duplicate: @json(\App\Support\LeadValidationRules::DUPLICATE_MPAN_MESSAGE),
        columns: @json('The sites CSV must have the columns: ' . implode(', ', \App\Support\MultisiteSitesCsv::COLUMNS) . '.'),
        overflow: @json(\App\Support\MultisiteSitesCsv::OVERFLOW_MESSAGE),
        field: @json($siteFieldMessages),
    };

    // Same per-site rules as MultisiteSitesCsv::validate() - MPAN is
    // required, the rest may be blank (the lead's own value is used).
    const SITE_RULES = [
        { key: 'mpan', test: v => /^\d{13}$/.test(v), message: MESSAGES.field['mpan.digits'] },
        { key: 'mprn', test: v => /^\d{6,8}$/.test(v), message: MESSAGES.field['mprn.digits_between'] },
        { key: 'spid', test: v => /^\d{8,10}$/.test(v), message: MESSAGES.field['spid.digits_between'] },
        { key: 'supply_address', test: v => v.length <= 2000, message: MESSAGES.field['supply_address.max'] },
    ];

    if (!numberOfSites || !fileInput || !form) {
        return;
    }

    // Sites read from the chosen file - null until one is chosen.
    let sites = null;
    let readError = null;

    // The MPANs in the chosen sites CSV, for Add Lead's duplicate-MPAN
    // check (partials/mpan-check) - empty when none is chosen.
    window.getSitesCsvMpans = function () {
        return (sites || []).map(site => site.mpan).filter(Boolean);
    };

    function isMultiple() {
        return numberOfSites.value === 'Multiple Site';
    }

    function expectedCount() {
        const value = sitesCount ? sitesCount.value.trim() : '';
        return /^\d+$/.test(value) ? parseInt(value, 10) : null;
    }

    function setFileName(name) {
        fileName.value = name || '';
        fileName.classList.toggle('has-file', Boolean(name));
    }

    // Minimal RFC 4180 parser - quoted fields may contain commas,
    // quotes ("") and line breaks (e.g. a multi-line Supply Address).
    function parseCsv(text) {
        const rows = [];
        let row = [];
        let value = '';
        let inQuotes = false;

        text = text.replace(/^﻿/, '');

        for (let i = 0; i < text.length; i++) {
            const char = text[i];

            if (inQuotes) {
                if (char === '"' && text[i + 1] === '"') {
                    value += '"';
                    i++;
                } else if (char === '"') {
                    inQuotes = false;
                } else {
                    value += char;
                }
            } else if (char === '"') {
                inQuotes = true;
            } else if (char === ',') {
                row.push(value);
                value = '';
            } else if (char === '\n' || char === '\r') {
                if (char === '\r' && text[i + 1] === '\n') {
                    i++;
                }
                row.push(value);
                rows.push(row);
                row = [];
                value = '';
            } else {
                value += char;
            }
        }

        if (value !== '' || row.length) {
            row.push(value);
            rows.push(row);
        }

        return rows;
    }

    function readFile(file) {
        const reader = new FileReader();

        reader.onload = function () {
            const rows = parseCsv(String(reader.result));
            const header = (rows.shift() || []).map(h => h.trim().toLowerCase().replace(/\s+/g, '_'));
            if (header.indexOf('mpan') === -1) {
                sites = null;
                readError = MESSAGES.columns;
            } else {
                readError = null;
                sites = rows
                    .filter(cells => cells.some(cell => cell.trim() !== ''))
                    .map(function (cells) {
                        const site = {
                            overflow: cells.slice(header.length).some(cell => cell.trim() !== ''),
                        };

                        ['supply_address', 'mpan', 'mprn', 'spid'].forEach(function (key) {
                            const index = header.indexOf(key);
                            site[key] = index === -1 ? '' : (cells[index] || '').trim();
                        });

                        return site;
                    });
            }

            render();
        };

        reader.onerror = function () {
            sites = null;
            readError = 'Could not read the sites CSV. Please make sure it is a valid CSV file.';
            render();
        };

        reader.readAsText(file);
    }

    // Problems with the chosen file, as the server would report them
    // (bar MPANs already used by other leads - only the server knows).
    function problems() {
        if (readError) {
            return [readError];
        }

        if (sites === null) {
            return [];
        }

        const errors = [];
        const expected = expectedCount();

        // Row numbers as in the file (row 1 is the header) - blank
        // rows are skipped, same as the server, so these match its
        // messages for any file without blank rows in the middle.
        sites.forEach(function (site, i) {
            if (site.overflow) {
                errors.push(`Row ${i + 2}: ${MESSAGES.overflow}`);
            }
        });

        if (expected === null) {
            errors.push('Please enter the Number of Sites to Create first.');
        } else if (sites.length !== expected) {
            errors.push(`${MESSAGES.count} (expected ${expected}, found ${sites.length}).`);
        }

        const seen = {};

        sites.forEach(function (site, i) {
            const row = i + 2;

            if (!site.mpan) {
                errors.push(`Row ${row}: MPAN is required.`);
                return;
            }

            SITE_RULES.forEach(function (rule) {
                if (site[rule.key] !== '' && !rule.test(site[rule.key])) {
                    errors.push(`Row ${row}: ${rule.message}`);
                }
            });

            if (/^\d{13}$/.test(site.mpan)) {
                (seen[site.mpan] = seen[site.mpan] || []).push(`Row ${row}`);
            }
        });

        Object.keys(seen).forEach(function (mpan) {
            if (seen[mpan].length > 1) {
                errors.push(`${MESSAGES.duplicate} MPAN ${mpan} appears more than once (${seen[mpan].join(', ')}).`);
            }
        });

        return errors;
    }

    function render(extraError) {
        const errors = extraError ? [extraError] : problems();

        status.innerHTML = '';
        status.className = 'sites-csv-note';
        field.classList.toggle('has-error', errors.length > 0 || Boolean(document.getElementById('sites-csv-server-errors')));

        if (errors.length) {
            status.classList.add('validation-error');

            if (errors.length === 1) {
                status.textContent = errors[0];
            } else {
                const list = document.createElement('ul');
                list.className = 'sites-csv-error-list';

                errors.forEach(function (message) {
                    const item = document.createElement('li');
                    item.textContent = message;
                    list.appendChild(item);
                });

                status.appendChild(list);
            }

        } else if (sites !== null) {
            status.classList.add('sites-csv-note-success');
            status.textContent = `✓ ${sites.length} ${sites.length === 1 ? 'site' : 'sites'} loaded - each with a valid MPAN.`;
        }
    }

    // Hides + disables a site field (its value put aside, so the
    // form's own client-side checks skip it and it isn't submitted),
    // or brings it back with that value.
    function setSiteInput(input, multiple) {
        const column = input.closest('[class*="col-"]');

        if (column) {
            column.style.display = multiple ? 'none' : '';
        }

        if (multiple && !input.disabled) {
            input.dataset.stashedValue = input.value;
            input.value = '';
            input.disabled = true;

            if (typeof clearFieldError === 'function') {
                clearFieldError(input);
            }
        } else if (!multiple && input.disabled) {
            input.disabled = false;
            input.value = input.dataset.stashedValue ?? input.value;
            delete input.dataset.stashedValue;
        }
    }

    function toggle() {
        const multiple = isMultiple();

        wrapper.style.display = multiple ? '' : 'none';

        // Add Lead toggles the count field itself; Edit relies on this.
        const countWrapper = document.getElementById('sites-count-wrapper');

        if (countWrapper) {
            countWrapper.style.display = multiple ? '' : 'none';
        }

        siteInputs.forEach(input => setSiteInput(input, multiple));

        if (!multiple) {
            fileInput.value = '';
            setFileName('');
            sites = null;
            readError = null;
            render();
        }
    }

    // Click anywhere on the control to choose a file - but not the
    // native input's own click, which would open the dialog twice.
    field.addEventListener('click', function (event) {
        if (event.target !== fileInput) {
            fileInput.click();
        }
    });

    fileInput.addEventListener('change', function () {
        const file = this.files && this.files[0];

        document.getElementById('sites-csv-server-errors')?.remove();

        if (!file) {
            setFileName('');
            sites = null;
            readError = null;
            render();
            return;
        }

        setFileName(file.name);
        readFile(file);
    });

    numberOfSites.addEventListener('change', toggle);

    if (sitesCount) {
        sitesCount.addEventListener('input', function () {
            if (sites !== null) {
                render();
            }
        });
    }

    // Capture phase, so this runs before the form's own submit
    // handler - which disables the buttons once it's happy. A blocked
    // submit must stop it from doing that.
    form.addEventListener('submit', function (event) {
        if (!isMultiple()) {
            return;
        }

        let error = null;

        if (problems().length) {
            error = problems()[0];
        } else if (sites === null && !(heldSitesCount > 0 && heldSitesCount === expectedCount())) {
            error = heldSitesCount > 0
                ? `${MESSAGES.count} (expected ${expectedCount() ?? '?'}, uploaded ${heldSitesCount}). Please upload a new sites CSV.`
                : MESSAGES.required;
        }

        if (!error) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        render(problems().length ? null : error);
        wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, true);

    toggle();

});
</script>
