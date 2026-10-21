{{-- Header world clock (UK + India). Desktop (>=992px): shown inline -
     mini analog clocks with the digital time, like the dashboard's World
     Clock card, no dropdown. Tablet / phone: a compact button that opens
     the same clocks in a panel. Styles: includes/world-clock/styles.
     Every [data-wc-zone] block is driven by the script below. --}}
@php
    // London's offset follows GMT / BST (GMT+1) and is filled in live;
    // India has no daylight saving.
    $wcZones = [
        ['tz' => 'Europe/London', 'class' => 'tz-uk', 'city' => 'London', 'short' => 'UK', 'badge' => 'GMT', 'dynamicOffset' => true],
        ['tz' => 'Asia/Kolkata',  'class' => 'tz-in', 'city' => 'India',  'short' => 'IN', 'badge' => 'GMT+5:30', 'dynamicOffset' => false],
    ];
@endphp
<li class="nav-item dropdown wc-nav" id="wcNav">

    {{-- Desktop: inline clocks --}}
    <div class="wc-inline" aria-label="World clock">
        @foreach($wcZones as $i => $zone)
            @if($i > 0)
                <span class="wc-inline-sep" aria-hidden="true"></span>
            @endif
            @include('includes.world-clock.zone', ['zone' => $zone])
        @endforeach
    </div>

    {{-- Tablet / phone: button + panel --}}
    <a class="nav-link wc-pill dropdown-toggle" href="#" data-toggle="dropdown" id="worldClockDropdown"
       aria-haspopup="true" aria-expanded="false" title="World clock">
        <span class="wc-pill-icon"><i class="mdi mdi-earth"></i></span>
        @foreach($wcZones as $i => $zone)
            @if($i > 0)
                <span class="wc-pill-sep" aria-hidden="true"></span>
            @endif
            <span class="wc-pill-zone {{ $zone['class'] }}" data-wc-zone="{{ $zone['tz'] }}">
                <span class="wc-pill-label"><span class="wc-dot"></span>{{ $zone['short'] }}</span>
                <span class="wc-pill-time" data-wc-show="short">--:--</span>
            </span>
        @endforeach
    </a>

    <div class="dropdown-menu dropdown-menu-right navbar-dropdown wc-dropdown" aria-labelledby="worldClockDropdown">
        <div class="wc-head">
            <span class="wc-head-icon"><i class="mdi mdi-earth"></i></span>
            <div>
                <p class="wc-head-title">World Clock</p>
                <p class="wc-head-sub">UK and India time at a glance</p>
            </div>
        </div>

        {{-- Same clocks as the desktop header. --}}
        <div class="wc-inline wc-panel-clocks">
            @foreach($wcZones as $i => $zone)
                @if($i > 0)
                    <span class="wc-inline-sep" aria-hidden="true"></span>
                @endif
                @include('includes.world-clock.zone', ['zone' => $zone])
            @endforeach
        </div>

        <div class="wc-foot">
            <i class="mdi mdi-swap-horizontal"></i>
            <span id="wcDifference">&nbsp;</span>
        </div>
    </div>

    <script>
    (function () {
        var zones = document.querySelectorAll('#wcNav [data-wc-zone]');
        var diffEl = document.getElementById('wcDifference');
        var cache = {};

        function fmt(tz, show) {
            var key = tz + '|' + show;
            if (cache[key]) return cache[key];

            var opts = { timeZone: tz, hour12: false };
            if (show === 'short') { opts.hour = '2-digit'; opts.minute = '2-digit'; }
            if (show === 'long') { opts.hour = '2-digit'; opts.minute = '2-digit'; opts.second = '2-digit'; }
            if (show === 'date') { opts.weekday = 'short'; opts.day = '2-digit'; opts.month = 'short'; }
            if (show === 'zone') { return cache[key] = new Intl.DateTimeFormat('en-US', { timeZone: tz, timeZoneName: 'short' }); }
            if (show === 'parts') {
                opts.year = 'numeric'; opts.month = '2-digit'; opts.day = '2-digit';
                opts.hour = '2-digit'; opts.minute = '2-digit'; opts.second = '2-digit';
            }

            return cache[key] = new Intl.DateTimeFormat('en-GB', opts);
        }

        function partsOf(tz, now) {
            var p = {};
            fmt(tz, 'parts').formatToParts(now).forEach(function (x) { p[x.type] = x.value; });
            return p;
        }

        // Minutes the zone is ahead of UTC right now (follows GMT/BST).
        function offsetMinutes(p, now) {
            var asUtc = Date.UTC(+p.year, +p.month - 1, +p.day, +p.hour % 24, +p.minute, +p.second);
            return Math.round((asUtc - now.getTime()) / 60000);
        }

        function tick() {
            var now = new Date();
            var offsets = {};

            zones.forEach(function (zone) {
                var tz = zone.getAttribute('data-wc-zone');
                var p = partsOf(tz, now);
                var h = +p.hour % 24, m = +p.minute, s = +p.second;
                offsets[tz] = offsetMinutes(p, now);

                zone.querySelectorAll('[data-wc-show]').forEach(function (el) {
                    var show = el.getAttribute('data-wc-show');
                    if (!show) return;
                    if (show === 'zone') {
                        var part = fmt(tz, 'zone').formatToParts(now).find(function (x) { return x.type === 'timeZoneName'; });
                        el.textContent = part ? part.value : el.textContent;
                    } else {
                        el.textContent = fmt(tz, show).format(now);
                    }
                });

                zone.querySelectorAll('[data-wc-hand]').forEach(function (el) {
                    var deg = {
                        hour: (h % 12) * 30 + m * 0.5,
                        minute: m * 6 + s * 0.1,
                        second: s * 6
                    }[el.getAttribute('data-wc-hand')];
                    el.style.transform = 'rotate(' + deg + 'deg)';
                });
            });

            if (diffEl && 'Asia/Kolkata' in offsets && 'Europe/London' in offsets) {
                var diff = offsets['Asia/Kolkata'] - offsets['Europe/London'];
                var dh = Math.floor(Math.abs(diff) / 60), dm = Math.abs(diff) % 60;
                diffEl.textContent = 'India is ' + dh + 'h' + (dm ? ' ' + dm + 'm' : '') + ' ahead of the UK';
            }
        }

        tick();
        setInterval(tick, 1000);
    })();
    </script>
</li>
