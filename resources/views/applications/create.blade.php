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
                            <div class="form-group">
                                <label for="company_business_name">
                                    Company / Business Name
                                </label>

                                <input
                                    type="text"
                                    name="company_business_name"
                                    id="company_business_name"
                                    class="form-control"
                                    placeholder="Enter company / business name"
                                >
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


                    <div class="mt-4 pt-3 border-top d-flex">

                        <button
                            type="submit"
                            class="btn btn-primary me-2 px-4"
                        >
                            <i class="mdi mdi-content-save-outline me-1"></i>
                            Save Application
                        </button>

                        <button
                            type="reset"
                            class="btn btn-light px-4"
                        >
                            Cancel
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>

</div>

{{-- ============================================================
     Visual-only polish for this page.
     Nothing here changes form behaviour, names, ids or values.
     ============================================================ --}}
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

    /* Buttons */
    .btn-primary {
        background-color: #4B7BEC;
        border-color: #4B7BEC;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(75, 123, 236, 0.25);
    }

    .btn-primary:hover {
        background-color: #3d68d8;
        border-color: #3d68d8;
    }

    .btn-light {
        border-radius: 8px;
        font-weight: 500;
        border: 1px solid #e2e6ee;
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

</script>

@endsection
