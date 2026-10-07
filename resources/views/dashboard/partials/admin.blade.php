{{-- Admin / Super Admin (App\Services\Dashboard\AdminDashboard): headline
     figures, what needs attention, and the latest leads. Detailed
     reports belong on the Reporting page. --}}
<div class="row kpi-row">
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Total Leads', 'value' => $kpis['total'], 'icon' => 'mdi-file-document-multiple', 'tone' => 'primary', 'hint' => 'All published leads', 'url' => route('leads.index')])
    </div>
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Open & Unassigned', 'value' => $kpis['open'], 'icon' => 'mdi-inbox-arrow-down', 'tone' => 'warning', 'hint' => 'Waiting for an Account Manager'])
    </div>
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'In Progress', 'value' => $kpis['inProgress'], 'icon' => 'mdi-progress-check', 'tone' => 'info', 'hint' => 'With an Account Manager'])
    </div>
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Closed This Month', 'value' => $kpis['closedThisMonth'], 'icon' => 'mdi-check-circle', 'tone' => 'success', 'hint' => now()->format('F Y')])
    </div>
</div>

@include('dashboard.partials.world-clock')

<div class="row panel-row">
    <div class="col-lg-5">
        <div class="dash-panel">
            @include('dashboard.partials.panel-head', ['title' => 'Needs Attention', 'subtitle' => 'Leads waiting on someone'])
            <div class="panel-body">
                @forelse($attention as $item)
                    <div class="dash-row tone-{{ $item['tone'] }}">
                        <span class="row-icon"><i class="mdi {{ $item['icon'] }}"></i></span>
                        <span class="row-main">
                            <span class="row-title">{{ $item['label'] }}</span>
                            <span class="row-meta">{{ $item['owner'] }}</span>
                        </span>
                        <span class="row-count">{{ number_format($item['count']) }}</span>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="mdi mdi-check-circle"></i>
                        All clear - nothing is waiting.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="dash-panel">
            @include('dashboard.partials.panel-head', ['title' => 'Latest Leads', 'subtitle' => 'Most recently created', 'linkUrl' => route('leads.index'), 'linkLabel' => 'All leads'])
            <div class="panel-body">
                @include('dashboard.partials.lead-list', ['rows' => $latest, 'empty' => 'No published leads yet.', 'emptyIcon' => 'mdi-file-document-outline'])
            </div>
        </div>
    </div>
</div>
