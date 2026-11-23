{{--
    AU Savers: Fuel Type (one checkbox chip per fuel) then MPAN / MPRN / SPID -
    shared by Add Lead and Edit Lead. Behaviour lives in partials/form-rules.

    @param array       $fuelTypes  selected Fuel Types
    @param array       $meters     ['mpan' => value, 'mprn' => ..., 'spid' => ...]
    @param array       $enabled    ['mpan' => bool, ...] - ticked checkboxes
    @param bool        $siteLead   one site of an expanded Multiple Site batch

    Meter Details is hidden until a Fuel Type is picked, and then shows
    only that fuel's meter (Electricity -> MPAN, Gas -> MPRN, Water ->
    SPID). A meter that already has a number on an existing lead stays
    visible even if its fuel isn't selected, so it isn't hidden and lost.

    There's no tick-box to click: each meter's {meter}_enabled flag is a
    hidden checkbox that follows whether the meter is shown. A hidden
    meter's input is disabled (so not submitted) and the server clears
    it (LeadValidationRules::applyMeterToggles()).
--}}
@php
    $fuelTypes = (array) ($fuelTypes ?? []);
    $siteLead = (bool) ($siteLead ?? false);
    $placeholders = ['mpan' => 'Enter MPAN (13 digits)', 'mprn' => 'Enter MPRN', 'spid' => 'Enter SPID'];
    // Which meter each Fuel Type uses.
    $meterFuels = \App\Support\LeadValidationRules::METER_FUELS;
    $shownMeters = array_filter(
        $meterFuels,
        fn ($fuel, $meter) => in_array($fuel, $fuelTypes, true) || ($enabled[$meter] ?? false),
        ARRAY_FILTER_USE_BOTH
    );
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

{{-- Meters - at least one must be filled to publish --}}
<div class="col-12" id="meter-fields">
    <div class="meter-group" data-meter-group @if (empty($shownMeters)) hidden @endif>
        <div class="meter-group-head">
            <span class="meter-group-title">Meter Details</span>
        </div>

        <div class="row">
            @foreach (\App\Support\LeadValidationRules::METER_LABELS as $meter => $label)
                <div class="col-md-4 meter-col" data-meter-col="{{ $meter }}"
                     data-meter-fuel="{{ $meterFuels[$meter] }}"
                     @unless (isset($shownMeters[$meter])) hidden @endunless>
                    <div class="form-group mb-0">
                        {{-- Not user-facing - follows the meter being shown. --}}
                        <input type="checkbox" id="{{ $meter }}_enabled" name="{{ $meter }}_enabled" value="1"
                               data-meter-toggle="{{ $meter }}" hidden tabindex="-1" aria-hidden="true"
                               @checked(isset($shownMeters[$meter]))>

                        <label class="meter-label" for="{{ $meter }}">{{ $label }}</label>

                        <input type="text" name="{{ $meter }}" id="{{ $meter }}" class="form-control"
                               inputmode="numeric" autocomplete="off"
                               placeholder="{{ $placeholders[$meter] }}"
                               value="{{ $meters[$meter] ?? '' }}"
                               @disabled(!isset($shownMeters[$meter]))>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
