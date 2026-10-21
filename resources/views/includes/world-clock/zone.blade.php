{{-- One city of the world clock: mini analog face + city, GMT offset,
     time and date. Used inline in the desktop header and in the mobile
     panel, so both look the same. Expects $zone (see nav.blade.php). --}}
<div class="wc-inline-zone {{ $zone['class'] }}" data-wc-zone="{{ $zone['tz'] }}">
    <div class="wc-analog" aria-hidden="true">
        @for($t = 0; $t < 12; $t++)
            <span class="wc-tick {{ $t % 3 === 0 ? 'major' : '' }}" style="--i:{{ $t }}"></span>
        @endfor
        <span class="wc-hand wc-hand-h" data-wc-hand="hour"></span>
        <span class="wc-hand wc-hand-m" data-wc-hand="minute"></span>
        <span class="wc-hand wc-hand-s" data-wc-hand="second"></span>
        <span class="wc-pivot"></span>
    </div>
    <div class="wc-inline-info">
        <span class="wc-inline-city">
            {{ $zone['city'] }}
            <span class="wc-badge" @if($zone['dynamicOffset']) data-wc-show="zone" @endif>{{ $zone['badge'] }}</span>
        </span>
        <span class="wc-inline-time" data-wc-show="long">--:--:--</span>
        <span class="wc-inline-date" data-wc-show="date">&nbsp;</span>
    </div>
</div>
