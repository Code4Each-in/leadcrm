@extends('layout')

@section('title', 'Leads')
@section('subtitle', 'Edit Lead')

@section('content')

<div class="row">

  <div class="col-12 grid-margin stretch-card">

    <div class="card">
      <div class="card-body">

        <h4 class="card-title">Edit Lead</h4>

        <p class="card-description">
          Update lead details
        </p>

        @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('leads.update', $lead->id) }}" class="forms-sample" novalidate>

          @csrf
          @method('PUT')

          {{-- Product --}}
          <div class="form-group">

            <label for="product_id">
              Product<span class="text-danger">*</span>
            </label>

            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror">

              <option value="">
                Select Product
              </option>

              @foreach($products as $product)

              <option value="{{ $product->id }}" {{ old('product_id', $lead->product_id) == $product->id ? 'selected' : '' }}>
                {{ $product->name }}
              </option>

              @endforeach

            </select>

            @error('product_id')
            <div class="validation-error">{{ $message }}</div>
            @enderror

          </div>

          <div id="rest-of-form" style="{{ old('product_id', $lead->product_id) ? '' : 'display:none;' }}">

            {{-- Business Information --}}
            <div class="section-heading mt-4 mb-3">

              <i class="mdi mdi-domain"></i>

              <div>

                <h4 class="card-title mb-1">
                  Business Information
                </h4>

                <p class="card-description mb-0">
                  Enter business details
                </p>

              </div>

            </div>


            <div class="row">


              {{-- Company Type --}}
              <div class="col-md-6">

                <div class="form-group">

                  <label for="company_type">
                    Company Type
                  </label>

                  <select name="company_type" id="company_type" class="form-select">

                    <option value="">
                      Select Company Type
                    </option>

                    <option value="Limited" {{ old('company_type', $lead->company_type) === 'Limited' ? 'selected' : '' }}>
                      Limited
                    </option>

                    <option value="Sole Trader" {{ old('company_type', $lead->company_type) === 'Sole Trader' ? 'selected' : '' }}>
                      Sole Trader
                    </option>

                    <option value="Partnership" {{ old('company_type', $lead->company_type) === 'Partnership' ? 'selected' : '' }}>
                      Partnership
                    </option>

                    <option value="Limited Liability Partnership" {{ old('company_type', $lead->company_type) === 'Limited Liability Partnership' ? 'selected' : '' }}>
                      Limited Liability Partnership
                    </option>

                  </select>

                </div>

              </div>


              {{-- Company Name --}}
              <div class="col-md-6">

                <div class="form-group company-search-wrapper">

                  <label for="company_business_name">
                    Company / Business Name
                  </label>

                  <input type="hidden" name="company_number" id="company_number" value="{{ old('company_number', $lead->company_number) }}">

                  <div class="input-group">

                    <input type="text" name="company_business_name" id="company_business_name" class="form-control" value="{{ old('company_business_name', $lead->company_business_name) }}" placeholder="Enter company name" autocomplete="off">

                    <div class="input-group-append">

                      <button type="button" class="btn btn-primary" id="searchCompanyBtn" title="Search Companies House" style="{{ old('company_type', $lead->company_type) === 'Limited' ? 'display:inline-flex;' : '' }}">
                        <i class="mdi mdi-magnify"></i>
                      </button>

                    </div>

                  </div>

                  <div id="companySearchResults" class="company-search-results" style="display:none;"></div>

                </div>

              </div>


              {{-- Business Start Date --}}
              <div class="col-md-6">

                <div class="form-group">

                  <label for="business_start_date">
                    Business Start Date
                  </label>

                  <input type="date" name="business_start_date" id="business_start_date" class="form-control" value="{{ old('business_start_date', optional($lead->business_start_date)->format('Y-m-d')) }}">

                </div>

              </div>


              {{-- Business Type --}}
              <div class="col-md-6">

                <div class="form-group">

                  <label for="business_type">
                    Business Type
                  </label>

                  <input type="text" name="business_type" id="business_type" class="form-control" value="{{ old('business_type', $lead->business_type) }}" placeholder="Enter business type">

                </div>

              </div>


              {{-- Registered Address --}}
              <div class="col-md-6">

                <div class="form-group">

                  <label for="business_registered_address">
                    Business Registered Address
                  </label>

                  <input type="text" name="business_registered_address" id="business_registered_address" class="form-control" value="{{ old('business_registered_address', $lead->business_registered_address) }}" placeholder="Enter registered address">

                </div>

              </div>


              {{-- Trading Address --}}
              <div class="col-md-6">

                <div class="form-group">

                  <label for="business_trading_address">
                    Business Trading Address
                  </label>

                  <input type="text" name="business_trading_address" id="business_trading_address" class="form-control" value="{{ old('business_trading_address', $lead->business_trading_address) }}" placeholder="Enter trading address">

                  <div class="form-check mt-2">

                    <label class="form-check-label">

                      <input type="checkbox" class="form-check-input" id="same_address" name="same_as_registered_address" value="1" {{ old('same_as_registered_address', $lead->same_as_registered_address) ? 'checked' : '' }}>

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

                <h4 class="card-title mb-1">
                  Contact Information
                </h4>

                <p class="card-description mb-0">
                  Who should we get in touch with
                </p>

              </div>

            </div>


            <div class="row">


              {{-- Customer Name --}}
              <div class="col-md-6">

                <div class="form-group">

                  <label for="customer_name">
                    Customer Name
                  </label>

                  <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name', $lead->customer_name) }}" placeholder="Enter customer name">

                </div>

              </div>


              {{-- Contact Person --}}
              <div class="col-md-6">

                <div class="form-group">

                  <label for="contact_person">
                    Contact Person
                  </label>

                  <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ old('contact_person', $lead->contact_person) }}" placeholder="Enter contact person">

                </div>

              </div>


              {{-- DOB --}}
              <div class="col-md-6">

                <div class="form-group">

                  <label for="date_of_birth">
                    Date of Birth
                  </label>

                  <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', optional($lead->date_of_birth)->format('Y-m-d')) }}">
                    <small
                        id="companies-house-dob-hint"
                        class="text-muted"
                        style="display:none;"
                    ></small>
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

                    <input type="text" name="phone_no" id="phone_no" class="form-control" value="{{ old('phone_no', $lead->phone_no) }}" inputmode="numeric" placeholder="Enter phone number">

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

                    <input type="text" name="mobile_no" id="mobile_no" class="form-control" value="{{ old('mobile_no', $lead->mobile_no) }}" inputmode="numeric" placeholder="Enter mobile number">

                  </div>

                </div>

              </div>


              {{-- Email --}}
              <div class="col-md-6">

                <div class="form-group">

                  <label for="email">
                    Email Address
                  </label>

                  <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $lead->email) }}" placeholder="Enter email address">

                </div>

              </div>
            </div>
            {{-- Notes --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="form-group">

                        <label for="notes">
                            Notes
                        </label>

                        <input
                            name="notes"
                            id="notes"
                            rows="4"
                            class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Enter any additional notes about this lead"
                            maxlength="5000"
                        >{{ old('notes', $lead->notes) }}</input>

                        @error('notes')
                            <div class="validation-error">{{ $message }}</div>
                        @enderror

                    </div>
                </div>
            </div>

            {{-- NFS / AF4U --}}
            <div id="nfs-af4u-fields" class="dynamic-panel mt-4" style="display:none;">

              <div class="row">


                {{-- Gross Sales --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="gross_sales">
                      Gross Sales
                    </label>

                    <div class="input-group">

                      <span class="input-group-text">
                        £
                      </span>

                      <input type="number" step="0.01" name="gross_sales" id="gross_sales" class="form-control" value="{{ old('gross_sales', $lead->gross_sales) }}" placeholder="Enter gross sales">

                    </div>

                  </div>

                </div>


                {{-- Funds Required --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="funds_required">
                      Funds Required
                    </label>

                    <div class="input-group">

                      <span class="input-group-text">
                        £
                      </span>

                      <input type="number" step="0.01" name="funds_required" id="funds_required" class="form-control" value="{{ old('funds_required', $lead->funds_required) }}" placeholder="Enter funds required">

                    </div>

                  </div>

                </div>


                {{-- Term --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="funds_term_months">
                      Term of Funds Required
                    </label>

                    <select name="funds_term_months" id="funds_term_months" class="form-select">

                      <option value="">
                        Select Term
                      </option>

                      @foreach([12,24,36,48,60,72] as $months)

                      <option value="{{ $months }}" {{ old('funds_term_months', $lead->funds_term_months) == $months ? 'selected' : '' }}>
                        {{ $months }} months
                      </option>

                      @endforeach

                    </select>

                  </div>

                </div>


                {{-- Home Owner --}}
                <div class="col-md-6">
                    <div class="form-group">

                        <label class="radio-field-label">
                            Home Owner
                        </label>

                        <div class="yes-no-group">

                            <label class="yes-no-option">
                                <input
                                    type="radio"
                                    name="home_owner"
                                    value="Yes"
                                    {{ old('home_owner', $lead->home_owner) === 'Yes' ? 'checked' : '' }}
                                >
                                <span class="yes-no-button">
                                    <i class="mdi mdi-check-circle-outline"></i>
                                    Yes
                                </span>
                            </label>

                            <label class="yes-no-option">
                                <input
                                    type="radio"
                                    name="home_owner"
                                    value="No"
                                    {{ old('home_owner', $lead->home_owner) === 'No' ? 'checked' : '' }}
                                >
                                <span class="yes-no-button">
                                    <i class="mdi mdi-close-circle-outline"></i>
                                    No
                                </span>
                            </label>

                        </div>

                    </div>
                </div>


                {{-- VAT Registered --}}
                <div class="col-md-6">
                    <div class="form-group">

                        <label class="radio-field-label">
                            VAT Registered
                        </label>

                        <div class="yes-no-group">

                            <label class="yes-no-option">
                                <input
                                    type="radio"
                                    name="vat_registered"
                                    value="Yes"
                                    {{ old('vat_registered', $lead->vat_registered) === 'Yes' ? 'checked' : '' }}
                                >
                                <span class="yes-no-button">
                                    <i class="mdi mdi-check-circle-outline"></i>
                                    Yes
                                </span>
                            </label>

                            <label class="yes-no-option">
                                <input
                                    type="radio"
                                    name="vat_registered"
                                    value="No"
                                    {{ old('vat_registered', $lead->vat_registered) === 'No' ? 'checked' : '' }}
                                >
                                <span class="yes-no-button">
                                    <i class="mdi mdi-close-circle-outline"></i>
                                    No
                                </span>
                            </label>

                        </div>

                    </div>
                </div>


                {{-- Loan Purpose --}}
                <div class="col-12 mt-3">

                  <div class="loan-purpose-section">

                    <label class="loan-purpose-title">
                      How does your client plan to use the loan?
                    </label>

                    <div class="loan-purpose-grid">

                      @php
                      $purposes = [
                      'Fund vehicle, equipment or machinery',
                      'Expansion / growth',
                      'Refinancing a loan',
                      'Tax payment',
                      'Working capital',
                      'Other',
                      ];
                      @endphp

                      @foreach($purposes as $purpose)

                      <label class="loan-purpose-option">

                        <input type="radio" name="loan_purpose" value="{{ $purpose }}" {{ old('loan_purpose', $lead->loan_purpose) === $purpose ? 'checked' : '' }}>

                        <span>
                          {{ $purpose }}
                        </span>

                      </label>

                      @endforeach

                    </div>

                  </div>

                </div>


                {{-- Additional Usage --}}
                <div class="col-12 mt-4" id="funds-usage-details-wrapper" style="display:none;">

                  <div class="form-group">

                    <label for="funds_usage_details">
                      Additional Details About Funds Usage
                    </label>

                    <input type="text" name="funds_usage_details" id="funds_usage_details" class="form-control" value="{{ old('funds_usage_details', $lead->funds_usage_details) }}" placeholder="Please provide additional details about how the funds will be used">

                  </div>

                </div>

              </div>

            </div>


            {{-- AU Savers --}}
            <div id="au-savers-fields" class="dynamic-panel mt-4" style="display:none;">

              <div class="row">


                {{-- Supply Address --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="supply_address">
                      Supply Address
                    </label>

                    <input type="text" name="supply_address" id="supply_address" class="form-control" value="{{ old('supply_address', $lead->supply_address) }}" placeholder="Enter supply address">

                  </div>

                </div>


                {{-- Postcode --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="postcode">
                      Postcode
                    </label>

                    <input type="text" name="postcode" id="postcode" class="form-control" value="{{ old('postcode', $lead->postcode) }}" placeholder="Enter postcode">

                  </div>

                </div>


                {{-- Number Sites --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="number_of_sites">
                      Number of Sites
                    </label>

                    <select name="number_of_sites" id="number_of_sites" class="form-select">

                      <option value="">
                        Select
                      </option>

                      <option value="Single Site" {{ old('number_of_sites', $lead->number_of_sites) === 'Single Site' ? 'selected' : '' }}>
                        Single Site
                      </option>

                      <option value="Multiple Site" {{ old('number_of_sites', $lead->number_of_sites) === 'Multiple Site' ? 'selected' : '' }}>
                        Multiple Site
                      </option>

                    </select>

                  </div>

                </div>


                {{-- MPAN --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="mpan">
                      MPAN
                    </label>

                    <input type="text" name="mpan" id="mpan" class="form-control" value="{{ old('mpan', $lead->mpan) }}" placeholder="Enter MPAN">

                  </div>

                </div>


                {{-- MPRN --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="mprn">
                      MPRN
                    </label>

                    <input type="text" name="mprn" id="mprn" class="form-control" value="{{ old('mprn', $lead->mprn) }}" placeholder="Enter MPRN">

                  </div>

                </div>


                {{-- SPID --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="spid">
                      SPID
                    </label>

                    <input type="text" name="spid" id="spid" class="form-control" value="{{ old('spid', $lead->spid) }}" placeholder="Enter SPID">

                  </div>

                </div>

              </div>

            </div>


            {{-- Actions --}}
            <div class="mt-4 pt-3 border-top d-flex align-items-center action-buttons">

              <button type="submit" name="status" value="published" class="btn btn-primary me-2 px-4">
                <i class="mdi mdi-check-circle-outline me-1"></i>
                Update & Publish
              </button>

              <button type="submit" name="status" value="draft" class="btn btn-light me-3 px-4">
                <i class="mdi mdi-file-document-edit-outline me-1"></i>
                Save as Draft
              </button>

              <a href="{{ route('leads.index') }}" class="btn btn-secondary px-4">
                Cancel
              </a>

            </div>

          </div>
          {{-- /#rest-of-form --}}

        </form>

      </div>
    </div>

  </div>

</div>

<style>
  .section-heading {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
    padding-bottom: .85rem;
    border-bottom: 1px solid #eef0f7;
  }

  .action-buttons {
    gap: 10px;
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
    background: rgba(75, 123, 236, .1);
    border-radius: 10px;
  }

  .section-heading .card-title {
    font-size: 1.05rem;
  }

  .dynamic-panel {
    background: #f8f9fc;
    border: 1px dashed #dfe4f0;
    border-radius: 12px;
    padding: 1.5rem;
  }

  .form-group label {
    font-weight: 500;
    font-size: .85rem;
    color: #3e4b5b;
    margin-bottom: .4rem;
  }

  .form-control,
  .form-select {
    min-height: 46px;
    border: 1px solid #e2e6ee;
    border-radius: 8px;
    padding: .5rem .9rem;
    font-size: .925rem;
    color: #3e4b5b;
    background-color: #fbfbfd;
    width: 100%;
  }

  .form-control:focus,
  .form-select:focus {
    background-color: #fff;
    border-color: #4B7BEC;
    box-shadow: 0 0 0 .18rem rgba(75, 123, 236, .15);
  }

  .input-group-text {
    border: 1px solid #e2e6ee;
    border-right: none;
    background-color: #f1f3f9;
    color: #6b7690;
    font-weight: 500;
  }

  .input-group .form-control {
    border-radius: 0 8px 8px 0;
  }

  /* Icon-only Companies House search button */
  #searchCompanyBtn {
    display: none;
    align-items: center;
    justify-content: center;
    min-width: 46px;
    padding: 0 14px;
    border-radius: 0 8px 8px 0;
    font-size: 1.1rem;
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

  .is-invalid {
    border-color: #dc3545 !important;
    box-shadow: none !important;
  }

  .validation-error {
    color: #dc3545;
    font-size: 0.78rem;
    margin-top: 5px;
    display: block;
  }

  /* Yes/No radio buttons (Home Owner / VAT Registered) */
  .radio-field-label {
    display: block;
    font-weight: 500;
    font-size: 0.85rem;
    color: #3e4b5b;
    margin-bottom: 0.55rem;
  }

  .yes-no-group {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .yes-no-option {
    position: relative;
    margin: 0;
    padding: 0;
    cursor: pointer;
  }

  .yes-no-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }

  .yes-no-button {
    min-width: 95px;
    height: 46px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 0 18px;
    background: #fbfbfd;
    border: 1px solid #e2e6ee;
    border-radius: 8px;
    color: #6b7690;
    font-size: 0.9rem;
    font-weight: 500;
    transition:
      border-color 0.15s ease,
      background-color 0.15s ease,
      color 0.15s ease,
      box-shadow 0.15s ease,
      transform 0.15s ease;
  }

  .yes-no-option:hover .yes-no-button {
    border-color: #4B7BEC;
    background: rgba(75, 123, 236, 0.04);
    color: #4B7BEC;
  }

  .yes-no-option input:checked + .yes-no-button {
    background: rgba(75, 123, 236, 0.09);
    border-color: #4B7BEC;
    color: #4B7BEC;
    box-shadow: 0 3px 10px rgba(75, 123, 236, 0.12);
  }

  .yes-no-option input:checked + .yes-no-button i {
    color: #4B7BEC;
  }

  .yes-no-option input:focus-visible + .yes-no-button {
    outline: 2px solid rgba(75, 123, 236, 0.35);
    outline-offset: 2px;
  }

  .radio-group-error .yes-no-button {
    border-color: #dc3545;
  }

  .radio-group-error .radio-field-label {
    color: #dc3545;
  }

  @media (max-width: 575px) {
    .yes-no-button {
      min-width: 85px;
      padding: 0 14px;
    }
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
    font-size: .9rem;
    font-weight: 500;
  }

  .loan-purpose-option input:checked+span {
    border: 2px solid #4B7BEC;
    background: rgba(75, 123, 236, .08);
    color: #4B7BEC;
  }

  @media(max-width:991px) {
    .loan-purpose-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media(max-width:575px) {
    .loan-purpose-grid {
      grid-template-columns: 1fr;
    }
  }
  /* Full-screen loading overlay */
.form-loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(255, 255, 255, 0.85);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 14px;
    z-index: 99999;
}

.form-loading-overlay .spinner {
    width: 44px;
    height: 44px;
    border: 4px solid #e2e6ee;
    border-top-color: #4B7BEC;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

.form-loading-overlay span {
    font-size: 0.95rem;
    font-weight: 500;
    color: #3e4b5b;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Spinner inside the search button */
#searchCompanyBtn.is-loading i {
    display: none;
}

#searchCompanyBtn.is-loading::after {
    content: '';
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

#searchCompanyBtn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>


<script>
    function showFormLoader(message) {

    hideFormLoader();

    const overlay = document.createElement('div');

    overlay.className = 'form-loading-overlay';
    overlay.id = 'form-loading-overlay';

    overlay.innerHTML = `
        <div class="spinner"></div>
        <span>${message || 'Loading company information...'}</span>
    `;

    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
}

function hideFormLoader() {

    const overlay = document.getElementById('form-loading-overlay');

    if (overlay) {
        overlay.remove();
    }

    document.body.style.overflow = '';
}
  function showFieldError(field, message) {

    clearFieldError(field);

    field.classList.add('is-invalid');

    const error = document.createElement('div');

    error.className = 'js-field-error text-danger mt-1';

    error.style.fontSize = '0.8rem';

    error.textContent = message;

    field.closest('.form-group')?.appendChild(error);
  }

  function clearFieldError(field) {

    field.classList.remove('is-invalid');

    const parent = field.closest('.form-group');

    if (!parent) {
      return;
    }

    const existingError =
      parent.querySelector('.js-field-error');

    if (existingError) {
      existingError.remove();
    }
  }

  document.addEventListener('DOMContentLoaded', function() {

    const productSelect =
      document.getElementById('product_id');

    const restOfForm =
      document.getElementById('rest-of-form');

    const nfsAf4uFields =
      document.getElementById('nfs-af4u-fields');

    const auSaversFields =
      document.getElementById('au-savers-fields');

    function updateProductFields() {

      const productId =
        productSelect.value;

      if (productId) {
        clearFieldError(productSelect);
        restOfForm.style.display = 'block';
      } else {
        restOfForm.style.display = 'none';
      }

      nfsAf4uFields.style.display = 'none';
      auSaversFields.style.display = 'none';

      if (productId === '1' || productId === '2') {

        nfsAf4uFields.style.display = 'block';

      }

      if (productId === '3') {

        auSaversFields.style.display = 'block';

      }

    }

    productSelect.addEventListener(
      'change',
      updateProductFields
    );

    updateProductFields();

    const genericRequiredIds = [
      'gross_sales',
      'funds_required',
      'funds_term_months',
      'supply_address',
      'number_of_sites',
    ];

    genericRequiredIds.forEach(function(id) {

      const el = document.getElementById(id);

      if (!el) {
        return;
      }

      const eventName = (el.tagName === 'SELECT') ? 'change' : 'input';

      el.addEventListener(eventName, function() {

        if (this.value.trim()) {
          clearFieldError(this);
        }
      });
    });

    // Clear validation error as soon as a Yes/No radio option is picked
    ['home_owner', 'vat_registered'].forEach(function(name) {

      const radios = document.querySelectorAll(
        `input[name="${name}"]`
      );

      radios.forEach(function(radio) {

        radio.addEventListener('change', function() {

          if (this.checked) {

            const formGroup =
              this.closest('.form-group');

            if (formGroup) {

              formGroup.classList.remove(
                'radio-group-error'
              );

              const error =
                formGroup.querySelector(
                  '.js-field-error'
                );

              if (error) {
                error.remove();
              }
            }
          }

        });

      });

    });


    /*
     * Same registered address
     */

    const sameAddress =
      document.getElementById('same_address');

    const registeredAddress =
      document.getElementById(
        'business_registered_address'
      );

    const tradingAddress =
      document.getElementById(
        'business_trading_address'
      );

    if (sameAddress) {

      sameAddress.addEventListener(
        'change',
        function() {

          if (this.checked) {

            tradingAddress.value =
              registeredAddress.value;

            tradingAddress.readOnly = true;

          } else {

            tradingAddress.readOnly = false;

          }

        }
      );

      registeredAddress.addEventListener('input', function() {

        if (sameAddress.checked) {

          tradingAddress.value = this.value;

        }

      });

      if (sameAddress.checked) {

        tradingAddress.readOnly = true;

      }

    }

    const loanPurposeInputs =
      document.querySelectorAll(
        'input[name="loan_purpose"]'
      );

    const fundsUsageWrapper =
      document.getElementById(
        'funds-usage-details-wrapper'
      );

    const fundsUsageDetails =
      document.getElementById('funds_usage_details');

    function updateFundsUsage() {

      const selected =
        document.querySelector(
          'input[name="loan_purpose"]:checked'
        );

      if (
        selected &&
        selected.value === 'Other'
      ) {

        fundsUsageWrapper.style.display =
          'block';

      } else {

        fundsUsageWrapper.style.display =
          'none';

      }

    }

    loanPurposeInputs.forEach(function(input) {

      input.addEventListener(
        'change',
        updateFundsUsage
      );

    });

    // Important for existing data.
    updateFundsUsage();

    const companyTypeSelect =
      document.getElementById('company_type');

    const searchCompanyBtn =
      document.getElementById('searchCompanyBtn');

    companyTypeSelect.addEventListener('change', function() {

      if (this.value === 'Limited') {

        searchCompanyBtn.style.display = 'inline-flex';

      } else {

        searchCompanyBtn.style.display = 'none';

        // Clear Companies House related values
        document.getElementById('company_number').value = '';

        document.getElementById('companySearchResults').style.display = 'none';
        document.getElementById('companySearchResults').innerHTML = '';
      }

    });

    searchCompanyBtn.addEventListener('click', function() {

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

          const text = await response.text();

          console.log('Raw search response:', text);

          if (!text.trim()) {
            throw new Error(
              `Server returned an empty response. HTTP ${response.status}`
            );
          }

          let result;

          try {
            result = JSON.parse(text);
          } catch (error) {

            console.error('Invalid JSON returned by search endpoint:', text);

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

              item.addEventListener('click', function() {

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
        })
        .finally(() => {
            btn.classList.remove('is-loading');
            btn.disabled = false;
        });
    });


    function getCompanyDetails(companyNumber) {

        const resultsBox = document.getElementById('companySearchResults');

        if (!companyNumber) {
            resultsBox.innerHTML = `<div class="alert alert-danger">Company number is missing.</div>`;
            return;
        }

        resultsBox.innerHTML = `<div class="alert alert-info">Loading company information...</div>`;

        // Show full-screen loader right before fields start auto-filling
        showFormLoader('Fetching company details...');

        const url = `{{ url('/companies-house') }}/${encodeURIComponent(companyNumber)}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const text = await response.text();

            if (!text.trim()) {
                throw new Error(`Server returned an empty response. HTTP ${response.status}`);
            }

            let result;

            try {
                result = JSON.parse(text);
            } catch (e) {
                throw new Error(`Server returned invalid JSON. HTTP ${response.status}`);
            }

            if (!response.ok) {
                throw new Error(result.message || `HTTP ${response.status}`);
            }

            return result;
        })
        .then(result => {

            if (!result.success) {
                resultsBox.innerHTML = `
                    <div class="alert alert-danger">
                        ${result.message ?? 'Unable to load company information.'}
                    </div>
                `;
                return;
            }

            const company = result.data.company;
            const officers = result.data.officers;

            const director = officers.items?.find(function (officer) {
                return officer.officer_role && officer.officer_role.toLowerCase() === 'director';
            });

            if (director) {
                const customerName = document.getElementById('customer_name');
                const contactPerson = document.getElementById('contact_person');

                if (customerName) customerName.value = director.name ?? '';
                if (contactPerson) contactPerson.value = director.name ?? '';
            }

            if (company.company_status && company.company_status.toLowerCase() !== 'active') {
                resultsBox.innerHTML = `
                    <div class="alert alert-warning">
                        This company is not active and cannot be used for this application.
                    </div>
                `;
                return;
            }

            fillCompanyDetails(company);
            fillOfficerDetails(officers);
            showCompaniesHouseDob(officers);

            resultsBox.innerHTML = `
                <div class="alert alert-success">
                    Company information loaded successfully.
                </div>
            `;
        })
        .catch(error => {
            resultsBox.innerHTML = `
                <div class="alert alert-danger">
                    ${error.message}
                </div>
            `;
        })
        .finally(() => {
            hideFormLoader();
        });
    }

    function fillCompanyDetails(company) {
      console.log('Filling company details:', company);

      const companyName =
        document.getElementById('company_business_name');

      if (companyName) {
        companyName.value =
          company.company_name ?? '';
      }

      const companyNumber =
        document.getElementById('company_number');

      if (companyNumber) {
        companyNumber.value =
          company.company_number ?? '';
      }

      const companyType =
        document.getElementById('company_type');

      if (companyType) {
        companyType.value = 'Limited';
      }

      const businessStartDate =
        document.getElementById('business_start_date');

      if (businessStartDate) {
        businessStartDate.value =
          company.date_of_creation ?? '';
      }

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

        businessType.value = businessActivity;
      }

      const registeredAddressField =
        document.getElementById(
          'business_registered_address'
        );

      if (
        registeredAddressField &&
        company.registered_office_address
      ) {

        const address =
          company.registered_office_address;

        const addressParts = [];

        if (address.premises) {
          addressParts.push(address.premises.trim());
        }

        if (address.address_line_1) {
          addressParts.push(address.address_line_1.trim());
        }

        if (address.address_line_2) {
          addressParts.push(address.address_line_2.trim());
        }

        if (address.locality) {
          addressParts.push(address.locality.trim());
        }

        if (address.region) {
          addressParts.push(address.region.trim());
        }

        if (address.postal_code) {
          addressParts.push(address.postal_code.trim());
        }

        if (address.country) {
          addressParts.push(address.country.trim());
        }

        registeredAddressField.value =
          addressParts.join(', ');
      }

      const apiData =
        document.getElementById('company_api_data');

      if (apiData) {
        apiData.value =
          JSON.stringify(company);
      }

      console.log(
        'Company fields populated successfully.'
      );
    }

    function fillOfficerDetails(officers) {
      console.log('Filling officer details:', officers);

      if (
        !officers ||
        !Array.isArray(officers.items) ||
        officers.items.length === 0
      ) {
        console.log('No active officers found.');
        return;
      }

      const officer = officers.items.find(function(item) {
        return !item.resigned_on;
      });

      if (!officer) {
        console.log('No active officer found.');
        return;
      }

      const contactPerson =
        document.getElementById('contact_person');

      if (contactPerson) {
        contactPerson.value = officer.name ?? '';
      }

      const customerName =
        document.getElementById('customer_name');

      if (customerName) {
        customerName.value = officer.name ?? '';
      }

      console.log('Officer populated:', officer);
    }
    function showCompaniesHouseDob(officers)
    {
        const dobHint = document.getElementById('companies-house-dob-hint');

        if (!dobHint) {
            return;
        }

        // Clear previous message
        dobHint.style.display = 'none';
        dobHint.textContent = '';

        if (
            !officers ||
            !Array.isArray(officers.items)
        ) {
            return;
        }

        /*
        * Find active director with DOB information.
        */
        const director = officers.items.find(function (officer) {

            return (
                officer.officer_role &&
                officer.officer_role.toLowerCase() === 'director' &&
                officer.date_of_birth &&
                officer.date_of_birth.month &&
                officer.date_of_birth.year &&
                !officer.resigned_on
            );

        });

        if (!director) {
            return;
        }

        const month = director.date_of_birth.month;
        const year = director.date_of_birth.year;

        dobHint.textContent =
            `DOB information: Month ${month}, Year ${year}. Please enter the manually.`;

        dobHint.style.display = 'block';

        console.log(
            'Companies House DOB:',
            director.date_of_birth
        );
    }

    const applicationForm =
        document.querySelector('form[action="{{ route('leads.update', $lead->id) }}"]');


    if (applicationForm) {

      const phoneInput =
        document.getElementById('phone_no');

      if (phoneInput) {

        phoneInput.addEventListener('input', function() {

          const value = this.value.trim();

          this.value = value.replace(/\D/g, '');

          if (!this.value) {
            clearFieldError(this);
            return;
          }

          if (this.value.length !== 10) {

            showFieldError(
              this,
              'Phone number must contain exactly 10 digits.'
            );

          } else {

            clearFieldError(this);
          }
        });
      }

      const mobileInput =
        document.getElementById('mobile_no');

      if (mobileInput) {

        mobileInput.addEventListener('input', function() {

          const value = this.value.trim();

          this.value = value.replace(/\D/g, '');

          if (!this.value) {
            clearFieldError(this);
            return;
          }

          if (this.value.length !== 10) {

            showFieldError(
              this,
              'Mobile number must contain exactly 10 digits.'
            );

          } else {

            clearFieldError(this);
          }
        });
      }

      const postcodeInput =
        document.getElementById('postcode');

      if (postcodeInput) {

        postcodeInput.addEventListener('input', function() {

          const value = this.value.trim();

          if (!value) {
            clearFieldError(this);
            return;
          }

          if (!/^[A-Za-z0-9 ]+$/.test(value)) {

            showFieldError(
              this,
              'Postcode can contain only letters, numbers and spaces.'
            );

          } else if (value.length > 10) {

            showFieldError(
              this,
              'Postcode cannot be longer than 10 characters.'
            );

          } else {

            clearFieldError(this);
          }
        });
      }

      const mpanInput =
        document.getElementById('mpan');

      if (mpanInput) {

        mpanInput.addEventListener('input', function() {

          this.value =
            this.value.replace(/\D/g, '');

          if (!this.value) {
            clearFieldError(this);
            return;
          }

          if (this.value.length < 13) {

            showFieldError(
              this,
              'MPAN must contain at least 13 digits.'
            );

          } else {

            clearFieldError(this);
          }
        });
      }

      const mprnInput =
        document.getElementById('mprn');

      if (mprnInput) {

        mprnInput.addEventListener('input', function() {

          this.value =
            this.value.replace(/\D/g, '');

          if (!this.value) {
            clearFieldError(this);
            return;
          }

          if (this.value.length < 6) {

            showFieldError(
              this,
              'MPRN must contain at least 6 digits.'
            );

          } else {

            clearFieldError(this);
          }
        });
      }

      const spidInput =
        document.getElementById('spid');

      if (spidInput) {

        spidInput.addEventListener('input', function() {

          this.value =
            this.value.replace(/\D/g, '');

          if (!this.value) {
            clearFieldError(this);
            return;
          }

          if (this.value.length < 8) {

            showFieldError(
              this,
              'SPID must contain at least 8 digits.'
            );

          } else {

            clearFieldError(this);
          }
        });
      }

      applicationForm.addEventListener('submit', function(event) {

        let hasError = false;

        /*
         * Product (required)
         */
        const productId = productSelect.value;

        if (!productId) {

          showFieldError(
            productSelect,
            'Please select a product.'
          );

          hasError = true;

        } else {

          clearFieldError(productSelect);
        }

        if (phoneInput && phoneInput.value.trim()) {

          if (!/^[0-9]{10}$/.test(phoneInput.value.trim())) {

            showFieldError(
              phoneInput,
              'Phone number must contain exactly 10 digits.'
            );

            hasError = true;
          }
        }

        if (mobileInput && mobileInput.value.trim()) {

          if (!/^[0-9]{10}$/.test(mobileInput.value.trim())) {

            showFieldError(
              mobileInput,
              'Mobile number must contain exactly 10 digits.'
            );

            hasError = true;
          }
        }

        if (postcodeInput && postcodeInput.value.trim()) {

          const postcode =
            postcodeInput.value.trim();

          if (!/^[A-Za-z0-9 ]+$/.test(postcode)) {

            showFieldError(
              postcodeInput,
              'Postcode can contain only letters, numbers and spaces.'
            );

            hasError = true;

          } else if (postcode.length > 10) {

            showFieldError(
              postcodeInput,
              'Postcode cannot be longer than 10 characters.'
            );

            hasError = true;
          }
        }

        if (mpanInput && mpanInput.value.trim()) {

          if (!/^[0-9]+$/.test(mpanInput.value.trim())) {

            showFieldError(
              mpanInput,
              'MPAN must contain numbers only.'
            );

            hasError = true;

          } else if (mpanInput.value.trim().length < 13) {

            showFieldError(
              mpanInput,
              'MPAN must contain at least 13 digits.'
            );

            hasError = true;
          }
        }

        if (mprnInput && mprnInput.value.trim()) {

          if (!/^[0-9]+$/.test(mprnInput.value.trim())) {

            showFieldError(
              mprnInput,
              'MPRN must contain numbers only.'
            );

            hasError = true;

          } else if (mprnInput.value.trim().length < 6) {

            showFieldError(
              mprnInput,
              'MPRN must contain at least 6 digits.'
            );

            hasError = true;
          }
        }

        if (spidInput && spidInput.value.trim()) {

          if (!/^[0-9]+$/.test(spidInput.value.trim())) {

            showFieldError(
              spidInput,
              'SPID must contain numbers only.'
            );

            hasError = true;

          } else if (spidInput.value.trim().length < 8) {

            showFieldError(
              spidInput,
              'SPID must contain at least 8 digits.'
            );

            hasError = true;
          }
        }

        if (hasError) {

          event.preventDefault();

          const firstError =
            document.querySelector('.js-field-error');

          if (firstError) {

            firstError.scrollIntoView({
              behavior: 'smooth',
              block: 'center'
            });
          }
        }

      });
    }

  });
</script>

@endsection
