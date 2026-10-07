@extends('layout')

@section('title', 'Leads')
@section('subtitle', 'Edit Lead')

@section('content')

<div class="row">

  <div class="col-12 grid-margin stretch-card">

    <div class="card" id="leadFormCard">
      <div class="card-body">

        <div class="lead-form-header">
          <div class="lead-form-header-main">
            <div class="lead-form-eyebrow">Lead Management</div>
            <h4 class="card-title">Edit Lead</h4>
            <p class="card-description">Update this lead's details below.</p>
          </div>

          <a href="{{ route('leads.index') }}" class="btn lead-form-back-btn">
            <i class="mdi mdi-arrow-left"></i>
            Back
          </a>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('leads.update', $lead) }}" class="forms-sample" enctype="multipart/form-data" novalidate>

          @csrf
          @method('PUT')

          {{-- Product --}}
          <div class="form-group mb-5">

            <label class="radio-field-label">
              Product<span class="text-danger">*</span>
            </label>

            <div id="product-radio-group" class="yes-no-group">
              @foreach($products as $product)
                <label class="yes-no-option">
                  <input
                    type="radio"
                    name="product_id"
                    id="product_id_{{ $product->id }}"
                    value="{{ $product->id }}"
                    @checked(old('product_id', $lead->product_id) == $product->id)
                  >

                  <span class="yes-no-button product-button">
                    {{ $product->name }}
                  </span>
                </label>
              @endforeach
            </div>

            @error('product_id')
            <div class="validation-error">{{ $message }}</div>
            @enderror

          </div>

          <div id="rest-of-form">

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

                      <button type="button" class="btn btn-primary" id="searchCompanyBtn" title="Search Companies House" style="{{ in_array(old('company_type', $lead->company_type), ['Limited', 'Limited Liability Partnership']) ? 'display:inline-flex;' : '' }}">
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

                  <input type="date" name="business_start_date" id="business_start_date" class="form-control" value="{{ old('business_start_date', optional($lead->business_start_date)->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}">

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


              {{-- Supply Address (formerly Business Trading Address) -
                   saved to supply_address. An older lead with only a
                   business_trading_address shows that here instead (saving
                   then copies it into supply_address); the old column
                   itself is never changed. For a site lead this is that
                   site's own address. --}}
              @php
                $supplyAddressValue = old('supply_address', filled($lead->supply_address) ? $lead->supply_address : $lead->business_trading_address);
              @endphp

              <div class="col-md-6">

                <div class="form-group">

                  <label for="supply_address">
                    Supply Address
                  </label>

                  <input type="text" name="supply_address" id="supply_address" class="form-control" value="{{ $supplyAddressValue }}" placeholder="Enter supply address">

                  {{-- A <label>, so its text toggles the checkbox too - sized to
                       its content (.same-address-label), so the rest of the row
                       doesn't. template.js adds the theme's visible box inside it. --}}
                  <div class="form-check mt-2">

                    <label class="form-check-label same-address-label" for="same_address">
                      <input type="checkbox" class="form-check-input" id="same_address" name="same_as_registered_address" value="1" {{ old('same_as_registered_address', $lead->same_as_registered_address) ? 'checked' : '' }}>
                      Same as Business Registered Address
                    </label>

                  </div>

                </div>

              </div>

            </div>


            {{-- Customer Contact Information --}}
            <div class="section-heading mt-4 mb-3">

              <i class="mdi mdi-account-outline"></i>

              <div>

                <h4 class="card-title mb-1">
                  Customer Contact Information
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

                  <label for="dob_day">
                    Date of Birth
                  </label>

                  {{-- Day / Month / Year, always shown - same as Add Lead
                       (see DateOfBirthParts). Pre-selected from the lead's
                       saved Date of Birth. --}}
                  @php
                    $dobDay = (int) old('dob_day', $lead->date_of_birth?->day);
                    $dobMonth = (int) old('dob_month', $lead->date_of_birth?->month);
                    $dobYear = (int) old('dob_year', $lead->date_of_birth?->year);
                  @endphp

                  <div class="dob-selects">
                    <select name="dob_day" id="dob_day" class="form-select @error('dob_day') is-invalid @enderror" aria-label="Date of birth day">
                      <option value="">Day</option>
                      @for ($day = 1; $day <= 31; $day++)
                        <option value="{{ $day }}" @selected($dobDay === $day)>{{ $day }}</option>
                      @endfor
                    </select>

                    <select name="dob_month" id="dob_month" class="form-select @error('dob_month') is-invalid @enderror" aria-label="Date of birth month">
                      <option value="">Month</option>
                      @foreach ([1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'] as $value => $label)
                        <option value="{{ $value }}" @selected($dobMonth === $value)>{{ $label }}</option>
                      @endforeach
                    </select>

                    <select name="dob_year" id="dob_year" class="form-select @error('dob_year') is-invalid @enderror" aria-label="Date of birth year">
                      <option value="">Year</option>
                      @for ($year = (int) date('Y'); $year >= \App\Support\DateOfBirthParts::OLDEST_YEAR; $year--)
                        <option value="{{ $year }}" @selected($dobYear === $year)>{{ $year }}</option>
                      @endfor
                    </select>
                  </div>

                  @if ($dobError = collect(['dob_day', 'dob_month', 'dob_year', 'date_of_birth'])->map(fn ($field) => $errors->first($field))->filter()->first())
                    <div class="validation-error" id="dob-server-error">{{ $dobError }}</div>
                  @endif

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

                  <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $lead->email) }}" placeholder="Enter email address">

                  @error('email')
                    <div class="validation-error">{{ $message }}</div>
                  @enderror

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

                        <textarea
                            name="notes"
                            id="notes"
                            rows="4"
                            class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Enter any additional notes about this lead"
                            maxlength="5000"
                        >{{ old('notes', $lead->notes) }}</textarea>

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

                      <input type="text" name="gross_sales" id="gross_sales" class="form-control" value="{{ old('gross_sales', $lead->gross_sales) }}" placeholder="Enter gross sales" inputmode="decimal" autocomplete="off">

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

                      <input type="text" name="funds_required" id="funds_required" class="form-control" value="{{ old('funds_required', $lead->funds_required) }}" placeholder="Enter funds required" inputmode="decimal" autocomplete="off">

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
                          @if ($purpose === 'Fund vehicle, equipment or machinery')
                            Fund vehicle,<br>
                            equipment or machinery
                          @else
                            {{ $purpose }}
                          @endif
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

              {{-- Same order and Multiple Site behaviour as Add Lead: Number of
                   Sites, Sites Count and the Sites CSV, then Postcode, Supply
                   Address, MPAN, MPRN, SPID. For an unexpanded Multiple Site
                   draft the sites-csv partial hides and disables those five
                   site fields. --}}
              <div class="row">


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

                    @if ($lead->base_lead_id)
                      <small class="text-muted" style="display:block; margin-top:4px;">
                        Part of multisite batch {{ $lead->base_lead_id }} (site {{ $lead->site_sequence }}).
                      </small>
                    @endif

                  </div>

                </div>

                {{-- A draft not yet expanded into its site leads can still
                     set its site count and upload / replace its sites CSV,
                     which it needs before it can be published. --}}
                @if (is_null($lead->base_lead_id) && $lead->isDraft())
                  @php
                    $editNumberOfSites = old('number_of_sites', $lead->number_of_sites);
                  @endphp

                  <div class="col-md-6" id="sites-count-wrapper" style="{{ $editNumberOfSites === 'Multiple Site' ? '' : 'display:none;' }}">

                    <div class="form-group">

                      <label for="sites_count">
                        Number of Sites to Create
                      </label>

                      <input type="number" name="sites_count" id="sites_count" class="form-control" min="1" max="500" step="1" placeholder="Enter a number between 1 and 500" value="{{ old('sites_count', $lead->sites_count) }}">

                      <small class="text-muted">
                        Creates one site lead per number entered, e.g. 20 sites creates 20 individual site leads sharing one base Lead ID.
                        Saving as <strong>Draft</strong> only saves this as a single lead for now -
                        the site leads are created once it's <strong>Published</strong>.
                      </small>

                    </div>

                  </div>

                  @include('leads.partials.sites-csv', [
                    'numberOfSites' => $editNumberOfSites,
                    'heldSitesCount' => count($lead->pending_sites ?? []),
                  ])
                @endif


                {{-- Postcode --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="postcode">
                      Postcode
                    </label>

                    <input type="text" name="postcode" id="postcode" class="form-control" value="{{ old('postcode', $lead->postcode) }}" placeholder="Enter postcode">

                  </div>

                </div>


                {{-- Supply Address - display only, showing the Supply Address
                     field above (see refreshSupplyAddressDisplay()). For a site
                     lead that is the site's own saved address; for an
                     unexpanded Multiple Site draft each site's comes from the
                     Sites CSV. --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label>
                      Supply Address
                    </label>

                    <div id="supply-address-display" class="form-control supply-address-display {{ filled($supplyAddressValue) ? '' : 'is-muted' }}" aria-live="polite">{{ filled($supplyAddressValue) ? $supplyAddressValue : '-' }}</div>

                  </div>

                </div>


                {{-- MPAN --}}
                <div class="col-md-6">

                  <div class="form-group">

                    <label for="mpan">
                      MPAN
                    </label>

                    <input type="text" name="mpan" id="mpan" class="form-control" value="{{ old('mpan', $lead->mpan) }}" placeholder="Enter MPAN">

                    @error('mpan')
                      <div class="validation-error text-danger small mt-1">{{ $message }}</div>
                    @enderror

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
                Update
              </button>

              {{-- Publishing is one-way - once a lead is published,
                   nobody can move it back to draft (enforced server-side
                   too, in LeadController::statusCannotRevertFromPublished()),
                   so this option simply isn't offered any more. --}}
              @if ($lead->isDraft())
                <button type="submit" name="status" value="draft" class="btn btn-light me-3 px-4 save-as-draft">
                  <i class="mdi mdi-file-document-edit-outline me-1"></i>
                  Save as Draft
                </button>
              @endif

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

  /* ==========================================================
     Header - same eyebrow/title language as leads/index.blade.php,
     plus a Back button at the top-right (same .ls2-btn-ghost look
     as the one on the Lead Details page) so getting back to the
     list doesn't require scrolling all the way down to Cancel.
     ========================================================== */
  #leadFormCard .lead-form-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    flex-wrap: wrap;
    padding-bottom: 20px;
    border-bottom: 1px solid #eef0f3;
    margin-bottom: 24px;
  }

  #leadFormCard .lead-form-header-main {
    min-width: 0;
  }

  #leadFormCard .lead-form-back-btn {
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
    transition: background 0.12s ease, color 0.12s ease;
  }

  #leadFormCard .lead-form-back-btn:hover,
  #leadFormCard .lead-form-back-btn:focus {
    background: #f4f5f7;
    color: #384153;
  }

  @media (max-width: 575px) {
    #leadFormCard .lead-form-header {
      flex-direction: column;
    }

    #leadFormCard .lead-form-back-btn {
      width: 100%;
      justify-content: center;
    }
  }

  #leadFormCard .lead-form-eyebrow {
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

  #leadFormCard .lead-form-eyebrow::before {
    content: '';
    width: 16px;
    height: 2px;
    background: #6c63ff;
    display: inline-block;
  }

  #leadFormCard .card-title {
    font-weight: 700;
    font-size: 27px;
    color: #1a1f2b;
    letter-spacing: -0.3px;
    margin-bottom: 4px;
  }

  #leadFormCard .card-description {
    color: #8a92a3;
    font-size: 13.5px;
    margin: 0;
  }

  /* ==========================================================
     Buttons - same look as leads/index.blade.php's action
     buttons (.btn-add-lead, .action-btns), scoped to this card
     so it doesn't affect .btn-primary/.btn-light/.btn-secondary
     on any other page.
     ========================================================== */
  /* Update, Save as Draft and Cancel all share one shape (padding
     from the existing px-4 utility, same border-radius, same
     font-weight, same hover lift) - only the color differs per
     action, each reusing an existing app token rather than a new
     one: indigo for the primary save action, the same amber already
     used for the Draft status badge/toggle elsewhere for "save as
     draft", and a neutral ghost for Cancel. */
  #leadFormCard .btn-primary,
  #leadFormCard .btn-secondary,
  #leadFormCard .save-as-draft {
    border-radius: 9px !important;
    font-weight: 500;
    transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease, color 0.12s ease, border-color 0.12s ease;
  }

  #leadFormCard .btn-primary {
    background: #6c63ff;
    border-color: #6c63ff;
    box-shadow: 0 2px 6px rgba(108, 99, 255, 0.28);
  }

  #leadFormCard .btn-primary:hover,
  #leadFormCard .btn-primary:focus {
    background: #5b52e8;
    border-color: #5b52e8;
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(108, 99, 255, 0.34);
  }

  #leadFormCard .btn-primary:disabled {
    transform: none;
    box-shadow: none;
    opacity: 0.65;
  }

  #leadFormCard .save-as-draft {
    background: #fff3cd;
    border: 1px solid #fbd469;
    color: #8a6d00;
  }

  #leadFormCard .save-as-draft:hover,
  #leadFormCard .save-as-draft:focus {
    background: #fbe8a6;
    border-color: #f0c419;
    color: #6b5400;
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(224, 168, 0, 0.22);
  }

  #leadFormCard .btn-secondary {
    background: #fff;
    border: 1px solid #e2e5eb;
    color: #6c7280;
  }

  #leadFormCard .btn-secondary:hover,
  #leadFormCard .btn-secondary:focus {
    background: #f4f5f7;
    color: #384153;
    border-color: #e2e5eb;
  }

  .section-heading {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
    padding-bottom: .85rem;
    border-bottom: 1px solid #eef0f3;
  }

  .action-buttons {
    gap: 10px;
    flex-wrap: wrap;
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

  .section-heading .card-title {
    font-size: 1.05rem;
  }

  .dynamic-panel {
    background: #f8f9fb;
    border: 1px dashed #e2e5eb;
    border-radius: 12px;
    padding: 1.5rem;
  }

  .form-group label {
    font-weight: 500;
    font-size: .85rem;
    color: #384153;
    margin-bottom: .4rem;
  }

  .form-control,
  .form-select {
    min-height: 46px;
    border: 1px solid #e2e5eb;
    border-radius: 8px;
    padding: .5rem .9rem;
    font-size: .925rem;
    color: #384153;
    background-color: #fbfbfd;
    width: 100%;
  }

  .form-control:focus,
  .form-select:focus {
    background-color: #fff;
    border-color: #6c63ff;
    box-shadow: 0 0 0 .18rem rgba(108, 99, 255, .15);
  }

  .input-group-text {
    border: 1px solid #e2e5eb;
    border-right: none;
    background-color: #f4f5f8;
    color: #6c7280;
    font-weight: 500;
  }

  .input-group .form-control {
    border-radius: 0 8px 8px 0;
  }

  /* Icon-only Companies House search button */
  /* Same-address checkbox: its <label> is only as wide as the box and
     its text (inline-flex, not the theme's full-width block), so a
     click elsewhere on the row doesn't toggle it. The theme hides the
     real input over the drawn 18px box - make the input cover the
     whole box so any click on it lands. */
  .form-check .same-address-label {
    display: inline-flex;
    cursor: pointer;
  }

  .form-check .form-check-label #same_address {
    width: 18px;
    height: 18px;
  }

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
    border: 1px solid #e2e5eb;
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
    background: #f4f6fb;
  }

  .company-result-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1a1f2b;
    line-height: 1.4;
  }

  .company-result-number {
    margin-top: 3px;
    font-size: 0.8rem;
    color: #6c7280;
  }

  .company-result:active {
    background: #eef0ff;
  }

  .is-invalid {
    border-color: #d33a3a !important;
    box-shadow: none !important;
  }

  .validation-error {
    color: #d33a3a;
    font-size: 0.78rem;
    margin-top: 5px;
    display: block;
  }

  /* Yes/No radio buttons (Home Owner / VAT Registered) */
  .radio-field-label {
    display: block;
    font-weight: 500;
    font-size: 0.85rem;
    color: #384153;
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
    border: 1px solid #e2e5eb;
    border-radius: 8px;
    color: #6c7280;
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
    border-color: #6c63ff;
    background: rgba(108, 99, 255, 0.04);
    color: #6c63ff;
  }

  .yes-no-option input:checked + .yes-no-button {
    background: rgba(108, 99, 255, 0.09);
    border-color: #6c63ff;
    color: #6c63ff;
    box-shadow: 0 3px 10px rgba(108, 99, 255, 0.12);
  }

  .yes-no-option input:checked + .yes-no-button i {
    color: #6c63ff;
  }

  .yes-no-option input:focus-visible + .yes-no-button {
    outline: 2px solid rgba(108, 99, 255, 0.35);
    outline-offset: 2px;
  }

  .radio-group-error .yes-no-button {
    border-color: #d33a3a;
  }

  .radio-group-error .radio-field-label {
    color: #d33a3a;
  }

  @media (max-width: 575px) {
    .yes-no-button {
      min-width: 85px;
      padding: 0 14px;
    }

    /* Full-width, stacked action buttons - easier to tap and
       matches how leads/index.blade.php stacks its own header
       button on mobile. */
    #leadFormCard .action-buttons {
      flex-direction: column;
      align-items: stretch !important;
    }

    #leadFormCard .action-buttons .btn {
      width: 100%;
      justify-content: center;
      margin: 0 0 10px !important;
    }

    #leadFormCard .action-buttons .btn:last-child {
      margin-bottom: 0 !important;
    }
  }

  #product-radio-group label {
    cursor: pointer;
    margin-bottom: 0 !important;
  }
  #product-radio-group input[type="radio"] {
    width: 18px;
    height: 18px;
    margin: 0;
    accent-color: #6c63ff;
    cursor: pointer;
    flex-shrink: 0;
  }

  /* Product buttons - all three (or however many products exist)
     share one uniform size/shape (equal width, same padding/radius
     as every other .yes-no-button) AND the same single selected
     color as every other yes/no option on this page (the generic
     .yes-no-option input:checked + .yes-no-button rule already
     covers that) - kept deliberately uniform, not color-coded per
     product, since differentiating them by color read as confusing
     rather than helpful. */
  #product-radio-group.yes-no-group {
    flex-wrap: wrap;
  }

  #product-radio-group .yes-no-option {
    flex: 1 1 140px;
  }

  .product-button {
    width: 100%;
    min-width: 0;
    text-align: center;
    justify-content: center;
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }

  .product-button i {
    display: none !important;
  }

  /* Mobile - stack product options full-width instead of squeezing
     them into a row that no longer fits once they can't shrink
     below their equal-width flex-basis; easier to tap too. */
  @media (max-width: 575px) {
    #product-radio-group.yes-no-group {
      flex-direction: column;
      align-items: stretch;
    }

    #product-radio-group .yes-no-option {
      flex: 1 1 auto;
      width: 100%;
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
    border: 1px solid #e2e5eb;
    border-radius: 12px;
    color: #384153;
    font-size: .9rem;
    font-weight: 500;
  }

  .loan-purpose-option input:checked+span {
    border: 2px solid #6c63ff;
    background: rgba(108, 99, 255, .08);
    color: #6c63ff;
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
    border: 4px solid #e2e5eb;
    border-top-color: #6c63ff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

.form-loading-overlay span {
    font-size: 0.95rem;
    font-weight: 500;
    color: #384153;
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

  /* Kept in step with Add Lead (leads/create.blade.php) - same field,
     checkbox, select and Loan Purpose styling, so both forms look alike. */
  .form-group label {
    font-size: 14px;
    color: #22252a;
  }

  .form-control,
  .form-select {
    transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
  }

  textarea.form-control {
    min-height: unset;
  }

  .form-control:hover,
  .form-select:hover {
    border-color: #c9d1e3;
  }

  .form-control::placeholder {
    color: #a4aab5;
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

  .input-group-text {
    border-radius: 8px 0 0 8px;
  }

  .input-group:focus-within .input-group-text {
    border-color: #6c63ff;
    color: #6c63ff;
  }

  .input-group + .validation-error {
    margin-top: 5px;
  }

  /* Checkbox row */
  .form-check-label {
    font-size: 0.875rem;
    color: #6c7280;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .form-check-input {
    cursor: pointer;
  }

  .form-check .form-check-label #same_address + .input-helper {
    margin-right: -0.5rem;
  }

  .btn-light {
    border-radius: 8px;
    font-weight: 500;
    border: 1px solid #e2e5eb;
  }

  .loan-purpose-section {
    margin-top: 0.5rem;
  }

  .loan-purpose-section.has-error {
    border: 1px solid #d33a3a;
    border-radius: 10px;
    padding: 12px;
  }

  .loan-purpose-title {
    display: block;
    font-size: 1rem !important;
    font-weight: 600 !important;
    color: #384153 !important;
    margin-bottom: 1rem !important;
  }

  .loan-purpose-option span {
    transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease, transform .15s ease;
  }

  .loan-purpose-option:hover span {
    border-color: #6c63ff;
    background: rgba(108, 99, 255, 0.04);
  }

  .loan-purpose-option input:checked + span {
    box-shadow: 0 4px 12px rgba(108, 99, 255, 0.12);
  }

  /* AU Savers Supply Address - looks like the other fields but is
     display-only (mirrors the Supply Address field above). */
  .supply-address-display {
    display: flex;
    align-items: center;
    height: auto;
    background-color: #f4f5f8;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    cursor: default;
  }

  .supply-address-display:hover {
    border-color: #e2e5eb;
  }

  .supply-address-display.is-muted {
    color: #8a92a3;
  }

  /* Date of Birth - Day / Month / Year dropdowns side by side */
  .dob-selects {
    display: flex;
    gap: 8px;
  }

  .dob-selects .form-select {
    flex: 1 1 0;
    min-width: 0;
    padding-left: 0.7rem;
    padding-right: 1.8rem;
    background-position: right 0.6rem center;
  }
</style>


<script>
    // Pressing Enter while typing in a field (Company Name, Phone,
    // etc.) was submitting the whole form via the browser's default
    // "Enter submits the first submit button" behavior - with two
    // differently-valued submit buttons (Update/Draft) that's
    // surprising and not something the user asked for, so block it.
    // Enter still works normally inside a textarea (new line) and
    // on the buttons themselves (activates them, as expected).
    document.addEventListener('DOMContentLoaded', function () {
        var leadForm = document.querySelector('#leadFormCard form.forms-sample');

        if (!leadForm) {
            return;
        }

        leadForm.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') {
                return;
            }

            var tag = e.target.tagName;

            if (tag === 'TEXTAREA' || tag === 'BUTTON') {
                return;
            }

            if (tag === 'INPUT' && e.target.type === 'submit') {
                return;
            }

            e.preventDefault();
        });
    });

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

    // ============================================================
    // Currency / Amount Formatting
    // ============================================================

    function formatAmount(value) {
      value = value.replace(/[^\d.]/g, '');
      const parts = value.split('.');

      let integerPart = parts[0] || '';
      let decimalPart = parts.length > 1
        ? parts[1].substring(0, 2)
        : null;

      integerPart = integerPart.replace(/^0+(?=\d)/, '');

      integerPart = integerPart.replace(
        /\B(?=(\d{3})+(?!\d))/g,
        ','
      );

      if (decimalPart !== null) {
        return integerPart + '.' + decimalPart;
      }

      return integerPart;
    }

    const amountFields = [
      document.getElementById('gross_sales'),
      document.getElementById('funds_required')
    ];

    amountFields.forEach(function(field) {

      if (!field) {
        return;
      }

      // Format existing / old Laravel value on page load
      if (field.value) {
        field.value = formatAmount(field.value);
      }

      // Format while typing
      field.addEventListener('input', function() {

        const cursorPosition = this.selectionStart;
        const oldValue = this.value;

        this.value = formatAmount(this.value);

        const commaCountBefore =
          (oldValue.substring(0, cursorPosition).match(/,/g) || []).length;

        const commaCountAfter =
          (this.value.substring(0, cursorPosition).match(/,/g) || []).length;

        const newCursorPosition =
          cursorPosition + (commaCountAfter - commaCountBefore);

        try {
          this.setSelectionRange(
            newCursorPosition,
            newCursorPosition
          );
        } catch (e) {
          // Ignore cursor positioning errors
        }

      });

    });

    // Product radio buttons (replaces old <select>)
    const productRadios =
      document.querySelectorAll('input[name="product_id"]');

    const productRadioGroup =
      document.getElementById('product-radio-group');

    const restOfForm =
      document.getElementById('rest-of-form');

    const nfsAf4uFields =
      document.getElementById('nfs-af4u-fields');

    const auSaversFields =
      document.getElementById('au-savers-fields');

    function clearProductError() {

      if (!productRadioGroup) {
        return;
      }

      productRadioGroup.classList.remove('radio-group-error');

      const parent = productRadioGroup.closest('.form-group');

      if (parent) {

        const error = parent.querySelector('.js-field-error, .validation-error');

        if (error) {
          error.remove();
        }
      }
    }

    function updateProductFields(productId) {

      // The rest of the form is always visible since a product
      // is always selected for an existing lead.
      if (restOfForm) {
        restOfForm.style.display = 'block';
      }

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

    }

    productRadios.forEach(function(radio) {

      radio.addEventListener('change', function() {

        clearProductError();
        updateProductFields(this.value);
      });
    });

    // Run once on page load so the pre-selected product's
    // panel is shown immediately.
    (function() {

      const checkedProduct =
        document.querySelector('input[name="product_id"]:checked');

      if (checkedProduct) {
        updateProductFields(checkedProduct.value);
      }

    })();

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

    const supplyAddress =
      document.getElementById('supply_address');

    // The AU Savers panel's Supply Address is display-only - it
    // mirrors the Supply Address field above. Called wherever that
    // field changes, including from code (setting .value fires no
    // input event). For an unexpanded Multiple Site draft each site's
    // address comes from the Sites CSV instead, so there's no single
    // one to show; a site lead's own address is shown as it is.
    function refreshSupplyAddressDisplay() {

      const display = document.getElementById('supply-address-display');

      if (!display || !supplyAddress) {
        return;
      }

      const numberOfSites = document.getElementById('number_of_sites');

      const perSite =
        numberOfSites &&
        numberOfSites.value === 'Multiple Site' &&
        document.getElementById('sites-csv-wrapper');

      const value = supplyAddress.value.trim();

      display.textContent = perSite
        ? 'Supply Address per site comes from the Sites CSV.'
        : (value || '-');

      display.classList.toggle('is-muted', Boolean(perSite) || !value);
    }

    if (supplyAddress) {

      supplyAddress.addEventListener('input', refreshSupplyAddressDisplay);

      document.getElementById('number_of_sites')
        ?.addEventListener('change', refreshSupplyAddressDisplay);
    }

    if (sameAddress) {

      sameAddress.addEventListener(
        'change',
        function() {

          if (this.checked) {

            supplyAddress.value =
              registeredAddress.value;

            supplyAddress.readOnly = true;

          } else {

            supplyAddress.readOnly = false;

            // Clear supply address when unchecked
            supplyAddress.value = '';

          }

          refreshSupplyAddressDisplay();

        }
      );

      registeredAddress.addEventListener('input', function() {

        if (sameAddress.checked) {

          supplyAddress.value = this.value;

          refreshSupplyAddressDisplay();

        }

      });

      if (sameAddress.checked) {

        supplyAddress.readOnly = true;

      }

    }

    refreshSupplyAddressDisplay();

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

      input.addEventListener('change', function() {

        updateFundsUsage();

        // Clear previous value once "Other" is no longer selected
        if (this.value !== 'Other') {
          fundsUsageDetails.value = '';
        }

      });

    });

    // Important for existing data.
    updateFundsUsage();

    const companyTypeSelect =
      document.getElementById('company_type');

    const searchCompanyBtn =
      document.getElementById('searchCompanyBtn');

    // Same as Add Lead: the Companies House search is offered once a
    // company type is chosen, whichever type it is.
    companyTypeSelect.addEventListener('change', function() {

      searchCompanyBtn.style.display = 'inline-flex';

    });

    // Enter in Company / Business Name runs the Companies House search
    // (same as the search button) instead of submitting the whole form.
    document.getElementById('company_business_name').addEventListener('keydown', function(e) {

      if (e.key !== 'Enter' || e.isComposing) {
        return;
      }

      e.preventDefault();

      if (getComputedStyle(searchCompanyBtn).display !== 'none' && !searchCompanyBtn.disabled) {
        searchCompanyBtn.click();
      }
    });

    searchCompanyBtn.addEventListener('click', function() {

      const companyName = document
        .getElementById('company_business_name')
        .value
        .trim();

      if (!companyName) {
        alert('Please enter company name.');
        return;
      }

      const btn = this;

      btn.classList.add('is-loading');
      btn.disabled = true;

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


    // e.g. "dissolved", "liquidation" - HTML-escaped for the alert.
    function escapeCompanyStatus(status) {
        const div = document.createElement('div');
        div.textContent = String(status).replace(/-/g, ' ');
        return div.innerHTML;
    }

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

            // Checked before anything is filled in, so a dissolved company never
            // leaves the form half-populated.
            if (company.company_status && company.company_status.toLowerCase() !== 'active') {
                resultsBox.innerHTML = `
                    <div class="alert alert-warning">
                        This company is ${escapeCompanyStatus(company.company_status)} on Companies House and cannot be used for this application.
                    </div>
                `;
                return;
            }

            fillCompanyDetails(company, result.data.company_type);
            fillOfficerDetails(officers);
            showCompaniesHouseDob(officers);

            // New company selected — supply address should be empty
            if (supplyAddress) {
                supplyAddress.value = '';
                supplyAddress.readOnly = false;
            }

            if (sameAddress) {
                sameAddress.checked = false;
            }

            refreshSupplyAddressDisplay();

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

    function fillCompanyDetails(company, companyTypeOption) {
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

      // The Company Type option matching Companies House's type (see
      // BusinessTypeMapper::companyTypeOption()) - left alone when
      // there isn't one.
      if (companyType && companyTypeOption) {
        companyType.value = companyTypeOption;
      }

      const businessStartDate =
        document.getElementById('business_start_date');

      if (businessStartDate) {
        businessStartDate.value =
          company.date_of_creation ?? '';
      }

      const businessType =
        document.getElementById('business_type');

      // Same as the create form: the company's type, already turned
      // into a readable label by BusinessTypeMapper.
      if (businessType) {
        businessType.value = company.type ?? '';
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

      // An active director first, otherwise any active officer.
      const officer =
        officers.items.find(function(item) {

          return (
            item.officer_role &&
            item.officer_role.toLowerCase() === 'director' &&
            !item.resigned_on
          );

        }) ||
        officers.items.find(function(item) {
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
    // Date of Birth - Day / Month / Year dropdowns, checked the same way
    // as the server (see App\Support\DateOfBirthParts): all three blank
    // is fine, otherwise all three are needed and must make a real date
    // that isn't in the future.
    const DOB_MESSAGES = @json(\App\Support\DateOfBirthParts::messages() + \Illuminate\Support\Arr::only(\App\Support\LeadValidationRules::messages(), ['date_of_birth.before_or_equal']));

    function dobSelects()
    {
        return {
            day: document.getElementById('dob_day'),
            month: document.getElementById('dob_month'),
            year: document.getElementById('dob_year'),
        };
    }

    // [dropdown to highlight, message] for the first problem, or null.
    function dateOfBirthError()
    {
        const dob = dobSelects();

        if (!dob.day || !dob.month || !dob.year) {
            return null;
        }

        const day = dob.day.value;
        const month = dob.month.value;
        const year = dob.year.value;

        if (!day && !month && !year) {
            return null;
        }

        if (!day) {
            return [dob.day, DOB_MESSAGES['dob_day.required_with']];
        }

        if (!month) {
            return [dob.month, DOB_MESSAGES['dob_month.required_with']];
        }

        if (!year) {
            return [dob.year, DOB_MESSAGES['dob_year.required_with']];
        }

        // e.g. 31 February rolls over into March.
        const date = new Date(Number(year), Number(month) - 1, Number(day));

        if (date.getMonth() !== Number(month) - 1) {
            return [dob.day, DOB_MESSAGES['date_of_birth.date']];
        }

        if (date > new Date()) {
            return [dob.day, DOB_MESSAGES['date_of_birth.before_or_equal']];
        }

        return null;
    }

    // Shows (or clears) the one Date of Birth message under the three
    // dropdowns. Returns true when there is a problem.
    function checkDateOfBirth()
    {
        const dob = dobSelects();

        if (!dob.day || !dob.month || !dob.year) {
            return false;
        }

        [dob.day, dob.month, dob.year].forEach(clearFieldError);

        document.getElementById('dob-server-error')?.remove();

        const error = dateOfBirthError();

        if (error) {
            showFieldError(error[0], error[1]);
        }

        return Boolean(error);
    }

    function showCompaniesHouseDob(officers)
    {
        const dobHint = document.getElementById('companies-house-dob-hint');
        const dob = dobSelects();

        if (!dobHint || !dob.day || !dob.month || !dob.year) {
            return;
        }

        // Clear previous state
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

        // Companies House only gives the month and year - the Day is
        // left unselected for the user to pick.
        dob.day.value = '';
        dob.month.value = String(Number(director.date_of_birth.month));
        dob.year.value = String(director.date_of_birth.year);

        [dob.day, dob.month, dob.year].forEach(clearFieldError);
        document.getElementById('dob-server-error')?.remove();

        const monthName = dob.month.selectedOptions[0]?.text ?? '';

        dobHint.textContent =
            `Companies House provided a partial Date of Birth (${monthName} ${director.date_of_birth.year}). Month and Year have been pre-selected - please select the Day.`;

        dobHint.style.display = 'block';
    }

    // Once a Date of Birth message is showing, re-check on every change
    // so it clears as soon as it's fixed - but don't nag while a date is
    // still being picked.
    ['dob_day', 'dob_month', 'dob_year'].forEach(function (id) {

        const select = document.getElementById(id);

        if (!select) {
            return;
        }

        select.addEventListener('change', function () {

            const group = this.closest('.form-group');

            if (group && group.querySelector('.js-field-error, #dob-server-error')) {
                checkDateOfBirth();
            }
        });
    });

    const applicationForm =
        document.querySelector('form[action="{{ route('leads.update', $lead) }}"]');


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

      const emailInput =
        document.getElementById('email');

      if (emailInput) {

        emailInput.addEventListener('input', function() {

          const value = this.value.trim();

          if (!value) {
            clearFieldError(this);
            return;
          }

          const emailPattern =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

          if (!emailPattern.test(value)) {

            showFieldError(
              this,
              'Please enter a valid email address.'
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

          if (this.value.length !== 13) {

            showFieldError(
              this,
              'Please enter a valid MPAN. It must contain exactly 13 digits.'
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

          if (this.value.length < 6 || this.value.length > 8) {

            showFieldError(
              this,
              'Please enter a valid MPRN. It must contain between 6 and 8 digits.'
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

          if (this.value.length < 8 || this.value.length > 10) {

            showFieldError(
              this,
              'Please enter a valid SPID. It must contain between 8 and 10 digits.'
            );

          } else {

            clearFieldError(this);
          }
        });
      }

      // Only on an unexpanded draft - the sites-csv partial shows /
      // hides the count field itself.
      const numberOfSitesSelect =
        document.getElementById('number_of_sites');

      const sitesCountInput =
        document.getElementById('sites_count');

      if (numberOfSitesSelect && sitesCountInput) {

        numberOfSitesSelect.addEventListener('change', function() {

          if (this.value !== 'Multiple Site') {
            sitesCountInput.value = '';
            clearFieldError(sitesCountInput);
          }
        });

        sitesCountInput.addEventListener('input', function() {

          const value = this.value.trim();

          if (!value) {
            clearFieldError(this);
            return;
          }

          const numeric = Number(value);

          if (!/^-?\d+$/.test(value) || !Number.isInteger(numeric) || numeric < 1 || numeric > 500) {

            showFieldError(
              this,
              'Number of sites must be a whole number between 1 and 500.'
            );

          } else {

            clearFieldError(this);
          }
        });
      }

      applicationForm.addEventListener('submit', function(event) {

        const grossSalesField =
          document.getElementById('gross_sales');

        const fundsRequiredField =
          document.getElementById('funds_required');

        if (grossSalesField) {
          grossSalesField.value =
            grossSalesField.value.replace(/,/g, '');
        }

        if (fundsRequiredField) {
          fundsRequiredField.value =
            fundsRequiredField.value.replace(/,/g, '');
        }

        let hasError = false;

        /*
         * Product (required)
         */
        const productChecked =
          document.querySelector('input[name="product_id"]:checked');

        if (!productChecked) {

          if (productRadioGroup) {
            productRadioGroup.classList.add('radio-group-error');
          }

          hasError = true;

        } else {

          clearProductError();
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

        if (emailInput && emailInput.value.trim()) {

          const email =
            emailInput.value.trim();

          const emailPattern =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

          if (!emailPattern.test(email)) {

            showFieldError(
              emailInput,
              'Please enter a valid email address.'
            );

            hasError = true;

          } else {

            clearFieldError(emailInput);
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

        if (sitesCountInput && numberOfSitesSelect.value === 'Multiple Site') {

          const sitesValue = sitesCountInput.value.trim();

          const numeric = Number(sitesValue);

          if (
            !sitesValue ||
            !/^-?\d+$/.test(sitesValue) ||
            !Number.isInteger(numeric) ||
            numeric < 1 ||
            numeric > 500
          ) {

            showFieldError(
              sitesCountInput,
              'Please enter a number of sites between 1 and 500.'
            );

            hasError = true;
          }
        }

        if (checkDateOfBirth()) {
          hasError = true;
        }

        if (hasError) {

          event.preventDefault();

          const firstError =
            document.querySelector('.js-field-error, .radio-group-error');

          if (firstError) {

            firstError.scrollIntoView({
              behavior: 'smooth',
              block: 'center'
            });
          }

        } else {

          // The clicked button itself carries name="status" - a
          // disabled control's name/value is excluded when the
          // browser builds the submission, so disabling it here
          // outright would silently drop "status" from the request.
          // Preserve it as a hidden field first, then disable both
          // buttons to prevent a second click firing a second update
          // request while the first is still in flight.
          const submitter = event.submitter;

          if (submitter && submitter.name) {

            const hiddenStatus = document.createElement('input');
            hiddenStatus.type = 'hidden';
            hiddenStatus.name = submitter.name;
            hiddenStatus.value = submitter.value;
            applicationForm.appendChild(hiddenStatus);
          }

          applicationForm
            .querySelectorAll('button[type="submit"]')
            .forEach(function (button) {
              button.disabled = true;
            });
        }

      });
    }

  });
</script>

@endsection