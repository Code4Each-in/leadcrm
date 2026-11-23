{{-- Account Executive (App\Services\Dashboard\AccountExecutiveDashboard). --}}
@php
    // Stage names as set in Stage Management.
    $draftStageLabels = \App\Models\Lead::draftStageLabels();
    // Each card opens the Leads list with that Stage filter selected.
    $stageUrl = fn (string $status) => route('leads.index', ['status' => $status]);
@endphp
<div class="row kpi-row">
    <div class="col-xl-4 col-md-4 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Sent Back To Me', 'value' => $kpis['sentBack'], 'icon' => 'mdi-undo-variant', 'tone' => 'danger', 'hint' => 'Needs your input', 'url' => $stageUrl(\App\Http\Controllers\LeadController::FILTER_RETURNED_TO_AE)])
    </div>
    <div class="col-xl-4 col-md-4 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Reminder', 'value' => $kpis['callBack'], 'icon' => 'mdi-phone-clock', 'tone' => 'purple', 'hint' => 'At ' . ($draftStageLabels[\App\Models\Lead::DRAFT_STAGE_CALL_BACK] ?? 'Call Back'), 'url' => $stageUrl(\App\Models\Lead::DRAFT_STAGE_CALL_BACK)])
    </div>
    <div class="col-xl-4 col-md-4 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Awaiting Information', 'value' => $kpis['awaitingInfo'], 'icon' => 'mdi-timer-sand', 'tone' => 'warning', 'hint' => 'At ' . ($draftStageLabels[\App\Models\Lead::DRAFT_STAGE_AWAITING_INFO] ?? 'Awaiting Additional Information'), 'url' => $stageUrl(\App\Models\Lead::DRAFT_STAGE_AWAITING_INFO)])
    </div>
    <div class="col-xl-4 col-md-4 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Drafts', 'value' => $kpis['drafts'], 'icon' => 'mdi-file-edit', 'tone' => 'neutral', 'hint' => 'Not published yet', 'url' => $stageUrl(\App\Models\Lead::STATUS_DRAFT)])
    </div>
    <div class="col-xl-4 col-md-4 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'In Progress', 'value' => $kpis['inProgress'], 'icon' => 'mdi-progress-check', 'tone' => 'info', 'hint' => 'Published and moving', 'url' => route('leads.index')])
    </div>
    <div class="col-xl-4 col-md-4 col-6">
        @include('dashboard.partials.kpi-card', ['label' => 'Closed', 'value' => $kpis['closed'], 'icon' => 'mdi-check-circle', 'tone' => 'success', 'hint' => 'All time', 'url' => $stageUrl(\App\Models\Lead::STATUS_CLOSED)])
    </div>
</div>

{{-- World Clock card hidden - it now lives in the header (includes/world-clock). Uncomment to bring it back. --}}
{{-- @include('dashboard.partials.world-clock') --}}

{{-- Sent Back To Me is the count card above only (it opens the filtered Leads list). --}}
<div class="row panel-row">
    <div class="col-12">
        <div class="dash-panel">
            @include('dashboard.partials.panel-head', ['title' => 'Drafts To Publish', 'subtitle' => 'Oldest first', 'linkUrl' => route('leads.index'), 'linkLabel' => 'All leads'])
            <div class="panel-body">
                @include('dashboard.partials.lead-list', ['rows' => $drafts, 'empty' => 'No drafts.'])
            </div>
        </div>
    </div>
</div>
