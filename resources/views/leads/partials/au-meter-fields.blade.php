{{--
    AU Savers: Fuel Type (one checkbox chip per fuel) then MPAN / MPRN / SPID, each
    with its own checkbox - shared by Add Lead and Edit Lead. A meter's
    input is only enabled while its checkbox is ticked; an unticked one
    is disabled (so not submitted) and the server clears it
    (LeadValidationRules::applyMeterToggles()). Behaviour lives in
    partials/form-rules.

    @param array       $fuelTypes  selected Fuel Types
    @param array       $meters     ['mpan' => value, 'mprn' => ..., 'spid' => ...]
    @param array       $enabled    ['mpan' => bool, ...] - ticked checkboxes
    @param bool        $siteLead   one site of an expanded Multiple Site batch
--}}
@php
    $fuelTypes = (array) ($fuelTypes ?? []);
    $siteLead = (bool) ($siteLead ?? false);
    $placeholders = ['mpan' => 'Enter MPAN (13 digits)', 'mprn' => 'Enter MPRN (6-10 digits)', 'spid' => 'Enter SPID (8-10 digits)'];
    $fuelIcons = ['Electricity' => 'mdi-flash', 'Gas' => 'mdi-fire', 'Water' => 'mdi-water'];
@endphp

{{-- Marks a form submission that carries the meter checkboxes. --}}
<input type="hidden" name="meter_toggles" value="{{ $siteLead ? 'site' : '1' }}">

{{-- Fuel Type - before MPAN --}}
<div class="col-md-6" id="fuel-types-wrapper">
    <div class="form-group">
        <label id="fuel-types-label">Fuel Type</label>

        {{-- Sent when nothing is selected, so Edit can clear it. --}}
        <input type="hidden" name="fuel_types" value="">

        {{-- One chip per fuel - several may be ticked. --}}
        <div class="fuel-type-options" role="group" aria-labelledby="fuel-types-label">
            @foreach (\App\Support\LeadValidationRules::FUEL_TYPES as $type)
                @php $slug = \Illuminate\Support\Str::slug($type); @endphp
                <label class="fuel-type-option fuel-type-{{ $slug }}" for="fuel_type_{{ $slug }}">
                    <input type="checkbox" class="fuel-type-input" name="fuel_types[]"
                           id="fuel_type_{{ $slug }}" value="{{ $type }}"
                           @checked(in_array($type, $fuelTypes, true))>
                    <span class="fuel-type-card">
                        <span class="fuel-type-icon" aria-hidden="true"><i class="mdi {{ $fuelIcons[$type] ?? 'mdi-gauge' }}"></i></span>
                        <span class="fuel-type-name">{{ $type }}</span>
                        <span class="fuel-type-check" aria-hidden="true"><i class="mdi mdi-check"></i></span>
                    </span>
                </label>
            @endforeach
        </div>
        <small class="fuel-type-hint">Select all that apply.</small>
    </div>
</div>

{{-- Meters - at least one must be ticked and filled to publish --}}
<div class="col-12" id="meter-fields">
    <div class="meter-group" data-meter-group>
        <div class="meter-group-head">
            <span class="meter-group-title">Meter details</span>
            <span class="meter-group-hint">Tick each meter this lead has and enter its number - at least one is needed.</span>
        </div>

        <div class="row">
            @foreach (\App\Support\LeadValidationRules::METER_LABELS as $meter => $label)
                <div class="col-md-4 meter-col">
                    <div class="form-group mb-0">
                        <label class="meter-toggle" for="{{ $meter }}_enabled">
                            <input type="checkbox" class="meter-toggle-input" id="{{ $meter }}_enabled"
                                   name="{{ $meter }}_enabled" value="1"
                                   data-meter-toggle="{{ $meter }}" @checked($enabled[$meter] ?? false)>
                            <span class="meter-toggle-box" aria-hidden="true"><i class="mdi mdi-check"></i></span>
                            <span class="meter-toggle-label">{{ $label }}</span>
                        </label>

                        <input type="text" name="{{ $meter }}" id="{{ $meter }}" class="form-control"
                               inputmode="numeric" autocomplete="off"
                               placeholder="{{ $placeholders[$meter] }}"
                               value="{{ $meters[$meter] ?? '' }}"
                               @disabled(!($enabled[$meter] ?? false))>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
