{{--
    Add / Edit Lead - optional Documents section, for every product.
    Each row is a document type + a file; on save each becomes a Notes &
    Documents entry on the lead (LeadController::saveFormExtras()), just
    like an upload on the Lead Details page. Rows left completely empty
    are ignored.

    @param \App\Models\Lead|null $lead  the lead being edited (Edit only)
--}}
@php
    $documentTypes = \App\Models\LeadActivity::DOCUMENT_TYPES;
    $maxDocuments = \App\Support\LeadValidationRules::MAX_FORM_DOCUMENTS;
    $existingDocuments = isset($lead) ? $lead->activities()->whereNotNull('file_path')->count() : 0;
    $documentErrors = collect($errors->getMessages())
        ->filter(fn ($messages, $key) => str_starts_with($key, 'documents'))
        ->flatten()
        ->unique()
        ->values();
@endphp

<div class="form-documents" id="form-documents">

    <div class="section-heading mt-4 mb-3">
        <i class="mdi mdi-paperclip"></i>
        <div>
            <h4 class="card-title mb-1">Documents <span class="form-documents-optional">Optional</span></h4>
            <p class="card-description mb-0">Attach any supporting documents - they're added to the lead's Notes &amp; Documents.</p>
        </div>
    </div>

    @if ($existingDocuments)
        <p class="form-documents-existing">
            <i class="mdi mdi-file-document-multiple-outline"></i>
            This lead already has {{ $existingDocuments }} {{ \Illuminate\Support\Str::plural('document', $existingDocuments) }} -
            <a href="{{ route('leads.show', $lead) }}" target="_blank" rel="noopener">view them on the lead</a>.
        </p>
    @endif

    @if ($documentErrors->isNotEmpty())
        <div class="form-documents-errors" role="alert">
            @foreach ($documentErrors as $message)
                <div>{{ $message }}</div>
            @endforeach
            <div>Files aren't kept when a save fails - please choose them again.</div>
        </div>
    @endif

    <div class="form-documents-rows" id="form-documents-rows"></div>

    <button type="button" class="form-documents-add" id="form-documents-add">
        <i class="mdi mdi-plus"></i> Add document
    </button>
    <small class="form-documents-help">Up to {{ $maxDocuments }} files, 10MB each.</small>

    <template id="form-documents-row-template">
        <div class="form-documents-row">
            <div class="form-group mb-0 form-documents-type">
                <select class="form-select" aria-label="Document type">
                    <option value="">Select document type</option>
                    @foreach ($documentTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0 form-documents-file">
                <label class="form-documents-picker">
                    <input type="file">
                    <i class="mdi mdi-upload"></i>
                    <span class="form-documents-filename">Choose file</span>
                </label>
            </div>
            <button type="button" class="form-documents-remove" aria-label="Remove document">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
    </template>
</div>

<style>
    .form-documents-optional {
        margin-left: 6px;
        padding: 2px 8px;
        border-radius: 10px;
        background: #f1f2f6;
        color: #7a8092;
        font-size: 11px;
        font-weight: 600;
        vertical-align: middle;
    }

    .form-documents-existing {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0 0 12px;
        font-size: 13px;
        color: #5b6070;
    }

    .form-documents-errors {
        margin-bottom: 12px;
        padding: 10px 14px;
        border: 1px solid #f1aeb5;
        border-radius: 10px;
        background: #fff5f5;
        color: #b3261e;
        font-size: 13px;
    }

    .form-documents-rows {
        display: grid;
        gap: 10px;
    }

    .form-documents-rows:not(:empty) {
        margin-bottom: 12px;
    }

    .form-documents-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.3fr) 40px;
        gap: 10px;
        align-items: start;
        padding: 12px;
        border: 1px solid #e6e8f1;
        border-radius: 12px;
        background: #fafbfe;
    }

    .form-documents-picker {
        display: flex;
        align-items: center;
        gap: 8px;
        height: 46px;
        margin: 0;
        padding: 0 14px;
        border: 1px dashed #c9cbe0;
        border-radius: 6px;
        background: #fff;
        color: #5b6070;
        font-size: 13.5px;
        cursor: pointer;
        overflow: hidden;
    }

    .form-documents-picker:hover {
        border-color: #4b49ac;
        color: #4b49ac;
    }

    .form-documents-picker input[type="file"] {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
    }

    .form-documents-picker.has-file {
        border-style: solid;
        border-color: #c9c8f0;
        color: #26215c;
    }

    .form-documents-filename {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .form-documents-picker.is-invalid,
    .form-documents-type .form-select.is-invalid {
        border-color: #dc3545;
    }

    .form-documents-remove {
        width: 40px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e6e8f1;
        border-radius: 6px;
        background: #fff;
        color: #8a90a0;
        font-size: 17px;
    }

    .form-documents-remove:hover {
        border-color: #f1aeb5;
        color: #dc3545;
    }

    .form-documents-add {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border: 1px dashed #b8b6ea;
        border-radius: 8px;
        background: #f8f8ff;
        color: #4b49ac;
        font-size: 13.5px;
        font-weight: 600;
    }

    .form-documents-add:hover:not(:disabled) {
        background: #eeedfe;
    }

    .form-documents-add:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .form-documents-help {
        margin-left: 10px;
        color: #8a90a0;
    }

    @media (max-width: 767.98px) {
        .form-documents-row {
            grid-template-columns: minmax(0, 1fr) 40px;
        }

        .form-documents-file {
            grid-column: 1;
            grid-row: 2;
        }

        .form-documents-remove {
            grid-column: 2;
            grid-row: 1;
        }
    }
</style>

<script>
window.LeadFormDocuments = (function () {
    const MAX = {{ $maxDocuments }};
    const MAX_BYTES = 10 * 1024 * 1024;
    const rowsEl = document.getElementById('form-documents-rows');
    const addBtn = document.getElementById('form-documents-add');
    const template = document.getElementById('form-documents-row-template');
    let counter = 0;

    if (!rowsEl || !addBtn || !template) {
        return { validate: function () { return false; } };
    }

    function rows() { return Array.from(rowsEl.querySelectorAll('.form-documents-row')); }

    function syncAdd() {
        addBtn.disabled = rows().length >= MAX;
    }

    function setError(row, part, message) {
        const group = row.querySelector(part === 'type' ? '.form-documents-type' : '.form-documents-file');
        group.querySelector('.js-field-error')?.remove();
        const target = part === 'type' ? group.querySelector('select') : group.querySelector('.form-documents-picker');
        target.classList.toggle('is-invalid', !!message);

        if (message) {
            const error = document.createElement('div');
            error.className = 'js-field-error text-danger mt-1';
            error.style.fontSize = '0.8rem';
            error.textContent = message;
            group.appendChild(error);
        }
    }

    function addRow() {
        if (rows().length >= MAX) {
            return;
        }

        const index = counter++;
        const row = template.content.firstElementChild.cloneNode(true);
        const select = row.querySelector('select');
        const file = row.querySelector('input[type="file"]');
        const picker = row.querySelector('.form-documents-picker');
        const name = row.querySelector('.form-documents-filename');

        select.name = `documents[${index}][type]`;
        file.name = `documents[${index}][file]`;

        select.addEventListener('change', function () { setError(row, 'type', ''); });

        file.addEventListener('change', function () {
            const chosen = file.files && file.files[0];
            picker.classList.toggle('has-file', !!chosen);
            name.textContent = chosen ? chosen.name : 'Choose file';
            setError(row, 'file', chosen && chosen.size > MAX_BYTES ? 'This file is larger than 10MB.' : '');
        });

        row.querySelector('.form-documents-remove').addEventListener('click', function () {
            row.remove();
            syncAdd();
        });

        rowsEl.appendChild(row);
        syncAdd();
        select.focus();
    }

    addBtn.addEventListener('click', addRow);

    /** Runs from the form's submit handler; true when a row needs fixing. */
    function validate() {
        let hasError = false;

        rows().forEach(function (row) {
            const type = row.querySelector('select').value;
            const file = row.querySelector('input[type="file"]').files[0];

            // A completely empty row is simply ignored.
            if (!type && !file) {
                return;
            }

            if (file && !type) {
                setError(row, 'type', 'Please select a document type.');
                hasError = true;
            }

            if (type && !file) {
                setError(row, 'file', 'Please choose a file, or remove this row.');
                hasError = true;
            }

            if (file && file.size > MAX_BYTES) {
                setError(row, 'file', 'This file is larger than 10MB.');
                hasError = true;
            }
        });

        return hasError;
    }

    return { validate: validate, addRow: addRow };
})();
</script>
