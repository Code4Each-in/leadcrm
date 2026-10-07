{{-- UK / India world clock (Admin / Super Admin dashboard), shown below
     the count cards. Design unchanged from the original dashboard view. --}}
<style>
.worldclock-card .card-body {
    padding: 26px 28px;
}

.worldclock-grid {
    display: flex;
    align-items: stretch;
    gap: 0;
    margin-top: 18px;
}

.worldclock-panel {
    flex: 1 1 0;
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 6px 26px;
    min-width: 0;
}

.worldclock-panel:first-child {
    padding-left: 4px;
}

.worldclock-panel:last-child {
    padding-right: 4px;
}

.worldclock-divider {
    width: 1px;
    align-self: stretch;
    background: #eeeef5;
}

.analog-clock {
    position: relative;
    width: clamp(92px, 8vw, 120px);
    height: clamp(92px, 8vw, 120px);
    flex-shrink: 0;
}

.clock-face {
    position: relative;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #fcfcff;
    border: 1px solid #eeeef5;
    box-shadow: 0 10px 26px rgba(38, 33, 92, 0.08);
}

.clock-ticks span {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transform: rotate(calc(var(--i) * 6deg));
}

.clock-ticks span::before {
    content: '';
    display: block;
    margin: 4% auto 0;
    width: 1px;
    height: 5%;
    background: #dcdce8;
}

.clock-ticks span.major::before {
    width: 2px;
    height: 8%;
}

.clock-numbers span {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    text-align: center;
    transform: rotate(calc(var(--i) * 30deg));
}

.clock-numbers span b {
    display: inline-block;
    margin-top: 11%;
    transform: rotate(calc(var(--i) * -30deg));
    font-size: 10px;
    font-weight: 700;
    font-style: normal;
}

.clock-hand {
    position: absolute;
    left: 50%;
    bottom: 50%;
    transform-origin: 50% 100%;
    border-radius: 3px;
}

.clock-hour-hand {
    width: 3px;
    height: 25%;
    margin-left: -1.5px;
}

.clock-minute-hand {
    width: 2px;
    height: 36%;
    margin-left: -1px;
}

.clock-second-hand {
    width: 1px;
    height: 41%;
    margin-left: -0.5px;
    background: #f96868;
}

.clock-pivot {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 7px;
    height: 7px;
    margin: -3.5px 0 0 -3.5px;
    border-radius: 50%;
    z-index: 5;
}

