{{-- Account Manager (App\Services\Dashboard\AccountManagerDashboard). --}}
<div class="row kpi-row">
    <div class="col-xl-4 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'New Leads', 'value' => $kpis['newlyAssigned'], 'icon' => 'mdi-account-plus', 'tone' => 'success', 'hint' => 'Assigned in the last 48 hours'])
    </div>
    <div class="col-xl-4 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Active Leads', 'value' => $kpis['active'], 'icon' => 'mdi-briefcase', 'tone' => 'primary', 'hint' => 'Being worked by you', 'url' => route('leads.index')])
    </div>
    <div class="col-xl-4 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'On Hold', 'value' => $kpis['hold'], 'icon' => 'mdi-pause-circle', 'tone' => 'info', 'hint' => 'Paused by you'])
    </div>
</div>

<div class="row panel-row">
    <div class="col-lg-12">
        <div class="dash-panel">
            @include('dashboard.partials.panel-head', ['title' => 'New Leads', 'subtitle' => 'Assigned to you in the last 48 hours', 'linkUrl' => route('leads.index'), 'linkLabel' => 'All leads'])
            <div class="panel-body">
                @include('dashboard.partials.lead-list', ['rows' => $newlyAssigned, 'empty' => 'Nothing new in the last 48 hours.', 'emptyIcon' => 'mdi-inbox'])
            </div>
        </div>
    </div>
</div>
