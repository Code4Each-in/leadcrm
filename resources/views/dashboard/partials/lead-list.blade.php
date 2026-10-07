{{-- Short list of leads (rows from Dashboard::leadRow()), each row
     opening the lead. Expects $rows, $empty and $emptyIcon. A row's
     note, when set, is shown in place of its status (coloured by
     its note_class, default the pricing amber). --}}
@if(empty($rows))
    <div class="empty-state">
        <i class="mdi {{ $emptyIcon ?? 'mdi-check-circle' }}"></i>
        {{ $empty }}
    </div>
@else
    @foreach($rows as $row)
        <a href="{{ $row['url'] }}" class="dash-row">
            <span class="row-main">
                <span class="row-title">{{ $row['company'] }}</span>
                <span class="row-meta"><span class="lead-id">#{{ $row['display_id'] }}</span></span>
            </span>
            <span class="row-side">
                @if($row['note'])
                    <span class="status-pill {{ $row['note_class'] ?? 'pill-pricing' }}">{{ $row['note'] }}</span>
                @else
                    @include('dashboard.partials.status-pill', ['status' => $row['status'], 'label' => $row['status_label']])
                @endif
                @if($row['since'])
                    @php($since = \Carbon\Carbon::parse($row['since']))
                    <span class="row-time" title="{{ $since->format('d M Y, H:i') }}">{{ $since->diffForHumans() }}</span>
                @endif
            </span>
        </a>
    @endforeach
@endif
