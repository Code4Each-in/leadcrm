@extends('layout')

@section('title', 'Leads')
@section('subtitle', 'Import Pricing')

@section('content')

<div class="row">

    <div class="col-12 grid-margin stretch-card">

        <div class="card" id="leadImportCard">
            <div class="card-body">

                <div class="lead-form-header">
                    <div class="lead-form-header-main">
                        <div class="lead-form-eyebrow">Pricing Management</div>
                        <h4 class="card-title">Import Pricing</h4>
                        <p class="card-description">
                            Upload a CSV file to create multiple Pricing records at once. Each row
                            targets the lead named in its own <code>lead_id</code> column, so one file
                            can add pricing to many leads - or add several pricing records to the same
                            lead - in one go. Every row is validated first - nothing is created until
                            the whole file passes.
                        </p>
                    </div>

                    <a href="{{ route('leads.index') }}" class="btn lead-form-back-btn">
                        <i class="mdi mdi-arrow-left"></i>
                        Back
                    </a>
                </div>

                @if (session('importError'))
                    <div class="alert alert-danger">
                        {{ session('importError') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('importErrors'))

                    @php
                        $importErrors = session('importErrors');
                        $totalRows = session('importTotalRows');
                        $errorRowCount = collect($importErrors)->pluck('row')->unique()->count();
                    @endphp

                    <div class="import-summary">
                        <div class="import-summary-item">
                            <div class="import-summary-value">{{ $totalRows }}</div>
                            <div class="import-summary-label">Records found</div>
                        </div>
                        <div class="import-summary-item import-summary-error">
                            <div class="import-summary-value">{{ $errorRowCount }}</div>
                            <div class="import-summary-label">Row(s) with errors</div>
                        </div>
                        <div class="import-summary-item">
                            <div class="import-summary-value">0</div>
                            <div class="import-summary-label">Pricing records created</div>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3">
                        CSV validation failed. Please correct the issues below and upload the file again.
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table import-error-table">
                            <thead>
                                <tr>
                                    <th>Row</th>
                                    <th>Field</th>
                                    <th>Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($importErrors as $rowError)
                                    <tr>
                                        <td>{{ $rowError['row'] }}</td>
                                        <td>{{ $rowError['field'] }}</td>
                                        <td>{{ $rowError['message'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="import-field-reference mb-4">
                    <div class="section-heading mb-3">
                        <i class="mdi mdi-table-column"></i>
                        <div>
                            <h4 class="card-title mb-1">Expected Columns</h4>
                            <p class="card-description mb-0">
                                The uploaded CSV must use these exact column headers.
                                <a href="{{ route('leads.pricing.csv.template') }}">Download the template</a>
                                to get started.
                            </p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table import-field-table">
                            <thead>
                                <tr>
                                    <th>Column</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fields as $field)
                                    <tr>
                                        <td><code>{{ $field }}</code></td>
                                        <td>{{ $hints[$field] ?? 'optional' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('leads.pricing.import.store') }}"
                    enctype="multipart/form-data"
                    class="forms-sample"
                >
                    @csrf

                    <div class="form-group">
                        <label for="csv_file">
                            CSV File<span class="text-danger">*</span>
                        </label>

                        <div class="file-upload-field @error('csv_file') has-error @enderror" id="csvFileField">
                            <input type="text" class="file-upload-info" id="csvFileName" placeholder="No file chosen" readonly>
                            <input
                                type="file"
                                name="csv_file"
                                id="csv_file"
                                accept=".csv,text/csv"
                                class="file-upload-default"
                                required
                            >
                            <button type="button" class="file-upload-browse">
                                <i class="mdi mdi-paperclip"></i>
                                Browse
                            </button>
                        </div>

                        <small class="form-text text-muted">CSV files only. Max 10MB.</small>

                        @error('csv_file')
                            <div class="validation-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex align-items-center action-buttons">

                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-upload me-1"></i>
                            Upload &amp; Validate
                        </button>

                        <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>

                    </div>

                </form>

            </div>
        </div>

    </div>

</div>

<style>
    #leadImportCard .lead-form-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 20px;
        border-bottom: 1px solid #eef0f3;
        margin-bottom: 24px;
    }

    #leadImportCard .lead-form-back-btn {
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
        flex-shrink: 0;
    }

    #leadImportCard .lead-form-eyebrow {
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

    #leadImportCard .lead-form-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    #leadImportCard .card-title {
        font-weight: 700;
        font-size: 24px;
        color: #1a1f2b;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }

    #leadImportCard .card-description {
        color: #8a92a3;
        font-size: 13.5px;
        margin: 0;
    }

    .import-summary {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .import-summary-item {
        flex: 1 1 160px;
        background: #f8f9fb;
        border: 1px solid #e2e5eb;
        border-radius: 10px;
        padding: 14px 18px;
        text-align: center;
    }

    .import-summary-item.import-summary-error {
        background: #fdecec;
        border-color: #f3c2c2;
    }

    .import-summary-value {
        font-size: 26px;
        font-weight: 700;
        color: #1a1f2b;
    }

    .import-summary-error .import-summary-value {
        color: #d33a3a;
    }

    .import-summary-label {
        font-size: 12.5px;
        color: #6c7280;
        margin-top: 2px;
    }

    .import-error-table th,
    .import-field-table th {
        background: #f8f9fb;
        font-size: 12.5px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6c7280;
    }

    .import-error-table td,
    .import-field-table td {
        font-size: 13.5px;
        vertical-align: middle;
    }

    .import-error-table {
        border: 1px solid #f3c2c2;
    }

    .section-heading {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid #eef0f3;
    }

    .section-heading i {
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #5b52e0;
        background: #eef0ff;
        border-radius: 10px;
    }

    .form-control {
        min-height: 46px;
        border: 1px solid #e2e5eb;
        border-radius: 8px;
        padding: 0.5rem 0.9rem;
        font-size: 0.925rem;
    }

    .is-invalid {
        border-color: #d33a3a !important;
    }

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

    .file-upload-field.has-error {
        border-color: #d33a3a;
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

    .validation-error {
        color: #d33a3a;
        font-size: 0.78rem;
        margin-top: 5px;
        display: block;
    }

    #leadImportCard .action-buttons {
        gap: 10px;
    }

    #leadImportCard .btn-primary,
    #leadImportCard .btn-secondary {
        padding: 10px 20px;
        border-radius: 9px !important;
        font-weight: 500;
    }

    #leadImportCard .btn-primary {
        background: #6c63ff;
        border-color: #6c63ff;
    }

    #leadImportCard .btn-secondary {
        background: #fff;
        border: 1px solid #e2e5eb;
        color: #6c7280;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('csv_file');
        const fieldWrapper = document.getElementById('csvFileField');
        const nameField = document.getElementById('csvFileName');

        if (!fileInput || !fieldWrapper || !nameField) return;

        fieldWrapper.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                nameField.value = fileInput.files[0].name;
                nameField.classList.add('has-file');
                fieldWrapper.classList.remove('has-error');
            } else {
                nameField.value = '';
                nameField.classList.remove('has-file');
            }
        });
    });
</script>

@endsection