.tz-london .clock-numbers span b { color: #4b49ac; }
.tz-london .clock-ticks span.major::before { background: #4b49ac; }
.tz-london .clock-hour-hand { background: #4b49ac; }
.tz-london .clock-minute-hand { background: #6c63ff; }
.tz-london .clock-pivot { background: #4b49ac; }

.tz-india .clock-numbers span b { color: #b77900; }
.tz-india .clock-ticks span.major::before { background: #f2a654; }
.tz-india .clock-hour-hand { background: #b77900; }
.tz-india .clock-minute-hand { background: #f2a654; }
.tz-india .clock-pivot { background: #b77900; }

.worldclock-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 0;
}

.worldclock-city {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #26215c;
    white-space: nowrap;
}

.worldclock-city-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.tz-london .worldclock-city-dot { background: #4b49ac; }
.tz-india .worldclock-city-dot { background: #f2a654; }

.worldclock-offset {
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 9px;
    border-radius: 20px;
    white-space: nowrap;
}

.tz-london .worldclock-offset { background: #eeedfe; color: #534ab7; }
.tz-india .worldclock-offset { background: #fff4db; color: #b77900; }

.worldclock-time {
    font-size: 25px;
    font-weight: 800;
    color: #26215c;
    line-height: 1.15;
    font-variant-numeric: tabular-nums;
}

.worldclock-date {
    font-size: 12px;
    color: #8a8a9a;
}

@media (max-width: 991px) {
    .worldclock-time {
        font-size: 21px;
    }

    .worldclock-panel {
        gap: 14px;
        padding: 6px 16px;
    }
}

@media (max-width: 767px) {
    .worldclock-grid {
        flex-direction: column;
        gap: 20px;
        margin-top: 20px;
    }

    .worldclock-divider {
        width: auto;
        height: 1px;
        align-self: stretch;
    }

    .worldclock-panel {
        padding: 0;
    }
}

@media (max-width: 400px) {
    .worldclock-panel {
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }

    .worldclock-city,
    .worldclock-info {
        justify-content: center;
        align-items: center;
    }
}
</style>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card worldclock-card">
            <div class="card-body">

                <p class="section-title mb-1">World Clock</p>
                <p class="section-subtitle mb-0">Compare UK and India time at a glance</p>

                <div class="worldclock-grid">

                    <div class="worldclock-panel tz-london">
                        <div class="analog-clock">
                            <div class="clock-face">
                                <div class="clock-ticks">
                                    @for ($i = 0; $i < 60; $i++)
                                        <span class="{{ $i % 5 === 0 ? 'major' : '' }}" style="--i:{{ $i }}"></span>
                                    @endfor
                                </div>
                                <div class="clock-numbers">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <span style="--i:{{ $i }}"><b>{{ $i }}</b></span>
                                    @endfor
                                </div>
                                <div class="clock-hand clock-hour-hand" id="londonClockHour"></div>
                                <div class="clock-hand clock-minute-hand" id="londonClockMinute"></div>
                                <div class="clock-hand clock-second-hand" id="londonClockSecond"></div>
                                <div class="clock-pivot"></div>
                            </div>
                        </div>
                        <div class="worldclock-info">
                            <div class="worldclock-city">
                                <span class="worldclock-city-dot"></span>
                                London, UK
                                <span class="worldclock-offset" id="londonClockOffset">GMT</span>
                            </div>
                            <div class="worldclock-time" id="londonClockDigital">--:--:--</div>
                            <div class="worldclock-date" id="londonClockDate">&nbsp;</div>
                        </div>
                    </div>

                    <div class="worldclock-divider"></div>

                    <div class="worldclock-panel tz-india">
                        <div class="analog-clock">
                            <div class="clock-face">
                                <div class="clock-ticks">
                                    @for ($i = 0; $i < 60; $i++)
                                        <span class="{{ $i % 5 === 0 ? 'major' : '' }}" style="--i:{{ $i }}"></span>
                                    @endfor
                                </div>
                                <div class="clock-numbers">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <span style="--i:{{ $i }}"><b>{{ $i }}</b></span>
                                    @endfor
                                </div>
                                <div class="clock-hand clock-hour-hand" id="indiaClockHour"></div>
                                <div class="clock-hand clock-minute-hand" id="indiaClockMinute"></div>
                                <div class="clock-hand clock-second-hand" id="indiaClockSecond"></div>
                                <div class="clock-pivot"></div>
                            </div>
                        </div>
                        <div class="worldclock-info">
                            <div class="worldclock-city">
                                <span class="worldclock-city-dot"></span>
                                India
                                <span class="worldclock-offset">GMT+5:30</span>
                            </div>
                            <div class="worldclock-time" id="indiaClockDigital">--:--:--</div>
                            <div class="worldclock-date" id="indiaClockDate">&nbsp;</div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>


<script>
(function () {
    function bindClock(cfg) {
        var hourHand = document.getElementById(cfg.hour);
        var minuteHand = document.getElementById(cfg.minute);
        var secondHand = document.getElementById(cfg.second);
        var digitalEl = document.getElementById(cfg.digital);
        var dateEl = document.getElementById(cfg.date);
        var offsetEl = cfg.offset ? document.getElementById(cfg.offset) : null;

        if (!hourHand || !minuteHand || !secondHand) return null;

        var timeFormatter = new Intl.DateTimeFormat('en-GB', {
            timeZone: cfg.tz,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });

        var dateFormatter = new Intl.DateTimeFormat('en-GB', {
            timeZone: cfg.tz,
            weekday: 'short',
            day: '2-digit',
            month: 'short'
        });

        var offsetFormatter = offsetEl ? new Intl.DateTimeFormat('en-US', {
            timeZone: cfg.tz,
            timeZoneName: 'short'
        }) : null;

        return function tick() {
            var now = new Date();
            var parts = {};
            timeFormatter.formatToParts(now).forEach(function (p) {
                parts[p.type] = p.value;
            });

            var hours = parseInt(parts.hour, 10) % 24;
            var minutes = parseInt(parts.minute, 10);
            var seconds = parseInt(parts.second, 10);

            secondHand.style.transform = 'rotate(' + (seconds * 6) + 'deg)';
            minuteHand.style.transform = 'rotate(' + (minutes * 6 + seconds * 0.1) + 'deg)';
            hourHand.style.transform = 'rotate(' + ((hours % 12) * 30 + minutes * 0.5) + 'deg)';

            if (digitalEl) digitalEl.textContent = timeFormatter.format(now);
            if (dateEl) dateEl.textContent = dateFormatter.format(now);

            if (offsetEl && offsetFormatter) {
                var offsetPart = offsetFormatter.formatToParts(now).find(function (p) {
                    return p.type === 'timeZoneName';
                });
                offsetEl.textContent = offsetPart ? offsetPart.value : '';
            }
        };
    }

    var ticks = [
        bindClock({
            tz: 'Europe/London',
            hour: 'londonClockHour', minute: 'londonClockMinute', second: 'londonClockSecond',
            digital: 'londonClockDigital', date: 'londonClockDate', offset: 'londonClockOffset'
        }),
        bindClock({
            tz: 'Asia/Kolkata',
            hour: 'indiaClockHour', minute: 'indiaClockMinute', second: 'indiaClockSecond',
            digital: 'indiaClockDigital', date: 'indiaClockDate'
        })
    ].filter(Boolean);

    if (!ticks.length) return;

    function tickAll() {
        ticks.forEach(function (fn) { fn(); });
    }

    tickAll();
    setInterval(tickAll, 1000);
})();
</script>
