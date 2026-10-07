{{-- Account Executive (App\Services\Dashboard\AccountExecutiveDashboard). --}}
<div class="row kpi-row">
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Sent Back To Me', 'value' => $kpis['sentBack'], 'icon' => 'mdi-undo-variant', 'tone' => 'danger', 'hint' => 'Needs your input'])
    </div>
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Drafts', 'value' => $kpis['drafts'], 'icon' => 'mdi-file-edit', 'tone' => 'warning', 'hint' => 'Not published yet'])
    </div>
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'In Progress', 'value' => $kpis['inProgress'], 'icon' => 'mdi-progress-check', 'tone' => 'info', 'hint' => 'Published and moving', 'url' => route('leads.index')])
    </div>
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Closed', 'value' => $kpis['closed'], 'icon' => 'mdi-check-circle', 'tone' => 'success', 'hint' => 'All time'])
    </div>
</div>

<div class="row panel-row">
    <div class="col-lg-6">
        <div class="dash-panel">
            @include('dashboard.partials.panel-head', ['title' => 'Sent Back To Me', 'subtitle' => 'Add the missing details on the lead'])
            <div class="panel-body">
                @include('dashboard.partials.lead-list', ['rows' => $sentBack, 'empty' => 'Nothing has been sent back to you.'])
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="dash-panel">
            @include('dashboard.partials.panel-head', ['title' => 'Drafts To Publish', 'subtitle' => 'Oldest first', 'linkUrl' => route('leads.index'), 'linkLabel' => 'All leads'])
            <div class="panel-body">
                @include('dashboard.partials.lead-list', ['rows' => $drafts, 'empty' => 'No drafts.'])
            </div>
        </div>
    </div>
</div>
