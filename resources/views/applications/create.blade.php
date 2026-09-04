@extends('layout')

@section('title', 'Applications')
@section('subtitle', 'Create Application')

@section('content')

<div class="row">

    <div class="col-12 grid-margin stretch-card">

        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Create Application</h4>

                <p class="card-description">
                    Enter application details
                </p>

                <form method="POST" action="{{ route('applications.store') }}" class="forms-sample">
                    @csrf

                    {{-- Product --}}
                    <div class="form-group">
                        <label for="product_id">
                            Product
                        </label>

                        <select
                            name="product_id"
                            id="product_id"
                            class="form-select"
                            required
                        >
                            <option value="">Select Product</option>

                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Business Information --}}
                    <div class="section-heading mt-4 mb-3">
                        <i class="mdi mdi-domain"></i>
                        <div>
                            <h4 class="card-title mb-1">Business Information</h4>
                            <p class="card-description mb-0">Enter business details</p>
                        </div>
                    </div>

                    <div class="row">

                        {{-- Company Type --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_type">
                                    Company Type
                                </label>

                                <select
                                    name="company_type"
                                    id="company_type"
                                    class="form-select"
                                >
                                    <option value="">
                                        Select Company Type
                                    </option>

                                    <option value="Limited">
                                        Limited
                                    </option>

                                    <option value="Sole Trader">
                                        Sole Trader
                                    </option>

                                    <option value="Partnership">
                                        Partnership
                                    </option>

                                    <option value="Limited Liability Partnership">
                                        Limited Liability Partnership
                                    </option>
                                </select>
                            </div>
                        </div>


                        {{-- Company / Business Name --}}
                        <div class="col-md-6">
                            <div class="form-group company-search-wrapper">

                                <label for="company_business_name">
                                    Company / Business Name
                                </label>

                                <input
                                    type="hidden"
                                    name="company_number"
                                    id="company_number"
                                >

                                <div class="input-group">

                                    <input
                                        type="text"
                                        name="company_business_name"
                                        id="company_business_name"
                                        class="form-control"
                                        placeholder="Enter company name"
                                        autocomplete="off"
                                    >

                                    <div class="input-group-append">

                                        <button
                                            type="button"
                                            class="btn btn-primary"
                                            id="searchCompanyBtn"
                                            disabled
                                        >
                                            Search
                                        </button>

                                    </div>

                                </div>

                                <div
                                    id="companySearchResults"
                                    class="company-search-results"
                                    style="display:none;"
                                ></div>

                            </div>
                        </div>

                        {{-- Business Start Date --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="business_start_date">
                                    Business Start Date
                                </label>

                                <input
                                    type="date"
                                    name="business_start_date"
                                    id="business_start_date"
                                    class="form-control"
                                >
                            </div>
                        </div>


                        {{-- Business Type --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="business_type">
                                    Business Type
                                </label>

                                <input
                                    type="text"
                                    name="business_type"
                                    id="business_type"
                                    class="form-control"
                                    placeholder="Enter business type"
                                >
                            </div>
                        </div>


                        {{-- Registered Address --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="business_registered_address">
                                    Business Registered Address
                                </label>

                                <textarea
                                    name="business_registered_address"
                                    id="business_registered_address"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Enter registered address"
                                ></textarea>
                            </div>
                        </div>


                        {{-- Trading Address --}}
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="business_trading_address">
                                    Business Trading Address
                                </label>

                                <textarea
                                    name="business_trading_address"
                                    id="business_trading_address"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Enter trading address"
                                ></textarea>

                                <div class="form-check mt-2">
                                    <label class="form-check-label">

                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="same_address"
                                            name="same_as_registered_address"
                                            value="1"
                                        >

                                        Same as Business Registered Address

                                    </label>
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- Contact Information --}}
                    <div class="section-heading mt-4 mb-3">
                        <i class="mdi mdi-account-outline"></i>
                        <div>
                            <h4 class="card-title mb-1">Contact Information</h4>
                            <p class="card-description mb-0">Who should we get in touch with</p>
                        </div>
                    </div>

                    <div class="row">

                        {{-- Customer Name --}}
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="customer_name">
                                    Customer Name
                                </label>

                                <input
                                    type="text"
                                    name="customer_name"
                                    id="customer_name"
                                    class="form-control"
                                    placeholder="Enter customer name"
                                >

                            </div>
                        </div>


                        {{-- Contact Person --}}
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="contact_person">
                                    Contact Person
                                </label>

                                <input
                                    type="text"
                                    name="contact_person"
                                    id="contact_person"
                                    class="form-control"
                                    placeholder="Enter contact person"
                                >

                            </div>
                        </div>


                        {{-- Date of Birth --}}
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="date_of_birth">
                                    Date of Birth
                                </label>

                                <input
                                    type="date"
                                    name="date_of_birth"
                                    id="date_of_birth"
                                    class="form-control"
                                >

                            </div>
                        </div>


                        {{-- Phone --}}
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="phone_no">
                                    Phone No.
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        +44
                                    </span>

                                    <input
                                        type="text"
                                        name="phone_no"
                                        id="phone_no"
                                        class="form-control"
                                        maxlength="10"
                                        inputmode="numeric"
                                        placeholder="Enter phone number"
                                    >

                                </div>

                            </div>
                        </div>


                        {{-- Mobile --}}
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="mobile_no">
                                    Mobile No.
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        +44
                                    </span>

                                    <input
                                        type="text"
                                        name="mobile_no"
                                        id="mobile_no"
                                        class="form-control"
                                        maxlength="10"
                                        inputmode="numeric"
                                        placeholder="Enter mobile number"
                                    >

                                </div>

                            </div>
                        </div>


                        {{-- Email --}}
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="email">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control"
                                    placeholder="Enter email address"
                                >

                            </div>
                        </div>

                    </div>

                    {{-- NFS / AF4U dynamic fields --}}
                    <div id="nfs-af4u-fields" style="display: none;" class="dynamic-panel mt-4">

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="gross_sales">
                                        Gross Sales
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input
                                            type="number"
                                            step="0.01"
                                            name="gross_sales"
                                            id="gross_sales"
                                            class="form-control"
                                            placeholder="Enter gross sales"
                                        >
                                    </div>

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="funds_required">
                                        Funds Required
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input
                                            type="number"
                                            step="0.01"
                                            name="funds_required"
                                            id="funds_required"
                                            class="form-control"
                                            placeholder="Enter funds required"
                                        >
                                    </div>

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="funds_term_months">
                                        Term of Funds Required
                                    </label>

                                    <select
                                        name="funds_term_months"
                                        id="funds_term_months"
                                        class="form-select"
                                    >
                                        <option value="">
                                            Select Term
                                        </option>

                                        <option value="12">
                                            12 months
                                        </option>

                                        <option value="24">
                                            24 months
                                        </option>

                                        <option value="36">
                                            36 months
                                        </option>

                                        <option value="48">
                                            48 months
                                        </option>

                                        <option value="60">
                                            60 months
                                        </option>

                                        <option value="72">
                                            72 months
                                        </option>

                                    </select>

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="home_owner">
                                        Home Owner
                                    </label>

                                    <select
                                        name="home_owner"
                                        id="home_owner"
                                        class="form-select"
                                    >
                                        <option value="">
                                            Select
                                        </option>

                                        <option value="Yes">
                                            Yes
                                        </option>

                                        <option value="No">
                                            No
                                        </option>

                                    </select>

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="vat_registered">
                                        VAT Registered
                                    </label>

                                    <select
                                        name="vat_registered"
                                        id="vat_registered"
                                        class="form-select"
                                    >
                                        <option value="">
                                            Select
                                        </option>

                                        <option value="Yes">
                                            Yes
                                        </option>

                                        <option value="No">
                                            No
                                        </option>

                                    </select>

                                </div>
                            </div>
                            {{-- Loan Purpose --}}
                            <div class="col-12 mt-3">

                                <div class="loan-purpose-section">

                                    <label class="loan-purpose-title">
                                        How does your client plan to use the loan?
                                    </label>

                                    <div class="loan-purpose-grid">

                                        <label class="loan-purpose-option">
                                            <input
                                                type="radio"
                                                name="loan_purpose"
                                                value="Fund vehicle, equipment or machinery"
                                            >

                                            <span>
                                                Fund vehicle,<br>
                                                equipment or machinery
                                            </span>
                                        </label>


                                        <label class="loan-purpose-option">
                                            <input
                                                type="radio"
                                                name="loan_purpose"
                                                value="Expansion / growth"
                                            >

                                            <span>
                                                Expansion / growth
                                            </span>
                                        </label>


                                        <label class="loan-purpose-option">
                                            <input
                                                type="radio"
                                                name="loan_purpose"
                                                value="Refinancing a loan"
                                            >

                                            <span>
                                                Refinancing a loan
                                            </span>
                                        </label>


                                        <label class="loan-purpose-option">
                                            <input
                                                type="radio"
                                                name="loan_purpose"
                                                value="Tax payment"
                                            >

                                            <span>
                                                Tax payment
                                            </span>
                                        </label>


                                        <label class="loan-purpose-option">
                                            <input
                                                type="radio"
                                                name="loan_purpose"
                                                value="Working capital"
                                            >

                                            <span>
                                                Working capital
                                            </span>
                                        </label>


                                        <label class="loan-purpose-option">
                                            <input
                                                type="radio"
                                                name="loan_purpose"
                                                value="Other"
                                            >

                                            <span>
                                                Other
                                            </span>
                                        </label>

                                    </div>

                                </div>

                            </div>

                            {{-- Additional Funds Usage Details --}}
                            <div
                                class="col-12 mt-4"
                                id="funds-usage-details-wrapper"
                                style="display: none;"
                            >
                                <div class="form-group">

                                    <label for="funds_usage_details">
                                        Additional Details About Funds Usage
                                    </label>

                                    <textarea
                                        name="funds_usage_details"
                                        id="funds_usage_details"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Please provide additional details about how the funds will be used"
                                    ></textarea>

                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- AU Savers dynamic fields --}}
                    <div id="au-savers-fields" style="display: none;" class="dynamic-panel mt-4">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="supply_address">
                                        Supply Address
                                    </label>

                                    <textarea
                                        name="supply_address"
                                        id="supply_address"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Enter supply address"
                                    ></textarea>

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="postcode">
                                        Postcode
                                    </label>

                                    <input
                                        type="text"
                                        name="postcode"
                                        id="postcode"
                                        class="form-control"
                                        placeholder="Enter postcode"
                                    >

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="number_of_sites">
                                        Number of Sites
                                    </label>

                                    <select
                                        name="number_of_sites"
                                        id="number_of_sites"
                                        class="form-select"
                                    >
                                        <option value="">
                                            Select
                                        </option>

                                        <option value="Single Site">
                                            Single Site
                                        </option>

                                        <option value="Multiple Site">
                                            Multiple Site
                                        </option>

                                    </select>

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="mpan">
                                        MPAN
                                    </label>

                                    <input
                                        type="text"
                                        name="mpan"
                                        id="mpan"
                                        class="form-control"
                                        placeholder="Enter MPAN"
                                    >

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="mprn">
                                        MPRN
                                    </label>

                                    <input
                                        type="text"
                                        name="mprn"
                                        id="mprn"
                                        class="form-control"
                                        placeholder="Enter MPRN"
                                    >

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="spid">
                                        SPID
                                    </label>

                                    <input
                                        type="text"
                                        name="spid"
                                        id="spid"
                                        class="form-control"
                                        placeholder="Enter SPID"
                                    >

                                </div>
                            </div>

                        </div>

                    </div>


                    <div class="mt-4 pt-3 border-top d-flex align-items-center action-buttons">

                        <button
                            type="submit"
                            name="status"
                            value="published"
                            class="btn btn-primary me-2 px-4"
                        >
                            <i class="mdi mdi-check-circle-outline me-1"></i>
                            Publish
                        </button>

                        <button
                            type="submit"
                            name="status"
                            value="draft"
                            class="btn btn-light me-3 px-4"
                        >
                            <i class="mdi mdi-file-document-edit-outline me-1"></i>
                            Save as Draft
                        </button>

                        <a
                            href="{{ url()->previous() }}"
                            class="btn btn-secondary  px-4"
                        >
                            Cancel
                        </a>

                    </div>

                </form>

            </div>
        </div>

    </div>

</div>

<style>

    /* Section headings (icon + title + description) */
    #applications-create .section-heading,
    .section-heading {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid #eef0f7;
    }

    .section-heading i {
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #4B7BEC;
        background: rgba(75, 123, 236, 0.1);
        border-radius: 10px;
    }

    .section-heading .card-title {
        font-size: 1.05rem;
    }

    /* Dynamic (product-specific) panels get a subtle callout background */
    .dynamic-panel {
        background: #f8f9fc;
        border: 1px dashed #dfe4f0;
        border-radius: 12px;
        padding: 1.5rem;
    }

    /* Consistent field styling */
    .form-group label {
        font-weight: 500;
        font-size: 0.85rem;
        color: #3e4b5b;
        margin-bottom: 0.4rem;
    }

    .form-control,
    .form-select {
        min-height: 46px;
        border: 1px solid #e2e6ee;
        border-radius: 8px;
        padding: 0.5rem 0.9rem;
        font-size: 0.925rem;
        color: #3e4b5b;
        background-color: #fbfbfd;
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
        width: 100%;
    }

    textarea.form-control {
        min-height: unset;
    }

    .form-control:hover,
    .form-select:hover {
        border-color: #c9d1e3;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: #fff;
        border-color: #4B7BEC;
        box-shadow: 0 0 0 0.18rem rgba(75, 123, 236, 0.15);
    }

    .form-control::placeholder {
        color: #a9b1c3;
    }

    /* Clean, theme-matching dropdown arrow for selects */
    select.form-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%237987a1' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 12px 8px;
        padding-right: 2.5rem;
        cursor: pointer;
    }

    /* Phone / mobile / currency prefixes */
    .input-group-text {
        border: 1px solid #e2e6ee;
        border-right: none;
        background-color: #f1f3f9;
        color: #6b7690;
        font-weight: 500;
        border-radius: 8px 0 0 8px;
    }

    .input-group .form-control {
        border-radius: 0 8px 8px 0;
    }

    .input-group:focus-within .input-group-text {
        border-color: #4B7BEC;
        color: #4B7BEC;
    }

    /* Checkbox row */
    .form-check-label {
        font-size: 0.875rem;
        color: #6b7690;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-check-input {
        cursor: pointer;
    }


    .btn-light {
        border-radius: 8px;
        font-weight: 500;
        border: 1px solid #e2e6ee;
    }
    .action-buttons {
        gap: 10px;
    }

    /* Loan purpose */

    .loan-purpose-section {
        margin-top: 0.5rem;
    }

    .loan-purpose-title {
        display: block;
        font-size: 1rem !important;
        font-weight: 600 !important;
        color: #3e4b5b !important;
        margin-bottom: 1rem !important;
    }

    .loan-purpose-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }

    .loan-purpose-option {
        position: relative;
        margin: 0 !important;
        cursor: pointer;
    }

    .loan-purpose-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .loan-purpose-option span {
        min-height: 90px;

        display: flex;
        align-items: center;
        justify-content: center;

        text-align: center;

        padding: 15px;

        background: #fff;
        border: 1px solid #e2e6ee;
        border-radius: 12px;

        color: #3e4b5b;
        font-size: 0.9rem;
        font-weight: 500;

        transition:
            border-color .15s ease,
            background-color .15s ease,
            box-shadow .15s ease,
            transform .15s ease;
    }

    .loan-purpose-option:hover span {
        border-color: #4B7BEC;
        background: rgba(75, 123, 236, 0.04);
    }

    .loan-purpose-option input:checked + span {
        border: 2px solid #4B7BEC;
        background: rgba(75, 123, 236, 0.08);
        color: #4B7BEC;
        box-shadow: 0 4px 12px rgba(75, 123, 236, 0.12);
    }
    /* Companies House autocomplete dropdown */

    .company-search-wrapper {
        position: relative;
    }

    #companySearchResults {
        position: relative;
        z-index: 1000;
        margin-top: 4px;
    }

    .company-result-list {
        background: #fff;
        border: 1px solid #e2e6ee;
        border-radius: 8px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        max-height: 350px;
        overflow-y: auto;
    }

    .company-result {
        padding: 12px 14px;
        border-bottom: 1px solid #eef0f5;
        cursor: pointer;
        background: #fff;
        transition: background-color 0.15s ease;
    }

    .company-result:last-child {
        border-bottom: none;
    }

    .company-result:hover {
        background: #f5f7ff;
    }

    .company-result-name {
        font-size: 0.95rem;
        font-weight: 600;
        color: #2f3650;
        line-height: 1.4;
    }

    .company-result-number {
        margin-top: 3px;
        font-size: 0.8rem;
        color: #6b7690;
    }

    .company-result:active {
        background: #eef2ff;
    }
    @media (max-width: 991px) {

        .loan-purpose-grid {
            grid-template-columns: repeat(2, 1fr);
        }

    }

    @media (max-width: 575px) {

        .loan-purpose-grid {
            grid-template-columns: 1fr;
        }

    }
</style>

<script>

document.getElementById('product_id').addEventListener('change', function () {

    const productId = this.value;

    const nfsAf4uFields =
        document.getElementById('nfs-af4u-fields');

    const auSaversFields =
        document.getElementById('au-savers-fields');

    // Hide everything first
    nfsAf4uFields.style.display = 'none';
    auSaversFields.style.display = 'none';

    // NFS = 1
    // AF4U = 2
    if (productId === '1' || productId === '2') {
        nfsAf4uFields.style.display = 'block';
    }

    // AU Savers = 3
    if (productId === '3') {
        auSaversFields.style.display = 'block';
    }

});

const sameAddress =
    document.getElementById('same_address');

const registeredAddress =
    document.getElementById('business_registered_address');

const tradingAddress =
    document.getElementById('business_trading_address');

if (sameAddress) {

    sameAddress.addEventListener('change', function () {

        if (this.checked) {

            tradingAddress.value =
                registeredAddress.value;

            tradingAddress.readOnly = true;

        } else {

            tradingAddress.readOnly = false;

        }

    });


    registeredAddress.addEventListener('input', function () {

        if (sameAddress.checked) {

            tradingAddress.value =
                this.value;

        }

    });

}
// Show additional funds usage details only when "Other" is selected

const loanPurposeInputs =
    document.querySelectorAll('input[name="loan_purpose"]');

const fundsUsageWrapper =
    document.getElementById('funds-usage-details-wrapper');

const fundsUsageDetails =
    document.getElementById('funds_usage_details');


loanPurposeInputs.forEach(function (input) {

    input.addEventListener('change', function () {

        if (this.value === 'Other') {

            // Show textarea
            fundsUsageWrapper.style.display = 'block';

        } else {

            // Hide textarea
            fundsUsageWrapper.style.display = 'none';

            // Clear previous value
            fundsUsageDetails.value = '';

        }

    });

});
const companyTypeSelect =
    document.getElementById('company_type');

const searchCompanyBtn =
    document.getElementById('searchCompanyBtn');

companyTypeSelect.addEventListener('change', function () {

    if (this.value === 'Limited') {

        searchCompanyBtn.disabled = false;

    } else {

        searchCompanyBtn.disabled = true;

        // Clear Companies House related values
        document.getElementById('company_number').value = '';

        document.getElementById('companySearchResults').style.display = 'none';
        document.getElementById('companySearchResults').innerHTML = '';
    }

});
document.getElementById('searchCompanyBtn').addEventListener('click', function () {
    const companyType =
        document.getElementById('company_type').value;

    if (companyType !== 'Limited') {

        alert(
            'Companies House search is only available for Limited companies.'
        );

        return;
    }

    const companyName = document
        .getElementById('company_business_name')
        .value
        .trim();

    if (!companyName) {
        alert('Please enter company name.');
        return;
    }

    const resultsBox = document.getElementById('companySearchResults');

    resultsBox.style.display = 'block';
    resultsBox.innerHTML = `
        <div class="alert alert-info">
            Searching Companies House...
        </div>
    `;

    const url = `{{ route('companies.house.search') }}?q=${encodeURIComponent(companyName)}`;

    console.log('Searching Companies House:', url);

    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {

        console.log('Search HTTP status:', response.status);
        console.log('Search content type:', response.headers.get('content-type'));

        // Read response as text first
        const text = await response.text();

        console.log('Raw search response:', text);

        // Empty response
        if (!text.trim()) {
            throw new Error(
                `Server returned an empty response. HTTP ${response.status}`
            );
        }

        let result;

        // Safely parse JSON
        try {
            result = JSON.parse(text);
        } catch (error) {

            console.error('Invalid JSON returned by search endpoint:', text);

            throw new Error(
                `Server returned invalid JSON. HTTP ${response.status}`
            );
        }

        // Handle HTTP errors
        if (!response.ok) {

            throw new Error(
                result.message || `HTTP ${response.status}`
            );
        }

        return result;
    })
    .then(result => {

        console.log('Companies House search result:', result);

        if (!result.success) {

            resultsBox.innerHTML = `
                <div class="alert alert-danger">
                    ${result.message ?? 'Unable to search Companies House.'}
                </div>
            `;

            return;
        }

        const items = result.data?.items ?? [];

        console.log('Companies found:', items);

        if (!items.length) {

            resultsBox.innerHTML = `
                <div class="alert alert-warning">
                    No companies found.
                </div>
            `;

            return;
        }

        let html = '';

        items.forEach(company => {

            const companyNumber = company.company_number;

            html += `
                <div
                    class="company-result"
                    data-company-number="${companyNumber ?? ''}"
                >
                    <div class="company-result-name">
                        ${company.title ?? ''}
                    </div>

                    <div class="company-result-number">
                        Company Number: ${companyNumber ?? ''}
                    </div>
                </div>
            `;
        });

        resultsBox.innerHTML = `
            <div class="company-result-list">
                ${html}
            </div>
        `;

        document.querySelectorAll('.company-result')
            .forEach(item => {

            item.addEventListener('click', function () {

                const companyNumber =
                    this.getAttribute('data-company-number');

                console.log(
                    'Selected company number:',
                    companyNumber
                );

                if (
                    !companyNumber ||
                    companyNumber === 'undefined'
                ) {
                    alert('Company number was not found.');
                    return;
                }

                resultsBox.style.display = 'none';

                getCompanyDetails(companyNumber);
            });

            });

    })
    .catch(error => {

        console.error('Company search error:', error);

        resultsBox.innerHTML = `
            <div class="alert alert-danger">
                ${error.message || 'Something went wrong while searching.'}
            </div>
        `;
    });
});


