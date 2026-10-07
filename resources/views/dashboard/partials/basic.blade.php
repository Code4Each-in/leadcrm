{{-- QA User, and any role without a dashboard of its own
     (App\Services\Dashboard\BasicDashboard): the leads they created. --}}
<div class="row kpi-row">
    <div class="col-md-4">
        @include('dashboard.partials.kpi-card', ['label' => 'My Leads', 'value' => $kpis['total'], 'icon' => 'mdi-file-document-multiple', 'tone' => 'primary', 'hint' => 'Leads you created', 'url' => route('leads.index')])
    </div>
    <div class="col-md-4">
        @include('dashboard.partials.kpi-card', ['label' => 'Published', 'value' => $kpis['published'], 'icon' => 'mdi-check-circle', 'tone' => 'success', 'hint' => 'Handed over'])
    </div>
    <div class="col-md-4">
        @include('dashboard.partials.kpi-card', ['label' => 'Drafts', 'value' => $kpis['drafts'], 'icon' => 'mdi-file-edit', 'tone' => 'warning', 'hint' => 'Not published yet'])
    </div>
</div>

<div class="row panel-row">
    <div class="col-12">
        <div class="dash-panel">
            @include('dashboard.partials.panel-head', ['title' => 'Recently Created', 'subtitle' => 'Your latest leads', 'linkUrl' => route('leads.index'), 'linkLabel' => 'All leads'])
            <div class="panel-body">
                @include('dashboard.partials.lead-list', ['rows' => $recent, 'empty' => 'You have not created any leads yet.', 'emptyIcon' => 'mdi-file-document-outline'])
            </div>
        </div>
    </div>
</div>
