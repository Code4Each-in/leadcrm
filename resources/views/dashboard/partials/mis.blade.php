{{-- MIS User (App\Services\Dashboard\MisDashboard). --}}
<div class="row kpi-row">
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Awaiting Assignment', 'value' => $kpis['awaitingAssignment'], 'icon' => 'mdi-account-alert', 'tone' => 'warning', 'hint' => 'Open leads with no owner', 'url' => route('leads.index')])
    </div>
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Pricing To Do', 'value' => $kpis['pricingToDo'], 'icon' => 'mdi-currency-gbp', 'tone' => 'danger', 'hint' => 'No pricing, draft or declined'])
    </div>
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Pending Auto-assign', 'value' => $kpis['pendingAutoAssign'], 'icon' => 'mdi-account-clock', 'tone' => 'info', 'hint' => 'Imported for an Account Manager'])
    </div>
    <div class="col-xl-3 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'My Drafts', 'value' => $kpis['myDrafts'], 'icon' => 'mdi-file-edit', 'tone' => 'neutral', 'hint' => 'Not published yet'])
    </div>
</div>

<div class="row panel-row">
    <div class="col-lg-6">
        <div class="dash-panel">
            @include('dashboard.partials.panel-head', ['title' => 'Awaiting Assignment', 'subtitle' => 'Oldest first', 'linkUrl' => route('leads.index'), 'linkLabel' => 'All leads'])
            <div class="panel-body">
                @include('dashboard.partials.lead-list', ['rows' => $unassigned, 'empty' => 'Every published lead has an owner.'])
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="dash-panel">
            @include('dashboard.partials.panel-head', ['title' => 'Pricing Queue', 'subtitle' => 'AU Savers - declined first'])
            <div class="panel-body">
                @include('dashboard.partials.lead-list', ['rows' => $pricingQueue, 'empty' => 'No pricing waiting on you.'])
            </div>
        </div>
    </div>
</div>
