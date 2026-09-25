{{--
    Shared field markup for the Add Pricing / Edit Pricing modals on
    leads/show.blade.php - included twice with a different $prefix so
    each copy gets unique element ids. Relies on $lead and $suppliers
    being in scope (both are passed to leads.show, and @include
    inherits the including view's variables).

    Deliberately mirrors the .row/.col-md-6/.form-group/label markup
    used throughout leads/edit.blade.php, rather than a bespoke
    layout, so these fields get the exact same spacing and look as
    every other Lead form field in this app (global theme:
    public/assets/css/vertical-layout-light/style.css). Selects use
    .form-control (not .form-select, which this app's theme never
    styles) so Select2 - already integrated with .form-control in
    that same stylesheet - inherits it correctly too.

    Each .form-group carries data-field="<validation field name>" so
    setPricingFieldErrors() (in the page script) can find the right
    field and its .invalid-feedback slot generically from the Laravel
    validation error bag, without a hand-maintained id map.
--}}
<div class="row">

    <div class="col-md-6">
        <div class="form-group" data-field="supplier_id">
            <label for="{{ $prefix }}Supplier">Supplier</label>
            <select id="{{ $prefix }}Supplier" class="form-control pricing-supplier-select" style="width: 100%;">
                <option value="">Select supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group" data-field="rate_type">
            <label for="{{ $prefix }}RateType">Rate Type</label>
            <select id="{{ $prefix }}RateType" class="form-control pricing-rate-type-select" style="width: 100%;" onchange="togglePricingRateFields('{{ $prefix }}')">
                <option value="single">Single-Rate</option>
                <option value="multi">Multi-Rate (Day / Evening / Night)</option>
            </select>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>MPAN / MPRN</label>
            <input type="text" class="form-control" value="{{ $lead->mpan ?: ($lead->mprn ?: '-') }}" readonly disabled>
            <small class="form-text text-muted">From this lead's utility details.</small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group" data-field="contract_term_months">
            <label for="{{ $prefix }}ContractTerm">Contract Term (months)</label>
            <input type="number" id="{{ $prefix }}ContractTerm" class="form-control" min="1" max="120" step="1" oninput="recalculatePricingPreview('{{ $prefix }}')">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    {{-- Single-rate --}}
    <div class="col-md-6 pricing-single-only" id="{{ $prefix }}SingleEacWrap">
        <div class="form-group" data-field="total_eac_kwh">
            <label for="{{ $prefix }}TotalEac">EAC (kWh/year)</label>
            <input type="number" id="{{ $prefix }}TotalEac" class="form-control" min="0" step="0.01" oninput="recalculatePricingPreview('{{ $prefix }}')">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    {{-- Multi-rate --}}
    <div class="col-md-4 pricing-multi-only" id="{{ $prefix }}DayWrap" style="display:none;">
        <div class="form-group" data-field="day_consumption_kwh">
            <label for="{{ $prefix }}DayConsumption">Day Consumption (kWh)</label>
            <input type="number" id="{{ $prefix }}DayConsumption" class="form-control" min="0" step="0.01" oninput="recalculatePricingPreview('{{ $prefix }}')">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-md-4 pricing-multi-only" id="{{ $prefix }}EveningConsumptionWrap" style="display:none;">
        <div class="form-group" data-field="evening_consumption_kwh">
            <label for="{{ $prefix }}EveningConsumption">Evening Consumption (kWh)</label>
            <input type="number" id="{{ $prefix }}EveningConsumption" class="form-control" min="0" step="0.01" oninput="recalculatePricingPreview('{{ $prefix }}')">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-md-4 pricing-multi-only" id="{{ $prefix }}NightConsumptionWrap" style="display:none;">
        <div class="form-group" data-field="night_consumption_kwh">
            <label for="{{ $prefix }}NightConsumption">Night Consumption (kWh)</label>
            <input type="number" id="{{ $prefix }}NightConsumption" class="form-control" min="0" step="0.01" oninput="recalculatePricingPreview('{{ $prefix }}')">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-md-6 pricing-multi-only" id="{{ $prefix }}TotalEacDisplayWrap" style="display:none;">
        <div class="form-group">
            <label for="{{ $prefix }}TotalEacDisplay">Total EAC (calculated)</label>
            <input type="text" id="{{ $prefix }}TotalEacDisplay" class="form-control" readonly disabled>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group" data-field="sc_pence_per_day">
            <label for="{{ $prefix }}Sc">SC p/day</label>
            <input type="number" id="{{ $prefix }}Sc" class="form-control" min="0" step="0.0001" oninput="recalculatePricingPreview('{{ $prefix }}')">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group" data-field="unit_rate_pence">
            <label for="{{ $prefix }}UnitRate" id="{{ $prefix }}UnitRateLabel">Unit Rate p/kWh</label>
            <input type="number" id="{{ $prefix }}UnitRate" class="form-control" min="0" step="0.0001" oninput="recalculatePricingPreview('{{ $prefix }}')">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-md-6 pricing-multi-only" id="{{ $prefix }}NightRateWrap" style="display:none;">
        <div class="form-group" data-field="night_unit_rate_pence">
            <label for="{{ $prefix }}NightRate">Night Unit Charge p/kWh</label>
            <input type="number" id="{{ $prefix }}NightRate" class="form-control" min="0" step="0.0001" oninput="recalculatePricingPreview('{{ $prefix }}')">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-md-6 pricing-multi-only" id="{{ $prefix }}EveningRateWrap" style="display:none;">
        <div class="form-group" data-field="evening_unit_rate_pence">
            <label for="{{ $prefix }}EveningRate">Evening Unit Charge p/kWh</label>
            <input type="number" id="{{ $prefix }}EveningRate" class="form-control" min="0" step="0.0001" oninput="recalculatePricingPreview('{{ $prefix }}')">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group" data-field="uplift_pence">
            <label for="{{ $prefix }}Uplift">Uplift p/kWh</label>
            <input type="number" id="{{ $prefix }}Uplift" class="form-control" min="0" step="0.0001" oninput="recalculatePricingPreview('{{ $prefix }}')">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    {{-- Annual Spend is the headline number this form exists to
         produce, so it gets a full-width, visually distinct row
         rather than sitting in one half of the grid like a plain
         field. --}}
    <div class="col-12">
        <div class="pricing-annual-spend-summary">
            <span class="pricing-annual-spend-label">
                Annual Spend (estimated)
                <small>Recalculated live as you type - the final figure is confirmed on save.</small>
            </span>
            <span class="pricing-annual-spend-value" id="{{ $prefix }}AnnualSpendPreview">£0.00</span>
        </div>

        {{-- Live calculation breakdown - the formula plus the actual
             values being used, recalculated on every keystroke by
             recalculatePricingPreview() so it's never out of sync
             with the Annual Spend figure above it. --}}
        <div class="pricing-calc-breakdown" id="{{ $prefix }}CalcBreakdown"></div>
    </div>

</div>