function getCompanyDetails(companyNumber)
{
    console.log('getCompanyDetails received:', companyNumber);

    const resultsBox =
        document.getElementById('companySearchResults');

    if (!companyNumber) {

        resultsBox.innerHTML = `
            <div class="alert alert-danger">
                Company number is missing.
            </div>
        `;

        return;
    }

    resultsBox.innerHTML = `
        <div class="alert alert-info">
            Loading company information...
        </div>
    `;

    const url =
        `{{ url('/companies-house') }}/${encodeURIComponent(companyNumber)}`;

    console.log('Fetching company details:', url);

    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {

        console.log('HTTP status:', response.status);

        const text = await response.text();

        console.log('Raw response:', text);

        if (!text.trim()) {

            throw new Error(
                `Server returned an empty response. HTTP ${response.status}`
            );
        }

        let result;

        try {

            result = JSON.parse(text);

        } catch (e) {

            console.error('Invalid JSON response:', text);

            throw new Error(
                `Server returned invalid JSON. HTTP ${response.status}`
            );
        }

        if (!response.ok) {

            throw new Error(
                result.message || `HTTP ${response.status}`
            );
        }

        return result;
    })
    .then(result => {

        console.log('Company details result:', result);

        if (!result.success) {

            resultsBox.innerHTML = `
                <div class="alert alert-danger">
                    ${result.message ?? 'Unable to load company information.'}
                </div>
            `;

            return;
        }

        const company = result.data;
        const officers = result.data.officers;

        console.log('Company details:', company);
        console.log('Company officers:', officers);
        /*
        * Find active director
        */
        const director = officers.items?.find(function (officer) {

            return (
                officer.officer_role &&
                officer.officer_role.toLowerCase() === 'director'
            );

        });
        if (director) {

            const customerName =
                document.getElementById('customer_name');

            const contactPerson =
                document.getElementById('contact_person');

            if (customerName) {
                customerName.value =
                    director.name ?? '';
            }

            if (contactPerson) {
                contactPerson.value =
                    director.name ?? '';
            }
        }
        // Check company status
        if (
            company.company_status &&
            company.company_status.toLowerCase() !== 'active'
        ) {

            resultsBox.innerHTML = `
                <div class="alert alert-warning">
                    This company is not active and cannot be used for this application.
                </div>
            `;

            return;
        }

        // Only active companies reach here
        fillCompanyDetails(
            company,
            officers
        );

        resultsBox.innerHTML = `
            <div class="alert alert-success">
                Company information loaded successfully.
            </div>
        `;
    })
    .catch(error => {

        console.error('Company details error:', error);

        resultsBox.innerHTML = `
            <div class="alert alert-danger">
                ${error.message}
            </div>
        `;
    });
}
function fillCompanyDetails(company, officers)
{
    console.log('Filling company details:', company);
    console.log('Company officers:', officers);


    /*
     * Company / Business Name
     */
    const companyName =
        document.getElementById('company_business_name');

    if (companyName) {

        companyName.value =
            company.company_name ?? '';
    }


    /*
     * Company Number
     */
    const companyNumber =
        document.getElementById('company_number');

    if (companyNumber) {

        companyNumber.value =
            company.company_number ?? '';
    }


    /*
     * Company Type
     *
     * This is Limited because this Companies House
     * flow is only available when Limited is selected.
     */
    const companyType =
        document.getElementById('company_type');

    if (companyType) {

        companyType.value = 'Limited';
    }


    /*
     * Business Start Date
     */
    const businessStartDate =
        document.getElementById('business_start_date');

    if (businessStartDate) {

        businessStartDate.value =
            company.date_of_creation ?? '';
    }


    /*
     * Business Type
     *
     * IMPORTANT:
     * Do NOT use sic_codes here.
     */
    const businessType =
        document.getElementById('business_type');

    if (businessType) {

        let businessActivity = '';

        if (
            company.branch_company_details &&
            company.branch_company_details.business_activity
        ) {
            businessActivity =
                company.branch_company_details.business_activity;
        }

        businessType.value =
            businessActivity;
    }


    /*
     * Registered Address
     */
    const registeredAddress =
        document.getElementById(
            'business_registered_address'
        );

    if (
        registeredAddress &&
        company.registered_office_address
    ) {

        const address =
            company.registered_office_address;

        const addressParts = [];

        if (address.premises) {
            addressParts.push(
                address.premises.trim()
            );
        }

        if (address.address_line_1) {
            addressParts.push(
                address.address_line_1.trim()
            );
        }

        if (address.address_line_2) {
            addressParts.push(
                address.address_line_2.trim()
            );
        }

        if (address.locality) {
            addressParts.push(
                address.locality.trim()
            );
        }

        if (address.region) {
            addressParts.push(
                address.region.trim()
            );
        }

        if (address.postal_code) {
            addressParts.push(
                address.postal_code.trim()
            );
        }

        if (address.country) {
            addressParts.push(
                address.country.trim()
            );
        }

        registeredAddress.value =
            addressParts.join(', ');
    }


    /*
     * Customer / Contact Person
     *
     * Get active director from Officers API.
     */
    if (
        officers &&
        Array.isArray(officers.items)
    ) {

        const director =
            officers.items.find(function (officer) {

                return (
                    officer.officer_role &&
                    officer.officer_role
                        .toLowerCase() === 'director'
                );

            });

        if (director) {

            const customerName =
                document.getElementById(
                    'customer_name'
                );

            const contactPerson =
                document.getElementById(
                    'contact_person'
                );

            if (customerName) {

                customerName.value =
                    director.name ?? '';
            }

            if (contactPerson) {

                contactPerson.value =
                    director.name ?? '';
            }
        }
    }


    /*
     * IMPORTANT:
     *
     * Date of Birth is intentionally NOT populated.
     *
     * It must be entered manually by the user.
     */


    /*
     * If trading address checkbox is already checked,
     * copy registered address.
     */
    const sameAddress =
        document.getElementById('same_address');

    const tradingAddress =
        document.getElementById(
            'business_trading_address'
        );

    if (
        sameAddress &&
        sameAddress.checked &&
        tradingAddress &&
        registeredAddress
    ) {

        tradingAddress.value =
            registeredAddress.value;
    }


    /*
     * Optional:
     * Store complete API response in hidden field
     * only if that field exists.
     */
    const apiData =
        document.getElementById('company_api_data');

    if (apiData) {

        apiData.value = JSON.stringify({
            company: company,
            officers: officers
        });
    }


    console.log(
        'Company fields populated successfully.'
    );
}
</script>

@endsection
