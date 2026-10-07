{{-- One headline figure. Expects $label, $value, $icon (mdi class),
     $tone (primary / success / warning / danger / info / purple /
     neutral); optional $hint and $url (makes the card a link). --}}
@php($tag = !empty($url) ? 'a' : 'div')
<{{ $tag }} @if(!empty($url)) href="{{ $url }}" @endif class="kpi-card tone-{{ $tone }}">
    <div class="kpi-top">
        <span class="kpi-label">{{ $label }}</span>
        <span class="kpi-icon"><i class="mdi {{ $icon }}"></i></span>
    </div>
    <div>
        <div class="kpi-value">{{ is_numeric($value) ? number_format($value) : $value }}</div>
        @if(!empty($hint))
            <div class="kpi-hint">{{ $hint }}</div>
        @endif
    </div>
</{{ $tag }}>
